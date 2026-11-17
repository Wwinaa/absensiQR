<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' — Absensi SMK NU WARGABINANGUN' : 'Sistem Absensi | SMK NU WARGABINANGUN' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('public/assets/images/logo_smk.png?v=2') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('public/assets/images/logo_smk.png?v=2') ?>">
    
    <!-- Bootstrap 5 CSS (Local with CDN fallback) -->
    <link href="<?= base_url('public/assets/css/bootstrap.min.css') ?>" rel="stylesheet" onerror="this.onerror=null;this.href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css'">
    
    <!-- Bootstrap Icons (Local with CDN fallback) -->
    <link rel="stylesheet" href="<?= base_url('public/assets/css/bootstrap-icons.min.css') ?>" onerror="this.onerror=null;this.href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css'">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/css/style.css') ?>">

    <script>
        // Apply theme immediately to prevent flashing
        (function() {
            const savedTheme = localStorage.getItem('app_theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --soft-blue: #6EA8FE;
            --light-blue: #EAF3FF;
            --very-light-blue: #F5F9FF;
            --medium-blue: #4F8FEF;
            --dark-blue: #1E3A5F;
            --border-blue: #BFD7F5;
            --bg-color: #F7FAFF;
            --text-secondary: #64748B;
        }

        body {
            background-color: var(--bg-color) !important;
            font-family: 'Inter', sans-serif;
            color: var(--dark-blue);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--dark-blue);
            font-weight: 700;
        }

        /* Navbar Modern Soft Blue */
        .navbar-custom {
            background-color: #FFFFFF !important;
            box-shadow: none !important;
            border-bottom: 1px solid var(--border-blue);
            height: var(--top-bar-height);
            min-height: var(--top-bar-height);
            max-height: var(--top-bar-height);
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            display: flex;
            align-items: center;
            box-sizing: border-box;
        }
        .navbar-custom .navbar-brand {
            color: var(--dark-blue) !important;
            font-weight: 800;
        }
        .navbar-custom .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .navbar-custom .nav-link:hover, .navbar-custom .nav-link.active {
            color: var(--medium-blue) !important;
            background-color: var(--very-light-blue);
        }
        .navbar-custom .navbar-toggler {
            border-color: rgba(110, 168, 254, 0.3);
        }
        .navbar-custom .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2830, 58, 95, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Common Modern Components */
        .card-modern {
            background: #FFFFFF;
            border: 1px solid var(--light-blue);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(110, 168, 254, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(110, 168, 254, 0.1);
        }

        .btn-soft-blue {
            background: linear-gradient(135deg, var(--soft-blue) 0%, var(--medium-blue) 100%);
            color: #FFFFFF !important;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(79, 143, 239, 0.25);
            padding: 0.6rem 1.5rem;
        }
        .btn-soft-blue:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 143, 239, 0.35);
        }
        .btn-soft-blue-outline {
            background: transparent;
            color: var(--medium-blue) !important;
            border: 2px solid var(--soft-blue);
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 0.5rem 1.4rem;
        }
        .btn-soft-blue-outline:hover {
            background: var(--very-light-blue);
            transform: translateY(-2px);
        }

        .table-modern-container {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--light-blue);
            background: #FFFFFF;
            box-shadow: 0 5px 20px rgba(110, 168, 254, 0.04);
        }
        .table-modern {
            margin-bottom: 0;
        }
        .table-modern thead th {
            background-color: var(--very-light-blue);
            color: var(--dark-blue);
            font-weight: 700;
            border-bottom: 2px solid var(--border-blue);
            padding: 1.2rem 1rem;
            white-space: nowrap;
        }
        .table-modern tbody td {
            padding: 1.2rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--light-blue);
            color: #475569;
        }
        .table-modern tbody tr:hover {
            background-color: var(--very-light-blue);
        }

        .badge-soft-success {
            background-color: #DEF7EC;
            color: #03543F;
            border: 1px solid #84E1BC;
            padding: 0.5em 0.8em;
            border-radius: 8px;
            font-weight: 600;
        }
        .badge-soft-danger {
            background-color: #FDE8E8;
            color: #9B1C1C;
            border: 1px solid #F8B4B4;
            padding: 0.5em 0.8em;
            border-radius: 8px;
            font-weight: 600;
        }
        .badge-soft-warning {
            background-color: #FEF08A;
            color: #723B13;
            border: 1px solid #FACA15;
            padding: 0.5em 0.8em;
            border-radius: 8px;
            font-weight: 600;
        }
        .badge-soft-info {
            background-color: #E0F2FE;
            color: #0369A1;
            border: 1px solid #7DD3FC;
            padding: 0.5em 0.8em;
            border-radius: 8px;
            font-weight: 600;
        }
        .badge-soft-primary {
            background-color: #EDE9FE;
            color: #5B21B6;
            border: 1px solid #C4B5FD;
            padding: 0.5em 0.8em;
            border-radius: 8px;
            font-weight: 600;
        }

        /* Chatbot Styles */
        .chatbot-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--soft-blue) 0%, var(--medium-blue) 100%);
            color: white;
            border: none;
            box-shadow: 0 6px 20px rgba(110, 168, 254, 0.4);
            font-size: 24px;
            z-index: 1000;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .chatbot-btn:hover {
            transform: scale(1.1) translateY(-5px);
        }
        .chatbot-window {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            height: 480px;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(30, 58, 95, 0.15);
            display: none;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
            border: 1px solid var(--border-blue);
        }
        .chatbot-header {
            background: linear-gradient(135deg, var(--soft-blue) 0%, var(--medium-blue) 100%);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chatbot-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background-color: var(--bg-color);
        }
        .chat-msg {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 16px;
            font-size: 14px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
            text-align: left;
        }
        .msg-user {
            background-color: var(--medium-blue);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }
        .msg-bot {
            background-color: #FFFFFF;
            color: var(--dark-blue);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border: 1px solid var(--light-blue);
            text-align: left;
        }
        .chatbot-footer {
            padding: 15px;
            border-top: 1px solid var(--light-blue);
            display: flex;
            background-color: white;
        }
        .chatbot-input {
            flex: 1;
            border: 1px solid var(--border-blue);
            background-color: var(--very-light-blue);
            border-radius: 20px;
            padding: 10px 15px;
            outline: none;
            transition: border-color 0.3s;
        }
        .chatbot-input:focus {
            border-color: var(--soft-blue);
            background-color: #FFFFFF;
        }
        .chatbot-send {
            background: none;
            border: none;
            color: var(--medium-blue);
            font-size: 22px;
            margin-left: 10px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .chatbot-send:hover {
            transform: scale(1.1);
        }
        /* =========================================================
           SIDEBAR ICON-RAIL FULL HEIGHT (Sesuai Desain Gambar)
           ========================================================= */

        :root {
            --top-bar-height: 70px;
            --bottom-bar-height: 70px;
            --sidebar-width-compact: 72px;
            --sidebar-width-expanded: 250px;
            --sidebar-bg: #FFFFFF;
            --sidebar-border: var(--border-blue);
            --sidebar-item-hover: var(--very-light-blue);
            --sidebar-item-active: linear-gradient(135deg, var(--soft-blue) 0%, var(--medium-blue) 100%);
            --sidebar-text: #64748B;
            --sidebar-text-active: #FFFFFF;
        }

        [data-bs-theme="dark"] {
            --sidebar-bg: #1E293B;
            --sidebar-border: #334155;
            --sidebar-item-hover: rgba(255, 255, 255, 0.08);
            --sidebar-item-active: linear-gradient(135deg, #0284C7 0%, #2563EB 100%);
            --sidebar-text: #94A3B8;
            --sidebar-text-active: #FFFFFF;
        }

        /* Penyesuaian layout body saat login (memiliki sidebar) */
        @media (min-width: 992px) {
            body.has-sidebar {
                padding-left: var(--sidebar-width-compact);
                transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            body.has-sidebar.sidebar-expanded {
                padding-left: var(--sidebar-width-expanded);
            }
            .btn-hamburger-nav {
                display: none !important;
            }
        }

        @media (max-width: 991.98px) {
            body.has-sidebar {
                padding-left: 0 !important;
            }
            .btn-hamburger-nav {
                display: flex !important;
            }
        }

        /* Backdrop overlay untuk drawer mobile */
        #sidebarBackdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(3px);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }
        #sidebarBackdrop.show {
            opacity: 1;
            visibility: visible;
        }

        /* Kontainer Sidebar Utama: Full Height (100vh), top 0, left 0, bottom 0 */
        #appSidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh;
            width: var(--sidebar-width-compact);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            box-shadow: none;
            z-index: 1045;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 0 !important;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.25s ease, border-color 0.25s ease;
            user-select: none;
            overflow-x: visible;
            box-sizing: border-box;
        }

        @media (max-width: 991.98px) {
            #appSidebar {
                width: var(--sidebar-width-expanded);
                transform: translateX(-100%);
            }
            #appSidebar.mobile-show {
                transform: translateX(0);
                box-shadow: 10px 0 30px rgba(0, 0, 0, 0.2) !important;
            }
        }

        @media (min-width: 992px) {
            body.sidebar-expanded #appSidebar {
                width: var(--sidebar-width-expanded);
            }
        }

        /* Bagian Atas: Tombol Garis 3 (Tinggi disamakan persis dengan Navbar 70px) */
        .sidebar-top {
            height: var(--top-bar-height);
            min-height: var(--top-bar-height);
            max-height: var(--top-bar-height);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 14px;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
            background: var(--sidebar-bg);
            box-sizing: border-box;
            transition: background-color 0.25s ease, border-color 0.25s ease;
        }

        body.sidebar-expanded .sidebar-top {
            justify-content: flex-start;
            gap: 10px;
        }
        @media (max-width: 991.98px) {
            #appSidebar .sidebar-top {
                justify-content: flex-start;
                gap: 10px;
            }
        }

        .sidebar-hamburger-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--very-light-blue);
            border: 1px solid var(--border-blue);
            color: var(--medium-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
            flex-shrink: 0;
        }
        .sidebar-hamburger-btn:hover {
            background: #DCEAFE;
            border-color: var(--soft-blue);
            color: var(--dark-blue);
            transform: scale(1.05);
        }

        .sidebar-top-brand {
            display: none;
            align-items: center;
            gap: 8px;
            overflow: hidden;
            white-space: nowrap;
        }
        body.sidebar-expanded .sidebar-top-brand {
            display: flex;
        }
        @media (max-width: 991.98px) {
            #appSidebar .sidebar-top-brand {
                display: flex;
            }
        }

        .sidebar-brand-title {
            color: var(--dark-blue);
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            line-height: 1;
        }
        .sidebar-brand-badge {
            background-color: var(--very-light-blue);
            color: var(--medium-blue);
            border: 1px solid var(--border-blue);
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.25rem 0.45rem;
            border-radius: 6px;
        }

        /* Bagian Tengah: List Navigasi Icon */
        .sidebar-nav-container {
            flex: 1;
            overflow-y: auto;
            overflow-x: visible;
            padding: 16px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            scrollbar-width: none;
        }
        .sidebar-nav-container::-webkit-scrollbar {
            display: none;
        }

        /* Pembungkus Setiap Item Menu */
        .sidebar-item-wrapper {
            position: relative;
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 0 12px;
        }

        /* Tombol Icon Navigasi */
        .sidebar-nav-link {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 1.25rem;
            position: relative;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: transparent;
        }
        .sidebar-nav-link:hover {
            background: var(--sidebar-item-hover);
            color: var(--medium-blue);
            transform: scale(1.06);
        }

        /* Kotak Highlight Aktif (Gradien Sesuai Tema Sistem) */
        .sidebar-nav-link.active {
            background: var(--sidebar-item-active);
            color: #FFFFFF !important;
            box-shadow: 0 4px 15px rgba(79, 143, 239, 0.35);
        }

        .sidebar-nav-link .nav-icon {
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Titik Notifikasi Belum Dibaca */
        .sidebar-unread-dot {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #EF4444;
            border: 2px solid var(--sidebar-bg);
            box-shadow: 0 0 6px rgba(239, 68, 68, 0.8);
        }

        /* Teks Label saat Sidebar Diperluas (Expanded) */
        .sidebar-nav-text {
            display: none;
            white-space: nowrap;
            font-size: 0.9rem;
            font-weight: 500;
            margin-left: 12px;
            color: inherit;
            flex: 1;
            text-align: left;
        }

        body.sidebar-expanded .sidebar-item-wrapper {
            justify-content: flex-start;
        }
        body.sidebar-expanded .sidebar-nav-link {
            width: 100%;
            justify-content: flex-start;
            padding: 0 14px;
            gap: 12px;
        }
        body.sidebar-expanded .sidebar-nav-text {
            display: block;
        }

        @media (max-width: 991.98px) {
            #appSidebar .sidebar-item-wrapper {
                justify-content: flex-start;
            }
            #appSidebar .sidebar-nav-link {
                width: 100%;
                justify-content: flex-start;
                padding: 0 14px;
                gap: 12px;
            }
            #appSidebar .sidebar-nav-text {
                display: block;
            }
        }

        /* =========================================================
           FLYOUT MENU / POPUP KANAN (Modern Floating Card)
           ========================================================= */

        .sidebar-flyout {
            position: absolute;
            left: calc(100% + 14px);
            top: 50%;
            transform: translateY(-50%) translateX(-6px);
            background: #FFFFFF;
            border: 1px solid var(--border-blue);
            border-radius: 12px;
            padding: 8px 14px;
            box-shadow: 0 10px 25px rgba(30, 58, 95, 0.12);
            white-space: nowrap;
            z-index: 1060;
            pointer-events: none;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
        }

        .sidebar-flyout::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 50%;
            transform: translateY(-50%);
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-right: 6px solid #FFFFFF;
        }

        @media (min-width: 992px) {
            body:not(.sidebar-expanded) .sidebar-item-wrapper:hover .sidebar-flyout {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
                transform: translateY(-50%) translateX(0);
            }
            body.sidebar-expanded .sidebar-flyout {
                display: none !important;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar-flyout {
                display: none !important;
            }
        }

        .flyout-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--dark-blue);
            line-height: 1.2;
        }
        .flyout-subtitle {
            font-size: 0.72rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* Bagian Bawah: Tombol Logout (Tinggi disamakan persis dengan Footer 70px) */
        .sidebar-bottom {
            height: var(--bottom-bar-height);
            min-height: var(--bottom-bar-height);
            max-height: var(--bottom-bar-height);
            padding: 0 14px;
            border-top: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: var(--sidebar-bg);
            box-sizing: border-box;
            transition: border-color 0.25s ease, background-color 0.25s ease;
        }

        /* Footer Utama Sistem (Tinggi disamakan persis dengan Sidebar Bottom 70px) */
        footer.app-footer {
            height: var(--bottom-bar-height);
            min-height: var(--bottom-bar-height);
            max-height: var(--bottom-bar-height);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 1.5rem;
            background-color: #FFFFFF !important;
            border-top: 1px solid var(--border-blue) !important;
            box-shadow: none !important;
            box-sizing: border-box;
            margin-top: auto;
            width: 100%;
        }

        .sidebar-logout-link {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #DC2626;
            background: #FEE2E2;
            border: 1px solid #FCA5A5;
            text-decoration: none;
            font-size: 1.2rem;
            transition: all 0.2s ease;
        }
        .sidebar-logout-link:hover {
            background: #EF4444;
            border-color: #EF4444;
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
            transform: scale(1.05);
        }

        body.sidebar-expanded .sidebar-logout-link {
            width: 100%;
            justify-content: flex-start;
            padding: 0 14px;
            gap: 12px;
        }
        body.sidebar-expanded .sidebar-logout-text {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .sidebar-logout-text {
            display: none;
            white-space: nowrap;
        }

        @media (max-width: 991.98px) {
            #appSidebar .sidebar-logout-link {
                width: 100%;
                justify-content: flex-start;
                padding: 0 14px;
                gap: 12px;
            }
            #appSidebar .sidebar-logout-text {
                display: block;
                font-size: 0.9rem;
                font-weight: 600;
            }
        }

        /* Hamburger button di Top Navbar untuk Layar Mobile */
        .btn-hamburger {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--very-light-blue);
            border: 1px solid var(--border-blue);
            color: var(--medium-blue);
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(79, 143, 239, 0.1);
            cursor: pointer;
            touch-action: manipulation;
            user-select: none;
        }
        .btn-hamburger:hover, .btn-hamburger:focus {
            background-color: #DCEAFE !important;
            border-color: var(--soft-blue) !important;
            color: var(--dark-blue) !important;
            transform: scale(1.05);
            outline: none;
        }

        /* Header Profile & Theme Toggle Styling */
        .header-profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 14px 4px 6px;
            border-radius: 30px;
            background: var(--very-light-blue);
            border: 1px solid var(--border-blue);
            color: var(--dark-blue);
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(79, 143, 239, 0.08);
        }
        .header-profile-btn::after {
            display: none !important;
        }
        .header-profile-btn:hover,
        .header-profile-btn[aria-expanded="true"] {
            background-color: #DCEAFE;
            border-color: var(--soft-blue);
            color: var(--dark-blue);
            box-shadow: 0 4px 14px rgba(79, 143, 239, 0.18);
            transform: translateY(-1px);
        }
        .header-profile-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #FFFFFF;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .header-profile-initial {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--soft-blue) 0%, var(--medium-blue) 100%);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #FFFFFF;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .header-theme-toggle-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--very-light-blue);
            border: 1px solid var(--border-blue);
            color: var(--medium-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(79, 143, 239, 0.08);
        }
        .header-theme-toggle-btn:hover {
            background-color: #DCEAFE;
            border-color: var(--soft-blue);
            transform: scale(1.05);
        }

        /* ================= DARK MODE STYLES ================= */
        [data-bs-theme="dark"] {
            --bg-color: #0B1120;
            --very-light-blue: #1E293B;
            --light-blue: #334155;
            --border-blue: #334155;
            --dark-blue: #F1F5F9;
            --text-secondary: #94A3B8;
            --soft-blue: #60A5FA;
            --medium-blue: #38BDF8;
        }

        [data-bs-theme="dark"] body {
            background-color: #0B1120 !important;
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] h1, 
        [data-bs-theme="dark"] h2, 
        [data-bs-theme="dark"] h3, 
        [data-bs-theme="dark"] h4, 
        [data-bs-theme="dark"] h5, 
        [data-bs-theme="dark"] h6 {
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .bg-light,
        [data-bs-theme="dark"] .bg-white {
            background-color: #1E293B !important;
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .text-dark {
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .text-muted,
        [data-bs-theme="dark"] .text-secondary {
            color: #94A3B8 !important;
        }

        [data-bs-theme="dark"] .navbar-custom {
            background-color: #1E293B !important;
            border-bottom: 1px solid #334155 !important;
            box-shadow: none !important;
        }

        [data-bs-theme="dark"] .navbar-custom .navbar-brand span span:last-child {
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .card-modern,
        [data-bs-theme="dark"] .card {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
        }

        [data-bs-theme="dark"] .card-header {
            background-color: #1E293B !important;
            border-bottom-color: #334155 !important;
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .table-modern thead th,
        [data-bs-theme="dark"] .table thead th {
            background-color: #0F172A !important;
            color: #F1F5F9 !important;
            border-bottom-color: #334155 !important;
        }

        [data-bs-theme="dark"] .table-modern tbody td,
        [data-bs-theme="dark"] .table tbody td {
            background-color: #1E293B !important;
            color: #CBD5E1 !important;
            border-bottom-color: #334155 !important;
        }

        [data-bs-theme="dark"] .table-modern tbody tr:hover td,
        [data-bs-theme="dark"] .table tbody tr:hover td {
            background-color: #334155 !important;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.45) !important;
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: #CBD5E1 !important;
        }

        [data-bs-theme="dark"] .dropdown-item:hover,
        [data-bs-theme="dark"] .dropdown-item:focus {
            background-color: #334155 !important;
            color: #38BDF8 !important;
        }

        [data-bs-theme="dark"] .dropdown-divider {
            border-top-color: #334155 !important;
        }

        [data-bs-theme="dark"] .dropdown-profile-header {
            background: #0F172A !important;
            border-color: #334155 !important;
        }

        /* ================= DARK MODE SIDEBAR ================= */
        [data-bs-theme="dark"] #appSidebar {
            background: #1E293B !important;
            border-right: 1px solid #334155 !important;
            box-shadow: none !important;
        }
        @media (max-width: 991.98px) {
            [data-bs-theme="dark"] #appSidebar.mobile-show {
                box-shadow: 10px 0 35px rgba(0, 0, 0, 0.6) !important;
            }
        }
        [data-bs-theme="dark"] .sidebar-top {
            background: #1E293B !important;
            border-bottom: 1px solid #334155 !important;
        }
        [data-bs-theme="dark"] .sidebar-bottom {
            border-top: 1px solid #334155 !important;
        }
        [data-bs-theme="dark"] .sidebar-hamburger-btn {
            background: rgba(255, 255, 255, 0.05) !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
        }
        [data-bs-theme="dark"] .sidebar-hamburger-btn:hover {
            background: rgba(56, 189, 248, 0.15) !important;
            border-color: #38BDF8 !important;
            color: #38BDF8 !important;
        }
        [data-bs-theme="dark"] .sidebar-brand-title {
            color: #F1F5F9 !important;
        }
        [data-bs-theme="dark"] .sidebar-brand-badge {
            background-color: rgba(56, 189, 248, 0.15) !important;
            color: #38BDF8 !important;
            border-color: rgba(56, 189, 248, 0.3) !important;
        }
        [data-bs-theme="dark"] .sidebar-nav-link {
            color: #94A3B8 !important;
        }
        [data-bs-theme="dark"] .sidebar-nav-link:hover {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #F1F5F9 !important;
        }
        [data-bs-theme="dark"] .sidebar-nav-link.active {
            background: linear-gradient(135deg, #0284C7 0%, #2563EB 100%) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.45) !important;
        }
        [data-bs-theme="dark"] .sidebar-unread-dot {
            border-color: #1E293B !important;
        }
        [data-bs-theme="dark"] .sidebar-logout-link {
            background: rgba(239, 68, 68, 0.15) !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
            color: #F87171 !important;
        }
        [data-bs-theme="dark"] .sidebar-logout-link:hover {
            background: #EF4444 !important;
            border-color: #EF4444 !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4) !important;
        }
        [data-bs-theme="dark"] .sidebar-flyout {
            background: #0F172A !important;
            border-color: #334155 !important;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.6) !important;
        }
        [data-bs-theme="dark"] .sidebar-flyout::before {
            border-right-color: #0F172A !important;
        }
        [data-bs-theme="dark"] .sidebar-flyout .flyout-title {
            color: #F1F5F9 !important;
        }
        [data-bs-theme="dark"] .sidebar-flyout .flyout-subtitle {
            color: #94A3B8 !important;
        }

        [data-bs-theme="dark"] .table-modern-container {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
        }

        [data-bs-theme="dark"] .modal-content {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .modal-body {
            background-color: #1E293B !important;
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #0F172A !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            border-color: #38BDF8 !important;
            box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.25) !important;
        }

        [data-bs-theme="dark"] footer,
        [data-bs-theme="dark"] footer.app-footer {
            background-color: #1E293B !important;
            border-top: 1px solid #334155 !important;
            box-shadow: none !important;
        }

        [data-bs-theme="dark"] .btn-hamburger {
            background: #0F172A !important;
            border-color: #334155 !important;
            color: #60A5FA !important;
        }

        [data-bs-theme="dark"] .header-profile-btn {
            background: #0F172A !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .header-profile-btn:hover,
        [data-bs-theme="dark"] .header-profile-btn[aria-expanded="true"] {
            background: #334155 !important;
            border-color: #60A5FA !important;
            color: #FFFFFF !important;
        }

        [data-bs-theme="dark"] .header-theme-toggle-btn {
            background: #0F172A !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
        }

        [data-bs-theme="dark"] .header-theme-toggle-btn:hover {
            background: #334155 !important;
            border-color: #60A5FA !important;
        }

        [data-bs-theme="dark"] .chatbot-window {
            background-color: #1E293B !important;
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .chatbot-body {
            background-color: #0F172A !important;
        }

        [data-bs-theme="dark"] .msg-bot {
            background-color: #1E293B !important;
            color: #F1F5F9 !important;
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .chatbot-footer {
            background-color: #1E293B !important;
            border-top-color: #334155 !important;
        }

        [data-bs-theme="dark"] .chatbot-input {
            background-color: #0F172A !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
        }

        /* Dark Mode Badges Enhancement */
        [data-bs-theme="dark"] .badge-soft-success {
            background-color: rgba(16, 185, 129, 0.2) !important;
            color: #34D399 !important;
            border-color: rgba(16, 185, 129, 0.4) !important;
        }
        [data-bs-theme="dark"] .badge-soft-danger {
            background-color: rgba(239, 68, 68, 0.2) !important;
            color: #F87171 !important;
            border-color: rgba(239, 68, 68, 0.4) !important;
        }
        [data-bs-theme="dark"] .badge-soft-warning {
            background-color: rgba(245, 158, 11, 0.2) !important;
            color: #FBBF24 !important;
            border-color: rgba(245, 158, 11, 0.4) !important;
        }
        [data-bs-theme="dark"] .badge-soft-info {
            background-color: rgba(14, 165, 233, 0.2) !important;
            color: #38BDF8 !important;
            border-color: rgba(14, 165, 233, 0.4) !important;
        }
        [data-bs-theme="dark"] .badge-soft-primary {
            background-color: rgba(99, 102, 241, 0.2) !important;
            color: #818CF8 !important;
            border-color: rgba(99, 102, 241, 0.4) !important;
        }
        [data-bs-theme="dark"] .personal-presensi-card {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%) !important;
            border-color: #334155 !important;
        }
        [data-bs-theme="dark"] .live-clock-widget {
            background-color: #1E293B !important;
            border-color: #334155 !important;
        }
        [data-bs-theme="dark"] .live-clock-widget .text-dark {
            color: #F1F5F9 !important;
        }
        [data-bs-theme="dark"] .live-clock-widget .text-secondary {
            color: #94A3B8 !important;
        }
        [data-bs-theme="dark"] .border-light,
        [data-bs-theme="dark"] .border-bottom {
            border-color: #334155 !important;
        }
        [data-bs-theme="dark"] .list-group-item {
            background-color: transparent !important;
            border-color: #334155 !important;
            color: #F1F5F9 !important;
        }
        [data-bs-theme="dark"] .progress {
            background-color: #334155 !important;
        }

        /* Isolasi Media Cetak / Print dari Dark Mode */
        @media print {
            html, body {
                background: #FFFFFF !important;
                background-color: #FFFFFF !important;
                color: #000000 !important;
                padding-left: 0 !important;
                padding-top: 0 !important;
                margin: 0 !important;
            }
            .navbar-custom, #appSidebar, #sidebarBackdrop, .btn-hamburger, .header-profile-btn, .header-theme-toggle-btn, .chatbot-btn, .chatbot-window, footer, .no-print {
                display: none !important;
            }
            .main-content {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-light <?= isset($_SESSION['user_id']) ? 'has-sidebar' : '' ?>">

    <?php
        $isLoggedIn = isset($_SESSION['user_id']);
        $navAvatarUrl = null;
        $userRole = 'siswa';
        $userName = 'User';
        $userFullName = null;
        $currentUrl = trim($_GET['url'] ?? 'dashboard', '/');
        $unreadCatatanCount = 0;
        $unreadCatatanGuruCount = 0;

        if ($isLoggedIn) {
            $navAvatarUrl = get_user_avatar($_SESSION['user_id'] ?? null);
            $userRole = $_SESSION['user_role'] ?? 'siswa';
            $userName = $_SESSION['user_username'] ?? 'User';

            // Ambil nama lengkap pengguna untuk tampilan profil yang lebih ramah & profesional
            try {
                $dbUserNav = new Database();
                if ($userRole === 'siswa') {
                    $dbUserNav->query("SELECT nama_lengkap FROM siswa WHERE user_id = :uid LIMIT 1");
                } elseif ($userRole === 'guru' || $userRole === 'wali_kelas') {
                    $dbUserNav->query("SELECT nama_lengkap FROM guru WHERE user_id = :uid LIMIT 1");
                } else {
                    $dbUserNav->query("SELECT username FROM users WHERE id = :uid LIMIT 1");
                }
                $dbUserNav->bind(':uid', $_SESSION['user_id']);
                $uRow = $dbUserNav->single();
                $userFullName = $uRow->nama_lengkap ?? $uRow->username ?? $userName;
            } catch (Exception $e) {
                $userFullName = $userName;
            }

            // Cek catatan pembinaan belum dibaca untuk role siswa (titik merah)
            if ($userRole === 'siswa') {
                try {
                    $dbCatatanNav = new Database();
                    $dbCatatanNav->query("SELECT id FROM siswa WHERE user_id = :uid LIMIT 1");
                    $dbCatatanNav->bind(':uid', $_SESSION['user_id']);
                    $swNav = $dbCatatanNav->single();
                    if ($swNav) {
                        $dbCatatanNav->query("SELECT COUNT(*) as total FROM catatan_pembinaan WHERE siswa_id = :sid AND visibilitas = 'Siswa' AND (status_baca = 0 OR status_baca IS NULL)");
                        $dbCatatanNav->bind(':sid', $swNav->id);
                        $resNav = $dbCatatanNav->single();
                        $unreadCatatanCount = $resNav ? (int)$resNav->total : 0;
                    }
                } catch (Exception $e) {
                    $unreadCatatanCount = 0;
                }
            }

            // Cek catatan dari kepala sekolah belum dibaca untuk role guru & wali_kelas
            if ($userRole === 'guru' || $userRole === 'wali_kelas') {
                try {
                    $dbCatatanNav = new Database();
                    $dbCatatanNav->query("SELECT id FROM guru WHERE user_id = :uid LIMIT 1");
                    $dbCatatanNav->bind(':uid', $_SESSION['user_id']);
                    $gNav = $dbCatatanNav->single();
                    if ($gNav) {
                        $dbCatatanNav->query("SELECT COUNT(*) as total FROM catatan_guru WHERE guru_id = :gid AND status_baca = 'belum'");
                        $dbCatatanNav->bind(':gid', $gNav->id);
                        $resGNav = $dbCatatanNav->single();
                        $unreadCatatanGuruCount = $resGNav ? (int)$resGNav->total : 0;
                    }
                } catch (Exception $e) {
                    $unreadCatatanGuruCount = 0;
                }
            }
        }
    ?>

    <!-- Top Navbar -->
    <nav class="navbar navbar-custom sticky-top py-0">
        <div class="container-fluid px-3 px-lg-4 d-flex align-items-center justify-content-between h-100">
            <!-- Left Side: Mobile Hamburger Button + Logo Brand -->
            <div class="d-flex align-items-center">
                <?php if ($isLoggedIn): ?>
                    <!-- Tombol Garis 3 untuk Mobile (Hanya muncul di hp/tablet < 992px) -->
                    <button class="btn btn-hamburger btn-hamburger-nav d-lg-none d-flex align-items-center justify-content-center me-2 me-sm-3" 
                            type="button" 
                            id="mobileSidebarToggleBtn"
                            aria-label="Buka Menu Navigasi" 
                            title="Buka Menu Navigasi"
                            onclick="toggleMobileSidebar(event)">
                        <i class="bi bi-list fs-2" style="line-height: 1;"></i>
                    </button>
                <?php endif; ?>

                <a class="navbar-brand font-weight-bold mb-0" href="<?= base_url() ?>" style="display:flex; align-items:center; gap:12px;">
                    <img src="<?= base_url('public/assets/images/logo_smk.png?v=2') ?>" alt="Logo SMK NU WARGABINANGUN"
                         style="height:44px; width:44px; object-fit:contain; filter:drop-shadow(0 2px 6px rgba(0,0,0,0.15));">
                    <span style="line-height:1.2;">
                        <span style="font-size:0.7rem; display:block; color:var(--text-secondary); font-weight:600; letter-spacing:.5px;">SISTEM ABSENSI</span>
                        <span style="font-size:0.95rem; color:var(--dark-blue); font-weight:800;">SMK NU WARGABINANGUN</span>
                    </span>
                </a>
            </div>

            <!-- Right Side: Profile Dropdown (or Login if guest) -->
            <div class="d-flex align-items-center gap-2">
                <?php if (!$isLoggedIn): ?>
                    <button class="btn header-theme-toggle-btn d-flex align-items-center justify-content-center" 
                            type="button" 
                            id="guestThemeToggleBtn" 
                            onclick="toggleTheme(event)" 
                            title="Beralih Mode Gelap/Terang" 
                            style="width: 38px; height: 38px; border-radius: 50%;">
                        <i class="bi bi-moon-stars-fill text-primary" id="guestThemeIcon"></i>
                    </button>
                    <a href="<?= base_url('auth/login') ?>" class="btn btn-sm btn-soft-blue px-3">Login</a>
                <?php else: ?>
                    <!-- Profil User Dropdown di Pojok Kanan Atas -->
                    <div class="dropdown">
                        <button class="btn header-profile-btn d-flex align-items-center" type="button" id="headerProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if ($navAvatarUrl): ?>
                                <img src="<?= $navAvatarUrl ?>" alt="Avatar" class="header-profile-avatar">
                            <?php else: ?>
                                <div class="header-profile-initial"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                            <?php endif; ?>
                            <div class="d-none d-md-flex flex-column text-start" style="line-height: 1.2;">
                                <span class="fw-bold header-user-name" style="font-size: 0.85rem; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($userFullName ?? $userName) ?>
                                </span>
                                <span class="text-secondary small" style="font-size: 0.7rem; text-transform: capitalize;">
                                    <?= str_replace('_', ' ', $userRole) ?>
                                </span>
                            </div>
                            <i class="bi bi-chevron-down ms-1 text-secondary" style="font-size: 0.75rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2" aria-labelledby="headerProfileDropdown" style="width: 280px; border-radius: 16px;">
                            <!-- Header Info Card -->
                            <li class="p-3 mb-2 rounded-3 text-center dropdown-profile-header" style="background: var(--very-light-blue); border: 1px solid var(--border-blue);">
                                <div class="mb-2">
                                    <?php if ($navAvatarUrl): ?>
                                        <img src="<?= $navAvatarUrl ?>" class="rounded-circle shadow-sm" style="width: 56px; height: 56px; object-fit: cover; border: 3px solid #FFFFFF;">
                                    <?php else: ?>
                                        <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm fw-bold" style="width: 56px; height: 56px; font-size: 1.4rem; border: 3px solid #FFFFFF;">
                                            <?= strtoupper(substr($userName, 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="fw-bold fs-6 mb-0 text-truncate"><?= htmlspecialchars($userFullName ?? $userName) ?></div>
                                <div class="small text-secondary mb-2">@<?= htmlspecialchars($userName) ?></div>
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                        <?= str_replace('_', ' ', $userRole) ?>
                                    </span>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1" style="font-size: 0.68rem;">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>Aktif
                                    </span>
                                </div>
                            </li>

                            <!-- Menu: Profil Saya -->
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2" href="<?= base_url('profile') ?>">
                                    <i class="bi bi-person-circle fs-5 text-primary"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold small">Profil Saya</div>
                                        <div class="text-secondary" style="font-size: 0.72rem;">Lihat dan ubah data akun</div>
                                    </div>
                                </a>
                            </li>

                            <!-- Menu: Ganti Password -->
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2" href="<?= base_url('profile#keamanan') ?>">
                                    <i class="bi bi-shield-lock-fill fs-5 text-info"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold small">Ganti Password</div>
                                        <div class="text-secondary" style="font-size: 0.72rem;">Perbarui kata sandi akun</div>
                                    </div>
                                </a>
                            </li>

                            <!-- Menu: Pengaturan Sistem -->
                            <li>
                                <?php if ($userRole === 'admin'): ?>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2" href="<?= base_url('admin/pengaturan') ?>">
                                        <i class="bi bi-gear-fill fs-5 text-secondary"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small">Pengaturan Sistem</div>
                                            <div class="text-secondary" style="font-size: 0.72rem;">Konfigurasi jam absen & sekolah</div>
                                        </div>
                                    </a>
                                <?php else: ?>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2" href="<?= base_url('profile') ?>">
                                        <i class="bi bi-gear-fill fs-5 text-secondary"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small">Pengaturan Akun</div>
                                            <div class="text-secondary" style="font-size: 0.72rem;">Preferensi dan keamanan akun</div>
                                        </div>
                                    </a>
                                <?php endif; ?>
                            </li>

                            <li><hr class="dropdown-divider my-2"></li>

                            <!-- Menu: Mode Terang & Mode Gelap Switch -->
                            <li>
                                <div class="dropdown-item d-flex align-items-center justify-content-between py-2 rounded-2" style="cursor: pointer;" onclick="toggleTheme(event)">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-moon-stars-fill fs-5 text-warning" id="dropdownThemeIcon"></i>
                                        <div>
                                            <div class="fw-semibold small" id="dropdownThemeTitle">Mode Gelap</div>
                                            <div class="text-secondary" style="font-size: 0.72rem;" id="dropdownThemeSub">Ubah tampilan terang / gelap</div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch mb-0 ms-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="dropdownThemeSwitch" style="cursor: pointer; pointer-events: none;">
                                    </div>
                                </div>
                            </li>

                            <li><hr class="dropdown-divider my-2"></li>

                            <!-- Menu: Logout -->
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2 text-danger fw-semibold" href="<?= base_url('auth/logout') ?>">
                                    <i class="bi bi-box-arrow-right fs-5"></i>
                                    <span>Keluar (Logout)</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <?php if ($isLoggedIn): ?>
    <!-- Backdrop Overlay saat Sidebar Terbuka di Mobile -->
    <div id="sidebarBackdrop" onclick="closeMobileSidebar(event)"></div>

    <!-- Sidebar Mini Rail / Icon Sidebar Full Height (100vh) Sesuai Desain Gambar -->
    <aside id="appSidebar" aria-label="Menu Navigasi">

        <!-- Bagian Atas: Tombol Garis 3 (Pengganti Logo MW) -->
        <div class="sidebar-top">
            <button type="button" 
                    class="sidebar-hamburger-btn" 
                    id="sidebarToggleBtn"
                    onclick="handleSidebarHamburgerClick(event)" 
                    title="Menu Navigasi (Perkecil / Perbesar)" 
                    aria-label="Toggle Menu">
                <i class="bi bi-list fs-2"></i>
            </button>
            <div class="sidebar-top-brand">
                <span class="sidebar-brand-title">SMK NU</span>
                <span class="badge sidebar-brand-badge">ABSENSI</span>
            </div>
        </div>

        <!-- Bagian Tengah: List Icon Navigasi dengan Flyout Box ke Kanan -->
        <nav class="sidebar-nav-container">
            <?php if ($userRole === 'siswa'): ?>
                <!-- Dashboard -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'dashboard' || $currentUrl === 'dashboard/siswa') ? 'active' : '' ?>"
                       href="<?= base_url('dashboard') ?>" title="Dashboard">
                        <i class="bi bi-speedometer2 nav-icon"></i>
                        <span class="sidebar-nav-text">Dashboard</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Dashboard</div>
                        <div class="flyout-subtitle">Panel Kontrol Siswa</div>
                    </div>
                </div>

                <!-- Scan Absensi -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'absensi/scan') ? 'active' : '' ?>"
                       href="<?= base_url('absensi/scan') ?>" title="Scan Absensi">
                        <i class="bi bi-qr-code-scan nav-icon"></i>
                        <span class="sidebar-nav-text">Scan Absensi</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Scan Absensi</div>
                        <div class="flyout-subtitle">Presensi Masuk &amp; Pulang</div>
                    </div>
                </div>

                <!-- Jadwal Pelajaran -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'siswa/jadwal') ? 'active' : '' ?>"
                       href="<?= base_url('siswa/jadwal') ?>" title="Jadwal Pelajaran">
                        <i class="bi bi-calendar-week nav-icon"></i>
                        <span class="sidebar-nav-text">Jadwal Pelajaran</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Jadwal Pelajaran</div>
                        <div class="flyout-subtitle">Jadwal Belajar Kelas</div>
                    </div>
                </div>

                <!-- Riwayat Absensi -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'siswa/riwayat' || $currentUrl === 'absensi/riwayat') ? 'active' : '' ?>"
                       href="<?= base_url('siswa/riwayat') ?>" title="Riwayat Absensi">
                        <i class="bi bi-clock-history nav-icon"></i>
                        <span class="sidebar-nav-text">Riwayat Absensi</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Riwayat Absensi</div>
                        <div class="flyout-subtitle">Catatan Kehadiran Anda</div>
                    </div>
                </div>

                <!-- Pengajuan Izin/Sakit -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'siswa/pengajuan') ? 'active' : '' ?>"
                       href="<?= base_url('siswa/pengajuan') ?>" title="Pengajuan Izin/Sakit">
                        <i class="bi bi-envelope-paper-heart nav-icon"></i>
                        <span class="sidebar-nav-text">Pengajuan Izin/Sakit</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Pengajuan Izin/Sakit</div>
                        <div class="flyout-subtitle">Permohonan Tidak Masuk</div>
                    </div>
                </div>

                <!-- Catatan Pembinaan -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'siswa/catatan-pembinaan' || $currentUrl === 'siswa/catatan_pembinaan') ? 'active' : '' ?>"
                       href="<?= base_url('siswa/catatan-pembinaan') ?>" title="Catatan Pembinaan">
                        <i class="bi bi-journal-text nav-icon"></i>
                        <span class="sidebar-nav-text">Catatan Pembinaan</span>
                        <?php if ($unreadCatatanCount > 0): ?>
                            <span class="sidebar-unread-dot" title="<?= $unreadCatatanCount ?> belum dibaca"></span>
                        <?php endif; ?>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Catatan Pembinaan</div>
                        <div class="flyout-subtitle">Bimbingan Wali Kelas <?= $unreadCatatanCount > 0 ? "({$unreadCatatanCount} baru)" : '' ?></div>
                    </div>
                </div>

            <?php elseif ($userRole === 'guru'): ?>
                <!-- Dashboard -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'dashboard' || $currentUrl === 'dashboard/guru') ? 'active' : '' ?>"
                       href="<?= base_url('dashboard') ?>" title="Dashboard">
                        <i class="bi bi-speedometer2 nav-icon"></i>
                        <span class="sidebar-nav-text">Dashboard</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Dashboard</div>
                        <div class="flyout-subtitle">Panel Kontrol Guru</div>
                    </div>
                </div>

                <!-- Scan Absensi -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'absensi/scan') ? 'active' : '' ?>"
                       href="<?= base_url('absensi/scan') ?>" title="Scan Absensi">
                        <i class="bi bi-qr-code-scan nav-icon"></i>
                        <span class="sidebar-nav-text">Scan Absensi</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Scan Absensi</div>
                        <div class="flyout-subtitle">Presensi Masuk &amp; Pulang</div>
                    </div>
                </div>

                <!-- Jam Mengajar -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'guru/jadwal') ? 'active' : '' ?>"
                       href="<?= base_url('guru/jadwal') ?>" title="Jam Mengajar">
                        <i class="bi bi-calendar-week nav-icon"></i>
                        <span class="sidebar-nav-text">Jam Mengajar</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Jam Mengajar</div>
                        <div class="flyout-subtitle">Jadwal Mengajar KBM</div>
                    </div>
                </div>

                <!-- Riwayat Absensi -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'guru/riwayat' || $currentUrl === 'absensi/riwayat') ? 'active' : '' ?>"
                       href="<?= base_url('guru/riwayat') ?>" title="Riwayat Absensi">
                        <i class="bi bi-clock-history nav-icon"></i>
                        <span class="sidebar-nav-text">Riwayat Absensi</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Riwayat Absensi</div>
                        <div class="flyout-subtitle">Catatan Presensi Pribadi</div>
                    </div>
                </div>

                <!-- Pengajuan Izin/Sakit -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'guru/pengajuan') ? 'active' : '' ?>"
                       href="<?= base_url('guru/pengajuan') ?>" title="Pengajuan Izin/Sakit">
                        <i class="bi bi-envelope-paper-heart nav-icon"></i>
                        <span class="sidebar-nav-text">Pengajuan Izin/Sakit</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Pengajuan Izin/Sakit</div>
                        <div class="flyout-subtitle">Permohonan Tidak Masuk</div>
                    </div>
                </div>

                <!-- Catatan Kepala Sekolah -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'guru/catatan') ? 'active' : '' ?>"
                       href="<?= base_url('guru/catatan') ?>" title="Catatan Kepala Sekolah">
                        <i class="bi bi-journal-check nav-icon"></i>
                        <span class="sidebar-nav-text">Catatan Kepala Sekolah</span>
                        <?php if ($unreadCatatanGuruCount > 0): ?>
                            <span class="sidebar-unread-dot" title="<?= $unreadCatatanGuruCount ?> belum dibaca"></span>
                        <?php endif; ?>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Catatan Kepala Sekolah</div>
                        <div class="flyout-subtitle">Pesan &amp; Evaluasi Kepsek <?= $unreadCatatanGuruCount > 0 ? "({$unreadCatatanGuruCount} baru)" : '' ?></div>
                    </div>
                </div>

            <?php elseif ($userRole === 'wali_kelas'): ?>
                <!-- Dashboard -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'dashboard' || $currentUrl === 'dashboard/wali_kelas') ? 'active' : '' ?>"
                       href="<?= base_url('dashboard') ?>" title="Dashboard">
                        <i class="bi bi-speedometer2 nav-icon"></i>
                        <span class="sidebar-nav-text">Dashboard</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Dashboard</div>
                        <div class="flyout-subtitle">Panel Kontrol Wali Kelas</div>
                    </div>
                </div>

                <!-- Scan Absensi -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'absensi/scan') ? 'active' : '' ?>"
                       href="<?= base_url('absensi/scan') ?>" title="Scan Absensi">
                        <i class="bi bi-qr-code-scan nav-icon"></i>
                        <span class="sidebar-nav-text">Scan Absensi</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Scan Absensi</div>
                        <div class="flyout-subtitle">Presensi Masuk &amp; Pulang</div>
                    </div>
                </div>

                <!-- Jam Mengajar -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'wali-kelas/jadwal') ? 'active' : '' ?>"
                       href="<?= base_url('wali-kelas/jadwal') ?>" title="Jam Mengajar">
                        <i class="bi bi-calendar-week nav-icon"></i>
                        <span class="sidebar-nav-text">Jam Mengajar</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Jam Mengajar</div>
                        <div class="flyout-subtitle">Jadwal Mengajar KBM</div>
                    </div>
                </div>

                <!-- Riwayat Absensi -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'wali-kelas/riwayat' || $currentUrl === 'absensi/riwayat') ? 'active' : '' ?>"
                       href="<?= base_url('wali-kelas/riwayat') ?>" title="Riwayat Absensi">
                        <i class="bi bi-clock-history nav-icon"></i>
                        <span class="sidebar-nav-text">Riwayat Absensi</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Riwayat Absensi</div>
                        <div class="flyout-subtitle">Catatan Presensi Pribadi</div>
                    </div>
                </div>

                <!-- Pemantauan Kelas -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'wali-kelas/pemantauan') ? 'active' : '' ?>"
                       href="<?= base_url('wali-kelas/pemantauan') ?>" title="Pemantauan Kelas">
                        <i class="bi bi-eye nav-icon"></i>
                        <span class="sidebar-nav-text">Pemantauan Kelas</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Pemantauan Kelas</div>
                        <div class="flyout-subtitle">Kehadiran Siswa Binaan</div>
                    </div>
                </div>

                <!-- Catatan Pembinaan -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'wali-kelas/catatan-pembinaan' || $currentUrl === 'wali-kelas/catatan_pembinaan') ? 'active' : '' ?>"
                       href="<?= base_url('wali-kelas/catatan-pembinaan') ?>" title="Catatan Pembinaan">
                        <i class="bi bi-journal-text nav-icon"></i>
                        <span class="sidebar-nav-text">Catatan Pembinaan</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Catatan Pembinaan</div>
                        <div class="flyout-subtitle">Bimbingan Siswa Kelas</div>
                    </div>
                </div>

                <!-- Catatan Kepala Sekolah -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'guru/catatan') ? 'active' : '' ?>"
                       href="<?= base_url('guru/catatan') ?>" title="Catatan Kepala Sekolah">
                        <i class="bi bi-journal-check nav-icon"></i>
                        <span class="sidebar-nav-text">Catatan Kepala Sekolah</span>
                        <?php if ($unreadCatatanGuruCount > 0): ?>
                            <span class="sidebar-unread-dot" title="<?= $unreadCatatanGuruCount ?> belum dibaca"></span>
                        <?php endif; ?>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Catatan Kepala Sekolah</div>
                        <div class="flyout-subtitle">Pesan &amp; Evaluasi Kepsek <?= $unreadCatatanGuruCount > 0 ? "({$unreadCatatanGuruCount} baru)" : '' ?></div>
                    </div>
                </div>

            <?php elseif ($userRole === 'admin'): ?>
                <!-- Dashboard -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'dashboard' || $currentUrl === 'dashboard/admin') ? 'active' : '' ?>"
                       href="<?= base_url('dashboard') ?>" title="Dashboard">
                        <i class="bi bi-speedometer2 nav-icon"></i>
                        <span class="sidebar-nav-text">Dashboard</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Dashboard</div>
                        <div class="flyout-subtitle">Panel Kontrol Utama</div>
                    </div>
                </div>

                <!-- Data Siswa -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/siswa') ? 'active' : '' ?>"
                       href="<?= base_url('admin/siswa') ?>" title="Data Siswa">
                        <i class="bi bi-people nav-icon"></i>
                        <span class="sidebar-nav-text">Data Siswa</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Data Siswa</div>
                        <div class="flyout-subtitle">Kelola Data Siswa &amp; Barcode</div>
                    </div>
                </div>

                <!-- Data Guru -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/guru') ? 'active' : '' ?>"
                       href="<?= base_url('admin/guru') ?>" title="Data Guru">
                        <i class="bi bi-person-badge nav-icon"></i>
                        <span class="sidebar-nav-text">Data Guru</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Data Guru</div>
                        <div class="flyout-subtitle">Kelola Data Pengajar</div>
                    </div>
                </div>

                <!-- Data Kelas -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/kelas') ? 'active' : '' ?>"
                       href="<?= base_url('admin/kelas') ?>" title="Data Kelas">
                        <i class="bi bi-door-closed nav-icon"></i>
                        <span class="sidebar-nav-text">Data Kelas</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Data Kelas</div>
                        <div class="flyout-subtitle">Daftar Rombongan Belajar</div>
                    </div>
                </div>

                <!-- Data Jurusan -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/jurusan') ? 'active' : '' ?>"
                       href="<?= base_url('admin/jurusan') ?>" title="Data Jurusan">
                        <i class="bi bi-diagram-3 nav-icon"></i>
                        <span class="sidebar-nav-text">Data Jurusan</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Data Jurusan</div>
                        <div class="flyout-subtitle">Program Keahlian SMK</div>
                    </div>
                </div>

                <!-- Data Mata Pelajaran -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/mata_pelajaran') ? 'active' : '' ?>"
                       href="<?= base_url('admin/mata_pelajaran') ?>" title="Data Mata Pelajaran">
                        <i class="bi bi-journal-text nav-icon"></i>
                        <span class="sidebar-nav-text">Mata Pelajaran</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Mata Pelajaran</div>
                        <div class="flyout-subtitle">Kurikulum &amp; Mapel</div>
                    </div>
                </div>

                <!-- Data Jadwal Pelajaran -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/jadwal_pelajaran') ? 'active' : '' ?>"
                       href="<?= base_url('admin/jadwal_pelajaran') ?>" title="Data Jadwal Pelajaran">
                        <i class="bi bi-calendar-week nav-icon"></i>
                        <span class="sidebar-nav-text">Jadwal Pelajaran</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Jadwal Pelajaran</div>
                        <div class="flyout-subtitle">Jadwal KBM Harian</div>
                    </div>
                </div>

                <!-- Tahun Ajaran -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/tahun-ajaran') ? 'active' : '' ?>"
                       href="<?= base_url('admin/tahun-ajaran') ?>" title="Tahun Ajaran">
                        <i class="bi bi-clock-history nav-icon"></i>
                        <span class="sidebar-nav-text">Tahun Ajaran</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Tahun Ajaran</div>
                        <div class="flyout-subtitle">Periode &amp; Semester Aktif</div>
                    </div>
                </div>

                <!-- Rekap Absensi -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/rekap') ? 'active' : '' ?>"
                       href="<?= base_url('admin/rekap') ?>" title="Rekap Absensi">
                        <i class="bi bi-bar-chart-line nav-icon"></i>
                        <span class="sidebar-nav-text">Rekap Absensi</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Rekap Absensi</div>
                        <div class="flyout-subtitle">Laporan Kehadiran Sekolah</div>
                    </div>
                </div>

                <!-- Kelola Pengajuan -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/pengajuan_izin') ? 'active' : '' ?>"
                       href="<?= base_url('admin/pengajuan_izin') ?>" title="Kelola Pengajuan">
                        <i class="bi bi-envelope-check nav-icon"></i>
                        <span class="sidebar-nav-text">Kelola Pengajuan</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Kelola Pengajuan</div>
                        <div class="flyout-subtitle">Persetujuan Izin &amp; Sakit</div>
                    </div>
                </div>

                <!-- AI Chatbot -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/pengaturan_ai' || $currentUrl === 'admin/pengaturan-ai') ? 'active' : '' ?>"
                       href="<?= base_url('admin/pengaturan_ai') ?>" title="Konfigurasi AI Chatbot">
                        <i class="bi bi-robot nav-icon"></i>
                        <span class="sidebar-nav-text">AI Chatbot</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">AI Chatbot</div>
                        <div class="flyout-subtitle">Konfigurasi Asisten AI</div>
                    </div>
                </div>

                <!-- Pengaturan -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'admin/pengaturan') ? 'active' : '' ?>"
                       href="<?= base_url('admin/pengaturan') ?>" title="Pengaturan">
                        <i class="bi bi-gear nav-icon"></i>
                        <span class="sidebar-nav-text">Pengaturan</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Pengaturan Sistem</div>
                        <div class="flyout-subtitle">Konfigurasi Jam &amp; Sekolah</div>
                    </div>
                </div>

            <?php elseif ($userRole === 'kepala_sekolah' || $userRole === 'kepsek'): ?>
                <!-- Dashboard -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'dashboard' || $currentUrl === 'dashboard/kepala_sekolah' || $currentUrl === 'dashboard/kepsek') ? 'active' : '' ?>"
                       href="<?= base_url('dashboard') ?>" title="Dashboard">
                        <i class="bi bi-speedometer2 nav-icon"></i>
                        <span class="sidebar-nav-text">Dashboard</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Dashboard</div>
                        <div class="flyout-subtitle">Panel Kontrol Kepala Sekolah</div>
                    </div>
                </div>

                <!-- Pemantauan Absensi -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'kepala-sekolah/laporan') ? 'active' : '' ?>"
                       href="<?= base_url('kepala-sekolah/laporan') ?>" title="Pemantauan Absensi">
                        <i class="bi bi-file-earmark-bar-graph nav-icon"></i>
                        <span class="sidebar-nav-text">Pemantauan Absensi</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Pemantauan Absensi</div>
                        <div class="flyout-subtitle">Monitoring Presensi Guru &amp; Siswa</div>
                    </div>
                </div>

                <!-- Catatan untuk Guru -->
                <div class="sidebar-item-wrapper">
                    <a class="sidebar-nav-link <?= ($currentUrl === 'kepala-sekolah/catatan') ? 'active' : '' ?>"
                       href="<?= base_url('kepala-sekolah/catatan') ?>" title="Catatan untuk Guru">
                        <i class="bi bi-journal-text nav-icon"></i>
                        <span class="sidebar-nav-text">Catatan untuk Guru</span>
                    </a>
                    <div class="sidebar-flyout">
                        <div class="flyout-title">Catatan untuk Guru</div>
                        <div class="flyout-subtitle">Arahan &amp; Evaluasi Pengajar</div>
                    </div>
                </div>
            <?php endif; ?>
        </nav>

        <!-- Bagian Bawah: Tombol Logout -->
        <div class="sidebar-bottom">
            <div class="sidebar-item-wrapper">
                <a href="<?= base_url('auth/logout') ?>" class="sidebar-logout-link" title="Keluar">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="sidebar-logout-text">Keluar (Logout)</span>
                </a>
                <div class="sidebar-flyout">
                    <div class="flyout-title text-danger">Keluar (Logout)</div>
                    <div class="flyout-subtitle">Akhiri sesi saat ini</div>
                </div>
            </div>
        </div>

    </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="container my-5 flex-grow-1">
        <?php flash(); ?>
        <?php 
            // Include the specific view file passed from the Controller
            if (isset($viewPath) && file_exists($viewPath)) {
                require_once $viewPath;
            }
        ?>
    </main>

    <!-- Footer -->
    <footer class="app-footer text-center">
        <div class="container text-muted d-flex align-items-center justify-content-center h-100">
            <small>&copy; <?= date('Y') ?> <strong style="color:var(--dark-blue);">SMK NU WARGABINANGUN</strong> — Sistem Absensi Digital by Winarti</small>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle (Local with CDN fallback) -->
    <script src="<?= base_url('public/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        if (typeof bootstrap === 'undefined') {
            const cdnScript = document.createElement('script');
            cdnScript.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js';
            document.head.appendChild(cdnScript);
        }
    </script>
    <!-- Custom JS -->
    <script src="<?= base_url('public/assets/js/script.js') ?>"></script>

    <!-- Chatbot Widget -->
    <?php if(isset($_SESSION['user_id'])): // Show chatbot only if logged in (e.g. Dashboard) ?>
    <button class="chatbot-btn" id="chatbotToggle">
        <i class="bi bi-chat-dots-fill"></i>
    </button>

    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <span><i class="bi bi-robot me-2"></i> Asisten AI Sekolah</span>
            <button type="button" class="btn-close btn-close-white" style="font-size: 12px;" id="chatbotClose"></button>
        </div>
        <div class="chatbot-body" id="chatbotBody">
            <div class="chat-msg msg-bot">Halo! Saya asisten AI sekolah. Ada yang bisa saya bantu terkait sistem absensi atau lainnya?</div>
        </div>
        <div class="chatbot-footer">
            <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Ketik pesan di sini...">
            <button class="chatbot-send" id="chatbotSend"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('chatbotToggle');
            const closeBtn = document.getElementById('chatbotClose');
            const chatWindow = document.getElementById('chatbotWindow');
            const sendBtn = document.getElementById('chatbotSend');
            const chatInput = document.getElementById('chatbotInput');
            const chatBody = document.getElementById('chatbotBody');
            
            let chatHistory = [];

            // Toggle chatbot window
            toggleBtn.addEventListener('click', () => {
                chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
            });

            closeBtn.addEventListener('click', () => {
                chatWindow.style.display = 'none';
            });

            const appendMessage = (text, sender) => {
                const msgDiv = document.createElement('div');
                msgDiv.className = `chat-msg ${sender === 'user' ? 'msg-user' : 'msg-bot'}`;
                let cleanText = typeof text === 'string' ? text.trim() : text;
                if (sender === 'bot' && typeof cleanText === 'string') {
                    cleanText = cleanText.replace(/\*/g, '');
                }
                msgDiv.textContent = cleanText;
                chatBody.appendChild(msgDiv);
                chatBody.scrollTop = chatBody.scrollHeight;
            };

            const sendMessage = async () => {
                const text = chatInput.value.trim();
                if (!text) return;

                // UI update
                appendMessage(text, 'user');
                chatInput.value = '';
                
                // Show loading
                const loadingId = 'loading-' + Date.now();
                const loadDiv = document.createElement('div');
                loadDiv.id = loadingId;
                loadDiv.className = 'chat-msg msg-bot text-muted';
                loadDiv.innerHTML = '<i class="bi bi-three-dots"></i> Mengetik...';
                chatBody.appendChild(loadDiv);
                chatBody.scrollTop = chatBody.scrollHeight;

                try {
                    // This calls the PHP backend created in C:\xampp\htdocs\absen\chat_api.php
                    const response = await fetch('<?= base_url('chat_api.php') ?>', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ message: text, history: chatHistory })
                    });
                    
                    const data = await response.json();
                    document.getElementById(loadingId).remove();
                    
                    if (response.ok && data.reply) {
                        appendMessage(data.reply, 'bot');
                        chatHistory.push({ role: 'user', content: text });
                        chatHistory.push({ role: 'assistant', content: data.reply });
                    } else {
                        console.error('Chat error:', data);
                        let detailMsg = data.details || data.error || 'Server error';
                        appendMessage('API Error: ' + detailMsg + ' (Silakan periksa API Key atau model di menu Pengaturan AI)', 'bot');
                    }
                } catch (error) {
                    document.getElementById(loadingId).remove();
                    console.error('Chat error:', error);
                    appendMessage('Gagal terhubung ke server Chatbot.', 'bot');
                }
            };

            sendBtn.addEventListener('click', sendMessage);
            chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        });
    </script>
    <?php endif; ?>

    <!-- Script Pengaturan Tema Terang & Gelap (Light / Dark Mode) -->
    <script>
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('app_theme', theme);
            
            // Update quick toggle button icon & guest icon
            const quickIcon = document.getElementById('quickThemeIcon');
            const guestIcon = document.getElementById('guestThemeIcon');
            [quickIcon, guestIcon].forEach(ic => {
                if (ic) {
                    if (theme === 'dark') {
                        ic.className = 'bi bi-sun-fill text-warning';
                        ic.parentElement.title = 'Beralih ke Mode Terang';
                    } else {
                        ic.className = 'bi bi-moon-stars-fill text-primary';
                        ic.parentElement.title = 'Beralih ke Mode Gelap';
                    }
                }
            });
            
            // Update dropdown switch & text
            const dropSwitch = document.getElementById('dropdownThemeSwitch');
            const dropIcon = document.getElementById('dropdownThemeIcon');
            const dropTitle = document.getElementById('dropdownThemeTitle');
            const dropSub = document.getElementById('dropdownThemeSub');
            
            if (dropSwitch) dropSwitch.checked = (theme === 'dark');
            if (dropIcon) {
                dropIcon.className = theme === 'dark' ? 'bi bi-sun-fill fs-5 text-warning' : 'bi bi-moon-stars-fill fs-5 text-warning';
            }
            if (dropTitle) {
                dropTitle.textContent = theme === 'dark' ? 'Mode Gelap (Aktif)' : 'Mode Terang (Aktif)';
            }
            if (dropSub) {
                dropSub.textContent = theme === 'dark' ? 'Klik untuk beralih ke Mode Terang' : 'Klik untuk beralih ke Mode Gelap';
            }
        }

        function toggleTheme(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('app_theme') || 'light';
            applyTheme(savedTheme);
        });

        // Live Clock Real-time (Detik Berjalan) untuk Dashboard Semua Role
        function updateLiveClocks() {
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const now = new Date();
            const dayName = days[now.getDay()];
            const day = String(now.getDate()).padStart(2, '0');
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const fullDateStr = `${dayName}, ${day} ${monthName} ${year}`;
            const timeStr = `${hours}:${minutes}:${seconds} WIB`;

            document.querySelectorAll('.live-clock-date').forEach(el => el.textContent = fullDateStr);
            document.querySelectorAll('.live-clock-time').forEach(el => el.textContent = timeStr);
        }
        setInterval(updateLiveClocks, 1000);
        document.addEventListener('DOMContentLoaded', updateLiveClocks);

        // =========================================================================
        // SIDEBAR MINI RAIL / EXPANDED (Sesuai Desain Gambar)
        // =========================================================================

        function toggleSidebarExpand() {
            const isExpanded = document.body.classList.toggle('sidebar-expanded');
            localStorage.setItem('sidebar_expanded', isExpanded ? '1' : '0');
        }

        function openMobileSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.add('mobile-show');
            if (backdrop) backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileSidebar(e) {
            if (e && e.preventDefault) e.preventDefault();
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.remove('mobile-show');
            if (backdrop) backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }

        function toggleMobileSidebar(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const sidebar = document.getElementById('appSidebar');
            if (!sidebar) return;
            if (sidebar.classList.contains('mobile-show')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        }

        function handleSidebarHamburgerClick(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            if (window.innerWidth < 992) {
                closeMobileSidebar();
            } else {
                toggleSidebarExpand();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Restore status expanded pada layar desktop jika sebelumnya disimpan
            if (window.innerWidth >= 992 && localStorage.getItem('sidebar_expanded') === '1') {
                document.body.classList.add('sidebar-expanded');
            }

            // Pada layar mobile, tutup drawer sidebar saat navigasi link di-klik
            const sidebar = document.getElementById('appSidebar');
            if (sidebar) {
                sidebar.querySelectorAll('.sidebar-nav-link, .sidebar-logout-link').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 992) {
                            closeMobileSidebar();
                        }
                    });
                });
            }

            // Tutup sidebar mobile saat tombol ESC ditekan
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMobileSidebar();
                }
            });
        });
    </script>
</body>
</html>
