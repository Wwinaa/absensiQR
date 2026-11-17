<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    [data-bs-theme="light"] body,
    body:not([data-bs-theme="dark"]) {
        background-color: #F7FAFF !important;
        font-family: 'Inter', sans-serif;
    }

    [data-bs-theme="dark"] body {
        background-color: #0F172A !important;
        color: #F1F5F9 !important;
        font-family: 'Inter', sans-serif;
    }

    /* Navbar Override for Login Page ONLY */
    .bg-primary {
        background-color: #FFFFFF !important;
        box-shadow: 0 2px 15px rgba(0,0,0,0.04) !important;
        border-bottom: 1px solid #EAF3FF;
    }
    .navbar-brand {
        color: #243447 !important;
        font-weight: 700 !important;
    }
    .navbar-dark .navbar-nav .nav-link {
        color: #64748B !important;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    .navbar-dark .navbar-nav .nav-link:hover {
        color: #6EA8FE !important;
    }
    .navbar-toggler {
        border-color: rgba(110, 168, 254, 0.3) !important;
    }
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2836, 52, 71, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
    }

    /* Footer overrides */
    footer.bg-white {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    footer .text-muted {
        color: #94a3b8 !important;
    }

    /* Login Page Specific Styles */
    .login-wrapper {
        animation: fadeIn 0.8s ease-out;
    }

    .login-container {
        background: #FFFFFF;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(110, 168, 254, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: row;
        min-height: 520px;
        border: 1px solid #EAF3FF;
    }

    /* Left Branding Area */
    .login-brand {
        background: linear-gradient(135deg, #F5F9FF 0%, #EAF3FF 100%);
        padding: 3.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
        flex: 1.1;
    }

    .login-brand::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(110,168,254,0.12) 0%, rgba(255,255,255,0) 70%);
        top: -100px;
        left: -100px;
        border-radius: 50%;
    }
    .login-brand::after {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(79,143,239,0.08) 0%, rgba(255,255,255,0) 70%);
        bottom: -50px;
        right: -50px;
        border-radius: 50%;
    }

    .brand-icon-wrapper {
        background: #FFFFFF;
        width: 72px;
        height: 72px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        box-shadow: 0 8px 24px rgba(110,168,254,0.15);
        margin-bottom: 2rem;
        z-index: 1;
    }

    .brand-title {
        color: #243447;
        font-weight: 800;
        font-size: 2.2rem;
        margin-bottom: 1rem;
        z-index: 1;
        letter-spacing: -0.5px;
    }

    .brand-tagline {
        color: #64748B;
        font-size: 1.05rem;
        line-height: 1.6;
        z-index: 1;
    }

    /* Right Form Area */
    .login-form-area {
        padding: 3.5rem 3rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #FFFFFF;
    }

    .login-heading {
        color: #243447;
        font-weight: 700;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .login-subtitle {
        color: #64748B;
        font-size: 0.95rem;
        margin-bottom: 2.5rem;
    }

    /* Modern Inputs */
    .input-group-modern {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .input-group-modern i.icon-left {
        position: absolute;
        left: 1.2rem;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        transition: color 0.3s;
        z-index: 10;
        font-size: 1.1rem;
    }

    .form-control-modern {
        padding: 0.9rem 1rem 0.9rem 3rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        color: #243447;
        background-color: #FAFCFF;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-modern::placeholder {
        color: #a0aec0;
        font-weight: 400;
    }

    .form-control-modern:focus {
        border-color: #6EA8FE;
        background-color: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(110, 168, 254, 0.15);
        outline: none;
    }

    .form-control-modern:focus + i.icon-left,
    .form-control-modern:focus ~ i.icon-left {
        color: #6EA8FE;
    }

    /* Toggle Password Icon */
    i.icon-right {
        position: absolute;
        right: 1.2rem;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        cursor: pointer;
        z-index: 10;
        transition: color 0.3s;
    }
    i.icon-right:hover {
        color: #6EA8FE;
    }

    /* Checkbox */
    .form-check-modern {
        margin-top: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
    }
    .form-check-modern .form-check-input {
        border-color: #cbd5e1;
        cursor: pointer;
        width: 1.2em;
        height: 1.2em;
        margin-top: 0;
        margin-right: 0.5rem;
    }
    .form-check-modern .form-check-input:checked {
        background-color: #6EA8FE;
        border-color: #6EA8FE;
    }
    .form-check-modern .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(110, 168, 254, 0.25);
    }
    .form-check-modern .form-check-label {
        color: #64748B;
        cursor: pointer;
        font-size: 0.95rem;
        user-select: none;
    }

    /* Button */
    .btn-login {
        background: linear-gradient(135deg, #6EA8FE 0%, #4F8FEF 100%);
        color: #FFFFFF;
        border: none;
        border-radius: 12px;
        padding: 0.9rem;
        font-weight: 600;
        font-size: 1.05rem;
        width: 100%;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(79, 143, 239, 0.25);
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(79, 143, 239, 0.35);
        color: #FFFFFF;
    }

    /* Micro Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
            min-height: auto;
        }
        .login-brand {
            padding: 2.5rem 2rem;
            text-align: center;
            align-items: center;
        }
        .brand-icon-wrapper {
            margin: 0 auto 1.5rem auto;
            width: 64px;
            height: 64px;
            font-size: 2rem;
        }
        .brand-title {
            font-size: 1.75rem;
        }
        .login-form-area {
            padding: 2.5rem 2rem;
        }
    }

    /* ============================================================
       DARK MODE THEME UNTUK KARTU LOGIN (SANGAT ELEGAN & KONTRAS)
       ============================================================ */
    [data-bs-theme="dark"] .login-container {
        background: #1E293B !important;
        border-color: #334155 !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.45) !important;
    }

    [data-bs-theme="dark"] .login-brand {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%) !important;
        border-right: 1px solid #334155 !important;
    }

    [data-bs-theme="dark"] .login-brand::before {
        background: radial-gradient(circle, rgba(56, 189, 248, 0.08) 0%, rgba(15, 23, 42, 0) 70%) !important;
    }

    [data-bs-theme="dark"] .login-brand::after {
        background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, rgba(15, 23, 42, 0) 70%) !important;
    }

    [data-bs-theme="dark"] .brand-title {
        color: #F8FAFC !important;
    }

    [data-bs-theme="dark"] .brand-tagline {
        color: #94A3B8 !important;
    }

    [data-bs-theme="dark"] .login-form-area {
        background: #1E293B !important;
    }

    [data-bs-theme="dark"] .login-heading {
        color: #F8FAFC !important;
    }

    [data-bs-theme="dark"] .login-subtitle {
        color: #94A3B8 !important;
    }

    [data-bs-theme="dark"] .form-control-modern {
        background-color: #0F172A !important;
        border-color: #334155 !important;
        color: #F8FAFC !important;
    }

    [data-bs-theme="dark"] .form-control-modern::placeholder {
        color: #64748B !important;
    }

    [data-bs-theme="dark"] .form-control-modern:focus {
        background-color: #0F172A !important;
        border-color: #38BDF8 !important;
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.2) !important;
    }

    [data-bs-theme="dark"] .input-group-modern i.icon-left {
        color: #64748B !important;
    }

    [data-bs-theme="dark"] .form-control-modern:focus + i.icon-left,
    [data-bs-theme="dark"] .form-control-modern:focus ~ i.icon-left {
        color: #38BDF8 !important;
    }

    [data-bs-theme="dark"] i.icon-right {
        color: #64748B !important;
    }

    [data-bs-theme="dark"] i.icon-right:hover {
        color: #38BDF8 !important;
    }

    [data-bs-theme="dark"] .form-check-modern .form-check-input {
        background-color: #0F172A !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] .form-check-modern .form-check-input:checked {
        background-color: #38BDF8 !important;
        border-color: #38BDF8 !important;
    }

    [data-bs-theme="dark"] .form-check-modern .form-check-label {
        color: #94A3B8 !important;
    }

    [data-bs-theme="dark"] .btn-login {
        background: linear-gradient(135deg, #38BDF8 0%, #2563EB 100%) !important;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35) !important;
    }

    [data-bs-theme="dark"] .btn-login:hover {
        box-shadow: 0 8px 25px rgba(56, 189, 248, 0.45) !important;
    }

    [data-bs-theme="dark"] .login-form-area a {
        color: #94A3B8 !important;
    }

    [data-bs-theme="dark"] .login-form-area a:hover {
        color: #38BDF8 !important;
    }

    [data-bs-theme="dark"] .alert-danger {
        background-color: rgba(239, 68, 68, 0.15) !important;
        border: 1px solid rgba(239, 68, 68, 0.3) !important;
        color: #FCA5A5 !important;
    }
</style>

<div class="row justify-content-center login-wrapper">
    <div class="col-lg-10 col-xl-9">
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background-color: #FEE2E2; color: #991B1B;">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="login-container">
            <!-- Area Branding -->
            <div class="login-brand">
                <div class="brand-icon-wrapper" style="background:transparent; box-shadow:none; padding:0;">
                    <img src="<?= base_url('public/assets/images/logo_smk.png?v=2') ?>"
                         alt="Logo SMK NU WARGABINANGUN"
                         style="width:105px; height:105px; object-fit:contain; filter:drop-shadow(0 6px 18px rgba(0,0,0,0.2)); margin-bottom:0.5rem;">
                </div>
                <h1 class="brand-title">SMK NU WARGABINANGUN</h1>
                <p class="brand-tagline" style="font-size:.85rem; font-weight:600; letter-spacing:.5px; color:#94a3b8; margin-bottom:.5rem;">SISTEM ABSENSI DIGITAL</p>
                <p class="brand-tagline">Kelola kehadiran sekolah dengan mudah, cepat, dan terintegrasi dalam satu platform berbasis QR Code.</p>
            </div>

            <!-- Area Form Login -->
            <div class="login-form-area">
                <h2 class="login-heading">Masuk ke Akun Anda</h2>
                <p class="login-subtitle">Silakan masukkan username dan password untuk melanjutkan.</p>

                <form id="loginForm" action="<?= base_url('auth/login') ?>" method="POST">
                    
                    <div class="input-group-modern">
                        <input type="text" class="form-control-modern" id="username" name="username" placeholder="Username" required autofocus>
                        <i class="bi bi-person-fill icon-left"></i>
                    </div>

                    <div class="input-group-modern">
                        <input type="password" class="form-control-modern" id="password" name="password" placeholder="Password" required style="padding-right: 2.8rem;">
                        <i class="bi bi-lock-fill icon-left"></i>
                        <i class="bi bi-eye-slash icon-right" id="togglePassword" title="Show/Hide Password"></i>
                    </div>

                    <div class="form-check-modern">
                        <input class="form-check-input" type="checkbox" value="1" id="rememberMe" name="remember_me">
                        <label class="form-check-label" for="rememberMe">
                            Ingat Saya
                        </label>
                    </div>

                    <button type="submit" id="btnLogin" class="btn-login">Login</button>
                    
                </form>
                
                <div class="mt-4 text-center">
                    <a href="#" class="text-decoration-none small" style="color: #64748B; transition: color 0.3s;" onmouseover="this.style.color='#6EA8FE'" onmouseout="this.style.color='#64748B'">
                        <i class="bi bi-chat-dots me-1"></i> Butuh bantuan dari AI Chatbot?
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon class
                this.classList.toggle('bi-eye-slash');
                this.classList.toggle('bi-eye');
            });
        }
    });
</script>
