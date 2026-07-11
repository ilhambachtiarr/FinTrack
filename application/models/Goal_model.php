<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Goal_model
 *
 * Mengelola Tujuan Keuangan (Financial Goals).
 *
 * @package  Keuangan Pribadi
 * @version  1.0.0
 */
class Goal_model extends CI_Model {

    public function get_by_user($user_id)
    {
        return $this->db->where('user_id', (int) $user_id)
                        ->order_by('status', 'ASC') // aktif di atas, lalu dibatalkan, tercapai
                        ->order_by('tgl_target', 'ASC')
                        ->get('tujuan_keuangan')
                        ->result_array();
    }

    public function get_by_id($id, $user_id)
    {
        return $this->db->where('id', (int) $id)
                        ->where('user_id', (int) $user_id)
                        ->get('tujuan_keuangan')
                        ->row_array();
    }

    public function insert($data)
    {
        return $this->db->insert('tujuan_keuangan', $data);
    }

    public function update($id, $user_id, $data)
    {
        return $this->db->where('id', (int) $id)
                        ->where('user_id', (int) $user_id)
                        ->update('tujuan_keuangan', $data);
    }

    public function delete($id, $user_id)
    {
        return $this->db->where('id', (int) $id)
                        ->where('user_id', (int) $user_id)
                        ->delete('tujuan_keuangan');
    }

    /**
     * Tambah dana ke saldo_saat_ini.
     * Menggunakan query string agar kebal race condition.
     */
    public function tambah_dana($id, $user_id, $jumlah)
    {
        // Pastikan goal ada dan milik user (Anti-IDOR)
        $goal = $this->get_by_id($id, $user_id);
        if (!$goal) return false;

        $jumlah = (float) $jumlah;
        
        $this->db->set('saldo_saat_ini', "saldo_saat_ini + {$jumlah}", FALSE);
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        
        // Cek jika status harus berubah jadi tercapai
        $new_saldo = $goal['saldo_saat_ini'] + $jumlah;
        if ($new_saldo >= $goal['target_jumlah'] && $goal['status'] === 'aktif') {
            $this->db->set('status', 'tercapai');
        }

        $this->db->where('id', (int) $id);
        $this->db->where('user_id', (int) $user_id);
        
        return $this->db->update('tujuan_keuangan');
    }

    /**
     * Hitung persentase ketercapaian, dibatasi maksimal 100%.
     * Bisa digunakan secara statis sebagai helper.
     */
    public static function hitung_persentase($saldo, $target)
    {
        if ($target <= 0) return 0;
        
        $persen = ($saldo / $target) * 100;
        
        // Batasi maks 100%
        if ($persen > 100) $persen = 100;
        if ($persen < 0) $persen = 0;
        
        return round($persen, 1);
    }
}
