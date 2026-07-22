<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Anggaran Controller
 *
 * Mengelola batas pengeluaran bulanan per kategori.
 */
class Anggaran extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Anggaran_model');
        $this->load->model('Kategori_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $user_id = $this->current_user_id;

        $budgets = $this->Anggaran_model->get_by_user($user_id);
        $categories = $this->Kategori_model->get_by_user($user_id);

        // Filter hanya kategori tipe pengeluaran untuk diset anggarannya
        $kategori_pengeluaran = array_filter($categories, function($cat) {
            return $cat['tipe'] === 'pengeluaran';
        });

        // Hitung total agregat anggaran
        $total_anggaran = 0;
        $total_terpakai = 0;
        foreach ($budgets as $b) {
            $total_anggaran += $b['nominal_batas'];
            $total_terpakai += $b['terpakai'];
        }

        $data = [
            'page_title'           => 'Anggaran Bulanan',
            'budgets'              => $budgets,
            'kategori_pengeluaran' => array_values($kategori_pengeluaran),
            'total_anggaran'       => $total_anggaran,
            'total_terpakai'       => $total_terpakai,
            'sisa_total'           => $total_anggaran - $total_terpakai
        ];

        $data['content'] = $this->load->view('anggaran/index', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    public function store()
    {
        $this->form_validation->set_rules('kategori_id', 'Kategori', 'required|numeric');
        $this->form_validation->set_rules('nominal_batas', 'Batas Anggaran', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors('<span>', '</span><br>'));
            redirect('anggaran');
            return;
        }

        $user_id       = $this->current_user_id;
        $kategori_id   = (int) $this->input->post('kategori_id', TRUE);
        $nominal_batas = str_replace(['.', ','], '', $this->input->post('nominal_batas', TRUE));

        if ($nominal_batas <= 0) {
            $this->session->set_flashdata('error', 'Batas anggaran harus lebih dari 0.');
            redirect('anggaran');
            return;
        }

        $data = [
            'user_id'       => $user_id,
            'kategori_id'   => $kategori_id,
            'nominal_batas' => $nominal_batas
        ];

        $this->Anggaran_model->save_anggaran($data);
        $this->session->set_flashdata('success', 'Batas anggaran kategori berhasil disimpan.');
        redirect('anggaran');
    }

    public function delete($id)
    {
        $user_id = $this->current_user_id;

        if ($this->Anggaran_model->delete_anggaran($id, $user_id)) {
            $this->session->set_flashdata('success', 'Anggaran berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus anggaran.');
        }

        redirect('anggaran');
    }
}
