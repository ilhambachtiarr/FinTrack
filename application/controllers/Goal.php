<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Goal Controller
 *
 * Mengelola Tujuan Keuangan (Financial Goals).
 *
 * @package  Keuangan Pribadi
 * @version  1.0.0
 */
class Goal extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Goal_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $user_id = $this->current_user_id;

        $data = [
            'page_title' => 'Tujuan Keuangan',
            'goals'      => $this->Goal_model->get_by_user($user_id)
        ];

        $data['content'] = $this->load->view('goal/index', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    public function store()
    {
        $this->_validate_form();

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors('<span>', '</span><br>'));
            redirect('goal');
            return;
        }

        $data = [
            'user_id'       => $this->current_user_id,
            'nama_tujuan'   => $this->input->post('nama_tujuan', TRUE),
            'deskripsi'     => $this->input->post('deskripsi', TRUE),
            'target_jumlah' => str_replace(['.', ','], '', $this->input->post('target_jumlah', TRUE)),
            'tgl_target'    => $this->input->post('tgl_target', TRUE) ?: NULL,
            'ikon'          => $this->input->post('ikon', TRUE) ?: 'bullseye',
            'kode_warna'    => $this->input->post('kode_warna', TRUE) ?: '#10B981',
            'status'        => 'aktif'
        ];

        if ($this->Goal_model->insert($data)) {
            $this->session->set_flashdata('success', 'Tujuan Keuangan berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data.');
        }

        redirect('goal');
    }

    public function update($id)
    {
        $user_id = $this->current_user_id;
        $goal = $this->Goal_model->get_by_id($id, $user_id);

        if (!$goal) {
            show_404();
        }

        $this->_validate_form();
        $this->form_validation->set_rules('status', 'Status', 'in_list[aktif,tercapai,dibatalkan]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors('<span>', '</span><br>'));
            redirect('goal');
            return;
        }

        $data = [
            'nama_tujuan'   => $this->input->post('nama_tujuan', TRUE),
            'deskripsi'     => $this->input->post('deskripsi', TRUE),
            'target_jumlah' => str_replace(['.', ','], '', $this->input->post('target_jumlah', TRUE)),
            'tgl_target'    => $this->input->post('tgl_target', TRUE) ?: NULL,
            'ikon'          => $this->input->post('ikon', TRUE) ?: 'bullseye',
            'kode_warna'    => $this->input->post('kode_warna', TRUE) ?: '#10B981',
            'status'        => $this->input->post('status', TRUE) ?: 'aktif'
        ];

        if ($this->Goal_model->update($id, $user_id, $data)) {
            $this->session->set_flashdata('success', 'Tujuan Keuangan berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data.');
        }

        redirect('goal');
    }

    public function delete($id)
    {
        if ($this->Goal_model->delete($id, $this->current_user_id)) {
            $this->session->set_flashdata('success', 'Tujuan Keuangan berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus tujuan atau data tidak ditemukan.');
        }
        redirect('goal');
    }

    public function tambah_dana($id)
    {
        $this->form_validation->set_rules('jumlah_dana', 'Jumlah Dana', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors('<span>', '</span><br>'));
            redirect('goal');
            return;
        }

        $jumlah = str_replace(['.', ','], '', $this->input->post('jumlah_dana', TRUE));

        if ($this->Goal_model->tambah_dana($id, $this->current_user_id, $jumlah)) {
            $this->session->set_flashdata('success', 'Dana berhasil ditambahkan ke Tujuan Keuangan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambah dana. Pastikan tujuan tersebut valid.');
        }

        redirect('goal');
    }

    private function _validate_form()
    {
        $this->form_validation->set_rules('nama_tujuan', 'Nama Tujuan', 'required|max_length[150]');
        $this->form_validation->set_rules('target_jumlah', 'Target Jumlah', 'required');
        // Gunakan callback agar aturan format hanya berjalan ketika field terisi (CI3-compatible)
        $this->form_validation->set_rules('tgl_target', 'Tanggal Target', 'callback__valid_tgl_target');
    }

    /**
     * Callback validasi tanggal opsional (CI3-compatible).
     * Lolos jika kosong, tolak jika format bukan YYYY-MM-DD.
     */
    public function _valid_tgl_target($value)
    {
        if (empty($value)) {
            return TRUE; // field opsional — boleh dikosongkan
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $this->form_validation->set_message(
                '_valid_tgl_target',
                'Format {field} tidak valid. Gunakan format YYYY-MM-DD.'
            );
            return FALSE;
        }
        return TRUE;
    }
}
