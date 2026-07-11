<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kategori_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $user_id = $this->current_user_id;
        
        $kategori_list = $this->Kategori_model->get_by_user($user_id);
        
        // Pisahkan pemasukan dan pengeluaran untuk view
        $pemasukan = array_filter($kategori_list, function($k) { return $k['tipe'] === 'pemasukan'; });
        $pengeluaran = array_filter($kategori_list, function($k) { return $k['tipe'] === 'pengeluaran'; });

        $data = [
            'page_title'  => 'Kustomisasi Kategori',
            'pemasukan'   => $pemasukan,
            'pengeluaran' => $pengeluaran
        ];

        $data['content'] = $this->load->view('kategori/index', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Tambah Kategori Baru',
            'mode'       => 'create'
        ];

        $data['content'] = $this->load->view('kategori/form', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    public function store()
    {
        $this->_validate_form();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = [
                'user_id'       => $this->current_user_id,
                'nama_kategori' => $this->input->post('nama_kategori', TRUE),
                'tipe'          => $this->input->post('tipe', TRUE),
                'ikon'          => $this->input->post('ikon', TRUE) ?: 'fa-tags',
                'kode_warna'    => $this->input->post('kode_warna', TRUE) ?: '#6B7280',
                'is_default'    => 0, // Buatan user selalu 0
                'created_at'    => date('Y-m-d H:i:s')
            ];

            $this->Kategori_model->insert_category($data);
            $this->session->set_flashdata('success', 'Kategori baru berhasil ditambahkan!');
            redirect('kategori');
        }
    }

    public function edit($id)
    {
        $user_id = $this->current_user_id;
        $kategori = $this->Kategori_model->get_by_id($id, $user_id);

        if (!$kategori) {
            show_404();
        }

        if ($kategori['is_default'] == 1) {
            $this->session->set_flashdata('error', 'Kategori bawaan sistem tidak dapat diubah.');
            redirect('kategori');
        }

        $data = [
            'page_title' => 'Edit Kategori',
            'mode'       => 'edit',
            'kategori'   => $kategori
        ];

        $data['content'] = $this->load->view('kategori/form', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    public function update($id)
    {
        $user_id = $this->current_user_id;
        $kategori = $this->Kategori_model->get_by_id($id, $user_id);

        if (!$kategori || $kategori['is_default'] == 1) {
            show_404();
        }

        $this->_validate_form();

        if ($this->form_validation->run() == FALSE) {
            $this->edit($id);
        } else {
            $data = [
                'nama_kategori' => $this->input->post('nama_kategori', TRUE),
                'tipe'          => $this->input->post('tipe', TRUE),
                'ikon'          => $this->input->post('ikon', TRUE) ?: 'fa-tags',
                'kode_warna'    => $this->input->post('kode_warna', TRUE) ?: '#6B7280'
            ];

            $this->Kategori_model->update_category($id, $user_id, $data);
            $this->session->set_flashdata('success', 'Kategori berhasil diperbarui!');
            redirect('kategori');
        }
    }

    public function delete($id)
    {
        $user_id = $this->current_user_id;
        $kategori = $this->Kategori_model->get_by_id($id, $user_id);

        if (!$kategori) {
            $this->session->set_flashdata('error', 'Kategori tidak ditemukan.');
            redirect('kategori');
        }

        if ($kategori['is_default'] == 1) {
            $this->session->set_flashdata('error', 'Kategori bawaan sistem tidak boleh dihapus.');
            redirect('kategori');
        }

        // Cek apakah ada transaksi yang memakai kategori ini
        $usage_count = $this->Kategori_model->check_usage($id);

        if ($usage_count > 0) {
            $this->session->set_flashdata('error', 'Jika ingin menghapus kategori tersebut harap ganti transaksi yang sudah berlaku di kategori tersebut ke kategori lain. (Terdapat ' . $usage_count . ' transaksi yang memakai kategori ini).');
            redirect('kategori');
        }

        // Lanjut Hapus
        if ($this->Kategori_model->delete_category($id, $user_id)) {
            $this->session->set_flashdata('success', 'Kategori berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus kategori.');
        }

        redirect('kategori');
    }

    private function _validate_form()
    {
        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('tipe', 'Tipe Kategori', 'required|in_list[pemasukan,pengeluaran]');
        $this->form_validation->set_rules('ikon', 'Ikon', 'trim|max_length[50]');
        $this->form_validation->set_rules('kode_warna', 'Warna', 'trim|max_length[7]');
    }
}
