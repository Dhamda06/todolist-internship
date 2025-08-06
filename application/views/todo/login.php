<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | List'in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
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

        .login-container {
            max-width: 520px;
            width: 100%;
            padding: 3rem;
            border-radius: 1.5rem;
            background-color: #ffffff;
            box-shadow: var(--shadow-light);
            animation: fadeIn 1s ease-out;
            position: relative;
            z-index: 10;
        }

        .dark-mode .login-container {
            background-color: var(--bg-dark);
            color: var(--text-light);
            box-shadow: var(--shadow-dark);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            font-size: 2.2rem;
            font-weight: 700;
            color: transparent;
            background-image: var(--gradient-main);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .login-header p {
            color: #6c757d;
            font-size: 1rem;
            margin: 0;
        }
        
        .dark-mode .login-header p {
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
            padding: 0.75rem 1.25rem;
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

        .btn-login {
            background-image: var(--gradient-main);
            border: none;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(107, 142, 35, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(107, 142, 35, 0.4);
        }

        .alert {
            border-radius: 0.75rem;
            font-weight: 600;
            margin-bottom: 2rem;
            animation: slideInDown 0.5s ease-out;
        }

        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-link a {
            color: var(--primary-olive);
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .dark-mode .register-link a {
            color: #a7d129;
        }

        .register-link a:hover {
            color: var(--secondary-olive);
            text-decoration: underline;
        }

        /* Style untuk ikon password */
        .input-group-text {
            cursor: pointer;
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
    <div class="login-container">
        <div class="login-header">
            <h2>List'in</h2>
            <p>Silakan masuk untuk melanjutkan</p>
        </div>

        <?php if (!empty($flash_message)): ?>
            <div class="alert alert-<?= $flash_type ?> text-center" role="alert">
                <i class="bi bi-info-circle me-2"></i><?= $flash_message ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('todo/process_login') ?>" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="email" id="email" class="form-control" placeholder="Masukkan Email Anda" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password Anda" required>
                    <span class="input-group-text toggle-password" id="togglePassword">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn btn-login w-100 mt-4">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>
        <p class="text-center mt-4 register-link">Belum punya akun? <a href="<?= site_url('todo/register') ?>">Daftar sekarang</a></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logika untuk hide/unhide password
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('bi-eye');
                this.querySelector('i').classList.toggle('bi-eye-slash');
            });
        });
    </script>
</body>
</html>