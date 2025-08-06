<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register | List'in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 2rem; }
        .register-container { max-width: 450px; padding: 2.5rem; border-radius: 1.5rem; background-color: #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h2 { text-align: center; font-weight: 700; color: #6B8E23; margin-bottom: 2rem; }
        .form-control { border-radius: 0.75rem; }
        .btn-register { background-color: #6B8E23; border-color: #6B8E23; color: white; font-weight: 600; }
        .btn-register:hover { background-color: #556B2F; border-color: #556B2F; }
        .alert { border-radius: 0.75rem; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Daftar Akun Baru</h2>
        <?php if (!empty($flash_message)): ?>
            <div class="alert alert-<?= $flash_type ?>" role="alert">
                <?= $flash_message ?>
            </div>
        <?php endif; ?>
        <form action="<?= site_url('todo/process_register') ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required minlength="5">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label for="passconf" class="form-label">Konfirmasi Password</label>
                <input type="password" name="passconf" id="passconf" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-register w-100 mt-3">Daftar</button>
        </form>
        <p class="text-center mt-3">Sudah punya akun? <a href="<?= site_url('todo/login') ?>">Masuk</a></p>
    </div>
</body>
</html>