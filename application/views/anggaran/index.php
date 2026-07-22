<!-- Header Section -->
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Anggaran Bulanan</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Tetapkan batas pengeluaran per kategori untuk mengontrol keuangan Anda.</p>
    </div>
    <div>
        <button onclick="openModal('anggaran-modal')" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Atur Anggaran Kategori
        </button>
    </div>
</div>

<!-- Flash Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-lg"></i>
        <div><?= $this->session->flashdata('success') ?></div>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm flex items-center gap-3">
        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
        <div><?= $this->session->flashdata('error') ?></div>
    </div>
<?php endif; ?>

<!-- Summary Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Limit Anggaran</p>
                <h3 class="text-2xl font-bold mt-1 text-gray-900 dark:text-white"><?= format_rupiah($total_anggaran) ?></h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <i class="fa-solid fa-calculator"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Terpakai Bulan Ini</p>
                <h3 class="text-2xl font-bold mt-1 text-amber-600 dark:text-amber-400"><?= format_rupiah($total_terpakai) ?></h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sisa Anggaran Total</p>
                <h3 class="text-2xl font-bold mt-1 <?= $sisa_total >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' ?>">
                    <?= format_rupiah($sisa_total) ?>
                </h3>
            </div>
            <div class="w-10 h-10 rounded-full <?= $sisa_total >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400' ?> flex items-center justify-center">
                <i class="fa-solid <?= $sisa_total >= 0 ? 'fa-piggy-bank' : 'fa-triangle-exclamation' ?>"></i>
            </div>
        </div>
    </div>
</div>

<!-- Category Budget Cards List -->
<?php if (empty($budgets)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-8 text-center border border-gray-100 dark:border-gray-700">
        <div class="w-16 h-16 rounded-full bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fa-solid fa-sliders"></i>
        </div>
        <h3 class="text-lg font-bold">Belum Ada Anggaran Kategori</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1 mb-4 max-w-md mx-auto">Anda belum memasang batas pengeluaran untuk kategori apapun. Klik tombol di bawah untuk mulai mengatur anggaran.</p>
        <button onclick="openModal('anggaran-modal')" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Atur Anggaran Kategori Sekarang
        </button>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <?php foreach ($budgets as $item): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:border-gray-200 dark:hover:border-gray-600 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold" style="background-color: <?= $item['kode_warna'] ?: '#3B82F6' ?>;">
                            <i class="<?= $item['ikon'] ?: 'fa-solid fa-tag' ?>"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gray-900 dark:text-white"><?= htmlspecialchars($item['nama_kategori']) ?></h3>
                            <span class="text-xs font-medium <?= $item['text_color'] ?>"><?= $item['status_label'] ?></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="editAnggaran(<?= $item['kategori_id'] ?>, <?= $item['nominal_batas'] ?>)" class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 p-1.5 transition-colors" title="Edit Anggaran">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <a href="<?= site_url('anggaran/delete/' . $item['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus anggaran kategori ini?')" class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 p-1.5 transition-colors" title="Hapus Anggaran">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-3">
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-gray-600 dark:text-gray-300">Terpakai: <?= format_rupiah($item['terpakai']) ?></span>
                        <span class="<?= $item['text_color'] ?>"><?= min(100, $item['persentase']) ?>%</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 h-2.5 rounded-full overflow-hidden">
                        <div class="h-full <?= $item['progress_color'] ?> transition-all duration-500" style="width: <?= min(100, $item['persentase']) ?>%;"></div>
                    </div>
                </div>

                <div class="flex justify-between items-center text-xs pt-2 border-t border-gray-100 dark:border-gray-700/60 text-gray-500 dark:text-gray-400">
                    <span>Limit: <strong class="text-gray-700 dark:text-gray-200"><?= format_rupiah($item['nominal_batas']) ?></strong></span>
                    <span>Sisa: <strong class="<?= $item['sisa'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' ?>"><?= format_rupiah($item['sisa']) ?></strong></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Modal Set/Edit Anggaran -->
<div id="anggaran-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 dark:border-gray-700 relative">
        <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">Atur Anggaran Kategori</h3>
            <button onclick="closeModal('anggaran-modal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <?= form_open('anggaran/store') ?>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Kategori Pengeluaran</label>
                <select name="kategori_id" id="modal_kategori_id" required class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategori_pengeluaran as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Batas Pengeluaran Maksimal (Rp)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 text-sm font-semibold">Rp</span>
                    <input type="text" name="nominal_batas" id="modal_nominal_batas" required placeholder="0" class="pl-9 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white font-bold" onkeyup="formatCurrencyInput(this)">
                </div>
                <p class="text-xs text-gray-400 mt-1">Batas ini berlaku setiap bulan untuk kategori tersebut.</p>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="closeModal('anggaran-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition-colors">
                    Simpan Anggaran
                </button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function editAnggaran(kategoriId, nominalBatas) {
    document.getElementById('modal_kategori_id').value = kategoriId;
    document.getElementById('modal_nominal_batas').value = new Intl.NumberFormat('id-ID').format(nominalBatas);
    document.getElementById('modal-title').innerText = 'Edit Anggaran Kategori';
    openModal('anggaran-modal');
}

function formatCurrencyInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value) {
        input.value = new Intl.NumberFormat('id-ID').format(value);
    } else {
        input.value = '';
    }
}
</script>
