<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Daftar') ?> — FinTrack</title>
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
                        primary: { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857' },
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes float  { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-18px) rotate(4deg)} }
        @keyframes float2 { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-12px) rotate(-3deg)} }
        .shape-a { animation: float  8s ease-in-out infinite; }
        .shape-b { animation: float2 10s ease-in-out 2s infinite; }
        .shape-c { animation: float  12s ease-in-out 4s infinite; }
        input:focus { outline: none; }
        .input-field {
            width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem;
            border: 1.5px solid #e5e7eb; border-radius: 0.75rem;
            font-size: 0.875rem; transition: all 0.2s;
            background: #f9fafb; color: #111827;
        }
        .input-field:focus { border-color: #10b981; background: #fff; box-shadow: 0 0 0 3px rgba(16,185,129,0.12); }
        .input-error { border-color: #f87171 !important; }
        .input-error:focus { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.12) !important; }
    </style>
</head>
<body class="min-h-screen flex">

    <!-- LEFT PANEL -->
    <div class="hidden lg:flex lg:w-5/12 xl:w-[42%] relative overflow-hidden flex-col justify-between p-12"
         style="background: linear-gradient(150deg, #047857 0%, #059669 35%, #0d9488 65%, #0284c7 100%);">

        <div class="absolute inset-0 overflow-hidden">
            <div class="shape-a absolute -top-16 -right-16 w-80 h-80 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #fff 0%, transparent 70%);"></div>
            <div class="shape-b absolute -bottom-20 -left-20 w-72 h-72 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #6ee7b7 0%, transparent 70%);"></div>
            <div class="shape-c absolute top-1/3 left-1/3 w-20 h-20 rounded-full opacity-20"
                 style="background: rgba(255,255,255,0.25); border:1px solid rgba(255,255,255,0.35);"></div>
            <div class="shape-a absolute bottom-1/4 right-1/3 w-14 h-14 rounded-full opacity-25"
                 style="background: rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.3);"></div>
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image:linear-gradient(rgba(255,255,255,1) 1px, transparent 1px),linear-gradient(90deg,rgba(255,255,255,1) 1px,transparent 1px);background-size:48px 48px;"></div>
        </div>

        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center text-white text-lg">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <span class="text-white font-extrabold text-2xl tracking-tight">FinTrack</span>
        </div>

        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 bg-white/15 border border-white/20 rounded-full px-4 py-1.5 text-white/90 text-xs font-medium mb-6">
                <i class="fa-solid fa-rocket text-yellow-300"></i> Gratis Selamanya
            </div>
            <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">
                Mulai Perjalanan<br>
                <span style="color:#6ee7b7;">Finansial Anda.</span>
            </h1>
            <p class="text-white/70 text-sm leading-relaxed max-w-xs">
                Bergabunglah dan mulai catat, analisis, serta rencanakan keuangan Anda hari ini — tanpa biaya apapun.
            </p>

            <div class="mt-8 space-y-3">
                <div class="flex items-center gap-3 text-white/80 text-sm">
                    <div class="w-6 h-6 rounded-full bg-emerald-400/30 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-emerald-300 text-xs"></i>
                    </div>
                    Catat transaksi dengan mudah & cepat
                </div>
                <div class="flex items-center gap-3 text-white/80 text-sm">
                    <div class="w-6 h-6 rounded-full bg-emerald-400/30 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-emerald-300 text-xs"></i>
                    </div>
                    Laporan & grafik arus kas otomatis
                </div>
                <div class="flex items-center gap-3 text-white/80 text-sm">
                    <div class="w-6 h-6 rounded-full bg-emerald-400/30 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-emerald-300 text-xs"></i>
                    </div>
                    Anggaran bulanan per kategori
                </div>
                <div class="flex items-center gap-3 text-white/80 text-sm">
                    <div class="w-6 h-6 rounded-full bg-emerald-400/30 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-emerald-300 text-xs"></i>
                    </div>
                    Ekspor laporan ke PDF & Excel
                </div>
            </div>
        </div>

        <div class="relative z-10 text-white/40 text-xs">
            Data Anda aman & terenkripsi. &copy; <?= date('Y') ?> FinTrack.
        </div>
    </div>

    <!-- RIGHT PANEL: Form -->
    <div class="w-full lg:w-7/12 xl:w-[58%] flex items-center justify-center p-6 sm:p-10 bg-white overflow-y-auto">
        <div class="w-full max-w-md py-8">

            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background:linear-gradient(135deg,#10b981,#0891b2);">
                    <i class="fa-solid fa-wallet text-sm"></i>
                </div>
                <span class="font-extrabold text-xl" style="background:linear-gradient(135deg,#059669,#0891b2);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">FinTrack</span>
            </div>

            <div class="mb-7">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Buat Akun Baru</h2>
                <p class="text-gray-500 text-sm mt-2">Isi data di bawah untuk membuat akun FinTrack Anda.</p>
            </div>

            <?= form_open('auth/register', ['class' => 'space-y-4']) ?>

                <!-- Nama -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-regular fa-user text-gray-400 text-sm"></i>
                        </div>
                        <input id="name" name="name" type="text" required
                            class="input-field <?= !empty($field_errors['name']) ? 'input-error' : '' ?>"
                            placeholder="Nama lengkap Anda"
                            value="<?= htmlspecialchars($old_input['name'] ?? '') ?>">
                    </div>
                    <?php if (!empty($field_errors['name'])): ?>
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> <?= $field_errors['name'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400 text-sm"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="input-field <?= !empty($field_errors['email']) ? 'input-error' : '' ?>"
                            placeholder="nama@email.com"
                            value="<?= htmlspecialchars($old_input['email'] ?? '') ?>">
                    </div>
                    <?php if (!empty($field_errors['email'])): ?>
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> <?= $field_errors['email'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400 text-sm"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="input-field pr-10 <?= !empty($field_errors['password']) ? 'input-error' : '' ?>"
                            placeholder="Minimal 8 karakter">
                        <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors" data-target="password">
                            <i class="fa-regular fa-eye toggle-icon text-sm"></i>
                        </button>
                    </div>
                    <?php if (!empty($field_errors['password'])): ?>
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> <?= $field_errors['password'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirm" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400 text-sm"></i>
                        </div>
                        <input id="password_confirm" name="password_confirm" type="password" autocomplete="new-password" required
                            class="input-field pr-10 <?= !empty($field_errors['password_confirm']) ? 'input-error' : '' ?>"
                            placeholder="Ulangi password Anda">
                        <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors" data-target="password_confirm">
                            <i class="fa-regular fa-eye toggle-icon text-sm"></i>
                        </button>
                    </div>
                    <?php if (!empty($field_errors['password_confirm'])): ?>
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> <?= $field_errors['password_confirm'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white shadow-lg shadow-emerald-500/30 transition-all hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 mt-2"
                    style="background: linear-gradient(135deg, #059669, #0d9488);">
                    <i class="fa-solid fa-user-plus text-sm"></i>
                    Buat Akun Sekarang
                </button>

            <?= form_close() ?>

            <div class="flex items-center gap-3 my-5">
                <div class="h-px flex-1 bg-gray-200"></div>
                <span class="text-xs text-gray-400 font-medium">Sudah punya akun?</span>
                <div class="h-px flex-1 bg-gray-200"></div>
            </div>

            <a href="<?= site_url('auth/login') ?>"
               class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-primary-700 border-2 border-primary-200 hover:border-primary-400 hover:bg-primary-50 transition-all">
                <i class="fa-solid fa-right-to-bracket text-sm"></i>
                Masuk ke Akun
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "timeOut": "5000" };
        <?php if (!empty($field_errors['general'])): ?>
            toastr.error("<?= addslashes($field_errors['general']) ?>");
        <?php endif; ?>

        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.target);
                const icon  = this.querySelector('.toggle-icon');
                const isPass = input.type === 'password';
                input.type = isPass ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !isPass);
                icon.classList.toggle('fa-eye-slash', isPass);
            });
        });
    </script>
</body>
</html>
