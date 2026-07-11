<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller — Autentikasi pengguna (Login, Register, Logout)
 *
 * WAJIB extends CI_Controller BUKAN MY_Controller —
 * karena MY_Controller akan redirect ke sini jika belum login,
 * dan itu akan menyebabkan infinite redirect loop.
 *
 * Fitur keamanan yang diimplementasi:
 *   - CSRF: form_open() otomatis inject token (aktif di config)
 *   - XSS: input->post($key, TRUE) untuk semua field non-password
 *   - Password: password_hash() BCrypt via Auth_model, tidak pernah disimpan plaintext
 *   - Rate-limit: max 5 gagal → akun dikunci 15 menit
 *   - Generic error message: tidak memberitahu apakah email terdaftar atau tidak
 *
 * @package  Keuangan Pribadi
 * @version  1.0.0
 */
class Auth extends CI_Controller {

    /** Maksimum percobaan login sebelum akun dikunci */
    const MAX_FAILED_ATTEMPTS  = 5;

    /** Durasi kunci akun dalam menit */
    const LOCK_DURATION_MINUTES = 15;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper('keuangan');
        $this->load->model('Auth_model');
        $this->load->model('Password_reset_model');
    }

    /**
     * Default route: arahkan ke login
     */
    public function index()
    {
        $this->login();
    }

    // =========================================================
    // LOGIN
    // =========================================================

    /**
     * GET  → Tampilkan form login
     * POST → Validasi, cek lock, verify password, buat session
     */
    public function login()
    {
        // Sudah login? Langsung ke dashboard
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
            return;
        }

        $data = [
            'page_title'    => 'Login — Keuangan Pribadi',
            'error_message' => '',
            'flash_success' => $this->session->flashdata('success'),
            'old_email'     => '',
        ];

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            // Password diambil tanpa XSS-filter agar karakter khusus tidak rusak
            $email    = $this->input->post('email',    TRUE);
            $password = $this->input->post('password', FALSE);

            $data['old_email'] = $email;

            // Validasi dasar: tidak boleh kosong
            if (empty(trim($email)) || empty($password)) {
                $data['error_message'] = 'Email dan password wajib diisi.';
            } else {

                $user = $this->Auth_model->get_by_email($email);

                if ( ! $user) {
                    // Jangan bocorkan info: email terdaftar atau tidak
                    $data['error_message'] = 'Email atau password tidak valid.';
                } else {

                    // ── Cek apakah akun sedang terkunci ──────────────────
                    $is_locked = ! empty($user['locked_until'])
                                 && strtotime($user['locked_until']) > time();

                    if ($is_locked) {
                        $sisa_detik = strtotime($user['locked_until']) - time();
                        $sisa_menit = (int) ceil($sisa_detik / 60);
                        $data['error_message'] = "Akun Anda terkunci. Coba lagi dalam <strong>{$sisa_menit} menit</strong>.";

                    } else {
                        // Jika lock sudah expired, reset dulu sebelum cek password
                        if ( ! empty($user['locked_until']) && strtotime($user['locked_until']) <= time()) {
                            $this->Auth_model->reset_failed_attempts($user['id']);
                            $user = $this->Auth_model->get_by_email($email);
                        }

                        // ── Verifikasi password ───────────────────────────
                        if ($this->Auth_model->verify_password($password, $user)) {

                            // ─── LOGIN SUKSES ────────────────────────────
                            $this->Auth_model->reset_failed_attempts($user['id']);

                            // Simpan data penting ke session (BUKAN password)
                            $this->session->set_userdata([
                                'user_id'   => (int) $user['id'],
                                'user_data' => [
                                    'id'          => (int) $user['id'],
                                    'name'        => $user['name'],
                                    'email'       => $user['email'],
                                    'foto_profil' => $user['foto_profil'] ?? null,
                                ],
                            ]);

                            redirect('dashboard');
                            return;

                        } else {

                            // ─── LOGIN GAGAL ─────────────────────────────
                            $this->Auth_model->increment_failed_attempts($user['id']);

                            // Reload agar dapat angka attempt terbaru
                            $user_updated = $this->Auth_model->get_by_email($email);
                            $attempts     = (int) $user_updated['failed_login_attempts'];

                            if ($attempts >= self::MAX_FAILED_ATTEMPTS) {
                                // Kunci akun
                                $this->Auth_model->lock_account($user['id'], self::LOCK_DURATION_MINUTES);
                                $data['error_message'] = 'Terlalu banyak percobaan gagal. '
                                    . 'Akun dikunci selama <strong>' . self::LOCK_DURATION_MINUTES . ' menit</strong>.';
                            } else {
                                $sisa = self::MAX_FAILED_ATTEMPTS - $attempts;
                                $data['error_message'] = "Email atau password tidak valid. "
                                    . "Tersisa <strong>{$sisa} percobaan</strong> sebelum akun dikunci.";
                            }
                        }
                    }
                }
            }
        }

        $this->load->view('auth/login', $data);
    }

    // =========================================================
    // REGISTER
    // =========================================================

    /**
     * GET  → Tampilkan form registrasi
     * POST → Validasi form, buat user, seed kategori, redirect ke login
     */
    public function register()
    {
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
            return;
        }

        $data = [
            'page_title'   => 'Daftar Akun — Keuangan Pribadi',
            'field_errors' => [],
            'old_input'    => ['name' => '', 'email' => ''],
        ];

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            $name             = $this->input->post('name',             TRUE);
            $email            = $this->input->post('email',            TRUE);
            $password         = $this->input->post('password',         FALSE); // No XSS filter
            $password_confirm = $this->input->post('password_confirm', FALSE); // No XSS filter

            $data['old_input'] = ['name' => $name, 'email' => $email];

            // ── Form Validation CI3 ─────────────────────────────────────
            $this->form_validation->set_rules('name',  'Nama', 'required|trim|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[150]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
            $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password',
                'required|matches[password]');

            // Pesan error Bahasa Indonesia
            $this->form_validation->set_message('required',   'Field %s wajib diisi.');
            $this->form_validation->set_message('min_length', 'Field %s minimal {param} karakter.');
            $this->form_validation->set_message('max_length', 'Field %s maksimal {param} karakter.');
            $this->form_validation->set_message('valid_email','Format email tidak valid.');
            $this->form_validation->set_message('matches',    'Konfirmasi password tidak cocok dengan password.');

            if ($this->form_validation->run() === FALSE) {

                // Kumpulkan error per field ke array (untuk ditampilkan inline di view)
                $data['field_errors'] = [
                    'name'             => form_error('name'),
                    'email'            => form_error('email'),
                    'password'         => form_error('password'),
                    'password_confirm' => form_error('password_confirm'),
                ];

            } else {

                // ── Cek duplikat email ──────────────────────────────────
                if ($this->Auth_model->email_exists($email)) {
                    $data['field_errors']['email'] = 'Email sudah terdaftar. '
                        . '<a href="' . site_url('auth/login') . '" class="underline font-medium">'
                        . 'Login di sini</a>.';
                } else {

                    // ─── REGISTER USER ─────────────────────────────────
                    $user_id = $this->Auth_model->register([
                        'name'     => $name,
                        'email'    => $email,
                        'password' => $password,
                    ]);

                    if ($user_id) {
                        // Seed 14 kategori default dari template sistem (user_id=0)
                        $this->Auth_model->seed_kategori_default($user_id);

                        $this->session->set_flashdata('success',
                            'Akun berhasil dibuat! Selamat datang, ' . htmlspecialchars($name) . '.');
                        redirect('auth/login');
                        return;
                    } else {
                        $data['field_errors']['general'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
                    }
                }
            }
        }

        $this->load->view('auth/register', $data);
    }

    // =========================================================
    // LUPA PASSWORD (FASE 10)
    // =========================================================

    public function forgot_password()
    {
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
            return;
        }

        $data = [
            'page_title' => 'Lupa Password — Keuangan Pribadi',
        ];
        $this->load->view('auth/forgot_password', $data);
    }

    public function send_reset_link()
    {
        if ($this->input->method() !== 'post') {
            redirect('auth/forgot_password');
        }

        $email = $this->input->post('email', TRUE);

        $user = $this->Auth_model->get_by_email($email);

        if ($user) {
            // Check rate limit (maks 3/15 menit)
            if ($this->Password_reset_model->can_request_reset($user['id'])) {
                $token = $this->Password_reset_model->create_token($user['id']);
                
                // Kirim Email
                $this->load->library('email');
                
                $this->email->from($this->config->item('smtp_user'), 'FinTrack');
                $this->email->to($email);
                $this->email->subject('Reset Password Anda');
                
                $data_email = ['name' => $user['name'], 'token' => $token];
                $message = $this->load->view('email/reset_password', $data_email, TRUE);
                
                $this->email->message($message);
                $this->email->send();
            }
        }

        // Anti-enumeration: Pesan selalu sama
        $this->session->set_flashdata('success', 'Jika email terdaftar, kami telah mengirimkan link reset password ke email tersebut.');
        redirect('auth/forgot_password');
    }

    public function reset_password($token = '')
    {
        if (empty($token)) {
            redirect('auth/login');
        }

        $user_id = $this->Password_reset_model->validate_token($token);

        $data = [
            'page_title' => 'Reset Password — Keuangan Pribadi',
            'token'      => $token,
            'is_valid'   => ($user_id !== false)
        ];

        $this->load->view('auth/reset_password', $data);
    }

    public function process_reset()
    {
        if ($this->input->method() !== 'post') {
            redirect('auth/login');
        }

        $token = $this->input->post('token', TRUE);
        $password = $this->input->post('password', FALSE);
        $password_confirm = $this->input->post('password_confirm', FALSE);

        $user_id = $this->Password_reset_model->validate_token($token);

        if (!$user_id) {
            $this->session->set_flashdata('error', 'Token tidak valid atau sudah kadaluarsa.');
            redirect('auth/forgot_password');
            return;
        }

        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('auth/reset_password/' . $token);
        } else {
            // Update password
            $this->db->where('id', (int) $user_id)
                     ->update('users', [
                         'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                         'updated_at'    => date('Y-m-d H:i:s'),
                         'locked_until'  => NULL,
                         'failed_login_attempts' => 0
                     ]);
                     
            $this->Password_reset_model->mark_used($token);

            $this->session->set_flashdata('success', 'Password berhasil diubah! Silakan login dengan password baru.');
            redirect('auth/login');
        }
    }

    // =========================================================
    // LOGOUT
    // =========================================================

    /**
     * Hancurkan session dan redirect ke halaman login.
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
