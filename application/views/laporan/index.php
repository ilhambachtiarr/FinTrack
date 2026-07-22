<!-- Header & Filters -->
<div class="mb-6 bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Laporan Keuangan</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                Laporan Arus Kas <?= htmlspecialchars($judul_rentang) ?>
            </p>
        </div>

        <form method="GET" action="<?= site_url('laporan') ?>" class="flex flex-col sm:flex-row gap-3 bg-gray-50 dark:bg-gray-700 p-2 rounded-lg">
            
            <!-- Mode Toggle -->
            <div class="flex bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-600 p-1">
                <label class="cursor-pointer">
                    <input type="radio" name="mode" value="minggu" <?= $current_mode === 'minggu' ? 'checked' : '' ?> onchange="this.form.submit()" class="sr-only peer">
                    <div class="px-3 py-1.5 text-sm font-medium rounded text-gray-500 dark:text-gray-400 peer-checked:bg-primary-50 peer-checked:text-primary-600 dark:peer-checked:bg-primary-900/30 dark:peer-checked:text-primary-400 transition-colors">Per Minggu</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="mode" value="bulan" <?= $current_mode === 'bulan' ? 'checked' : '' ?> onchange="this.form.submit()" class="sr-only peer">
                    <div class="px-3 py-1.5 text-sm font-medium rounded text-gray-500 dark:text-gray-400 peer-checked:bg-primary-50 peer-checked:text-primary-600 dark:peer-checked:bg-primary-900/30 dark:peer-checked:text-primary-400 transition-colors">Per Bulan</div>
                </label>
            </div>

            <!-- Tanggal Input -->
            <?php if ($current_mode === 'minggu'): ?>
                <div class="flex items-center">
                    <input type="date" name="tanggal" value="<?= htmlspecialchars($current_tanggal) ?>" class="block w-full sm:w-auto border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-800 dark:text-white focus:ring-primary-500 focus:border-primary-500 border p-2">
                </div>
            <?php else: ?>
                <div class="flex items-center gap-2">
                    <select name="bulan" class="block w-full sm:w-auto border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-800 dark:text-white border p-2">
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $current_bulan == $m ? 'selected' : '' ?>>
                                <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <select name="tahun" class="block w-full sm:w-auto border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-800 dark:text-white border p-2">
                        <?php $cy = date('Y'); for($y = $cy; $y >= $cy-5; $y--): ?>
                            <option value="<?= $y ?>" <?= $current_tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            <?php endif; ?>

            <button type="submit" class="inline-flex justify-center items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white text-sm font-medium rounded-md transition-colors">
                <i class="fa-solid fa-rotate-right mr-1.5"></i> Terapkan
            </button>
        </form>
        
        <!-- Tombol Download (PDF & CSV) -->
        <div class="flex items-center gap-2">
            <a href="<?= site_url("laporan/download_csv?" . http_build_query($this->input->get())) ?>"
               class="inline-flex justify-center items-center h-11 px-5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-medium rounded-lg shadow-sm transition-all hover:shadow text-sm"
               title="Ekspor Data Transaksi (Bisa dibuka di Excel)">
                <i class="fa-solid fa-file-csv text-lg mr-2"></i> Ekspor CSV
            </a>
            
            <a href="<?= site_url("laporan/download_pdf?" . http_build_query($this->input->get())) ?>" target="_blank"
               class="inline-flex justify-center items-center h-11 px-5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-medium rounded-lg shadow-sm transition-all hover:shadow text-sm">
                <i class="fa-solid fa-file-pdf text-lg mr-2"></i> Unduh PDF
            </a>
        </div>
    </div>
</div>

<!-- 3 Kartu Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Total Pemasukan</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400"><?= format_rupiah($summary['total_pemasukan']) ?></p>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-arrow-turn-down"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Total Pengeluaran</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400"><?= format_rupiah($summary['total_pengeluaran']) ?></p>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-arrow-turn-up"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Arus Kas Bersih</p>
                <?php $is_plus = $summary['arus_kas_bersih'] >= 0; ?>
                <p class="text-2xl font-bold <?= $is_plus ? 'text-primary-600 dark:text-primary-400' : 'text-gray-900 dark:text-white' ?>">
                    <?= $is_plus ? '+' : '' ?><?= format_rupiah($summary['arus_kas_bersih']) ?>
                </p>
            </div>
            <div class="w-10 h-10 rounded-full <?= $is_plus ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-600' ?> flex items-center justify-center text-xl">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Donut Chart & Breakdown -->
    <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Pengeluaran per Kategori</h3>
        
        <?php if(empty($breakdown_pengeluaran)): ?>
            <div class="py-10 text-center text-gray-500 dark:text-gray-400 text-sm">Belum ada pengeluaran di periode ini.</div>
        <?php else: ?>
            <div id="kategori-chart" class="mb-4"></div>
            
            <div class="space-y-3 mt-4 max-h-60 overflow-y-auto pr-2">
                <?php foreach($breakdown_pengeluaran as $b): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: <?= $b['kode_warna'] ?>"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate"><?= htmlspecialchars($b['nama_kategori']) ?></span>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-sm font-medium text-gray-900 dark:text-white"><?= format_rupiah($b['total']) ?></div>
                        <div class="text-xs text-gray-500"><?= $b['persentase'] ?>%</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabel Transaksi -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Detail Transaksi (<?= count($detail_transaksi) ?>)</h3>
        </div>
        
        <div class="overflow-x-auto max-h-[450px] overflow-y-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400 sticky top-0">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Catatan</th>
                        <th class="px-4 py-3">Akun</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-right">Jumlah</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if(empty($detail_transaksi)): ?>
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada transaksi di periode ini.</td></tr>
                    <?php else: ?>
                        <?php foreach($detail_transaksi as $t): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3"><?= date('d/m/Y', strtotime($t['tanggal_transaksi'])) ?></td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                <?php if(isset($t['sumber']) && $t['sumber'] === 'struk'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">🧾 Struk</span>
                                    <span class="ml-1 text-gray-600 dark:text-gray-300"><?= htmlspecialchars($t['nama_merchant'] ?: 'Tanpa Merchant') ?></span>
                                <?php else: ?>
                                    <?= htmlspecialchars($t['catatan'] ?: '-') ?>
                                    <?php if($t['tipe'] === 'transfer'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 ml-2 rounded text-[10px] font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">TRF</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($t['tipe'] === 'transfer'): ?>
                                    <?= htmlspecialchars($t['nama_akun']) ?> &rarr; <?= htmlspecialchars($t['nama_target']) ?>
                                <?php else: ?>
                                    <?= htmlspecialchars($t['nama_akun']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($t['tipe'] !== 'transfer'): ?>
                                    <span class="inline-flex items-center gap-1.5">
                                        <div class="w-2 h-2 rounded-full" style="background-color: <?= $t['warna_kategori'] ?? '#9CA3AF' ?>"></div>
                                        <?= htmlspecialchars($t['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right font-bold <?= $t['tipe'] === 'pemasukan' ? 'text-green-600 dark:text-green-400' : ($t['tipe'] === 'pengeluaran' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100') ?>">
                                <?= $t['tipe'] === 'pemasukan' ? '+' : ($t['tipe'] === 'pengeluaran' ? '-' : '') ?> 
                                <?= format_rupiah($t['jumlah']) ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if(isset($t['sumber']) && $t['sumber'] === 'struk'): ?>
                                    <button onclick="lihatDetailItem(<?= $t['id'] ?>)" class="text-amber-600 hover:text-amber-800 p-1" title="Lihat Detail Item"><i class="fa-solid fa-list-ul"></i></button>
                                <?php else: ?>
                                    <span class="text-gray-300 dark:text-gray-600">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script rendering ApexCharts untuk Preview Web -->
<?php if(!empty($breakdown_pengeluaran)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataKategori = <?= json_encode($breakdown_pengeluaran) ?>;
    const series = dataKategori.map(d => parseFloat(d.total));
    const labels = dataKategori.map(d => d.nama_kategori);
    const colors = dataKategori.map(d => d.kode_warna);

    const isDark = document.documentElement.classList.contains('dark');

    const options = {
        series: series,
        labels: labels,
        colors: colors,
        chart: {
            type: 'donut',
            height: 250,
            background: 'transparent'
        },
        plotOptions: {
            pie: {
                donut: { size: '65%' },
                expandOnClick: false
            }
        },
        stroke: { show: true, colors: isDark ? ['#1f2937'] : ['#ffffff'], width: 2 },
        dataLabels: { enabled: false },
        legend: { show: false },
        theme: { mode: isDark ? 'dark' : 'light' },
        tooltip: {
            y: {
                formatter: function (val) { return "Rp " + val.toLocaleString('id-ID'); }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#kategori-chart"), options);
    chart.render();

    // Re-render when theme changed
    window.addEventListener('theme-changed', function() {
        const dark = document.documentElement.classList.contains('dark');
        chart.updateOptions({
            stroke: { colors: dark ? ['#1f2937'] : ['#ffffff'] },
            theme: { mode: dark ? 'dark' : 'light' }
        });
    });
});
</script>
<?php endif; ?>

<!-- ============================================== -->
<!-- MODAL DETAIL ITEM STRUK -->
<!-- ============================================== -->
<div id="detail-item-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75" onclick="document.getElementById('detail-item-modal').classList.add('hidden')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl z-10">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="detail-item-title">Detail Item Struk</h3>
                <div class="flex items-center gap-2">
                    <button onclick="document.getElementById('detail-item-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 p-1"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
            </div>
            <div class="p-6">
                <div id="detail-item-body">
                    <!-- Diisi JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var STRUK_DETAIL_URL  = '<?= site_url("struk/get_detail_item") ?>';

    function lihatDetailItem(trxId) {
        var modal = document.getElementById('detail-item-modal');
        var body = document.getElementById('detail-item-body');
        body.innerHTML = '<div class="text-center py-8 text-gray-400"><svg class="animate-spin h-6 w-6 mx-auto" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
        modal.classList.remove('hidden');

        fetch(STRUK_DETAIL_URL + '/' + trxId, {credentials: 'same-origin'})
        .then(r => r.json())
        .then(function(data) {
            if (!data.success) {
                body.innerHTML = '<p class="text-red-500">' + (data.error || 'Gagal memuat item.') + '</p>';
                return;
            }
            var merchant = data.nama_merchant ? ('<p class="font-medium text-gray-700 dark:text-gray-300 mb-3">🏪 ' + escHtml(data.nama_merchant) + '</p>') : '';
            var rows = data.items.map(function(item) {
                return `<tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="py-2 pr-4 text-gray-800 dark:text-gray-200">${escHtml(item.nama_item)}</td>
                    <td class="py-2 px-2 text-center text-gray-500 dark:text-gray-400">${item.qty}</td>
                    <td class="py-2 px-2 text-right text-gray-600 dark:text-gray-400">Rp ${formatAngka(item.harga_satuan)}</td>
                    <td class="py-2 pl-4 text-right font-medium text-gray-900 dark:text-white">Rp ${formatAngka(item.subtotal)}</td>
                </tr>`;
            }).join('');
            var totalItem = data.items.reduce(function(s, i) { return s + parseFloat(i.subtotal); }, 0);
            body.innerHTML = merchant +
                '<div class="overflow-x-auto">' +
                '<table class="w-full text-sm">' +
                '<thead><tr class="text-xs text-gray-500 uppercase">' +
                '<th class="py-2 pr-4 text-left">Item</th><th class="py-2 px-2 text-center">Qty</th><th class="py-2 px-2 text-right">Harga</th><th class="py-2 pl-4 text-right">Subtotal</th>' +
                '</tr></thead>' +
                '<tbody>' + rows + '</tbody>' +
                '<tfoot><tr><td colspan="3" class="pt-3 text-right font-bold text-gray-800 dark:text-gray-200">Total</td><td class="pt-3 pl-4 text-right font-bold text-primary-600 dark:text-primary-400">Rp ' + formatAngka(totalItem) + '</td></tr></tfoot>' +
                '</table></div>';
            document.getElementById('detail-item-title').textContent = '🧾 Detail Item — ' + (data.nama_merchant || 'Struk');
        })
        .catch(function() {
            body.innerHTML = '<p class="text-red-500">Gagal memuat item. Periksa koneksi Anda.</p>';
        });
    }

    function formatAngka(n) { return Math.round(n).toLocaleString('id-ID'); }
    function escHtml(str) { var d = document.createElement('div'); d.textContent = str || ''; return d.innerHTML; }
</script>
