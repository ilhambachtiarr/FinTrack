<!-- Header Section -->
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight">Dashboard Overview</h1>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Ringkasan keuangan Anda bulan ini.</p>
</div>

<!-- Financial Health Insight Card -->
<?php if (isset($health_insight)): ?>
<div class="glass rounded-2xl p-5 shadow-xl border border-white/50 dark:border-slate-700/50 mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200/60 dark:border-slate-700/60 pb-4 mb-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 text-white flex items-center justify-center text-xl font-bold shrink-0 shadow-lg shadow-primary-500/30">
                <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Kesehatan Keuangan</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $health_insight['status_badge'] ?>">
                        <?= $health_insight['status_label'] ?>
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Analisis arus kas & ketahanan dana darurat Anda secara real-time</p>
            </div>
        </div>
        <div class="flex items-center gap-6 self-start md:self-auto">
            <div class="text-left md:text-right">
                <span class="text-xs text-gray-500 dark:text-gray-400 block">Skor Keuangan</span>
                <span class="text-2xl font-black tracking-tight <?= $health_insight['score_color'] ?>"><?= $health_insight['score'] ?><span class="text-sm font-normal text-gray-400">/100</span></span>
            </div>
        </div>
    </div>

    <!-- Metric Pills Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-gray-100/70 dark:bg-slate-800/60 border border-gray-200/60 dark:border-slate-700/50 rounded-xl p-3">
            <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1"><i class="fa-solid fa-piggy-bank text-emerald-500 mr-1"></i> Savings Rate</span>
            <span class="text-base font-bold text-gray-800 dark:text-gray-100"><?= $health_insight['savings_rate'] ?>%</span>
        </div>
        <div class="bg-gray-100/70 dark:bg-slate-800/60 border border-gray-200/60 dark:border-slate-700/50 rounded-xl p-3">
            <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1"><i class="fa-solid fa-shield-halved text-blue-500 mr-1"></i> Daya Tahan Kas</span>
            <span class="text-base font-bold text-gray-800 dark:text-gray-100"><?= $health_insight['runway_bulan'] ?> Bulan</span>
        </div>
        <div class="bg-gray-100/70 dark:bg-slate-800/60 border border-gray-200/60 dark:border-slate-700/50 rounded-xl p-3 col-span-2 md:col-span-1">
            <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1"><i class="fa-solid fa-scale-balanced text-amber-500 mr-1"></i> Arus Kas</span>
            <span class="text-base font-bold <?= $pemasukan_bulan >= $pengeluaran_bulan ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' ?>">
                <?= $pemasukan_bulan >= $pengeluaran_bulan ? 'Surplus' : 'Defisit' ?>
            </span>
        </div>
    </div>

    <!-- Smart Insights List -->
    <?php if (!empty($health_insight['insights'])): ?>
        <div class="space-y-2 pt-2 border-t border-gray-200/60 dark:border-slate-700/40">
            <?php foreach ($health_insight['insights'] as $item): ?>
                <div class="flex items-start gap-2.5 text-xs text-gray-600 dark:text-gray-300">
                    <i class="fa-solid <?= $item['icon'] ?> <?= $item['color'] ?> text-sm mt-0.5 shrink-0"></i>
                    <span>
                        <?php 
                            $formatted_text = preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-gray-900 dark:text-white font-semibold">$1</strong>', $item['text']);
                            echo $formatted_text;
                        ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>


<!-- Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Saldo -->
    <div class="bg-gradient-to-br from-primary-500 to-primary-700 dark:from-primary-600 dark:to-primary-900 rounded-2xl p-6 shadow-xl shadow-primary-500/20 border border-primary-400/30 relative overflow-hidden hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 group">
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-white/10 blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-sm font-medium text-primary-50 uppercase tracking-wider mb-1">Total Saldo</p>
                <h3 class="text-3xl font-extrabold text-white tracking-tight drop-shadow-sm"><?= format_rupiah($total_saldo) ?></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm text-white flex items-center justify-center shadow-inner">
                <i class="fa-solid fa-wallet text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Pemasukan Bulan Ini -->
    <div class="glass rounded-2xl p-6 shadow-xl shadow-emerald-500/5 border border-white/50 dark:border-slate-700/50 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 group relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-emerald-500/10 blur-2xl group-hover:bg-emerald-500/20 transition-colors duration-500"></div>
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Pemasukan Bulan Ini</p>
                <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight"><?= format_rupiah($pemasukan_bulan) ?></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/40 dark:to-emerald-800/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-arrow-turn-down text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Pengeluaran Bulan Ini -->
    <div class="glass rounded-2xl p-6 shadow-xl shadow-rose-500/5 border border-white/50 dark:border-slate-700/50 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 group relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-rose-500/10 blur-2xl group-hover:bg-rose-500/20 transition-colors duration-500"></div>
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Pengeluaran Bulan Ini</p>
                <h3 class="text-3xl font-extrabold text-rose-600 dark:text-rose-400 tracking-tight"><?= format_rupiah($pengeluaran_bulan) ?></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-100 to-rose-200 dark:from-rose-900/40 dark:to-rose-800/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-arrow-turn-up text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Line Chart (Arus Kas) -->
    <div class="glass rounded-2xl p-6 shadow-xl border border-white/50 dark:border-slate-700/50 lg:col-span-2 relative overflow-hidden group">
        <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 tracking-tight">Arus Kas (30 Hari Terakhir)</h3>
        <div id="cashflow-chart" class="w-full h-[300px]"></div>
    </div>

    <!-- Donut Chart (Kategori Pengeluaran) -->
    <div class="glass rounded-2xl p-6 shadow-xl border border-white/50 dark:border-slate-700/50 relative overflow-hidden group">
        <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 tracking-tight">Pengeluaran per Kategori</h3>
        <?php $kategori_data = json_decode($chart_kategori, true); ?>
        <?php if(empty($kategori_data['series'])): ?>
            <div class="flex flex-col items-center justify-center h-[280px] text-gray-400 bg-gray-50/50 dark:bg-slate-800/30 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                <i class="fa-solid fa-chart-pie text-5xl mb-3 text-gray-300 dark:text-gray-600"></i>
                <p class="text-sm font-medium">Belum ada data pengeluaran</p>
            </div>
        <?php else: ?>
            <div id="category-chart" class="w-full flex justify-center h-[300px]"></div>
        <?php endif; ?>
    </div>
</div>

<!-- Status Anggaran Kategori Widget -->
<?php if (!empty($budget_summary['items'])): ?>
<div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h3 class="text-base font-semibold">Status Anggaran Kategori</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pemantauan limit pengeluaran bulanan Anda</p>
        </div>
        <a href="<?= site_url('anggaran') ?>" class="text-xs font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 flex items-center gap-1">
            Kelola Anggaran <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach (array_slice($budget_summary['items'], 0, 3) as $item): ?>
            <div class="p-3.5 rounded-lg border border-gray-100 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-700/30">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded flex items-center justify-center text-white text-xs font-bold" style="background-color: <?= $item['kode_warna'] ?: '#3B82F6' ?>;">
                            <i class="<?= $item['ikon'] ?: 'fa-solid fa-tag' ?>"></i>
                        </div>
                        <span class="text-xs font-bold truncate max-w-[120px]"><?= htmlspecialchars($item['nama_kategori']) ?></span>
                    </div>
                    <span class="text-xs font-semibold <?= $item['text_color'] ?>"><?= min(100, $item['persentase']) ?>%</span>
                </div>

                <div class="w-full bg-gray-200 dark:bg-gray-600 h-2 rounded-full overflow-hidden mb-2">
                    <div class="h-full <?= $item['progress_color'] ?>" style="width: <?= min(100, $item['persentase']) ?>%;"></div>
                </div>

                <div class="flex justify-between text-[11px] text-gray-500 dark:text-gray-400">
                    <span>Terpakai: <?= format_rupiah($item['terpakai']) ?></span>
                    <span>Limit: <?= format_rupiah($item['nominal_batas']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Transaksi Terbaru -->
<div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-base font-semibold">Transaksi Terbaru</h3>
        <a href="#" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">Lihat Semua</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3 rounded-l-lg">Tanggal</th>
                    <th scope="col" class="px-4 py-3">Keterangan</th>
                    <th scope="col" class="px-4 py-3">Kategori</th>
                    <th scope="col" class="px-4 py-3 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php if(empty($transaksi_terbaru)): ?>
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            Belum ada transaksi bulan ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($transaksi_terbaru as $trx): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= format_tanggal($trx['tanggal_transaksi']) ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium"><?= htmlspecialchars($trx['catatan'] ?: 'Tanpa keterangan') ?></div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    <i class="fa-solid fa-wallet text-gray-400 mr-1"></i>
                                    <?= htmlspecialchars($trx['nama_akun']) ?>
                                    <?php if($trx['tipe'] === 'transfer' && !empty($trx['nama_target'])): ?>
                                        <i class="fa-solid fa-arrow-right mx-1 text-gray-400"></i>
                                        <?= htmlspecialchars($trx['nama_target']) ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($trx['tipe'] === 'transfer'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        <i class="fa-solid fa-money-bill-transfer mr-1"></i> Transfer
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" 
                                          style="background-color: <?= $trx['warna_kategori'] ?? '#9CA3AF' ?>20; color: <?= $trx['warna_kategori'] ?? '#9CA3AF' ?>">
                                        <i class="fa-solid fa-<?= $trx['ikon_kategori'] ?? 'tag' ?> mr-1"></i>
                                        <?= htmlspecialchars($trx['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right font-medium whitespace-nowrap <?= $trx['tipe'] === 'pemasukan' ? 'text-green-600 dark:text-green-400' : ($trx['tipe'] === 'pengeluaran' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100') ?>">
                                <?= $trx['tipe'] === 'pemasukan' ? '+' : ($trx['tipe'] === 'pengeluaran' ? '-' : '') ?> 
                                <?= format_rupiah($trx['jumlah']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#9ca3af' : '#6b7280';
        const tooltipBg = isDark ? '#1f2937' : '#ffffff';
        
        // Data from PHP
        const chart30Hari = <?= $chart_30_hari ?>;
        const chartKategori = <?= $chart_kategori ?>;

        // Shared function to update theme dynamically
        const getChartThemeOptions = () => {
            const isDarkNow = document.documentElement.classList.contains('dark');
            return {
                theme: { mode: isDarkNow ? 'dark' : 'light' },
                chart: { background: 'transparent' },
                tooltip: { theme: isDarkNow ? 'dark' : 'light' }
            };
        };

        // 1. Line Chart (Arus Kas)
        const cashflowOptions = {
            series: [
                { name: 'Pemasukan', data: chart30Hari.pemasukan },
                { name: 'Pengeluaran', data: chart30Hari.pengeluaran }
            ],
            chart: {
                type: 'area',
                height: 300,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                background: 'transparent',
                animations: { enabled: true }
            },
            colors: ['#10B981', '#EF4444'], // Emerald (green) and Red
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: chart30Hari.categories,
                labels: { style: { colors: textColor }, tickPlacement: 'on' },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: textColor },
                    formatter: (value) => {
                        if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                        if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                        return 'Rp ' + value;
                    }
                }
            },
            grid: {
                borderColor: isDark ? '#374151' : '#e5e7eb',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            legend: { position: 'top', horizontalAlign: 'right' },
            theme: { mode: isDark ? 'dark' : 'light' }
        };

        let cashflowChart = new ApexCharts(document.querySelector("#cashflow-chart"), cashflowOptions);
        cashflowChart.render();

        // 2. Donut Chart (Kategori)
        let categoryChart = null;
        if (chartKategori.series && chartKategori.series.length > 0) {
            const categoryOptions = {
                series: chartKategori.series,
                labels: chartKategori.labels,
                colors: chartKategori.colors,
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'Inter, sans-serif',
                    background: 'transparent',
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: { show: true },
                                value: {
                                    show: true,
                                    formatter: function (val) {
                                        return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function (w) {
                                        const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return "Rp " + new Intl.NumberFormat('id-ID').format(total);
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { show: false },
                legend: { show: false }, // Hide default legend to save space
                theme: { mode: isDark ? 'dark' : 'light' }
            };

            categoryChart = new ApexCharts(document.querySelector("#category-chart"), categoryOptions);
            categoryChart.render();
        }

        // Listen for theme change from layout
        window.addEventListener('theme-changed', function() {
            const newOptions = getChartThemeOptions();
            newOptions.grid = { borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb' };
            cashflowChart.updateOptions(newOptions);
            if(categoryChart) categoryChart.updateOptions(newOptions);
        });
    });
</script>
