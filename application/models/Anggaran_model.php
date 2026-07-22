<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Anggaran_model
 *
 * Mengelola data anggaran bulanan per kategori.
 * Keamanan: Semua method menggunakan $user_id untuk proteksi Anti-IDOR.
 */
class Anggaran_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil semua anggaran milik pengguna beserta statistik pengeluaran bulan ini.
     */
    public function get_by_user($user_id)
    {
        $first_day = date('Y-m-01');
        $last_day  = date('Y-m-t');

        // Query mengambil data anggaran + join kategori
        $this->db->select('anggaran.*, kategori.nama_kategori, kategori.ikon, kategori.kode_warna, kategori.tipe as tipe_kategori');
        $this->db->from('anggaran');
        $this->db->join('kategori', 'anggaran.kategori_id = kategori.id', 'inner');
        $this->db->where('anggaran.user_id', (int) $user_id);
        $this->db->order_by('kategori.nama_kategori', 'ASC');

        $budgets = $this->db->get()->result_array();

        // Hitung total pengeluaran bulan ini per kategori
        foreach ($budgets as &$item) {
            $this->db->select_sum('jumlah');
            $this->db->where('user_id', (int) $user_id);
            $this->db->where('kategori_id', (int) $item['kategori_id']);
            $this->db->where('tipe', 'pengeluaran');
            $this->db->where('tanggal_transaksi >=', $first_day);
            $this->db->where('tanggal_transaksi <=', $last_day);
            $this->db->where('deleted_at IS NULL');
            
            $res = $this->db->get('transaksi')->row();
            $terpakai = (float) ($res->jumlah ?? 0.00);

            $item['terpakai'] = $terpakai;
            $item['sisa']     = (float) $item['nominal_batas'] - $terpakai;
            
            $persentase = 0;
            if ($item['nominal_batas'] > 0) {
                $persentase = round(($terpakai / $item['nominal_batas']) * 100, 1);
            }
            $item['persentase'] = $persentase;

            // Tentukan warna status progress bar
            if ($persentase >= 90) {
                $item['progress_color'] = 'bg-rose-500';
                $item['text_color']     = 'text-rose-600 dark:text-rose-400';
                $item['status_label']   = 'Hampir Habis / Overbudget';
            } elseif ($persentase >= 75) {
                $item['progress_color'] = 'bg-amber-500';
                $item['text_color']     = 'text-amber-600 dark:text-amber-400';
                $item['status_label']   = 'Waspada';
            } else {
                $item['progress_color'] = 'bg-emerald-500';
                $item['text_color']     = 'text-emerald-600 dark:text-emerald-400';
                $item['status_label']   = 'Aman';
            }
        }

        return $budgets;
    }

    /**
     * Simpan / Update Anggaran (Upsert berbasis user_id & kategori_id)
     */
    public function save_anggaran($data)
    {
        $user_id     = (int) $data['user_id'];
        $kategori_id = (int) $data['kategori_id'];

        // Cek apakah anggaran kategori ini sudah ada
        $existing = $this->db->where('user_id', $user_id)
                             ->where('kategori_id', $kategori_id)
                             ->get('anggaran')
                             ->row_array();

        if ($existing) {
            $this->db->where('id', $existing['id'])
                     ->update('anggaran', [
                         'nominal_batas' => $data['nominal_batas'],
                         'updated_at'    => date('Y-m-d H:i:s')
                     ]);
            return $existing['id'];
        } else {
            $this->db->insert('anggaran', $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Hapus anggaran berdasarkan ID (Anti-IDOR)
     */
    public function delete_anggaran($id, $user_id)
    {
        $this->db->where('id', (int) $id);
        $this->db->where('user_id', (int) $user_id);
        $this->db->delete('anggaran');

        return $this->db->affected_rows() > 0;
    }

    /**
     * Ringkasan total anggaran untuk Widget Dashboard
     */
    public function get_dashboard_summary($user_id)
    {
        $budgets = $this->get_by_user($user_id);

        $total_anggaran = 0;
        $total_terpakai = 0;

        foreach ($budgets as $b) {
            $total_anggaran += (float) $b['nominal_batas'];
            $total_terpakai += (float) $b['terpakai'];
        }

        $sisa = $total_anggaran - $total_terpakai;
        $persentase = $total_anggaran > 0 ? round(($total_terpakai / $total_anggaran) * 100, 1) : 0;

        return [
            'total_anggaran' => $total_anggaran,
            'total_terpakai' => $total_terpakai,
            'sisa'           => $sisa,
            'persentase'     => $persentase,
            'items'          => $budgets
        ];
    }
}
