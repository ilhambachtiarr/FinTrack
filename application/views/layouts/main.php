<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Keuangan Pribadi') ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <!-- jQuery (required for Toastr) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Tailwind Configuration
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                        }
                    }
                }
            }
        }
        
        // Dark Mode Logic (Client Side)
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Smooth transition for theme toggle */
        * { transition: background-color 0.2s ease, border-color 0.2s ease; }
        /* Fix apexcharts dark mode tooltips */
        .apexcharts-tooltip { background: #fff; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .dark .apexcharts-tooltip { background: #1f2937; border: 1px solid #374151; color: #f3f4f6; }
        .dark .apexcharts-tooltip-title { background: #111827 !important; border-bottom: 1px solid #374151 !important; }
        .dark .apexcharts-text tspan { fill: #9ca3af; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">

    <!-- Mobile Header & Burger Menu -->
    <div class="lg:hidden flex items-center justify-between p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 fixed top-0 w-full z-20">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded bg-primary-100 text-primary-600 flex items-center justify-center">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <span class="font-bold text-lg">FinTrack</span>
        </div>
        <button id="mobile-menu-btn" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white p-2">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900/50 z-30 hidden lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full lg:translate-x-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">
        <div class="p-6 hidden lg:flex items-center gap-3 border-b border-gray-100 dark:border-gray-700">
            <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-wallet"></i>
                 <!-- <img src="/logo.png" alt="" style="weidth: 100%;"> -->
            </div>
            <span class="font-bold text-xl tracking-tight">FinTrack</span>
        </div>
        
        <div class="p-4 flex-1 overflow-y-auto">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="<?= site_url('dashboard') ?>" class="flex items-center p-3 rounded-lg <?= ($this->uri->segment(1) == 'dashboard' || $this->uri->segment(1) == '') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?> transition-colors">
                        <i class="fa-solid fa-house w-6"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('akun') ?>" class="flex items-center p-3 rounded-lg <?= ($this->uri->segment(1) == 'akun') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?> transition-colors">
                        <i class="fa-solid fa-credit-card w-6"></i>
                        <span>Akun</span>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('transaction') ?>" class="flex items-center p-3 rounded-lg <?= ($this->uri->segment(1) == 'transaction') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?> transition-colors">
                        <i class="fa-solid fa-money-bill-transfer w-6"></i>
                        <span>Transaksi</span>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('kategori') ?>" class="flex items-center p-3 rounded-lg <?= ($this->uri->segment(1) == 'kategori') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?> transition-colors">
                        <i class="fa-solid fa-tags w-6"></i>
                        <span>Kategori</span>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('goal') ?>" class="flex items-center p-3 rounded-lg <?= ($this->uri->segment(1) == 'goal') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?> transition-colors">
                        <i class="fa-solid fa-bullseye w-6"></i>
                        <span>Tujuan Keuangan</span>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('laporan') ?>" class="flex items-center p-3 rounded-lg <?= ($this->uri->segment(1) == 'laporan') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?> transition-colors">
                        <i class="fa-solid fa-chart-pie w-6"></i>
                        <span>Laporan PDF</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <!-- Theme Toggle -->
            <button id="theme-toggle" type="button" class="flex items-center w-full p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors mb-2">
                <i id="theme-toggle-dark-icon" class="fa-solid fa-moon w-6 hidden"></i>
                <i id="theme-toggle-light-icon" class="fa-solid fa-sun w-6 hidden"></i>
                <span id="theme-toggle-text" class="ml-1">Ubah Tema</span>
            </button>
            
            <!-- User Profile & Logout -->
            <div class="flex items-center p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors cursor-pointer" onclick="window.location.href='<?= site_url('profil') ?>'">
                <?php if(!empty($current_user['foto_profil'])): ?>
                    <img src="<?= base_url('uploads/profil/' . $current_user['foto_profil']) ?>" alt="Foto" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-600">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 uppercase font-bold border border-gray-200 dark:border-gray-600">
                        <?= substr(htmlspecialchars($current_user['name']), 0, 1) ?>
                    </div>
                <?php endif; ?>
                <div class="ml-3 flex-1 overflow-hidden">
                    <p class="text-sm font-medium truncate group-hover:text-primary-600"><?= htmlspecialchars($current_user['name']) ?></p>
                </div>
                <a href="<?= site_url('auth/logout') ?>" class="text-gray-500 hover:text-red-500 transition-colors p-2" title="Logout" onclick="event.stopPropagation();">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="p-4 lg:ml-64 mt-16 lg:mt-0 transition-all">
        <div class="max-w-7xl mx-auto">
            <?= $content ?? '' ?>
        </div>
    </div>

    <script>
        // --- Mobile Menu Logic ---
        const btn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleMenu() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        btn.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);

        // --- Dark Mode Logic ---
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        const themeToggleBtn = document.getElementById('theme-toggle');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            // toggle icons
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
            
            // Dispatch event for ApexCharts to detect theme change
            window.dispatchEvent(new Event('theme-changed'));
        });

        // --- Toastr Configuration ---
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Global showToast function
        window.showToast = function(msg, type = 'info') {
            if (type === 'success') toastr.success(msg);
            else if (type === 'error' || type === 'danger') toastr.error(msg);
            else if (type === 'warning') toastr.warning(msg);
            else toastr.info(msg);
        };

        // Handle CodeIgniter Flashdata automatically via Toastr
        <?php if ($this->session->flashdata('success')): ?>
            toastr.success("<?= addslashes($this->session->flashdata('success')) ?>");
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('error')): ?>
            toastr.error("<?= addslashes($this->session->flashdata('error')) ?>");
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('warning')): ?>
            toastr.warning("<?= addslashes($this->session->flashdata('warning')) ?>");
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('info')): ?>
            toastr.info("<?= addslashes($this->session->flashdata('info')) ?>");
        <?php endif; ?>

        // ─── Global SweetAlert2 Helper ───────────────────────────
        // Pola pemakaian: swalHref(event, url, title, text, confirmText, icon)
        // icon: 'warning' | 'question' | 'error' | 'info'
        window.swalHref = function(event, url, title, text, confirmText = 'Ya, Lanjutkan', icon = 'warning') {
            event.preventDefault();
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: title,
                html: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                background: isDark ? '#1f2937' : '#ffffff',
                color: isDark ? '#f9fafb' : '#111827',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                customClass: {
                    popup:          'rounded-2xl shadow-2xl',
                    title:          'text-lg font-semibold',
                    htmlContainer:  'text-sm',
                    confirmButton:  'rounded-lg px-5 py-2 text-sm font-medium',
                    cancelButton:   'rounded-lg px-5 py-2 text-sm font-medium',
                },
                buttonsStyling: true,
            }).then(function(result) {
                if (result.isConfirmed) { window.location.href = url; }
            });
        };

        // Versi khusus untuk konfirmasi yang tidak menghapus (icon question, tombol biru)
        window.swalHrefConfirm = function(event, url, title, text, confirmText = 'Ya, Lanjutkan') {
            event.preventDefault();
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: title,
                html: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                background: isDark ? '#1f2937' : '#ffffff',
                color: isDark ? '#f9fafb' : '#111827',
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                customClass: {
                    popup:          'rounded-2xl shadow-2xl',
                    title:          'text-lg font-semibold',
                    htmlContainer:  'text-sm',
                    confirmButton:  'rounded-lg px-5 py-2 text-sm font-medium',
                    cancelButton:   'rounded-lg px-5 py-2 text-sm font-medium',
                },
                buttonsStyling: true,
            }).then(function(result) {
                if (result.isConfirmed) { window.location.href = url; }
            });
        };
    </script>
</body>
</html>
