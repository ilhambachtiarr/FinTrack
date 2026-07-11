<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Keuangan Helper — Fungsi utilitas untuk aplikasi manajemen keuangan
 *
 * Helper ini di-load otomatis oleh MY_Controller sehingga tersedia
 * di seluruh controller dan dapat dipanggil dari view.
 *
 * @package    Keuangan Pribadi
 * @version    1.0.0
 */

// ============================================================
// FORMAT RUPIAH
// ============================================================

/**
 * Format angka menjadi format mata uang Rupiah Indonesia.
 *
 * Contoh penggunaan:
 *   echo format_rupiah(1500000);       // Output: Rp 1.500.000
 *   echo format_rupiah(1500000.50);    // Output: Rp 1.500.000,50
 *   echo format_rupiah(0);             // Output: Rp 0
 *
 * @param  float  $angka        Nilai numerik yang akan diformat
 * @param  bool   $dengan_desimal  Tampilkan desimal jika TRUE (default FALSE)
 * @return string               String format Rupiah, sudah di-escape untuk HTML
 */
if ( ! function_exists('format_rupiah'))
{
    function format_rupiah($angka, $dengan_desimal = FALSE)
    {
        // Pastikan nilai adalah numerik, default 0 jika bukan
        $angka = is_numeric($angka) ? (float) $angka : 0;

        if ($dengan_desimal) {
            // Format dengan 2 desimal: Rp 1.500.000,50
            $formatted = number_format($angka, 2, ',', '.');
        } else {
            // Format tanpa desimal: Rp 1.500.000
            $formatted = number_format($angka, 0, ',', '.');
        }

        return 'Rp ' . $formatted;
    }
}

// ============================================================
// FORMAT TANGGAL INDONESIA
// ============================================================

/**
 * Format tanggal ke format Indonesia yang ramah dibaca.
 *
 * Contoh:
 *   echo format_tanggal('2024-01-15');        // Output: 15 Januari 2024
 *   echo format_tanggal('2024-01-15', TRUE);  // Output: Selasa, 15 Januari 2024
 *
 * @param  string $tanggal   Tanggal dalam format Y-m-d atau timestamp
 * @param  bool   $hari      Sertakan nama hari jika TRUE
 * @return string            Tanggal dalam format Indonesia
 */
if ( ! function_exists('format_tanggal'))
{
    function format_tanggal($tanggal, $hari = FALSE)
    {
        $bulan = array(
            1  => 'Januari', 2  => 'Februari', 3  => 'Maret',
            4  => 'April',   5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',    8  => 'Agustus',   9  => 'September',
            10 => 'Oktober', 11 => 'November',  12 => 'Desember'
        );

        $nama_hari = array(
            'Sunday'    => 'Minggu',  'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',  'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',   'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        );

        $timestamp = is_numeric($tanggal) ? $tanggal : strtotime($tanggal);

        if ($timestamp === FALSE) {
            return '-';
        }

        $d = (int) date('j', $timestamp);
        $m = (int) date('n', $timestamp);
        $y = date('Y', $timestamp);

        $hasil = $d . ' ' . $bulan[$m] . ' ' . $y;

        if ($hari) {
            $h = $nama_hari[date('l', $timestamp)];
            $hasil = $h . ', ' . $hasil;
        }

        return $hasil;
    }
}

// ============================================================
// FORMAT ANGKA SINGKAT (K, M, B)
// ============================================================

/**
 * Singkat angka besar untuk display di dashboard/card.
 *
 * Contoh:
 *   echo format_angka_singkat(1500000);   // Output: 1,5 Jt
 *   echo format_angka_singkat(2500000000); // Output: 2,5 M
 *
 * @param  float  $angka  Nilai yang akan disingkat
 * @return string         Representasi singkat
 */
if ( ! function_exists('format_angka_singkat'))
{
    function format_angka_singkat($angka)
    {
        $angka = is_numeric($angka) ? (float) $angka : 0;

        if ($angka >= 1000000000) {
            return number_format($angka / 1000000000, 1, ',', '.') . ' M';
        } elseif ($angka >= 1000000) {
            return number_format($angka / 1000000, 1, ',', '.') . ' Jt';
        } elseif ($angka >= 1000) {
            return number_format($angka / 1000, 1, ',', '.') . ' Rb';
        }

        return number_format($angka, 0, ',', '.');
    }
}

// ============================================================
// PERSENTASE PROGRESS
// ============================================================

/**
 * Hitung persentase progress (digunakan untuk tujuan keuangan).
 *
 * @param  float $saldo_saat_ini  Nilai saat ini
 * @param  float $target          Nilai target
 * @return float                  Persentase 0–100
 */
if ( ! function_exists('hitung_persentase'))
{
    function hitung_persentase($saldo_saat_ini, $target)
    {
        if ($target <= 0) {
            return 0;
        }

        $persen = ($saldo_saat_ini / $target) * 100;

        // Batas maksimum 100%
        return min(100, round($persen, 1));
    }
}
