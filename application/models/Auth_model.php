<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth_model — Model autentikasi pengguna
 *
 * Bertanggung jawab atas:
 *   - Pendaftaran user baru (register + seed kategori)
 *   - Verifikasi password login
 *   - Brute-force protection: rate limiting & account locking
 *
 * KEAMANAN:
 *   - Password SELALU di-hash dengan BCrypt (PASSWORD_BCRYPT)
 *   - Semua query menggunakan Active Record CI3 (anti SQL injection)
 *   - Tidak ada plaintext password yang disimpan ke DB
 *
 * @package  Keuangan Pribadi
 * @version  1.0.0
 */
class Auth_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    // =========================================================
    // REGISTER
    // =========================================================

    /**
     * Daftarkan user baru ke tabel users.
     *
     * Password di-hash dengan BCrypt sebelum INSERT.
     * insert_id() dipakai untuk mendapatkan ID user yang baru dibuat.
     *
     * @param  array $data  Wajib berisi: 'name', 'email', 'password' (plaintext)
     * @return int|false    user_id jika sukses, false jika INSERT gagal
     */
    public function register($data)
    {
        $now = date('Y-m-d H:i:s');

        $this->db->insert('users', [
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        $new_id = $this->db->insert_id();
        return ($new_id > 0) ? (int) $new_id : false;
    }

    /**
     * Seed kategori default untuk user yang baru register.
     *
     * Mengambil template sistem (user_id = 0, is_default = 1) dari tabel
     * kategori, lalu menyalinnya untuk user_id yang diberikan. Hasilnya:
     * setiap user baru langsung punya kategori siap pakai.
     *
     * insert_batch() digunakan agar satu query INSERT saja (efisien).
     *
     * @param  int $user_id  ID user baru
     * @return bool
     */
    public function seed_kategori_default($user_id)
    {
        // Ambil semua template kategori sistem
        $templates = $this->db
            ->where('user_id', 0)
            ->where('is_default', 1)
            ->get('kategori')
            ->result_array();

        if (empty($templates)) {
            return false;
        }

        $now   = date('Y-m-d H:i:s');
        $batch = [];

        foreach ($templates as $tpl) {
            $batch[] = [
                'user_id'       => (int) $user_id,
                'nama_kategori' => $tpl['nama_kategori'],
                'tipe'          => $tpl['tipe'],
                'ikon'          => $tpl['ikon'],
                'kode_warna'    => $tpl['kode_warna'],
                'is_default'    => 1,
                'created_at'    => $now,
            ];
        }

        return $this->db->insert_batch('kategori', $batch) !== false;
    }

    // =========================================================
    // LOGIN / VERIFIKASI
    // =========================================================

    /**
     * Cari user berdasarkan alamat email.
     *
     * @param  string $email
     * @return array|null  Row user sebagai array asosiatif, atau null jika tidak ditemukan
     */
    public function get_by_email($email)
    {
        return $this->db
            ->where('email', $email)
            ->get('users')
            ->row_array();
    }

    /**
     * Verifikasi password terhadap hash di database.
     *
     * Menggunakan password_verify() PHP — aman terhadap timing attack.
     * Cek locked_until TIDAK dilakukan di sini; itu tanggung jawab controller
     * agar controller dapat menampilkan pesan yang tepat.
     *
     * @param  string $password_plaintext  Password dari form (belum di-hash)
     * @param  array  $user                Row user dari DB (hasil get_by_email)
     * @return bool
     */
    public function verify_password($password_plaintext, $user)
    {
        return password_verify($password_plaintext, $user['password_hash']);
    }

    // =========================================================
    // RATE LIMITING / ACCOUNT LOCKING
    // =========================================================

    /**
     * Tambahkan 1 ke kolom failed_login_attempts.
     *
     * Menggunakan ekspresi SQL agar atomic (tidak ada race condition
     * dibanding read-then-write).
     *
     * @param  int $user_id
     */
    public function increment_failed_attempts($user_id)
    {
        $this->db
            ->set('failed_login_attempts', 'failed_login_attempts + 1', FALSE)
            ->set('updated_at', date('Y-m-d H:i:s'))
            ->where('id', (int) $user_id)
            ->update('users');
    }

    /**
     * Reset failed_login_attempts dan hapus kunci akun (locked_until = NULL).
     *
     * Dipanggil setelah login berhasil, atau setelah masa kunci habis.
     *
     * @param  int $user_id
     */
    public function reset_failed_attempts($user_id)
    {
        $this->db->update('users', [
            'failed_login_attempts' => 0,
            'locked_until'          => NULL,
            'updated_at'            => date('Y-m-d H:i:s'),
        ], ['id' => (int) $user_id]);
    }

    /**
     * Kunci akun selama $minutes menit dengan mengisi locked_until.
     *
     * @param  int $user_id
     * @param  int $minutes  Durasi kunci (default: 15 menit)
     */
    public function lock_account($user_id, $minutes = 15)
    {
        $locked_until = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));

        $this->db->update('users', [
            'locked_until' => $locked_until,
            'updated_at'   => date('Y-m-d H:i:s'),
        ], ['id' => (int) $user_id]);
    }

    // =========================================================
    // UTILITAS
    // =========================================================

    /**
     * Cek apakah email sudah terdaftar di tabel users.
     *
     * Dipakai untuk validasi email unik saat register.
     *
     * @param  string $email
     * @return bool
     */
    public function email_exists($email)
    {
        return $this->db
            ->where('email', $email)
            ->count_all_results('users') > 0;
    }

    // =========================================================
    // PROFIL USER (FASE 10)
    // =========================================================

    /**
     * Ambil data user tanpa password.
     */
    public function get_by_id($user_id)
    {
        return $this->db
            ->select('id, name, email, foto_profil, created_at, updated_at')
            ->where('id', (int) $user_id)
            ->get('users')
            ->row_array();
    }

    /**
     * Update profil (nama & email).
     * Validasi email unik dilakukan dengan cek apakah email sudah dipakai user lain.
     */
    public function update_profile($user_id, $data)
    {
        // Cek email unik
        $exists = $this->db->where('email', $data['email'])
                           ->where('id !=', (int) $user_id)
                           ->count_all_results('users');
        
        if ($exists > 0) {
            return false; // Email sudah digunakan
        }

        $this->db->update('users', [
            'name'       => $data['name'],
            'email'      => $data['email'],
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => (int) $user_id]);

        return true;
    }

    /**
     * Update password profil.
     */
    public function update_password($user_id, $password_lama, $password_baru)
    {
        // Ambil data user beserta password_hash
        $user = $this->db->select('password_hash')->where('id', (int) $user_id)->get('users')->row_array();
        
        if (!$user || !password_verify($password_lama, $user['password_hash'])) {
            return false; // Password lama salah
        }

        // Update password
        $this->db->update('users', [
            'password_hash' => password_hash($password_baru, PASSWORD_DEFAULT),
            'updated_at'    => date('Y-m-d H:i:s')
        ], ['id' => (int) $user_id]);

        return true;
    }

    /**
     * Update foto profil dan hapus foto lama (jika ada).
     */
    public function update_foto($user_id, $nama_file_baru)
    {
        $user = $this->get_by_id($user_id);
        
        if ($user && !empty($user['foto_profil']) && $user['foto_profil'] !== 'default-avatar.png') {
            $old_path = FCPATH . 'uploads/profil/' . $user['foto_profil'];
            if (file_exists($old_path)) {
                @unlink($old_path);
            }
        }

        $this->db->update('users', [
            'foto_profil' => $nama_file_baru,
            'updated_at'  => date('Y-m-d H:i:s')
        ], ['id' => (int) $user_id]);

        return true;
    }
}
