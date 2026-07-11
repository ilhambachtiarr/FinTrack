<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Akun Controller
 *
 * Manajemen akun keuangan pengguna.
 * SEMUA method memverifikasi user_id dari session (Anti-IDOR).
 *
 * @package  Keuangan Pribadi
 */
class Akun extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Akun_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $user_id = $this->current_user_id;

        $data = [
            'page_title'   => 'Manajemen Akun',
            'akun_list'    => $this->Akun_model->get_all_by_user($user_id),
            'total_saldo'  => $this->Akun_model->get_total_saldo($user_id),
        ];

        $data['content'] = $this->load->view('akun/index', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    public function store()
    {
        $this->_validate_form();

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors('<span>', '</span>'));
            redirect('akun');
            return;
        }

        $saldo_awal = (float) str_replace(['.', ','], '', $this->input->post('saldo_awal', TRUE));

        $data = [
            'user_id'    => $this->current_user_id,
            'nama_akun'  => $this->input->post('nama_akun', TRUE),
            'jenis_akun' => $this->input->post('jenis_akun', TRUE) ?: 'bank',
            'saldo'      => $saldo_awal,
            'kode_warna' => $this->input->post('kode_warna', TRUE) ?: '#3B82F6',
            'ikon'       => $this->input->post('ikon', TRUE) ?: 'wallet',
            'is_active'  => 1,
            'urutan'     => 0,
        ];

        if ($this->Akun_model->create($data)) {
            $this->session->set_flashdata('success', "Akun <strong>{$data['nama_akun']}</strong> berhasil ditambahkan.");
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan akun baru.');
        }

        redirect('akun');
    }

    public function update($id)
    {
        $user_id = $this->current_user_id;

        // Anti-IDOR: pastikan akun ini milik user
        $akun = $this->Akun_model->get_by_id($id, $user_id);
        if (!$akun) show_404();

        $this->_validate_form();
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors('<span>', '</span>'));
            redirect('akun');
            return;
        }

        $data = [
            'nama_akun'  => $this->input->post('nama_akun', TRUE),
            'jenis_akun' => $this->input->post('jenis_akun', TRUE) ?: 'bank',
            'kode_warna' => $this->input->post('kode_warna', TRUE) ?: '#3B82F6',
            'ikon'       => $this->input->post('ikon', TRUE) ?: 'wallet',
            // Saldo TIDAK ada di sini — hanya bisa berubah via transaksi
        ];

        if ($this->Akun_model->update($id, $user_id, $data)) {
            $this->session->set_flashdata('success', "Akun berhasil diperbarui.");
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui akun.');
        }

        redirect('akun');
    }

    public function toggle_active($id)
    {
        $result = $this->Akun_model->toggle_active($id, $this->current_user_id);

        if ($result) {
            $this->session->set_flashdata('success', 'Status akun berhasil diubah.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengubah status akun atau akun tidak ditemukan.');
        }

        redirect('akun');
    }

    public function delete($id)
    {
        $result = $this->Akun_model->delete($id, $this->current_user_id);

        if ($result['status']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect('akun');
    }

    private function _validate_form()
    {
        $this->form_validation->set_rules('nama_akun', 'Nama Akun', 'required|max_length[100]');
        $this->form_validation->set_rules('jenis_akun', 'Jenis Akun', 'required');
    }
}
