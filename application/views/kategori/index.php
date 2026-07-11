<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    
    <!-- Flash Messages (SweetAlert2 handled by footer/layout, or fallback here) -->
    <?php if($this->session->flashdata('error')): ?>
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative flex items-start shadow-sm">
            <i class="fa-solid fa-circle-exclamation mt-1 mr-3 text-red-500"></i>
            <div>
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline"><?= $this->session->flashdata('error') ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kustomisasi Kategori</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Atur kategori pemasukan dan pengeluaran Anda sendiri.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
            <a href="<?= site_url('kategori/create') ?>" class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Kategori
            </a>
        </div>
    </div>

    <!-- Grid Container -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Pemasukan -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-green-600 dark:text-green-400">
                    <i class="fa-solid fa-arrow-down mr-2"></i> Pemasukan
                </h2>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900/30 dark:text-green-400">
                    <?= count($pemasukan) ?> Kategori
                </span>
            </div>
            
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($pemasukan)): ?>
                    <li class="px-6 py-8 text-center text-sm text-gray-500">Belum ada kategori pemasukan.</li>
                <?php else: ?>
                    <?php foreach ($pemasukan as $k): ?>
                        <li class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white shadow-sm" style="background-color: <?= htmlspecialchars($k['kode_warna']) ?>">
                                        <i class="fa-solid <?= htmlspecialchars($k['ikon']) ?>"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($k['nama_kategori']) ?></p>
                                        <?php if ($k['is_default'] == 1): ?>
                                            <p class="text-xs text-gray-500">Bawaan Sistem</p>
                                        <?php else: ?>
                                            <p class="text-xs text-primary-500">Kustom</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($k['is_default'] == 0): ?>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="<?= site_url('kategori/edit/' . $k['id']) ?>" class="p-2 text-gray-400 hover:text-primary-600 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" onclick="confirmDelete(<?= $k['id'] ?>)" class="p-2 text-gray-400 hover:text-red-600 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Pengeluaran -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-red-600 dark:text-red-400">
                    <i class="fa-solid fa-arrow-up mr-2"></i> Pengeluaran
                </h2>
                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900/30 dark:text-red-400">
                    <?= count($pengeluaran) ?> Kategori
                </span>
            </div>
            
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($pengeluaran)): ?>
                    <li class="px-6 py-8 text-center text-sm text-gray-500">Belum ada kategori pengeluaran.</li>
                <?php else: ?>
                    <?php foreach ($pengeluaran as $k): ?>
                        <li class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white shadow-sm" style="background-color: <?= htmlspecialchars($k['kode_warna']) ?>">
                                        <i class="fa-solid <?= htmlspecialchars($k['ikon']) ?>"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($k['nama_kategori']) ?></p>
                                        <?php if ($k['is_default'] == 1): ?>
                                            <p class="text-xs text-gray-500">Bawaan Sistem</p>
                                        <?php else: ?>
                                            <p class="text-xs text-primary-500">Kustom</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($k['is_default'] == 0): ?>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="<?= site_url('kategori/edit/' . $k['id']) ?>" class="p-2 text-gray-400 hover:text-primary-600 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" onclick="confirmDelete(<?= $k['id'] ?>)" class="p-2 text-gray-400 hover:text-red-600 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        
    </div>

</div>

<!-- Delete Form -->
<form id="delete-form" method="POST" action="" class="hidden"></form>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Kategori?',
        text: "Pastikan tidak ada transaksi yang menggunakan kategori ini. Jika ada, mohon ubah dulu kategori pada transaksi tersebut.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = '<?= site_url('kategori/delete/') ?>' + id;
            form.submit();
        }
    })
}
</script>
