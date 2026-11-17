<?php
// Hitung persentase kehadiran
$totalSiswa = $stats['total_siswa'] ?? 0;
$siswaHadir = $stats['siswa_hadir'] ?? 0;
$pctSiswa = $totalSiswa > 0 ? round(($siswaHadir / $totalSiswa) * 100, 1) : 0;

$totalGuru = $stats['total_guru'] ?? 0;
$guruHadir = $stats['guru_hadir'] ?? 0;
$pctGuru = $totalGuru > 0 ? round(($guruHadir / $totalGuru) * 100, 1) : 0;
?>

<!-- Header Dashboard -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold mb-1" style="color: var(--dark-blue);">
            <i class="bi bi-speedometer2 me-2" style="color: var(--medium-blue);"></i>Dashboard Kepala Sekolah
        </h1>
        <p class="text-secondary small mb-0">Selamat Datang, <strong><?= htmlspecialchars($kepsek_nama ?? 'Bapak/Ibu Kepala Sekolah') ?></strong> &bull; Kepala Sekolah SMK NU WARGABINANGUN</p>
    </div>
    
    <!-- Tanggal & Jam Real-Time Detik Berjalan -->
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

<!-- Welcome Alert Banner -->
<div class="alert alert-dismissible fade show text-start border-0 shadow-sm badge-soft-success mb-4 d-flex align-items-center justify-content-between" role="alert">
    <div class="d-flex align-items-center">
        <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="bi bi-award-fill fs-5"></i>
        </div>
        <div>
            <div class="fw-bold" style="color: var(--dark-blue); font-size: 1.05rem;">
                Selamat Datang, <strong><?= htmlspecialchars($kepsek_nama ?? 'Bapak/Ibu Kepala Sekolah') ?></strong>
            </div>
            <div class="text-secondary small">
                Kepala Sekolah SMK NU WARGABINANGUN &bull; Memantau data kehadiran hari ini: <strong><?= date('d F Y') ?></strong>. Sistem absensi beroperasi optimal.
            </div>
        </div>
    </div>
    <div class="d-none d-md-flex align-items-center gap-2">
        <a href="<?= base_url('kepala-sekolah/laporan') ?>" class="btn btn-sm btn-soft-blue"><i class="bi bi-file-earmark-bar-graph me-1"></i> Laporan Lengkap</a>
        <a href="<?= base_url('kepala-sekolah/catatan') ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;"><i class="bi bi-journal-text me-1"></i> Catatan Guru</a>
    </div>
</div>

<!-- ============================================================== -->
<!-- SEKSI GRAFIK TREN KEHADIRAN (7 HARI) & CATATAN UNTUK GURU      -->
<!-- DITEMPATKAN DI BAGIAN ATAS SESUAI PERMINTAAN KEPSEK             -->
<!-- ============================================================== -->
<div class="row g-4 mb-4">
    <!-- Kolom Kiri: Tren Kehadiran Siswa & Guru 7 Hari -->
    <div class="col-lg-7">
        <div class="card card-modern border-0 h-100 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--border-blue) !important;">
                <span class="fw-bold" style="color: var(--dark-blue);">
                    <i class="bi bi-graph-up-arrow me-1" style="color: var(--medium-blue);"></i> Tren Kehadiran Siswa & Guru (7 Hari Terakhir)
                </span>
                <span class="badge bg-light text-secondary border">Presensi Hadir</span>
            </div>
            <div class="card-body p-4">
                <canvas id="chartTrenKepsek" style="max-height: 260px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Catatan Guru Terbaru -->
    <div class="col-lg-5">
        <div class="card card-modern border-0 h-100 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--border-blue) !important;">
                <span class="fw-bold" style="color: var(--dark-blue);">
                    <i class="bi bi-journal-text me-1" style="color: var(--medium-blue);"></i> Catatan Guru Terbaru
                </span>
                <a href="<?= base_url('kepala-sekolah/catatan') ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">Lihat Semua</a>
            </div>
            <div class="card-body p-3">
                <?php if(!empty($stats['catatan_guru_terbaru'])): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach($stats['catatan_guru_terbaru'] as $cg): ?>
                            <?php 
                                $badgeUrgensi = 'badge bg-secondary';
                                if($cg->urgensi == 'Penting') $badgeUrgensi = 'badge bg-warning text-dark';
                                elseif($cg->urgensi == 'Peringatan') $badgeUrgensi = 'badge bg-danger';
                                elseif($cg->urgensi == 'Apresiasi') $badgeUrgensi = 'badge bg-success';
                            ?>
                            <div class="list-group-item px-0 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong class="text-truncate" style="color: var(--dark-blue); max-width: 65%;">
                                        <?= htmlspecialchars($cg->nama_guru) ?>
                                    </strong>
                                    <span class="<?= $badgeUrgensi ?>"><?= htmlspecialchars($cg->kategori) ?></span>
                                </div>
                                <p class="small text-muted mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($cg->isi_catatan) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center small text-secondary" style="font-size: 0.75rem;">
                                    <span><i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y', strtotime($cg->tanggal)) ?></span>
                                    <span>Status: <strong><?= $cg->status_baca == 'sudah' ? '<span class="text-success"><i class="bi bi-check-all"></i> Dibaca</span>' : '<span class="text-warning"><i class="bi bi-clock"></i> Belum dibaca</span>' ?></strong></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x fs-1 d-block mb-2" style="color: var(--light-blue);"></i>
                        Belum ada catatan untuk guru.
                        <div class="mt-2">
                            <a href="<?= base_url('kepala-sekolah/catatan') ?>" class="btn btn-sm btn-soft-blue">Buat Catatan Pertama</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<!-- ========================================== -->
<!-- SEKSI 1: KPI STATISTIK KEHADIRAN SISWA -->
<!-- ========================================== -->
<div class="d-flex align-items-center justify-content-between mb-2">
    <h6 class="fw-bold text-uppercase small tracking-wide" style="color: var(--medium-blue);">
        <i class="bi bi-people-fill me-1"></i> Ringkasan Presensi Siswa (<?= date('d/m/Y') ?>)
    </h6>
    <span class="badge bg-light text-secondary border">Total Siswa: <?= $totalSiswa ?></span>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #10B981 !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Siswa Hadir</span>
                    <h3 class="fw-bold my-1 text-success"><?= $stats['siswa_hadir'] ?></h3>
                    <div class="small text-muted"><span class="badge bg-success bg-opacity-10 text-success fw-bold"><?= $pctSiswa ?>%</span> Kehadiran</div>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                    <i class="bi bi-person-check-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #0EA5E9 !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Izin / Sakit</span>
                    <h3 class="fw-bold my-1 text-info"><?= ($stats['siswa_izin'] + $stats['siswa_sakit']) ?></h3>
                    <div class="small text-muted">Izin: <strong><?= $stats['siswa_izin'] ?></strong> | Sakit: <strong><?= $stats['siswa_sakit'] ?></strong></div>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                    <i class="bi bi-envelope-paper-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #EF4444 !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Alpa</span>
                    <h3 class="fw-bold my-1 text-danger"><?= $stats['siswa_alpa'] ?></h3>
                    <div class="small text-muted"><?= $stats['siswa_alpa'] > 0 ? 'Perlu pembinaan' : 'Nihil alpa' ?></div>
                </div>
                <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2">
                    <i class="bi bi-person-x-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #F59E0B !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Belum Presensi</span>
                    <?php 
                        $siswaBelum = max(0, $totalSiswa - ($stats['siswa_hadir'] + $stats['siswa_izin'] + $stats['siswa_sakit'] + $stats['siswa_alpa']));
                    ?>
                    <h3 class="fw-bold my-1 text-warning"><?= $siswaBelum ?></h3>
                    <div class="small text-muted"><?= $siswaBelum > 0 ? 'Belum scan kartu/QR' : 'Seluruh siswa tercatat' ?></div>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SEKSI 2: KPI STATISTIK KEHADIRAN GURU -->
<!-- ========================================== -->
<div class="d-flex align-items-center justify-content-between mb-2">
    <h6 class="fw-bold text-uppercase small tracking-wide" style="color: var(--dark-blue);">
        <i class="bi bi-person-badge-fill me-1"></i> Ringkasan Presensi Guru & Tenaga Pendidik
    </h6>
    <span class="badge bg-light text-secondary border">Total Guru: <?= $totalGuru ?></span>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #3B82F6 !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Guru Hadir</span>
                    <h3 class="fw-bold my-1" style="color: var(--dark-blue);"><?= $stats['guru_hadir'] ?></h3>
                    <div class="small text-muted"><span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= $pctGuru ?>%</span> Kehadiran</div>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                    <i class="bi bi-mortarboard-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #06B6D4 !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Izin / Sakit</span>
                    <h3 class="fw-bold my-1 text-info"><?= ($stats['guru_izin'] + $stats['guru_sakit']) ?></h3>
                    <div class="small text-muted">Izin: <strong><?= $stats['guru_izin'] ?></strong> | Sakit: <strong><?= $stats['guru_sakit'] ?></strong></div>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                    <i class="bi bi-file-medical-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #EF4444 !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Alpa / Belum Absen</span>
                    <?php 
                        $guruBelum = max(0, $totalGuru - ($stats['guru_hadir'] + $stats['guru_izin'] + $stats['guru_sakit'] + $stats['guru_alpa']));
                        $totalGuruTidakHadir = $stats['guru_alpa'] + $guruBelum;
                    ?>
                    <h3 class="fw-bold my-1 text-danger"><?= $totalGuruTidakHadir ?></h3>
                    <div class="small text-muted">Alpa: <?= $stats['guru_alpa'] ?> | Belum: <?= $guruBelum ?></div>
                </div>
                <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #8B5CF6 !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Catatan Pembinaan</span>
                    <h3 class="fw-bold my-1 text-purple" style="color: #8B5CF6;"><?= $stats['total_catatan_guru'] ?? 0 ?></h3>
                    <div class="small text-muted"><a href="<?= base_url('kepala-sekolah/catatan') ?>" class="text-decoration-none">Kelola Catatan &rarr;</a></div>
                </div>
                <div class="rounded-3 p-2" style="background-color: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                    <i class="bi bi-journal-bookmark-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- SEKSI 3: TABEL PEMANTAUAN REAL-TIME SISWA & GURU (FITUR UTAMA) -->
<!-- ============================================================== -->
<div class="card card-modern border-0 mb-4 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom" style="border-color: var(--border-blue) !important;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <!-- Nav Tabs -->
            <ul class="nav nav-pills" id="monitoringTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-3 py-2" id="tab-siswa-btn" data-bs-toggle="pill" data-bs-target="#tab-siswa" type="button" role="tab" style="border-radius: 8px;">
                        <i class="bi bi-people me-1"></i> Pemantauan Siswa
                        <span class="badge bg-success ms-1"><?= $stats['siswa_hadir'] ?>/<?= $totalSiswa ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-3 py-2" id="tab-guru-btn" data-bs-toggle="pill" data-bs-target="#tab-guru" type="button" role="tab" style="border-radius: 8px;">
                        <i class="bi bi-person-badge me-1"></i> Pemantauan Guru
                        <span class="badge bg-primary ms-1"><?= $stats['guru_hadir'] ?>/<?= $totalGuru ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-3 py-2" id="tab-kelas-btn" data-bs-toggle="pill" data-bs-target="#tab-kelas" type="button" role="tab" style="border-radius: 8px;">
                        <i class="bi bi-grid-3x3-gap me-1"></i> Rekap Per Kelas
                    </button>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary border px-2 py-1">
                    <i class="bi bi-circle-fill text-success me-1" style="font-size: 8px;"></i> Live Monitoring
                </span>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="monitoringTabsContent">
            
            <!-- -------------------------------------------------- -->
            <!-- TAB 1: PEMANTAUAN SISWA HARI INI -->
            <!-- -------------------------------------------------- -->
            <div class="tab-pane fade show active" id="tab-siswa" role="tabpanel">
                <!-- Filter Bar Siswa -->
                <div class="p-3 bg-light border-bottom d-flex flex-wrap gap-2 align-items-center justify-content-between" style="border-color: var(--border-blue) !important;">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <input type="text" id="searchSiswa" class="form-control form-control-sm bg-white" placeholder="Cari nama atau NIS siswa..." style="max-width: 260px; border-radius: 6px; border-color: var(--border-blue);">
                        
                        <select id="filterKelasSiswa" class="form-select form-select-sm bg-white" style="max-width: 180px; border-radius: 6px; border-color: var(--border-blue);">
                            <option value="">Semua Kelas</option>
                            <?php if(!empty($all_kelas)): ?>
                                <?php foreach($all_kelas as $k): ?>
                                    <option value="<?= htmlspecialchars($k->nama_kelas) ?>"><?= htmlspecialchars($k->nama_kelas) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>

                        <select id="filterStatusSiswa" class="form-select form-select-sm bg-white" style="max-width: 170px; border-radius: 6px; border-color: var(--border-blue);">
                            <option value="">Semua Status</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alpa">Alpa / Bolos</option>
                            <option value="Belum Hadir">Belum Presensi</option>
                        </select>
                    </div>

                    <div class="small text-muted">
                        Total Data: <span id="countSiswaTable" class="fw-bold text-dark"><?= count($stats['monitoring_siswa'] ?? []) ?></span> siswa
                    </div>
                </div>

                <!-- Tabel Siswa -->
                <div class="table-modern-container" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-modern mb-0 align-middle" id="tableSiswa">
                        <thead class="sticky-top bg-white shadow-sm" style="z-index: 2;">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>NIS</th>
                                <th>Nama Lengkap</th>
                                <th>Kelas / Jurusan</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stats['monitoring_siswa'])): ?>
                                <?php $no = 1; foreach ($stats['monitoring_siswa'] as $s): ?>
                                    <?php 
                                        $st = strtolower($s->status ?? '');
                                        $labelStatus = 'Belum Hadir';
                                        $badgeClass = 'badge bg-secondary';

                                        if ($st === 'hadir') {
                                            $labelStatus = 'Hadir';
                                            $badgeClass = 'badge badge-soft-success';
                                        } elseif ($st === 'izin') {
                                            $labelStatus = 'Izin';
                                            $badgeClass = 'badge badge-soft-info';
                                        } elseif ($st === 'sakit') {
                                            $labelStatus = 'Sakit';
                                            $badgeClass = 'badge badge-soft-primary';
                                        } elseif ($st === 'alpa' || $st === 'bolos') {
                                            $labelStatus = 'Alpa';
                                            $badgeClass = 'badge badge-soft-danger';
                                        }
                                    ?>
                                    <tr class="row-siswa" 
                                        data-nama="<?= strtolower(htmlspecialchars($s->nama_lengkap)) ?>" 
                                        data-nis="<?= strtolower(htmlspecialchars($s->nis)) ?>" 
                                        data-kelas="<?= htmlspecialchars($s->nama_kelas ?? '') ?>" 
                                        data-status="<?= $labelStatus ?>">
                                        <td class="text-center"><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= $no++ ?></span></td>
                                        <td class="fw-semibold text-muted"><?= htmlspecialchars($s->nis) ?></td>
                                        <td>
                                            <strong style="color: var(--dark-blue);"><?= htmlspecialchars($s->nama_lengkap) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($s->nama_kelas ?? '-') ?></span>
                                            <small class="text-muted d-block"><?= htmlspecialchars($s->nama_jurusan ?? '') ?></small>
                                        </td>
                                        <td>
                                            <?= !empty($s->jam_masuk) ? '<span class="fw-bold text-success"><i class="bi bi-clock me-1"></i>' . substr($s->jam_masuk, 0, 5) . '</span>' : '<span class="text-muted">-</span>' ?>
                                        </td>
                                        <td>
                                            <?= !empty($s->jam_pulang) ? '<span class="fw-bold text-primary"><i class="bi bi-clock-history me-1"></i>' . substr($s->jam_pulang, 0, 5) . '</span>' : '<span class="text-muted">-</span>' ?>
                                        </td>
                                        <td>
                                            <span class="<?= $badgeClass ?>"><?= $labelStatus ?></span>
                                        </td>
                                        <td>
                                            <small class="text-secondary"><?= htmlspecialchars($s->keterangan ?? '-') ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>
                                        Tidak ada data siswa ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- TAB 2: PEMANTAUAN GURU HARI INI -->
            <!-- -------------------------------------------------- -->
            <div class="tab-pane fade" id="tab-guru" role="tabpanel">
                <!-- Filter Bar Guru -->
                <div class="p-3 bg-light border-bottom d-flex flex-wrap gap-2 align-items-center justify-content-between" style="border-color: var(--border-blue) !important;">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <input type="text" id="searchGuru" class="form-control form-control-sm bg-white" placeholder="Cari nama atau NIP guru..." style="max-width: 260px; border-radius: 6px; border-color: var(--border-blue);">
                        
                        <select id="filterStatusGuru" class="form-select form-select-sm bg-white" style="max-width: 170px; border-radius: 6px; border-color: var(--border-blue);">
                            <option value="">Semua Status</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alpa">Alpa / Bolos</option>
                            <option value="Belum Hadir">Belum Presensi</option>
                        </select>
                    </div>

                    <div class="small text-muted">
                        Total Tenaga Pendidik: <span id="countGuruTable" class="fw-bold text-dark"><?= count($stats['monitoring_guru'] ?? []) ?></span> guru
                    </div>
                </div>

                <!-- Tabel Guru -->
                <div class="table-modern-container" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-modern mb-0 align-middle" id="tableGuru">
                        <thead class="sticky-top bg-white shadow-sm" style="z-index: 2;">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>NIP</th>
                                <th>Nama Guru / Pendidik</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stats['monitoring_guru'])): ?>
                                <?php $no = 1; foreach ($stats['monitoring_guru'] as $g): ?>
                                    <?php 
                                        $stg = strtolower($g->status ?? '');
                                        $labelStatusG = 'Belum Hadir';
                                        $badgeClassG = 'badge bg-secondary';

                                        if ($stg === 'hadir') {
                                            $labelStatusG = 'Hadir';
                                            $badgeClassG = 'badge badge-soft-success';
                                        } elseif ($stg === 'izin') {
                                            $labelStatusG = 'Izin';
                                            $badgeClassG = 'badge badge-soft-info';
                                        } elseif ($stg === 'sakit') {
                                            $labelStatusG = 'Sakit';
                                            $badgeClassG = 'badge badge-soft-primary';
                                        } elseif ($stg === 'alpa' || $stg === 'bolos') {
                                            $labelStatusG = 'Alpa';
                                            $badgeClassG = 'badge badge-soft-danger';
                                        }
                                    ?>
                                    <tr class="row-guru" 
                                        data-nama="<?= strtolower(htmlspecialchars($g->nama_lengkap)) ?>" 
                                        data-nip="<?= strtolower(htmlspecialchars($g->nip ?? '')) ?>" 
                                        data-status="<?= $labelStatusG ?>">
                                        <td class="text-center"><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= $no++ ?></span></td>
                                        <td class="fw-semibold text-muted"><?= htmlspecialchars($g->nip ?? '-') ?></td>
                                        <td>
                                            <strong style="color: var(--dark-blue);"><?= htmlspecialchars($g->nama_lengkap) ?></strong>
                                        </td>
                                        <td>
                                            <?= !empty($g->jam_masuk) ? '<span class="fw-bold text-success"><i class="bi bi-clock me-1"></i>' . substr($g->jam_masuk, 0, 5) . '</span>' : '<span class="text-muted">-</span>' ?>
                                        </td>
                                        <td>
                                            <?= !empty($g->jam_pulang) ? '<span class="fw-bold text-primary"><i class="bi bi-clock-history me-1"></i>' . substr($g->jam_pulang, 0, 5) . '</span>' : '<span class="text-muted">-</span>' ?>
                                        </td>
                                        <td>
                                            <span class="<?= $badgeClassG ?>"><?= $labelStatusG ?></span>
                                        </td>
                                        <td>
                                            <small class="text-secondary"><?= htmlspecialchars($g->keterangan ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('kepala-sekolah/catatan?guru_id=' . $g->id) ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;" title="Tulis / Lihat Catatan untuk Guru Ini">
                                                <i class="bi bi-pencil-square"></i> Catatan
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>
                                        Tidak ada data guru ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- -------------------------------------------------- -->
            <!-- TAB 3: REKAP PERSENTASE PER KELAS -->
            <!-- -------------------------------------------------- -->
            <div class="tab-pane fade" id="tab-kelas" role="tabpanel">
                <div class="p-3 bg-light border-bottom text-muted small" style="border-color: var(--border-blue) !important;">
                    <i class="bi bi-info-circle me-1"></i> Rekapitulasi perbandingan persentase kehadiran peserta didik di setiap rombongan belajar (rombel) pada tanggal terpilih.
                </div>
                <div class="table-modern-container">
                    <table class="table table-modern mb-0 align-middle">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Nama Kelas</th>
                                <th>Jurusan</th>
                                <th class="text-center">Total Siswa</th>
                                <th class="text-center text-success">Hadir</th>
                                <th class="text-center text-info">Izin</th>
                                <th class="text-center text-primary">Sakit</th>
                                <th class="text-center text-danger">Alpa</th>
                                <th width="25%">Tingkat Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($stats['rekap_kelas'])): ?>
                                <?php $noK = 1; foreach($stats['rekap_kelas'] as $rk): ?>
                                    <?php 
                                        $totK = (int)$rk->total_siswa;
                                        $hdrK = (int)$rk->hadir;
                                        $pctK = $totK > 0 ? round(($hdrK / $totK) * 100, 1) : 0;
                                        $barColor = $pctK >= 85 ? 'bg-success' : ($pctK >= 70 ? 'bg-warning' : 'bg-danger');
                                    ?>
                                    <tr>
                                        <td class="text-center"><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= $noK++ ?></span></td>
                                        <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($rk->nama_kelas) ?></strong></td>
                                        <td><span class="text-secondary small"><?= htmlspecialchars($rk->nama_jurusan) ?></span></td>
                                        <td class="text-center fw-bold"><?= $totK ?></td>
                                        <td class="text-center fw-bold text-success"><?= $hdrK ?></td>
                                        <td class="text-center fw-semibold text-info"><?= (int)$rk->izin ?></td>
                                        <td class="text-center fw-semibold text-primary"><?= (int)$rk->sakit ?></td>
                                        <td class="text-center fw-semibold text-danger"><?= (int)$rk->alpa ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 8px; border-radius: 4px; background-color: var(--very-light-blue);">
                                                    <div class="progress-bar <?= $barColor ?>" role="progressbar" style="width: <?= $pctK ?>%;" aria-valuenow="<?= $pctK ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="small fw-bold text-dark" style="min-width: 45px; text-align: right;"><?= $pctK ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Belum ada data rombel kelas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>



<!-- Script Filter & Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Filter Real-Time Siswa
    const searchSiswa = document.getElementById('searchSiswa');
    const filterKelasSiswa = document.getElementById('filterKelasSiswa');
    const filterStatusSiswa = document.getElementById('filterStatusSiswa');
    const rowSiswa = document.querySelectorAll('.row-siswa');
    const countSiswaTable = document.getElementById('countSiswaTable');

    function filterSiswaTable() {
        const query = searchSiswa ? searchSiswa.value.toLowerCase().trim() : '';
        const kelas = filterKelasSiswa ? filterKelasSiswa.value : '';
        const status = filterStatusSiswa ? filterStatusSiswa.value : '';
        let visible = 0;

        rowSiswa.forEach(row => {
            const rowNama = row.getAttribute('data-nama') || '';
            const rowNis = row.getAttribute('data-nis') || '';
            const rowKelas = row.getAttribute('data-kelas') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchSearch = !query || rowNama.includes(query) || rowNis.includes(query);
            const matchKelas = !kelas || rowKelas === kelas;
            const matchStatus = !status || rowStatus === status;

            if (matchSearch && matchKelas && matchStatus) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        if (countSiswaTable) countSiswaTable.textContent = visible;
    }

    if (searchSiswa) searchSiswa.addEventListener('input', filterSiswaTable);
    if (filterKelasSiswa) filterKelasSiswa.addEventListener('change', filterSiswaTable);
    if (filterStatusSiswa) filterStatusSiswa.addEventListener('change', filterSiswaTable);

    // 2. Filter Real-Time Guru
    const searchGuru = document.getElementById('searchGuru');
    const filterStatusGuru = document.getElementById('filterStatusGuru');
    const rowGuru = document.querySelectorAll('.row-guru');
    const countGuruTable = document.getElementById('countGuruTable');

    function filterGuruTable() {
        const query = searchGuru ? searchGuru.value.toLowerCase().trim() : '';
        const status = filterStatusGuru ? filterStatusGuru.value : '';
        let visible = 0;

        rowGuru.forEach(row => {
            const rowNama = row.getAttribute('data-nama') || '';
            const rowNip = row.getAttribute('data-nip') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchSearch = !query || rowNama.includes(query) || rowNip.includes(query);
            const matchStatus = !status || rowStatus === status;

            if (matchSearch && matchStatus) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        if (countGuruTable) countGuruTable.textContent = visible;
    }

    if (searchGuru) searchGuru.addEventListener('input', filterGuruTable);
    if (filterStatusGuru) filterStatusGuru.addEventListener('change', filterGuruTable);

    // 3. Render Chart.js Tren Kehadiran
    const ctx = document.getElementById('chartTrenKepsek');
    if (ctx) {
        const trendLabels = <?= json_encode($stats['trend_labels'] ?? []) ?>;
        const trendSiswa = <?= json_encode($stats['trend_siswa'] ?? []) ?>;
        const trendGuru = <?= json_encode($stats['trend_guru'] ?? []) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'Siswa Hadir',
                        data: trendSiswa,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#10B981'
                    },
                    {
                        label: 'Guru Hadir',
                        data: trendGuru,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        tension: 0.3,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#3B82F6'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: { family: "'Inter', sans-serif", size: 12 }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
