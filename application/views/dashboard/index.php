<!-- Header Section -->
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight">Dashboard Overview</h1>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Ringkasan keuangan Anda bulan ini.</p>
</div>

<!-- Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- Total Saldo -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Saldo</p>
                <h3 class="text-2xl font-bold mt-1"><?= format_rupiah($total_saldo) ?></h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>
    </div>

    <!-- Pemasukan Bulan Ini -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pemasukan Bulan Ini</p>
                <h3 class="text-2xl font-bold mt-1 text-green-600 dark:text-green-400"><?= format_rupiah($pemasukan_bulan) ?></h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center">
                <i class="fa-solid fa-arrow-down"></i>
            </div>
        </div>
    </div>

    <!-- Pengeluaran Bulan Ini -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengeluaran Bulan Ini</p>
                <h3 class="text-2xl font-bold mt-1 text-red-600 dark:text-red-400"><?= format_rupiah($pengeluaran_bulan) ?></h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center">
                <i class="fa-solid fa-arrow-up"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Line Chart (Arus Kas) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 lg:col-span-2">
        <h3 class="text-base font-semibold mb-4">Arus Kas (30 Hari Terakhir)</h3>
        <div id="cashflow-chart" class="w-full h-[300px]"></div>
    </div>

    <!-- Donut Chart (Kategori Pengeluaran) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="text-base font-semibold mb-4">Pengeluaran per Kategori</h3>
        <?php $kategori_data = json_decode($chart_kategori, true); ?>
        <?php if(empty($kategori_data['series'])): ?>
            <div class="flex flex-col items-center justify-center h-[300px] text-gray-400">
                <i class="fa-solid fa-chart-pie text-4xl mb-2"></i>
                <p class="text-sm">Belum ada pengeluaran</p>
            </div>
        <?php else: ?>
            <div id="category-chart" class="w-full flex justify-center h-[300px]"></div>
        <?php endif; ?>
    </div>
</div>

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
