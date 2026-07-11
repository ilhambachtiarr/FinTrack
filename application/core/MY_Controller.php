<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller — Base Controller untuk seluruh aplikasi keuangan
 *
 * Semua controller (kecuali Auth) WAJIB extends MY_Controller.
 * Constructor ini melakukan:
 *   1. Mengecek apakah user sudah login (via session 'user_id')
 *   2. Redirect ke halaman login jika belum login
 *   3. Set data umum yang tersedia di semua view (nama user, dsb)
 *   4. Set timezone aplikasi ke Asia/Jakarta
 *
 * @package    Keuangan Pribadi
 * @author     Senior Dev
 * @version    1.0.0
 */
class MY_Controller extends CI_Controller {

    /**
     * @var int|null ID user yang sedang login, diambil dari session
     */
    protected $current_user_id = NULL;

    /**
     * @var array|null Data lengkap user yang sedang login
     */
    protected $current_user = NULL;

    /**
     * Constructor — dijalankan sebelum method apapun di controller turunan.
     *
     * Urutan eksekusi:
     *   1. Panggil parent constructor CI_Controller
     *   2. Set timezone aplikasi
     *   3. Cek session login — redirect ke /auth/login jika tidak ada
     *   4. Simpan data user ke property agar mudah diakses di setiap method
     *   5. Load helper format rupiah (custom helper)
     */
    public function __construct()
    {
        parent::__construct();

        // Set timezone aplikasi ke Waktu Indonesia Barat (WIB)
        date_default_timezone_set('Asia/Jakarta');

        // Cek apakah user sudah login dengan memverifikasi session 'user_id'
        if ( ! $this->session->userdata('user_id')) {
            // Belum login — redirect ke halaman login
            redirect('auth/login');
            exit; // Hentikan eksekusi lebih lanjut
        }

        // Simpan user_id dari session ke property protected
        // CATATAN: Ini adalah sumber terpercaya untuk user_id,
        // JANGAN pernah gunakan ID dari URL/POST untuk query data sensitif.
        $this->current_user_id = (int) $this->session->userdata('user_id');

        // Simpan data lengkap user untuk dipakai di view
        $this->current_user = $this->session->userdata('user_data');

        // Load custom helper keuangan (format_rupiah, dll)
        $this->load->helper('keuangan');

        // Bagikan data user ke semua view secara global
        // sehingga layout/header dapat menampilkan nama user, dsb.
        $this->load->vars(array(
            'current_user'    => $this->current_user,
            'current_user_id' => $this->current_user_id,
        ));
    }
}
