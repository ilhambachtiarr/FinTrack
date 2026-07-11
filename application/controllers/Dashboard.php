<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller
 *
 * Menampilkan halaman utama aplikasi setelah login.
 * Mengambil data agregat dari Dashboard_model.
 *
 * @package  Keuangan Pribadi
 * @version  1.0.0
 */
class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');
    }

    public function index()
    {
        $user_id = $this->current_user_id;

        // Ambil data agregat
        $total_saldo      = $this->Dashboard_model->get_total_saldo($user_id);
        $pemasukan_bulan  = $this->Dashboard_model->get_pemasukan_bulan_ini($user_id);
        $pengeluaran_bulan = $this->Dashboard_model->get_pengeluaran_bulan_ini($user_id);
        
        // Ambil data grafik JSON
        $chart_30_hari = $this->Dashboard_model->get_data_grafik_30_hari($user_id);
        $chart_kategori = $this->Dashboard_model->get_pengeluaran_per_kategori($user_id);
        
        // 5 transaksi terbaru
        $transaksi_terbaru = $this->Dashboard_model->get_transaksi_terbaru($user_id, 5);

        $data = [
            'page_title'        => 'Dashboard',
            'total_saldo'       => $total_saldo,
            'pemasukan_bulan'   => $pemasukan_bulan,
            'pengeluaran_bulan' => $pengeluaran_bulan,
            'chart_30_hari'     => json_encode($chart_30_hari),
            'chart_kategori'    => json_encode($chart_kategori),
            'transaksi_terbaru' => $transaksi_terbaru
        ];

        // Load view menggunakan layout
        // Parameter true pada view inner (dashboard/index) berarti view tsb
        // akan di-return sebagai string untuk disuntikkan ke dalam layout.
        $data['content'] = $this->load->view('dashboard/index', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }
}
