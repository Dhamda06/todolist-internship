<?php
// FILE INI DIHARAPKAN DI-LOAD OLEH CONTROLLER Todo.php ANDA
// Seperti: $this->load->view('todo/index', $data);
// Dan menerima variabel $filter, $priority, $search, $todos (dari get_filtered model)

// Cek apakah pengguna sudah login
if (!$this->session->userdata('logged_in')) {
    redirect('todo/landing');
}

// **PENTING: Pastikan zona waktu diatur dengan benar untuk konsistensi waktu.**
date_default_timezone_set('Asia/Jakarta'); // Mengatur zona waktu ke WIB (Jakarta)

// Variabel-variabel ini DIASUMSIKAN sudah di-pass dari CodeIgniter Controller Anda:
$filter = $filter ?? ''; // Default value jika tidak ada
$priority = $priority ?? ''; // Default value
$search = $search ?? ''; // Default value
$todos = $todos ?? []; // Pastikan selalu array (ini adalah tugas aktif/difilter)
$todos_home = $todos_home ?? []; // Pastikan selalu array (tugas untuk home - mendekat)
$archived_todos = $archived_todos ?? []; // Pastikan selalu array
$current_section = $current_section ?? 'home'; // Default ke 'home'
$current_stats_view = $current_stats_view ?? 'card'; // Default ke 'card' for statistics section
$current_daily_chart_view = $current_daily_chart_view ?? 'status'; // Default to 'status' for daily chart
$current_task_view = $current_task_view ?? 'table'; // Tampilan default untuk daftar tugas: 'table' atau 'card'
$sort = $sort ?? ''; // Default value
$user_data = $user_data ?? null; // Tambahkan data pengguna untuk halaman settings

// Ambil URL profile picture dari data user, jika ada. Jika tidak, gunakan default.
$profile_pic_url = base_url('asset/images/profiles/' . ($user_data->profile_picture ?? 'default_profile.png'));

// Tambahkan variabel untuk data JavaScript jika belum ada
$all_active_tasks_for_js = $all_active_tasks_for_js ?? [];
$all_archived_tasks_for_js = $all_archived_tasks_for_js ?? [];

// Dapatkan pesan flashdata dari controller dengan sintaks CodeIgniter 3
$flash_message = $this->session->flashdata('message') ?? '';
$flash_type = $this->session->flashdata('type') ?? 'info';

// Dapatkan pengaturan pengguna dari database
$user_settings = $user_settings ?? (object)['theme' => 'light', 'background_type' => 'image', 'background_value' => 'default'];

// Logika untuk menentukan judul halaman/section
$page_title = 'To-Do List';
switch ($current_section) {
    case 'home':
        $page_title = 'Home';
        break;
    case 'about':
        $page_title = 'Tentang Kami';
        break;
    case 'tasks':
        $page_title = 'Daftar Tugas';
        break;
    case 'statistics':
        $page_title = 'Statistik Tugas';
        break;
    case 'archived':
        $page_title = 'Arsip Tugas';
        break;
    case 'settings':
        $page_title = 'Pengaturan';
        break;
}

// Tentukan kelas tema dari database
$theme_class = ($user_settings->theme ?? 'light') == 'dark' ? 'dark-mode' : '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?> | List'in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
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
            --border-dark-mode: #333;
            --shadow-dark-mode: rgba(0,0,0,0.5);
            --placeholder-dark-mode: #aaa;
            --link-dark-mode: #99ccff;
            --link-dark-mode-hover: #cceeff;
            --archive-color-light: #800000;
            --archive-color-dark: #990000;
            --priority-high-light: #dc3545;
            --priority-medium-light: #6f42c1;
            --priority-low-light: #17a2b8;
            --priority-high-dark: #ff6b6b;
            --priority-medium-dark: #a27dd2;
            --priority-low-dark: #5bc0de;
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
            --color-solid-bg1: #495057; /* Dark Gray */
            --color-solid-bg2: #6B8E23; /* Olive Drab */
            --color-solid-bg3: #87CEEB; /* Sky Blue */
            --color-solid-bg4: #F4C2C2; /* Powder Rose */
            /* New solid colors */
            --color-solid-bg5: #4B0082; /* Indigo */
            --color-solid-bg6: #800000; /* Maroon */
            --color-solid-bg7: #2F4F4F; /* Dark Slate Gray */
            --color-solid-bg8: #FFD700; /* Gold */
            /* New image backgrounds */
            --bg-image-3: url('<?= base_url('asset/images/bg3.jpg'); ?>');
            --bg-image-4: url('<?= base_url('asset/images/bg4.jpg'); ?>');
            --bg-image-5: url('<?= base_url('asset/images/bg5.jpg'); ?>');
            --bg-image-6: url('<?= base_url('asset/images/bg6.jpg'); ?>');
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
        body.dark-mode {
            background-color: var(--bg-dark);
            color: var(--text-light);
            background-image: none;
        }
        body:not(.dark-mode)::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: var(--bg-image);
            background-color: var(--bg-color);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            opacity: var(--bg-opacity, 0.9);
            z-index: -1;
            transition: opacity 0.4s ease-in-out;
        }
        body.dark-mode::before {
            background-image: var(--bg-image);
            background-color: var(--bg-color-dark);
            opacity: var(--bg-opacity-dark, 0.2);
            transition: opacity 0.4s ease-in-out;
        }
        .dark-mode .card {
            background-color: var(--card-dark);
            color: var(--text-light);
        }
        .card {
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }
        .dark-mode .card {
            box-shadow: 0 10px 30px var(--shadow-dark-mode);
        }
        h1 {
            font-weight: 700;
            font-size: 2.25rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .section-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title:first-of-type {
            margin-top: 0;
        }
        .form-control, .form-select {
            border-radius: 0.75rem;
            background-color: #fff;
            color: var(--text-dark);
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .dark-mode .form-control, .dark-mode .form-select {
            background-color: #2a2a2a;
            color: var(--text-light);
            border-color: var(--border-dark-mode);
        }
        .dark-mode .form-control::placeholder, .dark-mode .form-select::placeholder {
            color: var(--placeholder-dark-mode);
        }
        .dark-mode .form-control:focus, .dark-mode .form-select:focus {
            background-color: #2a2a2a;
            color: var(--text-light);
            border-color: #666;
            box-shadow: 0 0 0 0.25rem rgba(102, 102, 102, 0.25);
        }
        .btn {
            border-radius: 0.75rem;
            transition: 0.2s ease;
        }
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .dark-mode .btn:hover {
            box-shadow: 0 4px 8px var(--shadow-dark-mode);
        }
        .task-view-toggle:not(.active):hover {
            opacity: 1;
            transform: translateY(0);
            box-shadow: none;
        }
        .task-view-toggle.btn-outline-olive:hover {
            color: var(--primary-olive) !important;
            background-color: transparent !important;
            border-color: var(--primary-olive) !important;
        }
        .dark-mode .task-view-toggle.btn-outline-olive:hover {
            color: var(--primary-olive) !important;
            background-color: transparent !important;
            border-color: var(--primary-olive) !important;
        }
        .task-status-badge {
            border-radius: 1rem;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            text-transform: capitalize;
            white-space: nowrap;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 1rem;
        }
        .table th, .table td {
            padding: 0.75rem;
            vertical-align: top;
            white-space: nowrap;
            border: none;
            border-bottom: 2px solid #dee2e6;
        }
        .table thead th {
            border-bottom: 3px solid #dee2e6;
        }
        .table tbody tr {
            border-bottom: 3px solid #dee2e2;
        }
        .table tbody tr:last-child {
            border-bottom: none;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }
        .dark-mode .table th, .dark-mode .table td {
            border-bottom-color: var(--border-dark-mode);
        }
        .dark-mode .table thead th {
            border-bottom-color: var(--border-dark-mode);
        }
        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }
        .dark-mode table {
            background-color: var(--card-dark);
            color: var(--text-light);
        }
        .dark-mode .table th, .dark-mode .table td {
            background-color: var(--card-dark);
            color: var(--text-light);
        }
        .dark-mode .table thead {
            background-color: #1f1f1f;
            color: var(--text-light);
        }
        .dark-mode .table-hover tbody tr:hover {
            background-color: #2a2a2a;
        }
        .dark-mode .table-bordered {
            border-color: var(--border-dark-mode);
        }
        .dark-mode hr {
            border-color: var(--border-dark-mode);
            opacity: 0.25;
        }
        .table td.task-details {
            white-space: normal;
            word-break: break-word;
            width: 35%;
        }
        .table td.task-actions-cell {
            width: 200px;
            min-width: 200px;
        }
        .table-edit-form {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            background: var(--bg-light);
            padding: 1rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
            width: 90%;
            max-width: 400px;
        }
        .dark-mode .table-edit-form {
            background: var(--card-dark);
            border-color: var(--border-dark-mode);
            box-shadow: 0 4px 10px var(--shadow-dark-mode);
        }
        .btn-toggle-edit {
            width: 100%;
        }
        .table .task-status-badge,
        .table .priority-label {
            padding: 0.3em 0.6em;
            font-size: 0.75rem;
        }
        .empty-state {
            color: #adb5bd;
            font-style: italic;
            text-align: center;
        }
        .toggle-mode {
            position: fixed;
            top: 20px;
            right: 25px;
            z-index: 999;
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-light);
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .dark-mode .toggle-mode {
            background-color: var(--card-dark);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            color: var(--text-light);
        }
        .toggle-mode:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .dark-mode .toggle-mode:hover {
            box-shadow: 0 4px 8px var(--shadow-dark-mode);
        }
        .toggle-mode i {
            font-size: 1.2rem;
        }
        .form-inline-edit .form-control,
        .form-inline-edit .form-select {
            border-radius: 0.5rem;
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            margin-bottom: 0.25rem;
        }
        .form-inline-edit .btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        .stats-card {
            border: none !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            padding: 1.5rem !important;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .dark-mode .stats-card:hover {
            box-shadow: 8px 20px var(--shadow-dark-mode);
        }
        .stats-card .stats-icon {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            color: inherit;
            opacity: 0.7;
            transition: transform 0.3s ease;
        }
        .stats-card:hover .stats-icon {
            transform: scale(1.1) rotate(5deg);
        }
        .stats-card h6 {
            font-size: 1rem;
            font-weight: 700;
            opacity: 1;
            margin-bottom: 0.4rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .stats-card p {
            font-size: 2.5rem;
            line-height: 1;
            margin-bottom: 0;
            font-weight: 800;
        }
        .stats-card.belum {
            background-color: var(--light-gray-card);
            color: #343a40;
        }
        .dark-mode .stats-card.belum {
            background-color: var(--dark-gray-card);
            color: var(--text-light);
        }
        .stats-card.progress {
            background-color: #ffc107;
            color: #343a40;
        }
        .dark-mode .stats-card.progress {
            background-color: #ffca2c;
            color: var(--text-dark);
        }
        .stats-card.selesai {
            background-color: #28a745;
            color: #ffffff;
        }
        .dark-mode .stats-card.selesai {
            background-color: #198754;
            color: #ffffff;
        }
        .stats-card.priority-high {
            background-color: var(--card-priority-high-bg-light);
            color: var(--card-priority-high-text-light);
        }
        .dark-mode .stats-card.priority-high {
            background-color: var(--card-priority-high-bg-dark);
            color: var(--card-priority-high-text-dark);
        }
        .stats-card.priority-medium {
            background-color: var(--card-priority-medium-bg-light);
            color: var(--card-priority-medium-text-light);
        }
        .dark-mode .stats-card.priority-medium {
            background-color: var(--card-priority-medium-bg-dark);
            color: var(--card-priority-medium-text-dark);
        }
        .stats-card.priority-low {
            background-color: var(--card-priority-low-bg-light);
            color: var(--card-priority-low-text-light);
        }
        .dark-mode .stats-card.priority-low {
            background-color: var(--card-priority-low-bg-dark);
            color: var(--card-priority-low-text-dark);
        }
        #doughnutChartWrapper {
            max-width: 450px;
            height: 450px;
            margin: 1.5rem auto;
            padding: 2rem;
            background-color: var(--bg-light);
            border-radius: 1.25rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #dailyTaskChartContainer {
            max-width: 900px;
            width: 100%;
            min-height: 400px;
            height: auto;
            margin: 1.5rem auto;
            padding: 2rem;
            background-color: var(--bg-light);
            border-radius: 1.25rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #monthlyActivityChartContainer {
            max-width: 900px;
            min-height: 400px;
            height: auto;
            margin: 1.5rem auto;
            padding: 2rem;
            background-color: var(--bg-light);
            border-radius: 1.25rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dark-mode #doughnutChartWrapper, .dark-mode #dailyTaskChartContainer, .dark-mode #monthlyActivityChartContainer {
            background-color: var(--card-dark);
            box-shadow: 0 4px 12px var(--shadow-dark-mode);
        }
        #dailyTaskChart, #taskStatusChart, #monthlyActivityChart {
            max-width: 100%;
            height: 100%;
        }
        @media (max-width: 767.98px) {
            #dailyTaskChartContainer, #monthlyActivityChartContainer {
                height: 300px;
                padding: 1rem;
            }
            #doughnutChartWrapper {
                max-width: 100%;
                height: 300px;
                padding: 1.5rem;
            }
        }
        .btn-olive {
            color: #fff;
            background-color: var(--primary-olive);
            border-color: var(--primary-olive);
        }
        .btn-olive:hover {
            color: #fff;
            background-color: var(--secondary-olive);
            border-color: var(--secondary-olive);
        }
        .btn-check:checked + .btn-olive, .btn-olive:active, .btn-olive.active, .show > .btn-olive.dropdown-toggle {
            color: #fff;
            background-color: var(--secondary-olive);
            border-color: var(--secondary-olive);
        }
        .btn-outline-olive {
            color: var(--primary-olive);
            border-color: var(--primary-olive);
        }
        .btn-outline-olive:hover {
            color: #fff;
            background-color: var(--primary-olive);
            border-color: var(--primary-olive);
        }
        .btn-check:checked + .btn-outline-olive, .btn-outline-olive:active, .btn-outline-olive.active, .btn-outline-olive.dropdown-toggle.show {
            color: #fff;
            background-color: var(--primary-olive);
            border-color: var(--primary-olive);
        }
        .btn-outline-archive {
            color: var(--archive-color-light);
            border-color: var(--archive-color-light);
            background-color: transparent;
        }
        .btn-outline-archive:hover {
            color: #fff;
            background-color: var(--archive-color-light);
            border-color: var(--archive-color-light);
        }
        .dark-mode .btn-outline-archive {
            color: var(--archive-color-dark);
            border-color: var(--archive-color-dark);
            background-color: transparent;
        }
        .dark-mode .btn-outline-archive:hover {
            color: var(--text-light);
            background-color: var(--archive-color-dark);
            border-color: var(--archive-color-dark);
        }
        .btn-archive {
            color: #fff;
            background-color: var(--archive-color-light);
            border-color: var(--archive-color-light);
        }
        .btn-archive:hover {
            color: #fff;
            background-color: #a00000;
            border-color: #a00000;
        }
        .dark-mode .btn-archive {
            color: #fff;
            background-color: var(--archive-color-dark);
            border-color: var(--archive-color-dark);
        }
        .dark-mode .btn-archive:hover {
            color: #fff;
            background-color: #b00000;
            border-color: #b00000;
        }
        .main-nav {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-radius: 0.75rem;
            padding: 0.5rem 1rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .dark-mode .main-nav {
            background-color: rgba(36, 36, 36, 0.9);
            box-shadow: 0 2px 10px var(--shadow-dark-mode);
        }
        .main-nav .nav-link {
            font-weight: 600;
            color: var(--text-dark);
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
        }
        .dark-mode .main-nav .nav-link {
            color: var(--text-light);
        }
        .main-nav .nav-link:hover {
            background-color: rgba(0,0,0,0.05);
        }
        .dark-mode .main-nav .nav-link:hover {
            background-color: rgba(255,255,255,0.08);
        }
        .main-nav .nav-link.active {
            color: #fff !important;
            background-color: var(--primary-olive) !important;
        }
        .dark-mode .main-nav .nav-link.active {
            background-color: var(--secondary-olive) !important;
        }
        #todosHomeContainer .task-card:hover {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .dark-mode #todosHomeContainer .task-card:hover {
            box-shadow: 0 4px 15px var(--shadow-dark-mode);
        }
        .welcome-section {
            text-align: center;
            padding: 3rem 1rem;
            margin-bottom: 2rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, rgba(107,142,35,0.8), rgba(85,107,47,0.8));
            color: white;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            animation: fadeInScale 0.8s ease-out;
        }
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .welcome-section h2 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .welcome-section p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .welcome-section .btn {
            margin-top: 1.5rem;
            font-size: 1.1rem;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            background-color: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.3);
            color: white;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
        .welcome-section .btn:hover {
            background-color: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.4);
            transform: translateY(-2px);
        }
        html {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        *, *::before, *::after {
            box-sizing: inherit;
        }
        .dark-mode .text-primary {
            color: #99ccff !important;
        }
        .dark-mode .text-info {
            color: #6edff6 !important;
        }
        .priority-label {
            padding: 0.25em 0.6em;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            min-width: 60px;
            text-align: center;
            white-space: nowrap;
        }
        .table-status-cell, .table-priority-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem;
        }
        .table-status-cell i, .table-priority-cell i {
            font-size: 1rem;
        }
        .priority-label.priority-tinggi { background-color: var(--priority-high-light); color: white; }
        .priority-label.priority-sedang { background-color: var(--priority-medium-light); color: white; }
        .priority-label.priority-rendah { background-color: var(--priority-low-light); color: white; }
        .dark-mode .priority-label.priority-tinggi { background-color: var(--priority-high-dark); color: var(--text-dark); }
        .dark-mode .priority-label.priority-sedang { background-color: var(--priority-medium-dark); color: var(--text-dark); }
        .dark-mode .priority-label.priority-rendah { background-color: var(--priority-low-dark); color: var(--text-dark); }
        .deadline-overdue { color: var(--priority-high-light); font-weight: 600; white-space: nowrap; }
        .dark-mode .deadline-overdue { color: var(--priority-high-dark); }
        .deadline-soon { color: #ffc107; font-weight: 600; white-space: nowrap; }
        .dark-mode .deadline-soon { color: #ffda6a; }
        .deadline-normal { color: #28a745; white-space: nowrap; }
        .dark-mode .deadline-normal { color: #198754; }
        
        /* New styling for finished task deadlines */
        .deadline-finished-light { color: #212529; white-space: nowrap; }
        .deadline-finished-dark { color: #adb5bd; white-space: nowrap; }

        .task-card {
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 0.5rem;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .dark-mode .task-card {
            background-color: var(--card-dark);
            box-shadow: 0 4-15px var(--shadow-dark-mode);
        }
        .task-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            background-color: lightgray;
            border-top-left-radius: 1rem;
            border-bottom-left-radius: 1rem;
        }
        .task-card.priority-tinggi::before { background-color: var(--priority-high-light); }
        .task-card.priority-sedang::before { background-color: var(--priority-medium-light); }
        .task-card.priority-rendah::before { background-color: var(--priority-low-light); }
        .dark-mode .task-card.priority-tinggi::before { background-color: var(--priority-high-dark); }
        .dark-mode .task-card.priority-sedang::before { background-color: var(--priority-medium-dark); }
        .dark-mode .task-card.priority-rendah::before { background-color: var(--priority-low-dark); }
        .task-title, .table-task-title {
            font-weight: 600;
            font-size: 1.15rem;
            margin-bottom: 0.5rem;
            padding-left: 10px;
            word-wrap: break-word;
        }
        .task-description, .table-task-description {
            font-size: 0.9rem;
            color: #6c757d;
            padding-left: 10px;
            margin-bottom: 0.75rem;
            word-wrap: break-word;
            white-space: pre-wrap;
        }
        .dark-mode .task-description, .dark-mode .table-task-description {
            color: #adb5bd;
        }
        .task-meta {
            font-size: 0.9rem;
            color: #6c757d;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            padding-left: 10px;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
        }
        .dark-mode .task-meta {
            color: #adb5bd;
        }
        .task-meta i {
            font-size: 1rem;
        }
        .btn-outline-edit {
            color: var(--edit-color-light);
            border-color: var(--edit-color-light);
            background-color: transparent;
        }
        .btn-outline-edit:hover {
            color: #fff;
            background-color: var(--edit-color-light);
            border-color: var(--edit-color-light);
        }
        .dark-mode .btn-outline-edit {
            color: var(--edit-color-dark);
            border-color: var(--edit-color-dark);
            background-color: transparent;
        }
        .dark-mode .btn-outline-edit:hover {
            color: var(--text-dark);
            background-color: var(--edit-color-dark);
            border-color: var(--edit-color-dark);
        }
        .task-card-item .task-actions {
            margin-top: 1rem;
            padding-left: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .task-card-item .task-actions .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            font-weight: 500;
        }
        .task-card-item .task-actions .btn i {
            font-size: 0.8rem;
        }
        .task-card-item .task-actions .btn-group {
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        .task-card-item .task-actions .btn-group .btn:first-child {
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }
        .task-card-item .task-actions .btn-group .btn:last-child {
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
        @media (max-width: 991.98px) {
            .stats-card h6 { font-size: 0.9rem; }
            .stats-card p { font-size: 2.2rem; }
            .stats-card .stats-icon { font-size: 2.8rem; }
            .main-nav {
                padding: 0.5rem;
                justify-content: space-around;
            }
            .main-nav .nav-link {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
                margin: 0 0.1rem;
            }
            .welcome-section h2 { font-size: 2rem; }
            .welcome-section p { font-size: 1rem; }
            .welcome-section .btn { font-size: 0.9rem; padding: 0.6rem 1.5rem; }
            .section-title { margin-top: 1.5rem; }
            .table th, .table td {
                font-size: 0.9em;
                padding: 0.5rem;
            }
            .form-inline-edit .form-control,
            .form-inline-edit .form-select {
                font-size: 0.75rem;
            }
            .form-inline-edit .btn {
                font-size: 0.75rem;
            }
        }
        @media (max-width: 767.98px) {
            body { padding: 1rem; }
            .card { padding: 1.5rem; }
            .stats-card { padding: 1rem !important; }
            .stats-card h6 { font-size: 0.8rem; }
            .stats-card p { font-size: 1.8rem; }
            .stats-card .stats-icon { font-size: 2.5rem; margin-bottom: 0.3rem; }
            .row.row-cols-1 > .col { flex: 0 0 auto; width: 100%; }
            .welcome-section { padding: 2rem 1rem; }
            .welcome-section h2 { font-size: 1.8rem; }
            .welcome-section p { font-size: 1rem; }
            .welcome-section .btn { font-size: 0.9rem; padding: 0.6rem 1.5rem; }
            .section-title { margin-top: 1.5rem; }
            .table th, .table td {
                font-size: 0.8em;
                padding: 0.4rem;
            }
            /* Menyesuaikan tampilan tabel untuk layar HP */
            .table thead {
                display: none;
            }
            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                padding: 0.5rem;
                border: 1px solid #dee2e6;
                border-radius: 0.5rem;
                box-shadow: 0 4px 8px rgba(0,0,0,0.05); /* Menambah shadow pada baris di HP */
            }
            .dark-mode .table tbody tr {
                border-color: var(--border-dark-mode);
                box-shadow: 0 4px 8px var(--shadow-dark-mode);
            }
            .table td {
                display: block;
                width: 100% !important;
                text-align: left !important;
                padding: 0.25rem 0.5rem;
                white-space: normal;
                word-break: break-word;
            }
            .table td {
                border-bottom: none;
            }
            .table-hover tbody tr:hover {
                background-color: #f1f3f5;
            }
            .dark-mode .table-hover tbody tr:hover {
                background-color: #2a2a2a;
            }
            .table td.task-details strong {
                margin-bottom: 0.25rem;
            }
            .table td.task-details small {
                margin-top: 0.25rem;
                color: #6c757d;
            }
            .dark-mode .table td.task-details small {
                color: #adb5bd;
            }
            .task-actions-cell {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                padding: 0.5rem 0;
            }
            .task-actions-cell > .btn-group,
            .task-actions-cell > .btn {
                width: 100%;
            }
            .form-inline-edit {
                display: block !important;
                width: 100%;
                box-sizing: border-box;
                margin-bottom: 0.5rem !important;
            }
            .form-inline-edit input,
            .form-inline-edit select,
            .form-inline-edit button {
                width: 100%;
                box-sizing: border-box;
                margin-bottom: 0.2rem !important;
            }
            .btn-group-sm .btn {
                font-size: 0.75rem;
                padding: 0.15rem 0.3rem;
            }
            .btn-group-sm {
                flex-wrap: wrap;
                gap: 0.2rem;
            }
            .task-card-list {
                grid-template-columns: 1fr;
            }
            #toastContainer {
                top: 0.5rem;
                left: 50%;
                transform: translateX(-50%);
                width: 95%;
                max-width: 350px;
            }
            .modal-dialog-centered.modal-sm {
                max-width: 90% !important;
                margin-top: unset;
                margin-bottom: unset;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }
            .modal-dialog-centered.modal-sm .modal-content {
                width: 90%;
                max-width: 350px;
                margin: auto;
                padding: 0.5rem;
                border-radius: 1rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }
            .modal-dialog-centered.modal-sm .modal-title {
                font-size: 1rem;
            }
            .modal-dialog-centered.modal-sm .modal-body {
                font-size: 0.9rem;
            }
            .modal-dialog-centered.modal-sm .modal-footer .btn {
                font-size: 0.8rem;
                padding: 0.25rem 0.75rem;
            }
        }
        
        /* Toast Notification Styling (Updated) */
        #toastContainer {
            position: fixed;
            top: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1090;
            width: 90%;
            max-width: 380px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .toast {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            background-color: var(--bg-light);
            color: var(--text-dark);
            /* animation: fadeInSlideUp 0.5s ease-out; */
            border: 2px solid transparent;
        }

        .dark-mode .toast {
            background-color: var(--card-dark);
            color: var(--text-light);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        /* @keyframes fadeInSlideUp {
            from { opacity: 0; transform: translateY(20px) translateX(-50%); }
            to { opacity: 1; transform: translateY(0) translateX(-50%); }
        } */

        .toast-header {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .dark-mode .toast-header {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .toast-header .btn-close {
            margin-right: -0.25rem;
            filter: invert(1);
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .toast-header .btn-close:hover {
            opacity: 1;
        }

        .dark-mode .toast-header .btn-close {
            filter: invert(0);
        }

        .toast-header .me-auto {
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .toast-body {
            padding: 1rem;
            font-size: 0.9rem;
            color: inherit;
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
        }

        /* Custom Toast Colors and Icons */
        .toast.toast-success .toast-header { background-color: #28a745; color: white; }
        .dark-mode .toast.toast-success .toast-header { background-color: #198754; }
        .toast.toast-success .toast-body { border: 2px solid #28a745; border-top: none; }
        .dark-mode .toast.toast-success .toast-body { border-color: #198754; }

        .toast.toast-info .toast-header { background-color: #17a2b8; color: white; }
        .dark-mode .toast.toast-info .toast-header { background-color: #0f687a; }
        .toast.toast-info .toast-body { border: 2px solid #17a2b8; border-top: none; }
        .dark-mode .toast.toast-info .toast-body { border-color: #0f687a; }

        .toast.toast-warning .toast-header { background-color: #ffc107; color: #343a40; }
        .dark-mode .toast.toast-warning .toast-header { background-color: #ffca2c; }
        .toast.toast-warning .toast-body { border: 2px solid #ffc107; border-top: none; }
        .dark-mode .toast.toast-warning .toast-body { border-color: #ffca2c; }

        .toast.toast-danger .toast-header { background-color: #dc3545; color: white; }
        .dark-mode .toast.toast-danger .toast-header { background-color: #990000; }
        .toast.toast-danger .toast-body { border: 2px solid #dc3545; border-top: none; }
        .dark-mode .toast.toast-danger .toast-body { border-color: #990000; }
        /* New Styles for Settings Page */
        .settings-page {
            padding: 1.5rem 0;
        }
        .settings-card {
            padding: 2.5rem;
            margin-bottom: 2rem;
            background-color: var(--bg-light);
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-radius: 1.25rem;
            transition: all 0.3s ease;
        }
        .dark-mode .settings-card {
            background-color: var(--card-dark);
            box-shadow: 0 4px 15px var(--shadow-dark-mode);
        }
        .settings-card h4 {
            font-weight: 700;
            font-size: 1.25rem; /* Mengubah ukuran font dari 2.5rem menjadi 1.25rem */
            margin-bottom: 1.5rem;
            color: var(--primary-olive);
            border-bottom: 2px solid var(--primary-olive);
            padding-bottom: 0.5rem;
            display: inline-block;
        }
        .dark-mode .settings-card h4 {
            color: #A7D129;
            border-bottom-color: #A7D129;
        }
        .settings-card .profile-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .settings-card .profile-section .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-olive);
            transition: all 0.3s ease;
            cursor: pointer; /* Menambahkan kursor pointer */
        }
        .dark-mode .settings-card .profile-section .profile-pic {
            border-color: #A7D129;
        }
        .settings-card .profile-section .profile-info {
            flex-grow: 1;
        }
        .settings-card .profile-section .profile-info h5 {
            font-weight: 600;
            font-size: 1.1rem; /* Mengubah ukuran font dari 1.25rem menjadi 1.1rem */
            margin-bottom: 0.2rem;
        }
        .settings-card .profile-section .profile-info p {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        .dark-mode .settings-card .profile-section .profile-info p {
            color: #adb5bd;
        }
        .settings-card .form-group {
            margin-bottom: 1.5rem;
        }
        .settings-card .form-label {
            font-weight: 600;
            font-size: 0.95rem; /* Mengubah ukuran font label form */
        }
        .settings-card .bg-option {
            height: 80px;
            border-radius: 0.75rem;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            background-size: cover;
            background-position: center;
        }
        .settings-card .bg-option.active,
        .settings-card .bg-option:hover {
            border-color: var(--primary-olive);
        }
        .dark-mode .settings-card .bg-option.active,
        .dark-mode .settings-card .bg-option:hover {
            border-color: #A7D129;
        }
        .settings-card .bg-option.active::after {
            content: '\f269'; /* bi-check-lg */
            font-family: 'bootstrap-icons';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 2rem;
            text-shadow: 0 0 5px rgba(0,0,0,0.5);
        }
        .bg-color-option {
            height: 80px;
            border-radius: 0.75rem;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        .bg-color-option.active, .bg-color-option:hover {
            border-color: var(--primary-olive);
        }
        .dark-mode .bg-color-option.active, .dark-mode .bg-color-option:hover {
            border-color: #A7D129;
        }
        .bg-color-option.active::after {
            content: '\f269';
            font-family: 'bootstrap-icons';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 2rem;
            text-shadow: 0 0 5px rgba(0,0,0,0.5);
        }
        .bg-color-option[data-color="solid-bg1"] { background-color: var(--color-solid-bg1); }
        .bg-color-option[data-color="solid-bg2"] { background-color: var(--color-solid-bg2); }
        .bg-color-option[data-color="solid-bg3"] { background-color: var(--color-solid-bg3); }
        .bg-color-option[data-color="solid-bg4"] { background-color: var(--color-solid-bg4); }
        /* New color options */
        .bg-color-option[data-color="solid-bg5"] { background-color: var(--color-solid-bg5); }
        .bg-color-option[data-color="solid-bg6"] { background-color: var(--color-solid-bg6); }
        .bg-color-option[data-color="solid-bg7"] { background-color: var(--color-solid-bg7); }
        .bg-color-option[data-color="solid-bg8"] { background-color: var(--color-solid-bg8); }
        
        .settings-card .bg-option[data-bg="none"] {
            background-color: var(--card-dark);
            border: 1px dashed var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .settings-card .bg-option[data-bg="none"]::after {
            content: 'Tidak ada';
            font-family: 'Inter', sans-serif;
            color: var(--text-light);
            font-size: 1rem;
            font-weight: 600;
            text-shadow: none;
        }

        .settings-card .theme-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            /* Perbaikan: Ukuran tombol mode yang lebih kecil */
            height: 60px;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            /* Perbaikan: Ukuran font yang lebih kecil */
            font-weight: 600;
            font-size: 1rem;
        }
        .settings-card .theme-toggle.light {
            background-color: var(--light-gray-card);
            color: var(--text-dark);
        }
        .settings-card .theme-toggle.dark {
            background-color: var(--dark-gray-card);
            color: var(--text-light);
        }
        .settings-card .theme-toggle.active {
            border: 3px solid var(--primary-olive);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .settings-card .theme-toggle.active i {
            /* Perbaikan: Mengurangi ukuran scale untuk ikon */
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }
        @media (max-width: 767.98px) {
            .settings-card .profile-section {
                flex-direction: column;
                text-align: center;
            }
            .settings-card { padding: 1.5rem; }
        }
        /* --- New/Improved Card View Styles --- */
        .task-card-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 0;
        }
        .task-card-item {
            background-color: var(--bg-light);
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 200px;
        }
        .dark-mode .task-card-item {
            background-color: var(--card-dark);
            box-shadow: 0 4-15px var(--shadow-dark-mode);
        }
        .task-card-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            border-top-left-radius: 1rem;
            border-bottom-left-radius: 1rem;
        }
        .task-card-item.priority-tinggi::before { background-color: var(--priority-high-light); }
        .task-card-item.priority-sedang::before { background-color: var(--priority-medium-light); }
        .task-card-item.priority-rendah::before { background-color: var(--priority-low-light); }
        .dark-mode .task-card-item.priority-tinggi::before { background-color: var(--priority-high-dark); }
        .dark-mode .task-card-item.priority-sedang::before { background-color: var(--priority-medium-dark); }
        .dark-mode .task-card-item.priority-rendah::before { background-color: var(--priority-low-dark); }
        .task-card-title {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            padding-left: 10px;
        }
        .task-card-description {
            font-size: 0.9rem;
            color: #6c757d;
            padding-left: 10px;
            margin-bottom: 1rem;
            white-space: pre-wrap;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dark-mode .task-card-description {
            color: #adb5bd;
        }
        .task-card-meta {
            margin-top: auto; /* Push meta to the bottom */
            padding-left: 10px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .dark-mode .task-card-meta {
            color: #adb5bd;
        }
        .task-card-meta .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0.5rem;
        }
        .task-card-meta .meta-item:last-child {
            margin-bottom: 0;
        }
        .task-card-actions-dropdown {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        .task-card-actions-dropdown .btn {
            border: none;
            background-color: transparent;
            color: #6c757d;
        }
        .dark-mode .task-card-actions-dropdown .btn {
            color: #adb5bd;
        }
        .task-card-actions-dropdown .dropdown-menu {
            border-radius: 0.75rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .dark-mode .task-card-actions-dropdown .dropdown-menu {
            background-color: #2a2a2a;
            border-color: var(--border-dark-mode);
        }
        .task-card-actions-dropdown .dropdown-item {
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .task-card-actions-dropdown .dropdown-item:hover {
            background-color: var(--light-gray-card);
        }
        .dark-mode .task-card-actions-dropdown .dropdown-item:hover {
            background-color: #333;
        }
        .task-card-actions-dropdown .dropdown-item.text-success:hover {
            background-color: rgba(40, 167, 69, 0.1);
        }
        .task-card-actions-dropdown .dropdown-item.text-danger:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }
        .task-card-actions-dropdown .dropdown-item.text-info:hover {
            background-color: rgba(23, 162, 184, 0.1);
        }
        /* Style baru untuk halaman About Us */
        .about-us-section p.lead {
            font-size: 1.15rem; /* Ukuran font standar */
            line-height: 1.5;
        }
        .about-us-section h2, .about-us-section h3 {
            font-weight: 700;
        }
        .about-us-section h2 {
            font-size: 1.5rem;
            color: var(--primary-olive);
            border-bottom: 2px solid var(--primary-olive);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .dark-mode .about-us-section h2 {
            color: #A7D129;
            border-bottom-color: #A7D129;
        }
        .about-us-section p {
            line-height: 1.6;
        }
        .feature-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 2rem;
            border-radius: 1rem;
            transition: all 0.3s ease;
            height: 100%;
            background-color: var(--light-gray-card);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .dark-mode .feature-card {
            background-color: #2a2a2a;
            border-color: var(--border-dark-mode);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .dark-mode .feature-card:hover {
            box-shadow: 0 8px 15px var(--shadow-dark-mode);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary-olive);
        }
        .dark-mode .feature-icon {
            color: #A7D129;
        }
        .feature-card h5 {
            font-weight: 600;
            font-size: 1.15rem;
            margin-bottom: 0.75rem;
        }
        .feature-card p {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .dark-mode .feature-card p {
            color: #adb5bd;
        }
        .about-us-card {
            padding: 2.5rem;
        }
        @media (max-width: 767.98px) {
            .about-us-section h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="<?= $theme_class ?>">

<div class="toast-container" id="toastContainer"></div>

<div class="modal fade" id="profilePicModal" tabindex="-1" aria-labelledby="profilePicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profilePicModalLabel">Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?= $profile_pic_url ?>" alt="Foto Profil" class="img-fluid rounded-circle" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus Permanen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus tugas ini secara permanen? Aksi ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" id="confirmDeleteModalBatal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteButton">Hapus Permanen</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmArchiveModal" tabindex="-1" aria-labelledby="confirmArchiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmArchiveModalLabel">Konfirmasi Arsip Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin mengarsipkan tugas ini? Anda dapat mengembalikannya nanti.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" id="confirmArchiveModalBatal">Batal</button>
                <button type="button" class="btn btn-archive btn-sm" id="confirmArchiveButton">Arsipkan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmUnarchiveModal" tabindex="-1" aria-labelledby="confirmUnarchiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmUnarchiveModalLabel">Konfirmasi Kembalikan Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin mengembalikan tugas ini dari arsip?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" id="confirmUnarchiveModalBatal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm" id="confirmUnarchiveButton">Kembalikan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editTaskCardModal" tabindex="-1" aria-labelledby="editTaskCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskCardModalLabel">Edit Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTaskCardForm" method="post" action="">
                <div class="modal-body">
                    <input type="hidden" name="task_id" id="editTaskIdCard">
                    <div class="mb-3">
                        <label for="editTaskTitleCard" class="form-label">Judul Tugas</label>
                        <input type="text" name="task_title" id="editTaskTitleCard" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTaskDescriptionCard" class="form-label">Deskripsi</label>
                        <textarea name="task_description" id="editTaskDescriptionCard" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editDeadlineCard" class="form-label">Deadline</label>
                        <input type="datetime-local" name="deadline" id="editDeadlineCard" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPriorityCard" class="form-label">Prioritas</label>
                        <select name="priority" id="editPriorityCard" class="form-select" required>
                            <option value="rendah">Rendah</option>
                            <option value="sedang">Sedang</option>
                            <option value="tinggi">Tinggi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <div class="card">
        <h1>
            <?php
            // Menentukan judul utama berdasarkan section saat ini
            switch ($current_section) {
                case 'home':
                    echo '🏠 Home';
                    break;
                case 'about':
                    echo 'ℹ️ Tentang Kami';
                    break;
                case 'tasks':
                    echo '📝 Daftar Tugas';
                    break;
                case 'statistics':
                    echo '📊 Statistik Tugas';
                    break;
                case 'archived':
                    echo '📦Arsip Tugas';
                    break;
                case 'settings':
                    echo '⚙️ Pengaturan';
                    break;
            }
            ?>
        </h1>
        <nav class="main-nav">
            <a class="nav-link <?= ($current_section == 'home') ? 'active' : '' ?>" href="<?= site_url('todo/index') ?>?section=home">Home</a>
            <a class="nav-link <?= ($current_section == 'about') ? 'active' : '' ?>" href="<?= site_url('todo/index') ?>?section=about">Tentang Kami</a>
            <a class="nav-link <?= ($current_section == 'tasks') ? 'active' : '' ?>" href="<?= site_url('todo/index') ?>?section=tasks">Daftar Tugas</a>
            <a class="nav-link <?= ($current_section == 'statistics') ? 'active' : '' ?>" href="<?= site_url('todo/index') ?>?section=statistics">Statistik Tugas</a>
            <a class="nav-link <?= ($current_section == 'archived') ? 'active' : '' ?>" href="<?= site_url('todo/index') ?>?section=archived">Arsip Tugas</a>
            <a class="nav-link <?= ($current_section == 'settings') ? 'active' : '' ?>" href="<?= site_url('todo/settings') ?>">Pengaturan</a>
        </nav>
        <hr class="mb-4">

        <?php if ($current_section == 'home'): ?>
            <section class="welcome-section">
                <h2 id="homeGreeting">Halo! Selamat Datang di List'in! 🎉</h2>
                <p id="homeTaskSummary">Kelola semua tugas Anda dengan mudah dan tingkatkan produktivitas setiap hari.</p>
                <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                    <a href="<?= site_url('todo/index') ?>?section=tasks" class="btn btn-outline-light"><i class="bi bi-list-task me-2"></i>Lihat Semua Tugas</a>
                    <a href="#quickAddTask" class="btn btn-outline-light" onclick="document.querySelector('a.nav-link[href=\'<?= site_url('todo/index') ?>?section=tasks\']').click(); return false;"><i class="bi bi-plus-circle me-2"></i>Tambah Tugas Cepat</a>
                </div>
            </section>
            <hr class="my-4">

            <h5 class="section-title">✨ Ringkasan Tugas Anda</h5>
            <div id="homeCardStatsContainer" class="row row-cols-1 row-cols-md-3 g-3 mb-4 text-center">
                <div class="col">
                    <div class="p-3 rounded-3 stats-card belum">
                        <i class="bi bi-hourglass-split stats-icon"></i>
                        <h6 class="mb-2">Belum Dimulai</h6>
                        <p class="fs-4 fw-bold" id="homeStatsBelum">0</p>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 rounded-3 stats-card progress">
                        <i class="bi bi-arrow-repeat stats-icon"></i>
                        <h6 class="mb-2">Sedang Dikerjakan</h6>
                        <p class="fs-4 fw-bold" id="homeStatsProgress">0</p>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 rounded-3 stats-card selesai">
                        <i class="bi bi-check-circle-fill stats-icon"></i>
                        <h6 class="mb-2">Selesai</h6>
                        <p class="fs-4 fw-bold" id="homeStatsSelesai">0</p>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <h5 class="section-title">🗓️ Tugas Mendekat <small class="text-muted">(Paling Penting untuk Diperhatikan)</small></h5>

            <?php if (empty($todos_home)): ?>
                <div class="alert alert-info text-center empty-state" role="alert">
                    <i class="bi bi-emoji-sunglasses me-2"></i> Tidak ada tugas mendekat deadline. Luar biasa!
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-3 g-3" id="todosHomeContainer">
                    <?php foreach ($todos_home as $todo): ?>
                        <?php
                            $deadline_class = '';
                            $deadline_icon = '';
                            $deadline_display_text = '';
                            
                            $deadline_obj = new DateTime($todo->deadline);
                            $now_date = new DateTime();
                            $interval = $now_date->diff($deadline_obj);
                            $days_diff = (int)$interval->format('%r%a');
                            
                            if ($todo->status == 'selesai') {
                                $deadline_class = 'deadline-finished-light';
                                $deadline_icon = '<i class="bi bi-check-lg me-1 text-success"></i>';
                                $deadline_display_text = date('d-m-Y H:i', strtotime($todo->deadline));
                            } elseif ($deadline_obj < $now_date) {
                                $deadline_class = 'deadline-overdue';
                                $deadline_icon = '<i class="bi bi-exclamation-circle-fill text-danger me-1"></i>';
                                $deadline_display_text = date('d-m-Y H:i', strtotime($todo->deadline)) . ' (Tugas Lewat Deadline!)';
                            } elseif ($days_diff <= 3) {
                                $deadline_class = 'deadline-soon';
                                $deadline_icon = '<i class="bi bi-hourglass-split text-warning me-1"></i>';
                                $deadline_display_text = date('d-m-Y H:i', strtotime($todo->deadline)) . ' (Segera!)';
                            } else {
                                $deadline_class = 'deadline-normal';
                                $deadline_icon = '<i class="bi bi-calendar-check text-success me-1"></i>';
                                $deadline_display_text = date('d-m-Y H:i', strtotime($todo->deadline));
                            }
                        ?>
                        <div class="col" id="task-card-<?= $todo->id ?>">
                            <div class="task-card priority-<?= strtolower($todo->priority) ?> d-flex flex-column h-100">
                                <h6 class="task-title"><?= htmlspecialchars($todo->title) ?></h6>
                                <p class="task-description"><?= nl2br(htmlspecialchars($todo->description)) ?></p>
                                <div class="task-meta">
                                    <span class="<?= $deadline_class ?>"><?= $deadline_icon ?><?= $deadline_display_text ?></span>
                                </div>
                                <div class="task-actions mt-auto">
                                    <button class="btn btn-sm btn-outline-success w-100 btn-home-selesai" data-id="<?= $todo->id ?>">
                                        <i class="bi bi-check-lg me-1"></i>Tandai Selesai
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        <?php endif; ?>

        <?php if ($current_section == 'tasks'): ?>
            <h5 class="section-title">
                <svg width="24" height="24" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#6c5ce7;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#a29bfe;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <circle cx="50" cy="50" r="40" fill="url(#grad)" />
                    <line x1="50" y1="35" x2="50" y2="65" stroke="white" stroke-width="6" stroke-linecap="round"/>
                    <line x1="35" y1="50" x2="65" y2="50" stroke="white" stroke-width="6" stroke-linecap="round"/>
                </svg>
                Tambah Tugas Baru
            </h5>
            <form id="addTaskForm" method="post" action="<?= site_url('todo/add') ?>?section=tasks&task_view=<?= htmlspecialchars($current_task_view) ?>" class="mb-5 mt-4">
                <div class="row g-3">
                    <div class="col-md-6 col-12">
                        <label for="taskTitleInput" class="form-label">Judul Tugas</label>
                        <input type="text" name="task_title" id="taskTitleInput" class="form-control" placeholder="Contoh: Tugas Individu Statistika Industri" required>
                    </div>
                    <div class="col-md-6 col-12">
                        <label for="taskDescriptionInput" class="form-label">Deskripsi Tugas</label>
                        <textarea name="task_description" id="taskDescriptionInput" class="form-control" placeholder="Contoh: Mengerjakan soal bab 3 dan 4 dari buku teks." rows="3"></textarea>
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="deadlineInput" class="form-label">Deadline</label>
                        <input type="datetime-local" name="deadline" id="deadlineInput" class="form-control" required>
                    </div>
                    <div class="col-md-3 col-12">
                        <label for="prioritySelect" class="form-label">Prioritas</label>
                        <select name="priority" id="prioritySelect" class="form-select" required>
                            <option value="rendah">Rendah</option>
                            <option value="sedang" selected>Sedang</option>
                            <option value="tinggi">Tinggi</option>
                        </select>
                    </div>
                    <div class="col-md-5 col-12 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 btn-add-task">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Tugas
                        </button>
                    </div>
                </div>
            </form>
            <hr class="my-4">

            <h5 class="section-title d-flex justify-content-between align-items-center">
                <span>📋 Daftar Semua Tugas</span>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn task-view-toggle <?= ($current_task_view == 'table') ? 'btn-olive' : 'btn-outline-olive' ?>" data-view="table">
                        <i class="bi bi-table"></i> Tabel
                    </button>
                    <button type="button" class="btn task-view-toggle <?= ($current_task_view == 'card') ? 'btn-olive' : 'btn-outline-olive' ?>" data-view="card">
                        <i class="bi bi-card-list"></i> Kartu
                    </button>
                </div>
            </h5>
            
            <div id="taskTableContainer" class="table-responsive <?= ($current_task_view == 'table') ? 'd-block' : 'd-none' ?>">
                <form method="get" action="<?= site_url('todo/index') ?>" class="mb-4" name="filter_sort_form">
                    <input type="hidden" name="section" value="tasks">
                    <input type="hidden" name="task_view" id="currentTaskViewHidden" value="<?= htmlspecialchars($current_task_view) ?>">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3 col-12">
                            <label for="filterStatus" class="form-label">
                                <i class="bi bi-funnel-fill me-1"></i> Filter Status
                            </label>
                            <select name="filter" id="filterStatus" class="form-select">
                                <option value="">Semua</option>
                                <option value="belum" <?= ($filter == 'belum') ? 'selected' : '' ?>>Belum</option>
                                <option value="progress" <?= ($filter == 'progress') ? 'selected' : '' ?>>Progress</option>
                                <option value="selesai" <?= ($filter == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <label for="filterPriority" class="form-label">
                                <i class="bi bi-filter-left me-1"></i> Prioritas
                            </label>
                            <select name="priority" id="filterPriority" class="form-select">
                                <option value="">Semua</option>
                                <option value="rendah" <?= ($priority == 'rendah') ? 'selected' : '' ?>>Rendah</option>
                                <option value="sedang" <?= ($priority == 'sedang') ? 'selected' : '' ?>>Sedang</option>
                                <option value="tinggi" <?= ($priority == 'tinggi') ? 'selected' : '' ?>>Tinggi</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <label for="searchTask" class="form-label">
                                <i class="bi bi-search me-1"></i> Cari Tugas
                            </label>
                            <input type="text" name="search" id="searchTask" class="form-control" placeholder="Contoh: Belajar..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-3 col-12">
                            <label for="sortOrder" class="form-label">
                                <i class="bi bi-sort-down me-1"></i> Urutkan Berdasarkan
                            </label>
                            <select name="sort" id="sortOrder" class="form-select">
                                <option value="">Default (Deadline & Prioritas)</option>
                                <option value="title_asc" <?= ($sort == 'title_asc') ? 'selected' : '' ?>>Judul Tugas (A-Z)</option>
                                <option value="title_desc" <?= ($sort == 'title_desc') ? 'selected' : '' ?>>Judul Tugas (Z-A)</option>
                                <option value="deadline_asc" <?= ($sort == 'deadline_asc') ? 'selected' : '' ?>>Deadline (Terdekat)</option>
                                <option value="deadline_desc" <?= ($sort == 'deadline_desc') ? 'selected' : '' ?>>Deadline (Terjauh)</option>
                                <option value="priority_high" <?= ($sort == 'priority_high') ? 'selected' : '' ?>>Prioritas (Tinggi-Rendah)</option>
                                <option value="priority_low" <?= ($sort == 'priority_low') ? 'selected' : '' ?>>Prioritas (Rendah-Tinggi)</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-check2"></i> Terapkan
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="<?= site_url('todo/index') ?>?section=tasks&task_view=<?= htmlspecialchars($current_task_view) ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
                <table class="table table-hover table-bordered align-middle text-center" id="fullTaskTable">
                    <thead>
                        <tr>
                            <th style="width: 35%;">Tugas</th>
                            <th>Deadline</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th style="width: 200px; min-width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($todos)): ?>
                        <tr>
                            <td colspan="5" class="empty-state">Tidak ada tugas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($todos as $todo): ?>
                            <tr class="table-row-priority-<?= strtolower($todo->priority) ?>">
                                <td class="task-details text-start">
                                    <strong class="table-task-title"><?= htmlspecialchars($todo->title) ?></strong>
                                    <small class="table-task-description d-block"><?= nl2br(htmlspecialchars($todo->description)) ?></small>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $deadline_class = '';
                                        $deadline_obj = new DateTime($todo->deadline);
                                        $now_date = new DateTime();
                                        $interval = $now_date->diff($deadline_obj);
                                        $days_diff = (int)$interval->format('%r%a');

                                        if ($todo->status == 'selesai') {
                                            $deadline_class = 'deadline-finished-light';
                                            echo '<span class="' . $deadline_class . '"><i class="bi bi-check-lg me-1 text-success"></i> ' . date('d-m-Y H:i', strtotime($todo->deadline)) . '</span>';
                                        } elseif ($deadline_obj < $now_date) {
                                            $deadline_class = 'deadline-overdue';
                                            echo '<span class="' . $deadline_class . '"><i class="bi bi-exclamation-circle-fill me-1"></i> ' . date('d-m-Y H:i', strtotime($todo->deadline)) . '</span>';
                                        } elseif ($days_diff <= 3) {
                                            $deadline_class = 'deadline-soon';
                                            echo '<span class="' . $deadline_class . '"><i class="bi bi-hourglass-split me-1"></i> ' . date('d-m-Y H:i', strtotime($todo->deadline)) . '</span>';
                                        } else {
                                            $deadline_class = 'deadline-normal';
                                            echo '<span class="' . $deadline_class . '"><i class="bi bi-calendar-check me-1"></i> ' . date('d-m-Y H:i', strtotime($todo->deadline)) . '</span>';
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $priorityClass = 'priority-' . strtolower($todo->priority);
                                        $priorityIcon = '';
                                        switch(strtolower($todo->priority)) {
                                            case 'tinggi': $priorityIcon = 'bi-exclamation-octagon-fill'; break;
                                            case 'sedang': $priorityIcon = 'bi-flag-fill'; break;
                                            case 'rendah': $priorityIcon = 'bi-bookmark-fill'; break;
                                        }
                                    ?>
                                    <div class="d-inline-flex align-items-center justify-content-center">
                                        <i class="bi <?= $priorityIcon ?> me-2"></i>
                                        <span class="priority-label <?= $priorityClass ?>"><?= ucfirst($todo->priority) ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $statusBadgeClass = '';
                                        $statusIcon = '';
                                        switch(strtolower($todo->status)) {
                                            case 'selesai': $statusBadgeClass = 'bg-success'; $statusIcon = 'bi-check-circle-fill'; break;
                                            case 'progress': $statusBadgeClass = 'bg-warning text-dark'; $statusIcon = 'bi-arrow-repeat'; break;
                                            default: $statusBadgeClass = 'bg-secondary'; $statusIcon = 'bi-hourglass-split'; break;
                                        }
                                    ?>
                                    <span class="badge <?= $statusBadgeClass ?> task-status-badge">
                                        <i class="bi <?= $statusIcon ?> me-1"></i>
                                        <?= ucfirst($todo->status) ?>
                                    </span>
                                </td>
                                <td class="task-actions-cell text-center">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="btn-group btn-group-sm w-100" role="group">
                                            <a href="<?= site_url('todo/set_status/'.$todo->id.'/belum') ?>?section=tasks&task_view=<?= htmlspecialchars($current_task_view) ?>" class="btn btn-outline-secondary btn-set-status" data-status-type="belum">Belum</a>
                                            <a href="<?= site_url('todo/set_status/'.$todo->id.'/progress') ?>?section=tasks&task_view=<?= htmlspecialchars($current_task_view) ?>" class="btn btn-outline-warning btn-set-status" data-status-type="progress">Progress</a>
                                            <a href="<?= site_url('todo/set_status/'.$todo->id.'/selesai') ?>?section=tasks&task_view=<?= htmlspecialchars($current_task_view) ?>" class="btn btn-outline-success btn-set-status" data-status-type="selesai">Selesai</a>
                                        </div>
                                        <button class="btn btn-sm btn-outline-edit w-100 btn-toggle-edit" type="button" data-bs-toggle="collapse" data-bs-target="#editForm-table-<?= $todo->id ?>" aria-expanded="false" aria-controls="editForm-table-<?= $todo->id ?>">
                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-archive w-100 btn-archive-task" type="button" data-id="<?= $todo->id ?>" data-bs-toggle="modal" data-bs-target="#confirmArchiveModal">
                                            <i class="bi bi-archive me-1"></i> Arsipkan
                                        </button>
                                    </div>
                                    
                                    <div class="collapse mt-2" id="editForm-table-<?= $todo->id ?>">
                                        <form action="<?= site_url('todo/edit/'.$todo->id) ?>?section=tasks&task_view=<?= htmlspecialchars($current_task_view) ?>" method="post" class="d-flex flex-column gap-1 form-inline-edit">
                                            <input type="text" name="task_title" value="<?= htmlspecialchars($todo->title) ?>" class="form-control form-control-sm" required aria-label="Edit Judul Tugas">
                                            <textarea name="task_description" class="form-control form-control-sm" rows="2" aria-label="Edit Deskripsi Tugas"><?= htmlspecialchars($todo->description) ?></textarea>
                                            <input type="datetime-local" name="deadline" value="<?= date('Y-m-d\TH:i', strtotime($todo->deadline)) ?>" class="form-control form-control-sm" required aria-label="Edit Deadline Tugas">
                                            <select name="priority" class="form-select form-select-sm" required aria-label="Edit Prioritas Tugas">
                                                <option value="rendah" <?= $todo->priority == 'rendah' ? 'selected' : '' ?>>Rendah</option>
                                                <option value="sedang" <?= $todo->priority == 'sedang' ? 'selected' : '' ?>>Sedang</option>
                                                <option value="tinggi" <?= $todo->priority == 'tinggi' ? 'selected' : '' ?>>Tinggi</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-success w-100 mt-2">
                                                <i class="bi bi-save me-1"></i> Simpan
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>

            <div id="taskCardViewContainer" class="task-card-list <?= ($current_task_view == 'card') ? 'd-block' : 'd-none' ?>">
                <form method="get" action="<?= site_url('todo/index') ?>" class="mb-4" name="filter_sort_form_card">
                    <input type="hidden" name="section" value="tasks">
                    <input type="hidden" name="task_view" id="currentTaskViewHiddenCard" value="<?= htmlspecialchars($current_task_view) ?>">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3 col-12">
                            <label for="filterStatusCard" class="form-label">
                                <i class="bi bi-funnel-fill me-1"></i> Filter Status
                            </label>
                            <select name="filter" id="filterStatusCard" class="form-select">
                                <option value="">Semua</option>
                                <option value="belum" <?= ($filter == 'belum') ? 'selected' : '' ?>>Belum</option>
                                <option value="progress" <?= ($filter == 'progress') ? 'selected' : '' ?>>Progress</option>
                                <option value="selesai" <?= ($filter == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <label for="filterPriorityCard" class="form-label">
                                <i class="bi bi-filter-left me-1"></i> Prioritas
                            </label>
                            <select name="priority" id="filterPriorityCard" class="form-select">
                                <option value="">Semua</option>
                                <option value="rendah" <?= ($priority == 'rendah') ? 'selected' : '' ?>>Rendah</option>
                                <option value="sedang" <?= ($priority == 'sedang') ? 'selected' : '' ?>>Sedang</option>
                                <option value="tinggi" <?= ($priority == 'tinggi') ? 'selected' : '' ?>>Tinggi</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-12">
                            <label for="searchTaskCard" class="form-label">
                                <i class="bi bi-search me-1"></i> Cari Tugas
                            </label>
                            <input type="text" name="search" id="searchTaskCard" class="form-control" placeholder="Contoh: Belajar..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-2 col-12">
                            <button type="submit" class="btn btn-outline-secondary w-100">Terapkan Filter</button>
                        </div>
                    </div>
                </form>
                <div class="task-card-list">
                    <?php if (empty($todos)): ?>
                        <div class="alert alert-info text-center empty-state w-100" role="alert" style="grid-column: 1 / -1;">
                            <i class="bi bi-emoji-sunglasses me-2"></i> Tidak ada tugas.
                        </div>
                    <?php else: ?>
                        <?php foreach ($todos as $todo): ?>
                            <?php
                                $deadline_class = '';
                                $deadline_icon = '';
                                $deadline_display_text = '';
                                $deadline_obj = new DateTime($todo->deadline);
                                $now_date = new DateTime();
                                $interval = $now_date->diff($deadline_obj);
                                $days_diff = (int)$interval->format('%r%a');

                                if ($todo->status == 'selesai') {
                                    $deadline_class = 'deadline-finished-light';
                                    $deadline_icon = 'bi-check-lg text-success';
                                    $deadline_display_text = date('d-m-Y H:i', strtotime($todo->deadline));
                                } elseif ($deadline_obj < $now_date) {
                                    $deadline_class = 'deadline-overdue';
                                    $deadline_icon = 'bi-exclamation-circle-fill text-danger';
                                    $deadline_display_text = date('d-m-Y H:i', strtotime($todo->deadline)) . ' (Lewat Deadline!)';
                                } elseif ($days_diff <= 3) {
                                    $deadline_class = 'deadline-soon';
                                    $deadline_icon = 'bi-hourglass-split text-warning';
                                    $deadline_display_text = date('d-m-Y H:i', strtotime($todo->deadline)) . ' (Segera!)';
                                } else {
                                    $deadline_class = 'deadline-normal';
                                    $deadline_icon = 'bi-calendar-check text-success';
                                    $deadline_display_text = date('d-m-Y H:i', strtotime($todo->deadline));
                                }
                                $priorityClass = 'priority-' . strtolower($todo->priority);
                                $priorityIcon = '';
                                switch(strtolower($todo->priority)) {
                                    case 'tinggi': $priorityIcon = 'bi-exclamation-octagon-fill'; break;
                                    case 'sedang': $priorityIcon = 'bi-flag-fill'; break;
                                    case 'rendah': $priorityIcon = 'bi-bookmark-fill'; break;
                                }
                            ?>
                            <div class="task-card-item priority-<?= strtolower($todo->priority) ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="task-card-title"><?= htmlspecialchars($todo->title) ?></h6>
                                    <div class="dropdown task-card-actions-dropdown">
                                        <button class="btn btn-sm btn-link text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><h6 class="dropdown-header">Ubah Status</h6></li>
                                            <li><a class="dropdown-item btn-set-status text-secondary" href="<?= site_url('todo/set_status/'.$todo->id.'/belum') ?>?section=tasks&task_view=card" data-status-type="belum"><i class="bi bi-hourglass-split me-2"></i>Belum</a></li>
                                            <li><a class="dropdown-item btn-set-status text-warning" href="<?= site_url('todo/set_status/'.$todo->id.'/progress') ?>?section=tasks&task_view=card" data-status-type="progress"><i class="bi bi-arrow-repeat me-2"></i>Progress</a></li>
                                            <li><a class="dropdown-item btn-set-status text-success" href="<?= site_url('todo/set_status/'.$todo->id.'/selesai') ?>?section=tasks&task_view=card" data-status-type="selesai"><i class="bi bi-check-circle-fill me-2"></i>Selesai</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-primary btn-edit-task-card" href="#" data-bs-toggle="modal" data-bs-target="#editTaskCardModal" data-id="<?= $todo->id ?>" data-title="<?= htmlspecialchars($todo->title) ?>" data-description="<?= htmlspecialchars($todo->description) ?>" data-deadline="<?= date('Y-m-d\TH:i', strtotime($todo->deadline)) ?>" data-priority="<?= strtolower($todo->priority) ?>"><i class="bi bi-pencil-square me-2"></i>Edit Tugas</a></li>
                                            <li><a class="dropdown-item text-danger btn-archive-task" href="#" data-id="<?= $todo->id ?>" data-bs-toggle="modal" data-bs-target="#confirmArchiveModal"><i class="bi bi-archive me-2"></i>Arsipkan</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="task-card-description"><?= nl2br(htmlspecialchars($todo->description)) ?></p>
                                <div class="task-card-meta">
                                    <div class="meta-item">
                                        <i class="bi <?= $deadline_icon ?>"></i>
                                        <span class="<?= $deadline_class ?>"><?= $deadline_display_text ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="bi <?= $priorityIcon ?>"></i>
                                        <span class="priority-label <?= $priorityClass ?>"><?= ucfirst($todo->priority) ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="bi bi-info-circle"></i>
                                        <span class="badge bg-<?=
                                            $todo->status == 'selesai' ? 'success' :
                                            ($todo->status == 'progress' ? 'warning text-dark' : 'secondary')
                                        ?> task-status-badge">
                                            <?= ucfirst($todo->status) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($current_section == 'statistics'): ?>
            <h5 class="section-title">📊 Gambaran Umum Status Tugas</h5>
            <div class="d-flex justify-content-center mb-3">
                <button class="btn <?= ($current_stats_view == 'card') ? 'btn-olive' : 'btn-outline-olive' ?>" id="toggleCardStats"
                        onclick="toggleStatsView('card')">Tampilan Kartu</button>
                <button class="btn <?= ($current_stats_view == 'chart') ? 'btn-olive' : 'btn-outline-olive' ?> ms-2" id="toggleChartStats"
                        onclick="toggleStatsView('chart')">Tampilan Diagram Lingkaran</button>
            </div>
            
            <div id="cardStatsContainer" class="row row-cols-1 row-cols-md-3 g-3 mb-4 text-center <?= ($current_stats_view == 'card') ? '' : 'd-none' ?>">
                <div class="col">
                    <div class="p-3 rounded-3 stats-card belum">
                        <i class="bi bi-hourglass-split stats-icon"></i>
                        <h6 class="mb-2">Tugas Belum Dimulai</h6>
                        <p class="fs-4 fw-bold" id="statsBelum">0</p>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 rounded-3 stats-card progress">
                        <i class="bi bi-arrow-repeat stats-icon"></i>
                        <h6 class="mb-2">Sedang Dikerjakan</h6>
                        <p class="fs-4 fw-bold" id="statsProgress">0</p>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 rounded-3 stats-card selesai">
                        <i class="bi bi-check-circle-fill stats-icon"></i>
                        <h6 class="mb-2">Selesai</h6>
                        <p class="fs-4 fw-bold" id="statsSelesai">0</p>
                    </div>
                </div>
            </div>

            <div id="chartStatsContainer" class="mb-4 <?= ($current_stats_view == 'chart') ? '' : 'd-none' ?>">
                <div id="doughnutChartWrapper">
                    <canvas id="taskStatusChart"></canvas>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="section-title">🌟 Tugas Berdasarkan Prioritas</h5>
            <div id="priorityCardStatsContainer" class="row row-cols-1 row-cols-md-3 g-3 mb-4 text-center">
                <div class="col">
                    <div class="p-3 rounded-3 stats-card priority-high">
                        <i class="bi bi-exclamation-octagon-fill stats-icon"></i>
                        <h6 class="mb-2">Prioritas Tinggi</h6>
                        <p class="fs-4 fw-bold" id="statsPriorityHigh">0</p>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 rounded-3 stats-card priority-medium">
                        <i class="bi bi-flag-fill stats-icon"></i>
                        <h6 class="mb-2">Prioritas Sedang</h6>
                        <p class="fs-4 fw-bold" id="statsPriorityMedium">0</p>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 rounded-3 stats-card priority-low">
                        <i class="bi bi-bookmark-fill stats-icon"></i>
                        <h6 class="mb-2">Prioritas Rendah</h6>
                        <p class="fs-4 fw-bold" id="statsPriorityLow">0</p>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">

            <h5 class="section-title">📅 Diagram Tugas Harian (7 Hari ke Depan)</h5>
            <div class="d-flex justify-content-center mb-3">
                <button class="btn daily-chart-view-toggle <?= ($current_daily_chart_view == 'status') ? 'btn-olive' : 'btn-outline-olive' ?>" data-view="status">
                    Berdasarkan Status
                </button>
                <button class="btn daily-chart-view-toggle <?= ($current_daily_chart_view == 'priority') ? 'btn-olive' : 'btn-outline-olive' ?> ms-2" data-view="priority">
                    Berdasarkan Prioritas
                </button>
            </div>
            <div id="dailyTaskChartContainer">
                <canvas id="dailyTaskChart"></canvas>
            </div>
        <?php endif; ?>

        <?php if ($current_section == 'archived'): ?>
    <h5 class="section-title"><i class="bi bi-box-seam me-2"></i> Arsip Tugas</h5>

    <?php if (empty($archived_todos)): ?>
        <div class="alert alert-info text-center empty-state" role="alert">
            <i class="bi bi-emoji-sunglasses me-2"></i> Tidak ada tugas yang diarsipkan.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="archivedTaskGrid">
            <?php foreach ($archived_todos as $todo): ?>
                <?php
                    $priority_class = 'priority-' . strtolower($todo->priority);
                    $priority_icon = '';
                    switch(strtolower($todo->priority)) {
                        case 'tinggi': $priority_icon = 'bi-exclamation-octagon-fill'; break;
                        case 'sedang': $priority_icon = 'bi-flag-fill'; break;
                        case 'rendah': $priority_icon = 'bi-bookmark-fill'; break;
                    }
                ?>
                <div class="col" id="archived-task-card-<?= $todo->id ?>">
                    <div class="card h-100 p-4 task-card <?= $priority_class ?>">
                        <div class="card-body p-0">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title fw-bold m-0 text-truncate" title="<?= htmlspecialchars($todo->title) ?>">
                                    <?= htmlspecialchars($todo->title) ?>
                                </h6>
                                <span class="badge rounded-pill bg-secondary text-white ms-2">
                                    <i class="bi bi-archive-fill me-1"></i> Diarsipkan
                                </span>
                            </div>
                            <p class="card-text text-muted mb-3" style="font-size: 0.9rem; min-height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                <?= nl2br(htmlspecialchars($todo->description)) ?>
                            </p>
                            <ul class="list-unstyled mb-3">
                                <li class="d-flex align-items-center mb-1 text-muted">
                                    <i class="bi bi-calendar-check me-2 text-primary"></i>
                                    <small>Deadline: <?= date('d-m-Y H:i', strtotime($todo->deadline)) ?></small>
                                </li>
                                <li class="d-flex align-items-center mb-1">
                                    <i class="bi <?= $priority_icon ?> me-2 <?= $priority_class ?>"></i>
                                    <small class="priority-label <?= $priority_class ?>"><?= ucfirst($todo->priority) ?></small>
                                </li>
                                <li class="d-flex align-items-center text-muted">
                                    <i class="bi bi-clock-history me-2"></i>
                                    <small>Diarsipkan: <?= isset($todo->archived_at) ? date('d-m-Y', strtotime($todo->archived_at)) : 'N/A' ?></small>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-0 d-flex flex-column gap-2">
                            <button type="button" class="btn btn-primary btn-sm btn-unarchive-task" data-bs-toggle="modal" data-bs-target="#confirmUnarchiveModal" data-id="<?= $todo->id ?>">
                                <i class="bi bi-arrow-return-left me-1"></i> Kembalikan
                            </button>
                            <button type="button" class="btn btn-danger btn-sm btn-permanent-delete-task" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?= $todo->id ?>">
                                <i class="bi bi-trash-fill me-1"></i> Hapus Permanen
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
<?php endif; ?>

        <?php if ($current_section == 'settings'): ?>
            <div class="settings-page">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card settings-card">
                            <h4><i class="bi bi-person-circle me-2"></i>Informasi Akun</h4>
                            <div class="profile-section">
                                <img src="<?= $profile_pic_url ?>" alt="Foto Profil" class="profile-pic" id="profilePic" data-bs-toggle="modal" data-bs-target="#profilePicModal">
                                <div class="profile-info">
                                    <h5 id="profileUsername"><?= htmlspecialchars($user_data->username ?? 'Pengguna') ?></h5>
                                    <p id="profileEmail"><?= htmlspecialchars($user_data->email ?? 'email@contoh.com') ?></p>
                                    <form id="uploadProfilePicForm" action="<?= site_url('todo/upload_profile_picture') ?>" method="post" enctype="multipart/form-data">
                                        <label for="profilePicInput" class="btn btn-sm btn-outline-secondary mt-2"><i class="bi bi-image me-2"></i>Ganti Foto Profil</label>
                                        <input type="file" id="profilePicInput" name="profile_picture" class="d-none" accept="image/*">
                                    </form>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="usernameInput" class="form-label">Username</label>
                                <input type="text" id="usernameInput" class="form-control" value="<?= htmlspecialchars($user_data->username ?? '') ?>">
                            </div>
                            <div class="mb-4">
                                <label for="emailInput" class="form-label">Email</label>
                                <input type="email" id="emailInput" class="form-control" value="<?= htmlspecialchars($user_data->email ?? '') ?>">
                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary btn-md" id="saveProfileButton"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
                            </div>
                        </div>

                        <div class="card settings-card">
                            <h4><i class="bi bi-person-gear me-2"></i>Aksi Akun</h4>
                            <div class="text-center">
                                <a href="<?= site_url('todo/logout') ?>" class="btn btn-danger btn-lg">
                                    <i class="bi bi-box-arrow-right me-2"></i>Keluar Akun
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card settings-card">
                            <h4><i class="bi bi-palette me-2"></i>Tampilan & Estetika</h4>
                            <div class="form-group">
                                <label class="form-label">Mode Tampilan</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="theme-toggle light" data-mode="light">
                                            <i class="bi bi-sun-fill me-2"></i>Terang
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="theme-toggle dark" data-mode="dark">
                                            <i class="bi bi-moon-fill me-2"></i>Gelap
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Latar Belakang</label>
                                
                                <h6 class="text-muted fw-bold mt-3"><i class="bi bi-images me-2"></i>Pilih Gambar</h6>
                                <div class="row g-2 mb-3">
                                    <div class="col-3">
                                        <div class="bg-option" data-bg="none"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-option" data-bg="default" style="background-image: url('<?= base_url('asset/images/cov.jpg'); ?>');"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-option" data-bg="bg1" style="background-image: url('<?= base_url('asset/images/bg1.jpg'); ?>');"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-option" data-bg="bg2" style="background-image: url('<?= base_url('asset/images/bg2.jpg'); ?>');"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-option" data-bg="bg3" style="background-image: var(--bg-image-3);"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-option" data-bg="bg4" style="background-image: var(--bg-image-4);"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-option" data-bg="bg5" style="background-image: var(--bg-image-5);"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-option" data-bg="bg6" style="background-image: var(--bg-image-6);"></div>
                                    </div>
                                </div>
                                
                                <h6 class="text-muted fw-bold"><i class="bi bi-palette-fill me-2"></i>Pilih Warna Solid</h6>
                                <div class="row g-2">
                                    <div class="col-3">
                                        <div class="bg-color-option" data-color="solid-bg1"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-color-option" data-color="solid-bg2"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-color-option" data-color="solid-bg3"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-color-option" data-color="solid-bg4"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-color-option" data-color="solid-bg5"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-color-option" data-color="solid-bg6"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-color-option" data-color="solid-bg7"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-color-option" data-color="solid-bg8"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        <?php endif; ?>
        
        <?php if ($current_section == 'about'): ?>
            <div class="about-us-section">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card about-us-card h-100 shadow-sm animate-on-scroll">
                            <h2><i class="bi bi-info-circle me-2"></i>Tentang List'in</h2>
                            <p class="lead">
                            List'in adalah asisten digital untuk membantu mengatur tugas, meningkatkan produktivitas, dan mencapai tujuan dengan lebih efisien. Kami percaya manajemen tugas seharusnya menjadi proses yang mudah dan menyenangkan.
                            </p>
                            <p class="lead">
                            Platform ini dirancang untuk memberikan kontrol penuh atas daftar tugas harian, mulai dari yang sederhana hingga yang paling kompleks. Dengan fokus pada antarmuka yang bersih dan fitur yang intuitif, List'in menjadi solusi ideal bagi siapa pun yang ingin tetap teratur dan fokus.
                            </p>
                           <div class="d-flex justify-content-center">
                                <a href="<?= site_url('todo/index') ?>?section=tasks" class="btn btn-olive mt-4 shadow-sm animate-button">
                                    <i class="bi bi-plus-circle me-2"></i>Mulai Tambah Tugas Pertama
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="card about-us-card h-100 shadow-sm animate-on-scroll">
                            <h2><i class="bi bi-stars me-2"></i>Fitur Unggulan</h2>
                            <p class="lead">
                                Temukan fitur-fitur yang membuat List'in menjadi pilihan tepat untuk produktivitas Anda.
                            </p>
                            
                            <div class="row row-cols-1 row-cols-md-3 g-3 text-center">
                                <div class="col">
                                    <div class="feature-card">
                                        <i class="bi bi-check-lg feature-icon"></i>
                                        <h5>Manajemen Tugas Intuitif</h5>
                                        <p class="mb-0">Atur tugas Anda dengan mudah, tambahkan deskripsi, dan tandai prioritas. Semua dalam satu tempat yang rapi.</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="feature-card">
                                        <i class="bi bi-speedometer2 feature-icon"></i>
                                        <h5>Statistik Produktivitas</h5>
                                        <p class="mb-0">Pantau kemajuan Anda dengan grafik dan ringkasan tugas yang komprehensif untuk tetap termotivasi.</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="feature-card">
                                        <i class="bi bi-palette-fill feature-icon"></i>
                                        <h5>Kustomisasi Tampilan</h5>
                                        <p class="mb-0">Personalisasi tampilan aplikasi Anda dengan mode terang/gelap dan berbagai pilihan latar belakang.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<script>
    // Pastikan untuk mendaftarkan plugin datalabels setelah memuatnya
    Chart.register(ChartDataLabels);

    // Menyimpan data tugas dari PHP ke JavaScript
    let allActiveTasksDataFromPHP = <?= json_encode($todos ?? []) ?>;
    let allTodosHomeDataFromPHP = <?= json_encode($todos_home ?? []) ?>;
    let allArchivedTasksDataFromPHP = <?= json_encode($archived_todos ?? []) ?>;

    const allTasksForStatsFromPHP = {
        active: <?= json_encode($all_active_tasks_for_js ?? []) ?>,
        archived: <?= json_encode($all_archived_tasks_for_js ?? []) ?>
    };

    let doughnutChartInstance;
    let dailyChartInstance;

    // Tambahkan variabel PHP untuk pengaturan awal
    const initialSettings = {
        theme: '<?= $user_settings->theme ?? 'light' ?>',
        background_type: '<?= $user_settings->background_type ?? 'image' ?>',
        background_value: '<?= $user_settings->background_value ?? 'default' ?>'
    };

    /**
     * Menampilkan notifikasi toast di tengah atas dengan desain yang lebih menarik.
     * @param {string} message Pesan yang akan ditampilkan.
     * @param {string} type Tipe notifikasi: 'success', 'info', 'warning', 'danger'.
     */
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer');
        const toastId = 'toast-' + Date.now();
        
        let iconClass;
        let titleText;

        switch(type) {
            case 'success':
                iconClass = 'bi-check-circle-fill';
                titleText = 'Berhasil!';
                break;
            case 'danger':
                iconClass = 'bi-exclamation-octagon-fill';
                titleText = 'Gagal!';
                break;
            case 'warning':
                iconClass = 'bi-exclamation-triangle-fill';
                titleText = 'Peringatan!';
                break;
            default: // info
                iconClass = 'bi-info-circle-fill';
                titleText = 'Informasi';
                break;
        }
        
        const toastHtml = `
            <div id="${toastId}" class="toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bi ${iconClass} me-2"></i>
                    <strong class="me-auto">${titleText}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        const toastEl = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastEl, {
            delay: 3000
        });
        toast.show();

        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }

    // --- UI/Theming Functions ---
    function toggleMode() {
        document.body.classList.toggle("dark-mode");
        const icon = document.querySelector('#modeToggle i');
        icon.classList.toggle("bi-moon-fill");
        icon.classList.toggle("bi-sun-fill");
        
        const isDarkMode = document.body.classList.contains('dark-mode');
        const newTheme = isDarkMode ? 'dark' : 'light';
        updateServerSettings(newTheme, initialSettings.background_type, initialSettings.background_value);
        
        if (isDarkMode) {
            document.querySelector('.theme-toggle.dark').classList.add('active');
            document.querySelector('.theme-toggle.light').classList.remove('active');
        } else {
            document.querySelector('.theme-toggle.light').classList.add('active');
            document.querySelector('.theme-toggle.dark').classList.remove('active');
        }

        if (doughnutChartInstance && document.getElementById('chartStatsContainer') && !document.getElementById('chartStatsContainer').classList.contains('d-none')) {
            updateDoughnutChartColors();
        }
        if (dailyChartInstance) {
            createDailyTaskChart(new URLSearchParams(window.location.search).get('daily_chart_view') || 'status');
        }
    }
    
    /**
     * Memperbarui pengaturan tampilan di server.
     * @param {string} theme Mode tampilan ('light' atau 'dark').
     * @param {string} bgType Jenis latar belakang ('image', 'color', atau 'none').
     * @param {string} bgValue Nilai spesifik latar belakang.
     */
    function updateServerSettings(theme, bgType, bgValue) {
        const formData = new FormData();
        formData.append('theme', theme);
        formData.append('background_type', bgType);
        formData.append('background_value', bgValue);

        fetch('<?= site_url('todo/update_settings') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Perbarui variabel JavaScript setelah sukses
                initialSettings.theme = theme;
                initialSettings.background_type = bgType;
                initialSettings.background_value = bgValue;
                showToast(data.message, 'success');
            } else {
                showToast(data.error, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan jaringan saat menyimpan pengaturan.', 'danger');
        });
    }

    function applyBackground(type, value) {
        let backgroundImage = '';
        let backgroundColor = '';
        let opacity = type === 'image' ? (document.body.classList.contains('dark-mode') ? '0.2' : '0.9') : '0';
        
        document.documentElement.style.removeProperty('--bg-image');
        document.documentElement.style.removeProperty('--bg-color');
        document.documentElement.style.removeProperty('--bg-color-dark');
        document.documentElement.style.removeProperty('--bg-opacity');
        document.documentElement.style.removeProperty('--bg-opacity-dark');

        if (type === 'image') {
            if (value === 'default') {
                backgroundImage = `url('<?= base_url('asset/images/cov.jpg'); ?>')`;
            } else if (value === 'bg1') {
                backgroundImage = `url('<?= base_url('asset/images/bg1.jpg'); ?>')`;
            } else if (value === 'bg2') {
                backgroundImage = `url('<?= base_url('asset/images/bg2.jpg'); ?>')`;
            } else if (value === 'bg3') {
                backgroundImage = `var(--bg-image-3)`;
            } else if (value === 'bg4') {
                backgroundImage = `var(--bg-image-4)`;
            } else if (value === 'bg5') {
                backgroundImage = `var(--bg-image-5)`;
            } else if (value === 'bg6') {
                backgroundImage = `var(--bg-image-6)`;
            }
            document.documentElement.style.setProperty('--bg-image', backgroundImage);
            document.documentElement.style.setProperty('--bg-opacity', opacity);
            document.documentElement.style.setProperty('--bg-opacity-dark', opacity);
        } else if (type === 'color') {
            backgroundColor = `var(--color-${value})`;
            document.documentElement.style.setProperty('--bg-color', backgroundColor);
            document.documentElement.style.setProperty('--bg-color-dark', backgroundColor);
            document.documentElement.style.setProperty('--bg-opacity', '1');
            document.documentElement.style.setProperty('--bg-opacity-dark', '1');
        } else if (value === 'none') {
            document.documentElement.style.setProperty('--bg-opacity', '0');
            document.documentElement.style.setProperty('--bg-opacity-dark', '0');
        }
    }

    // --- Data & Statistics Functions ---
    function getTaskCountsByStatus(tasks) {
        let countBelum = 0;
        let countProgress = 0;
        let countSelesai = 0;

        tasks.forEach(task => {
            const status = task.status ? task.status.toLowerCase() : '';
            if (status === 'belum') {
                countBelum++;
            } else if (status === 'progress') {
                countProgress++;
            } else if (status === 'selesai') {
                countSelesai++;
            }
        });
        return { belum: countBelum, progress: countProgress, selesai: countSelesai };
    }

    function getTaskCountsByPriority(tasks) {
        let countHigh = 0;
        let countMedium = 0;
        let countLow = 0;

        tasks.forEach(task => {
            const priority = task.priority ? task.priority.toLowerCase() : '';
            if (priority === 'tinggi') {
                countHigh++;
            } else if (priority === 'sedang') {
                countMedium++;
            } else if (priority === 'rendah') {
                countLow++;
            }
        });
        return { high: countHigh, medium: countMedium, low: countLow };
    }

    function updateHomeStatistics() {
        const counts = getTaskCountsByStatus(allTasksForStatsFromPHP.active);
        const totalSelesai = counts.selesai + allTasksForStatsFromPHP.archived.length;
        const totalTasks = counts.belum + counts.progress + counts.selesai;

        const homeStatsBelum = document.getElementById('homeStatsBelum');
        const homeStatsProgress = document.getElementById('homeStatsProgress');
        const homeStatsSelesai = document.getElementById('homeStatsSelesai');

        if (homeStatsBelum) homeStatsBelum.textContent = counts.belum;
        if (homeStatsProgress) homeStatsProgress.textContent = counts.progress;
        if (homeStatsSelesai) homeStatsSelesai.textContent = totalSelesai;

        const homeGreeting = document.getElementById('homeGreeting');
        const homeTaskSummary = document.getElementById('homeTaskSummary');
        const now = new Date();
        const hour = now.getHours();
        let greetingText;

        if (hour < 10) {
            greetingText = "Selamat Pagi";
        } else if (hour < 15) {
            greetingText = "Selamat Siang";
        } else if (hour < 18) {
            greetingText = "Selamat Sore";
        } else {
            greetingText = "Selamat Malam";
        }

        if (homeGreeting) {
            homeGreeting.innerHTML = `${greetingText}! Selamat Datang di List'in`;
        }
        if (homeTaskSummary) {
            let summaryMessage = `Anda memiliki total ${totalTasks} tugas yang tercatat.`;
            if (counts.belum > 0) {
                summaryMessage += ` Saat ini, ada ${counts.belum} tugas yang belum dimulai. Yuk, semangat selesaikan!`;
            } else if (counts.progress > 0) {
                summaryMessage += ` Ada ${counts.progress} tugas yang sedang Anda kerjakan. Terus pantau ya!`;
            } else if (totalSelesai > 0) {
                summaryMessage += ` Semua tugas sudah dimulai atau selesai! Pertahankan produktivitas Anda!`;
            } else {
                summaryMessage += ` Sepertinya tidak ada tugas aktif saat ini. Mari mulai buat tugas baru!`;
            }
            homeTaskSummary.innerHTML = summaryMessage;
        }
    }

    function updateOverallStatistics() {
        const statusCounts = getTaskCountsByStatus(allTasksForStatsFromPHP.active);
        const priorityCounts = getTaskCountsByPriority(allTasksForStatsFromPHP.active);

        const totalSelesai = statusCounts.selesai + allTasksForStatsFromPHP.archived.length;

        const statsBelum = document.getElementById('statsBelum');
        const statsProgress = document.getElementById('statsProgress');
        const statsSelesai = document.getElementById('statsSelesai');

        if (statsBelum) statsBelum.textContent = statusCounts.belum;
        if (statsProgress) statsProgress.textContent = statusCounts.progress;
        if (statsSelesai) statsSelesai.textContent = totalSelesai;

        const statsPriorityHigh = document.getElementById('statsPriorityHigh');
        const statsPriorityMedium = document.getElementById('statsPriorityMedium');
        const statsPriorityLow = document.getElementById('statsPriorityLow');

        if (statsPriorityHigh) statsPriorityHigh.textContent = priorityCounts.high;
        if (statsPriorityMedium) statsPriorityMedium.textContent = priorityCounts.medium;
        if (statsPriorityLow) statsPriorityLow.textContent = priorityCounts.low;

        if (doughnutChartInstance && document.getElementById('chartStatsContainer') && !document.getElementById('chartStatsContainer').classList.contains('d-none')) {
            updateDoughnutChartData({ belum: statusCounts.belum, progress: statusCounts.progress, selesai: totalSelesai });
        }
    }

    // --- Chart.js Specific Functions ---
    function getChartColors() {
        const isDarkMode = document.body.classList.contains('dark-mode');
        return {
            belum: isDarkMode ? '#495057' : '#e9ecef',
            progress: isDarkMode ? '#ffca2c' : '#ffc107',
            selesai: isDarkMode ? '#198754' : '#28a745',

            priorityHigh: isDarkMode ? '#ff6b6b' : '#dc3545',
            priorityMedium: isDarkMode ? '#a27dd2' : '#6f42c1',
            priorityLow: isDarkMode ? '#5bc0de' : '#17a2b8',

            fontColor: isDarkMode ? '#f5f5f5' : '#1a1a1a',
            primaryOlive: isDarkMode ? '#A7D129' : '#6B8E23',
            secondaryOlive: isDarkMode ? '#8BBF43' : '#556B2F',
            gridLineColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
        };
    }

    function createDoughnutChart() {
        const ctx = document.getElementById('taskStatusChart');
        if (!ctx) return;

        if (doughnutChartInstance) {
            doughnutChartInstance.destroy();
        }

        const counts = getTaskCountsByStatus(allTasksForStatsFromPHP.active);
        const totalSelesai = counts.selesai + allTasksForStatsFromPHP.archived.length;
        const colors = getChartColors();

        const data = {
            labels: ['Belum Dimulai', 'Sedang Dikerjakan', 'Selesai'],
            datasets: [{
                data: [counts.belum, counts.progress, totalSelesai],
                backgroundColor: [colors.belum, colors.progress, colors.selesai],
                hoverOffset: 4
            }]
        };

        const config = {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: colors.fontColor,
                            font: { family: 'Inter', size: 14 }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Distribusi Status Tugas',
                        color: colors.fontColor,
                        font: { family: 'Inter', size: 18, weight: '600' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed !== null) {
                                    label += context.parsed;
                                    const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    label += ` (${percentage}%)`;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        };
        doughnutChartInstance = new Chart(ctx, config);
    }

    function updateDoughnutChartData(counts) {
        if (doughnutChartInstance) {
            doughnutChartInstance.data.datasets[0].data = [counts.belum, counts.progress, counts.selesai];
            doughnutChartInstance.update();
        }
    }

    function updateDoughnutChartColors() {
        if (doughnutChartInstance) {
            const colors = getChartColors();
            doughnutChartInstance.data.datasets[0].backgroundColor = [colors.belum, colors.progress, colors.selesai];
            doughnutChartInstance.options.plugins.legend.labels.color = colors.fontColor;
            doughnutChartInstance.options.plugins.title.color = colors.fontColor;
            doughnutChartInstance.update();
        }
    }

    function createDailyTaskChart(viewType = 'status') {
        const ctx = document.getElementById('dailyTaskChart');
        if (!ctx) return;

        if (dailyChartInstance) {
            dailyChartInstance.destroy();
        }

        const now = new Date();
        now.setHours(0, 0, 0, 0);
        
        const labels = [];
        for (let i = 0; i < 7; i++) {
            const d = new Date(now);
            d.setDate(now.getDate() + i);
            labels.push(d.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' }));
        }

        const colors = getChartColors();
        let datasets = [];
        let chartTitle = '';

        if (viewType === 'status') {
            chartTitle = 'Jumlah Tugas Harian Berdasarkan Status';
            const dataBelum = Array(7).fill(0);
            const dataProgress = Array(7).fill(0);
            const dataSelesai = Array(7).fill(0);

            allTasksForStatsFromPHP.active.forEach(task => {
                const deadline = new Date(task.deadline);
                const deadlineDateOnly = new Date(deadline.getFullYear(), deadline.getMonth(), deadline.getDate());
                const diffDays = Math.floor((deadlineDateOnly.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));

                if (diffDays >= 0 && diffDays < 7) {
                    if (task.status === 'belum') {
                        dataBelum[diffDays]++;
                    } else if (task.status === 'progress') {
                        dataProgress[diffDays]++;
                    } else if (task.status === 'selesai') {
                        dataSelesai[diffDays]++;
                    }
                }
            });

            datasets = [
                {
                    label: 'Belum Dimulai',
                    data: dataBelum,
                    backgroundColor: colors.belum,
                    borderColor: colors.belum,
                    borderWidth: 1,
                    stack: 'Stack 0'
                },
                {
                    label: 'Sedang Dikerjakan',
                    data: dataProgress,
                    backgroundColor: colors.progress,
                    borderColor: colors.progress,
                    borderWidth: 1,
                    stack: 'Stack 0'
                },
                {
                    label: 'Selesai',
                    data: dataSelesai,
                    backgroundColor: colors.selesai,
                    borderColor: colors.selesai,
                    borderWidth: 1,
                    stack: 'Stack 0'
                }
            ];

        } else if (viewType === 'priority') {
            chartTitle = 'Jumlah Tugas Harian Berdasarkan Prioritas';
            const dataHigh = Array(7).fill(0);
            const dataMedium = Array(7).fill(0);
            const dataLow = Array(7).fill(0);

            allTasksForStatsFromPHP.active.forEach(task => {
                const deadline = new Date(task.deadline);
                const deadlineDateOnly = new Date(deadline.getFullYear(), deadline.getMonth(), deadline.getDate());
                const diffDays = Math.floor((deadlineDateOnly.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));

                if (diffDays >= 0 && diffDays < 7 && task.status !== 'selesai') {
                    if (task.priority === 'tinggi') {
                        dataHigh[diffDays]++;
                    } else if (task.priority === 'sedang') {
                        dataMedium[diffDays]++;
                    } else if (task.priority === 'rendah') {
                        dataLow[diffDays]++;
                    }
                }
            });

            datasets = [
                {
                    label: 'Prioritas Tinggi',
                    data: dataHigh,
                    backgroundColor: colors.priorityHigh,
                    borderColor: colors.priorityHigh,
                    borderWidth: 1,
                    stack: 'Stack 1'
                },
                {
                    label: 'Prioritas Sedang',
                    data: dataMedium,
                    backgroundColor: colors.priorityMedium,
                    borderColor: colors.priorityMedium,
                    borderWidth: 1,
                    stack: 'Stack 1'
                },
                {
                    label: 'Prioritas Rendah',
                    data: dataLow,
                    backgroundColor: colors.priorityLow,
                    borderColor: colors.priorityLow,
                    borderWidth: 1,
                    stack: 'Stack 1'
                }
            ];
        }

        dailyChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1.5,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: colors.fontColor,
                            font: { family: 'Inter', size: 12 }
                        }
                    },
                    title: {
                        display: true,
                        text: chartTitle,
                        color: colors.fontColor,
                        font: { family: 'Inter', size: 16, weight: '600' }
                    },
                    datalabels: {
                        display: false,
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        ticks: {
                            color: colors.fontColor,
                            font: { size: 12 }
                        },
                        grid: {
                            color: colors.gridLineColor,
                            drawBorder: false
                        },
                        offset: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            color: colors.fontColor,
                            precision: 0
                        },
                        grid: {
                            color: colors.gridLineColor,
                            drawBorder: false
                        }
                    }
                },
                barPercentage: 0.5,
                categoryPercentage: 0.9
            }
        });
    }

    function updateDailyChartColors() {
        if (dailyChartInstance) {
            const colors = getChartColors();
            dailyChartInstance.options.plugins.title.color = colors.fontColor;
            dailyChartInstance.options.plugins.legend.labels.color = colors.fontColor;
            dailyChartInstance.options.scales.x.ticks.color = colors.fontColor;
            dailyChartInstance.options.scales.x.grid.color = colors.gridLineColor;
            dailyChartInstance.options.scales.y.ticks.color = colors.fontColor;
            dailyChartInstance.options.scales.y.grid.color = colors.gridLineColor;

            const currentView = new URLSearchParams(window.location.search).get('daily_chart_view') || 'status';
            if (currentView === 'status') {
                dailyChartInstance.data.datasets[0].backgroundColor = colors.belum;
                dailyChartInstance.data.datasets[0].borderColor = colors.belum;
                dailyChartInstance.data.datasets[1].backgroundColor = colors.progress;
                dailyChartInstance.data.datasets[1].borderColor = colors.progress;
                dailyChartInstance.data.datasets[2].backgroundColor = colors.selesai;
                dailyChartInstance.data.datasets[2].borderColor = colors.selesai;
            } else if (currentView === 'priority') {
                dailyChartInstance.data.datasets[0].backgroundColor = colors.priorityHigh;
                dailyChartInstance.data.datasets[0].borderColor = colors.priorityHigh;
                dailyChartInstance.data.datasets[1].backgroundColor = colors.priorityMedium;
                dailyChartInstance.data.datasets[1].borderColor = colors.priorityMedium;
                dailyChartInstance.data.datasets[2].backgroundColor = colors.priorityLow;
                dailyChartInstance.data.datasets[2].borderColor = colors.priorityLow;
            }
            dailyChartInstance.update();
        }
    }

    function toggleStatsView(view) {
        const cardContainer = document.getElementById('cardStatsContainer');
        const chartContainer = document.getElementById('chartStatsContainer');
        const toggleCardBtn = document.getElementById('toggleCardStats');
        const toggleChartBtn = document.getElementById('toggleChartStats');

        if (view === 'card') {
            cardContainer.classList.remove('d-none');
            cardContainer.classList.add('d-block');
            chartContainer.classList.remove('d-block');
            chartContainer.classList.add('d-none');
            toggleCardBtn.classList.add('btn-olive');
            toggleCardBtn.classList.remove('btn-outline-olive');
            toggleChartBtn.classList.remove('btn-olive');
            toggleChartBtn.classList.add('btn-outline-olive');
            if (doughnutChartInstance) doughnutChartInstance.destroy();
        } else if (view === 'chart') {
            cardContainer.classList.remove('d-block');
            cardContainer.classList.add('d-none');
            chartContainer.classList.remove('d-none');
            chartContainer.classList.add('d-block');
            toggleCardBtn.classList.remove('btn-olive');
            toggleCardBtn.classList.add('btn-outline-olive');
            toggleChartBtn.classList.add('btn-olive');
            toggleChartBtn.classList.remove('btn-outline-olive');
            createDoughnutChart();
        }
        const url = new URL(window.location.href);
        url.searchParams.set('stats_view', view);
        window.history.replaceState({}, '', url.toString());
    }

    function toggleDailyChartType(type) {
        createDailyTaskChart(type);

        const toggleButtons = document.querySelectorAll('.daily-chart-view-toggle');
        toggleButtons.forEach(btn => {
            if (btn.dataset.view === type) {
                btn.classList.add('btn-olive');
                btn.classList.remove('btn-outline-olive');
            } else {
                btn.classList.remove('btn-olive');
                btn.classList.add('btn-outline-olive');
            }
        });
        const url = new URL(window.location.href);
        url.searchParams.set('daily_chart_view', type);
        window.history.replaceState({}, '', url.toString());
    }

    function toggleTaskView(view) {
        const tableView = document.getElementById('taskTableContainer');
        const cardView = document.getElementById('taskCardViewContainer');
        const viewToggleButtons = document.querySelectorAll('.task-view-toggle');

        if (view === 'table') {
            tableView.classList.remove('d-none');
            tableView.classList.add('d-block');
            cardView.classList.remove('d-block');
            cardView.classList.add('d-none');
        } else {
            cardView.classList.remove('d-none');
            cardView.classList.add('d-block');
            tableView.classList.remove('d-block');
            tableView.classList.add('d-none');
        }
        
        viewToggleButtons.forEach(button => {
            if (button.getAttribute('data-view') === view) {
                button.classList.add('btn-olive');
                button.classList.remove('btn-outline-olive');
            } else {
                button.classList.remove('btn-olive');
                button.classList.add('btn-outline-olive');
            }
        });
        localStorage.setItem('current_task_view', view);
        const url = new URL(window.location.href);
        url.searchParams.set('task_view', view);
        window.history.replaceState({}, '', url.toString());
    }

    // New function to handle settings page interactions
    function setupSettingsListeners() {
        const lightThemeBtn = document.querySelector('.theme-toggle.light');
        const darkThemeBtn = document.querySelector('.theme-toggle.dark');
        const bgOptions = document.querySelectorAll('.bg-option');
        const bgColorOptions = document.querySelectorAll('.bg-color-option');
        const profilePicInput = document.getElementById('profilePicInput');
        const profilePic = document.getElementById('profilePic');
        const saveProfileButton = document.getElementById('saveProfileButton');
        const usernameInput = document.getElementById('usernameInput');
        const emailInput = document.getElementById('emailInput');

        // Set initial active state for theme buttons based on database data
        if (initialSettings.theme === 'dark') {
            darkThemeBtn.classList.add('active');
            lightThemeBtn.classList.remove('active');
        } else {
            lightThemeBtn.classList.add('active');
            darkThemeBtn.classList.remove('active');
        }

        // Apply saved background on load based on database data
        if (initialSettings.background_type && initialSettings.background_value) {
            let activeElement;
            if (initialSettings.background_type === 'image') {
                activeElement = document.querySelector(`.bg-option[data-bg="${initialSettings.background_value}"]`);
            } else if (initialSettings.background_type === 'color') {
                activeElement = document.querySelector(`.bg-color-option[data-color="${initialSettings.background_value}"]`);
            }
            if (activeElement) {
                document.querySelectorAll('.bg-option, .bg-color-option').forEach(btn => btn.classList.remove('active'));
                activeElement.classList.add('active');
            }
            applyBackground(initialSettings.background_type, initialSettings.background_value);
        }

        // Handle theme change buttons
        lightThemeBtn.addEventListener('click', () => {
            document.body.classList.remove('dark-mode');
            lightThemeBtn.classList.add('active');
            darkThemeBtn.classList.remove('active');
            updateServerSettings('light', initialSettings.background_type, initialSettings.background_value);
        });

        darkThemeBtn.addEventListener('click', () => {
            document.body.classList.add('dark-mode');
            darkThemeBtn.classList.add('active');
            lightThemeBtn.classList.remove('active');
            updateServerSettings('dark', initialSettings.background_type, initialSettings.background_value);
        });
        
        // Handle background image change
        bgOptions.forEach(option => {
            option.addEventListener('click', () => {
                const bgValue = option.dataset.bg;
                
                document.querySelectorAll('.bg-option, .bg-color-option').forEach(btn => btn.classList.remove('active'));
                
                option.classList.add('active');
                
                let currentTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
                
                if (bgValue === 'none') {
                    updateServerSettings(currentTheme, 'none', 'none');
                    applyBackground('none', 'none');
                } else {
                    updateServerSettings(currentTheme, 'image', bgValue);
                    applyBackground('image', bgValue);
                }
            });
        });

        // Handle background color change
        bgColorOptions.forEach(option => {
            option.addEventListener('click', () => {
                const colorValue = option.dataset.color;
                
                document.querySelectorAll('.bg-option, .bg-color-option').forEach(btn => btn.classList.remove('active'));
                
                option.classList.add('active');
                
                let currentTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
                updateServerSettings(currentTheme, 'color', colorValue);
                applyBackground('color', colorValue);
            });
        });
        
        // Profile picture upload handler
        profilePicInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const formData = new FormData();
                formData.append('profile_picture', this.files[0]);

                fetch('<?= site_url('todo/upload_profile_picture') ?>', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        profilePic.src = `<?= base_url('asset/images/profiles/') ?>${data.file_name}?t=${new Date().getTime()}`;
                        showToast('Foto profil berhasil diperbarui!', 'success');
                    } else {
                        showToast(`Gagal mengunggah foto: ${data.error}`, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan jaringan.', 'danger');
                });
            }
        });

        // Save profile data handler
        saveProfileButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const newUsername = usernameInput.value;
            const newEmail = emailInput.value;

            // Validasi sederhana di sisi klien
            if (!newUsername.trim() || !newEmail.trim()) {
                showToast('Username dan Email tidak boleh kosong.', 'danger');
                return;
            }

            const formData = new FormData();
            formData.append('username', newUsername);
            formData.append('email', newEmail);

            fetch('<?= site_url('todo/update_profile') ?>', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Informasi profil berhasil diperbarui!', 'success');
                    document.getElementById('profileUsername').textContent = newUsername;
                    document.getElementById('profileEmail').textContent = newEmail;
                } else {
                    let errorMessage = data.error.replace(/<p>|<\/p>/g, '');
                    showToast(`Gagal memperbarui profil: ${errorMessage}`, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan jaringan.', 'danger');
            });
        });
    }

    // --- Initial Setup on DOMContentLoaded ---
    document.addEventListener('DOMContentLoaded', () => {
        // Apply saved theme and background from database on load
        if (initialSettings.theme === 'dark') {
            document.body.classList.add('dark-mode');
            const modeToggleIcon = document.querySelector('#modeToggle i');
            if (modeToggleIcon) {
                modeToggleIcon.classList.remove("bi-moon-fill");
                modeToggleIcon.classList.add("bi-sun-fill");
            }
        }
        applyBackground(initialSettings.background_type, initialSettings.background_value);
        
        const currentSection = '<?= $current_section ?>';
        const currentStatsView = '<?= $current_stats_view ?? "card" ?>';
        const currentDailyChartView = '<?= $current_daily_chart_view ?? "status" ?>';
        let currentTaskView = localStorage.getItem('current_task_view') || '<?= $current_task_view ?? "table" ?>';


        if (currentSection === 'home') {
            updateHomeStatistics();
        }
        if (currentSection === 'statistics') {
            updateOverallStatistics();
            createDailyTaskChart(currentDailyChartView);
            if (currentStatsView === 'card') {
                document.getElementById('cardStatsContainer').classList.remove('d-none');
                document.getElementById('chartStatsContainer').classList.add('d-none');
                if (doughnutChartInstance) doughnutChartInstance.destroy();
            } else if (currentStatsView === 'chart') {
                document.getElementById('cardStatsContainer').classList.add('d-none');
                document.getElementById('chartStatsContainer').classList.remove('d-none');
                createDoughnutChart();
            }

            document.querySelectorAll('.daily-chart-view-toggle').forEach(button => {
                button.addEventListener('click', function() {
                    const viewType = this.dataset.view;
                    toggleDailyChartType(viewType);
                });
            });
        }
        
        if (currentSection === 'tasks') {
            toggleTaskView(currentTaskView);

            document.querySelectorAll('.task-view-toggle').forEach(button => {
                button.addEventListener('click', function() {
                    const viewType = this.getAttribute('data-view');
                    toggleTaskView(viewType);
                });
            });

            document.getElementById('addTaskForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    } else {
                        throw new Error('Terjadi kesalahan saat menambahkan tugas.');
                    }
                })
                .then(text => {
                    if (text.includes('redirect')) {
                            showToast('Tugas berhasil ditambahkan!', 'success');
                            setTimeout(() => { window.location.reload(); }, 1000);
                    } else {
                            throw new Error('Gagal menambahkan tugas. Coba lagi.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message, 'danger');
                });
            });

            document.querySelectorAll('form.form-inline-edit').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const form = this;
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.text();
                        } else {
                            throw new Error('Gagal memperbarui tugas.');
                        }
                    })
                    .then(text => {
                        if (text.includes('redirect')) {
                            showToast('Tugas berhasil diperbarui!', 'success');
                            setTimeout(() => { window.location.reload(); }, 1000);
                        } else {
                            throw new Error('Gagal memperbarui tugas. Coba lagi.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast(error.message, 'danger');
                    });
                });
            });
            
            document.querySelectorAll('.btn-set-status').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const url = e.target.href;
                    const statusType = e.target.dataset.statusType;
                    fetch(url)
                        .then(response => {
                            if (response.ok) {
                                if (statusType === 'belum') {
                                    showToast('Status tugas diubah menjadi Belum!', 'info');
                                } else if (statusType === 'progress') {
                                    showToast('Status tugas diubah menjadi Sedang Dikerjakan!', 'warning');
                                } else if (statusType === 'selesai') {
                                    showToast('Tugas berhasil diselesaikan!', 'success');
                                }
                                setTimeout(() => { window.location.reload(); }, 1000);
                            } else {
                                throw new Error('Gagal memperbarui status tugas.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast(error.message, 'danger');
                        });
                });
            });
            
            // Event listener untuk tombol "Arsipkan" yang memicu modal
            document.querySelectorAll('.btn-archive-task').forEach(button => {
                button.addEventListener('click', function(e) {
                    const taskId = this.getAttribute('data-id');
                    const archiveUrl = `<?= site_url('todo/archive/') ?>${taskId}?section=tasks&task_view=<?= htmlspecialchars($current_task_view) ?>`;
                    const confirmButton = document.getElementById('confirmArchiveButton');

                    // Simpan URL ke tombol konfirmasi
                    confirmButton.dataset.actionUrl = archiveUrl;
                });
            });

            // Event listener untuk tombol "Arsipkan" di dalam modal
            document.getElementById('confirmArchiveButton').addEventListener('click', function(e) {
                e.preventDefault();
                const archiveUrl = this.dataset.actionUrl;
                
                const archiveModal = bootstrap.Modal.getInstance(document.getElementById('confirmArchiveModal'));
                archiveModal.hide();
                
                fetch(archiveUrl)
                    .then(response => {
                        if (response.ok) {
                            showToast('Tugas berhasil diarsipkan!', 'warning');
                            setTimeout(() => { window.location.reload(); }, 1000);
                        } else {
                            throw new Error('Gagal mengarsipkan tugas.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast(error.message, 'danger');
                    });
            });

            // Event listener untuk tombol Edit di Card View
            document.querySelectorAll('.btn-edit-task-card').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-id');
                    const taskTitle = this.getAttribute('data-title');
                    const taskDescription = this.getAttribute('data-description');
                    const taskDeadline = this.getAttribute('data-deadline');
                    const taskPriority = this.getAttribute('data-priority');

                    const form = document.getElementById('editTaskCardForm');
                    const modal = document.getElementById('editTaskCardModal');

                    document.getElementById('editTaskIdCard').value = taskId;
                    document.getElementById('editTaskTitleCard').value = taskTitle;
                    document.getElementById('editTaskDescriptionCard').value = taskDescription;
                    document.getElementById('editDeadlineCard').value = taskDeadline;
                    document.getElementById('editPriorityCard').value = taskPriority;

                    form.action = `<?= site_url('todo/edit/') ?>${taskId}?section=tasks&task_view=card`;
                    
                    const editModal = new bootstrap.Modal(modal);
                    editModal.show();
                });
            });

            // Handle submit form edit di modal
            document.getElementById('editTaskCardForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const taskId = document.getElementById('editTaskIdCard').value;

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    } else {
                        throw new Error('Gagal memperbarui tugas.');
                    }
                })
                .then(() => {
                    showToast('Tugas berhasil diperbarui!', 'success');
                    setTimeout(() => { window.location.reload(); }, 1000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message, 'danger');
                });
            });
        }
        
        const flashMessage = `<?= htmlspecialchars($flash_message ?? '') ?>`;
        const flashType = `<?= htmlspecialchars($flash_type ?? '') ?>`;
        if (flashMessage) {
            showToast(flashMessage, flashType);
        }
        
        const datetimeLocalInputs = document.querySelectorAll('input[type="datetime-local"]');
        datetimeLocalInputs.forEach(input => {
            if (!input.value) {
                const now = new Date();
                const year = now.getFullYear();
                const month = (now.getMonth() + 1).toString().padStart(2, '0');
                const day = now.getDate().toString().padStart(2, '0');
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                input.value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        });

        document.querySelectorAll('.btn-permanent-delete-task').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const taskId = this.getAttribute('data-id');
                const deleteUrl = `<?= site_url('todo/permanent_delete/') ?>${taskId}?section=archived`;
                const confirmButton = document.getElementById('confirmDeleteButton');
                confirmButton.onclick = function() {
                    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
                    deleteModal.hide();
                    fetch(deleteUrl)
                        .then(response => {
                            if (response.ok) {
                                showToast('Tugas berhasil dihapus permanen!', 'danger');
                                setTimeout(() => { window.location.reload(); }, 1000);
                            } else {
                                throw new Error('Gagal menghapus tugas.');
                            }
                        })
                        .catch(error => {
                            showToast(error.message, 'danger');
                        });
                };
                const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                deleteModal.show();
            });
        });


        document.querySelectorAll('.btn-unarchive-task').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const taskId = this.getAttribute('data-id');
                const unarchiveUrl = `<?= site_url('todo/unarchive/') ?>${taskId}?section=archived`;
                const confirmButton = document.getElementById('confirmUnarchiveButton');
                confirmButton.onclick = function() {
                    const unarchiveModal = bootstrap.Modal.getInstance(document.getElementById('confirmUnarchiveModal'));
                    unarchiveModal.hide();
                    fetch(unarchiveUrl)
                        .then(response => {
                            if (response.ok) {
                                showToast('Tugas berhasil dikembalikan!', 'info');
                                setTimeout(() => { window.location.reload(); }, 1000);
                            } else {
                                throw new Error('Gagal mengembalikan tugas.');
                            }
                        })
                        .catch(error => {
                            showToast(error.message, 'danger');
                        });
                };
                const unarchiveModal = new bootstrap.Modal(document.getElementById('confirmUnarchiveModal'));
                unarchiveModal.show();
            });
        });

        document.querySelectorAll('.btn-home-selesai').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const taskId = this.getAttribute('data-id');
                const taskCard = document.getElementById('task-card-' + taskId);
                const actionUrl = `<?= site_url('todo/set_status/') ?>${taskId}/selesai?section=home`;

                fetch(actionUrl)
                .then(response => {
                    if (response.ok) {
                        showToast('Tugas berhasil ditandai selesai!', 'success');
                        if (taskCard) {
                            taskCard.remove();
                        }
                        const todosHomeContainer = document.getElementById('todosHomeContainer');
                        if (todosHomeContainer && todosHomeContainer.children.length === 0) {
                            const emptyStateHtml = `
                                <div class="alert alert-info text-center empty-state" role="alert">
                                    <i class="bi bi-emoji-sunglasses me-2"></i> Tidak ada tugas mendekat deadline. Luar biasa!
                                </div>
                            `;
                            todosHomeContainer.innerHTML = emptyStateHtml;
                        }
                    } else {
                        throw new Error('Terjadi kesalahan saat memperbarui status tugas.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message, 'danger');
                });
            });
        });

        // Initialize settings page listeners if on settings page
        if (currentSection === 'settings') {
            setupSettingsListeners();
        }

        // Add event listeners for the "Batal" buttons to reload the page
        const batalButtons = document.querySelectorAll('#confirmDeleteModalBatal, #confirmUnarchiveModalBatal, #confirmArchiveModalBatal');
        batalButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-bs-target');
                const modal = new bootstrap.Modal(document.querySelector(modalId));
                modal.hide();
            });
        });
    });
</script>

</body>
</html>