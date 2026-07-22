<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Login') ?> — FinTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b' },
                    },
                    animation: { 'float': 'float 6s ease-in-out infinite', 'float-delay': 'float 8s ease-in-out 2s infinite' }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes float { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-20px) rotate(5deg)} }
        @keyframes float2 { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-14px) rotate(-4deg)} }
        .shape-1 { animation: float 7s ease-in-out infinite; }
        .shape-2 { animation: float2 9s ease-in-out 1.5s infinite; }
        .shape-3 { animation: float 11s ease-in-out 3s infinite; }
        .shape-4 { animation: float2 8s ease-in-out 0.5s infinite; }
        input:focus { outline: none; }
        .input-field {
            width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem;
            border: 1.5px solid #e5e7eb; border-radius: 0.75rem;
            font-size: 0.875rem; transition: all 0.2s;
            background: #f9fafb; color: #111827;
        }
        .input-field:focus { border-color: #10b981; background: #fff; box-shadow: 0 0 0 3px rgba(16,185,129,0.12); }
    </style>
</head>
<body class="min-h-screen flex">

    <!-- LEFT PANEL: Branding & Visual -->
    <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] relative overflow-hidden flex-col justify-between p-12"
         style="background: linear-gradient(135deg, #065f46 0%, #059669 40%, #0d9488 70%, #0891b2 100%);">

        <!-- Decorative Geometric Shapes -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Large circle top-right -->
            <div class="shape-1 absolute -top-20 -right-20 w-96 h-96 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #fff 0%, transparent 70%);"></div>
            <!-- Medium hexagon-ish bottom-left -->
            <div class="shape-2 absolute -bottom-24 -left-24 w-80 h-80 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #6ee7b7 0%, transparent 70%);"></div>
            <!-- Floating small orbs -->
            <div class="shape-3 absolute top-1/4 left-1/4 w-24 h-24 rounded-full opacity-20"
                 style="background: rgba(255,255,255,0.3); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.4);"></div>
            <div class="shape-4 absolute bottom-1/3 right-1/4 w-16 h-16 rounded-full opacity-25"
                 style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);"></div>
            <div class="shape-1 absolute top-2/3 left-1/3 w-10 h-10 rounded-full opacity-30"
                 style="background: rgba(110,231,183,0.5);"></div>

            <!-- Grid pattern overlay -->
            <div class="absolute inset-0 opacity-5"
                 style="background-image: linear-gradient(rgba(255,255,255,0.8) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.8) 1px, transparent 1px); background-size: 48px 48px;"></div>
        </div>

        <!-- Top Logo -->
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center text-white text-lg">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <span class="text-white font-extrabold text-2xl tracking-tight">FinTrack</span>
            </div>
        </div>

        <!-- Center Content -->
        <div class="relative z-10 py-12">
            <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1.5 text-white/90 text-xs font-medium mb-8 tracking-wide">
                <i class="fa-solid fa-shield-halved text-emerald-300"></i>
                Aman & Terenkripsi
            </div>
            <h1 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight mb-5">
                Kelola Keuangan<br>
                <span style="color: #6ee7b7;">Lebih Cerdas.</span>
            </h1>
            <p class="text-white/70 text-base leading-relaxed max-w-sm">
                Pantau pemasukan, pengeluaran, dan tujuan finansial Anda dalam satu dasbor yang elegan dan intuitif.
            </p>

            <!-- Feature pills -->
            <div class="flex flex-wrap gap-3 mt-8">
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-white text-xs font-medium">
                    <i class="fa-solid fa-chart-line text-emerald-300 text-xs"></i> Laporan Real-time
                </div>
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-white text-xs font-medium">
                    <i class="fa-solid fa-bullseye text-teal-300 text-xs"></i> Target Keuangan
                </div>
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-white text-xs font-medium">
                    <i class="fa-solid fa-sliders text-cyan-300 text-xs"></i> Anggaran Kategori
                </div>
            </div>
        </div>

        <!-- Bottom testimonial-style -->
        <div class="relative z-10">
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-5">
                <p class="text-white/80 text-sm italic leading-relaxed mb-3">
                    "Akhirnya saya tahu kemana uang saya pergi setiap bulan. FinTrack benar-benar mengubah cara saya mengelola keuangan."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-300 to-teal-400 flex items-center justify-center text-white font-bold text-sm">A</div>
                    <div>
                        <p class="text-white font-semibold text-sm">Andi S.</p>
                        <p class="text-white/50 text-xs">Mahasiswa Teknik</p>
                    </div>
                    <div class="ml-auto flex gap-0.5">
                        <?php for($i=0;$i<5;$i++): ?>
                        <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Form -->
    <div class="w-full lg:w-1/2 xl:w-[45%] flex items-center justify-center p-6 sm:p-10 bg-white">
        <div class="w-full max-w-md">

            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-base" style="background: linear-gradient(135deg,#10b981,#0891b2);">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <span class="font-extrabold text-xl" style="background:linear-gradient(135deg,#059669,#0891b2);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">FinTrack</span>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Selamat Datang</h2>
                <p class="text-gray-500 text-sm mt-2">Masuk ke akun keuangan Anda untuk melanjutkan.</p>
            </div>

            <?= form_open('auth/login', ['class' => 'space-y-5']) ?>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400 text-sm"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="input-field pl-10"
                            placeholder="nama@email.com"
                            value="<?= htmlspecialchars($old_email ?? '') ?>">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                        <a href="<?= site_url('auth/forgot_password') ?>" class="text-xs font-medium text-primary-600 hover:text-primary-700 transition-colors">Lupa Password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400 text-sm"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="input-field pl-10 pr-10"
                            placeholder="••••••••">
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fa-regular fa-eye text-sm" id="toggle-password-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white shadow-lg shadow-emerald-500/30 transition-all hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0"
                    style="background: linear-gradient(135deg, #059669, #0d9488);">
                    <i class="fa-solid fa-right-to-bracket text-sm"></i>
                    Masuk ke Akun
                </button>

            <?= form_close() ?>

            <!-- Divider -->
            <div class="flex items-center gap-3 my-6">
                <div class="h-px flex-1 bg-gray-200"></div>
                <span class="text-xs text-gray-400 font-medium">Belum punya akun?</span>
                <div class="h-px flex-1 bg-gray-200"></div>
            </div>

            <a href="<?= site_url('auth/register') ?>"
               class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-primary-700 border-2 border-primary-200 hover:border-primary-400 hover:bg-primary-50 transition-all">
                <i class="fa-solid fa-user-plus text-sm"></i>
                Daftar Gratis Sekarang
            </a>

            <p class="text-center text-xs text-gray-400 mt-8">
                &copy; <?= date('Y') ?> FinTrack &mdash; Manajemen Keuangan Pribadi
            </p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "timeOut": "5000" };
        <?php if (!empty($flash_success)): ?>
            toastr.success("<?= addslashes($flash_success) ?>");
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            toastr.error("<?= addslashes($error_message) ?>");
        <?php endif; ?>

        const togglePassword = document.getElementById('toggle-password');
        const passwordInput  = document.getElementById('password');
        const toggleIcon     = document.getElementById('toggle-password-icon');
        if (togglePassword) {
            togglePassword.addEventListener('click', function () {
                const isPass = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPass ? 'text' : 'password');
                toggleIcon.classList.toggle('fa-eye', !isPass);
                toggleIcon.classList.toggle('fa-eye-slash', isPass);
            });
        }
    </script>
</body>
</html>
