<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Lupa Password') ?> — FinTrack</title>
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
    </style>
</head>
<body class="min-h-screen flex">

    <!-- LEFT PANEL -->
    <div class="hidden lg:flex lg:w-5/12 relative overflow-hidden flex-col justify-between p-12"
         style="background: linear-gradient(150deg, #065f46 0%, #059669 40%, #0d9488 70%, #0369a1 100%);">

        <div class="absolute inset-0 overflow-hidden">
            <div class="shape-a absolute -top-16 -right-16 w-80 h-80 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #fff 0%, transparent 70%);"></div>
            <div class="shape-b absolute -bottom-20 -left-20 w-72 h-72 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #6ee7b7 0%, transparent 70%);"></div>
            <div class="shape-c absolute top-1/3 left-1/3 w-20 h-20 rounded-full opacity-20"
                 style="background: rgba(255,255,255,0.25); border:1px solid rgba(255,255,255,0.35);"></div>
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
            <div class="w-16 h-16 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center text-white text-3xl mb-6">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">
                Lupa Password?<br>
                <span style="color:#6ee7b7;">Tenang, Kami Bantu.</span>
            </h1>
            <p class="text-white/70 text-sm leading-relaxed max-w-xs">
                Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset password dalam hitungan menit.
            </p>

            <div class="mt-8 bg-white/10 border border-white/20 rounded-2xl p-5 space-y-3">
                <div class="flex items-start gap-3 text-white/80 text-sm">
                    <div class="w-7 h-7 rounded-full bg-emerald-400/25 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-emerald-300 font-bold text-xs">1</span>
                    </div>
                    <span>Masukkan alamat email akun Anda di bawah</span>
                </div>
                <div class="flex items-start gap-3 text-white/80 text-sm">
                    <div class="w-7 h-7 rounded-full bg-emerald-400/25 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-emerald-300 font-bold text-xs">2</span>
                    </div>
                    <span>Cek kotak masuk email Anda (termasuk folder spam)</span>
                </div>
                <div class="flex items-start gap-3 text-white/80 text-sm">
                    <div class="w-7 h-7 rounded-full bg-emerald-400/25 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-emerald-300 font-bold text-xs">3</span>
                    </div>
                    <span>Klik tautan di email dan buat password baru Anda</span>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-white/40 text-xs">
            &copy; <?= date('Y') ?> FinTrack &mdash; Manajemen Keuangan Pribadi
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="w-full lg:w-7/12 flex items-center justify-center p-6 sm:p-10 bg-white">
        <div class="w-full max-w-md">

            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background:linear-gradient(135deg,#10b981,#0891b2);">
                    <i class="fa-solid fa-wallet text-sm"></i>
                </div>
                <span class="font-extrabold text-xl" style="background:linear-gradient(135deg,#059669,#0891b2);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">FinTrack</span>
            </div>

            <!-- Icon -->
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-emerald-500/25"
                 style="background: linear-gradient(135deg, #059669, #0d9488);">
                <i class="fa-solid fa-envelope-circle-check"></i>
            </div>

            <div class="mb-7">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Reset Password</h2>
                <p class="text-gray-500 text-sm mt-2">Masukkan email terdaftar Anda. Kami akan mengirimkan tautan reset password segera.</p>
            </div>

            <?= form_open('auth/send_reset_link', ['class' => 'space-y-5', 'id' => 'forgot-form']) ?>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400 text-sm"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="input-field"
                            placeholder="nama@email.com">
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-info text-primary-400"></i>
                        Pastikan email yang Anda masukkan sudah terdaftar di FinTrack.
                    </p>
                </div>

                <button type="submit" id="btn-submit"
                    class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white shadow-lg shadow-emerald-500/30 transition-all hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0"
                    style="background: linear-gradient(135deg, #059669, #0d9488);">
                    <i class="fa-solid fa-paper-plane text-sm" id="btn-icon-send"></i>
                    <i class="fa-solid fa-spinner fa-spin text-sm hidden" id="btn-icon-spin"></i>
                    <span id="btn-text">Kirim Link Reset</span>
                </button>

            <?= form_close() ?>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <a href="<?= site_url('auth/login') ?>"
                   class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary-600 transition-colors group">
                    <i class="fa-solid fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                    Kembali ke halaman Login
                </a>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                &copy; <?= date('Y') ?> FinTrack &mdash; Manajemen Keuangan Pribadi
            </p>
        </div>
    </div>

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
            $('#btn-icon-send').addClass('hidden');
            $('#btn-icon-spin').removeClass('hidden');
            $('#btn-text').text('Mengirim...');
        });
    </script>
</body>
</html>
