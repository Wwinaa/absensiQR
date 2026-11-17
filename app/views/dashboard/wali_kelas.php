<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold mb-1" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
        <p class="text-secondary small mb-0">Selamat datang, <strong><?= htmlspecialchars($wali_kelas->nama_lengkap ?? $_SESSION['user_username']) ?></strong> &bull; Wali Kelas <?= htmlspecialchars($wali_kelas->nama_kelas ?? '-') ?></p>
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
        <a href="<?= base_url('absensi/scan') ?>" class="btn-soft-blue">
            <i class="bi bi-qr-code-scan me-1"></i> Scan QR Presensi
        </a>
    </div>
</div>

<!-- Presensi Pribadi Wali Kelas Hari Ini -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-modern h-100 border-0 p-3 personal-presensi-card" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-left: 5px solid var(--primary-blue) !important;">
            <div class="card-body p-2">
                <span class="text-uppercase fw-bold small text-primary mb-1 d-block">Presensi Pribadi Hari Ini</span>
                <h4 class="fw-bold mb-2" style="color: var(--dark-blue);">
                    <?php if (date('N') == 7): ?>
                        <span class="fs-5 fw-bold text-secondary">HARI LIBUR</span>
                    <?php elseif (!empty($absensi) && in_array(strtolower($absensi->status), ['hadir', 'izin', 'sakit'])): ?>
                        <?php 
                            $badgeCls = 'bg-success';
                            $iconCls = 'bi-check-circle-fill';
                            if (strtolower($absensi->status) === 'izin') {
                                $badgeCls = 'bg-info text-white';
                                $iconCls = 'bi-info-circle-fill';
                            } elseif (strtolower($absensi->status) === 'sakit') {
                                $badgeCls = 'bg-warning text-dark';
                                $iconCls = 'bi-heart-pulse-fill';
                            }
                        ?>
                        <span class="badge <?= $badgeCls ?> px-3 py-2 fs-6"><i class="bi <?= $iconCls ?> me-1"></i> <?= strtoupper($absensi->status) ?></span>
                    <?php elseif (!empty($absensi) && strtolower($absensi->status) === 'alpa' && empty($absensi->jam_masuk)): ?>
                        <span class="badge bg-danger px-3 py-2 fs-6"><i class="bi bi-x-circle-fill me-1"></i> ALPA</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6"><i class="bi bi-hourglass-split me-1"></i> BELUM SCAN</span>
                    <?php endif; ?>
                </h4>
                <div class="small text-secondary mt-2">
                    <div>Jam Masuk: <strong><?= (!empty($absensi->jam_masuk) && $absensi->jam_masuk !== '00:00:00') ? date('H:i', strtotime($absensi->jam_masuk)) . ' WIB' : '-' ?></strong></div>
                    <div>Jam Pulang: <strong><?= (!empty($absensi->jam_pulang) && $absensi->jam_pulang !== '00:00:00') ? date('H:i', strtotime($absensi->jam_pulang)) . ' WIB' : '-' ?></strong></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #10B981 !important;">
            <div class="card-body p-2">
                <span class="text-uppercase fw-bold small text-secondary mb-1 d-block">Kelas Tanggung Jawab</span>
                <h3 class="fw-bold mb-1" style="color: var(--dark-blue);"><?= htmlspecialchars($wali_kelas->nama_kelas ?? 'Belum Ditugaskan') ?></h3>
                <div class="small text-muted">
                    Total Siswa: <strong><?= $stats['total_siswa'] ?? 0 ?> siswa</strong>
                </div>
                <div class="mt-2">
                    <a href="<?= base_url('wali-kelas/pemantauan') ?>" class="text-primary small fw-semibold text-decoration-none">
                        Buka Pemantauan Kelas <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #7c3aed !important;">
            <div class="card-body p-2">
                <span class="text-uppercase fw-bold small text-secondary mb-1 d-block">Tugas Mengajar</span>
                <h3 class="fw-bold mb-1" style="color: #7c3aed;"><?= count($jadwal) ?> Jam Hari Ini</h3>
                <div class="small text-muted">Guru Pengajar & Pembinaan</div>
                <div class="mt-2">
                    <a href="<?= base_url('wali-kelas/jadwal') ?>" class="text-primary small fw-semibold text-decoration-none">
                        Lihat Seluruh Jadwal <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal Mengajar Hari Ini -->
<div class="card card-modern mb-4 border-0">
    <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center border-bottom" style="border-color: var(--border-blue) !important; color: var(--dark-blue);">
        <div>
            <i class="bi bi-calendar-event me-2" style="color: var(--medium-blue);"></i> Jadwal Mengajar Anda Hari Ini
        </div>
        <a href="<?= base_url('wali-kelas/jadwal') ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">
            Semua Jadwal
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-modern-container shadow-none border-0">
            <table class="table table-modern mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Jam Pelajaran</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jadwal)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-check fs-2 d-block mb-2 text-primary opacity-50"></i>
                                Tidak ada jadwal mengajar pada hari ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($jadwal as $j): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= substr($j->jam_mulai, 0, 5) ?> - <?= substr($j->jam_selesai, 0, 5) ?></span></td>
                            <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($j->nama_mapel) ?></strong></td>
                            <td><span class="badge bg-primary text-white" style="border-radius: 6px;"><?= htmlspecialchars($j->nama_kelas) ?></span></td>
                            <td><?= htmlspecialchars($j->nama_jurusan ?? '-') ?></td>
                            <td><span class="badge bg-success" style="border-radius: 6px;">Terjadwal</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Monitoring Kehadiran Siswa Kelas Tanggung Jawab -->
<div class="card card-modern border-0">
    <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center border-bottom" style="border-color: var(--border-blue) !important; color: var(--dark-blue);">
        <div>
            <i class="bi bi-people-fill me-2" style="color: var(--medium-blue);"></i> Ringkasan Kehadiran Siswa (Kelas <?= htmlspecialchars($wali_kelas->nama_kelas ?? '-') ?>) Hari Ini
        </div>
        <div class="gap-2 d-flex">
            <a href="<?= base_url('wali-kelas/pemantauan') ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">
                <i class="bi bi-eye me-1"></i> Pemantauan Lengkap
            </a>
            <a href="<?= base_url('wali-kelas/catatan-pembinaan') ?>" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px;">
                <i class="bi bi-journal-text me-1"></i> Catatan Pembinaan
            </a>
        </div>
    </div>
    <div class="card-body p-4">
        <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1"></i> Klik pada kotak ringkasan di bawah untuk melihat daftar nama siswa:</p>
        <div class="row g-3 text-center">
            <!-- 1. Total Siswa -->
            <div class="col-6 col-md-3">
                <div class="p-3 rounded h-100 shadow-sm clickable-card summary-card-total" style="background-color: #f8fafc; border: 1px solid #e2e8f0; cursor: pointer; transition: all 0.2s ease;" 
                     data-bs-toggle="modal" data-bs-target="#modalDetailTotal" title="Klik untuk melihat semua siswa">
                    <div class="small text-muted mb-1">Total Siswa</div>
                    <h3 class="fw-bold mb-1 text-dark"><?= $stats['total_siswa'] ?? 0 ?></h3>
                    <span class="badge bg-light text-secondary border small" style="font-size: 0.7rem;"><i class="bi bi-eye me-1"></i>Lihat Semua</span>
                </div>
            </div>
            <!-- 2. Hadir Hari Ini -->
            <div class="col-6 col-md-3">
                <div class="p-3 rounded h-100 shadow-sm clickable-card summary-card-hadir" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; cursor: pointer; transition: all 0.2s ease;"
                     data-bs-toggle="modal" data-bs-target="#modalDetailHadir" title="Klik untuk melihat siswa yang hadir">
                    <div class="small text-success mb-1 fw-semibold">Hadir Hari Ini</div>
                    <h3 class="fw-bold mb-1 text-success"><?= (date('N') >= 6) ? '-' : ($stats['hadir'] ?? 0) ?></h3>
                    <span class="badge bg-success-subtle text-success border border-success-subtle small" style="font-size: 0.7rem;"><i class="bi bi-eye me-1"></i>Lihat Nama</span>
                </div>
            </div>
            <!-- 3. Izin / Sakit -->
            <div class="col-6 col-md-3">
                <div class="p-3 rounded h-100 shadow-sm clickable-card summary-card-izinsakit" style="background-color: #eff6ff; border: 1px solid #bfdbfe; cursor: pointer; transition: all 0.2s ease;"
                     data-bs-toggle="modal" data-bs-target="#modalDetailIzinSakit" title="Klik untuk melihat siswa yang izin atau sakit">
                    <div class="small text-primary mb-1 fw-semibold">Izin / Sakit</div>
                    <h3 class="fw-bold mb-1 text-primary"><?= (date('N') >= 6) ? '-' : (($stats['izin'] ?? 0) + ($stats['sakit'] ?? 0)) ?></h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle small" style="font-size: 0.7rem;"><i class="bi bi-eye me-1"></i>Lihat Nama</span>
                </div>
            </div>
            <!-- 4. Alpa Hari Ini -->
            <div class="col-6 col-md-3">
                <div class="p-3 rounded h-100 shadow-sm clickable-card summary-card-alpa" style="background-color: #fef2f2; border: 1px solid #fecaca; cursor: pointer; transition: all 0.2s ease;"
                     data-bs-toggle="modal" data-bs-target="#modalDetailAlpa" title="Klik untuk melihat siswa yang alpa / tidak hadir">
                    <div class="small text-danger mb-1 fw-semibold">Alpa Hari Ini</div>
                    <h3 class="fw-bold mb-1 text-danger"><?= (date('N') >= 6) ? '-' : ($stats['alpa'] ?? 0) ?></h3>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle small" style="font-size: 0.7rem;"><i class="bi bi-eye me-1"></i>Lihat Nama</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.clickable-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08) !important;
}

/* ================= DARK MODE STYLES FOR WALI KELAS DASHBOARD ================= */
[data-bs-theme="dark"] .personal-presensi-card {
    background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%) !important;
    border-left: 5px solid var(--primary-blue) !important;
}
[data-bs-theme="dark"] .summary-card-total {
    background-color: #1E293B !important;
    border-color: #334155 !important;
}
[data-bs-theme="dark"] .summary-card-total .text-dark {
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .summary-card-total .badge {
    background-color: #0F172A !important;
    color: #94A3B8 !important;
    border-color: #334155 !important;
}
[data-bs-theme="dark"] .summary-card-hadir {
    background-color: rgba(16, 185, 129, 0.15) !important;
    border-color: rgba(16, 185, 129, 0.4) !important;
}
[data-bs-theme="dark"] .summary-card-hadir h3,
[data-bs-theme="dark"] .summary-card-hadir .small {
    color: #34D399 !important;
}
[data-bs-theme="dark"] .summary-card-hadir .badge {
    background-color: rgba(16, 185, 129, 0.25) !important;
    color: #6EE7B7 !important;
    border-color: rgba(16, 185, 129, 0.4) !important;
}
[data-bs-theme="dark"] .summary-card-izinsakit {
    background-color: rgba(59, 130, 246, 0.15) !important;
    border-color: rgba(59, 130, 246, 0.4) !important;
}
[data-bs-theme="dark"] .summary-card-izinsakit h3,
[data-bs-theme="dark"] .summary-card-izinsakit .small {
    color: #60A5FA !important;
}
[data-bs-theme="dark"] .summary-card-izinsakit .badge {
    background-color: rgba(59, 130, 246, 0.25) !important;
    color: #93C5FD !important;
    border-color: rgba(59, 130, 246, 0.4) !important;
}
[data-bs-theme="dark"] .summary-card-alpa {
    background-color: rgba(239, 68, 68, 0.15) !important;
    border-color: rgba(239, 68, 68, 0.4) !important;
}
[data-bs-theme="dark"] .summary-card-alpa h3,
[data-bs-theme="dark"] .summary-card-alpa .small {
    color: #F87171 !important;
}
[data-bs-theme="dark"] .summary-card-alpa .badge {
    background-color: rgba(239, 68, 68, 0.25) !important;
    color: #FCA5A5 !important;
    border-color: rgba(239, 68, 68, 0.4) !important;
}

/* Modal Dark Theme Overrides */
[data-bs-theme="dark"] .modal-header.bg-light,
[data-bs-theme="dark"] .modal-footer.bg-light {
    background-color: #0F172A !important;
    border-color: #334155 !important;
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .modal-header.bg-success-subtle {
    background-color: #064E3B !important;
    color: #A7F3D0 !important;
    border-color: #047857 !important;
}
[data-bs-theme="dark"] .modal-header.bg-success-subtle h5,
[data-bs-theme="dark"] .modal-header.bg-success-subtle small {
    color: #A7F3D0 !important;
}
[data-bs-theme="dark"] .modal-header.bg-primary-subtle {
    background-color: #1E3A8A !important;
    color: #BFDBFE !important;
    border-color: #1D4ED8 !important;
}
[data-bs-theme="dark"] .modal-header.bg-primary-subtle h5,
[data-bs-theme="dark"] .modal-header.bg-primary-subtle small {
    color: #BFDBFE !important;
}
[data-bs-theme="dark"] .modal-header.bg-danger-subtle {
    background-color: #7F1D1D !important;
    color: #FECACA !important;
    border-color: #B91C1C !important;
}
[data-bs-theme="dark"] .modal-header.bg-danger-subtle h5,
[data-bs-theme="dark"] .modal-header.bg-danger-subtle small {
    color: #FECACA !important;
}
[data-bs-theme="dark"] .modal-content thead.table-light th,
[data-bs-theme="dark"] .modal-content thead th {
    background-color: #0F172A !important;
    color: #F1F5F9 !important;
    border-bottom: 2px solid #334155 !important;
}
[data-bs-theme="dark"] .modal-content tbody td {
    background-color: #1E293B !important;
    color: #CBD5E1 !important;
    border-bottom-color: #334155 !important;
}
[data-bs-theme="dark"] .modal-content tbody td strong {
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .modal-content .table-striped > tbody > tr:nth-of-type(odd) > * {
    background-color: #1E293B !important;
    color: #CBD5E1 !important;
}
[data-bs-theme="dark"] .modal-content .table-striped > tbody > tr:nth-of-type(even) > * {
    background-color: #162236 !important;
    color: #CBD5E1 !important;
}
[data-bs-theme="dark"] .modal-content .badge.bg-light {
    background-color: #0F172A !important;
    color: #F1F5F9 !important;
    border-color: #334155 !important;
}
</style>

<!-- ========================================================
     MODALS: Detail Kehadiran Siswa
     ======================================================== -->

<!-- Modal 1: Total Siswa (Semua Status) -->
<div class="modal fade" id="modalDetailTotal" tabindex="-1" aria-labelledby="modalDetailTotalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-light py-3 px-4 border-bottom" style="border-color: var(--border-blue) !important;">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="modalDetailTotalLabel" style="color: var(--dark-blue);">
                        <i class="bi bi-people-fill me-2" style="color: var(--medium-blue);"></i> Daftar Seluruh Siswa (Kelas <?= htmlspecialchars($wali_kelas->nama_kelas ?? '-') ?>)
                    </h5>
                    <small class="text-muted">Total: <?= count($stats['list_semua'] ?? []) ?> Siswa</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height: 420px;">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="6%">No</th>
                                <th>Nama Lengkap</th>
                                <th>NIS</th>
                                <th>Status Hari Ini</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stats['list_semua'])): ?>
                                <?php $no = 1; foreach ($stats['list_semua'] as $sw): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($sw->nama_lengkap) ?></strong></td>
                                    <td><span class="text-muted"><?= htmlspecialchars($sw->nis) ?></span></td>
                                    <td>
                                        <?php 
                                            $st = strtolower($sw->status ?? '');
                                            if ($st === 'hadir'): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hadir (<?= !empty($sw->jam_masuk) ? date('H:i', strtotime($sw->jam_masuk)) : '-' ?>)</span>
                                            <?php elseif ($st === 'izin'): ?>
                                                <span class="badge bg-info text-dark"><i class="bi bi-info-circle me-1"></i>Izin</span>
                                            <?php elseif ($st === 'sakit'): ?>
                                                <span class="badge bg-warning text-dark"><i class="bi bi-heart-pulse me-1"></i>Sakit</span>
                                            <?php elseif ($st === 'alpa' || $st === 'bolos'): ?>
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Alpa</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($sw->status) ?></span>
                                            <?php endif; ?>
                                    </td>
                                    <td class="small text-muted"><?= htmlspecialchars($sw->keterangan ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data siswa di kelas ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-sm btn-secondary px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Hadir Hari Ini -->
<div class="modal fade" id="modalDetailHadir" tabindex="-1" aria-labelledby="modalDetailHadirLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-success-subtle py-3 px-4 border-bottom border-success-subtle">
                <div>
                    <h5 class="modal-title fw-bold mb-0 text-success" id="modalDetailHadirLabel">
                        <i class="bi bi-check-circle-fill me-2"></i> Siswa Hadir Hari Ini (Kelas <?= htmlspecialchars($wali_kelas->nama_kelas ?? '-') ?>)
                    </h5>
                    <small class="text-success-emphasis">Total Hadir: <?= count($stats['list_hadir'] ?? []) ?> Siswa</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height: 420px;">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="6%">No</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stats['list_hadir'])): ?>
                                <?php $no = 1; foreach ($stats['list_hadir'] as $sw): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($sw->nama_lengkap) ?></strong></td>
                                    <td><span class="text-muted"><?= htmlspecialchars($sw->nis) ?></span></td>
                                    <td><span class="badge bg-light text-dark border"><i class="bi bi-clock me-1 text-success"></i><?= !empty($sw->jam_masuk) ? date('H:i', strtotime($sw->jam_masuk)) . ' WIB' : '-' ?></span></td>
                                    <td><span class="badge bg-light text-dark border"><?= !empty($sw->jam_pulang) ? date('H:i', strtotime($sw->jam_pulang)) . ' WIB' : '-' ?></span></td>
                                    <td><span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Hadir</span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> Belum ada siswa yang melakukan presensi hadir hari ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-sm btn-secondary px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Izin / Sakit Hari Ini -->
<div class="modal fade" id="modalDetailIzinSakit" tabindex="-1" aria-labelledby="modalDetailIzinSakitLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary-subtle py-3 px-4 border-bottom border-primary-subtle">
                <div>
                    <h5 class="modal-title fw-bold mb-0 text-primary" id="modalDetailIzinSakitLabel">
                        <i class="bi bi-envelope-check-fill me-2"></i> Siswa Izin / Sakit Hari Ini (Kelas <?= htmlspecialchars($wali_kelas->nama_kelas ?? '-') ?>)
                    </h5>
                    <small class="text-primary-emphasis">Izin: <?= count($stats['list_izin'] ?? []) ?> | Sakit: <?= count($stats['list_sakit'] ?? []) ?></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height: 420px;">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="6%">No</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Jenis</th>
                                <th>Alasan / Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $combined = array_merge($stats['list_izin'] ?? [], $stats['list_sakit'] ?? []);
                            ?>
                            <?php if (!empty($combined)): ?>
                                <?php $no = 1; foreach ($combined as $sw): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($sw->nama_lengkap) ?></strong></td>
                                    <td><span class="text-muted"><?= htmlspecialchars($sw->nis) ?></span></td>
                                    <td>
                                        <?php if (strtolower($sw->status) === 'sakit'): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-heart-pulse me-1"></i>Sakit</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark"><i class="bi bi-envelope me-1"></i>Izin</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($sw->keterangan ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> Tidak ada siswa yang izin atau sakit hari ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-sm btn-secondary px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 4: Alpa Hari Ini -->
<div class="modal fade" id="modalDetailAlpa" tabindex="-1" aria-labelledby="modalDetailAlpaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-danger-subtle py-3 px-4 border-bottom border-danger-subtle">
                <div>
                    <h5 class="modal-title fw-bold mb-0 text-danger" id="modalDetailAlpaLabel">
                        <i class="bi bi-x-circle-fill me-2"></i> Siswa Alpa / Tidak Hadir (Kelas <?= htmlspecialchars($wali_kelas->nama_kelas ?? '-') ?>)
                    </h5>
                    <small class="text-danger-emphasis">Total Alpa: <?= count($stats['list_alpa'] ?? []) ?> Siswa</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height: 420px;">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="6%">No</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stats['list_alpa'])): ?>
                                <?php $no = 1; foreach ($stats['list_alpa'] as $sw): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($sw->nama_lengkap) ?></strong></td>
                                    <td><span class="text-muted"><?= htmlspecialchars($sw->nis) ?></span></td>
                                    <td><span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Alpa</span></td>
                                    <td class="text-muted small"><?= htmlspecialchars($sw->keterangan ?? 'Tanpa Keterangan') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> Luar biasa! Tidak ada siswa yang alpa hari ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-sm btn-secondary px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

