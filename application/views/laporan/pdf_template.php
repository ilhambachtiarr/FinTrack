<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 10pt;
            line-height: 1.5;
        }
        
        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 3px solid #2563eb; /* Biru brand */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-title {
            font-size: 18pt;
            font-weight: bold;
            color: #1f2937;
            text-align: right;
        }
        .header-logo {
            font-size: 16pt;
            font-weight: bold;
            color: #2563eb;
        }
        
        /* Info */
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 9pt;
            color: #6b7280;
        }

        /* Summary Boxes (Menggunakan tabel 3 kolom) */
        .summary-table {
            width: 100%;
            margin-bottom: 30px;
            border-spacing: 10px;
            border-collapse: separate;
        }
        .summary-box {
            padding: 15px;
            border-radius: 8px;
            width: 33.33%;
            vertical-align: top;
        }
        .box-in { background-color: #ecfdf5; border: 1px solid #d1fae5; }
        .box-out { background-color: #fef2f2; border: 1px solid #fee2e2; }
        .box-net { background-color: #eff6ff; border: 1px solid #dbeafe; }
        
        .box-title { font-size: 9pt; color: #6b7280; margin-bottom: 5px; font-weight: normal; }
        .box-value { font-size: 14pt; font-weight: bold; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-blue { color: #2563eb; }

        /* Section Title */
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 15px;
            margin-top: 10px;
        }

        /* Kategori Breakdown */
        .kategori-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .kategori-table td {
            padding: 8px 5px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .cat-name { width: 30%; font-weight: bold; font-size: 9pt; }
        .cat-bar-container { width: 45%; }
        .cat-amount { width: 25%; text-align: right; font-size: 9pt; }
        
        /* Progress Bar di PDF */
        .progress-bg {
            background-color: #f3f4f6;
            width: 100%;
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-fill {
            height: 10px;
            border-radius: 5px;
        }

        /* Transaksi Detail */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .detail-table th {
            background-color: #374151;
            color: #ffffff;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .detail-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-table tr.stripe { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .badge-trf {
            background-color: #dbeafe;
            color: #1e40af;
            font-size: 7pt;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .badge-struk {
            background-color: #fef3c7;
            color: #92400e;
            font-size: 7pt;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="header-logo" width="30%">FinTrack</td>
            <td class="header-title" width="70%">Laporan Arus Kas</td>
        </tr>
    </table>

    <!-- Info User & Periode -->
    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>Pemilik Akun:</strong> <?= htmlspecialchars($current_user['name']) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($current_user['email']) ?>
            </td>
            <td width="50%" class="text-right">
                <strong>Periode:</strong> <?= htmlspecialchars($judul_rentang) ?><br>
                <strong>Dicetak:</strong> <?= date('d/m/Y H:i') ?>
            </td>
        </tr>
    </table>

    <!-- Ringkasan -->
    <table class="summary-table">
        <tr>
            <td class="summary-box box-in">
                <div class="box-title">Total Pemasukan</div>
                <div class="box-value text-green"><?= format_rupiah($summary['total_pemasukan']) ?></div>
            </td>
            <td class="summary-box box-out">
                <div class="box-title">Total Pengeluaran</div>
                <div class="box-value text-red"><?= format_rupiah($summary['total_pengeluaran']) ?></div>
            </td>
            <td class="summary-box box-net">
                <div class="box-title">Arus Kas Bersih</div>
                <?php $is_plus = $summary['arus_kas_bersih'] >= 0; ?>
                <div class="box-value <?= $is_plus ? 'text-blue' : '' ?>">
                    <?= $is_plus ? '+' : '' ?><?= format_rupiah($summary['arus_kas_bersih']) ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- Breakdown Kategori -->
    <div class="section-title">Breakdown Pengeluaran per Kategori</div>
    <?php if (empty($breakdown_pengeluaran)): ?>
        <p style="color: #6b7280; font-size: 9pt; font-style: italic;">Tidak ada pengeluaran di periode ini.</p>
    <?php else: ?>
        <table class="kategori-table">
            <?php foreach ($breakdown_pengeluaran as $b): ?>
            <tr>
                <td class="cat-name">
                    <!-- Dot warna manual krn HTML email/pdf kadang rewel -->
                    <span style="color: <?= $b['kode_warna'] ?>;">●</span> 
                    <?= htmlspecialchars($b['nama_kategori']) ?>
                </td>
                <td class="cat-bar-container">
                    <div class="progress-bg">
                        <!-- Bar persentase -->
                        <div class="progress-fill" style="width: <?= $b['persentase'] ?>%; background-color: <?= $b['kode_warna'] ?>;"></div>
                    </div>
                </td>
                <td class="cat-amount">
                    <strong><?= format_rupiah($b['total']) ?></strong><br>
                    <span style="color: #6b7280; font-size: 8pt;"><?= $b['persentase'] ?>%</span>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <br> <!-- Spacing -->

    <!-- Detail Transaksi -->
    <div class="section-title">Detail Transaksi</div>
    <table class="detail-table">
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th width="30%">Catatan</th>
                <th width="23%">Akun</th>
                <th width="15%">Kategori</th>
                <th width="20%" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($detail_transaksi)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b7280; font-style: italic;">Tidak ada data transaksi.</td>
                </tr>
            <?php else: ?>
                <?php $i = 0; foreach ($detail_transaksi as $t): 
                    $is_stripe = ($i % 2 !== 0) ? 'stripe' : '';
                ?>
                <tr class="<?= $is_stripe ?>">
                    <td><?= date('d/m', strtotime($t['tanggal_transaksi'])) ?></td>
                    <td>
                        <?php if (isset($t['sumber']) && $t['sumber'] === 'struk'): ?>
                            <span class="badge-struk">Struk</span> <?= htmlspecialchars($t['nama_merchant'] ?: 'Tanpa Merchant') ?>
                        <?php else: ?>
                            <?= htmlspecialchars($t['catatan'] ?: '-') ?>
                            <?php if ($t['tipe'] === 'transfer'): ?>
                                <span class="badge-trf">TRF</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($t['tipe'] === 'transfer'): ?>
                            <?= htmlspecialchars($t['nama_akun']) ?> &rarr; <br><?= htmlspecialchars($t['nama_target']) ?>
                        <?php else: ?>
                            <?= htmlspecialchars($t['nama_akun']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($t['tipe'] !== 'transfer'): ?>
                            <span style="color: <?= $t['warna_kategori'] ?? '#9CA3AF' ?>">●</span>
                            <?= htmlspecialchars($t['nama_kategori'] ?? 'Tanpa Kategori') ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php if ($t['tipe'] === 'pemasukan'): ?>
                            <span class="text-green">+ <?= format_rupiah($t['jumlah'], false) ?></span>
                        <?php elseif ($t['tipe'] === 'pengeluaran'): ?>
                            <span class="text-red">- <?= format_rupiah($t['jumlah'], false) ?></span>
                        <?php else: ?>
                            <?= format_rupiah($t['jumlah'], false) ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php $i++; endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php
        $has_struk = false;
        if (!empty($detail_transaksi)) {
            foreach ($detail_transaksi as $t) {
                if (isset($t['sumber']) && $t['sumber'] === 'struk') {
                    $has_struk = true;
                    break;
                }
            }
        }
        if ($has_struk):
    ?>
    <p style="font-size: 8pt; color: #6b7280; font-style: italic; margin-top: 10px;">
        *Detail item transaksi struk dapat dilihat langsung di aplikasi
    </p>
    <?php endif; ?>

</body>
</html>
