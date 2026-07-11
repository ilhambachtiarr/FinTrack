<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Profil Controller (FASE 10)
 *
 * Mengelola update data diri, ganti password, dan upload foto profil.
 * Extends MY_Controller untuk proteksi wajib login dan IDOR protection.
 */
class Profil extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library(['form_validation', 'upload']);
    }

    /**
     * Halaman Utama Profil
     */
    public function index()
    {
        $user = $this->Auth_model->get_by_id($this->current_user_id);
        
        $data = [
            'page_title' => 'Profil Saya — Keuangan Pribadi',
            'user'       => $user,
        ];

        $data['content'] = $this->load->view('profil/index', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    /**
     * Update Data Diri (Nama & Email)
     */
    public function update()
    {
        if ($this->input->method() !== 'post') {
            redirect('profil');
        }

        $this->form_validation->set_rules('name', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('profil');
        }

        $data = [
            'name'  => $this->input->post('name', TRUE),
            'email' => $this->input->post('email', TRUE)
        ];

        $update = $this->Auth_model->update_profile($this->current_user_id, $data);

        if ($update) {
            // Update session data
            $user_data = $this->session->userdata('user_data');
            $user_data['name'] = $data['name'];
            $user_data['email'] = $data['email'];
            $this->session->set_userdata('user_data', $user_data);
            
            $this->session->set_flashdata('success', 'Profil berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Email sudah digunakan oleh akun lain.');
        }

        redirect('profil');
    }

    /**
     * Update Password
     */
    public function update_password()
    {
        if ($this->input->method() !== 'post') {
            redirect('profil');
        }

        $this->form_validation->set_rules('password_lama', 'Password Lama', 'required');
        $this->form_validation->set_rules('password_baru', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required|matches[password_baru]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('profil');
        }

        $password_lama = $this->input->post('password_lama', FALSE);
        $password_baru = $this->input->post('password_baru', FALSE);

        $update = $this->Auth_model->update_password($this->current_user_id, $password_lama, $password_baru);

        if ($update) {
            $this->session->set_flashdata('success', 'Password berhasil diubah.');
        } else {
            $this->session->set_flashdata('error', 'Password lama salah.');
        }

        redirect('profil');
    }

    /**
     * Upload Foto Profil
     */
    public function upload_foto()
    {
        if ($this->input->method() !== 'post') {
            redirect('profil');
        }

        $config['upload_path']   = FCPATH . 'uploads/profil/';
        $config['allowed_types'] = 'jpg|jpeg|png|webp';
        $config['max_size']      = 2048; // 2MB
        $config['file_name']     = 'profil_' . $this->current_user_id . '_' . time() . '_' . mt_rand(1000, 9999);
        $config['overwrite']     = TRUE;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('foto_profil')) {
            $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
        } else {
            $upload_data = $this->upload->data();
            $nama_file_baru = $upload_data['file_name'];

            $this->Auth_model->update_foto($this->current_user_id, $nama_file_baru);

            // Update session data
            $user_data = $this->session->userdata('user_data');
            $user_data['foto_profil'] = $nama_file_baru;
            $this->session->set_userdata('user_data', $user_data);

            $this->session->set_flashdata('success', 'Foto profil berhasil diperbarui.');
        }

        redirect('profil');
    }
}
