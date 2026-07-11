<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Struk Controller
 *
 * Mengelola upload foto struk, pemanggilan OCR, preview hasil parsing,
 * dan penyimpanan transaksi multi-item ke database.
 */
class Struk extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Transaction_model');
        $this->load->model('Akun_model');
        $this->load->model('Kategori_model');
        $this->load->library(['Ocr', 'upload']);
        $this->load->helper(['struk', 'keuangan']);
    }

    // =========================================================
    // ACTION: Upload & Proses OCR (AJAX, return JSON)
    // =========================================================

    /**
     * Terima file gambar struk, jalankan OCR, kembalikan preview JSON.
     * TIDAK insert apapun ke database di sini — hanya preview/draft.
     */
    public function upload()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json_error('Method tidak diizinkan.', 405);
        }

        // Konfigurasi CI3 Upload Library
        $upload_path = FCPATH . 'uploads/struk/';
        $config_upload = [
            'upload_path'   => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif|webp',
            'max_size'      => 5120, // 5MB
            'file_name'     => 'struk_' . $this->current_user_id . '_' . time() . '_' . mt_rand(1000, 9999),
            'overwrite'     => TRUE,
        ];

        $this->upload->initialize($config_upload);

        if ( ! $this->upload->do_upload('struk_file')) {
            return $this->_json_error('Gagal upload: ' . $this->upload->display_errors('', ''));
        }

        $upload_data = $this->upload->data();
        $full_path   = $upload_data['full_path'];
        $file_url    = base_url('uploads/struk/' . $upload_data['file_name']);

        // Panggil OCR library
        $ocr_result = $this->ocr->extract_text($full_path);

        if ( ! $ocr_result['success']) {
            // Hapus file yang gagal diproses OCR
            @unlink($full_path);
            return $this->_json_error($ocr_result['error']);
        }

        $teks_mentah = $ocr_result['text'];

        // Parse teks OCR ke data terstruktur
        $parsed = parse_struk($teks_mentah);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'         => TRUE,
                'nama_merchant'   => $parsed['nama_merchant'],
                'items'           => $parsed['items'],
                'total_terdeteksi'=> $parsed['total_terdeteksi'],
                'path_gambar'     => $upload_data['file_name'], // nama file saja
                'file_url'        => $file_url,
                'ocr_raw_text'    => $teks_mentah, // untuk debug/audit
                'csrf_hash'       => $this->security->get_csrf_hash(), // Kirim hash baru untuk request berikutnya
            ]));
    }

    // =========================================================
    // ACTION: Simpan Transaksi (AJAX, return JSON)
    // =========================================================

    /**
     * Terima data review dari frontend (setelah user cek & edit),
     * lalu simpan transaksi + item ke database.
     */
    public function simpan()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json_error('Method tidak diizinkan.', 405);
        }

        $user_id      = $this->current_user_id;
        $akun_id      = (int) $this->input->post('akun_id');
        $tipe         = $this->input->post('tipe');
        $tanggal      = $this->input->post('tanggal');
        $catatan      = $this->input->post('catatan');
        $kategori_id  = $this->input->post('kategori_id');
        $nama_merchant= trim($this->input->post('nama_merchant') ?? '');
        $path_gambar  = $this->input->post('path_gambar'); // nama file
        $ocr_raw_text = $this->input->post('ocr_raw_text');
        $items_raw    = $this->input->post('items');

        // Validasi dasar
        if ( ! in_array($tipe, ['pemasukan', 'pengeluaran'])) {
            return $this->_json_error('Tipe transaksi tidak valid.');
        }
        if ($akun_id <= 0) {
            return $this->_json_error('Akun tidak valid.');
        }
        if (empty($tanggal) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return $this->_json_error('Tanggal tidak valid.');
        }

        // Anti-IDOR: pastikan akun milik user
        $akun = $this->Akun_model->get_by_id($akun_id, $user_id);
        if ( ! $akun) {
            return $this->_json_error('Akun tidak ditemukan.', 403);
        }

        // Parse & validasi items
        if (empty($items_raw) || ! is_array($items_raw)) {
            return $this->_json_error('Minimal harus ada 1 item transaksi.');
        }

        $items = [];
        foreach ($items_raw as $item) {
            $nama   = trim($item['nama_item'] ?? '');
            $qty    = (float) str_replace(',', '.', $item['qty'] ?? 1);
            $harga  = (float) str_replace(['.', ','], ['', '.'], $item['harga_satuan'] ?? 0);
            // Terapkan format Indonesia: titik sebagai pemisah ribuan
            // Deteksi format "15.000" vs "15.5"
            if (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $item['harga_satuan'] ?? '')) {
                $harga = (float) str_replace('.', '', $item['harga_satuan']);
            }
            $subtotal = round($qty * $harga);

            if (strlen($nama) < 1 || $qty <= 0 || $harga <= 0) continue;

            $items[] = [
                'nama_item'    => $nama,
                'qty'          => $qty,
                'harga_satuan' => $harga,
                'subtotal'     => $subtotal,
            ];
        }

        if (empty($items)) {
            return $this->_json_error('Tidak ada item valid untuk disimpan.');
        }

        // Simpan ke database
        $transaksi_id = $this->Transaction_model->create_dari_struk(
            $user_id,
            $akun_id,
            $tipe,
            $items,
            $nama_merchant,
            $tanggal,
            $catatan,
            $kategori_id,
            $ocr_raw_text
        );

        if ( ! $transaksi_id) {
            return $this->_json_error('Gagal menyimpan transaksi. Silakan coba lagi.');
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'      => TRUE,
                'message'      => 'Transaksi struk berhasil disimpan!',
                'transaksi_id' => $transaksi_id,
            ]));
    }

    // =========================================================
    // ACTION: Get Detail Item (AJAX, Anti-IDOR)
    // =========================================================

    /**
     * Kembalikan detail item dari sebuah transaksi struk.
     * Wajib verifikasi transaksi_id milik user dari session.
     */
    public function get_detail_item($transaksi_id)
    {
        $transaksi_id = (int) $transaksi_id;
        $user_id      = $this->current_user_id;

        $items = $this->Transaction_model->get_items_by_transaksi($transaksi_id, $user_id);

        if ($items === FALSE) {
            return $this->_json_error('Transaksi tidak ditemukan.', 403);
        }

        // Ambil data transaksi utama untuk nama merchant dll
        $trx = $this->Transaction_model->get_by_id($transaksi_id, $user_id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'       => TRUE,
                'items'         => $items,
                'nama_merchant' => $trx['nama_merchant'] ?? '',
                'catatan'       => $trx['catatan'] ?? '',
                'tipe'          => $trx['tipe'],
                'jumlah'        => $trx['jumlah'],
            ]));
    }

    // =========================================================
    // ACTION: Update Item (AJAX, Anti-IDOR)
    // =========================================================

    /**
     * Update item dari transaksi struk yang sudah tersimpan.
     */
    public function update_item($transaksi_id)
    {
        if ($this->input->method() !== 'post') {
            return $this->_json_error('Method tidak diizinkan.', 405);
        }

        $transaksi_id  = (int) $transaksi_id;
        $user_id       = $this->current_user_id;
        $items_raw     = $this->input->post('items');
        $nama_merchant = trim($this->input->post('nama_merchant') ?? '');
        $catatan       = $this->input->post('catatan');

        if (empty($items_raw) || ! is_array($items_raw)) {
            return $this->_json_error('Minimal harus ada 1 item.');
        }

        $items = [];
        foreach ($items_raw as $item) {
            $nama  = trim($item['nama_item'] ?? '');
            $qty   = (float) str_replace(',', '.', $item['qty'] ?? 1);
            $harga = (float) $item['harga_satuan'];
            if (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $item['harga_satuan'] ?? '')) {
                $harga = (float) str_replace('.', '', $item['harga_satuan']);
            }
            $subtotal = round($qty * $harga);
            if (strlen($nama) < 1 || $qty <= 0 || $harga <= 0) continue;
            $items[] = ['nama_item' => $nama, 'qty' => $qty, 'harga_satuan' => $harga, 'subtotal' => $subtotal];
        }

        if (empty($items)) {
            return $this->_json_error('Tidak ada item valid.');
        }

        $ok = $this->Transaction_model->update_dari_struk($transaksi_id, $user_id, $items, $nama_merchant, $catatan);

        if ( ! $ok) {
            return $this->_json_error('Gagal memperbarui item. Transaksi tidak ditemukan atau bukan milik Anda.', 403);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => TRUE, 'message' => 'Item transaksi berhasil diperbarui!']));
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function _json_error($message, $status = 400)
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => FALSE, 
                'error' => $message,
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
    }
}
