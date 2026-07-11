<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pengaturan Profil</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Foto Profil -->
        <div class="md:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 text-center">
                <div class="relative inline-block mb-4">
                    <?php 
                        $avatar_url = !empty($user['foto_profil']) 
                            ? base_url('uploads/profil/' . $user['foto_profil']) 
                            : base_url('assets/img/default-avatar.png');
                    ?>
                    <img id="foto_profil_preview" src="<?= $avatar_url ?>" alt="Foto Profil" class="w-32 h-32 rounded-full object-cover border-4 border-blue-50 dark:border-blue-900/30 shadow-sm mx-auto">
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1"><?= htmlspecialchars($user['name']) ?></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6"><?= htmlspecialchars($user['email']) ?></p>

                <?= form_open_multipart('profil/upload_foto', ['class' => 'space-y-4']) ?>
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 text-left">Ganti Foto Profil</label>
                        <input type="file" name="foto_profil" id="foto_profil_input" accept="image/jpeg,image/png,image/webp" required
                            class="block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-900/30 dark:file:text-blue-400
                                hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50
                                cursor-pointer">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-left">PNG, JPG atau WEBP (Maks. 2MB)</p>
                    </div>
                    <button type="submit" id="upload_foto_btn" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-upload mr-2" id="upload_foto_icon"></i> <span id="upload_foto_text">Upload Foto</span>
                    </button>
                <?= form_close() ?>
            </div>
        </div>

        <!-- Kolom Kanan: Form Data & Password -->
        <div class="md:col-span-2 space-y-6">
            
            <!-- Update Data Diri -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Pribadi</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ubah nama dan alamat email Anda.</p>
                </div>
                <div class="p-6">
                    <?= form_open('profil/update', ['class' => 'space-y-6']) ?>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Email</label>
                            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border">
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Ubah Password</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pastikan akun Anda menggunakan password panjang acak untuk tetap aman.</p>
                </div>
                <div class="p-6">
                    <?= form_open('profil/update_password', ['class' => 'space-y-6']) ?>
                        <div>
                            <label for="password_lama" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password Saat Ini</label>
                            <input type="password" name="password_lama" id="password_lama" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border">
                        </div>
                        <div>
                            <label for="password_baru" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru</label>
                            <input type="password" name="password_baru" id="password_baru" required minlength="6"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimal 6 karakter.</p>
                        </div>
                        <div>
                            <label for="konfirmasi_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" id="konfirmasi_password" required minlength="6"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2 border">
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <i class="fas fa-key mr-2"></i> Perbarui Password
                            </button>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fotoInput = document.getElementById('foto_profil_input');
        const fotoPreview = document.getElementById('foto_profil_preview');
        const uploadForm = fotoInput.closest('form');
        const uploadBtn = document.getElementById('upload_foto_btn');
        const uploadIcon = document.getElementById('upload_foto_icon');
        const uploadText = document.getElementById('upload_foto_text');

        // Image Preview
        fotoInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    fotoPreview.src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        // Loading State
        uploadForm.addEventListener('submit', function() {
            uploadBtn.disabled = true;
            uploadBtn.classList.add('opacity-75', 'cursor-not-allowed');
            uploadIcon.className = 'fas fa-spinner fa-spin mr-2';
            uploadText.innerText = 'Mengupload...';
        });
    });
</script>
