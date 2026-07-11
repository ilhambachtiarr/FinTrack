<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Akun_model
 *
 * Mengelola data Akun Keuangan pengguna.
 * PENTING: Saldo HANYA bisa berubah via transaksi (Transaction_model),
 *          BUKAN lewat form edit akun langsung.
 *
 * @package  Keuangan Pribadi
 */
class Akun_model extends CI_Model {

    /**
     * Seluruh akun milik user (termasuk nonaktif) — untuk halaman manajemen.
     */
    public function get_all_by_user($user_id)
    {
        return $this->db->where('user_id', (int) $user_id)
                        ->order_by('urutan', 'ASC')
                        ->order_by('nama_akun', 'ASC')
                        ->get('akun')
                        ->result_array();
    }

    /**
     * Hanya akun AKTIF — untuk dropdown form transaksi & transfer.
     */
    public function get_active_by_user($user_id)
    {
        return $this->db->where('user_id', (int) $user_id)
                        ->where('is_active', 1)
                        ->order_by('urutan', 'ASC')
                        ->order_by('nama_akun', 'ASC')
                        ->get('akun')
                        ->result_array();
    }

    /**
     * Ambil 1 akun berdasarkan ID dengan validasi kepemilikan (Anti-IDOR).
     */
    public function get_by_id($id, $user_id)
    {
        return $this->db->where('id', (int) $id)
                        ->where('user_id', (int) $user_id)
                        ->get('akun')
                        ->row_array();
    }

    /**
     * Total saldo seluruh akun aktif milik user — harus sama dengan Dashboard.
     */
    public function get_total_saldo($user_id)
    {
        $row = $this->db->select_sum('saldo')
                        ->where('user_id', (int) $user_id)
                        ->where('is_active', 1)
                        ->get('akun')
                        ->row_array();
        return $row['saldo'] ?? 0;
    }

    /**
     * Buat akun baru. Saldo awal diisi hanya SATU KALI di sini.
     */
    public function create($data)
    {
        return $this->db->insert('akun', $data);
    }

    /**
     * Update akun — hanya nama_akun, jenis_akun, kode_warna, ikon, urutan.
     * Saldo TIDAK ikut diupdate dari sini.
     */
    public function update($id, $user_id, $data)
    {
        // Pastikan field saldo tidak bisa diselundupkan
        unset($data['saldo'], $data['user_id'], $data['id']);

        return $this->db->where('id', (int) $id)
                        ->where('user_id', (int) $user_id)
                        ->update('akun', $data);
    }

    /**
     * Toggle status is_active (aktif ↔ nonaktif).
     */
    public function toggle_active($id, $user_id)
    {
        $akun = $this->get_by_id($id, $user_id);
        if (!$akun) return false;

        $new_status = $akun['is_active'] ? 0 : 1;

        return $this->db->where('id', (int) $id)
                        ->where('user_id', (int) $user_id)
                        ->update('akun', ['is_active' => $new_status]);
    }

    /**
     * Hapus akun PERMANEN.
     * WAJIB cek histori transaksi terlebih dahulu.
     * Jika masih ada transaksi → tolak dengan pesan jelas.
     */
    public function delete($id, $user_id)
    {
        $akun = $this->get_by_id($id, $user_id);
        if (!$akun) return ['status' => false, 'message' => 'Akun tidak ditemukan.'];

        // Cek apakah akun masih dipakai di tabel transaksi (sebagai sumber ATAU target)
        $count = $this->db->where('user_id', (int) $user_id)
                          ->group_start()
                              ->where('akun_id', (int) $id)
                              ->or_where('target_akun_id', (int) $id)
                          ->group_end()
                          ->count_all_results('transaksi');

        if ($count > 0) {
            return [
                'status'  => false,
                'message' => "Akun '{$akun['nama_akun']}' memiliki {$count} riwayat transaksi dan tidak bisa dihapus permanen. Silakan <strong>Nonaktifkan</strong> akun ini agar tidak muncul di transaksi baru, namun histori tetap tersimpan."
            ];
        }

        $this->db->where('id', (int) $id)->where('user_id', (int) $user_id)->delete('akun');
        return ['status' => true, 'message' => "Akun '{$akun['nama_akun']}' berhasil dihapus."];
    }
}
