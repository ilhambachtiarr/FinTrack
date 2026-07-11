<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-3xl mx-auto">
    
    <div class="mb-8">
        <a href="<?= site_url('kategori') ?>" class="text-sm font-medium text-primary-600 hover:text-primary-500 mb-4 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Daftar Kategori
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $page_title ?></h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Silakan isi formulir di bawah ini untuk menyimpan data kategori.</p>
    </div>

    <!-- Validasi Error Form -->
    <?php if(validation_errors()): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <?= validation_errors('<p class="text-sm"><i class="fa-solid fa-circle-exclamation mr-2"></i> ', '</p>') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-xl p-6 sm:p-8">
        <?php 
            $action = ($mode === 'create') ? 'kategori/store' : 'kategori/update/' . $kategori['id'];
            echo form_open($action, ['class' => 'space-y-6', 'id' => 'kategori-form']); 
        ?>

            <!-- Tipe Kategori -->
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-gray-200 mb-2">Tipe Kategori <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none">
                        <input type="radio" name="tipe" value="pemasukan" class="peer sr-only" <?= set_radio('tipe', 'pemasukan', (isset($kategori) && $kategori['tipe'] == 'pemasukan')) ?>>
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">Pemasukan</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400"><i class="fa-solid fa-arrow-down mr-1 text-green-500"></i> Dana Masuk</span>
                            </span>
                        </span>
                        <i class="fa-solid fa-circle-check text-primary-600 absolute right-4 top-4 hidden peer-checked:block text-xl"></i>
                        <span class="pointer-events-none absolute -inset-px rounded-lg border-2 border-transparent peer-checked:border-primary-600" aria-hidden="true"></span>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none">
                        <input type="radio" name="tipe" value="pengeluaran" class="peer sr-only" <?= set_radio('tipe', 'pengeluaran', (isset($kategori) && $kategori['tipe'] == 'pengeluaran')) ?>>
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">Pengeluaran</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400"><i class="fa-solid fa-arrow-up mr-1 text-red-500"></i> Dana Keluar</span>
                            </span>
                        </span>
                        <i class="fa-solid fa-circle-check text-primary-600 absolute right-4 top-4 hidden peer-checked:block text-xl"></i>
                        <span class="pointer-events-none absolute -inset-px rounded-lg border-2 border-transparent peer-checked:border-primary-600" aria-hidden="true"></span>
                    </label>
                </div>
            </div>

            <!-- Nama Kategori -->
            <div>
                <label for="nama_kategori" class="block text-sm font-medium text-gray-900 dark:text-gray-200 mb-2">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kategori" id="nama_kategori" required
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-2.5 px-3 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:text-white"
                    placeholder="Contoh: Belanja Bulanan, Uang Saku, dll."
                    value="<?= set_value('nama_kategori', $kategori['nama_kategori'] ?? '') ?>">
            </div>

            <!-- Ikon & Warna Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Pilihan Warna -->
                <div>
                    <label for="kode_warna" class="block text-sm font-medium text-gray-900 dark:text-gray-200 mb-2">Warna Identitas <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="kode_warna" id="kode_warna" 
                            class="h-10 w-14 rounded border border-gray-300 p-1 cursor-pointer"
                            value="<?= set_value('kode_warna', $kategori['kode_warna'] ?? '#6B7280') ?>" onchange="updatePreview()">
                        <span class="text-sm text-gray-500 dark:text-gray-400 font-mono" id="color-hex"><?= set_value('kode_warna', $kategori['kode_warna'] ?? '#6B7280') ?></span>
                    </div>
                </div>

                <!-- Pilihan Ikon (Text Input Sederhana) -->
                <div>
                    <label for="ikon" class="block text-sm font-medium text-gray-900 dark:text-gray-200 mb-2">Ikon (FontAwesome Class) <span class="text-red-500">*</span></label>
                    <input type="text" name="ikon" id="ikon" required
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-2.5 px-3 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:text-white font-mono text-sm"
                        placeholder="fa-cart-shopping"
                        value="<?= set_value('ikon', $kategori['ikon'] ?? 'fa-tags') ?>" onkeyup="updatePreview()">
                    <p class="mt-1 text-xs text-gray-500">Cari ikon di <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank" class="text-primary-600 hover:underline">FontAwesome Free</a>.</p>
                </div>
            </div>

            <!-- Live Preview Card -->
            <div class="mt-8 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 bg-gray-50 dark:bg-gray-800/50 flex flex-col items-center justify-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 font-medium">Live Preview</p>
                
                <div class="flex items-center gap-4 bg-white dark:bg-gray-700 px-6 py-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-600">
                    <div id="preview-circle" class="w-12 h-12 rounded-full flex items-center justify-center text-white shadow-sm transition-colors duration-200" style="background-color: <?= set_value('kode_warna', $kategori['kode_warna'] ?? '#6B7280') ?>;">
                        <i id="preview-icon" class="fa-solid <?= set_value('ikon', $kategori['ikon'] ?? 'fa-tags') ?> text-xl"></i>
                    </div>
                    <div>
                        <p id="preview-name" class="text-base font-medium text-gray-900 dark:text-white">
                            <?= set_value('nama_kategori', $kategori['nama_kategori'] ?? 'Nama Kategori') ?: 'Nama Kategori' ?>
                        </p>
                        <p id="preview-type" class="text-xs text-gray-500 dark:text-gray-400">Tipe Kategori</p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <a href="<?= site_url('kategori') ?>" class="rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">
                    <i class="fa-solid fa-save mr-2 mt-0.5"></i> Simpan Kategori
                </button>
            </div>

        <?= form_close() ?>
    </div>
</div>

<script>
    function updatePreview() {
        const color = document.getElementById('kode_warna').value;
        const iconInput = document.getElementById('ikon').value.trim() || 'fa-tags';
        const nameInput = document.getElementById('nama_kategori').value.trim() || 'Nama Kategori';
        
        // Cek tipe (radio button)
        const typeRadios = document.getElementsByName('tipe');
        let typeVal = 'Tipe Kategori';
        for (const radio of typeRadios) {
            if (radio.checked) {
                typeVal = radio.value === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran';
                break;
            }
        }

        // Update hex text
        document.getElementById('color-hex').innerText = color.toUpperCase();
        
        // Update live preview UI
        document.getElementById('preview-circle').style.backgroundColor = color;
        
        // Membersihkan kelas icon sebelumnya dan menambahkan yang baru
        const iconEl = document.getElementById('preview-icon');
        iconEl.className = ''; 
        // Mengasumsikan user mengetik "fa-solid fa-xxx" atau hanya "fa-xxx". 
        // Untuk amannya tambahkan 'fa-solid' jika belum ada.
        let finalIconClass = iconInput;
        if (!finalIconClass.includes('fa-')) {
            finalIconClass = 'fa-' + finalIconClass;
        }
        iconEl.className = 'fa-solid ' + finalIconClass + ' text-xl';
        
        document.getElementById('preview-name').innerText = nameInput;
        document.getElementById('preview-type').innerText = typeVal;
    }

    // Add event listeners
    document.getElementById('nama_kategori').addEventListener('keyup', updatePreview);
    const radios = document.getElementsByName('tipe');
    for (const r of radios) {
        r.addEventListener('change', updatePreview);
    }
    
    // Initial call
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
