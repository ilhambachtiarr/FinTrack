<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Laporan_model');
        $this->load->library('Pdf');
    }

    /**
     * Memproses filter GET dan mengembalikan data laporan.
     */
    private function _get_laporan_data()
    {
        $user_id = $this->current_user_id;

        $mode = $this->input->get('mode') ?: 'bulan';
        
        if ($mode === 'minggu') {
            $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
            list($tanggal_awal, $tanggal_akhir) = $this->Laporan_model->get_rentang_minggu($tanggal);
            $judul_rentang = "Mingguan (" . format_tanggal($tanggal_awal) . " - " . format_tanggal($tanggal_akhir) . ")";
        } else {
            // Mode Bulan
            $bulan = $this->input->get('bulan') ?: date('m');
            $tahun = $this->input->get('tahun') ?: date('Y');
            list($tanggal_awal, $tanggal_akhir) = $this->Laporan_model->get_rentang_bulan($bulan, $tahun);
            
            $nama_bulan = format_tanggal("$tahun-$bulan-01");
            $nama_bulan = explode(' ', $nama_bulan)[1] . ' ' . $tahun; // Ambil Bulan + Tahun
            $judul_rentang = "Bulanan ($nama_bulan)";
        }

        $summary = $this->Laporan_model->get_summary($user_id, $tanggal_awal, $tanggal_akhir);
        
        return [
            'mode' => $mode,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'judul_rentang' => $judul_rentang,
            'summary' => $summary,
            'breakdown_pengeluaran' => $this->Laporan_model->get_breakdown_kategori($user_id, $tanggal_awal, $tanggal_akhir, 'pengeluaran'),
            'detail_transaksi' => $this->Laporan_model->get_detail_transaksi($user_id, $tanggal_awal, $tanggal_akhir)
        ];
    }

    /**
     * Tampilan web preview laporan
     */
    public function index()
    {
        $data = $this->_get_laporan_data();
        $data['page_title'] = 'Laporan Keuangan';
        
        // Pass filter values to view
        $data['current_mode'] = $this->input->get('mode') ?: 'bulan';
        $data['current_tanggal'] = $this->input->get('tanggal') ?: date('Y-m-d');
        $data['current_bulan'] = $this->input->get('bulan') ?: date('m');
        $data['current_tahun'] = $this->input->get('tahun') ?: date('Y');

        $data['content'] = $this->load->view('laporan/index', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    /**
     * Download PDF laporan
     */
    public function download_pdf()
    {
        $data = $this->_get_laporan_data();
        $data['current_user'] = $this->current_user; // Dari MY_Controller
        
        $html = $this->load->view('laporan/pdf_template', $data, TRUE);
        
        $filename = "Laporan-Keuangan-" . str_replace(' ', '-', $data['judul_rentang']) . ".pdf";
        
        // Render PDF (inline)
        $this->pdf->generate($html, $filename);
    }
}
