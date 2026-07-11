<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OCR Library — Wrapper untuk OCR.space API
 *
 * Mengirim gambar struk ke OCR.space dan mengembalikan teks hasil OCR.
 * Mendukung auto-kompres gambar jika ukuran melebihi limit free tier (~1MB).
 *
 * OCR.space Free Tier:
 *   - Limit: 25.000 request/bulan
 *   - Maks file: ~1MB per request
 *   - Engine 2 lebih akurat untuk struk/angka/bahasa Indonesia
 */
class Ocr {

    private $api_key;
    private $endpoint;

    /** Batas ukuran file untuk free tier OCR.space (1MB dalam bytes) */
    const FREE_TIER_LIMIT_BYTES = 1048576; // 1MB

    public function __construct()
    {
        $CI =& get_instance();
        $CI->config->load('ocr', TRUE);
        $this->api_key  = $CI->config->item('ocrspace_api_key', 'ocr');
        $this->endpoint = $CI->config->item('ocrspace_endpoint', 'ocr') ?: 'https://api.ocr.space/parse/image';
    }

    /**
     * Ekstrak teks mentah dari file gambar menggunakan OCR.space API.
     *
     * @param  string $path_gambar  Path absolut ke file gambar
     * @return array  ['success' => bool, 'text' => string, 'error' => string]
     */
    public function extract_text($path_gambar)
    {
        // Validasi API Key
        if (empty($this->api_key)) {
            return [
                'success' => FALSE,
                'text'    => '',
                'error'   => 'API Key OCR belum dikonfigurasi. Silakan hubungi administrator.'
            ];
        }

        // Validasi file ada
        if ( ! file_exists($path_gambar)) {
            return [
                'success' => FALSE,
                'text'    => '',
                'error'   => 'File gambar tidak ditemukan di server.'
            ];
        }

        // Cek ukuran file — kompres jika melebihi limit free tier
        $file_size = filesize($path_gambar);
        $path_to_send = $path_gambar; // default: kirim file asli
        $compressed_path = NULL;

        if ($file_size > self::FREE_TIER_LIMIT_BYTES) {
            $compress_result = $this->_compress_image($path_gambar);

            if ($compress_result['success']) {
                $path_to_send    = $compress_result['path'];
                $compressed_path = $compress_result['path'];
            } else {
                // Kompres gagal: tampilkan pesan yang jelas
                return [
                    'success' => FALSE,
                    'text'    => '',
                    'error'   => 'Ukuran gambar terlalu besar (' . round($file_size / 1048576, 1) . ' MB). ' .
                                 'Batas free tier adalah 1MB. Coba foto ulang dengan resolusi lebih rendah, ' .
                                 'atau gunakan input manual.'
                ];
            }
        }

        // Kirim ke OCR.space via cURL multipart/form-data
        $result = $this->_call_ocrspace($path_to_send);

        // Hapus file kompresi sementara jika ada
        if ($compressed_path && file_exists($compressed_path)) {
            @unlink($compressed_path);
        }

        return $result;
    }

    /**
     * Kirim gambar ke OCR.space API.
     * Mencoba dengan bahasa Indonesia ("ind") terlebih dahulu,
     * fallback ke English ("eng") jika hasil kosong.
     */
    private function _call_ocrspace($path_gambar)
    {
        if ( ! function_exists('curl_init')) {
            return [
                'success' => FALSE,
                'text'    => '',
                'error'   => 'cURL tidak tersedia di server ini. Hubungi administrator.'
            ];
        }

        $post_data = [
            'file'               => new CURLFile($path_gambar),
            'language'           => 'eng', // Engine 2 biasanya memakai 'eng' sebagai default untuk huruf Latin (termasuk Indonesia)
            'OCREngine'          => 2,     // Engine 2 lebih akurat untuk struk & angka
            'isOverlayRequired'  => 'false',
            'detectOrientation'  => 'true', // Auto-deteksi orientasi gambar
            'scale'              => 'true', // OCR.space auto-scale untuk kualitas lebih baik
            'isTable'            => 'false',
        ];

        $ch = curl_init($this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => TRUE,
            CURLOPT_POSTFIELDS     => $post_data,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT        => 45, // 45s — free tier bisa lambat
            CURLOPT_HTTPHEADER     => [
                'apikey: ' . $this->api_key,
            ],
            CURLOPT_SSL_VERIFYPEER => FALSE, // Laragon local dev
        ]);

        $response   = curl_exec($ch);
        $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Tangani error koneksi / timeout
        if ($curl_error) {
            return [
                'success' => FALSE,
                'text'    => '',
                'error'   => 'Gagal menghubungi server OCR (' . $curl_error . '). Periksa koneksi internet atau coba lagi.'
            ];
        }

        // Parse response JSON
        $data = json_decode($response, TRUE);

        if ($data === NULL) {
            return [
                'success' => FALSE,
                'text'    => '',
                'error'   => 'Respons OCR tidak valid (HTTP ' . $http_code . '). Coba lagi atau gunakan input manual.'
            ];
        }

        // Cek error dari OCR.space
        if ( ! empty($data['IsErroredOnProcessing']) && $data['IsErroredOnProcessing'] === TRUE) {
            $err_msg = $data['ParsedResults'][0]['ErrorMessage'] ?? ($data['ErrorMessage'] ?? 'OCR gagal memproses gambar.');
            if (is_array($err_msg)) {
                $err_msg = implode(', ', $err_msg);
            }
            return [
                'success' => FALSE,
                'text'    => '',
                'error'   => 'OCR Error: ' . $err_msg . ' — Coba foto ulang dengan pencahayaan lebih baik, atau gunakan input manual.'
            ];
        }

        // Cek OCRExitCode (1 = sukses, lainnya = ada masalah)
        $exit_code = $data['OCRExitCode'] ?? 0;
        if ($exit_code !== 1) {
            return [
                'success' => FALSE,
                'text'    => '',
                'error'   => 'OCR tidak dapat membaca gambar (ExitCode: ' . $exit_code . '). Pastikan foto cukup terang dan tidak buram.'
            ];
        }

        // Ambil teks hasil OCR
        $parsed_text = $data['ParsedResults'][0]['ParsedText'] ?? '';
        $parsed_text = trim($parsed_text);

        if (empty($parsed_text)) {
            return [
                'success' => FALSE,
                'text'    => '',
                'error'   => 'Teks tidak terdeteksi di gambar ini. Pastikan struk terlihat jelas, cukup cahaya, dan tidak blur.'
            ];
        }

        return [
            'success' => TRUE,
            'text'    => $parsed_text,
            'error'   => ''
        ];
    }

    /**
     * Kompres/resize gambar agar ukurannya di bawah limit free tier OCR.space (~1MB).
     * Menggunakan GD library yang tersedia di PHP/Laragon.
     *
     * @param  string $path  Path file gambar asli
     * @return array  ['success' => bool, 'path' => string compressed path]
     */
    private function _compress_image($path)
    {
        if ( ! function_exists('imagecreatefromjpeg') && ! function_exists('imagecreatefrompng')) {
            return ['success' => FALSE, 'path' => ''];
        }

        $info = @getimagesize($path);
        if ( ! $info) {
            return ['success' => FALSE, 'path' => ''];
        }

        $mime = $info['mime'];
        $src  = NULL;

        // Load source image
        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $src = @imagecreatefromjpeg($path);
        } elseif ($mime === 'image/png') {
            $src = @imagecreatefrompng($path);
        } elseif ($mime === 'image/gif') {
            $src = @imagecreatefromgif($path);
        } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            $src = @imagecreatefromwebp($path);
        }

        if ( ! $src) {
            return ['success' => FALSE, 'path' => ''];
        }

        // Hitung dimensi baru — resize ke max 1500px sambil pertahankan rasio
        $orig_w = imagesx($src);
        $orig_h = imagesy($src);
        $max_dim = 1500;

        if ($orig_w > $max_dim || $orig_h > $max_dim) {
            if ($orig_w > $orig_h) {
                $new_w = $max_dim;
                $new_h = (int) ($orig_h * $max_dim / $orig_w);
            } else {
                $new_h = $max_dim;
                $new_w = (int) ($orig_w * $max_dim / $orig_h);
            }
        } else {
            $new_w = $orig_w;
            $new_h = $orig_h;
        }

        $dst = imagecreatetruecolor($new_w, $new_h);

        // Pertahankan transparansi PNG
        if ($mime === 'image/png') {
            imagealphablending($dst, FALSE);
            imagesavealpha($dst, TRUE);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefilledrectangle($dst, 0, 0, $new_w, $new_h, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_w, $new_h, $orig_w, $orig_h);

        // Simpan ke file sementara
        $compressed_path = dirname($path) . '/compressed_' . basename($path, pathinfo($path, PATHINFO_EXTENSION)) . '.jpg';

        // Coba quality 75 dulu, turun ke 60 jika masih > 1MB
        $quality = 75;
        imagejpeg($dst, $compressed_path, $quality);

        if (filesize($compressed_path) > self::FREE_TIER_LIMIT_BYTES) {
            imagejpeg($dst, $compressed_path, 60);
        }

        imagedestroy($src);
        imagedestroy($dst);

        if (filesize($compressed_path) > self::FREE_TIER_LIMIT_BYTES) {
            @unlink($compressed_path);
            return ['success' => FALSE, 'path' => ''];
        }

        return ['success' => TRUE, 'path' => $compressed_path];
    }
}
