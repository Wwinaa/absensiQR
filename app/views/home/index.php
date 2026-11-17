<div class="row align-items-center">
    <div class="col-md-7">
        <span class="badge mb-3 px-3 py-2 d-inline-flex align-items-center gap-2" style="background:var(--light-blue); color:var(--medium-blue); border-radius:20px; font-weight:600; letter-spacing:.5px;">
            <img src="<?= base_url('public/assets/images/logo_smk.png?v=2') ?>" alt="Logo" style="width:20px; height:20px; object-fit:contain;">
            SMK NU WARGABINANGUN
        </span>
        <h1 class="display-5 fw-bold mb-2" style="color:var(--dark-blue); line-height:1.2;">
            Sistem Absensi<br>
            <span style="color:var(--medium-blue);">Digital & QR Code</span>
        </h1>
        <p class="lead text-secondary mb-4">
            <?= isset($description) ? $description : 'Sistem absensi modern berbasis QR Code untuk SMK NU WARGABINANGUN.' ?>
        </p>
        
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('auth/login') ?>" class="btn btn-soft-blue btn-lg px-4 shadow-sm">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
            </a>
        </div>
    </div>
    <div class="col-md-5 text-center mt-4 mt-md-0">
        <div class="p-4 rounded-4 shadow-sm" style="background:white; border:1px solid var(--border-blue);">
            <div class="mb-3 text-center">
                <img src="<?= base_url('public/assets/images/logo_smk.png?v=2') ?>" alt="Logo SMK NU WARGABINANGUN"
                     style="width:125px; height:125px; object-fit:contain; filter:drop-shadow(0 6px 16px rgba(0,0,0,0.15)); transition:transform 0.3s ease;"
                     onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            </div>
            <h5 class="fw-bold mt-2" style="color:var(--dark-blue);">SMK NU WARGABINANGUN</h5>
            <p class="text-muted small mb-3">Sistem Absensi Siswa & Guru Berbasis QR Code</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <div class="text-center">
                    <div style="font-size:1.8rem; color:var(--medium-blue);">📱</div>
                    <small class="text-muted">QR Code</small>
                </div>
                <div class="text-center">
                    <div style="font-size:1.8rem; color:var(--medium-blue);">📊</div>
                    <small class="text-muted">Rekap</small>
                </div>
                <div class="text-center">
                    <div style="font-size:1.8rem; color:var(--medium-blue);">🤖</div>
                    <small class="text-muted">AI Chat</small>
                </div>
            </div>
        </div>
    </div>
</div>

