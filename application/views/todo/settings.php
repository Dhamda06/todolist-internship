<?php
// FILE INI HARUS DI-LOAD OLEH CONTROLLER Todo.php -> settings()
$current_section = $current_section ?? 'settings';
$flash_message = $flash_message ?? '';
$flash_type = $flash_type ?? 'info';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan - To-Do List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* SALIN SEMUA CSS KUSTOM DARI FILE INDEX.PHP ANDA DI SINI */
        :root {
            --bg-light: #f9fbfd;
            --bg-dark: #181818;
            --text-dark: #1a1a1a;
            --text-light: #f5f5f5;
            --card-dark: #242424;
            --primary-olive: #6B8E23;
            --secondary-olive: #556B2F;
            --light-gray-card: #e9ecef;
            --dark-gray-card: #495057;
            --edit-color-light: #6c757d;
            --edit-color-dark: #adb5bd;
            --edit-hover-light: #5a6268;
            --edit-hover-dark: #8f979e;

            /* Variabel untuk border dan shadow dalam mode gelap */
            --border-dark-mode: #333;
            --shadow-dark-mode: rgba(0,0,0,0.5);
            --placeholder-dark-mode: #aaa;
            --link-dark-mode: #99ccff;
            --link-dark-mode-hover: #cceeff;
            --archive-color-light: #800000;
            --archive-color-dark: #990000;

            /* Warna Prioritas untuk Garis Batas Baris */
            --priority-high-light: #dc3545;
            --priority-medium-light: #6f42c1;
            --priority-low-light: #17a2b8;

            --priority-high-dark: #ff6b6b;
            --priority-medium-dark: #a27dd2;
            --priority-low-dark: #5bc0de;

            /* Warna baru untuk kartu prioritas */
            --card-priority-high-bg-light: #ffe6e6;
            --card-priority-high-text-light: #dc3545;
            --card-priority-high-bg-dark: #7b0000;
            --card-priority-high-text-dark: #ffb3b3;

            --card-priority-medium-bg-light: #f3e6ff;
            --card-priority-medium-text-light: #6f42c1;
            --card-priority-medium-bg-dark: #4a1d82;
            --card-priority-medium-text-dark: #d4bfff;

            --card-priority-low-bg-light: #e6f7ff;
            --card-priority-low-text-light: #17a2b8;
            --card-priority-low-bg-dark: #0f687a;
            --card-priority-low-text-dark: #b3ecf6;
        }
        body {
            overflow-y: scroll;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            padding: 2rem;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            transition: background-color 0.4s ease-in-out;
        }
        body.dark-mode { background-color: var(--bg-dark); color: var(--text-light); background-image: none; }
        body:not(.dark-mode)::before {
            content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("<?= base_url('asset/images/cov.jpg'); ?>");
            background-size: cover; background-position: center; background-repeat: no-repeat;
            background-attachment: fixed; opacity: 0.9; z-index: -1; transition: opacity 0.4s ease-in-out;
        }
        body.dark-mode::before { background-image: url("<?= base_url('asset/images/cov.jpg'); ?>"); opacity: 0.2; transition: opacity 0.4s ease-in-out; }
        .dark-mode .card { background-color: var(--card-dark); color: var(--text-light); }
        .card { border-radius: 1.25rem; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.3s; }
        .dark-mode .card { box-shadow: 0 10px 30px var(--shadow-dark-mode); }
        h1 { font-weight: 700; font-size: 2.25rem; text-align: center; margin-bottom: 2rem; }
        .section-title { font-weight: 600; font-size: 1.1rem; margin-top: 0.5rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .form-control, .form-select { border-radius: 0.75rem; background-color: #fff; color: var(--text-dark); border: 1px solid #dee2e6; transition: all 0.3s ease; }
        .dark-mode .form-control, .dark-mode .form-select { background-color: #2a2a2a; color: var(--text-light); border-color: var(--border-dark-mode); }
        .dark-mode .form-control::placeholder, .dark-mode .form-select::placeholder { color: var(--placeholder-dark-mode); }
        .btn { border-radius: 0.75rem; transition: 0.2s ease; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .dark-mode .btn:hover { box-shadow: 0 4px 8px var(--shadow-dark-mode); }
        .toggle-mode { position: fixed; top: 20px; right: 25px; z-index: 999; border-radius: 50%; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; background-color: var(--bg-light); border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 2px 5px rgba(0,0,0,0.1); color: var(--text-dark); cursor: pointer; transition: all 0.3s ease; }
        .dark-mode .toggle-mode { background-color: var(--card-dark); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 2px 5px rgba(0,0,0,0.3); color: var(--text-light); }
        .toggle-mode i { font-size: 1.2rem; }
        .btn-olive { color: #fff; background-color: var(--primary-olive); border-color: var(--primary-olive); }
        .btn-outline-olive { color: var(--primary-olive); border-color: var(--primary-olive); }
        .main-nav { background-color: rgba(255, 255, 255, 0.9); box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-radius: 0.75rem; padding: 0.5rem 1rem; margin-bottom: 2rem; display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap; }
        .dark-mode .main-nav { background-color: rgba(36, 36, 36, 0.9); box-shadow: 0 2px 10px var(--shadow-dark-mode); }
        .main-nav .nav-link { font-weight: 600; color: var(--text-dark); transition: all 0.3s ease; border-radius: 0.5rem; padding: 0.5rem 1rem; }
        .dark-mode .main-nav .nav-link { color: var(--text-light); }
        .main-nav .nav-link:hover { background-color: rgba(0,0,0,0.05); }
        .dark-mode .main-nav .nav-link:hover { background-color: rgba(255,255,255,0.08); }
        .main-nav .nav-link.active { color: #fff !important; background-color: var(--primary-olive) !important; }
        .dark-mode .main-nav .nav-link.active { background-color: var(--secondary-olive) !important; }
        #toastContainer { position: fixed; top: 1rem; left: 50%; transform: translateX(-50%); z-index: 1090; width: 90%; max-width: 400px; }
        .toast-body { background-color: var(--bg-light); color: var(--text-dark); }
        .dark-mode .toast-body { background-color: var(--card-dark); color: var(--text-light); }

        /* CSS KHUSUS UNTUK SETTINGS.PHP */
        .settings-card {
            border: 1px solid #dee2e6;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            transition: all 0.3s ease;
        }
        .dark-mode .settings-card {
            background-color: var(--card-dark);
            border-color: var(--border-dark-mode);
        }
        .settings-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .dark-mode .settings-card:hover {
            box-shadow: 0 8px 15px var(--shadow-dark-mode);
        }
        .settings-card h6 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
        }
        .settings-card h6 i {
            font-size: 1.5rem;
            color: var(--primary-olive);
            line-height: 1;
            transition: transform 0.3s ease;
        }
        .dark-mode .settings-card h6 i {
            color: #A7D129;
        }
        .settings-card:hover h6 i {
            transform: scale(1.1) rotate(-5deg);
        }

        .settings-card p.small {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .dark-mode .settings-card p.small {
            color: #adb5bd;
        }

        .form-check.form-switch {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .form-check-input {
            width: 3.5rem;
            height: 2rem;
            cursor: pointer;
            border-radius: 2rem;
            background-color: #adb5bd;
            border-color: #adb5bd;
            transition: background-color 0.3s ease, border-color 0.3s ease;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            outline: none;
        }
        .form-check-input:checked {
            background-color: var(--primary-olive);
            border-color: var(--primary-olive);
        }
        .form-check-input:focus {
            box-shadow: none;
        }
        .form-check-label {
            font-weight: 600;
            color: var(--text-dark);
        }
        .dark-mode .form-check-label {
            color: var(--text-light);
        }

        .toggle-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            width: 100%;
        }
        .toggle-options .btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease-in-out;
            border: 2px solid #dee2e6;
            color: #6c757d;
        }
        .dark-mode .toggle-options .btn {
            border-color: var(--border-dark-mode);
            color: var(--edit-color-dark);
        }
        .toggle-options .btn.active {
            background-color: var(--primary-olive);
            border-color: var(--primary-olive);
            color: white;
            box-shadow: 0 4px 10px rgba(107, 142, 35, 0.3);
        }
        .toggle-options .btn:hover:not(.active) {
            background-color: rgba(107, 142, 35, 0.1);
            color: var(--primary-olive);
            border-color: var(--primary-olive);
            transform: translateY(-2px);
        }
        .toggle-options .btn i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .dark-mode .toggle-options .btn.active {
             background-color: var(--secondary-olive);
             border-color: var(--secondary-olive);
             box-shadow: 0 4px 10px var(--shadow-dark-mode);
        }
        .dark-mode .toggle-options .btn:hover:not(.active) {
            background-color: rgba(167, 209, 41, 0.1);
            color: var(--primary-olive);
            border-color: var(--primary-olive);
        }
    </style>
</head>
<body>

<div class="toast-container" id="toastContainer"></div>

<button class="toggle-mode" onclick="toggleMode()" aria-label="Toggle dark mode">
    <i class="bi bi-moon-fill"></i>
</button>

<div class="container">
    <div class="card">
        <h1>⚙️ Pengaturan Aplikasi</h1>

        <nav class="main-nav">
            <a class="nav-link" href="<?= site_url('todo/index') ?>?section=home">Home</a>
            <a class="nav-link" href="<?= site_url('todo/index') ?>?section=tasks">Daftar Tugas</a>
            <a class="nav-link" href="<?= site_url('todo/index') ?>?section=statistics">Statistik Tugas</a>
            <a class="nav-link" href="<?= site_url('todo/index') ?>?section=archived">Arsip Tugas</a>
            <a class="nav-link active" href="<?= site_url('todo/settings') ?>">Pengaturan</a>
        </nav>
        <hr class="mb-4">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="settings-card">
                    <h6><i class="bi bi-palette-fill"></i> Tema Aplikasi</h6>
                    <p class="text-muted small">Pilih antara mode terang (light) atau mode gelap (dark) untuk kenyamanan mata.</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <label class="form-check-label" for="themeSwitch">
                            <span id="themeLabel">Mode Terang</span>
                        </label>
                        <div class="form-check form-switch p-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="themeSwitch" aria-label="Toggle dark mode">
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <h6><i class="bi bi-layout-text-window-reverse"></i> Tampilan Default Daftar Tugas</h6>
                    <p class="text-muted small">Pilih tampilan yang akan muncul pertama kali di halaman "Daftar Tugas".</p>
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <button type="button" class="btn flex-fill task-view-toggle btn-outline-secondary" data-view="table">
                            <i class="bi bi-table"></i> Tampilan Tabel
                        </button>
                        <button type="button" class="btn flex-fill task-view-toggle btn-outline-secondary" data-view="card">
                            <i class="bi bi-card-list"></i> Tampilan Kartu
                        </button>
                    </div>
                </div>

                <div class="settings-card">
                    <h6><i class="bi bi-bell-fill"></i> Notifikasi</h6>
                    <p class="text-muted small">Aktifkan atau nonaktifkan notifikasi pop-up di pojok atas untuk setiap aksi.</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <label class="form-check-label" for="enableNotifications">
                            Tampilkan Notifikasi Toast
                        </label>
                        <div class="form-check form-switch p-0">
                             <input class="form-check-input" type="checkbox" role="switch" id="enableNotifications">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // --- FUNGSI UTILITY GLOBAL ---
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer');
        const toastId = 'toast-' + Date.now();
        let headerBgClass, iconClass, titleText;
        switch(type) {
            case 'success': headerBgClass = 'bg-success text-white'; iconClass = 'bi-check-circle-fill'; titleText = 'Berhasil!'; break;
            case 'danger': headerBgClass = 'bg-danger text-white'; iconClass = 'bi-exclamation-octagon-fill'; titleText = 'Gagal!'; break;
            case 'warning': headerBgClass = 'bg-warning text-dark'; iconClass = 'bi-exclamation-triangle-fill'; titleText = 'Peringatan!'; break;
            default: headerBgClass = 'bg-info text-white'; iconClass = 'bi-info-circle-fill'; titleText = 'Informasi'; break;
        }
        const isDarkMode = document.body.classList.contains('dark-mode');
        if (isDarkMode) { 
            headerBgClass = headerBgClass.replace('text-dark', 'text-white'); 
        }
        const toastHtml = `<div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true"><div class="toast-header ${headerBgClass}"><i class="bi ${iconClass} me-2"></i><strong class="me-auto">${titleText}</strong><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button></div><div class="toast-body">${message}</div></div>`;
        if (localStorage.getItem('enable_notifications') !== 'false') {
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastEl = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => { toastEl.remove(); });
        }
    }
    
    // --- LOGIKA PENGATURAN TEMA ---
    function toggleMode() {
        const themeSwitch = document.getElementById('themeSwitch');
        const themeLabel = document.getElementById('themeLabel');
        const modeToggleButtonIcon = document.querySelector('.toggle-mode i');
        
        const isDarkMode = !document.body.classList.contains('dark-mode');
        
        if (isDarkMode) {
            document.body.classList.add("dark-mode");
            localStorage.setItem('dark-mode', 'true');
        } else {
            document.body.classList.remove("dark-mode");
            localStorage.setItem('dark-mode', 'false');
        }
        
        // Perbarui UI di semua elemen terkait
        themeSwitch.checked = isDarkMode;
        themeLabel.textContent = isDarkMode ? "Mode Gelap" : "Mode Terang";
        modeToggleButtonIcon.className = isDarkMode ? "bi bi-sun-fill" : "bi bi-moon-fill";

        showToast(`Mode tampilan diubah ke ${isDarkMode ? 'Gelap' : 'Terang'}.`, 'info');
    }

    // --- LOGIKA PENGATURAN TAMPILAN TUGAS ---
    function toggleTaskView(view) {
        const buttons = document.querySelectorAll('.task-view-toggle');
        buttons.forEach(button => {
            if (button.dataset.view === view) {
                button.classList.add('active');
                button.classList.remove('btn-outline-secondary');
            } else {
                button.classList.remove('active');
                button.classList.add('btn-outline-secondary');
            }
        });
        localStorage.setItem('default_task_view', view);
        showToast(`Tampilan default diubah ke ${view}.`, 'info');
    }

    // --- LOGIKA PENGATURAN NOTIFIKASI ---
    function toggleNotifications() {
        const enableNotificationsCheckbox = document.getElementById('enableNotifications');
        const isEnabled = enableNotificationsCheckbox.checked;
        localStorage.setItem('enable_notifications', isEnabled);
        if (isEnabled) {
            showToast('Notifikasi diaktifkan.', 'success');
        } else {
            showToast('Notifikasi dinonaktifkan.', 'warning');
        }
    }

    // --- INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Inisialisasi Mode Gelap/Terang
        const savedMode = localStorage.getItem('dark-mode');
        const isSystemDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedMode === 'true' || (savedMode === null && isSystemDarkMode)) {
            document.body.classList.add('dark-mode');
        }
        
        const isDarkModeActive = document.body.classList.contains('dark-mode');
        const themeSwitch = document.getElementById('themeSwitch');
        const themeLabel = document.getElementById('themeLabel');
        const modeToggleButtonIcon = document.querySelector('.toggle-mode i');

        themeSwitch.checked = isDarkModeActive;
        themeLabel.textContent = isDarkModeActive ? "Mode Gelap" : "Mode Terang";
        modeToggleButtonIcon.className = isDarkModeActive ? "bi bi-sun-fill" : "bi bi-moon-fill";

        // Hubungkan event listener
        document.getElementById('themeSwitch').addEventListener('change', toggleMode);

        // 2. Inisialisasi Tampilan Default Tugas
        const defaultTaskView = localStorage.getItem('default_task_view') || 'table';
        toggleTaskView(defaultTaskView);
        document.querySelectorAll('.task-view-toggle').forEach(button => {
            button.addEventListener('click', function() {
                toggleTaskView(this.dataset.view);
            });
        });

        // 3. Inisialisasi Notifikasi
        const enableNotificationsCheckbox = document.getElementById('enableNotifications');
        const savedNotificationPref = localStorage.getItem('enable_notifications');
        if (savedNotificationPref === 'false') {
            enableNotificationsCheckbox.checked = false;
        } else {
            enableNotificationsCheckbox.checked = true;
        }
        enableNotificationsCheckbox.addEventListener('change', toggleNotifications);
        
        // 4. Tampilkan flashdata dari server (jika ada)
        const flashMessage = `<?= htmlspecialchars($flash_message ?? '') ?>`;
        const flashType = `<?= htmlspecialchars($flash_type ?? '') ?>`;
        if (flashMessage) {
            showToast(flashMessage, flashType);
        }
    });
</script>

</body>
</html>