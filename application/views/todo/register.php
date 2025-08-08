<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register | List'in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Variabel CSS untuk kemudahan tema */
        :root {
            --primary-olive: #6B8E23;
            --secondary-olive: #556B2F;
            --bg-light: #f4f7f9;
            --bg-dark: #1f2937;
            --text-dark: #1a1a1a;
            --text-light: #f5f5f5;
            --gradient-main: linear-gradient(135deg, #A7D129 0%, #6B8E23 100%);
            --shadow-light: 0 12px 25px rgba(0,0,0,0.1);
            --shadow-dark: 0 12px 25px rgba(0,0,0,0.3);
            --body-bg-gradient: linear-gradient(to bottom right, #e8f5e9, #c8e6c9, #e8f5e9);
            --dark-body-bg-gradient: linear-gradient(to bottom right, #2c3e50, #4a677a, #2c3e50);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
            transition: background-color 0.4s ease-in-out, color 0.4s ease-in-out;
            background: var(--body-bg-gradient);
            background-attachment: fixed;
            background-size: cover;
        }

        .dark-mode body {
            background: var(--dark-body-bg-gradient);
            background-attachment: fixed;
            background-size: cover;
            color: var(--text-light);
        }
        .background-animated {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 0v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM30 0L0 30L30 60L60 30L30 0z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .register-container {
            max-width: 530px;
            width: 100%;
            padding: 3rem;
            border-radius: 1.5rem;
            background-color: #ffffff;
            box-shadow: var(--shadow-light);
            animation: fadeIn 1s ease-out;
            position: relative;
            z-index: 10;
        }

        .dark-mode .register-container {
            background-color: var(--bg-dark);
            color: var(--text-light);
            box-shadow: var(--shadow-dark);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .register-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: transparent;
            background-image: var(--gradient-main);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .register-header p {
            color: #6c757d;
            font-size: 1.1rem;
            margin: 0;
        }
        
        .dark-mode .register-header p {
            color: #adb5bd;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .dark-mode .form-label {
            color: var(--text-light);
        }

        .form-control {
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            padding: 0.9rem 1.25rem;
            background-color: #f1f3f5;
            border: 1px solid #dee2e6;
        }

        .dark-mode .form-control {
            background-color: #2e3a47;
            border-color: #4a5568;
            color: var(--text-light);
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(107, 142, 35, 0.25);
            border-color: var(--primary-olive);
            background-color: #fff;
        }
        .dark-mode .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(167, 209, 41, 0.25);
            background-color: #2e3a47;
            color: var(--text-light);
        }

        .btn-register {
            background-image: var(--gradient-main);
            border: none;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(107, 142, 35, 0.3);
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(107, 142, 35, 0.4);
        }

        /* ----- Perbaikan Ukuran Notifikasi ----- */
        .alert {
            background-color: #ffebee;
            border: 1px solid #ef9a9a;
            color: #c62828;
            padding: 0.75rem 1.25rem; /* Ukuran padding lebih kecil */
            border-radius: 1rem; /* Sudut sedikit melengkung */
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.15);
            font-weight: 600;
            font-size: 0.9rem; /* Ukuran font lebih kecil */
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            animation: fadeInSlideDown 0.6s ease-out;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
            transition: opacity 0.5s ease;
        }

        /* Tampilan Notifikasi Success */
        .alert-success {
            background-color: #e8f5e9;
            border-color: #81c784;
            color: #2e7d32;
        }
        .alert-success i {
            color: #43a047;
        }

        /* Tampilan Notifikasi Info */
        .alert-info {
            background-color: #e3f2fd;
            border-color: #64b5f6;
            color: #1976d2;
        }
        .alert-info i {
            color: #2196f3;
        }
        
        .dark-mode .alert {
            background-color: #5d2b2b;
            border-color: #8c3b3b;
            color: #ffcdd2;
            box-shadow: 0 4px 15px rgba(229, 57, 53, 0.25);
        }
        .dark-mode .alert-success {
            background-color: #384e3a;
            border-color: #4a674e;
            color: #c8e6c9;
        }
        .dark-mode .alert-success i {
            color: #81c784;
        }
        .dark-mode .alert-info {
            background-color: #2c3e50;
            border-color: #4a677a;
            color: #bbdefb;
        }
        .dark-mode .alert-info i {
            color: #64b5f6;
        }


        .alert i {
            font-size: 1.25rem; /* Ukuran ikon lebih kecil */
            margin-right: 0.75rem; /* Jarak ikon lebih kecil */
        }

        @keyframes fadeInSlideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert.fade-out {
            animation: fadeOutSlideUp 0.5s ease-in forwards;
        }

        @keyframes fadeOutSlideUp {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-30px); }
        }
        /* ------------------------------------------- */

        .login-link a {
            color: var(--primary-olive);
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .dark-mode .login-link a {
            color: #a7d129;
        }

        .login-link a:hover {
            color: var(--secondary-olive);
            text-decoration: underline;
        }

        .input-group-text {
            cursor: pointer;
            border-top-left-radius: 0.75rem;
            border-bottom-left-radius: 0.75rem;
            background-color: #e9ecef;
        }

        .dark-mode .input-group-text {
            background-color: #3b4554;
            border-color: #4a5568;
            color: var(--text-light);
        }

        .input-group-text.toggle-password {
            background-color: #e9ecef;
            border-top-right-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            border-left: none;
        }

        .dark-mode .input-group-text.toggle-password {
            background-color: #3b4554;
            border-color: #4a5568;
        }

        .input-group-text i {
            transition: color 0.3s ease;
        }

        .input-group-text i:hover {
            color: var(--primary-olive);
        }
    </style>
</head>
<body>
<div class="background-animated"></div>
    <div class="register-container">
        <div class="register-header">
            <h2>List'in</h2>
            <p>Daftar sekarang dan kelola tugas Anda</p>
        </div>

        <?php if (!empty($flash_message)): ?>
            <div id="alert-notification" class="alert alert-<?= $flash_type ?> text-center" role="alert">
                <?php
                    // Pilih ikon berdasarkan jenis notifikasi
                    $icon_class = 'bi-info-circle';
                    if ($flash_type === 'success') {
                        $icon_class = 'bi-check-circle';
                    } elseif ($flash_type === 'danger') {
                        $icon_class = 'bi-x-circle';
                    } elseif ($flash_type === 'warning') {
                        $icon_class = 'bi-exclamation-triangle';
                    }
                ?>
                <i class="bi <?= $icon_class ?> me-2"></i>
                <span><?= $flash_message ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('todo/process_register') ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan Username Anda" required minlength="5">
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan alamat email Anda" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Buat password (minimal 6 karakter)" required minlength="6">
                    <span class="input-group-text toggle-password" id="togglePassword">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label for="passconf" class="form-label">Konfirmasi Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="passconf" id="passconf" class="form-control" placeholder="Ketik ulang password Anda" required>
                    <span class="input-group-text toggle-password" id="togglePassconf">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn btn-register w-100 mt-4">
                <i class="bi bi-person-add me-2"></i>Daftar
            </button>
        </form>
        <p class="text-center mt-4 login-link">Sudah punya akun? <a href="<?= site_url('todo/login') ?>">Masuk</a></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logika untuk menampilkan/menyembunyikan password
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const togglePassconf = document.getElementById('togglePassconf');
            const passconfInput = document.getElementById('passconf');

            function toggleVisibility(inputElement, iconElement) {
                const type = inputElement.getAttribute('type') === 'password' ? 'text' : 'password';
                inputElement.setAttribute('type', type);
                iconElement.classList.toggle('bi-eye');
                iconElement.classList.toggle('bi-eye-slash');
            }

            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    toggleVisibility(passwordInput, this.querySelector('i'));
                });
            }

            if (togglePassconf) {
                togglePassconf.addEventListener('click', function() {
                    toggleVisibility(passconfInput, this.querySelector('i'));
                });
            }

            // Logika untuk notifikasi otomatis hilang
            const alertElement = document.querySelector('.alert');
            if (alertElement) {
                setTimeout(() => {
                    alertElement.classList.add('fade-out');
                    setTimeout(() => {
                        alertElement.remove();
                    }, 500); // Waktu yang sama dengan durasi transisi CSS
                }, 5000); // Notifikasi akan hilang setelah 5 detik
            }
        });
    </script>
</body>
</html>