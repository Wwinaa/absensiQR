<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--light-blue) !important;">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--dark-blue);">Dashboard <?= isset($role_name) ? htmlspecialchars($role_name) : 'Siswa' ?></h1>
        <p class="text-secondary small mb-0">Portal Presensi & Riwayat Kehadiran Siswa SMK NU WARGABINANGUN</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center gap-2">
        <div class="live-clock-widget d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 border shadow-sm" style="background: var(--very-light-blue); border-color: var(--border-blue) !important;">
            <div class="d-flex align-items-center gap-1">
                <i class="bi bi-calendar3 text-primary"></i>
                <span class="live-clock-date fw-semibold small text-secondary">Memuat tanggal...</span>
            </div>
            <span class="text-muted small">|</span>
            <div class="d-flex align-items-center gap-1">
                <i class="bi bi-clock-fill text-primary"></i>
                <span class="live-clock-time fw-bold small text-dark font-monospace">--:--:-- WIB</span>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="me-3">
                <?php 
                    $dashboardAvatar = get_user_avatar($siswa->user_id ?? $_SESSION['user_id'] ?? null); 
                ?>
                <?php if ($dashboardAvatar): ?>
                    <img src="<?= $dashboardAvatar ?>" class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid var(--border-blue);">
                <?php else: ?>
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border: 2px solid var(--border-blue);">
                        <i class="bi bi-person text-primary fs-1"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--dark-blue);"><?= htmlspecialchars($siswa->nama_lengkap ?? $_SESSION['user_username']) ?></h4>
                <p class="text-secondary mb-0">NIS: <?= htmlspecialchars($siswa->nis ?? '-') ?> | NISN: <?= htmlspecialchars($siswa->nisn ?? '-') ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-5 text-md-end">
        <a href="<?= base_url('absensi/scan') ?>" class="btn py-2 px-4 fw-bold d-inline-flex align-items-center gap-2" style="background-color: var(--medium-blue); color: #FFFFFF; border-radius: 50px; box-shadow: 0 4px 10px rgba(41,98,255,0.3); transition: transform 0.3s;">
            <i class="bi bi-upc-scan fs-5"></i> BUKA SCANNER ABSENSI
        </a>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12 mb-4">
        <div class="card card-modern">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-light rounded-circle p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: var(--very-light-blue) !important;">
                        <i class="bi bi-mortarboard fs-4" style="color: var(--medium-blue);"></i>
                    </div>
                    <h5 class="card-title fw-bold m-0" style="color: var(--dark-blue);">Informasi Kelas & Akademik</h5>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <span class="text-secondary d-block small fw-semibold mb-1">Kelas Saat Ini</span>
                        <strong class="fs-5" style="color: var(--dark-blue);"><?= isset($siswa) && isset($siswa->nama_kelas) ? htmlspecialchars($siswa->nama_kelas) : '-' ?></strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-secondary d-block small fw-semibold mb-1">Jurusan</span>
                        <strong class="fs-5" style="color: var(--dark-blue);"><?= isset($siswa) && isset($siswa->nama_jurusan) ? htmlspecialchars($siswa->nama_jurusan) : '-' ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-6 mb-4">
        <div class="card card-modern h-100 text-white text-center border-0" style="background: linear-gradient(135deg, var(--soft-blue) 0%, var(--medium-blue) 100%);">
            <div class="card-body p-4 d-flex flex-column justify-content-center position-relative overflow-hidden">
                <i class="bi bi-clock-history position-absolute" style="font-size: 8rem; right: -20px; bottom: -30px; opacity: 0.1;"></i>
                
                <h6 class="fw-bold mb-3 text-uppercase opacity-75">Status Kehadiran Hari Ini</h6>
                <h2 class="display-6 fw-bolder mb-4">
                    <?php 
                    if (date('N') >= 6) {
                        echo '-';
                    } else if (!isset($absensi) || !$absensi) {
                        echo 'BELUM ABSEN';
                    } else if (empty($absensi->jam_pulang)) {
                        echo htmlspecialchars(strtoupper($absensi->status));
                    } else {
                        echo 'LENGKAP (' . htmlspecialchars(strtoupper($absensi->status)) . ')';
                    }
                    ?>
                </h2>
                
                <div class="row text-center mt-2 border-top pt-3 border-light border-opacity-25 w-100 mx-0">
                    <div class="col-6 border-end border-light border-opacity-25">
                        <div class="small fw-semibold opacity-75 mb-1">Jam Masuk</div>
                        <div class="fs-5 fw-bold"><?= (date('N') < 6 && isset($absensi) && $absensi && !empty($absensi->jam_masuk)) ? htmlspecialchars($absensi->jam_masuk) : '-' ?></div>
                    </div>
                    <div class="col-6">
                        <div class="small fw-semibold opacity-75 mb-1">Jam Pulang</div>
                        <div class="fs-5 fw-bold"><?= (date('N') < 6 && isset($absensi) && $absensi && !empty($absensi->jam_pulang)) ? htmlspecialchars($absensi->jam_pulang) : '-' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card card-modern border-0 h-100">
            <div class="card-body">
                <h5 class="fw-bold border-bottom pb-2 mb-3" style="color: var(--dark-blue);"><i class="bi bi-pie-chart me-2"></i> Rekap Kehadiran (Seluruh Riwayat)</h5>
                <div class="row text-center mt-3 g-2">
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100">
                            <div class="small text-muted fw-bold mb-1">Hadir</div>
                            <div class="fs-5 fw-bold text-success"><?= $stats['total_hadir'] ?? 0 ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100">
                            <div class="small text-muted fw-bold mb-1">Izin</div>
                            <div class="fs-5 fw-bold text-info"><?= $stats['total_izin'] ?? 0 ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100">
                            <div class="small text-muted fw-bold mb-1">Sakit</div>
                            <div class="fs-5 fw-bold text-primary"><?= $stats['total_sakit'] ?? 0 ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100">
                            <div class="small text-muted fw-bold mb-1">Alpa</div>
                            <div class="fs-5 fw-bold text-danger"><?= $stats['total_alpa'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
                <div class="mt-2 text-center">
                    <a href="<?= base_url('siswa/riwayat') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4">Lihat Detail Riwayat</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dark Mode Enhancements for Siswa Dashboard */
[data-bs-theme="dark"] .bg-light.rounded-circle[style*="var(--very-light-blue)"] {
    background-color: #0F172A !important;
}
[data-bs-theme="dark"] .p-2.border.rounded.bg-light {
    background-color: #0F172A !important;
    border-color: #334155 !important;
    color: #F1F5F9 !important;
}
</style>
