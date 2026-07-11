<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Lupa Password') ?></title>
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
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all">
        
        <!-- Header -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-600 mb-4">
                <i class="fa-solid fa-key text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight">Lupa Password?</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Masukkan email Anda untuk menerima link reset password.</p>
        </div>

        <!-- Form -->
        <?= form_open('auth/send_reset_link', ['class' => 'mt-8 space-y-6', 'id' => 'forgot-form']) ?>
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white dark:bg-gray-700 dark:text-white transition-colors"
                            placeholder="nama@email.com">
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" id="btn-submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <span id="btn-text">Kirim Link Reset</span>
                    <i id="btn-icon" class="fa-solid fa-spinner fa-spin hidden ml-2 mt-1"></i>
                </button>
            </div>
        <?= form_close() ?>

        <!-- Footer -->
        <div class="text-center text-sm">
            <a href="<?= site_url('auth/login') ?>" class="font-medium text-gray-600 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Login
            </a>
        </div>
    </div>

    <!-- Toastr & jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "timeOut": "5000" };
        <?php if ($this->session->flashdata('success')): ?>
            toastr.success("<?= addslashes($this->session->flashdata('success')) ?>");
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            toastr.error("<?= addslashes($this->session->flashdata('error')) ?>");
        <?php endif; ?>

        $('#forgot-form').on('submit', function() {
            $('#btn-submit').prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
            $('#btn-text').text('Mengirim...');
            $('#btn-icon').removeClass('hidden');
        });
    </script>
</body>
</html>
