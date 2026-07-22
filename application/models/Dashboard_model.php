<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard_model
 *
 * Mengambil data agregasi dan statistik untuk dashboard utama.
 * Keamanan: Semua method memerlukan parameter $user_id untuk Anti-IDOR.
 *
 * @package  Keuangan Pribadi
 * @version  1.0.0
 */
class Dashboard_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Hitung total saldo dari semua akun aktif milik user.
     */
    public function get_total_saldo($user_id)
    {
        $this->db->select_sum('saldo');
        $this->db->where('user_id', (int) $user_id);
        $this->db->where('is_active', 1);
        $result = $this->db->get('akun')->row();

        return $result->saldo ?? 0.00;
    }

    /**
     * Hitung total pemasukan bulan berjalan.
     */
    public function get_pemasukan_bulan_ini($user_id)
    {
        $first_day = date('Y-m-01');
        $last_day  = date('Y-m-t');

        $this->db->select_sum('jumlah');
        $this->db->where('user_id', (int) $user_id);
        $this->db->where('tipe', 'pemasukan');
        $this->db->where('tanggal_transaksi >=', $first_day);
        $this->db->where('tanggal_transaksi <=', $last_day);
        $this->db->where('deleted_at IS NULL');
        $result = $this->db->get('transaksi')->row();

        return $result->jumlah ?? 0.00;
    }

    /**
     * Hitung total pengeluaran bulan berjalan.
     */
    public function get_pengeluaran_bulan_ini($user_id)
    {
        $first_day = date('Y-m-01');
        $last_day  = date('Y-m-t');

        $this->db->select_sum('jumlah');
        $this->db->where('user_id', (int) $user_id);
        $this->db->where('tipe', 'pengeluaran');
        $this->db->where('tanggal_transaksi >=', $first_day);
        $this->db->where('tanggal_transaksi <=', $last_day);
        $this->db->where('deleted_at IS NULL');
        $result = $this->db->get('transaksi')->row();

        return $result->jumlah ?? 0.00;
    }

    /**
     * Ambil data grafik pemasukan & pengeluaran 30 hari terakhir.
     * Mengembalikan array yang siap dikonversi ke JSON untuk ApexCharts.
     */
    public function get_data_grafik_30_hari($user_id)
    {
        $start_date = date('Y-m-d', strtotime('-29 days'));
        
        $this->db->select('tanggal_transaksi, tipe, SUM(jumlah) as total');
        $this->db->where('user_id', (int) $user_id);
        $this->db->where_in('tipe', ['pemasukan', 'pengeluaran']);
        $this->db->where('tanggal_transaksi >=', $start_date);
        $this->db->where('deleted_at IS NULL');
        $this->db->group_by(['tanggal_transaksi', 'tipe']);
        $this->db->order_by('tanggal_transaksi', 'ASC');
        
        $query = $this->db->get('transaksi')->result_array();
        
        // Siapkan struktur data 30 hari dengan nilai awal 0
        $data_pemasukan = [];
        $data_pengeluaran = [];
        $categories = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $categories[] = date('d M', strtotime($date));
            $data_pemasukan[$date] = 0;
            $data_pengeluaran[$date] = 0;
        }

        // Isi dengan data sebenarnya
        foreach ($query as $row) {
            if ($row['tipe'] === 'pemasukan') {
                $data_pemasukan[$row['tanggal_transaksi']] = (float) $row['total'];
            } else {
                $data_pengeluaran[$row['tanggal_transaksi']] = (float) $row['total'];
            }
        }

        return [
            'categories'  => $categories,
            'pemasukan'   => array_values($data_pemasukan),
            'pengeluaran' => array_values($data_pengeluaran)
        ];
    }

    /**
     * Ambil rincian pengeluaran per kategori untuk donut chart bulan ini.
     */
    public function get_pengeluaran_per_kategori($user_id)
    {
        $first_day = date('Y-m-01');
        $last_day  = date('Y-m-t');

        $this->db->select('kategori.nama_kategori, kategori.kode_warna, SUM(transaksi.jumlah) as total');
        $this->db->from('transaksi');
        $this->db->join('kategori', 'transaksi.kategori_id = kategori.id', 'left');
        $this->db->where('transaksi.user_id', (int) $user_id);
        $this->db->where('transaksi.tipe', 'pengeluaran');
        $this->db->where('transaksi.tanggal_transaksi >=', $first_day);
        $this->db->where('transaksi.tanggal_transaksi <=', $last_day);
        $this->db->where('transaksi.deleted_at IS NULL');
        $this->db->group_by('transaksi.kategori_id');
        $this->db->order_by('total', 'DESC');
        
        $query = $this->db->get()->result_array();

        $labels = [];
        $series = [];
        $colors = [];

        foreach ($query as $row) {
            // Tangani jika kategori_id NULL
            $nama = $row['nama_kategori'] ?? 'Tanpa Kategori';
            $warna = $row['kode_warna'] ?? '#9CA3AF';
            
            $labels[] = $nama;
            $series[] = (float) $row['total'];
            $colors[] = $warna;
        }

        return [
            'labels' => $labels,
            'series' => $series,
            'colors' => $colors
        ];
    }

    /**
     * Ambil X transaksi terbaru.
     */
    public function get_transaksi_terbaru($user_id, $limit = 5)
    {
        $this->db->select('transaksi.*, akun.nama_akun, akun.ikon as ikon_akun, akun.kode_warna as warna_akun, kategori.nama_kategori, kategori.ikon as ikon_kategori, kategori.kode_warna as warna_kategori, target.nama_akun as nama_target');
        $this->db->from('transaksi');
        $this->db->join('akun', 'transaksi.akun_id = akun.id', 'left');
        $this->db->join('kategori', 'transaksi.kategori_id = kategori.id', 'left');
        $this->db->join('akun target', 'transaksi.target_akun_id = target.id', 'left');
        $this->db->where('transaksi.user_id', (int) $user_id);
        $this->db->where('transaksi.deleted_at IS NULL');
        $this->db->order_by('transaksi.tanggal_transaksi', 'DESC');
        $this->db->order_by('transaksi.id', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Hitung rata-rata pengeluaran per bulan selama 3 bulan terakhir (90 hari).
     */
    public function get_rata_rata_pengeluaran_3_bulan($user_id)
    {
        $start_date = date('Y-m-d', strtotime('-90 days'));
        
        $this->db->select_sum('jumlah');
        $this->db->where('user_id', (int) $user_id);
        $this->db->where('tipe', 'pengeluaran');
        $this->db->where('tanggal_transaksi >=', $start_date);
        $this->db->where('deleted_at IS NULL');
        $result = $this->db->get('transaksi')->row();

        $total_90_hari = $result->jumlah ?? 0.00;
        
        // Rata-rata per bulan (3 bulan)
        return $total_90_hari / 3.0;
    }

    /**
     * Ambil kategori dengan pengeluaran terbanyak bulan ini.
     */
    public function get_kategori_pengeluaran_terbesar_bulan_ini($user_id)
    {
        $first_day = date('Y-m-01');
        $last_day  = date('Y-m-t');

        $this->db->select('kategori.nama_kategori, SUM(transaksi.jumlah) as total');
        $this->db->from('transaksi');
        $this->db->join('kategori', 'transaksi.kategori_id = kategori.id', 'left');
        $this->db->where('transaksi.user_id', (int) $user_id);
        $this->db->where('transaksi.tipe', 'pengeluaran');
        $this->db->where('transaksi.tanggal_transaksi >=', $first_day);
        $this->db->where('transaksi.tanggal_transaksi <=', $last_day);
        $this->db->where('transaksi.deleted_at IS NULL');
        $this->db->group_by('transaksi.kategori_id');
        $this->db->order_by('total', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row_array();
        return $row ?: null;
    }
}

