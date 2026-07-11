<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Struk Helper — Parser heuristik teks OCR struk belanja Indonesia
 *
 * Memecah teks mentah dari OCR.space menjadi array terstruktur:
 * - nama_merchant (dari 1-2 baris pertama yang bukan angka/harga)
 * - items[] (nama item, qty, harga_satuan, subtotal)
 * - total_terdeteksi (pembanding saja, bukan sumber hitung utama)
 *
 * CATATAN: Parser ini tidak akan 100% akurat karena format struk sangat
 * beragam (Indomaret, Alfamart, warung, restoran, dll). OCR.space juga
 * sering memisah baris berbeda dari aslinya. Tujuannya adalah menghasilkan
 * draft awal yang bisa dikoreksi user dengan mudah.
 */

if ( ! function_exists('parse_struk'))
{
    /**
     * Parse teks OCR struk menjadi data terstruktur.
     *
     * @param  string $teks_mentah  Teks raw hasil OCR dari OCR.space
     * @return array
     */
    function parse_struk($teks_mentah)
    {
        $lines = preg_split('/\r?\n/', trim($teks_mentah));
        $lines = array_values(array_filter($lines, function($l) {
            return trim($l) !== '';
        }));

        $hasil = [
            'nama_merchant'    => '',
            'items'            => [],
            'total_terdeteksi' => 0,
        ];

        if (empty($lines)) {
            return $hasil;
        }

        // -------------------------------------------------------
        // 1. Deteksi Nama Merchant (1-2 baris pertama yg bukan harga)
        // -------------------------------------------------------
        $merchant_candidates  = [];
        $merchant_line_indices = []; // Track index baris merchant agar bisa skip di loop utama
        foreach (array_slice($lines, 0, 5) as $idx => $line) {
            $bersih = trim($line);

            // Jika ada angka besar (harga >= 1000) di baris ini, STOP mencari merchant
            $angka_cek = preg_replace('/\s+/', '', $bersih); // Hapus spasi dulu (OCR: "16 000")
            if (preg_match('/[\d]{1,3}(?:\.[\d]{3})+(?:,\d{2})?$/', $angka_cek)
                || preg_match('/[\d]{4,}$/', $angka_cek)) {
                $val_cek = _parse_nominal(preg_replace('/.*?(\d[\d\.,]*)$/', '$1', $angka_cek));
                if ($val_cek >= 1000) break;
            }

            if (strlen($bersih) > 2 && !preg_match('/^\d[\d\s\.,\-]+$/', $bersih)) {
                $merchant_candidates[]   = $bersih;
                $merchant_line_indices[] = $idx;
                if (count($merchant_candidates) >= 2) break;
            }
        }
        $hasil['nama_merchant'] = implode(' - ', $merchant_candidates);

        // -------------------------------------------------------
        // 2. Keywords yang dilewati (bukan item)
        // -------------------------------------------------------
        $skip_keywords = [
            'struk', 'kasir', 'cashier', 'nota', 'invoice', 'npwp',
            'terima kasih', 'thank you', 'member', 'diskon promo',
            'telepon', 'address', 'alamat', 'tax', 'pajak', 'ppn',
            'tunai', 'cash', 'kembalian', 'change', 'debit', 'kredit',
            'credit', 'visa', 'mastercard', 'bca', 'mandiri', 'bni', 'bri',
            'bayar', 'terbayar', 'kembal', 'kembali', 'tot item',
            'no. nota', 'tanggal', 'no nota', 'f4', 'f3', 'f2', 'f1',
            'telp', 'hp:', 'website', 'www.', 'http',
        ];

        $total_keywords = [
            'total', 'jumlah', 'grand total', 'subtotal', 'total bayar',
            'total belanja', 'total transaksi',
        ];

        // Daftar satuan yang umum di struk
        $satuan_pattern = 'PORSI|GLS|GLASS|PCS|PC|KG|GR|LTR|ML|UNIT|BUAH|PAK|BKS|BOX|BTL|DUS|BH|LEMBAR|PK|LITER';

        // -------------------------------------------------------
        // 3. Loop utama — deteksi item
        // -------------------------------------------------------
        $possible_name  = ''; // Nama item kandidat dari baris sebelumnya
        $urutan         = 0;

        for ($index = 0; $index < count($lines); $index++) {
            $line       = $lines[$index];
            $bersih     = trim($line);
            $bersih_low = strtolower($bersih);

            if (strlen($bersih) < 2) continue;

            // Skip baris merchant yang sudah diambil (gunakan index, bukan nilai string)
            if (in_array($index, $merchant_line_indices)) continue;

            // Cek baris total
            $is_total = FALSE;
            foreach ($total_keywords as $kw) {
                if (strpos($bersih_low, $kw) !== FALSE) {
                    $angka = _ekstrak_angka_terakhir($bersih);
                    if ($angka > 0) {
                        $hasil['total_terdeteksi'] = $angka;
                    }
                    $is_total = TRUE;
                    break;
                }
            }
            if ($is_total) { $possible_name = ''; continue; }

            // Skip baris non-item
            $skip = FALSE;
            foreach ($skip_keywords as $kw) {
                if (strpos($bersih_low, $kw) !== FALSE) {
                    $skip = TRUE;
                    break;
                }
            }
            if ($skip) { $possible_name = ''; continue; }

            $item_processed = FALSE; // Flag: apakah baris ini sudah diproses sebagai item/qty

            // -------------------------------------------------------
            // POLA E: "Qty [Satuan] x|X|@ Harga" pada satu baris
            //  → nama item ada di $possible_name (baris sebelumnya)
            // Contoh: "1 PORSI X 16 000"  /  "2 PCS @ 8.500"
            // -------------------------------------------------------
            if ( ! $item_processed && preg_match(
                '/^(\d+(?:[,.]\d+)?)\s*(?:' . $satuan_pattern . ')?\s*[xX@]\s*(?:Rp\.?\s*)?([\d\s\.,]+)$/i',
                $bersih, $m
            )) {
                $qty   = (float) str_replace(',', '.', $m[1]);
                $harga = _parse_nominal($m[2]);
                if ($qty > 0 && $harga > 0 && !empty($possible_name)) {
                    $hasil['items'][] = [
                        'nama_item'    => $possible_name,
                        'qty'          => $qty,
                        'harga_satuan' => $harga,
                        'subtotal'     => round($qty * $harga),
                        'urutan'       => $urutan,
                    ];
                    $urutan++;
                    $possible_name = '';
                }
                $item_processed = TRUE; // Tandai bahwa baris ini adalah baris qty/pola E
            }

            // -------------------------------------------------------
            // POLA F: "Qty [Satuan]" satu baris, harga di baris berikutnya
            //  → nama item ada di $possible_name
            // Contoh:
            //   STRAWBERRY MOJITO
            //   1 GLS
            //   16.000
            // -------------------------------------------------------
            if ( ! $item_processed && preg_match(
                '/^(\d+(?:[,.]\d+)?)\s*(?:' . $satuan_pattern . ')$/i',
                $bersih, $m
            )) {
                // Cari baris harga berikutnya — boleh ada 1 baris separator (=, -, dst)
                $next_idx  = $index + 1;
                $next_line = isset($lines[$next_idx]) ? trim($lines[$next_idx]) : '';
                // Kalau baris berikutnya murni separator, loncat 1 lagi
                if ($next_line !== '' && preg_match('/^[=\-\*\.\s]+$/', $next_line)) {
                    $next_idx++;
                    $next_line = isset($lines[$next_idx]) ? trim($lines[$next_idx]) : '';
                }
                $angka_next    = _ekstrak_angka_terakhir($next_line);
                // Pastikan baris berikutnya memang harga (hanya angka/Rp), bukan nama item lagi
                $is_harga_line = preg_match('/^(?:Rp\.?\s*)?[\d\s\.,]+$/', $next_line);
                if ($angka_next >= 100 && $is_harga_line && !empty($possible_name)) {
                    $qty   = (float) str_replace(',', '.', $m[1]);
                    $harga = $angka_next;
                    $hasil['items'][] = [
                        'nama_item'    => $possible_name,
                        'qty'          => $qty,
                        'harga_satuan' => $harga,
                        'subtotal'     => round($qty * $harga),
                        'urutan'       => $urutan,
                    ];
                    $urutan++;
                    $possible_name = '';
                    $index = $next_idx; // Skip sampai baris harga
                }
                $item_processed = TRUE; // Tandai bahwa baris ini adalah baris qty
            }

            // -------------------------------------------------------
            // POLA A: "Nama Item [spasi+] Harga" pada satu baris
            // Contoh: "T ROAST (A) 16.000"  /  "ICE LEMON TEA  14.000"
            // -------------------------------------------------------
            if ( ! $item_processed && preg_match(
                '/^(.+?)\s+(?:Rp\.?\s*)?([\d\.]+(?:,\d{2})?)$/i',
                $bersih, $m
            )) {
                $nama  = trim($m[1]);
                $harga = _parse_nominal($m[2]);
                // Pastikan: harga valid, nama bukan murni angka, nama tidak seperti jam/tanggal
                if ($harga >= 100
                    && strlen($nama) > 1
                    && !preg_match('/^\d+$/', $nama)
                    && !preg_match('/^\d{1,2}[\/\-\.]\d{1,2}/', $nama)
                ) {
                    $hasil['items'][] = [
                        'nama_item'    => $nama,
                        'qty'          => 1,
                        'harga_satuan' => $harga,
                        'subtotal'     => $harga,
                        'urutan'       => $urutan,
                    ];
                    $urutan++;
                    $possible_name = '';
                    $item_processed = TRUE;
                }
            }

            // -------------------------------------------------------
            // POLA B: "Qty x Nama Item  Harga" pada satu baris
            // Contoh: "2 x Kopi Susu  30.000"
            // -------------------------------------------------------
            if ( ! $item_processed && preg_match(
                '/^(\d+(?:[,.]\d+)?)\s*[xX@]\s*(.+?)\s+(?:Rp\.?\s*)?([\d\.]+(?:,\d{2})?)$/i',
                $bersih, $m
            )) {
                $qty   = (float) str_replace(',', '.', $m[1]);
                $nama  = trim($m[2]);
                $total = _parse_nominal($m[3]);
                if ($qty > 0 && $total > 0 && strlen($nama) > 1) {
                    $hasil['items'][] = [
                        'nama_item'    => $nama,
                        'qty'          => $qty,
                        'harga_satuan' => round($total / $qty),
                        'subtotal'     => $total,
                        'urutan'       => $urutan,
                    ];
                    $urutan++;
                    $possible_name = '';
                    $item_processed = TRUE;
                }
            }

            // -------------------------------------------------------
            // POLA C: Baris qty detail setelah nama item
            // Contoh: "2 Pcs  @8.500  17.000"
            // -------------------------------------------------------
            if ( ! $item_processed && preg_match(
                '/^(\d+(?:[,.]\d+)?)\s*(?:' . $satuan_pattern . ')?\s*@?\s*([\d\.]+(?:,\d{2})?)\s+([\d\.]+(?:,\d{2})?)$/i',
                $bersih, $m
            )) {
                $qty   = (float) str_replace(',', '.', $m[1]);
                $harga = _parse_nominal($m[2]);
                $total = _parse_nominal($m[3]);
                if ($qty > 0 && $harga > 0 && !empty($hasil['items'])) {
                    $last = count($hasil['items']) - 1;
                    $hasil['items'][$last]['qty']          = $qty;
                    $hasil['items'][$last]['harga_satuan'] = $harga;
                    $hasil['items'][$last]['subtotal']     = $total ?: round($qty * $harga);
                    $item_processed = TRUE;
                }
            }

            // -------------------------------------------------------
            // Simpan baris ini sebagai kandidat nama item berikutnya
            // HANYA jika baris ini bukan pola qty/angka (item belum diproses)
            // -------------------------------------------------------
            if ( ! $item_processed
                && strlen($bersih) > 2
                && !preg_match('/^[\d\s\.,=\-\*\_\/\(\)]+$/', $bersih)
                && !preg_match('/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/', $bersih)  // Tanggal
                && !preg_match('/^\d{1,2}[:.]\d{2}/', $bersih)                    // Jam
                && !preg_match('/^0\d{9,}$/', $bersih)                            // No HP
            ) {
                $possible_name = $bersih;
            }
        }

        return $hasil;
    }
}

/**
 * Ambil angka terakhir dari sebuah baris teks
 * Mendukung format "16 000" (spasi sebagai pemisah ribuan dari OCR)
 */
if ( ! function_exists('_ekstrak_angka_terakhir'))
{
    function _ekstrak_angka_terakhir($baris)
    {
        preg_match_all('/([\d]{1,3}(?:\.[\d]{3})*(?:,\d{2})?|[\d]+)/', $baris, $matches);
        if (empty($matches[0])) return 0;
        return _parse_nominal(end($matches[0]));
    }
}

/**
 * Konversi string angka format Indonesia ke float
 * Mendukung: "15.000", "15.000,50", "15000", "16 000" (spasi OCR)
 */
if ( ! function_exists('_parse_nominal'))
{
    function _parse_nominal($str)
    {
        $str = trim($str);
        // Hapus spasi di dalam angka (OCR sering baca "16 000" -> "16000")
        $str = str_replace(' ', '', $str);
        // Format Indonesia: 1.000.000,50
        if (preg_match('/^[\d]{1,3}(?:\.[\d]{3})+(?:,\d{2})?$/', $str)) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        }
        // Format dengan koma desimal saja: 15000,50
        elseif (preg_match('/^\d+,\d{2}$/', $str)) {
            $str = str_replace(',', '.', $str);
        }
        // Format lainnya — buang titik & koma
        else {
            $str = str_replace(['.', ','], '', $str);
        }
        return (float) $str;
    }
}
