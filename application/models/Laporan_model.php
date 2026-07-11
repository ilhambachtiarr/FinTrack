<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {

    /**
     * Mengembalikan array [$awal, $akhir] untuk rentang Senin-Minggu.
     */
    public function get_rentang_minggu($tanggal)
    {
        $time = strtotime($tanggal);
        // Dapatkan hari Senin dari minggu tersebut
        $senin = date('Y-m-d', strtotime('monday this week', $time));
        if (date('w', $time) == 1) { // Jika hari ini sudah Senin, strtotime 'monday this week' mungkin menghasilkan Senin minggu depan atau minggu ini tergantung PHP version. Kita paskan saja.
            $senin = date('Y-m-d', $time);
        }
        $minggu = date('Y-m-d', strtotime('sunday this week', $time));
        if (date('w', $time) == 0) {
            $minggu = date('Y-m-d', $time);
            $senin = date('Y-m-d', strtotime('monday last week', $time));
        }

        return [$senin, $minggu];
    }

    /**
     * Mengembalikan array [$awal, $akhir] untuk rentang 1 bulan penuh.
     */
    public function get_rentang_bulan($bulan, $tahun)
    {
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $awal = "$tahun-$bulan-01";
        $akhir = date('Y-m-t', strtotime($awal)); // t = jumlah hari dalam bulan
        
        return [$awal, $akhir];
    }

    /**
     * Mendapatkan total pemasukan, pengeluaran, dan arus kas bersih
     */
    public function get_summary($user_id, $tanggal_awal, $tanggal_akhir)
    {
        $this->db->select("
            COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE 0 END), 0) as total_pemasukan,
            COALESCE(SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END), 0) as total_pengeluaran
        ", FALSE);
        $this->db->from('transaksi');
        $this->db->where('user_id', (int) $user_id);
        $this->db->where('deleted_at IS NULL');
        $this->db->where('tanggal_transaksi >=', $tanggal_awal);
        $this->db->where('tanggal_transaksi <=', $tanggal_akhir);

        $row = $this->db->get()->row_array();

        $total_pemasukan = (float) ($row['total_pemasukan'] ?? 0);
        $total_pengeluaran = (float) ($row['total_pengeluaran'] ?? 0);

        return [
            'total_pemasukan' => $total_pemasukan,
            'total_pengeluaran' => $total_pengeluaran,
            'arus_kas_bersih' => $total_pemasukan - $total_pengeluaran
        ];
    }

    /**
     * Mendapatkan breakdown kategori beserta persentase
     */
    public function get_breakdown_kategori($user_id, $tanggal_awal, $tanggal_akhir, $tipe = 'pengeluaran')
    {
        // Ambil total per kategori
        $this->db->select('kategori_id, k.nama_kategori, k.kode_warna, SUM(t.jumlah) as total');
        $this->db->from('transaksi t');
        $this->db->join('kategori k', 't.kategori_id = k.id', 'left');
        $this->db->where('t.user_id', (int) $user_id);
        $this->db->where('t.deleted_at IS NULL');
        $this->db->where('t.tipe', $tipe);
        $this->db->where('t.tanggal_transaksi >=', $tanggal_awal);
        $this->db->where('t.tanggal_transaksi <=', $tanggal_akhir);
        $this->db->group_by('t.kategori_id');
        $this->db->order_by('total', 'DESC');
        
        $kategori_data = $this->db->get()->result_array();

        // Hitung persentase
        $total_semua = 0;
        foreach ($kategori_data as $k) {
            $total_semua += $k['total'];
        }

        $result = [];
        foreach ($kategori_data as $k) {
            $persen = $total_semua > 0 ? ($k['total'] / $total_semua) * 100 : 0;
            $result[] = [
                'nama_kategori' => $k['nama_kategori'] ?: 'Tanpa Kategori',
                'kode_warna' => $k['kode_warna'] ?: '#9CA3AF',
                'total' => $k['total'],
                'persentase' => round($persen, 1)
            ];
        }

        return $result;
    }

    /**
     * Mendapatkan list transaksi terperinci (diurutkan per tanggal desc)
     */
    public function get_detail_transaksi($user_id, $tanggal_awal, $tanggal_akhir)
    {
        $this->db->select('t.*, a.nama_akun, k.nama_kategori, k.kode_warna as warna_kategori, at.nama_akun as nama_target, t.sumber, t.nama_merchant');
        $this->db->from('transaksi t');
        $this->db->join('akun a', 't.akun_id = a.id', 'left');
        $this->db->join('akun at', 't.target_akun_id = at.id', 'left');
        $this->db->join('kategori k', 't.kategori_id = k.id', 'left');
        
        $this->db->where('t.user_id', (int) $user_id);
        $this->db->where('t.deleted_at IS NULL');
        $this->db->where('t.tanggal_transaksi >=', $tanggal_awal);
        $this->db->where('t.tanggal_transaksi <=', $tanggal_akhir);
        
        $this->db->order_by('t.tanggal_transaksi', 'DESC');
        $this->db->order_by('t.id', 'DESC');
        
        return $this->db->get()->result_array();
    }
}
