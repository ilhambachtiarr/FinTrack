<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Reset Password') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], },
                    colors: { primary: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', } }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all">
        
        <?php if (!$is_valid): ?>
        
        <!-- State: Token Invalid -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-4">
                <i class="fa-solid fa-circle-xmark text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Link Tidak Berlaku</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 mb-6">Link reset password tidak valid atau sudah kadaluarsa (lebih dari 60 menit). Silakan minta link baru.</p>
            <a href="<?= site_url('auth/forgot_password') ?>" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                Minta Link Baru
            </a>
        </div>
        
        <?php else: ?>

        <!-- State: Token Valid, Form Reset -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-600 mb-4">
                <i class="fa-solid fa-unlock-keyhole text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight">Set Password Baru</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Buat password baru yang kuat untuk akun Anda.</p>
        </div>

        <?= form_open('auth/process_reset', ['class' => 'mt-8 space-y-6', 'id' => 'reset-form']) ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <div class="space-y-4">
                <div>
                    <label for="password" class="block text-sm font-medium mb-1">Password Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required minlength="6"
                            class="block w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white transition-colors"
                            placeholder="••••••••">
                        <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none" data-target="password" title="Lihat password">
                            <i class="fa-regular fa-eye toggle-icon"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Minimal 6 karakter.</p>
                </div>

                <div>
                    <label for="password_confirm" class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input id="password_confirm" name="password_confirm" type="password" required minlength="6"
                            class="block w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white transition-colors"
                            placeholder="••••••••">
                        <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none" data-target="password_confirm" title="Lihat password">
                            <i class="fa-regular fa-eye toggle-icon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" id="btn-submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <span id="btn-text">Simpan Password Baru</span>
                    <i id="btn-icon" class="fa-solid fa-spinner fa-spin hidden ml-2 mt-1"></i>
                </button>
            </div>
        <?= form_close() ?>
        
        <?php endif; ?>
    </div>

    <!-- Toastr & jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "timeOut": "5000" };
        <?php if ($this->session->flashdata('error')): ?>
            toastr.error("<?= addslashes($this->session->flashdata('error')) ?>");
        <?php endif; ?>

        $('#reset-form').on('submit', function() {
            $('#btn-submit').prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
            $('#btn-text').text('Menyimpan...');
            $('#btn-icon').removeClass('hidden');
        });

        // Toggle Password Visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('.toggle-icon');
                
                if (input.getAttribute('type') === 'password') {
                    input.setAttribute('type', 'text');
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.setAttribute('type', 'password');
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>
</html>
