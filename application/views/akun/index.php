<?php
// Daftar preset ikon — FontAwesome 6
$ikon_presets = [
    ['key' => 'wallet',           'label' => 'Dompet'],
    ['key' => 'building-columns', 'label' => 'Bank'],
    ['key' => 'mobile-screen',   'label' => 'Dompet Digital'],
    ['key' => 'coins',           'label' => 'Emas / Koin'],
    ['key' => 'chart-line',      'label' => 'Saham / Investasi'],
    ['key' => 'money-bill',      'label' => 'Tunai'],
    ['key' => 'piggy-bank',      'label' => 'Tabungan'],
    ['key' => 'credit-card',     'label' => 'Kartu Kredit'],
    ['key' => 'house',           'label' => 'Properti'],
    ['key' => 'car',             'label' => 'Kendaraan'],
    ['key' => 'landmark',        'label' => 'Investasi Negara'],
    ['key' => 'bitcoin-sign',    'label' => 'Kripto'],
];

$jenis_presets = ['bank', 'e-wallet', 'tunai', 'tabungan', 'investasi', 'lainnya'];
?>

<!-- Header + Total Saldo -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Manajemen Akun</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Kelola akun, dompet, dan rekening keuangan Anda.</p>
        </div>
        <button onclick="openModal('create-modal')" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Akun
        </button>
    </div>

    <!-- Total Saldo Card -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-500 rounded-xl p-5 text-white shadow-md">
        <p class="text-primary-100 text-sm font-medium mb-1">Total Saldo Seluruh Akun Aktif</p>
        <p class="text-3xl font-bold tracking-tight"><?= format_rupiah($total_saldo) ?></p>
        <p class="text-primary-200 text-xs mt-1">Angka ini identik dengan Total Saldo di Dashboard</p>
    </div>
</div>



<!-- Akun Card Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php if (empty($akun_list)): ?>
        <!-- Empty State -->
        <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl p-10 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fa-solid fa-wallet text-4xl text-blue-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Belum ada akun tersimpan</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-5 max-w-sm mx-auto">Buat akun pertama Anda — bisa berupa rekening bank, dompet digital, atau kas tunai.</p>
            <button onclick="openModal('create-modal')" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Akun Pertama
            </button>
        </div>
    <?php else: ?>
        <?php foreach ($akun_list as $akun):
            $is_inactive = !$akun['is_active'];
        ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow group relative <?= $is_inactive ? 'opacity-60' : '' ?>">
            <!-- Top accent bar -->
            <div class="h-1.5 w-full" style="background-color: <?= htmlspecialchars($akun['kode_warna']) ?>"></div>

            <div class="p-5">
                <div class="flex items-start justify-between mb-4">
                    <!-- Icon + Name -->
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl shadow-sm flex-shrink-0" style="background-color: <?= htmlspecialchars($akun['kode_warna']) ?>">
                            <i class="fa-solid fa-<?= htmlspecialchars($akun['ikon'] ?: 'wallet') ?>"></i>
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="font-bold text-gray-900 dark:text-white leading-tight truncate max-w-[140px]"><?= htmlspecialchars($akun['nama_akun']) ?></h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400 capitalize"><?= htmlspecialchars($akun['jenis_akun']) ?></span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <?php if ($is_inactive): ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 flex-shrink-0">
                            <i class="fa-solid fa-pause mr-1"></i> Nonaktif
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 flex-shrink-0">
                            <i class="fa-solid fa-circle text-xs mr-1"></i> Aktif
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Saldo -->
                <div class="mb-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Saldo Saat Ini</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight"><?= format_rupiah($akun['saldo']) ?></p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <!-- Edit -->
                    <button onclick='editAkun(<?= json_encode($akun) ?>)' class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        <i class="fa-solid fa-pen"></i> Edit
                    </button>

                    <!-- Toggle Aktif -->
                    <button onclick="confirmToggle(<?= $akun['id'] ?>, '<?= htmlspecialchars(addslashes($akun['nama_akun'])) ?>', <?= $akun['is_active'] ?>)"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition-colors <?= $is_inactive ? 'text-green-700 bg-green-50 hover:bg-green-100 dark:text-green-400 dark:bg-green-900/20 dark:hover:bg-green-900/40' : 'text-orange-700 bg-orange-50 hover:bg-orange-100 dark:text-orange-400 dark:bg-orange-900/20 dark:hover:bg-orange-900/40' ?>">
                        <i class="fa-solid fa-<?= $is_inactive ? 'play' : 'pause' ?>"></i>
                        <?= $is_inactive ? 'Aktifkan' : 'Nonaktifkan' ?>
                    </button>

                    <!-- Delete -->
                    <button onclick="confirmDelete(<?= $akun['id'] ?>, '<?= htmlspecialchars(addslashes($akun['nama_akun'])) ?>')"
                        class="inline-flex items-center justify-center w-9 h-9 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 dark:text-red-400 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-lg transition-colors">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ============================================== -->
<!-- MODAL CREATE -->
<!-- ============================================== -->
<div id="create-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75" onclick="closeModal('create-modal')"></div>

        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
            <?= form_open('akun/store', ['id' => 'form-akun-create']) ?>
            <div class="px-6 pt-6 pb-2">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Akun Baru</h3>
                    <button type="button" onclick="closeModal('create-modal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- Nama Akun -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Akun <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_akun" required placeholder="Cth: BCA Tabungan, GoPay, Dompet Tunai"
                            class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2.5 text-sm bg-white dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Jenis Akun -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Akun</label>
                        <select name="jenis_akun" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2.5 text-sm bg-white dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <?php foreach ($jenis_presets as $j): ?>
                                <option value="<?= $j ?>"><?= ucfirst($j) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Saldo Awal (hanya saat Create) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Saldo Awal (Rp) <span class="text-red-500">*</span></label>
                        <input type="text" name="saldo_awal" required onkeyup="formatRupiahInput(this)" placeholder="0"
                            class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2.5 text-sm bg-white dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1"><i class="fa-solid fa-lock mr-1"></i> Saldo hanya bisa diubah lewat transaksi, bukan edit akun.</p>
                    </div>

                    <!-- Ikon Preset -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Ikon</label>
                        <div class="grid grid-cols-6 gap-2">
                            <?php foreach ($ikon_presets as $i => $preset): ?>
                                <label class="cursor-pointer" title="<?= $preset['label'] ?>">
                                    <input type="radio" name="ikon" value="<?= $preset['key'] ?>" class="sr-only ikon-radio" <?= $i === 0 ? 'checked' : '' ?>>
                                    <div class="ikon-option w-10 h-10 rounded-lg border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:border-primary-400 hover:text-primary-500 transition-colors <?= $i === 0 ? 'border-primary-500 text-primary-600 bg-primary-50 dark:bg-primary-900/20' : '' ?>">
                                        <i class="fa-solid fa-<?= $preset['key'] ?>"></i>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Warna -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Warna Aksen</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="kode_warna" id="create-warna" value="#3B82F6"
                                class="h-10 w-16 border border-gray-300 dark:border-gray-600 rounded-lg p-1 cursor-pointer">
                            <div class="flex gap-2">
                                <?php $colors = ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#06B6D4','#84CC16']; ?>
                                <?php foreach ($colors as $c): ?>
                                    <button type="button" onclick="document.getElementById('create-warna').value='<?= $c ?>'" 
                                        class="w-6 h-6 rounded-full border-2 border-white dark:border-gray-600 shadow-sm hover:scale-110 transition-transform"
                                        style="background-color:<?= $c ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <button type="button" onclick="closeModal('create-modal')" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">Batal</button>
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">Simpan Akun</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL EDIT (tanpa field saldo) -->
<!-- ============================================== -->
<div id="edit-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75" onclick="closeModal('edit-modal')"></div>

        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
            <?= form_open('', ['id' => 'form-akun-edit']) ?>
            <div class="px-6 pt-6 pb-2">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Akun</h3>
                    <button type="button" onclick="closeModal('edit-modal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Tampilkan saldo saat ini sebagai info saja (read-only) -->
                <div id="edit-saldo-info" class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                    <div>
                        <p class="text-xs text-blue-700 dark:text-blue-300 font-medium">Saldo Saat Ini</p>
                        <p class="text-lg font-bold text-blue-900 dark:text-blue-100" id="edit-saldo-display">—</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Akun <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_akun" id="edit-nama" required
                            class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2.5 text-sm bg-white dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Akun</label>
                        <select name="jenis_akun" id="edit-jenis" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2.5 text-sm bg-white dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <?php foreach ($jenis_presets as $j): ?>
                                <option value="<?= $j ?>"><?= ucfirst($j) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Ikon Preset (Edit) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Ikon</label>
                        <div class="grid grid-cols-6 gap-2" id="edit-ikon-grid">
                            <?php foreach ($ikon_presets as $preset): ?>
                                <label class="cursor-pointer" title="<?= $preset['label'] ?>">
                                    <input type="radio" name="ikon" value="<?= $preset['key'] ?>" class="sr-only edit-ikon-radio">
                                    <div class="edit-ikon-option w-10 h-10 rounded-lg border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:border-primary-400 hover:text-primary-500 transition-colors">
                                        <i class="fa-solid fa-<?= $preset['key'] ?>"></i>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Warna Aksen</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="kode_warna" id="edit-warna" value="#3B82F6"
                                class="h-10 w-16 border border-gray-300 dark:border-gray-600 rounded-lg p-1 cursor-pointer">
                            <div class="flex gap-2">
                                <?php foreach ($colors as $c): ?>
                                    <button type="button" onclick="document.getElementById('edit-warna').value='<?= $c ?>'"
                                        class="w-6 h-6 rounded-full border-2 border-white dark:border-gray-600 shadow-sm hover:scale-110 transition-transform"
                                        style="background-color:<?= $c ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <button type="button" onclick="closeModal('edit-modal')" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">Batal</button>
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">Simpan Perubahan</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
// =============================================
// Modal helpers
// =============================================
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

// =============================================
// Format Rupiah Input
// =============================================
function formatRupiahInput(input) {
    let val = input.value.replace(/[^\d]/g, '');
    if (!val) { input.value = ''; return; }
    input.value = parseInt(val).toLocaleString('id-ID');
}

// =============================================
// Ikon Picker — Create Form
// =============================================
document.querySelectorAll('.ikon-radio').forEach(radio => {
    radio.addEventListener('change', function () {
        document.querySelectorAll('.ikon-option').forEach(el => {
            el.classList.remove('border-primary-500', 'text-primary-600', 'bg-primary-50', 'dark:bg-primary-900/20');
            el.classList.add('border-gray-200', 'dark:border-gray-600');
        });
        const selected = this.closest('label').querySelector('.ikon-option');
        selected.classList.add('border-primary-500', 'text-primary-600', 'bg-primary-50', 'dark:bg-primary-900/20');
        selected.classList.remove('border-gray-200', 'dark:border-gray-600');
    });
});

// =============================================
// Edit Akun — isi modal edit
// =============================================
function editAkun(akun) {
    document.getElementById('form-akun-edit').action = '<?= site_url("akun/update/") ?>' + akun.id;
    document.getElementById('edit-nama').value = akun.nama_akun;
    document.getElementById('edit-jenis').value = akun.jenis_akun;
    document.getElementById('edit-warna').value = akun.kode_warna;
    document.getElementById('edit-saldo-display').innerText = 'Rp ' + parseFloat(akun.saldo).toLocaleString('id-ID', {minimumFractionDigits: 0});

    // Set ikon yang sesuai
    document.querySelectorAll('.edit-ikon-radio').forEach(radio => {
        const opt = radio.closest('label').querySelector('.edit-ikon-option');
        opt.classList.remove('border-primary-500', 'text-primary-600', 'bg-primary-50', 'dark:bg-primary-900/20');
        opt.classList.add('border-gray-200', 'dark:border-gray-600');
        if (radio.value === akun.ikon) {
            radio.checked = true;
            opt.classList.add('border-primary-500', 'text-primary-600', 'bg-primary-50', 'dark:bg-primary-900/20');
            opt.classList.remove('border-gray-200', 'dark:border-gray-600');
        }
    });

    // Ikon picker event untuk form edit
    document.querySelectorAll('.edit-ikon-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.edit-ikon-option').forEach(el => {
                el.classList.remove('border-primary-500', 'text-primary-600', 'bg-primary-50', 'dark:bg-primary-900/20');
                el.classList.add('border-gray-200', 'dark:border-gray-600');
            });
            const selected = this.closest('label').querySelector('.edit-ikon-option');
            selected.classList.add('border-primary-500', 'text-primary-600', 'bg-primary-50', 'dark:bg-primary-900/20');
            selected.classList.remove('border-gray-200', 'dark:border-gray-600');
        });
    });

    openModal('edit-modal');
}

// =============================================
// Konfirmasi Toggle Aktif/Nonaktif
// =============================================
function confirmToggle(id, nama, isActive) {
    const action  = isActive ? 'menonaktifkan' : 'mengaktifkan kembali';
    const isDark  = document.documentElement.classList.contains('dark');
    const warningText = isActive
        ? `Akun <strong>${nama}</strong> akan <strong>dinonaktifkan</strong> dan tidak muncul di dropdown transaksi baru.`
        : `Akun <strong>${nama}</strong> akan <strong>diaktifkan kembali</strong>.`;
    Swal.fire({
        title: `${isActive ? 'Nonaktifkan' : 'Aktifkan'} Akun?`,
        html: warningText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Ya, ${action}`,
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true,
        background: isDark ? '#1f2937' : '#ffffff',
        color: isDark ? '#f9fafb' : '#111827',
        confirmButtonColor: isActive ? '#f59e0b' : '#3b82f6',
        cancelButtonColor: '#6b7280',
        customClass: { popup: 'rounded-2xl shadow-2xl', title: 'text-lg font-semibold', htmlContainer: 'text-sm', confirmButton: 'rounded-lg px-5 py-2 text-sm font-medium', cancelButton: 'rounded-lg px-5 py-2 text-sm font-medium' },
        buttonsStyling: true,
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = '<?= site_url("akun/toggle_active/") ?>' + id;
        }
    });
}

// =============================================
// Konfirmasi Hapus (menjelaskan konsekuensinya)
// =============================================
function confirmDelete(id, nama) {
    const isDark = document.documentElement.classList.contains('dark');
    Swal.fire({
        title: 'Hapus Akun Permanen?',
        html: `Akun <strong>${nama}</strong> akan dihapus secara permanen.<br><br><small class="text-gray-500">⚠️ Penghapusan hanya diizinkan jika akun tidak memiliki riwayat transaksi. Jika ada, sistem akan menolak — gunakan <em>Nonaktifkan</em> saja.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus Permanen',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true,
        background: isDark ? '#1f2937' : '#ffffff',
        color: isDark ? '#f9fafb' : '#111827',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        customClass: { popup: 'rounded-2xl shadow-2xl', title: 'text-lg font-semibold', htmlContainer: 'text-sm', confirmButton: 'rounded-lg px-5 py-2 text-sm font-medium', cancelButton: 'rounded-lg px-5 py-2 text-sm font-medium' },
        buttonsStyling: true,
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = '<?= site_url("akun/delete/") ?>' + id;
        }
    });
}
</script>
