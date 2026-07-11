<!-- Header & Flash Messages -->
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Tujuan Keuangan</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Kelola dan pantau target finansial Anda.</p>
    </div>
    <button onclick="openModal('create-modal')" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none transition-colors">
        <i class="fa-solid fa-plus mr-2"></i> Buat Tujuan Baru
    </button>
</div>



<!-- Goal Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
    <?php if (empty($goals)): ?>
        <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl p-8 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                <i class="fa-solid fa-bullseye text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">Belum ada tujuan keuangan</h3>
            <p class="text-gray-500 text-sm mb-4">Buat tujuan pertama Anda, misalnya "Dana Darurat" atau "Liburan".</p>
            <button onclick="openModal('create-modal')" class="text-primary-600 font-medium hover:underline">Buat Sekarang</button>
        </div>
    <?php else: ?>
        <?php foreach ($goals as $goal): 
            $persen = Goal_model::hitung_persentase($goal['saldo_saat_ini'], $goal['target_jumlah']);
            
            // Hitung sisa hari
            $sisa_hari = null;
            if ($goal['tgl_target']) {
                $target_date = new DateTime($goal['tgl_target']);
                $today = new DateTime();
                $diff = $today->diff($target_date);
                if ($diff->invert) {
                    $sisa_hari = "Terlewat " . $diff->days . " hari";
                } else {
                    $sisa_hari = $diff->days . " hari lagi";
                }
            }

            // Opacity & Status Style
            $is_completed = $goal['status'] === 'tercapai';
            $is_canceled = $goal['status'] === 'dibatalkan';
            $card_class = "bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md relative overflow-hidden";
            
            if ($is_completed) $card_class .= " border-green-200 dark:border-green-800";
            if ($is_canceled) $card_class .= " opacity-75 grayscale";
        ?>
            <div class="<?= $card_class ?>">
                <!-- Decorative Top Border -->
                <div class="absolute top-0 left-0 w-full h-1" style="background-color: <?= htmlspecialchars($goal['kode_warna']) ?>"></div>
                
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white shadow-sm" style="background-color: <?= htmlspecialchars($goal['kode_warna']) ?>">
                            <i class="fa-solid fa-<?= htmlspecialchars($goal['ikon']) ?>"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white leading-tight"><?= htmlspecialchars($goal['nama_tujuan']) ?></h3>
                            <?php if ($goal['deskripsi']): ?>
                                <p class="text-xs text-gray-500 mt-0.5 truncate max-w-[150px]" title="<?= htmlspecialchars($goal['deskripsi']) ?>">
                                    <?= htmlspecialchars($goal['deskripsi']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Actions Dropdown (Simplified to inline icons for space) -->
                    <div class="flex gap-1 text-gray-400">
                        <button onclick='editGoal(<?= json_encode($goal) ?>)' class="p-1 hover:text-blue-500 transition-colors" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <a href="<?= site_url('goal/delete/'.$goal['id']) ?>" onclick="swalHref(event, this.href, 'Hapus Tujuan Keuangan?', 'Tujuan <strong><?= htmlspecialchars($goal['nama_tujuan']) ?></strong> akan dihapus permanen dan tidak dapat dikembalikan.', 'Ya, Hapus')" class="p-1 hover:text-red-500 transition-colors" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </div>

                <!-- Nominal Progress -->
                <div class="mb-2">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-bold <?= $is_completed ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' ?>">
                            <?= format_rupiah($goal['saldo_saat_ini']) ?>
                        </span>
                        <span class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">
                            dari <?= format_rupiah($goal['target_jumlah']) ?>
                        </span>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-1 overflow-hidden">
                        <div class="h-2.5 rounded-full transition-all duration-500" style="width: <?= $persen ?>%; background-color: <?= htmlspecialchars($goal['kode_warna']) ?>"></div>
                    </div>
                    
                    <div class="flex justify-between text-xs text-gray-500">
                        <span><?= $persen ?>% Terkumpul</span>
                        <?php if ($sisa_hari && !$is_canceled): ?>
                            <span class="flex items-center gap-1 <?= strpos($sisa_hari, 'Terlewat') !== false ? 'text-red-500' : 'text-orange-500' ?>">
                                <i class="fa-regular fa-clock"></i> <?= $sisa_hari ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Footer / Status Badge -->
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <?php if ($is_completed): ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                            <i class="fa-solid fa-check-circle mr-1"></i> Tercapai
                        </span>
                    <?php elseif ($is_canceled): ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            <i class="fa-solid fa-ban mr-1"></i> Dibatalkan
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Aktif
                        </span>
                        <button onclick="openTambahDana(<?= $goal['id'] ?>, '<?= htmlspecialchars(addslashes($goal['nama_tujuan'])) ?>')" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 flex items-center">
                            <i class="fa-solid fa-plus-circle mr-1"></i> Tambah Dana
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ============================================== -->
<!-- MODAL CREATE & EDIT -->
<!-- ============================================== -->
<div id="create-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('create-modal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
            <?= form_open('goal/store', ['id' => 'form-goal']) ?>
                <div class="px-6 pt-5 pb-4">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">Buat Tujuan Keuangan</h3>
                        <button type="button" onclick="closeModal('create-modal')" class="text-gray-400 hover:text-gray-500">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Tujuan</label>
                            <input type="text" name="nama_tujuan" id="form-nama" required class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border" placeholder="Cth: Liburan ke Bali">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Deskripsi Singkat</label>
                            <input type="text" name="deskripsi" id="form-deskripsi" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border" placeholder="Cth: Tiket pesawat & hotel (Opsional)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Target Jumlah (Rp)</label>
                            <input type="text" name="target_jumlah" id="form-target" required onkeyup="formatRupiahInput(this)" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border" placeholder="5.000.000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tanggal Target Pencapaian</label>
                            <input type="date" name="tgl_target" id="form-tgl" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Ikon (FontAwesome)</label>
                                <input type="text" name="ikon" id="form-ikon" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border" placeholder="plane, car, home">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Warna Label</label>
                                <input type="color" name="kode_warna" id="form-warna" value="#10B981" class="block w-full h-[42px] border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 p-1 border cursor-pointer">
                            </div>
                        </div>

                        <div id="wrapper-status" class="hidden">
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" id="form-status" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white p-2.5 border">
                                <option value="aktif">Aktif</option>
                                <option value="tercapai">Tercapai</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" onclick="closeModal('create-modal')" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none">
                        Simpan Goal
                    </button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL TAMBAH DANA -->
<!-- ============================================== -->
<div id="dana-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('dana-modal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full">
            <?= form_open('', ['id' => 'form-dana']) ?>
                <div class="px-6 pt-5 pb-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Tambah Dana</h3>
                        <button type="button" onclick="closeModal('dana-modal')" class="text-gray-400 hover:text-gray-500">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Masukkan jumlah dana yang disisihkan untuk goal: <br>
                        <strong class="text-gray-900 dark:text-gray-100" id="dana-goal-name"></strong>
                    </p>

                    <div>
                        <label class="block text-sm font-medium mb-1">Jumlah (Rp)</label>
                        <input type="text" name="jumlah_dana" required onkeyup="formatRupiahInput(this)" class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 text-lg font-bold text-center bg-white dark:bg-gray-700 dark:text-white p-3 border" placeholder="50.000">
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" onclick="closeModal('dana-modal')" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" class="w-full px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                        Masukkan Dana
                    </button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        if(id === 'create-modal' && document.getElementById('modal-title').innerText !== 'Edit Tujuan Keuangan') {
            document.getElementById('form-goal').reset();
            document.getElementById('form-goal').action = '<?= site_url("goal/store") ?>';
            document.getElementById('wrapper-status').classList.add('hidden');
        }
    }
    
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        if(id === 'create-modal') {
            document.getElementById('modal-title').innerText = 'Buat Tujuan Keuangan';
        }
    }

    function editGoal(goal) {
        document.getElementById('form-goal').action = '<?= site_url("goal/update/") ?>' + goal.id;
        document.getElementById('modal-title').innerText = 'Edit Tujuan Keuangan';
        
        document.getElementById('form-nama').value = goal.nama_tujuan;
        document.getElementById('form-deskripsi').value = goal.deskripsi;
        document.getElementById('form-target').value = new Intl.NumberFormat('id-ID').format(goal.target_jumlah);
        document.getElementById('form-tgl').value = goal.tgl_target;
        document.getElementById('form-ikon').value = goal.ikon;
        document.getElementById('form-warna').value = goal.kode_warna;
        document.getElementById('form-status').value = goal.status;
        
        document.getElementById('wrapper-status').classList.remove('hidden');
        
        openModal('create-modal');
    }

    function openTambahDana(goalId, goalName) {
        document.getElementById('form-dana').reset();
        document.getElementById('form-dana').action = '<?= site_url("goal/tambah_dana/") ?>' + goalId;
        document.getElementById('dana-goal-name').innerText = goalName;
        openModal('dana-modal');
    }

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
        input.value = rupiah;
    }
</script>
