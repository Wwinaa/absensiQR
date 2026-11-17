<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Login - Sistem Absensi SMK' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('app_theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
    
    <style>
        body {
            background-color: #f4f7f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .login-image {
            background: linear-gradient(135deg, #0d6efd 0%, #00d2ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 3rem;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container my-auto">
        <?php 
            if (isset($viewPath) && file_exists($viewPath)) {
                require_once $viewPath;
            }
        ?>
    </div>

    <footer class="text-center py-3 text-muted small w-100">
        &copy; <?= date('Y') ?> <strong>SMK NU WARGABINANGUN</strong> — Sistem Absensi Digital by Winarti
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Show loading state safely without aborting form submission
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            if(loginForm) {
                loginForm.addEventListener('submit', function() {
                    const btn = document.getElementById('btnLogin');
                    if(btn) {
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sedang masuk...';
                        btn.style.pointerEvents = 'none';
                        setTimeout(function() {
                            btn.disabled = true;
                        }, 50);
                    }
                });
            }
        });
    </script>
</body>
</html>
