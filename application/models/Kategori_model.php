<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori_model extends CI_Model {
    
    public function get_by_user($user_id) {
        return $this->db->where('user_id', (int) $user_id)
                        ->order_by('tipe', 'ASC')
                        ->order_by('nama_kategori', 'ASC')
                        ->get('kategori')->result_array();
    }

    public function get_by_id($id, $user_id) {
        return $this->db->where('id', (int) $id)
                        ->where('user_id', (int) $user_id)
                        ->get('kategori')->row_array();
    }

    public function insert_category($data) {
        $this->db->insert('kategori', $data);
        return $this->db->insert_id();
    }

    public function update_category($id, $user_id, $data) {
        $this->db->where('id', (int) $id)
                 ->where('user_id', (int) $user_id)
                 ->where('is_default', 0) // Jangan biarkan user edit template bawaan secara sistem
                 ->update('kategori', $data);
        return $this->db->affected_rows() > 0;
    }

    public function delete_category($id, $user_id) {
        $this->db->where('id', (int) $id)
                 ->where('user_id', (int) $user_id)
                 ->where('is_default', 0) // Jangan biarkan delete template default
                 ->delete('kategori');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengecek apakah kategori sedang dipakai oleh transaksi aktif
     * (mengabaikan transaksi yang sudah soft-deleted jika diperlukan, 
     * tapi lebih aman cek semua).
     */
    public function check_usage($id) {
        $this->db->where('kategori_id', (int) $id);
        $this->db->where('deleted_at IS NULL', null, false);
        return $this->db->count_all_results('transaksi');
    }
}
