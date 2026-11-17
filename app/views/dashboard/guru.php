<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--light-blue) !important;">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--dark-blue);">Dashboard <?= isset($role_name) ? htmlspecialchars($role_name) : 'Guru' ?></h1>
        <p class="text-secondary small mb-0">Portal Presensi & Jadwal Mengajar Tenaga Pendidik SMK NU WARGABINANGUN</p>
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
                    $dashboardAvatar = get_user_avatar($guru->user_id ?? $_SESSION['user_id'] ?? null); 
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
                <h4 class="fw-bold mb-1" style="color: var(--dark-blue);"><?= htmlspecialchars($guru->nama_lengkap ?? $_SESSION['user_username']) ?></h4>
                <p class="text-secondary mb-0">NIP: <?= htmlspecialchars($guru->nip ?? '-') ?></p>
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
    <div class="col-md-4 mb-4">
        <div class="card card-modern h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="bg-light rounded-circle p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: var(--very-light-blue) !important;">
                    <i class="bi bi-info-circle fs-4" style="color: var(--medium-blue);"></i>
                </div>
                <div>
                    <h6 class="text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Status Hari Ini</h6>
                    <h4 class="fw-bold mb-0" style="color: var(--dark-blue);">
                        <?php 
                        if (date('N') >= 6) {
                            echo '-';
                        } else if (!$absensi) {
                            echo 'Belum Absen';
                        } else if (empty($absensi->jam_pulang)) {
                            echo htmlspecialchars(ucfirst($absensi->status));
                        } else {
                            echo 'Lengkap (' . htmlspecialchars(ucfirst($absensi->status)) . ')';
                        }
                        ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card card-modern h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="bg-light rounded-circle p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #DEF7EC !important;">
                    <i class="bi bi-box-arrow-in-right fs-4" style="color: #03543F;"></i>
                </div>
                <div>
                    <h6 class="text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Jam Masuk</h6>
                    <h4 class="fw-bold mb-0" style="color: var(--dark-blue);">
                        <?= (date('N') < 6 && $absensi && !empty($absensi->jam_masuk)) ? htmlspecialchars($absensi->jam_masuk) : '-' ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card card-modern h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="bg-light rounded-circle p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #FDE8E8 !important;">
                    <i class="bi bi-box-arrow-right fs-4" style="color: #9B1C1C;"></i>
                </div>
                <div>
                    <h6 class="text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Jam Pulang</h6>
                    <h4 class="fw-bold mb-0" style="color: var(--dark-blue);">
                        <?= (date('N') < 6 && $absensi && !empty($absensi->jam_pulang)) ? htmlspecialchars($absensi->jam_pulang) : '-' ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Rekap Kehadiran Guru -->
    <div class="<?= !empty($stats['siswa_wali_stats']) ? 'col-md-6' : 'col-12' ?> mb-4">
        <div class="card card-modern border-0 h-100">
            <div class="card-body">
                <h5 class="fw-bold border-bottom pb-2 mb-3" style="color: var(--dark-blue);"><i class="bi bi-pie-chart me-2"></i> Rekap Kehadiran Saya (Seluruh Riwayat)</h5>
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
                <div class="mt-3 text-center">
                    <a href="<?= base_url('guru/riwayat') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4">Lihat Detail Riwayat</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Kelas Binaan Jika Wali Kelas -->
    <?php if (!empty($stats['siswa_wali_stats'])): ?>
    <div class="col-md-6 mb-4">
        <div class="card card-modern border-0 h-100">
            <div class="card-body">
                <h5 class="fw-bold border-bottom pb-2 mb-3" style="color: var(--dark-blue);"><i class="bi bi-people me-2"></i> Status Kelas <?= htmlspecialchars($stats['siswa_wali_stats']['kelas_nama']) ?> Hari Ini</h5>
                <div class="row text-center mt-3 g-2">
                    <div class="col-12 mb-2">
                        <div class="p-2 border rounded bg-light">
                            <span class="text-secondary fw-bold">Total Siswa:</span> <span class="fw-bold fs-5 text-dark ms-2"><?= $stats['siswa_wali_stats']['total_siswa'] ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100">
                            <div class="small text-muted fw-bold mb-1">Hadir</div>
                            <div class="fs-5 fw-bold text-success"><?= $stats['siswa_wali_stats']['hadir'] ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100">
                            <div class="small text-muted fw-bold mb-1">Izin</div>
                            <div class="fs-5 fw-bold text-info"><?= $stats['siswa_wali_stats']['izin'] ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100">
                            <div class="small text-muted fw-bold mb-1">Sakit</div>
                            <div class="fs-5 fw-bold text-primary"><?= $stats['siswa_wali_stats']['sakit'] ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100">
                            <div class="small text-muted fw-bold mb-1">Alpa</div>
                            <div class="fs-5 fw-bold text-danger"><?= $stats['siswa_wali_stats']['alpa'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="table-modern-container mb-5">
    <div class="p-4 border-bottom" style="border-color: var(--light-blue) !important;">
        <h5 class="fw-bold m-0" style="color: var(--dark-blue);"><i class="bi bi-calendar-week me-2"></i> JADWAL MENGAJAR HARI INI</h5>
    </div>
    
    <?php if (empty($jadwal)): ?>
        <div class="text-center text-secondary py-5">
            <i class="bi bi-inbox fs-1 mb-3 d-block" style="color: var(--border-blue);"></i>
            Belum ada jadwal mengajar hari ini atau jadwal belum diatur.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-modern table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="20%">Jam</th>
                        <th>Mata Pelajaran</th>
                        <th width="30%">Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jadwal as $jdw): ?>
                        <tr>
                            <td>
                                <span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue); font-size: 0.9rem;">
                                    <i class="bi bi-clock me-1"></i> <?= htmlspecialchars(substr($jdw->jam_mulai, 0, 5)) ?> - <?= htmlspecialchars(substr($jdw->jam_selesai, 0, 5)) ?>
                                </span>
                            </td>
                            <td class="fw-semibold" style="color: var(--dark-blue);"><?= htmlspecialchars($jdw->mata_pelajaran) ?></td>
                            <td><?= htmlspecialchars($jdw->kelas) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
/* ================= DARK MODE STYLES FOR GURU DASHBOARD ================= */
[data-bs-theme="dark"] .table-modern-container {
    background-color: #1E293B !important;
    border: 1px solid #334155 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
}
[data-bs-theme="dark"] .table-modern-container .border-bottom {
    border-bottom: 1px solid #334155 !important;
}
[data-bs-theme="dark"] .table-modern-container h5 {
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .table-modern thead th {
    background-color: #0F172A !important;
    color: #F1F5F9 !important;
    border-bottom: 2px solid #334155 !important;
}
[data-bs-theme="dark"] .table-modern tbody td {
    background-color: #1E293B !important;
    color: #CBD5E1 !important;
    border-bottom: 1px solid #334155 !important;
}
[data-bs-theme="dark"] .table-modern tbody td strong,
[data-bs-theme="dark"] .table-modern tbody td.fw-semibold {
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .table-modern tbody tr:hover td {
    background-color: #334155 !important;
    color: #FFFFFF !important;
}
[data-bs-theme="dark"] .badge[style*="var(--very-light-blue)"] {
    background-color: #0F172A !important;
    color: #38BDF8 !important;
    border: 1px solid #334155 !important;
}
[data-bs-theme="dark"] .bg-light.rounded-circle[style*="var(--very-light-blue)"] {
    background-color: #0F172A !important;
}
[data-bs-theme="dark"] .bg-light.rounded-circle[style*="#DEF7EC"] {
    background-color: #064E3B !important;
}
[data-bs-theme="dark"] .bg-light.rounded-circle[style*="#DEF7EC"] i {
    color: #34D399 !important;
}
[data-bs-theme="dark"] .bg-light.rounded-circle[style*="#FDE8E8"] {
    background-color: #7F1D1D !important;
}
[data-bs-theme="dark"] .bg-light.rounded-circle[style*="#FDE8E8"] i {
    color: #F87171 !important;
}
[data-bs-theme="dark"] .card-body .bg-light.border,
[data-bs-theme="dark"] .card-body .p-2.border.rounded.bg-light {
    background-color: #0F172A !important;
    border-color: #334155 !important;
    color: #F1F5F9 !important;
}
</style>
