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

        // Kalkulasi Financial Health Insight
        $health_insight = $this->_calculate_health_insight($total_saldo, $pemasukan_bulan, $pengeluaran_bulan, $user_id);

        $data = [
            'page_title'        => 'Dashboard',
            'total_saldo'       => $total_saldo,
            'pemasukan_bulan'   => $pemasukan_bulan,
            'pengeluaran_bulan' => $pengeluaran_bulan,
            'chart_30_hari'     => json_encode($chart_30_hari),
            'chart_kategori'    => json_encode($chart_kategori),
            'transaksi_terbaru' => $transaksi_terbaru,
            'health_insight'    => $health_insight
        ];

        // Load view menggunakan layout
        // Parameter true pada view inner (dashboard/index) berarti view tsb
        // akan di-return sebagai string untuk disuntikkan ke dalam layout.
        $data['content'] = $this->load->view('dashboard/index', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    /**
     * Hitung Skor Kesehatan Keuangan dan susun Smart Insights
     */
    private function _calculate_health_insight($total_saldo, $pemasukan, $pengeluaran, $user_id)
    {
        // 1. Savings Rate
        $savings_rate = 0;
        if ($pemasukan > 0) {
            $savings_rate = max(0, (($pemasukan - $pengeluaran) / $pemasukan) * 100);
        }

        // 2. Rata-rata pengeluaran 3 bulan & Runway Kas
        $avg_expense = $this->Dashboard_model->get_rata_rata_pengeluaran_3_bulan($user_id);
        if ($avg_expense <= 0) {
            $avg_expense = $pengeluaran > 0 ? $pengeluaran : 1;
        }

        $runway_bulan = 0;
        if ($avg_expense > 0) {
            $runway_bulan = round($total_saldo / $avg_expense, 1);
        }

        // 3. Skor Kesehatan (0 - 100)
        $score = 50; // Base score

        if ($pemasukan > 0) {
            if ($pemasukan > $pengeluaran) {
                $score += 15;
            } else {
                $score -= 20;
            }
        }

        if ($savings_rate >= 20) {
            $score += 20;
        } elseif ($savings_rate > 0) {
            $score += 10;
        }

        if ($runway_bulan >= 3) {
            $score += 15;
        } elseif ($runway_bulan >= 1) {
            $score += 5;
        }

        $score = max(10, min(100, $score));

        // Status Label & Warna
        if ($score >= 80) {
            $status_label = 'Sangat Sehat';
            $status_badge = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300';
            $score_color = 'text-emerald-600 dark:text-emerald-400';
        } elseif ($score >= 60) {
            $status_label = 'Sehat';
            $status_badge = 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300';
            $score_color = 'text-blue-600 dark:text-blue-400';
        } elseif ($score >= 40) {
            $status_label = 'Waspada';
            $status_badge = 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300';
            $score_color = 'text-amber-600 dark:text-amber-400';
        } else {
            $status_label = 'Kritis';
            $status_badge = 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300';
            $score_color = 'text-rose-600 dark:text-rose-400';
        }

        // 4. Susun Smart Insights
        $insights = [];

        // Cashflow Insight
        if ($pemasukan > $pengeluaran) {
            $surplus = $pemasukan - $pengeluaran;
            $insights[] = [
                'icon' => 'fa-circle-check',
                'color' => 'text-emerald-500',
                'text' => 'Arus kas bulan ini **Surplus Rp ' . number_format($surplus, 0, ',', '.') . '** (Pemasukan melebihi pengeluaran).'
            ];
        } elseif ($pengeluaran > $pemasukan && $pemasukan > 0) {
            $defisit = $pengeluaran - $pemasukan;
            $insights[] = [
                'icon' => 'fa-triangle-exclamation',
                'color' => 'text-rose-500',
                'text' => 'Arus kas bulan ini **Defisit Rp ' . number_format($defisit, 0, ',', '.') . '**. Evaluasi kembali pengeluaran opsional.'
            ];
        }

        // Savings Rate Insight
        if ($savings_rate >= 20) {
            $insights[] = [
                'icon' => 'fa-piggy-bank',
                'color' => 'text-emerald-500',
                'text' => 'Hebat! Anda berhasil menghemat **' . round($savings_rate, 1) . '%** dari pemasukan bulan ini (di atas target 20%).'
            ];
        } elseif ($savings_rate > 0) {
            $insights[] = [
                'icon' => 'fa-seedling',
                'color' => 'text-amber-500',
                'text' => 'Anda menabung **' . round($savings_rate, 1) . '%** dari pemasukan. Coba tingkatkan hingga 20% untuk kondisi ideal.'
            ];
        }

        // Runway Insight
        if ($runway_bulan >= 3) {
            $insights[] = [
                'icon' => 'fa-shield-halved',
                'color' => 'text-blue-500',
                'text' => 'Saldo kas saat ini mampu menopang kebutuhan biaya hidup selama **' . $runway_bulan . ' bulan** ke depan.'
            ];
        } else {
            $insights[] = [
                'icon' => 'fa-shield-cat',
                'color' => 'text-amber-500',
                'text' => 'Daya tahan kas saat ini diperkirakan **' . $runway_bulan . ' bulan**. Disarankan menambah simpanan dana cadangan.'
            ];
        }

        // Top Category Insight
        $top_kat = $this->Dashboard_model->get_kategori_pengeluaran_terbesar_bulan_ini($user_id);
        if ($top_kat && $pengeluaran > 0) {
            $persen_top = round(($top_kat['total'] / $pengeluaran) * 100, 1);
            if ($persen_top >= 25) {
                $insights[] = [
                    'icon' => 'fa-chart-pie',
                    'color' => 'text-indigo-500',
                    'text' => 'Kategori **' . htmlspecialchars($top_kat['nama_kategori']) . '** menyedot **' . $persen_top . '%** dari total pengeluaran bulan ini.'
                ];
            }
        }

        return [
            'score'        => $score,
            'status_label' => $status_label,
            'status_badge' => $status_badge,
            'score_color'  => $score_color,
            'savings_rate' => round($savings_rate, 1),
            'runway_bulan' => $runway_bulan,
            'insights'     => $insights
        ];
    }
}

