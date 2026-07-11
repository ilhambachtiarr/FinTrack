<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Transaction_model
 *
 * Bertanggung jawab atas pengelolaan data transaksi (CRUD).
 * Menerapkan fitur Database Transactions (commit/rollback) untuk menjamin
 * integritas saldo akun.
 * Mendukung Soft Delete dan Restore transaksi.
 *
 * @package  Keuangan Pribadi
 * @version  1.0.0
 */
class Transaction_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil data transaksi dengan paginasi dan filter (Anti-IDOR included).
     */
    public function get_all_paginated($user_id, $limit, $start, $filters = [])
    {
        $this->db->select('transaksi.*, 
                           akun.nama_akun, akun.ikon as ikon_akun, akun.kode_warna as warna_akun, 
                           kategori.nama_kategori, kategori.ikon as ikon_kategori, kategori.kode_warna as warna_kategori, 
                           target.nama_akun as nama_target,
                           transaksi.sumber, transaksi.nama_merchant');
        $this->db->from('transaksi');
        $this->db->join('akun', 'transaksi.akun_id = akun.id', 'left');
        $this->db->join('kategori', 'transaksi.kategori_id = kategori.id', 'left');
        $this->db->join('akun target', 'transaksi.target_akun_id = target.id', 'left');
        
        // WAJIB Anti-IDOR
        $this->db->where('transaksi.user_id', (int) $user_id);
        
        // Secara default tidak menampilkan yang soft-deleted
        $show_deleted = $filters['show_deleted'] ?? false;
        if (!$show_deleted) {
            $this->db->where('transaksi.deleted_at IS NULL');
        }

        // Filters
        if (!empty($filters['bulan']) && !empty($filters['tahun'])) {
            $this->db->where('MONTH(transaksi.tanggal_transaksi)', $filters['bulan']);
            $this->db->where('YEAR(transaksi.tanggal_transaksi)', $filters['tahun']);
        }
        if (!empty($filters['tipe'])) {
            $this->db->where('transaksi.tipe', $filters['tipe']);
        }
        if (!empty($filters['akun_id'])) {
            // Bisa sebagai sumber atau target (jika transfer)
            $this->db->group_start();
            $this->db->where('transaksi.akun_id', $filters['akun_id']);
            $this->db->or_where('transaksi.target_akun_id', $filters['akun_id']);
            $this->db->group_end();
        }

        $this->db->order_by('transaksi.tanggal_transaksi', 'DESC');
        $this->db->order_by('transaksi.id', 'DESC');
        $this->db->limit($limit, $start);

        return $this->db->get()->result_array();
    }

    /**
     * Hitung total baris untuk pagination
     */
    public function count_all($user_id, $filters = [])
    {
        $this->db->where('user_id', (int) $user_id);
        
        $show_deleted = $filters['show_deleted'] ?? false;
        if (!$show_deleted) {
            $this->db->where('deleted_at IS NULL');
        }

        if (!empty($filters['bulan']) && !empty($filters['tahun'])) {
            $this->db->where('MONTH(tanggal_transaksi)', $filters['bulan']);
            $this->db->where('YEAR(tanggal_transaksi)', $filters['tahun']);
        }
        if (!empty($filters['tipe'])) {
            $this->db->where('tipe', $filters['tipe']);
        }
        if (!empty($filters['akun_id'])) {
            $this->db->group_start();
            $this->db->where('akun_id', $filters['akun_id']);
            $this->db->or_where('target_akun_id', $filters['akun_id']);
            $this->db->group_end();
        }

        return $this->db->count_all_results('transaksi');
    }

    /**
     * Ambil 1 baris transaksi berdasarkan ID (Anti-IDOR)
     */
    public function get_by_id($id, $user_id)
    {
        return $this->db->where('id', (int) $id)
                        ->where('user_id', (int) $user_id)
                        ->get('transaksi')
                        ->row_array();
    }

    // =========================================================
    // CORE TRANSACTION LOGIC (ACID)
    // =========================================================

    /**
     * Insert Transaksi Baru dan Update Saldo Akun.
     */
    public function insert_transaction($data)
    {
        $this->db->trans_start();

        // 1. Insert transaksi
        $this->db->insert('transaksi', $data);
        $insert_id = $this->db->insert_id();

        // 2. Modifikasi Saldo Akun
        $this->_apply_balance_effect($data['tipe'], $data['akun_id'], $data['target_akun_id'] ?? null, $data['jumlah'], 'apply');

        $this->db->trans_complete();
        return $this->db->trans_status() ? $insert_id : false;
    }

    /**
     * Update Transaksi (Khusus Pemasukan & Pengeluaran, TIDAK UNTUK TRANSFER)
     */
    public function update_transaction($id, $user_id, $new_data)
    {
        // Pastikan kita mendapatkan transaksi lama yang valid (Anti-IDOR)
        $old_trx = $this->get_by_id($id, $user_id);
        if (!$old_trx) return false;

        // Validasi: Dilarang edit transaksi transfer
        if ($old_trx['tipe'] === 'transfer' || $new_data['tipe'] === 'transfer') {
            return false;
        }

        $this->db->trans_start();

        // 1. REVERSE efek saldo lama
        $this->_apply_balance_effect($old_trx['tipe'], $old_trx['akun_id'], null, $old_trx['jumlah'], 'reverse');

        // 2. UPDATE data transaksi
        $new_data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        $this->db->update('transaksi', $new_data);

        // 3. APPLY efek saldo baru
        $this->_apply_balance_effect($new_data['tipe'], $new_data['akun_id'], null, $new_data['jumlah'], 'apply');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Hapus Transaksi (Soft Delete) dan Reverse Saldo
     */
    public function delete_transaction($id, $user_id)
    {
        $old_trx = $this->get_by_id($id, $user_id);
        if (!$old_trx) return false;

        // Cegah penghapusan ganda
        if (!is_null($old_trx['deleted_at'])) return true;

        $this->db->trans_start();

        // 1. Jika transaksi struk → hard delete item-itemnya
        //    (Soft delete tidak men-trigger FK ON DELETE CASCADE,
        //     sehingga item akan menjadi orphan dan tidak bisa diakses.)
        if (!empty($old_trx['sumber']) && $old_trx['sumber'] === 'struk') {
            $this->db->where('transaksi_id', (int) $id)->delete('transaksi_item');
        }

        // 2. Soft Delete transaksi utama
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        $this->db->update('transaksi', [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        // 3. REVERSE efek saldo
        $this->_apply_balance_effect($old_trx['tipe'], $old_trx['akun_id'], $old_trx['target_akun_id'] ?? null, $old_trx['jumlah'], 'reverse');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Restore Transaksi (Batal Soft Delete) dan Apply Saldo Kembali
     */
    public function restore_transaction($id, $user_id)
    {
        $old_trx = $this->get_by_id($id, $user_id);
        if (!$old_trx) return false;

        // Jika tidak dalam kondisi terhapus, abaikan
        if (is_null($old_trx['deleted_at'])) return true;

        $this->db->trans_start();

        // 1. Batal Soft Delete
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        $this->db->update('transaksi', [
            'deleted_at' => NULL,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // 2. APPLY efek saldo kembali
        $this->_apply_balance_effect($old_trx['tipe'], $old_trx['akun_id'], $old_trx['target_akun_id'] ?? null, $old_trx['jumlah'], 'apply');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // =========================================================
    // STRUK / OCR — Multi-Item Transaction Logic
    // =========================================================

    /**
     * Buat transaksi dari hasil scan struk (multi-item, ACID).
     *
     * @param  int    $user_id
     * @param  int    $akun_id
     * @param  string $tipe           'pemasukan' atau 'pengeluaran'
     * @param  array  $items          [{nama_item, qty, harga_satuan, subtotal, urutan}]
     * @param  string $nama_merchant
     * @param  string $tanggal        Format Y-m-d
     * @param  string $catatan
     * @param  string $kategori_id
     * @param  string $ocr_raw_text
     * @return int|false insert_id jika sukses, false jika gagal
     */
    public function create_dari_struk($user_id, $akun_id, $tipe, $items, $nama_merchant, $tanggal, $catatan, $kategori_id, $ocr_raw_text = '')
    {
        // Hitung jumlah total dari item (sumber kebenaran utama)
        $jumlah_total = 0;
        foreach ($items as $item) {
            $jumlah_total += (float) $item['subtotal'];
        }

        if ($jumlah_total <= 0) return false;

        $this->db->trans_start();

        // 1. Insert 1 baris transaksi utama
        $data_transaksi = [
            'user_id'         => (int) $user_id,
            'akun_id'         => (int) $akun_id,
            'tipe'            => $tipe,
            'sumber'          => 'struk',
            'jumlah'          => $jumlah_total,
            'kategori_id'     => $kategori_id ?: NULL,
            'tanggal_transaksi' => $tanggal,
            'catatan'         => $catatan,
            'nama_merchant'   => $nama_merchant,
            'ocr_raw_text'    => $ocr_raw_text,
            'created_at'      => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('transaksi', $data_transaksi);
        $transaksi_id = $this->db->insert_id();

        // 2. Loop insert item ke transaksi_item
        foreach ($items as $urutan => $item) {
            $this->db->insert('transaksi_item', [
                'transaksi_id'  => $transaksi_id,
                'nama_item'     => $item['nama_item'],
                'qty'           => (float) ($item['qty'] ?? 1),
                'harga_satuan'  => (float) $item['harga_satuan'],
                'subtotal'      => (float) $item['subtotal'],
                'urutan'        => $urutan,
            ]);
        }

        // 3. Update saldo akun
        $this->_apply_balance_effect($tipe, $akun_id, null, $jumlah_total, 'apply');

        $this->db->trans_complete();
        return $this->db->trans_status() ? $transaksi_id : false;
    }

    /**
     * Update item-item dari transaksi struk (delete lama + insert ulang, ACID).
     * Juga menyesuaikan saldo akun berdasarkan selisih jumlah lama vs baru.
     *
     * @param  int   $transaksi_id
     * @param  int   $user_id
     * @param  array $items_baru     Array item baru
     * @param  string $nama_merchant (opsional untuk update)
     * @param  string $catatan
     * @return bool
     */
    public function update_dari_struk($transaksi_id, $user_id, $items_baru, $nama_merchant = null, $catatan = null)
    {
        // Anti-IDOR: pastikan transaksi ini milik user
        $old_trx = $this->get_by_id($transaksi_id, $user_id);
        if (!$old_trx) return false;

        // Hitung jumlah baru dari item hasil edit
        $jumlah_baru = 0;
        foreach ($items_baru as $item) {
            $jumlah_baru += (float) $item['subtotal'];
        }

        if ($jumlah_baru <= 0) return false;

        $jumlah_lama = (float) $old_trx['jumlah'];

        $this->db->trans_start();

        // 1. Reverse saldo lama
        $this->_apply_balance_effect($old_trx['tipe'], $old_trx['akun_id'], null, $jumlah_lama, 'reverse');

        // 2. Update transaksi utama
        $update_data = [
            'jumlah'     => $jumlah_baru,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($nama_merchant !== null) $update_data['nama_merchant'] = $nama_merchant;
        if ($catatan !== null) $update_data['catatan'] = $catatan;

        $this->db->where('id', $transaksi_id)->where('user_id', $user_id)->update('transaksi', $update_data);

        // 3. Hapus semua item lama
        $this->db->where('transaksi_id', $transaksi_id)->delete('transaksi_item');

        // 4. Insert ulang item baru
        foreach ($items_baru as $urutan => $item) {
            $this->db->insert('transaksi_item', [
                'transaksi_id'  => $transaksi_id,
                'nama_item'     => $item['nama_item'],
                'qty'           => (float) ($item['qty'] ?? 1),
                'harga_satuan'  => (float) $item['harga_satuan'],
                'subtotal'      => (float) $item['subtotal'],
                'urutan'        => $urutan,
            ]);
        }

        // 5. Apply saldo baru
        $this->_apply_balance_effect($old_trx['tipe'], $old_trx['akun_id'], null, $jumlah_baru, 'apply');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Ambil semua item dari satu transaksi struk (Anti-IDOR via transaksi_id + user_id)
     *
     * @param  int $transaksi_id
     * @param  int $user_id
     * @return array|false
     */
    public function get_items_by_transaksi($transaksi_id, $user_id)
    {
        // Verifikasi kepemilikan transaksi
        $trx = $this->get_by_id($transaksi_id, $user_id);
        if (!$trx) return false;

        return $this->db
            ->where('transaksi_id', (int) $transaksi_id)
            ->order_by('urutan', 'ASC')
            ->get('transaksi_item')
            ->result_array();
    }

    // =========================================================
    // PRIVATE HELPER (DRY PRINCIPLE)
    // =========================================================

    /**
     * Terapkan logika perubahan saldo.
     * Menggunakan Query String (SET saldo = saldo + X) agar aman dari race condition.
     * 
     * @param string $tipe ('pemasukan', 'pengeluaran', 'transfer')
     * @param int $akun_id
     * @param int|null $target_akun_id
     * @param float $jumlah
     * @param string $action ('apply' = eksekusi normal, 'reverse' = pembalikan/undo)
     */
    private function _apply_balance_effect($tipe, $akun_id, $target_akun_id, $jumlah, $action = 'apply')
    {
        $jumlah = (float) $jumlah;

        if ($action === 'apply') {
            
            if ($tipe === 'pemasukan') {
                $this->db->set('saldo', "saldo + {$jumlah}", FALSE)->where('id', $akun_id)->update('akun');
            } 
            elseif ($tipe === 'pengeluaran') {
                $this->db->set('saldo', "saldo - {$jumlah}", FALSE)->where('id', $akun_id)->update('akun');
            } 
            elseif ($tipe === 'transfer') {
                $this->db->set('saldo', "saldo - {$jumlah}", FALSE)->where('id', $akun_id)->update('akun');
                $this->db->set('saldo', "saldo + {$jumlah}", FALSE)->where('id', $target_akun_id)->update('akun');
            }

        } elseif ($action === 'reverse') {

            // Kebalikan dari apply
            if ($tipe === 'pemasukan') {
                $this->db->set('saldo', "saldo - {$jumlah}", FALSE)->where('id', $akun_id)->update('akun');
            } 
            elseif ($tipe === 'pengeluaran') {
                $this->db->set('saldo', "saldo + {$jumlah}", FALSE)->where('id', $akun_id)->update('akun');
            } 
            elseif ($tipe === 'transfer') {
                $this->db->set('saldo', "saldo + {$jumlah}", FALSE)->where('id', $akun_id)->update('akun');
                $this->db->set('saldo', "saldo - {$jumlah}", FALSE)->where('id', $target_akun_id)->update('akun');
            }

        }
    }
}
