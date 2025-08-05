<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
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
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            padding: 2rem;
            transition: all 0.4s ease-in-out;
            background-image: url("<?= base_url('asset/images/cov.jpg'); ?>");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .bg-overlay {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .dark-mode {
            background-color: var(--bg-dark);
            color: var(--text-light);
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
        h1 {
            font-weight: 700;
            font-size: 2.25rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .section-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        .form-control, .form-select {
            border-radius: 0.75rem;
        }
        .btn {
            border-radius: 0.75rem;
            transition: 0.2s ease;
        }
        .btn:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }
        .task-status-badge {
            border-radius: 1rem;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            text-transform: capitalize;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }
        .dark-mode .table-hover tbody tr:hover {
            background-color: #2a2a2a;
        }
        .table thead {
            background-color: #f0f0f0;
        }
        .dark-mode .table thead {
            background-color: #1f1f1f;
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
        }
        .form-inline-edit input {
            font-size: 0.85rem;
        }
        @media (max-width: 576px) {
            .btn-group {
                flex-direction: column;
            }
            .btn-group .btn {
                margin-bottom: 0.25rem;
            }
            .toggle-mode {
                right: 15px;
                top: 15px;
            }
        }
        .dark-mode input,
        .dark-mode select,
        .dark-mode textarea {
            background-color: #2a2a2a;
            color: var(--text-light);
            border-color: #444;
        }
        .dark-mode input::placeholder {
            color: #aaa;
        }
        .dark-mode .form-control:focus,
        .dark-mode .form-select:focus {
            background-color: #2a2a2a;
            color: var(--text-light);
            border-color: #666;
            box-shadow: none;
        }
        .dark-mode table {
            background-color: var(--card-dark);
            color: var(--text-light);
        }
        .dark-mode .table th,
        .dark-mode .table td {
            background-color: var(--card-dark);
            color: var(--text-light);
            border-color: #333;
        }
        .dark-mode .table-bordered {
            border-color: #444;
        }
        .priority-label {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.35em 0.75em;
            border-radius: 0.75rem;
            display: inline-block;
        }
        .priority-tinggi {
            background-color: #dc3545; /* Merah */
            color: #fff;
        }
        .priority-sedang {
            background-color: #6f42c1; /* Ungu */
            color: #fff;
        }
        .priority-rendah {
            background-color: #17a2b8; /* Biru Muda */
            color: #fff;
        }

        /* Gaya tambahan untuk statistik */
        .stats-card {
            border: none !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            padding: 1rem !important;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .dark-mode .stats-card {
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .dark-mode .stats-card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        .stats-card .stats-icon {
            font-size: 3rem;
            position: absolute;
            bottom: -10px;
            right: -10px;
            opacity: 0.1;
            color: inherit;
            transform: rotate(-15deg);
        }
        .stats-card h6 {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.4rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .stats-card p {
            font-size: 2rem;
            line-height: 1;
            margin-bottom: 0;
        }

        /* Warna khusus untuk setiap kartu statistik */
        .stats-card.belum {
            background-color: #ced4da;
            color: #343a40;
        }
        .dark-mode .stats-card.belum {
            background-color: #6c757d;
            color: #f8f9fa;
        }

        .stats-card.progress {
            background-color: #ffc107;
            color: #343a40;
        }
        .dark-mode .stats-card.progress {
            background-color: #ffca2c;
            color: #1a1a1a;
        }

        .stats-card.selesai {
            background-color: #28a745;
            color: #ffffff;
        }
        .dark-mode .stats-card.selesai {
            background-color: #198754;
            color: #ffffff;
        }

        /* Gaya untuk chart */
        #chartContainer {
            max-width: 600px;
            margin: 0 auto;
            padding: 1rem;
            background-color: var(--bg-light);
            border-radius: 1.25rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .dark-mode #chartContainer {
            background-color: var(--card-dark);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        /* Gaya Tombol Olive */
        .btn-olive {
            color: #fff;
            background-color: #6B8E23; /* Darker Olive Green */
            border-color: #6B8E23;
        }
        .btn-olive:hover {
            color: #fff;
            background-color: #556B2F; /* Even darker on hover */
            border-color: #556B2F;
        }
        .btn-check:checked + .btn-olive, .btn-olive:active, .btn-olive.active, .show > .btn-olive.dropdown-toggle {
            color: #fff;
            background-color: #556B2F;
            border-color: #556B2F;
        }
        .btn-outline-olive {
            color: #6B8E23;
            border-color: #6B8E23;
        }
        .btn-outline-olive:hover {
            color: #fff;
            background-color: #6B8E23;
            border-color: #6B8E23;
        }
        .btn-check:checked + .btn-outline-olive, .btn-outline-olive:active, .btn-outline-olive.active, .btn-outline-olive.dropdown-toggle.show {
            color: #fff;
            background-color: #6B8E23;
            border-color: #6B8E23;
        }

        /* Media queries untuk responsivitas pada layar yang lebih kecil */
        @media (max-width: 991.98px) {
            .stats-card h6 {
                font-size: 0.8rem;
            }
            .stats-card p {
                font-size: 1.8rem;
            }
            .stats-card .stats-icon {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 767.98px) {
            .stats-card {
                padding: 0.7rem !important;
            }
            .stats-card h6 {
                font-size: 0.7rem;
            }
            .stats-card p {
                font-size: 1.6rem;
            }
            .stats-card .stats-icon {
                font-size: 2rem;
                bottom: -5px;
                right: -5px;
            }
            .row > .col-sm-6.mb-2 {
                flex: 0 0 auto;
                width: 50%;
            }
        }

        @media (max-width: 575.98px) {
            .stats-card h6 {
                font-size: 0.65rem;
            }
            .stats-card p {
                font-size: 1.4rem;
            }
            .stats-card .stats-icon {
                font-size: 1.8rem;
            }
             .row > .col-sm-6.mb-2 {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h1>📝 To-Do List</h1>
        <nav class="nav nav-pills nav-fill mb-4">
            <a class="nav-link active" aria-current="page" href="<?= site_url('todo/home') ?>">Home</a>
            <a class="nav-link" href="<?= site_url('todo/tasks') ?>">Daftar Tugas</a>
            <a class="nav-link" href="<?= site_url('todo/statistics') ?>">Statistik Tugas</a>
        </nav>
        <hr>

        ```

### 2. Buat File `application/views/template/footer.php`

File ini akan berisi penutup `div.card`, `div.container`, dan tag `</body>`, serta semua JavaScript yang digunakan.

```php
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    </div></div><script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    // Fungsi umum yang digunakan di beberapa halaman
    function toggleMode() {
        document.body.classList.toggle("dark-mode");
        const icon = document.querySelector('#modeToggle i');
        icon.classList.toggle("bi-moon-fill");
        icon.classList.toggle("bi-sun-fill");
        // Jika ada chart di halaman, update warnanya
        if (typeof myChart !== 'undefined' && myChart) { // Check if myChart variable exists and is not null
            updateChartColors();
        }
    }

    // Fungsi pengingat notifikasi (akan diintegrasikan di home_view atau di footer jika ingin global)
    function requestNotificationPermission() {
        if (!("Notification" in window)) {
            console.log("Browser ini tidak mendukung notifikasi desktop.");
        } else if (Notification.permission === "granted") {
            console.log("Izin notifikasi sudah diberikan.");
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(function (permission) {
                if (permission === "granted") {
                    console.log("Izin notifikasi diberikan.");
                } else {
                    console.log("Izin notifikasi ditolak.");
                }
            });
        }
    }

    function setupTaskReminders() {
        const now = new Date();
        // Pastikan taskRows hanya diambil dari tabel tugas aktif jika ada
        const taskRows = document.querySelectorAll('#taskTable tbody tr'); // Asumsi tabel tugas punya ID 'taskTable'
        const REMINDER_THRESHOLD_MS = 15 * 60 * 1000; 

        taskRows.forEach(row => {
            if (row.querySelector('.empty-state') || row.children.length < 5) {
                return;
            }

            const taskName = row.children[0].textContent;
            const deadlineText = row.children[1].textContent;
            const statusBadge = row.querySelector('.task-status-badge');
            const status = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';

            if (status === 'selesai') {
                return;
            }

            const parts = deadlineText.match(/(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2})/);
            if (parts) {
                const deadline = new Date(parts[3], parts[2] - 1, parts[1], parts[4], parts[5]);
                const timeUntilDeadline = deadline.getTime() - now.getTime();

                if (timeUntilDeadline > 0 && timeUntilDeadline <= REMINDER_THRESHOLD_MS) {
                    setTimeout(() => {
                        if (Notification.permission === "granted") {
                            new Notification("🔔 Pengingat Tugas!", {
                                body: `Tugas "${taskName}" akan jatuh tempo pada ${deadlineText}.`,
                                icon: "https://cdn-icons-png.flaticon.com/512/2805/2805364.png"
                            });
                        }
                    }, timeUntilDeadline);
                }
            }
        });
    }

    // Fungsi untuk statistik (akan dipanggil di statistik_view)
    function getTaskCounts() {
        let countBelum = 0;
        let countProgress = 0;
        let countSelesai = 0;

        const taskRows = document.querySelectorAll('table tbody tr'); // Ini akan mencari di semua tabel

        taskRows.forEach(row => {
            if (row.querySelector('.empty-state') || row.children.length < 5) {
                return;
            }

            const statusBadge = row.querySelector('.task-status-badge');
            if (statusBadge) {
                const status = statusBadge.textContent.trim().toLowerCase();
                if (status === 'belum') {
                    countBelum++;
                } else if (status === 'progress') {
                    countProgress++;
                } else if (status === 'selesai') {
                    countSelesai++;
                }
            }
        });
        return { belum: countBelum, progress: countProgress, selesai: countSelesai };
    }

    // Fungsi updateTaskStatistics hanya untuk kartu (angka)
    function updateCardStatistics() {
        const counts = getTaskCounts();
        const statsBelum = document.getElementById('statsBelum');
        const statsProgress = document.getElementById('statsProgress');
        const statsSelesai = document.getElementById('statsSelesai');
        
        if (statsBelum) statsBelum.textContent = counts.belum;
        if (statsProgress) statsProgress.textContent = counts.progress;
        if (statsSelesai) statsSelesai.textContent = counts.selesai;
    }

    // Chart-related functions, will be specific to statistics_view
    let myChart; // Make it global so it can be managed by toggleMode

    function getChartColors() {
        const isDarkMode = document.body.classList.contains('dark-mode');
        return {
            belum: isDarkMode ? '#6c757d' : '#ced4da',
            progress: isDarkMode ? '#ffca2c' : '#ffc107',
            selesai: isDarkMode ? '#198754' : '#28a745',
            fontColor: isDarkMode ? '#f5f5f5' : '#1a1a1a'
        };
    }

    function createChart() {
        const ctx = document.getElementById('taskStatusChart');
        if (!ctx) return; // Ensure canvas exists

        const counts = getTaskCounts();
        const colors = getChartColors();

        const data = {
            labels: ['Belum Dimulai', 'Sedang Dikerjakan', 'Selesai'],
            datasets: [{
                data: [counts.belum, counts.progress, counts.selesai],
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
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: colors.fontColor,
                            font: {
                                family: 'Inter',
                                size: 14
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Distribusi Status Tugas',
                        color: colors.fontColor,
                        font: {
                            family: 'Inter',
                            size: 18,
                            weight: '600'
                        }
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

        myChart = new Chart(ctx, config);
    }

    function updateChartData(counts) {
        if (myChart) {
            myChart.data.datasets[0].data = [counts.belum, counts.progress, counts.selesai];
            myChart.update();
        }
    }

    function updateChartColors() {
        if (myChart) {
            const colors = getChartColors();
            myChart.data.datasets[0].backgroundColor = [colors.belum, colors.progress, colors.selesai];
            myChart.options.plugins.legend.labels.color = colors.fontColor;
            myChart.options.plugins.title.color = colors.fontColor;
            myChart.update();
        }
    }

    // Common DOMContentLoaded logic for all views (if needed)
    document.addEventListener('DOMContentLoaded', () => {
        // This runs on every page. Specific JS for each page will be in its own view.
        // For notifications, it needs to run on the page with the tasks (Home/Daftar Tugas).
        // For stats update, it runs when the stats view is loaded.
    });

</script>

</body>
</html>