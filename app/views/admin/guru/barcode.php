<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h3 fw-bold" style="color: var(--dark-blue);">QR Code Absensi Guru</h1>
        <p class="text-muted small mb-0">Kartu identitas QR Code 2D untuk presensi guru dan jam mengajar.</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <a href="<?= base_url('admin/guru') ?>" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnDownload" style="border-radius: 8px;">
            <i class="bi bi-download me-1"></i> Unduh QR Code
        </button>
        <button type="button" class="btn btn-sm btn-primary" onclick="window.print()" style="background-color: var(--primary-blue); border-color: var(--primary-blue); border-radius: 8px;">
            <i class="bi bi-printer me-1"></i> Cetak Kartu
        </button>
    </div>
</div>

<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow border-0 print-card text-center" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
            <!-- Header Kartu -->
            <div class="p-3 text-white" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                    <img src="<?= base_url('public/assets/images/logo_smk.png') ?>" alt="Logo" style="width:38px; height:38px; object-fit:contain; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.25));">
                    <div class="text-start">
                        <div class="fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size:0.95rem; line-height:1.15;">SMK NU WARGABINANGUN</div>
                        <div style="font-size: 0.72rem; opacity: 0.88; letter-spacing: 0.5px;">KARTU PRESENSI QR CODE GURU</div>
                    </div>
                </div>
            </div>

            <!-- Isi Kartu -->
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1" style="color: var(--dark-blue);"><?= htmlspecialchars($guru->nama_lengkap ?? $guru->nama) ?></h4>
                <div class="badge px-3 py-1 mb-3" style="font-size: 0.85rem; border-radius: 20px; <?= !empty($guru->wali_kelas_nama) ? 'color: #6d28d9; background-color: #ede9fe !important;' : 'color: #0f766e; background-color: #ccfbf1 !important;' ?>">
                    <i class="bi <?= !empty($guru->wali_kelas_nama) ? 'bi-person-workspace' : 'bi-person-badge' ?> me-1"></i>
                    <?= !empty($guru->wali_kelas_nama) ? 'Wali Kelas ' . htmlspecialchars($guru->wali_kelas_nama) : 'Guru Pengajar' ?>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-sm table-borderless text-start mx-auto mb-0" style="max-width: 280px; font-size: 0.9rem;">
                        <tr>
                            <td class="text-muted py-1" style="width: 35%;">NIP</td>
                            <td class="fw-semibold py-1">: <?= htmlspecialchars($guru->nip ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Jenis Kelamin</td>
                            <td class="fw-semibold py-1">: <?= ($guru->jenis_kelamin ?? 'L') === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Tugas / Peran</td>
                            <td class="fw-semibold py-1">: <?= !empty($guru->wali_kelas_nama) ? 'Wali Kelas ' . htmlspecialchars($guru->wali_kelas_nama) . ' & Pengajar' : 'Guru Pengajar' ?></td>
                        </tr>
                    </table>
                </div>

                <!-- QR Code 2D Square Container -->
                <div class="d-inline-block p-3 bg-light rounded-3 shadow-sm border mb-3">
                    <div id="qrcode-box" class="d-flex justify-content-center align-items-center" style="width: 200px; height: 200px; background: #fff;"></div>
                </div>

                <div class="text-muted small">
                    <div class="font-monospace fw-bold text-dark mb-1" style="letter-spacing: 1.5px;"><?= htmlspecialchars($guru->barcode) ?></div>
                    <div>Tunjukkan QR Code ini ke kamera scanner saat presensi datang & pulang.</div>
                </div>
            </div>

            <!-- Footer Kartu -->
            <div class="card-footer bg-light py-2 border-0 text-muted small">
                Berlaku selama tahun ajaran aktif &bull; SMK Negeri
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    .print-card, .print-card * {
        visibility: visible !important;
    }
    .print-card {
        position: fixed !important;
        left: 50% !important;
        top: 50px !important;
        transform: translateX(-50%) !important;
        width: 340px !important;
        box-shadow: none !important;
        border: 1px solid #ccc !important;
    }
    .btn, nav, .sidebar, header {
        display: none !important;
    }
}
</style>

<!-- Load Local QR Code Library (Offline & Fast) -->
<script src="<?= base_url('public/assets/js/qrcode.min.js') ?>"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var qrValue = "<?= htmlspecialchars($guru->barcode) ?>";
    var qrContainer = document.getElementById("qrcode-box");

    if (!qrValue) {
        qrContainer.innerHTML = '<span class="text-danger small">Kode tidak tersedia</span>';
        return;
    }

    // Render 2D Square QR Code
    var qr = new QRCode(qrContainer, {
        text: qrValue,
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    // Download functionality
    document.getElementById("btnDownload").addEventListener("click", function() {
        var canvas = qrContainer.querySelector("canvas");
        var img = qrContainer.querySelector("img");
        var dataUrl = "";

        if (canvas) {
            dataUrl = canvas.toDataURL("image/png");
        } else if (img && img.src) {
            dataUrl = img.src;
        }

        if (dataUrl) {
            var a = document.createElement("a");
            a.href = dataUrl;
            a.download = "QR_Guru_<?= preg_replace('/[^A-Za-z0-9_-]/', '_', $guru->nama_lengkap ?? $guru->nama) ?>.png";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            alert("QR Code sedang dimuat, silakan coba lagi.");
        }
    });
});
</script>
