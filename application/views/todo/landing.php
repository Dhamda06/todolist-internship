<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang di List'in</title>
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
            --gradient-secondary: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            --shadow-light: 0 12px 25px rgba(0,0,0,0.1);
            --shadow-dark: 0 12px 25px rgba(0,0,0,0.3);
            /* Gradasi latar belakang body */
            --body-bg-gradient: linear-gradient(to bottom right, #e8f5e9, #c8e6c9, #e8f5e9);
            --dark-body-bg-gradient: linear-gradient(to bottom right, #2c3e50, #4a677a, #2c3e50);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            padding: 2rem;
            transition: background-color 0.4s ease-in-out, color 0.4s ease-in-out;
            
            /* Perbaikan di sini */
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
        
        .landing-container {
            max-width: 900px;
            width: 100%;
            padding: 3.5rem;
            border-radius: 1.5rem;
            background-color: #ffffff;
            box-shadow: var(--shadow-light);
            animation: fadeIn 1s ease-out;
            position: relative;
            z-index: 10;
        }
        
        .dark-mode .landing-container {
            background-color: #2e3a47;
            color: var(--text-light);
            box-shadow: var(--shadow-dark);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            font-size: 4rem;
            font-weight: 800;
            color: transparent;
            background-image: var(--gradient-main);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            animation: bounceIn 1.2s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.1); opacity: 1; }
            75% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }

        h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--text-dark);
        }
        
        .dark-mode h2 {
            color: var(--text-light);
        }

        p {
            font-size: 1.25rem;
            color: #6c757d;
            margin-bottom: 2.5rem;
        }

        .dark-mode p {
            color: #adb5bd;
        }

        .btn-action {
            font-size: 1.2rem;
            padding: 0.8rem 2.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            font-weight: 700;
            text-transform: uppercase;
        }

        .btn-register {
            background-image: var(--gradient-main);
            border: none;
            color: white;
            box-shadow: 0 4px 10px rgba(107, 142, 35, 0.3);
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(107, 142, 35, 0.4);
        }
        
        .btn-login {
            background-color: transparent;
            border: 2px solid var(--primary-olive);
            color: var(--primary-olive);
        }

        .btn-login:hover {
            background-color: rgba(107, 142, 35, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        
        .dark-mode .btn-login {
            border: 2px solid #a7d129;
            color: #a7d129;
        }
        
        .dark-mode .btn-login:hover {
            background-color: rgba(167, 209, 41, 0.1);
        }

        .feature-card {
            background-color: #e9f2ea;
            border-radius: 1.2rem;
            padding: 2.5rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(107, 142, 35, 0.1);
        }
        
        .dark-mode .feature-card {
            background-color: #2e3a47;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .dark-mode .feature-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }

        .feature-card i {
            color: var(--primary-olive);
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .feature-card h3 {
            color: var(--text-dark);
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .dark-mode .feature-card h3 {
            color: var(--text-light);
        }

        .feature-card p {
            font-size: 1rem;
            color: #495057;
            margin-bottom: 0;
        }
        
        .dark-mode .feature-card p {
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <h1>List'in</h1>
        <h2>Kelola Tugas. Jadi Lebih Produktif</h2>
        <p class="lead">Aplikasi manajemen tugas yang sederhana namun kuat untuk membantu Anda tetap teratur dan fokus pada hal yang paling penting.</p>
        <div class="d-flex justify-content-center gap-4 flex-wrap mb-5">
            <a href="<?= site_url('todo/register') ?>" class="btn btn-register btn-action">
                <i class="bi bi-person-add me-2"></i>Daftar Sekarang
            </a>
            <a href="<?= site_url('todo/login') ?>" class="btn btn-login btn-action">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </a>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card h-100">
                    <i class="bi bi-list-check"></i>
                    <h3 class="mt-3">Organisasi Mudah</h3>
                    <p>Atur daftar tugas dengan prioritas dan deadline yang jelas.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100">
                    <i class="bi bi-graph-up"></i>
                    <h3 class="mt-3">Analisis Statistik</h3>
                    <p>Dapatkan wawasan berharga tentang kebiasaan dan kemajuan Anda.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100">
                    <i class="bi bi-lightning-charge"></i>
                    <h3 class="mt-3">Tingkatkan Fokus</h3>
                    <p>Selesaikan lebih banyak hal dengan antarmuka yang bersih dan bebas gangguan.</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isDarkMode = localStorage.getItem('dark-mode') === 'true';
            if (isDarkMode) {
                document.body.classList.add('dark-mode');
            }
        });
    </script>
</body>
</html>