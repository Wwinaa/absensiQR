<?php
/**
 * Flash Message Helper
 */

if (!function_exists('set_flash')) {
    function set_flash($param1 = '', $param2 = null) {
        $types = ['success', 'error', 'danger', 'warning', 'info'];
        $p1 = strtolower(trim((string)$param1));
        
        if ($param2 === null) {
            if (in_array($p1, $types)) {
                $type = $p1;
                $message = '';
            } else {
                $type = 'success';
                $message = (string)$param1;
            }
        } else {
            $p2 = strtolower(trim((string)$param2));
            if (in_array($p1, $types) && !in_array($p2, $types)) {
                $type = $p1;
                $message = (string)$param2;
            } else if (in_array($p2, $types) && !in_array($p1, $types)) {
                $message = (string)$param1;
                $type = $p2;
            } else if (in_array($p1, $types) && in_array($p2, $types)) {
                $type = $p1;
                $message = '';
            } else {
                $message = (string)$param1;
                $type = !empty($param2) ? (string)$param2 : 'success';
            }
        }
        
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type
        ];
    }
}

if (!function_exists('flash')) {
    function flash() {
        if (isset($_SESSION['flash'])) {
            $message = $_SESSION['flash']['message'] ?? '';
            $type = $_SESSION['flash']['type'] ?? 'success';
            
            $isError = in_array(strtolower((string)$type), ['error', 'danger']);
            
            if (!$isError) {
                // Success Notification (Centered Popup)
                $cleanMsg = trim((string)$message);
                $isWordInfoOrSuccess = in_array(strtolower($cleanMsg), ['info', 'success', 'berhasil', 'berhasil!']);
                $isScanPage = (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'absensi/scan') !== false);
                
                // Ketika berhasil scan absensi cukup "Berhasil!" saja, tidak ada kata 'info' lagi
                $showSubtitle = !$isScanPage && !$isWordInfoOrSuccess && !empty($cleanMsg);
                
                echo '<div id="successPopup" class="success-popup">
                        <div class="success-popup-content">
                            <div class="success-icon-wrapper">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <h4 class="success-title"' . (!$showSubtitle ? ' style="margin-bottom: 0;"' : '') . '>Berhasil!</h4>';
                if ($showSubtitle) {
                    echo '  <p class="success-message">' . htmlspecialchars($cleanMsg) . '</p>';
                }
                echo '  </div>
                      </div>';
                
                echo '<style>
                    .success-popup {
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%) scale(0.9);
                        z-index: 9999;
                        background-color: #ffffff;
                        border: 2px solid var(--soft-blue, #6ea8fe);
                        border-radius: 16px;
                        box-shadow: 0 15px 35px rgba(110, 168, 254, 0.2);
                        padding: 30px 40px;
                        text-align: center;
                        min-width: 320px;
                        max-width: 450px;
                        opacity: 0;
                        animation: popupScaleIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
                    }
                    .success-icon-wrapper {
                        width: 60px;
                        height: 60px;
                        background-color: var(--soft-blue, #6ea8fe);
                        color: white;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 32px;
                        margin: 0 auto 15px auto;
                    }
                    .success-title {
                        color: var(--primary, #0d6efd);
                        font-weight: 700;
                        margin-bottom: 10px;
                    }
                    .success-message {
                        color: #495057;
                        font-size: 16px;
                        margin-bottom: 0;
                    }
                    @keyframes popupScaleIn {
                        to {
                            transform: translate(-50%, -50%) scale(1);
                            opacity: 1;
                        }
                    }
                    .success-popup.fade-out {
                        animation: popupScaleOut 0.3s ease forwards;
                    }
                    @keyframes popupScaleOut {
                        to {
                            transform: translate(-50%, -50%) scale(0.9);
                            opacity: 0;
                        }
                    }
                    @media (max-width: 768px) {
                        .success-popup {
                            width: 90%;
                            padding: 25px 20px;
                        }
                    }
                </style>';
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const popup = document.getElementById("successPopup");
                        if (popup) {
                            setTimeout(() => {
                                popup.classList.add("fade-out");
                                setTimeout(() => popup.remove(), 300); // 300ms delay matches CSS animation
                            }, 2500); // Auto dismiss after 2.5s
                        }
                    });
                </script>';
            } else {
                // Error / Warning Notification (Centered Popup Alert)
                $titleText = (stripos($message, 'gagal') !== false) ? 'Pengajuan Gagal!' : 'Perhatian!';
                
                echo '<div id="errorPopup" class="error-popup">
                        <div class="error-popup-content">
                            <div class="error-icon-wrapper">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>
                            <h4 class="error-title">' . htmlspecialchars($titleText) . '</h4>
                            <p class="error-message">' . htmlspecialchars($message) . '</p>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-3 px-4 rounded-pill" onclick="var p = document.getElementById(\'errorPopup\'); if(p) p.remove();">
                                <i class="bi bi-x-lg me-1"></i> Tutup
                            </button>
                        </div>
                      </div>';
                
                echo '<style>
                    .error-popup {
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%) scale(0.9);
                        z-index: 99999;
                        background-color: #ffffff;
                        border: 2px solid #F87171;
                        border-radius: 18px;
                        box-shadow: 0 20px 45px rgba(220, 38, 38, 0.25);
                        padding: 30px 40px;
                        text-align: center;
                        min-width: 320px;
                        max-width: 480px;
                        width: 90%;
                        opacity: 0;
                        animation: errorPopupScaleIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
                    }
                    .error-icon-wrapper {
                        width: 68px;
                        height: 68px;
                        background-color: #FEE2E2;
                        color: #DC2626;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 34px;
                        margin: 0 auto 16px auto;
                        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
                    }
                    .error-title {
                        color: #DC2626;
                        font-weight: 700;
                        font-size: 1.25rem;
                        margin-bottom: 10px;
                    }
                    .error-message {
                        color: #374151;
                        font-size: 15px;
                        line-height: 1.5;
                        margin-bottom: 0;
                    }
                    @keyframes errorPopupScaleIn {
                        to {
                            transform: translate(-50%, -50%) scale(1);
                            opacity: 1;
                        }
                    }
                    .error-popup.fade-out {
                        animation: errorPopupScaleOut 0.3s ease forwards;
                    }
                    @keyframes errorPopupScaleOut {
                        to {
                            transform: translate(-50%, -50%) scale(0.9);
                            opacity: 0;
                        }
                    }
                    @media (max-width: 768px) {
                        .error-popup {
                            width: 92%;
                            padding: 24px 20px;
                        }
                    }
                </style>';
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const toast = document.getElementById("errorPopup");
                        if (toast) {
                            setTimeout(() => {
                                toast.classList.add("fade-out");
                                setTimeout(() => toast.remove(), 350);
                            }, 4000);
                        }
                    });
                </script>';
            }
            
            unset($_SESSION['flash']);
        }
    }
}
