<!-- Header & Flash Messages -->
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Riwayat Transaksi</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Kelola pemasukan, pengeluaran, dan transfer Anda.</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="openScanStrukModal()" class="inline-flex items-center justify-center px-4 py-2 border border-primary-300 dark:border-primary-700 rounded-lg shadow-sm text-sm font-medium text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/40 focus:outline-none transition-colors">
            <i class="fa-solid fa-camera mr-2"></i> Scan Struk
        </button>
        <button onclick="openModal('create-modal')" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Transaksi
        </button>
    </div>
</div>



<!-- Filters -->
<div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
    <form method="GET" action="<?= site_url('transaction') ?>" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan</label>
            <select name="bulan" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white">
                <?php for($m=1; $m<=12; $m++): ?>
                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $filters['bulan'] == $m ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
            <select name="tahun" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white">
                <?php $current_year = date('Y'); for($y = $current_year; $y >= $current_year-5; $y--): ?>
                    <option value="<?= $y ?>" <?= $filters['tahun'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe</label>
            <select name="tipe" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white">
                <option value="">Semua Tipe</option>
                <option value="pemasukan" <?= $filters['tipe'] == 'pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                <option value="pengeluaran" <?= $filters['tipe'] == 'pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
                <option value="transfer" <?= $filters['tipe'] == 'transfer' ? 'selected' : '' ?>>Transfer</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tampilkan Dihapus?</label>
            <select name="show_deleted" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white">
                <option value="0" <?= !$filters['show_deleted'] ? 'selected' : '' ?>>Tidak</option>
                <option value="1" <?= $filters['show_deleted'] ? 'selected' : '' ?>>Ya</option>
            </select>
        </div>
        <div>
            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none transition-colors">
                <i class="fa-solid fa-filter mr-2"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-4">Tanggal & Catatan</th>
                    <th scope="col" class="px-6 py-4">Akun & Kategori</th>
                    <th scope="col" class="px-6 py-4 text-right">Jumlah</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php if (empty($transaksi)): ?>
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Tidak ada data transaksi.</td></tr>
                <?php else: ?>
                    <?php foreach ($transaksi as $trx): 
                        $is_deleted = !is_null($trx['deleted_at']);
                        $row_class = $is_deleted ? 'bg-red-50/50 dark:bg-red-900/10 opacity-75' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50';
                    ?>
                        <tr class="<?= $row_class ?> transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium flex items-center flex-wrap gap-1">
                                    <?php if(isset($trx['sumber']) && $trx['sumber'] === 'struk'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">🧾 Struk</span>
                                        <?php if($trx['nama_merchant']): ?>
                                            <span class="text-gray-600 dark:text-gray-300"><?= htmlspecialchars($trx['nama_merchant']) ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?= htmlspecialchars($trx['catatan'] ?: 'Tanpa Keterangan') ?>
                                        <?php if($trx['lampiran']): ?>
                                            <a href="<?= base_url('uploads/lampiran/' . $trx['lampiran']) ?>" target="_blank" class="ml-2 text-primary-500 hover:text-primary-700" title="Lihat Lampiran"><i class="fa-solid fa-paperclip"></i></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-gray-500 mt-1"><?= format_tanggal($trx['tanggal_transaksi']) ?></div>
                                <?php if($is_deleted): ?>
                                    <span class="inline-flex items-center px-2 mt-1 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Terhapus</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center text-sm font-medium">
                                    <i class="fa-solid fa-wallet text-gray-400 mr-2"></i> <?= htmlspecialchars($trx['nama_akun']) ?>
                                    <?php if($trx['tipe'] === 'transfer'): ?>
                                        <i class="fa-solid fa-arrow-right mx-2 text-gray-400"></i>
                                        <i class="fa-solid fa-wallet text-gray-400 mr-2"></i> <?= htmlspecialchars($trx['nama_target']) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-1">
                                    <?php if($trx['tipe'] === 'transfer'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            Transfer
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: <?= $trx['warna_kategori'] ?? '#9CA3AF' ?>20; color: <?= $trx['warna_kategori'] ?? '#9CA3AF' ?>">
                                            <i class="fa-solid fa-<?= $trx['ikon_kategori'] ?? 'tag' ?> mr-1"></i>
                                            <?= htmlspecialchars($trx['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold <?= $trx['tipe'] === 'pemasukan' ? 'text-green-600 dark:text-green-400' : ($trx['tipe'] === 'pengeluaran' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100') ?>">
                                <?= $trx['tipe'] === 'pemasukan' ? '+' : ($trx['tipe'] === 'pengeluaran' ? '-' : '') ?> 
                                <?= format_rupiah($trx['jumlah']) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($is_deleted): ?>
                                    <a href="<?= site_url('transaction/restore/'.$trx['id']) ?>" onclick="swalHrefConfirm(event, this.href, 'Kembalikan Transaksi?', 'Transaksi ini akan <strong>dipulihkan</strong> dan saldonya akan <strong>diterapkan kembali</strong> ke akun.', 'Ya, Kembalikan')" class="text-green-600 hover:text-green-800 p-2" title="Restore"><i class="fa-solid fa-rotate-left"></i></a>
                                <?php else: ?>
                                    <?php if(isset($trx['sumber']) && $trx['sumber'] === 'struk'): ?>
                                        <!-- Transaksi Struk: Lihat Detail Item -->
                                        <button onclick="lihatDetailItem(<?= $trx['id'] ?>)" class="text-amber-600 hover:text-amber-800 p-2" title="Lihat Detail Item"><i class="fa-solid fa-list-ul"></i></button>
                                        <a href="<?= site_url('transaction/delete/'.$trx['id']) ?>" onclick="swalHref(event, this.href, 'Hapus Transaksi Struk?', 'Transaksi struk ini akan dihapus dan <strong>saldo akan dikembalikan</strong> secara otomatis ke akun.', 'Ya, Hapus')" class="text-red-600 hover:text-red-800 p-2" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                                    <?php elseif($trx['tipe'] !== 'transfer'): ?>
                                        <button onclick="editTransaction(<?= htmlspecialchars(json_encode($trx)) ?>)" class="text-blue-600 hover:text-blue-800 p-2" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <a href="<?= site_url('transaction/delete/'.$trx['id']) ?>" onclick="swalHref(event, this.href, 'Hapus Transaksi?', 'Transaksi ini akan dihapus dan <strong>saldo akan dikembalikan</strong> secara otomatis ke akun.', 'Ya, Hapus')" class="text-red-600 hover:text-red-800 p-2" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                                    <?php else: ?>
                                        <a href="<?= site_url('transaction/delete/'.$trx['id']) ?>" onclick="swalHref(event, this.href, 'Hapus Transaksi Transfer?', 'Transaksi transfer ini akan dihapus dan <strong>saldo akan dikembalikan</strong> secara otomatis ke akun.', 'Ya, Hapus')" class="text-red-600 hover:text-red-800 p-2" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <?= $pagination ?>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL CREATE & EDIT -->
<!-- ============================================== -->
<div id="create-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('create-modal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <?= form_open_multipart('transaction/store', ['id' => 'form-transaction']) ?>
                <input type="hidden" name="id" id="form-id" value="">
                
                <div class="px-6 pt-5 pb-4">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">Tambah Transaksi</h3>
                        <button type="button" onclick="closeModal('create-modal')" class="text-gray-400 hover:text-gray-500">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Tipe -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Tipe Transaksi</label>
                            <select name="tipe" id="form-tipe" required onchange="handleTipeChange()" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border">
                                <option value="pengeluaran">Pengeluaran</option>
                                <option value="pemasukan">Pemasukan</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <!-- Akun Asal -->
                        <div>
                            <label class="block text-sm font-medium mb-1" id="label-akun">Dari Akun</label>
                            <select name="akun_id" id="form-akun" required class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border">
                                <option value="">-- Pilih Akun --</option>
                                <?php foreach($akun_list as $akun): ?>
                                    <option value="<?= $akun['id'] ?>"><?= htmlspecialchars($akun['nama_akun']) ?> (<?= format_rupiah($akun['saldo']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Target Akun (Hanya Transfer) -->
                        <div id="wrapper-target-akun" class="hidden">
                            <label class="block text-sm font-medium mb-1">Ke Akun Tujuan</label>
                            <select name="target_akun_id" id="form-target-akun" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border">
                                <option value="">-- Pilih Akun Tujuan --</option>
                                <?php foreach($akun_list as $akun): ?>
                                    <option value="<?= $akun['id'] ?>"><?= htmlspecialchars($akun['nama_akun']) ?> (<?= format_rupiah($akun['saldo']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Kategori (Sembunyi saat transfer) -->
                        <div id="wrapper-kategori">
                            <label class="block text-sm font-medium mb-1">Kategori</label>
                            <select name="kategori_id" id="form-kategori" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border">
                                <option value="">-- Pilih Kategori --</option>
                                <!-- Kategori difilter via JS berdasarkan tipe -->
                            </select>
                        </div>

                        <!-- Tanggal & Jumlah -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Tanggal</label>
                                <input type="date" name="tanggal_transaksi" id="form-tanggal" required value="<?= date('Y-m-d') ?>" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Jumlah (Rp)</label>
                                <input type="text" name="jumlah" id="form-jumlah" required class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border" onkeyup="formatRupiahInput(this)" placeholder="10.000">
                            </div>
                        </div>

                        <!-- Catatan & Lampiran -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Catatan</label>
                            <input type="text" name="catatan" id="form-catatan" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border" placeholder="Opsional">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Lampiran (Struk/Nota)</label>
                            <input type="file" name="lampiran" id="form-lampiran" accept=".jpg,.jpeg,.png,.pdf" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2 text-gray-500">
                            <p class="text-xs text-gray-500 mt-1">Maks 2MB. Format: JPG, PNG, PDF.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Modal -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" onclick="closeModal('create-modal')" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none">
                        Simpan Transaksi
                    </button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    const kategoriList = <?= json_encode($kategori_list) ?>;
    
    // Buka tutup modal
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        if(id === 'create-modal' && !document.getElementById('form-id').value) {
            // Form baru, bersihkan
            document.getElementById('form-transaction').reset();
            document.getElementById('form-transaction').action = '<?= site_url("transaction/store") ?>';
            document.getElementById('modal-title').innerText = 'Tambah Transaksi';
            document.getElementById('form-id').value = '';
            document.getElementById('form-tanggal').value = '<?= date("Y-m-d") ?>';
            document.getElementById('form-tipe').disabled = false;
            handleTipeChange();
        }
    }
    
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById('form-id').value = ''; // Reset ID state
    }

    // Logic dropdown tipe
    function handleTipeChange() {
        const tipe = document.getElementById('form-tipe').value;
        const targetAkunWrapper = document.getElementById('wrapper-target-akun');
        const kategoriWrapper = document.getElementById('wrapper-kategori');
        const targetAkunInput = document.getElementById('form-target-akun');
        const kategoriInput = document.getElementById('form-kategori');
        const labelAkun = document.getElementById('label-akun');

        if (tipe === 'transfer') {
            targetAkunWrapper.classList.remove('hidden');
            kategoriWrapper.classList.add('hidden');
            targetAkunInput.required = true;
            kategoriInput.required = false;
            labelAkun.innerText = 'Dari Akun (Sumber)';
        } else {
            targetAkunWrapper.classList.add('hidden');
            kategoriWrapper.classList.remove('hidden');
            targetAkunInput.required = false;
            kategoriInput.required = true;
            labelAkun.innerText = 'Akun';
            
            // Filter option kategori
            kategoriInput.innerHTML = '<option value="">-- Pilih Kategori --</option>';
            kategoriList.forEach(k => {
                if(k.tipe === tipe) {
                    kategoriInput.innerHTML += `<option value="${k.id}">${k.nama_kategori}</option>`;
                }
            });
        }
    }

    // Logic edit transaksi (isi modal)
    function editTransaction(trx) {
        document.getElementById('form-transaction').action = '<?= site_url("transaction/update/") ?>' + trx.id;
        document.getElementById('modal-title').innerText = 'Edit Transaksi';
        document.getElementById('form-id').value = trx.id;
        
        document.getElementById('form-tipe').value = trx.tipe;
        // Kunci dropdown tipe agar tidak bisa diubah saat edit
        document.getElementById('form-tipe').disabled = true; 
        
        // Buat input hidden agar tipe tetap ter-submit (karena disabled attribute mencegah submit)
        let hiddenTipe = document.getElementById('hidden-tipe');
        if(!hiddenTipe) {
            hiddenTipe = document.createElement('input');
            hiddenTipe.type = 'hidden';
            hiddenTipe.id = 'hidden-tipe';
            hiddenTipe.name = 'tipe';
            document.getElementById('form-transaction').appendChild(hiddenTipe);
        }
        hiddenTipe.value = trx.tipe;

        handleTipeChange();

        document.getElementById('form-akun').value = trx.akun_id;
        document.getElementById('form-tanggal').value = trx.tanggal_transaksi;
        document.getElementById('form-jumlah').value = new Intl.NumberFormat('id-ID').format(trx.jumlah);
        document.getElementById('form-catatan').value = trx.catatan;
        
        if (trx.tipe !== 'transfer') {
            document.getElementById('form-kategori').value = trx.kategori_id;
        }
        
        openModal('create-modal');
    }

    // Format input rupiah live
    function formatRupiahInput(input) {
        let val = input.value.replace(/[^,\d]/g, '').toString();
        let split = val.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        input.value = rupiah;
    }

    // Inisiasi awal
    handleTipeChange();
</script>

<!-- ============================================== -->
<!-- MODAL SCAN STRUK -->
<!-- ============================================== -->
<div id="scan-struk-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeScanStrukModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-top bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 w-full max-w-3xl">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="scan-struk-title">
                    <i class="fa-solid fa-camera text-primary-500 mr-2"></i> Scan Struk Belanja
                </h3>
                <button onclick="closeScanStrukModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <!-- STEP 1: Upload -->
            <div id="scan-step-1" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Transaksi <span class="text-red-500">*</span></label>
                        <select id="scan-tipe" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 text-sm bg-white dark:bg-gray-700 dark:text-white">
                            <option value="pengeluaran">Pengeluaran</option>
                            <option value="pemasukan">Pemasukan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Akun <span class="text-red-500">*</span></label>
                        <select id="scan-akun" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 text-sm bg-white dark:bg-gray-700 dark:text-white">
                            <?php foreach($akun_list as $akun): ?>
                            <option value="<?= $akun['id'] ?>"><?= htmlspecialchars($akun['nama_akun']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                        <input type="date" id="scan-tanggal" value="<?= date('Y-m-d') ?>" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 text-sm bg-white dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                        <select id="scan-kategori" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 text-sm bg-white dark:bg-gray-700 dark:text-white">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach($kategori_list as $kat): ?>
                            <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Upload Area -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center cursor-pointer hover:border-primary-400 transition-colors" onclick="document.getElementById('scan-file-input').click()">
                    <div id="scan-upload-preview" class="hidden">
                        <img id="scan-img-preview" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg mb-2">
                        <p id="scan-file-name" class="text-sm text-gray-500"></p>
                    </div>
                    <div id="scan-upload-placeholder">
                        <i class="fa-solid fa-image text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Ketuk untuk ambil foto atau pilih gambar</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — Maks. 5MB</p>
                    </div>
                    <!-- capture=environment → kamera belakang di HP -->
                    <input type="file" id="scan-file-input" accept="image/*" capture="environment" class="hidden" onchange="onScanFileSelected(this)">
                </div>

                <!-- Loading State -->
                <div id="scan-loading" class="hidden mt-4 text-center py-4">
                    <div class="inline-flex items-center gap-3 text-primary-600 dark:text-primary-400">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="font-medium">Membaca struk... mohon tunggu</span>
                    </div>
                </div>

                <div id="scan-step1-error" class="hidden mt-3 p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg text-sm"></div>

                <div class="mt-4 flex justify-end gap-2">
                    <button onclick="closeScanStrukModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                    <button id="scan-btn-ocr" onclick="doOcr()" disabled class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Baca Struk
                    </button>
                </div>
            </div>

            <!-- STEP 2: Review & Edit Items -->
            <div id="scan-step-2" class="p-6 hidden">
                <div class="flex items-center justify-between mb-4">
                    <button onclick="kembaliStep1()" class="text-sm text-gray-500 hover:text-primary-600 flex items-center gap-1"><i class="fa-solid fa-arrow-left"></i> Ulangi Foto</button>
                    <div id="scan-img-thumb-container" class="text-right">
                        <img id="scan-img-thumb" src="" class="h-14 rounded-md inline-block border border-gray-200" alt="Struk">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Merchant / Toko</label>
                    <input type="text" id="scan-merchant" placeholder="Contoh: Indomaret, Alfamart..." class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 text-sm bg-white dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-2 flex justify-between items-center">
                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Item Transaksi</h4>
                    <button onclick="tambahBarisScan()" class="text-xs text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-plus"></i> Tambah Item
                    </button>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Nama Item</th>
                                <th class="px-3 py-2 text-center w-16">Qty</th>
                                <th class="px-3 py-2 text-right w-28">Harga Satuan</th>
                                <th class="px-3 py-2 text-right w-28">Subtotal</th>
                                <th class="px-2 py-2 w-8"></th>
                            </tr>
                        </thead>
                        <tbody id="scan-items-body" class="divide-y divide-gray-100 dark:divide-gray-700">
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg mb-4">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Keseluruhan</span>
                    <span id="scan-grand-total" class="text-lg font-bold text-gray-900 dark:text-white">Rp 0</span>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan (opsional)</label>
                    <input type="text" id="scan-catatan" placeholder="Keterangan tambahan..." class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 text-sm bg-white dark:bg-gray-700 dark:text-white">
                </div>

                <div id="scan-step2-error" class="hidden mb-3 p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg text-sm"></div>

                <div class="flex justify-end gap-2">
                    <button onclick="closeScanStrukModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                    <button id="scan-btn-simpan" onclick="simpanStruk()" disabled class="px-5 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-check mr-1"></i> Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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
                    <button id="btn-edit-item" onclick="editItemStruk()" class="text-sm text-primary-600 hover:text-primary-700 font-medium hidden"><i class="fa-solid fa-pen"></i> Edit Item</button>
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

<!-- JS Scan Struk -->
<script>
    var STRUK_UPLOAD_URL  = '<?= site_url("struk/upload") ?>';
    var STRUK_SIMPAN_URL  = '<?= site_url("struk/simpan") ?>';
    var STRUK_DETAIL_URL  = '<?= site_url("struk/get_detail_item") ?>';
    var STRUK_UPDATE_URL  = '<?= site_url("struk/update_item") ?>';
    var CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
    var CSRF_HASH  = '<?= $this->security->get_csrf_hash() ?>';

    var currentPathGambar = '';
    var currentOcrRaw = '';
    var currentDetailTrxId = null;

    function openScanStrukModal() {
        document.getElementById('scan-struk-modal').classList.remove('hidden');
        resetScanModal();
    }
    function closeScanStrukModal() {
        document.getElementById('scan-struk-modal').classList.add('hidden');
    }
    function kembaliStep1() {
        document.getElementById('scan-step-1').classList.remove('hidden');
        document.getElementById('scan-step-2').classList.add('hidden');
    }
    function resetScanModal() {
        document.getElementById('scan-step-1').classList.remove('hidden');
        document.getElementById('scan-step-2').classList.add('hidden');
        document.getElementById('scan-upload-preview').classList.add('hidden');
        document.getElementById('scan-upload-placeholder').classList.remove('hidden');
        document.getElementById('scan-loading').classList.add('hidden');
        document.getElementById('scan-step1-error').classList.add('hidden');
        document.getElementById('scan-btn-ocr').disabled = true;
        document.getElementById('scan-file-input').value = '';
        currentPathGambar = '';
        currentOcrRaw = '';
    }

    function onScanFileSelected(input) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('scan-img-preview').src = e.target.result;
            document.getElementById('scan-upload-preview').classList.remove('hidden');
            document.getElementById('scan-upload-placeholder').classList.add('hidden');
            document.getElementById('scan-file-name').textContent = file.name;
            document.getElementById('scan-btn-ocr').disabled = false;
            document.getElementById('scan-step1-error').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }

    function doOcr() {
        var fileInput = document.getElementById('scan-file-input');
        if (!fileInput.files || !fileInput.files[0]) return;

        document.getElementById('scan-loading').classList.remove('hidden');
        document.getElementById('scan-btn-ocr').disabled = true;
        document.getElementById('scan-step1-error').classList.add('hidden');

        var formData = new FormData();
        formData.append('struk_file', fileInput.files[0]);
        formData.append(CSRF_NAME, CSRF_HASH);

        fetch(STRUK_UPLOAD_URL, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(function(data) {
            document.getElementById('scan-loading').classList.add('hidden');
            if (data.csrf_hash) CSRF_HASH = data.csrf_hash; // Update CSRF Hash untuk request berikutnya
            
            if (!data.success) {
                var errEl = document.getElementById('scan-step1-error');
                errEl.textContent = data.error || 'Terjadi kesalahan saat membaca struk.';
                errEl.classList.remove('hidden');
                document.getElementById('scan-btn-ocr').disabled = false;
                return;
            }
            // Pindah ke Step 2
            currentPathGambar = data.path_gambar;
            currentOcrRaw = data.ocr_raw_text;
            renderStep2(data);
        })
        .catch(function(err) {
            document.getElementById('scan-loading').classList.add('hidden');
            var errEl = document.getElementById('scan-step1-error');
            errEl.textContent = 'Gagal menghubungi server. Periksa koneksi Anda.';
            errEl.classList.remove('hidden');
            document.getElementById('scan-btn-ocr').disabled = false;
        });
    }

    function renderStep2(data) {
        document.getElementById('scan-step-1').classList.add('hidden');
        document.getElementById('scan-step-2').classList.remove('hidden');
        document.getElementById('scan-merchant').value = data.nama_merchant || '';
        document.getElementById('scan-img-thumb').src = data.file_url || '';
        document.getElementById('scan-step2-error').classList.add('hidden');

        var tbody = document.getElementById('scan-items-body');
        tbody.innerHTML = '';
        if (data.items && data.items.length > 0) {
            data.items.forEach(function(item) {
                tambahBarisScan(item.nama_item, item.qty, item.harga_satuan);
            });
        } else {
            tambahBarisScan();
        }
        updateGrandTotal();
    }

    var scanRowIndex = 0;
    function tambahBarisScan(nama, qty, harga) {
        var tbody = document.getElementById('scan-items-body');
        var idx = scanRowIndex++;
        var row = document.createElement('tr');
        row.setAttribute('data-row', idx);
        row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
        row.innerHTML = `
            <td class="px-2 py-1">
                <input type="text" class="w-full border-0 bg-transparent text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-primary-500 rounded px-1 py-0.5 scan-nama" value="${escHtml(nama||'')}" placeholder="Nama Item">
            </td>
            <td class="px-2 py-1">
                <input type="number" class="w-16 border-0 bg-transparent text-sm text-center text-gray-900 dark:text-white focus:ring-1 focus:ring-primary-500 rounded px-1 py-0.5 scan-qty" value="${qty||1}" min="0.01" step="0.01" oninput="updateSubtotal(this)">
            </td>
            <td class="px-2 py-1">
                <input type="number" class="w-full border-0 bg-transparent text-sm text-right text-gray-900 dark:text-white focus:ring-1 focus:ring-primary-500 rounded px-1 py-0.5 scan-harga" value="${harga||0}" min="0" oninput="updateSubtotal(this)">
            </td>
            <td class="px-2 py-1 text-right text-sm font-medium text-gray-900 dark:text-white scan-subtotal">
                Rp ${formatAngka((qty||1)*(harga||0))}
            </td>
            <td class="px-2 py-1 text-center">
                <button onclick="hapusBarisScan(this)" class="text-red-400 hover:text-red-600 text-xs"><i class="fa-solid fa-xmark"></i></button>
            </td>
        `;
        tbody.appendChild(row);
        updateGrandTotal();
    }

    function hapusBarisScan(btn) {
        btn.closest('tr').remove();
        updateGrandTotal();
    }

    function updateSubtotal(input) {
        var row = input.closest('tr');
        var qty = parseFloat(row.querySelector('.scan-qty').value) || 0;
        var harga = parseFloat(row.querySelector('.scan-harga').value) || 0;
        row.querySelector('.scan-subtotal').textContent = 'Rp ' + formatAngka(qty * harga);
        updateGrandTotal();
    }

    function updateGrandTotal() {
        var rows = document.querySelectorAll('#scan-items-body tr');
        var total = 0;
        rows.forEach(function(row) {
            var qty = parseFloat(row.querySelector('.scan-qty').value) || 0;
            var harga = parseFloat(row.querySelector('.scan-harga').value) || 0;
            total += qty * harga;
        });
        document.getElementById('scan-grand-total').textContent = 'Rp ' + formatAngka(total);
        document.getElementById('scan-btn-simpan').disabled = (rows.length === 0 || total <= 0);
    }

    function simpanStruk() {
        var rows = document.querySelectorAll('#scan-items-body tr');
        if (rows.length === 0) return;

        var items = [];
        rows.forEach(function(row) {
            var nama = row.querySelector('.scan-nama').value.trim();
            var qty = parseFloat(row.querySelector('.scan-qty').value) || 1;
            var harga = parseFloat(row.querySelector('.scan-harga').value) || 0;
            if (nama && harga > 0) {
                items.push({nama_item: nama, qty: qty, harga_satuan: harga, subtotal: qty * harga});
            }
        });

        if (items.length === 0) {
            document.getElementById('scan-step2-error').textContent = 'Minimal harus ada 1 item dengan nama dan harga yang valid.';
            document.getElementById('scan-step2-error').classList.remove('hidden');
            return;
        }

        var payload = new FormData();
        payload.append(CSRF_NAME, CSRF_HASH);
        payload.append('tipe', document.getElementById('scan-tipe').value);
        payload.append('akun_id', document.getElementById('scan-akun').value);
        payload.append('tanggal', document.getElementById('scan-tanggal').value);
        payload.append('kategori_id', document.getElementById('scan-kategori').value);
        payload.append('nama_merchant', document.getElementById('scan-merchant').value);
        payload.append('catatan', document.getElementById('scan-catatan').value);
        payload.append('path_gambar', currentPathGambar);
        payload.append('ocr_raw_text', currentOcrRaw);
        items.forEach(function(item, i) {
            payload.append('items['+i+'][nama_item]', item.nama_item);
            payload.append('items['+i+'][qty]', item.qty);
            payload.append('items['+i+'][harga_satuan]', item.harga_satuan);
            payload.append('items['+i+'][subtotal]', item.subtotal);
        });

        document.getElementById('scan-btn-simpan').disabled = true;
        document.getElementById('scan-btn-simpan').innerHTML = '<svg class="animate-spin h-4 w-4 mr-1 inline" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';

        fetch(STRUK_SIMPAN_URL, {
            method: 'POST',
            body: payload,
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(function(data) {
            if (data.csrf_hash) CSRF_HASH = data.csrf_hash;
            
            if (data.success) {
                closeScanStrukModal();
                // Tampilkan toast dan reload
                showToast(data.message || 'Transaksi struk berhasil disimpan!', 'success');
                setTimeout(function() { window.location.reload(); }, 1200);
            } else {
                document.getElementById('scan-step2-error').textContent = data.error || 'Gagal menyimpan.';
                document.getElementById('scan-step2-error').classList.remove('hidden');
                document.getElementById('scan-btn-simpan').disabled = false;
                document.getElementById('scan-btn-simpan').innerHTML = '<i class="fa-solid fa-check mr-1"></i> Simpan Transaksi';
            }
        })
        .catch(function() {
            document.getElementById('scan-step2-error').textContent = 'Terjadi kesalahan jaringan.';
            document.getElementById('scan-step2-error').classList.remove('hidden');
            document.getElementById('scan-btn-simpan').disabled = false;
            document.getElementById('scan-btn-simpan').innerHTML = '<i class="fa-solid fa-check mr-1"></i> Simpan Transaksi';
        });
    }

    // ---- Lihat Detail Item ----
    function lihatDetailItem(trxId) {
        currentDetailTrxId = trxId;
        var modal = document.getElementById('detail-item-modal');
        var body = document.getElementById('detail-item-body');
        document.getElementById('btn-edit-item').classList.add('hidden');
        body.innerHTML = '<div class="text-center py-8 text-gray-400"><svg class="animate-spin h-6 w-6 mx-auto" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
        modal.classList.remove('hidden');

        fetch(STRUK_DETAIL_URL + '/' + trxId, {credentials: 'same-origin'})
        .then(r => r.json())
        .then(function(data) {
            if (!data.success) {
                body.innerHTML = '<p class="text-red-500">' + (data.error || 'Gagal memuat item.') + '</p>';
                return;
            }
            
            // Simpan data untuk diedit nantinya
            currentDetailData = data;
            
            var merchant = data.nama_merchant ? ('<p class="font-medium text-gray-700 dark:text-gray-300 mb-3">🏪 ' + escHtml(data.nama_merchant) + '</p>') : '';
            var rows = data.items.map(function(item) {
                var sub = (item.qty * item.harga_satuan);
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
            document.getElementById('btn-edit-item').classList.remove('hidden');
            document.getElementById('detail-item-title').textContent = '🧾 Detail Item — ' + (data.nama_merchant || 'Struk');
        })
        .catch(function() {
            body.innerHTML = '<p class="text-red-500">Gagal memuat item. Periksa koneksi Anda.</p>';
        });
    }

    var currentDetailData = null; // Menyimpan data aktif untuk diedit
    var editRowIndex = 0;

    function editItemStruk() {
        if (!currentDetailData || !currentDetailData.items) return;
        
        document.getElementById('detail-item-title').textContent = '✏️ Edit Item Struk';
        document.getElementById('btn-edit-item').classList.add('hidden');
        
        var body = document.getElementById('detail-item-body');
        var merchant = currentDetailData.nama_merchant || '';
        var catatan = currentDetailData.catatan || '';
        
        var rows = currentDetailData.items.map(function(item) {
            var idx = editRowIndex++;
            return `<tr data-row="${idx}" class="border-b border-gray-100 dark:border-gray-700">
                <td class="py-1 pr-2"><input type="text" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded text-sm px-2 py-1 edit-nama" value="${escHtml(item.nama_item)}"></td>
                <td class="py-1 px-1"><input type="number" class="w-16 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded text-sm text-center px-1 py-1 edit-qty" value="${item.qty}" min="0.01" step="0.01" oninput="updateEditSubtotal(this)"></td>
                <td class="py-1 pl-2"><input type="number" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded text-sm text-right px-2 py-1 edit-harga" value="${item.harga_satuan}" min="0" oninput="updateEditSubtotal(this)"></td>
                <td class="py-1 pl-2 text-right font-medium text-sm text-gray-900 dark:text-white edit-subtotal">Rp ${formatAngka(item.qty * item.harga_satuan)}</td>
                <td class="py-1 text-center"><button onclick="this.closest('tr').remove(); updateEditGrandTotal();" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button></td>
            </tr>`;
        }).join('');
        
        var total = currentDetailData.items.reduce(function(s, i) { return s + (i.qty * i.harga_satuan); }, 0);

        var html = `
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Merchant</label>
                <input type="text" id="edit-merchant" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded text-sm px-3 py-1.5" value="${escHtml(merchant)}">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                <input type="text" id="edit-catatan" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded text-sm px-3 py-1.5" value="${escHtml(catatan)}">
            </div>
            <div class="overflow-x-auto mb-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase">
                            <th class="py-1 pr-2 text-left">Item</th>
                            <th class="py-1 px-1 text-center">Qty</th>
                            <th class="py-1 pl-2 text-right">Harga</th>
                            <th class="py-1 pl-2 text-right">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="edit-items-body">
                        ${rows}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="pt-3 text-right font-bold text-gray-800 dark:text-gray-200">Total</td>
                            <td class="pt-3 pl-2 text-right font-bold text-primary-600 dark:text-primary-400" id="edit-grand-total">Rp ${formatAngka(total)}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <button onclick="tambahBarisEdit()" class="mt-2 text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400 font-medium">+ Tambah Item</button>
            </div>
            
            <div class="flex justify-end gap-2 border-t border-gray-100 dark:border-gray-700 pt-4 mt-2">
                <button onclick="lihatDetailItem(currentDetailTrxId)" class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-200 dark:hover:bg-gray-600 font-medium">Batal</button>
                <button onclick="simpanEditItem()" id="btn-simpan-edit" class="px-4 py-2 text-sm bg-primary-600 text-white rounded hover:bg-primary-700 font-medium shadow-sm"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
            </div>
            <div id="edit-error" class="hidden mt-3 text-sm text-red-500 bg-red-50 dark:bg-red-900/20 p-2 rounded"></div>
        `;
        body.innerHTML = html;
    }

    function tambahBarisEdit() {
        var tbody = document.getElementById('edit-items-body');
        var idx = editRowIndex++;
        var tr = document.createElement('tr');
        tr.className = 'border-b border-gray-100 dark:border-gray-700';
        tr.innerHTML = `
            <td class="py-1 pr-2"><input type="text" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded text-sm px-2 py-1 edit-nama" placeholder="Nama Item"></td>
            <td class="py-1 px-1"><input type="number" class="w-16 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded text-sm text-center px-1 py-1 edit-qty" value="1" min="0.01" step="0.01" oninput="updateEditSubtotal(this)"></td>
            <td class="py-1 pl-2"><input type="number" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded text-sm text-right px-2 py-1 edit-harga" value="0" min="0" oninput="updateEditSubtotal(this)"></td>
            <td class="py-1 pl-2 text-right font-medium text-sm text-gray-900 dark:text-white edit-subtotal">Rp 0</td>
            <td class="py-1 text-center"><button onclick="this.closest('tr').remove(); updateEditGrandTotal();" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function updateEditSubtotal(input) {
        var row = input.closest('tr');
        var qty = parseFloat(row.querySelector('.edit-qty').value) || 0;
        var harga = parseFloat(row.querySelector('.edit-harga').value) || 0;
        row.querySelector('.edit-subtotal').textContent = 'Rp ' + formatAngka(qty * harga);
        updateEditGrandTotal();
    }

    function updateEditGrandTotal() {
        var rows = document.querySelectorAll('#edit-items-body tr');
        var total = 0;
        rows.forEach(function(row) {
            var qty = parseFloat(row.querySelector('.edit-qty').value) || 0;
            var harga = parseFloat(row.querySelector('.edit-harga').value) || 0;
            total += (qty * harga);
        });
        document.getElementById('edit-grand-total').textContent = 'Rp ' + formatAngka(total);
        document.getElementById('btn-simpan-edit').disabled = (rows.length === 0 || total <= 0);
    }

    function simpanEditItem() {
        var rows = document.querySelectorAll('#edit-items-body tr');
        if (rows.length === 0) return;

        var items = [];
        rows.forEach(function(row) {
            var nama = row.querySelector('.edit-nama').value.trim();
            var qty = parseFloat(row.querySelector('.edit-qty').value) || 1;
            var harga = parseFloat(row.querySelector('.edit-harga').value) || 0;
            if (nama && harga > 0) {
                items.push({nama_item: nama, qty: qty, harga_satuan: harga});
            }
        });

        if (items.length === 0) {
            var errEl = document.getElementById('edit-error');
            errEl.textContent = 'Minimal harus ada 1 item dengan nama dan harga yang valid.';
            errEl.classList.remove('hidden');
            return;
        }

        var payload = new URLSearchParams();
        payload.append(CSRF_NAME, CSRF_HASH);
        payload.append('nama_merchant', document.getElementById('edit-merchant').value);
        payload.append('catatan', document.getElementById('edit-catatan').value);
        items.forEach(function(item, i) {
            payload.append('items['+i+'][nama_item]', item.nama_item);
            payload.append('items['+i+'][qty]', item.qty);
            payload.append('items['+i+'][harga_satuan]', item.harga_satuan);
        });

        var btn = document.getElementById('btn-simpan-edit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        document.getElementById('edit-error').classList.add('hidden');

        fetch(STRUK_UPDATE_URL + '/' + currentDetailTrxId, {
            method: 'POST',
            body: payload,
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .then(r => r.json())
        .then(function(data) {
            if (data.csrf_hash) CSRF_HASH = data.csrf_hash;
            if (data.success) {
                showToast(data.message || 'Berhasil memperbarui item!', 'success');
                // Refresh modal (kembali ke view)
                lihatDetailItem(currentDetailTrxId);
                // Kita perlu me-reload halaman agar tabel di background terupdate
                setTimeout(function() { window.location.reload(); }, 1200);
            } else {
                var errEl = document.getElementById('edit-error');
                errEl.textContent = data.error || 'Gagal menyimpan.';
                errEl.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan Perubahan';
            }
        })
        .catch(function() {
            var errEl = document.getElementById('edit-error');
            errEl.textContent = 'Kesalahan jaringan.';
            errEl.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan Perubahan';
        });
    }

    // Helper
    function formatAngka(n) {
        return Math.round(n).toLocaleString('id-ID');
    }
    function escHtml(str) {
        var d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

</script>
