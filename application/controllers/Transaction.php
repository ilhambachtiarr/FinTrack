<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Transaction Controller
 *
 * Mengelola antarmuka dan logika HTTP untuk Transaksi.
 * Memastikan semua data yang diproses aman (Anti-IDOR) dan tervalidasi.
 *
 * @package  Keuangan Pribadi
 * @version  1.0.0
 */
class Transaction extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Transaction_model');
        $this->load->model('Akun_model');
        $this->load->model('Kategori_model');
        $this->load->library('pagination');
        $this->load->library('form_validation');
    }

    /**
     * Tampilkan daftar transaksi dengan pagination dan filter.
     */
    public function index()
    {
        $user_id = $this->current_user_id;

        // Tangkap parameter filter (GET)
        $bulan   = $this->input->get('bulan', TRUE) ?: date('m');
        $tahun   = $this->input->get('tahun', TRUE) ?: date('Y');
        $tipe    = $this->input->get('tipe', TRUE);
        $akun_id = $this->input->get('akun_id', TRUE);
        $kategori_id = $this->input->get('kategori_id', TRUE);
        $show_deleted = $this->input->get('show_deleted', TRUE) == '1';

        $filters = [
            'bulan'   => $bulan,
            'tahun'   => $tahun,
            'tipe'    => $tipe,
            'akun_id' => $akun_id,
            'kategori_id' => $kategori_id,
            'show_deleted' => $show_deleted
        ];

        // Konfigurasi Pagination
        $config['base_url']   = site_url('transaction/index');
        $config['total_rows'] = $this->Transaction_model->count_all($user_id, $filters);
        $config['per_page']   = 15;
        $config['page_query_string'] = TRUE; // Menggunakan ?per_page= (cocok jika ada GET filters)
        $config['reuse_query_string'] = TRUE; // Mempertahankan GET filter di link pagination

        // Styling Pagination (Tailwind)
        $config['full_tag_open']   = '<nav class="flex items-center justify-center space-x-1">';
        $config['full_tag_close']  = '</nav>';
        $config['num_tag_open']    = '<div>';
        $config['num_tag_close']   = '</div>';
        $config['cur_tag_open']    = '<div><span class="px-3 py-2 text-sm font-medium text-white bg-primary-600 rounded-md">';
        $config['cur_tag_close']   = '</span></div>';
        $config['next_link']       = '&raquo;';
        $config['next_tag_open']   = '<div>';
        $config['next_tag_close']  = '</div>';
        $config['prev_link']       = '&laquo;';
        $config['prev_tag_open']   = '<div>';
        $config['prev_tag_close']  = '</div>';
        $config['attributes']      = ['class' => 'px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'];

        $this->pagination->initialize($config);
        
        $start = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 0;

        $data = [
            'page_title' => 'Riwayat Transaksi',
            'transaksi'  => $this->Transaction_model->get_all_paginated($user_id, $config['per_page'], $start, $filters),
            'pagination' => $this->pagination->create_links(),
            'filters'    => $filters,
            'akun_list'  => $this->Akun_model->get_active_by_user($user_id), // Hanya akun aktif untuk dropdown
            'kategori_list' => $this->Kategori_model->get_by_user($user_id)
        ];

        $data['content'] = $this->load->view('transaction/index', $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    /**
     * Handle proses insert transaksi.
     */
    public function store()
    {
        $this->_validate_form();

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors('<span>', '</span><br>'));
            redirect('transaction');
            return;
        }

        $user_id = $this->current_user_id;
        $tipe    = $this->input->post('tipe', TRUE);

        // Upload handler
        $lampiran = $this->_handle_upload();
        if (isset($lampiran['error'])) {
            $this->session->set_flashdata('error', $lampiran['error']);
            redirect('transaction');
            return;
        }

        $data = [
            'user_id'           => $user_id,
            'akun_id'           => (int) $this->input->post('akun_id', TRUE),
            'tipe'              => $tipe,
            'jumlah'            => str_replace(['.', ','], '', $this->input->post('jumlah', TRUE)), // Bersihkan format uang
            'tanggal_transaksi' => $this->input->post('tanggal_transaksi', TRUE),
            'catatan'           => $this->input->post('catatan', TRUE),
            'lampiran'          => $lampiran['file_name'] ?? NULL
        ];

        if ($tipe === 'transfer') {
            $data['target_akun_id'] = (int) $this->input->post('target_akun_id', TRUE);
            $data['kategori_id']    = NULL; // Transfer tidak punya kategori

            // Validasi: akun asal tidak boleh sama dengan target
            if ($data['akun_id'] === $data['target_akun_id']) {
                $this->session->set_flashdata('error', 'Akun asal dan akun tujuan transfer tidak boleh sama.');
                redirect('transaction');
                return;
            }
        } else {
            $data['kategori_id']    = (int) $this->input->post('kategori_id', TRUE);
            $data['target_akun_id'] = NULL;
        }

        if ($this->Transaction_model->insert_transaction($data)) {
            $this->session->set_flashdata('success', 'Transaksi berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan transaksi. Pastikan akun valid.');
        }

        redirect('transaction');
    }

    /**
     * Handle proses update (khusus non-transfer).
     */
    public function update($id)
    {
        $user_id = $this->current_user_id;
        $old_trx = $this->Transaction_model->get_by_id($id, $user_id);

        if (!$old_trx) {
            show_404();
        }

        if ($old_trx['tipe'] === 'transfer') {
            $this->session->set_flashdata('error', 'Transaksi transfer tidak dapat diedit. Silakan hapus dan buat ulang.');
            redirect('transaction');
            return;
        }

        $this->_validate_form('update');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors('<span>', '</span><br>'));
            redirect('transaction');
            return;
        }

        $new_lampiran = $this->_handle_upload();
        if (isset($new_lampiran['error'])) {
            $this->session->set_flashdata('error', $new_lampiran['error']);
            redirect('transaction');
            return;
        }

        $data = [
            'akun_id'           => (int) $this->input->post('akun_id', TRUE),
            'tipe'              => $this->input->post('tipe', TRUE),
            'kategori_id'       => (int) $this->input->post('kategori_id', TRUE),
            'jumlah'            => str_replace(['.', ','], '', $this->input->post('jumlah', TRUE)),
            'tanggal_transaksi' => $this->input->post('tanggal_transaksi', TRUE),
            'catatan'           => $this->input->post('catatan', TRUE)
        ];

        if (!empty($new_lampiran['file_name'])) {
            $data['lampiran'] = $new_lampiran['file_name'];
            // Hapus file lama jika ada
            if (!empty($old_trx['lampiran']) && file_exists(FCPATH . 'uploads/lampiran/' . $old_trx['lampiran'])) {
                unlink(FCPATH . 'uploads/lampiran/' . $old_trx['lampiran']);
            }
        }

        if ($data['tipe'] === 'transfer') {
            $this->session->set_flashdata('error', 'Tidak diizinkan mengubah tipe menjadi transfer.');
            redirect('transaction');
            return;
        }

        if ($this->Transaction_model->update_transaction($id, $user_id, $data)) {
            $this->session->set_flashdata('success', 'Transaksi berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui transaksi.');
        }

        redirect('transaction');
    }

    /**
     * Soft Delete transaksi (dengan reversal saldo).
     */
    public function delete($id)
    {
        if ($this->Transaction_model->delete_transaction($id, $this->current_user_id)) {
            $this->session->set_flashdata('success', 'Transaksi berhasil dihapus (dikembalikan ke saldo awal).');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus transaksi atau transaksi tidak ditemukan.');
        }
        redirect('transaction');
    }

    /**
     * Restore transaksi (mengembalikan efek saldo).
     */
    public function restore($id)
    {
        if ($this->Transaction_model->restore_transaction($id, $this->current_user_id)) {
            $this->session->set_flashdata('success', 'Transaksi berhasil direstore (diterapkan kembali ke saldo).');
        } else {
            $this->session->set_flashdata('error', 'Gagal me-restore transaksi.');
        }
        
        // Tetap di halaman dengan filter show_deleted=1
        redirect('transaction?show_deleted=1');
    }

    // =========================================================
    // PRIVATE METHODS
    // =========================================================

    private function _validate_form($action = 'store')
    {
        $this->form_validation->set_rules('tipe', 'Tipe Transaksi', 'required|in_list[pemasukan,pengeluaran,transfer]');
        $this->form_validation->set_rules('akun_id', 'Akun', 'required|integer');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required');
        $this->form_validation->set_rules('tanggal_transaksi', 'Tanggal', 'required|regex_match[/^\d{4}-\d{2}-\d{2}$/]');
        $this->form_validation->set_rules('catatan', 'Catatan', 'max_length[255]');

        $tipe = $this->input->post('tipe');
        if ($tipe === 'transfer') {
            $this->form_validation->set_rules('target_akun_id', 'Target Akun', 'required|integer');
        } else {
            $this->form_validation->set_rules('kategori_id', 'Kategori', 'required|integer');
        }
    }

    private function _handle_upload()
    {
        if (empty($_FILES['lampiran']['name'])) {
            return []; // Tidak ada file yang diupload, tidak masalah (nullable)
        }

        $config['upload_path']   = FCPATH . 'uploads/lampiran/';
        $config['allowed_types'] = 'jpg|jpeg|png|pdf';
        $config['max_size']      = 2048; // 2MB
        $config['encrypt_name']  = TRUE; // Acak nama file demi keamanan

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('lampiran')) {
            return ['error' => $this->upload->display_errors('','')];
        }

        return ['file_name' => $this->upload->data('file_name')];
    }
}
