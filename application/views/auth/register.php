<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Daftar') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
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
        
        <!-- Header -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-600 mb-4">
                <i class="fa-solid fa-user-plus text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight">Buat Akun Baru</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Mulai kelola keuangan Anda dengan lebih baik</p>
        </div>



        <!-- Form -->
        <?= form_open('auth/register', ['class' => 'mt-8 space-y-5']) ?>
            
            <!-- Nama -->
            <div>
                <label for="name" class="block text-sm font-medium mb-1">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-regular fa-user text-gray-400"></i>
                    </div>
                    <input id="name" name="name" type="text" required 
                        class="block w-full pl-10 pr-3 py-2 border <?= !empty($field_errors['name']) ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-primary-500 focus:border-primary-500' ?> rounded-lg sm:text-sm bg-white dark:bg-gray-700 dark:text-white transition-colors"
                        placeholder="John Doe"
                        value="<?= htmlspecialchars($old_input['name'] ?? '') ?>">
                </div>
                <?php if (!empty($field_errors['name'])): ?>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= $field_errors['name'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium mb-1">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-regular fa-envelope text-gray-400"></i>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                        class="block w-full pl-10 pr-3 py-2 border <?= !empty($field_errors['email']) ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-primary-500 focus:border-primary-500' ?> rounded-lg sm:text-sm bg-white dark:bg-gray-700 dark:text-white transition-colors"
                        placeholder="nama@email.com"
                        value="<?= htmlspecialchars($old_input['email'] ?? '') ?>">
                </div>
                <?php if (!empty($field_errors['email'])): ?>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= $field_errors['email'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="new-password" required 
                        class="block w-full pl-10 pr-10 py-2 border <?= !empty($field_errors['password']) ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-primary-500 focus:border-primary-500' ?> rounded-lg sm:text-sm bg-white dark:bg-gray-700 dark:text-white transition-colors"
                        placeholder="Minimal 8 karakter">
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none" data-target="password" title="Lihat password">
                        <i class="fa-regular fa-eye toggle-icon"></i>
                    </button>
                </div>
                <?php if (!empty($field_errors['password'])): ?>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= $field_errors['password'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirm" class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                    <input id="password_confirm" name="password_confirm" type="password" autocomplete="new-password" required 
                        class="block w-full pl-10 pr-10 py-2 border <?= !empty($field_errors['password_confirm']) ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-primary-500 focus:border-primary-500' ?> rounded-lg sm:text-sm bg-white dark:bg-gray-700 dark:text-white transition-colors"
                        placeholder="Ulangi password">
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none" data-target="password_confirm" title="Lihat password">
                        <i class="fa-regular fa-eye toggle-icon"></i>
                    </button>
                </div>
                <?php if (!empty($field_errors['password_confirm'])): ?>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= $field_errors['password_confirm'] ?></p>
                <?php endif; ?>
            </div>

            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Daftar Sekarang
                </button>
            </div>
        <?= form_close() ?>

        <!-- Footer -->
        <div class="text-center text-sm">
            <span class="text-gray-500 dark:text-gray-400">Sudah punya akun?</span>
            <a href="<?= site_url('auth/login') ?>" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 transition-colors">
                Masuk di sini
            </a>
        </div>
    </div>

    <!-- Toastr & jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };
        <?php if (!empty($field_errors['general'])): ?>
            toastr.error("<?= addslashes($field_errors['general']) ?>");
        <?php endif; ?>

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
