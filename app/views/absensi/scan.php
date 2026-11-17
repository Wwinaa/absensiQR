<?php
$isWeekend = (date('N') == 7); // Hanya hari Minggu yang libur KBM (Hari Sabtu dibuka untuk uji coba presensi)
$namaHariIni = $namaHari ?? ([1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'][date('N')]);

// Tentukan apakah ada hasil scan sesi atau absensi hari ini yang sudah tercatat
$activeResult = null;
if (isset($_SESSION['scan_result'])) {
    $activeResult = $_SESSION['scan_result'];
} elseif (!empty($todayAbsensi) && !empty($currentUserData)) {
    // Pengguna yang sedang login sudah memiliki catatan absensi hari ini
    $stLower = strtolower($todayAbsensi->status ?? 'hadir');
    $isNonHadir = in_array($stLower, ['izin', 'sakit', 'alpa', 'bolos']) || (strpos($stLower, 'sakit') !== false) || (strpos($stLower, 'izin') !== false) || (strpos($stLower, 'alpa') !== false) || (strpos($stLower, 'bolos') !== false);
    $activeResult = [
        'nama' => $currentUserData['nama'],
        'role' => $currentUserData['role'],
        'nis' => $currentUserData['nis'] ?? null,
        'nip' => $currentUserData['nip'] ?? null,
        'kelas' => $currentUserData['kelas'] ?? null,
        'kelas_binaan' => $currentUserData['kelas_binaan'] ?? null,
        'tanggal' => date('d-m-Y', strtotime($todayAbsensi->tanggal)),
        'jam_masuk' => (!$isNonHadir && !empty($todayAbsensi->jam_masuk)) ? substr($todayAbsensi->jam_masuk, 0, 5) . ' WIB' : '-',
        'jam_pulang' => (!$isNonHadir && !empty($todayAbsensi->jam_pulang)) ? substr($todayAbsensi->jam_pulang, 0, 5) . ' WIB' : '-',
        'status' => ucfirst($stLower),
        'keterangan' => !empty($todayAbsensi->keterangan) ? $todayAbsensi->keterangan : ($stLower === 'hadir' ? 'Hadir tepat waktu' : '-'),
        'jadwal' => $todayJadwal ?? [],
        'riwayat' => $riwayatTerakhir ?? [],
        'is_sudah_absen' => true,
        'is_pre_existing' => true
    ];
}
?>

<div class="container-fluid px-2 px-md-3 px-lg-4 py-3 mb-5 scan-split-container">
    
    <!-- Top Bar: Header & Live Indicators -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: var(--dark-blue);">
                <span class="d-inline-flex align-items-center justify-content-center p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-qr-code-scan fs-5"></i>
                </span>
                <span>SCAN PRESENSI KEHADIRAN</span>
            </h4>
            <p class="text-secondary small mb-0">Pindai barcode atau QR Code kartu Anda. Tampilan sebelah-menyebelah: Kamera di panel kiri dan riwayat kehadiran langsung muncul otomatis di panel kanan.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge px-3 py-2 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2 bg-adaptive-subtle border">
                <i class="bi bi-calendar3 text-primary"></i> <?= htmlspecialchars($namaHariIni) ?>, <?= date('d M Y') ?>
            </span>
            <span class="badge px-3 py-2 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2 bg-adaptive-subtle border">
                <i class="bi bi-clock text-primary"></i> <span id="liveClock"><?= date('H:i:s') ?></span> WIB
            </span>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- PERATURAN & KETENTUAN ABSENSI SMK (DITARUH DI ATAS)      -->
    <!-- ======================================================== -->
    <div class="card border-0 text-start mb-4 shadow-sm" style="background: rgba(59, 130, 246, 0.08); border-left: 5px solid #3B82F6 !important; border-radius: 14px;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom" style="border-color: rgba(59, 130, 246, 0.2) !important;">
                <div class="fw-bold text-primary d-flex align-items-center gap-2" style="font-size: 1rem;">
                    <i class="bi bi-shield-check fs-4"></i>
                    <span>Peraturan & Ketentuan Wajib Absensi SMK NU WARGABINANGUN</span>
                </div>
                <span class="badge bg-primary px-3 py-1 fw-semibold rounded-pill">Ketentuan Operasional</span>
            </div>
            <div class="row g-2 pt-1">
                <div class="col-md-6">
                    <div class="p-2 rounded-3 bg-white bg-opacity-75 border border-primary border-opacity-25 h-100">
                        <div class="fw-bold text-dark small"><i class="bi bi-qr-code-scan me-1 text-warning"></i> Wajib Scan Ulang</div>
                        <div class="small text-secondary mt-1">Siswa, Guru, dan Wali Kelas <strong>wajib scan ulang QR Code</strong> saat jam kepulangan.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-2 rounded-3 bg-white bg-opacity-75 border border-primary border-opacity-25 h-100">
                        <div class="fw-bold text-danger small"><i class="bi bi-exclamation-octagon-fill me-1"></i> Sanksi Bolos</div>
                        <div class="small text-secondary mt-1">Jika tidak scan pulang, kehadiran otomatis tercatat <strong>Bolos / Alpa</strong> oleh sistem.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Split Grid (Sebelahan) -->
    <div class="row g-4 align-items-stretch">
        
        <!-- ======================================================== -->
        <!-- KOLOM KIRI: Kamera Pemindai (QR Code Scanner)            -->
        <!-- ======================================================== -->
        <div class="col-lg-6">
            <div class="card card-modern border-0 p-3 p-md-4 h-100 shadow-sm d-flex flex-column scan-card-left">
                
                <!-- Header Panel Scanner -->
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: var(--light-blue) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-camera-video-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-adaptive-title">Kamera Pemindai</h6>
                            <small class="text-secondary">Posisikan QR Code di dalam kotak panduan scanner</small>
                        </div>
                    </div>
                    <?php if ($isWeekend): ?>
                    <span id="scanStatus" class="badge d-inline-flex align-items-center gap-1 bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50" style="font-size: 0.8rem; padding: 0.45rem 0.85rem; border-radius: 20px;">
                        <i class="bi bi-calendar-x-fill me-1"></i> Presensi Ditutup (Hari Libur)
                    </span>
                    <?php else: ?>
                    <span id="scanStatus" class="badge d-inline-flex align-items-center gap-1" style="background-color: var(--light-blue); color: var(--dark-blue); font-size: 0.8rem; padding: 0.45rem 0.85rem; border-radius: 20px; border: 1px solid var(--border-blue);">
                        <span class="scanner-live-dot me-1"></span> Kamera Aktif
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Flash Alerts Container -->
                <div id="flashAlertsContainer">
                    <?php if ($isWeekend): ?>
                        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-2 mb-3" style="border-radius: 12px; background: rgba(245, 158, 11, 0.15); color: #B45309;" role="alert">
                            <i class="bi bi-calendar-x-fill fs-5 flex-shrink-0"></i>
                            <div>
                                <strong>Hari Libur KBM:</strong> Hari ini adalah hari <?= htmlspecialchars($namaHariIni) ?> (Kegiatan Belajar Mengajar Libur). Presensi kehadiran ditutup pada hari Minggu.
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])): ?>
                        <div class="alert alert-dismissible fade show text-start border-0 shadow-sm badge-soft-success mb-3" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($_SESSION['success_message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-dismissible fade show text-start border-0 shadow-sm badge-soft-danger mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($_SESSION['error_message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['warning_message'])): ?>
                        <div class="alert alert-dismissible fade show text-start border-0 shadow-sm badge-soft-warning mb-3" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i> <?= htmlspecialchars($_SESSION['warning_message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['warning_message']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['info_message'])): ?>
                        <div class="alert alert-dismissible fade show text-start border-0 shadow-sm badge-soft-info mb-3" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i> <?= htmlspecialchars($_SESSION['info_message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['info_message']); ?>
                    <?php endif; ?>
                </div>

                <!-- Video Viewport Scanner dengan Targeting Overlays -->
                <div class="scanner-viewport-wrapper mb-3 position-relative">
                    <div id="reader" style="width: 100%; min-height: 340px; background-color: var(--very-light-blue); border: 2px dashed var(--soft-blue); display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 16px; box-shadow: inset 0 0 25px rgba(110, 168, 254, 0.1);">
                        <div class="text-center p-4" id="readerPlaceholder" style="color: var(--medium-blue);">
                            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                            <i class="bi bi-camera fs-1 d-block mb-2"></i>
                            <span class="fw-semibold">Menyiapkan kamera pemindai QR Code...</span>
                        </div>
                    </div>

                    <!-- Target Bracket Box & Scanning Beam Overlay -->
                    <div class="scanner-overlay-brackets" id="scannerTargetBox" style="display: none;">
                        <span class="scanner-bracket top-left"></span>
                        <span class="scanner-bracket top-right"></span>
                        <span class="scanner-bracket bottom-left"></span>
                        <span class="scanner-bracket bottom-right"></span>
                        <div class="scanner-laser-line"></div>
                    </div>
                    
                    <div id="cameraErrorAlert" class="alert alert-warning border-0 mt-3 d-none text-start small shadow-sm">
                        <i class="bi bi-camera-video-off-fill me-2 fs-6"></i>
                        <strong>Kamera Tidak Aktif:</strong> <span id="cameraErrorMessage">Akses kamera tidak diizinkan atau perangkat tidak memiliki kamera.</span>
                    </div>
                </div>

                <!-- Hidden Form untuk fallback submit -->
                <form action="<?= base_url('absensi/proses_scan') ?>" method="POST" id="scanForm" style="display: none;">
                    <input type="hidden" name="barcode" id="barcode">
                    <input type="hidden" name="jenis" id="jenis" value="<?= htmlspecialchars($_SESSION['user_role'] ?? 'siswa') ?>">
                </form>

            </div>
        </div>

        <!-- ======================================================== -->
        <!-- KOLOM KANAN: Riwayat & Status Kehadiran (Sebelahan)      -->
        <!-- ======================================================== -->
        <div class="col-lg-6">
            <div class="card card-modern border-0 p-3 p-md-4 h-100 shadow-sm d-flex flex-column scan-card-right" id="riwayatCard">
                
                <!-- Header Panel Riwayat -->
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: var(--light-blue) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                            <i class="bi bi-calendar2-check-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-adaptive-title">Riwayat & Status Kehadiran</h6>
                            <small class="text-secondary">Hasil absensi muncul seketika di panel ini</small>
                        </div>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 small fw-semibold d-flex align-items-center gap-1">
                        <span class="scanner-live-dot"></span> Live Presensi
                    </span>
                </div>

                <!-- Container Dinamis Riwayat Presensi -->
                <div id="riwayatContent" class="flex-grow-1 d-flex flex-column">
                    <?php if ($activeResult): 
                        $res = $activeResult;
                        $isSudahAbsen = !empty($res['is_sudah_absen']);
                        $isPreExisting = !empty($res['is_pre_existing']);
                        $roleScanned = $res['role'] ?? (isset($res['nis']) ? 'Siswa' : 'Guru');
                        $curStatus = strtolower($res['status'] ?? 'hadir');
                        $isNonHadir = in_array($curStatus, ['izin', 'sakit', 'alpa', 'bolos']) || (strpos($curStatus, 'sakit') !== false) || (strpos($curStatus, 'izin') !== false) || (strpos($curStatus, 'alpa') !== false) || (strpos($curStatus, 'bolos') !== false);
                        if ($isNonHadir) {
                            $jamMasuk = '-';
                            $jamPulang = '-';
                        } else {
                            $jamMasuk = !empty($res['jam_masuk']) ? $res['jam_masuk'] : '-';
                            $jamPulang = !empty($res['jam_pulang']) ? $res['jam_pulang'] : '-';
                        }
                        
                        if (strpos($curStatus, 'sakit') !== false) {
                            $statusName = 'Sakit';
                            $statusBadge = '<span class="badge" style="background:#FEF3C7; color:#B45309; border:1px solid #FCD34D; font-size:0.85rem; padding:0.4em 0.8em;"><i class="bi bi-bandaid me-1"></i>Sakit</span>';
                            $cardBorderColor = '#F59E0B';
                            $cardTitleColor = '#B45309';
                            $cardTitle = 'ABSENSI HARI INI TERCATAT (SAKIT)';
                            $headerBg = 'rgba(245, 158, 11, 0.1)';
                            $headerIcon = 'bi-bandaid-fill text-warning';
                        } else if (strpos($curStatus, 'izin') !== false) {
                            $statusName = 'Izin';
                            $statusBadge = '<span class="badge" style="background:#E0F2FE; color:#0369A1; border:1px solid #BAE6FD; font-size:0.85rem; padding:0.4em 0.8em;"><i class="bi bi-envelope-paper me-1"></i>Izin</span>';
                            $cardBorderColor = '#0284C7';
                            $cardTitleColor = '#0369A1';
                            $cardTitle = 'ABSENSI HARI INI TERCATAT (IZIN)';
                            $headerBg = 'rgba(2, 132, 199, 0.1)';
                            $headerIcon = 'bi-envelope-paper-fill text-info';
                        } else if (strpos($curStatus, 'alpa') !== false || strpos($curStatus, 'bolos') !== false) {
                            $statusName = 'Alpa';
                            $statusBadge = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger fw-bold" style="font-size:0.85rem; padding:0.4em 0.8em;"><i class="bi bi-x-circle-fill me-1"></i>Alpa</span>';
                            $cardBorderColor = '#DC2626';
                            $cardTitleColor = '#DC2626';
                            $cardTitle = 'ABSENSI HARI INI TERCATAT (ALPA)';
                            $headerBg = 'rgba(220, 38, 38, 0.1)';
                            $headerIcon = 'bi-x-circle-fill text-danger';
                        } else {
                            $isLate = (isset($res['keterangan']) && stripos($res['keterangan'], 'terlambat') !== false) || (isset($res['status']) && stripos($res['status'], 'terlambat') !== false);
                            $statusName = $isLate ? 'Hadir (Terlambat)' : 'Hadir';
                            $statusBadge = $isLate ? 
                                '<span class="badge" style="background:#FEF3C7; color:#B45309; border:1px solid #FCD34D; font-size:0.85rem; padding:0.4em 0.8em;"><i class="bi bi-clock-history me-1"></i>Terlambat</span>' :
                                '<span class="badge badge-soft-success" style="font-size:0.85rem; padding:0.4em 0.8em;"><i class="bi bi-check-circle-fill me-1"></i>Hadir</span>';
                            $cardBorderColor = $isLate ? '#F59E0B' : '#10B981';
                            $cardTitleColor = $isLate ? '#B45309' : '#059669';
                            $cardTitle = ($isPreExisting || $isSudahAbsen) ? ($isLate ? 'ABSENSI HARI INI TERCATAT (TERLAMBAT)' : 'ABSENSI HARI INI TERCATAT') : ($isLate ? 'ABSENSI BERHASIL DICATAT (TERLAMBAT)!' : 'ABSENSI BERHASIL DICATAT!');
                            $headerBg = $isLate ? 'rgba(245, 158, 11, 0.1)' : 'rgba(16, 185, 129, 0.1)';
                            $headerIcon = $isLate ? 'bi-clock-history text-warning' : 'bi-check-circle-fill text-success';
                        }

                        $statusKet = !empty($res['keterangan']) ? $res['keterangan'] : ($statusName === 'Alpa' ? 'Tidak ada keterangan' : ($jamPulang !== '-' ? 'Kehadiran lengkap' : 'Hadir tepat waktu'));
                        $historyList = $res['riwayat'] ?? ($riwayatTerakhir ?? []);
                    ?>
                        <div class="result-card-animated">
                            
                            <!-- Banner Status Presensi -->
                            <div class="p-3 rounded-4 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: <?= $headerBg ?>; border-left: 5px solid <?= $cardBorderColor ?>;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi <?= $headerIcon ?> fs-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: <?= $cardTitleColor ?>;"><?= $cardTitle ?></h6>
                                        <small class="text-secondary"><?= htmlspecialchars($res['tanggal']) ?> &bull; SMK NU WARGABINANGUN</small>
                                    </div>
                                </div>
                                <div>
                                    <?= $statusBadge ?>
                                </div>
                            </div>

                            <!-- Data Profil Pemilik Barcode -->
                            <div class="p-3 rounded-4 bg-adaptive-subtle mb-3 border">
                                <div class="row g-2 align-items-center">
                                    <div class="col-sm-7">
                                        <span class="text-secondary small fw-semibold d-block">Nama Lengkap:</span>
                                        <span class="fw-bold fs-5 text-adaptive-title"><?= htmlspecialchars($res['nama']) ?></span>
                                    </div>
                                    <div class="col-sm-5 text-sm-end">
                                        <span class="badge px-3 py-2 rounded-pill" style="background-color: var(--light-blue); color: var(--dark-blue); font-size: 0.85rem;">
                                            <i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($roleScanned) ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (isset($res['nis'])): ?>
                                        <div class="col-6 mt-2 pt-2 border-top">
                                             <span class="text-secondary small fw-semibold d-block">NIS / NISN:</span>
                                            <span class="fw-semibold text-adaptive-title"><?= htmlspecialchars($res['nis']) ?></span>
                                        </div>
                                        <div class="col-6 mt-2 pt-2 border-top">
                                            <span class="text-secondary small fw-semibold d-block">Kelas:</span>
                                            <span class="fw-semibold text-adaptive-title"><?= htmlspecialchars($res['kelas'] ?? '-') ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-6 mt-2 pt-2 border-top">
                                            <span class="text-secondary small fw-semibold d-block">NIP:</span>
                                            <span class="fw-semibold text-adaptive-title"><?= htmlspecialchars($res['nip'] ?? '-') ?></span>
                                        </div>
                                        <?php if (!empty($res['kelas_binaan'])): ?>
                                        <div class="col-6 mt-2 pt-2 border-top">
                                            <span class="text-secondary small fw-semibold d-block">Kelas Binaan:</span>
                                            <span class="fw-semibold text-adaptive-title"><?= htmlspecialchars($res['kelas_binaan']) ?></span>
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Stat Cards Jam Masuk & Jam Pulang -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="p-3 rounded-4 stat-box-modern border shadow-sm h-100">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-secondary small fw-semibold">Scan Masuk</span>
                                            <i class="bi bi-box-arrow-in-right text-success fs-5"></i>
                                        </div>
                                        <div class="fs-4 fw-bold text-success mb-1"><?= htmlspecialchars($jamMasuk) ?></div>
                                        <small class="text-secondary d-block" style="font-size: 0.75rem;"><?= $isNonHadir ? 'Tidak perlu scan' : 'Bebas Kapan Saja (Mode Demo)' ?></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-4 stat-box-modern border shadow-sm h-100">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-secondary small fw-semibold">Scan Pulang</span>
                                            <i class="bi bi-box-arrow-left text-primary fs-5"></i>
                                        </div>
                                        <div class="fs-4 fw-bold text-primary mb-1"><?= htmlspecialchars($jamPulang) ?></div>
                                        <small class="text-secondary d-block" style="font-size: 0.75rem;"><?= $isNonHadir ? 'Tidak perlu scan' : (($jamPulang !== '-') ? 'Kepulangan tercatat' : 'Bebas Kapan Saja (Mode Demo)') ?></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Ringkasan Presensi Hari Ini -->
                            <div class="border rounded-4 p-3 bg-adaptive-subtle mb-3">
                                <h6 class="fw-bold mb-2 d-flex align-items-center gap-1 text-adaptive-title" style="font-size: 0.9rem;">
                                    <i class="bi bi-clock-history text-primary"></i> Ringkasan Presensi Hari Ini
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-adaptive align-middle mb-0 text-center" style="font-size: 0.84rem;">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Masuk</th>
                                                <th>Pulang</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-semibold"><?= htmlspecialchars($res['tanggal']) ?></td>
                                                <td>
                                                    <?php if ($jamMasuk !== '-'): ?>
                                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold"><?= htmlspecialchars($jamMasuk) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted fw-semibold">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($jamPulang !== '-'): ?>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= htmlspecialchars($jamPulang) ?></span>
                                                    <?php else: ?>
                                                        <?php if (!$isNonHadir && ($statusName === 'Hadir' || stripos($statusName, 'Terlambat') !== false)): ?>
                                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Belum Scan</span>
                                                        <?php else: ?>
                                                            <span class="text-muted fw-semibold">-</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $statusBadge ?></td>
                                                <td class="text-secondary small fw-medium"><?= htmlspecialchars($statusKet) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Riwayat Presensi Kehadiran Terakhir -->
                            <?php if (!empty($historyList)): ?>
                                <div class="border rounded-4 p-3 bg-adaptive-subtle mb-3">
                                    <h6 class="fw-bold mb-2 d-flex align-items-center justify-content-between text-adaptive-title" style="font-size: 0.9rem;">
                                        <span><i class="bi bi-journal-text text-primary me-1"></i> Riwayat Presensi Terakhir</span>
                                        <span class="badge bg-light text-secondary fw-normal">Daftar Kehadiran</span>
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-adaptive align-middle mb-0 text-center" style="font-size: 0.82rem;">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Masuk</th>
                                                    <th>Pulang</th>
                                                    <th>Status</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($historyList, 0, 5) as $rw): 
                                                    $rTgl = is_object($rw) ? date('d-m-Y', strtotime($rw->tanggal)) : ($rw['tanggal'] ?? '-');
                                                    $rSt = strtolower(is_object($rw) ? ($rw->status ?? 'hadir') : ($rw['status'] ?? 'hadir'));
                                                    $rIsNonHadir = (strpos($rSt, 'sakit') !== false || strpos($rSt, 'izin') !== false || strpos($rSt, 'alpa') !== false || strpos($rSt, 'bolos') !== false);
                                                    $rMsk = (!$rIsNonHadir && (is_object($rw) ? !empty($rw->jam_masuk) : !empty($rw['jam_masuk']))) ? (is_object($rw) ? substr($rw->jam_masuk, 0, 5) . ' WIB' : $rw['jam_masuk']) : '-';
                                                    $rPlg = (!$rIsNonHadir && (is_object($rw) ? !empty($rw->jam_pulang) : !empty($rw['jam_pulang']))) ? (is_object($rw) ? substr($rw->jam_pulang, 0, 5) . ' WIB' : $rw['jam_pulang']) : '-';
                                                    $rKet = is_object($rw) ? ($rw->keterangan ?? '-') : ($rw['keterangan'] ?? '-');
                                                    
                                                    if (strpos($rSt, 'sakit') !== false) {
                                                        $rBadge = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Sakit</span>';
                                                    } else if (strpos($rSt, 'izin') !== false) {
                                                        $rBadge = '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">Izin</span>';
                                                    } else if (strpos($rSt, 'alpa') !== false) {
                                                        $rBadge = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Alpa</span>';
                                                    } else if (strpos($rSt, 'terlambat') !== false || stripos($rKet, 'terlambat') !== false) {
                                                        $rBadge = '<span class="badge" style="background:#FEF3C7; color:#B45309; border:1px solid #FCD34D;"><i class="bi bi-clock-history me-1"></i>Terlambat</span>';
                                                    } else {
                                                        $rBadge = '<span class="badge bg-success bg-opacity-10 text-success">Hadir</span>';
                                                    }
                                                ?>
                                                    <tr>
                                                        <td class="fw-semibold"><?= htmlspecialchars($rTgl) ?></td>
                                                        <td><?= htmlspecialchars($rMsk) ?></td>
                                                        <td><?= htmlspecialchars($rPlg) ?></td>
                                                        <td><?= $rBadge ?></td>
                                                        <td class="text-secondary small"><?= htmlspecialchars($rKet ?: '-') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Tabel Jadwal Pelajaran / Mengajar Hari Ini -->
                            <?php if (!empty($res['jadwal'])): ?>
                                <div class="border rounded-4 p-3 bg-adaptive-subtle">
                                    <h6 class="fw-bold mb-2 d-flex align-items-center gap-1 text-adaptive-title" style="font-size: 0.9rem;">
                                        <i class="bi bi-journal-bookmark-fill text-primary"></i> 
                                        <?= isset($res['nis']) ? 'Jadwal Pelajaran Hari Ini:' : 'Jadwal Mengajar Hari Ini:' ?>
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-modern table-adaptive mb-0" style="font-size: 0.83rem;">
                                            <thead>
                                                <tr>
                                                    <th>Jam</th>
                                                    <th>Mata Pelajaran</th>
                                                    <th><?= isset($res['nis']) ? 'Guru' : 'Kelas' ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($res['jadwal'] as $jdw): ?>
                                                    <tr>
                                                        <td><span class="badge" style="background-color: var(--light-blue); color: var(--dark-blue); font-size: 0.78rem;"><?= htmlspecialchars(substr($jdw->jam_mulai, 0, 5)) ?> - <?= htmlspecialchars(substr($jdw->jam_selesai, 0, 5)) ?></span></td>
                                                        <td class="fw-semibold text-adaptive-title"><?= htmlspecialchars($jdw->nama_mapel) ?></td>
                                                        <td><span class="badge bg-light text-primary"><?= htmlspecialchars(isset($res['nis']) ? ($jdw->nama_guru ?? 'Pengajar') : ($jdw->nama_kelas ?? '-')) ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php 
                        if (isset($_SESSION['scan_result'])) {
                            unset($_SESSION['scan_result']); 
                        }
                    ?>
                    <?php else: ?>
                        <!-- State Standby: Menunggu Pemindaian QR Code -->
                        <div class="d-flex flex-column align-items-center justify-content-center text-center p-3 my-auto" id="standbyPlaceholder">
                            <div class="scanner-idle-circle mb-3">
                                <i class="bi bi-qr-code-scan fs-1 text-primary"></i>
                            </div>
                            <h5 class="fw-bold mb-2 text-adaptive-title">Menunggu Pemindaian QR Code</h5>
                            <p class="text-secondary small mb-3" style="max-width: 440px;">
                                Arahkan QR Code kartu Anda ke kamera pemindai di sebelah kiri. Status presensi, waktu scan masuk/pulang, dan riwayat kehadiran Anda langsung muncul di panel ini.
                            </p>

                            <?php if (!empty($currentUserData)): ?>
                                <div class="p-3 rounded-4 bg-adaptive-subtle border w-100 text-start mb-3 shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="small text-secondary fw-semibold">Akun Login Saat Ini:</span>
                                        <span class="badge badge-soft-warning font-monospace">Siap Scan Presensi</span>
                                    </div>
                                    <div class="fw-bold text-adaptive-title fs-6"><?= htmlspecialchars($currentUserData['nama']) ?></div>
                                    <div class="small text-secondary"><?= htmlspecialchars($currentUserData['role']) ?> &bull; <?= htmlspecialchars($currentUserData['kelas'] ?? ($currentUserData['nip'] ?? '-')) ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Riwayat Kehadiran Sebelumnya (Supaya panel kanan langsung terisi) -->
                            <?php if (!empty($riwayatTerakhir)): ?>
                                <div class="w-100 text-start mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold small text-adaptive-title"><i class="bi bi-clock-history me-1 text-primary"></i> Riwayat Kehadiran Sebelumnya:</span>
                                        <span class="badge bg-light text-secondary"><?= count($riwayatTerakhir) ?> Hari Terakhir</span>
                                    </div>
                                    <div class="table-responsive rounded-3 border overflow-hidden">
                                        <table class="table table-sm table-bordered table-adaptive align-middle mb-0 text-center" style="font-size: 0.82rem;">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Masuk</th>
                                                    <th>Pulang</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($riwayatTerakhir, 0, 4) as $rw): 
                                                    $rTgl = is_object($rw) ? date('d-m-Y', strtotime($rw->tanggal)) : ($rw['tanggal'] ?? '-');
                                                    $rMsk = is_object($rw) ? (!empty($rw->jam_masuk) ? substr($rw->jam_masuk, 0, 5) . ' WIB' : '-') : ($rw['jam_masuk'] ?? '-');
                                                    $rPlg = is_object($rw) ? (!empty($rw->jam_pulang) ? substr($rw->jam_pulang, 0, 5) . ' WIB' : '-') : ($rw['jam_pulang'] ?? '-');
                                                    $rSt = strtolower(is_object($rw) ? ($rw->status ?? 'hadir') : ($rw['status'] ?? 'hadir'));
                                                    
                                                    if (strpos($rSt, 'sakit') !== false) {
                                                        $rBadge = '<span class="badge bg-warning bg-opacity-10 text-warning">Sakit</span>';
                                                    } else if (strpos($rSt, 'izin') !== false) {
                                                        $rBadge = '<span class="badge bg-info bg-opacity-10 text-info">Izin</span>';
                                                    } else if (strpos($rSt, 'alpa') !== false) {
                                                        $rBadge = '<span class="badge bg-danger bg-opacity-10 text-danger">Alpa</span>';
                                                    } else if (strpos($rSt, 'terlambat') !== false || stripos($rKet, 'terlambat') !== false) {
                                                        $rBadge = '<span class="badge" style="background:#FEF3C7; color:#B45309; border:1px solid #FCD34D;"><i class="bi bi-clock-history me-1"></i>Terlambat</span>';
                                                    } else {
                                                        $rBadge = '<span class="badge bg-success bg-opacity-10 text-success">Hadir</span>';
                                                    }
                                                ?>
                                                    <tr>
                                                        <td class="fw-semibold"><?= htmlspecialchars($rTgl) ?></td>
                                                        <td><?= htmlspecialchars($rMsk) ?></td>
                                                        <td><?= htmlspecialchars($rPlg) ?></td>
                                                        <td><?= $rBadge ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($todayJadwal)): ?>
                                <div class="w-100 text-start">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold small text-adaptive-title"><i class="bi bi-calendar3 me-1 text-primary"></i> Jadwal Anda Hari Ini (<?= htmlspecialchars($namaHariIni) ?>):</span>
                                        <span class="badge bg-light text-secondary"><?= count($todayJadwal) ?> Sesi</span>
                                    </div>
                                    <div class="list-group list-group-flush rounded-3 border overflow-hidden">
                                        <?php foreach (array_slice($todayJadwal, 0, 3) as $tj): ?>
                                            <div class="list-group-item d-flex align-items-center justify-content-between p-2 bg-adaptive-subtle border-bottom">
                                                <div>
                                                    <div class="fw-semibold small text-adaptive-title"><?= htmlspecialchars($tj->nama_mapel) ?></div>
                                                    <small class="text-secondary" style="font-size: 0.75rem;"><?= htmlspecialchars($tj->nama_guru ?? ($tj->nama_kelas ?? '-')) ?></small>
                                                </div>
                                                <span class="badge bg-primary bg-opacity-10 text-primary small"><?= substr($tj->jam_mulai, 0, 5) ?> - <?= substr($tj->jam_selesai, 0, 5) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
/* Scoped Styling for Side-by-Side Split View */
.scan-split-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Tampilan Kamera Pemindai (Mode Mirror) */
#reader video,
#reader canvas {
    transform: scaleX(-1) !important;
    -webkit-transform: scaleX(-1) !important;
    object-fit: cover !important;
}

.scan-card-left,
.scan-card-right {
    min-height: 600px;
    border-radius: 20px !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

/* Targeting Viewfinder Overlay */
.scanner-viewport-wrapper {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
}

/* Hilangkan garis border internal bawaan html5-qrcode agar garis pembidik tidak double (cukup 1 garis rapi) */
#qr-shaded-region > div {
    display: none !important;
}
#reader__scan_region {
    border: none !important;
}
#reader__scan_region svg {
    display: none !important;
}

.scanner-overlay-brackets {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 240px;
    height: 240px;
    pointer-events: none;
    z-index: 5;
}

.scanner-bracket {
    position: absolute;
    width: 32px;
    height: 32px;
    border-color: #FFFFFF;
    border-style: solid;
    filter: drop-shadow(0 0 5px rgba(0, 0, 0, 0.5));
}

.scanner-bracket.top-left {
    top: 0;
    left: 0;
    border-width: 3.5px 0 0 3.5px;
    border-top-left-radius: 8px;
}
.scanner-bracket.top-right {
    top: 0;
    right: 0;
    border-width: 3.5px 3.5px 0 0;
    border-top-right-radius: 8px;
}
.scanner-bracket.bottom-left {
    bottom: 0;
    left: 0;
    border-width: 0 0 3.5px 3.5px;
    border-bottom-left-radius: 8px;
}
.scanner-bracket.bottom-right {
    bottom: 0;
    right: 0;
    border-width: 0 3.5px 3.5px 0;
    border-bottom-right-radius: 8px;
}

.scanner-laser-line {
    position: absolute;
    top: 0;
    left: 4px;
    right: 4px;
    height: 3px;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.8), #38BDF8, rgba(59, 130, 246, 0.8), transparent);
    box-shadow: 0 0 10px #38BDF8, 0 0 16px #3B82F6;
    animation: scannerScanLaser 2.4s ease-in-out infinite alternate;
}

@keyframes scannerScanLaser {
    0% { top: 6px; opacity: 0.7; }
    50% { opacity: 1; }
    100% { top: calc(100% - 6px); opacity: 0.7; }
}

.scanner-idle-circle {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: var(--very-light-blue);
    border: 2px dashed var(--soft-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(79, 143, 239, 0.15);
    animation: scannerPulse 2.5s infinite ease-in-out;
}

@keyframes scannerPulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 8px 24px rgba(79, 143, 239, 0.15);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 12px 30px rgba(79, 143, 239, 0.25);
    }
}

.scanner-live-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: #10B981;
    border-radius: 50%;
    animation: liveDotPulse 1.5s infinite;
}

@keyframes liveDotPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.75); }
}

.result-card-animated {
    animation: resultFadeIn 0.35s ease-out forwards;
}

@keyframes resultFadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Light / Dark Mode adaptive helpers */
[data-bs-theme="dark"] .bg-adaptive-subtle {
    background-color: #141F32 !important;
    border-color: #334155 !important;
    color: #F1F5F9 !important;
}
[data-bs-theme="light"] .bg-adaptive-subtle,
body:not([data-bs-theme="dark"]) .bg-adaptive-subtle {
    background-color: var(--very-light-blue) !important;
    border-color: var(--border-blue) !important;
    color: var(--dark-blue) !important;
}

[data-bs-theme="dark"] .text-adaptive-title {
    color: #F8FAFC !important;
}
[data-bs-theme="light"] .text-adaptive-title,
body:not([data-bs-theme="dark"]) .text-adaptive-title {
    color: var(--dark-blue) !important;
}

[data-bs-theme="dark"] .stat-box-modern {
    background-color: #0F172A !important;
    border-color: #334155 !important;
}
[data-bs-theme="light"] .stat-box-modern,
body:not([data-bs-theme="dark"]) .stat-box-modern {
    background-color: #FFFFFF !important;
    border-color: var(--border-blue) !important;
}

[data-bs-theme="dark"] .table-adaptive {
    color: #CBD5E1 !important;
}
[data-bs-theme="dark"] .table-adaptive thead th {
    background-color: #0F172A !important;
    color: #F1F5F9 !important;
    border-color: #334155 !important;
}
[data-bs-theme="dark"] .table-adaptive tbody td {
    background-color: #141F32 !important;
    color: #CBD5E1 !important;
    border-color: #334155 !important;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .scan-card-left,
    .scan-card-right {
        min-height: auto;
    }
}
</style>

<!-- Load html5-qrcode library for QR Code Scanner -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcode');
    const scanStatus = document.getElementById('scanStatus');
    const scanForm = document.getElementById('scanForm');
    const cameraErrorAlert = document.getElementById('cameraErrorAlert');
    const cameraErrorMessage = document.getElementById('cameraErrorMessage');
    const riwayatContent = document.getElementById('riwayatContent');
    const flashAlertsContainer = document.getElementById('flashAlertsContainer');
    const scannerTargetBox = document.getElementById('scannerTargetBox');
    
    let isScanning = true;

    // Web Audio API synthesizer for instant pleasant chime
    function playBeepSound(isSuccess = true) {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (isSuccess) {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime); // Note A5
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.22);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.22);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(260, ctx.currentTime);
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.28);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.28);
            }
        } catch (e) {
            // Audio context policy fallback
        }
    }

    // Live clock update
    const liveClockEl = document.getElementById('liveClock');
    if (liveClockEl) {
        setInterval(() => {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            liveClockEl.textContent = `${h}:${m}:${s}`;
        }, 1000);
    }

    // Render HTML card untuk hasil scan absensi secara instan
    function renderDynamicResult(res, statusMsg) {
        if (!res || !riwayatContent) return;

        const curStatus = (res.status || 'hadir').toLowerCase();
        let statusBadge = '';
        let cardBorderColor = '#10B981';
        let cardTitleColor = '#059669';
        let cardTitle = res.is_sudah_absen ? 'ABSENSI HARI INI TERCATAT' : 'ABSENSI BERHASIL DICATAT!';
        let headerBg = 'rgba(16, 185, 129, 0.1)';
        let headerIcon = 'bi-check-circle-fill text-success';
        let statusName = 'Hadir';

        const isLate = (res.status && res.status.toLowerCase().indexOf('terlambat') !== -1) || 
                       (res.keterangan && res.keterangan.toLowerCase().indexOf('terlambat') !== -1);

        if (curStatus.indexOf('sakit') !== -1) {
            statusName = 'Sakit';
            statusBadge = '<span class="badge" style="background:#FEF3C7; color:#B45309; border:1px solid #FCD34D; font-size:0.85rem; padding:0.4em 0.8em;"><i class="bi bi-bandaid me-1"></i>Sakit</span>';
            cardBorderColor = '#F59E0B';
            cardTitleColor = '#B45309';
            cardTitle = 'ABSENSI HARI INI TERCATAT (SAKIT)';
            headerBg = 'rgba(245, 158, 11, 0.1)';
            headerIcon = 'bi-bandaid-fill text-warning';
        } else if (curStatus.indexOf('izin') !== -1) {
            statusName = 'Izin';
            statusBadge = '<span class="badge" style="background:#E0F2FE; color:#0369A1; border:1px solid #BAE6FD; font-size:0.85rem; padding:0.4em 0.8em;"><i class="bi bi-envelope-paper me-1"></i>Izin</span>';
            cardBorderColor = '#0284C7';
            cardTitleColor = '#0369A1';
            cardTitle = 'ABSENSI HARI INI TERCATAT (IZIN)';
            headerBg = 'rgba(2, 132, 199, 0.1)';
            headerIcon = 'bi-envelope-paper-fill text-info';
        } else if (curStatus.indexOf('alpa') !== -1 || curStatus.indexOf('bolos') !== -1) {
            statusName = 'Alpa';
            statusBadge = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger fw-bold" style="font-size:0.85rem; padding:0.4em 0.8em;"><i class="bi bi-x-circle-fill me-1"></i>Alpa</span>';
            cardBorderColor = '#DC2626';
            cardTitleColor = '#DC2626';
            cardTitle = 'ABSENSI HARI INI TERCATAT (ALPA)';
            headerBg = 'rgba(220, 38, 38, 0.1)';
            headerIcon = 'bi-x-circle-fill text-danger';
        } else if (isLate) {
            statusName = 'Hadir (Terlambat)';
            statusBadge = '<span class="badge" style="background:#FEF3C7; color:#B45309; border:1px solid #FCD34D; font-size:0.85rem; padding:0.4em 0.8em;"><i class="bi bi-clock-history me-1"></i>Terlambat</span>';
            cardBorderColor = '#F59E0B';
            cardTitleColor = '#B45309';
            cardTitle = res.is_sudah_absen ? 'ABSENSI HARI INI TERCATAT (TERLAMBAT)' : 'ABSENSI BERHASIL DICATAT (TERLAMBAT)!';
            headerBg = 'rgba(245, 158, 11, 0.1)';
            headerIcon = 'bi-clock-history text-warning';
        } else {
            statusName = 'Hadir';
            statusBadge = '<span class="badge badge-soft-success" style="font-size:0.85rem; padding:0.4em 0.8em;"><i class="bi bi-check-circle-fill me-1"></i>Hadir</span>';
            cardBorderColor = '#10B981';
            cardTitleColor = '#059669';
            cardTitle = res.is_sudah_absen ? 'ABSENSI HARI INI TERCATAT' : 'ABSENSI BERHASIL DICATAT!';
            headerBg = 'rgba(16, 185, 129, 0.1)';
            headerIcon = 'bi-check-circle-fill text-success';
        }

        const isNonHadir = (curStatus.indexOf('sakit') !== -1 || curStatus.indexOf('izin') !== -1 || curStatus.indexOf('alpa') !== -1 || curStatus.indexOf('bolos') !== -1);
        const jamMasuk = isNonHadir ? '-' : (res.jam_masuk || '-');
        const jamPulang = isNonHadir ? '-' : (res.jam_pulang || '-');
        const roleScanned = res.role || (res.nis ? 'Siswa' : 'Guru');
        const statusKet = res.keterangan || (statusName === 'Alpa' ? 'Tidak ada keterangan' : (jamPulang !== '-' ? 'Kehadiran lengkap' : 'Hadir tepat waktu'));

        let extraIdentitas = '';
        if (res.nis) {
            extraIdentitas = `
                <div class="col-6 mt-2 pt-2 border-top">
                    <span class="text-secondary small fw-semibold d-block">NIS / NISN:</span>
                    <span class="fw-semibold text-adaptive-title">${escapeHtml(res.nis)}</span>
                </div>
                <div class="col-6 mt-2 pt-2 border-top">
                    <span class="text-secondary small fw-semibold d-block">Kelas:</span>
                    <span class="fw-semibold text-adaptive-title">${escapeHtml(res.kelas || '-')}</span>
                </div>
            `;
        } else {
            extraIdentitas = `
                <div class="col-6 mt-2 pt-2 border-top">
                    <span class="text-secondary small fw-semibold d-block">NIP:</span>
                    <span class="fw-semibold text-adaptive-title">${escapeHtml(res.nip || '-')}</span>
                </div>
                ${res.kelas_binaan ? `
                <div class="col-6 mt-2 pt-2 border-top">
                    <span class="text-secondary small fw-semibold d-block">Kelas Binaan:</span>
                    <span class="fw-semibold text-adaptive-title">${escapeHtml(res.kelas_binaan)}</span>
                </div>` : ''}
            `;
        }

        // Generate Riwayat rows
        let riwayatRows = '';
        if (res.riwayat && Array.isArray(res.riwayat) && res.riwayat.length > 0) {
            res.riwayat.slice(0, 5).forEach(rw => {
                const rSt = (rw.status || 'Hadir').toLowerCase();
                const rIsNonHadir = (rSt.indexOf('sakit') !== -1 || rSt.indexOf('izin') !== -1 || rSt.indexOf('alpa') !== -1 || rSt.indexOf('bolos') !== -1);
                const rMsk = rIsNonHadir ? '-' : (rw.jam_masuk || '-');
                const rPlg = rIsNonHadir ? '-' : (rw.jam_pulang || '-');
                let b = '<span class="badge bg-success bg-opacity-10 text-success">Hadir</span>';
                if (rSt.indexOf('sakit') !== -1) {
                    b = '<span class="badge bg-warning bg-opacity-10 text-warning">Sakit</span>';
                } else if (rSt.indexOf('izin') !== -1) {
                    b = '<span class="badge bg-info bg-opacity-10 text-info">Izin</span>';
                } else if (rSt.indexOf('alpa') !== -1) {
                    b = '<span class="badge bg-danger bg-opacity-10 text-danger">Alpa</span>';
                } else if (rSt.indexOf('terlambat') !== -1 || (rw.keterangan && rw.keterangan.toLowerCase().indexOf('terlambat') !== -1)) {
                    b = '<span class="badge" style="background:#FEF3C7; color:#B45309; border:1px solid #FCD34D;"><i class="bi bi-clock-history me-1"></i>Terlambat</span>';
                }
                riwayatRows += `
                    <tr>
                        <td class="fw-semibold">${escapeHtml(rw.tanggal)}</td>
                        <td>${escapeHtml(rMsk)}</td>
                        <td>${escapeHtml(rPlg)}</td>
                        <td>${b}</td>
                        <td class="text-secondary small">${escapeHtml(rw.keterangan || '-')}</td>
                    </tr>
                `;
            });
        }

        const riwayatSection = riwayatRows ? `
            <div class="border rounded-4 p-3 bg-adaptive-subtle mb-3">
                <h6 class="fw-bold mb-2 d-flex align-items-center justify-content-between text-adaptive-title" style="font-size: 0.9rem;">
                    <span><i class="bi bi-journal-text text-primary me-1"></i> Riwayat Presensi Terakhir</span>
                    <span class="badge bg-light text-secondary fw-normal">Daftar Kehadiran</span>
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-adaptive align-middle mb-0 text-center" style="font-size: 0.82rem;">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${riwayatRows}
                        </tbody>
                    </table>
                </div>
            </div>
        ` : '';

        // Jadwal Table Rows
        let jadwalRows = '';
        if (res.jadwal && Array.isArray(res.jadwal) && res.jadwal.length > 0) {
            res.jadwal.forEach(jdw => {
                const jamMulai = (jdw.jam_mulai || '').substring(0, 5);
                const jamSelesai = (jdw.jam_selesai || '').substring(0, 5);
                const guruAtauKelas = res.nis ? (jdw.nama_guru || 'Pengajar') : (jdw.nama_kelas || '-');
                jadwalRows += `
                    <tr>
                        <td><span class="badge" style="background-color: var(--light-blue); color: var(--dark-blue); font-size: 0.78rem;">${jamMulai} - ${jamSelesai}</span></td>
                        <td class="fw-semibold text-adaptive-title">${escapeHtml(jdw.nama_mapel || '')}</td>
                        <td><span class="badge bg-light text-primary">${escapeHtml(guruAtauKelas)}</span></td>
                    </tr>
                `;
            });
        }

        const jadwalSection = res.jadwal && res.jadwal.length > 0 ? `
            <div class="border rounded-4 p-3 bg-adaptive-subtle">
                <h6 class="fw-bold mb-2 d-flex align-items-center gap-1 text-adaptive-title" style="font-size: 0.9rem;">
                    <i class="bi bi-journal-bookmark-fill text-primary"></i> 
                    ${res.nis ? 'Jadwal Pelajaran Hari Ini:' : 'Jadwal Mengajar Hari Ini:'}
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-modern table-adaptive mb-0" style="font-size: 0.83rem;">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Mata Pelajaran</th>
                                <th>${res.nis ? 'Guru' : 'Kelas'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${jadwalRows}
                        </tbody>
                    </table>
                </div>
            </div>
        ` : '';

        const html = `
            <div class="result-card-animated">
                <!-- Banner Status Presensi -->
                <div class="p-3 rounded-4 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: ${headerBg}; border-left: 5px solid ${cardBorderColor};">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi ${headerIcon} fs-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0" style="color: ${cardTitleColor};">${cardTitle}</h6>
                            <small class="text-secondary">${escapeHtml(res.tanggal)} &bull; SMK NU WARGABINANGUN</small>
                        </div>
                    </div>
                    <div>
                        ${statusBadge}
                    </div>
                </div>

                <!-- Data Profil Pemilik Barcode -->
                <div class="p-3 rounded-4 bg-adaptive-subtle mb-3 border">
                    <div class="row g-2 align-items-center">
                        <div class="col-sm-7">
                            <span class="text-secondary small fw-semibold d-block">Nama Lengkap:</span>
                            <span class="fw-bold fs-5 text-adaptive-title">${escapeHtml(res.nama)}</span>
                        </div>
                        <div class="col-sm-5 text-sm-end">
                            <span class="badge px-3 py-2 rounded-pill" style="background-color: var(--light-blue); color: var(--dark-blue); font-size: 0.85rem;">
                                <i class="bi bi-person-badge me-1"></i>${escapeHtml(roleScanned)}
                            </span>
                        </div>
                        ${extraIdentitas}
                    </div>
                </div>

                <!-- Stat Cards Jam Masuk & Jam Pulang -->
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="p-3 rounded-4 stat-box-modern border shadow-sm h-100">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-secondary small fw-semibold">Scan Masuk</span>
                                <i class="bi bi-box-arrow-in-right text-success fs-5"></i>
                            </div>
                            <div class="fs-4 fw-bold text-success mb-1">${escapeHtml(jamMasuk)}</div>
                            <small class="text-secondary d-block" style="font-size: 0.75rem;">${isNonHadir ? 'Tidak perlu scan' : 'Bebas Kapan Saja (Mode Demo)'}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-4 stat-box-modern border shadow-sm h-100">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-secondary small fw-semibold">Scan Pulang</span>
                                <i class="bi bi-box-arrow-left text-primary fs-5"></i>
                            </div>
                            <div class="fs-4 fw-bold text-primary mb-1">${escapeHtml(jamPulang)}</div>
                            <small class="text-secondary d-block" style="font-size: 0.75rem;">${isNonHadir ? 'Tidak perlu scan' : ((jamPulang !== '-') ? 'Kepulangan tercatat' : 'Bebas Kapan Saja (Mode Demo)')}</small>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Presensi Hari Ini -->
                <div class="border rounded-4 p-3 bg-adaptive-subtle mb-3">
                    <h6 class="fw-bold mb-2 d-flex align-items-center gap-1 text-adaptive-title" style="font-size: 0.9rem;">
                        <i class="bi bi-clock-history text-primary"></i> Ringkasan Presensi Hari Ini
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-adaptive align-middle mb-0 text-center" style="font-size: 0.84rem;">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Masuk</th>
                                    <th>Pulang</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">${escapeHtml(res.tanggal)}</td>
                                    <td>
                                        ${jamMasuk !== '-' ? `<span class="badge bg-success bg-opacity-10 text-success fw-bold">${escapeHtml(jamMasuk)}</span>` : `<span class="text-muted fw-semibold">-</span>`}
                                    </td>
                                    <td>
                                        ${jamPulang !== '-' ? `<span class="badge bg-primary bg-opacity-10 text-primary fw-bold">${escapeHtml(jamPulang)}</span>` : (!isNonHadir && (statusName === 'Hadir' || statusName.indexOf('Terlambat') !== -1) ? `<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Belum Scan</span>` : `<span class="text-muted fw-semibold">-</span>`)}
                                    </td>
                                    <td>${statusBadge}</td>
                                    <td class="text-secondary small fw-medium">${escapeHtml(statusKet)}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                ${riwayatSection}
                ${jadwalSection}
            </div>
        `;

        riwayatContent.innerHTML = html;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function showFlashNotice(type, msg) {
        if (!flashAlertsContainer || !msg) return;
        const alertClass = type === 'success' ? 'badge-soft-success' : (type === 'info' ? 'badge-soft-info' : (type === 'warning' ? 'badge-soft-warning' : 'badge-soft-danger'));
        const iconClass = type === 'success' ? 'bi-check-circle-fill' : (type === 'info' ? 'bi-info-circle-fill' : 'bi-exclamation-triangle-fill');
        
        flashAlertsContainer.innerHTML = `
            <div class="alert alert-dismissible fade show text-start border-0 shadow-sm ${alertClass} mb-3" role="alert">
                <i class="bi ${iconClass} me-2"></i> ${escapeHtml(msg)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    let isProcessingScan = false;

    function onScanSuccess(decodedText, decodedResult) {
        if (!isScanning || isProcessingScan) return;
        isProcessingScan = true;
        isScanning = false;
        
        console.log("QR Code terdeteksi: ", decodedText);

        const isWeekend = <?= $isWeekend ? 'true' : 'false' ?>;
        if (isWeekend) {
            playBeepSound(false);
            showFlashNotice('error', 'Absensi gagal! Hari Minggu kegiatan belajar mengajar (KBM) libur, absensi tidak dapat dilakukan.');
            scanStatus.style.backgroundColor = "#FDE8E8";
            scanStatus.style.color = "#9B1C1C";
            scanStatus.innerHTML = '<i class="bi bi-calendar-x-fill me-1"></i> Presensi Libur';
            setTimeout(() => {
                isScanning = true;
                isProcessingScan = false;
                scanStatus.style.backgroundColor = "rgba(245, 158, 11, 0.15)";
                scanStatus.style.color = "#B45309";
                scanStatus.innerHTML = '<i class="bi bi-calendar-x-fill me-1"></i> Presensi Ditutup (Hari Libur)';
            }, 2500);
            return;
        }
        
        // Mainkan suara chime konfirmasi instan
        playBeepSound(true);

        scanStatus.style.backgroundColor = "#DEF7EC";
        scanStatus.style.color = "#03543F";
        scanStatus.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Membaca Presensi...';

        const formData = new FormData(scanForm);
        formData.set('barcode', decodedText);
        formData.set('ajax', '1');

        fetch('<?= base_url('absensi/proses_scan') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            const ct = response.headers.get("content-type");
            if (ct && ct.indexOf("application/json") !== -1) {
                return response.json();
            } else {
                barcodeInput.value = decodedText;
                scanForm.submit();
                return null;
            }
        })
        .then(data => {
            if (!data) return;

            if (data.status === 'error') {
                playBeepSound(false);
                if (data.scan_result) {
                    renderDynamicResult(data.scan_result, data.message);
                }
                if (data.message) {
                    showFlashNotice('error', data.message);
                }
                scanStatus.style.backgroundColor = "#FDE8E8";
                scanStatus.style.color = "#9B1C1C";
                scanStatus.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> ' + (data.message ? (data.message.length > 35 ? data.message.substring(0, 35) + '...' : data.message) : 'Presensi Gagal');
            } else if (data.scan_result) {
                renderDynamicResult(data.scan_result, data.message);
                if (data.message) {
                    showFlashNotice(data.status || 'success', data.message);
                }
                scanStatus.style.backgroundColor = "#DEF7EC";
                scanStatus.style.color = "#03543F";
                scanStatus.innerHTML = '<i class="bi bi-check2-all me-1"></i> ' + (data.status === 'info' ? 'Informasi Presensi' : 'Absensi Berhasil!');
            } else {
                playBeepSound(false);
                if (data.message) {
                    showFlashNotice('error', data.message);
                }
                scanStatus.style.backgroundColor = "#FDE8E8";
                scanStatus.style.color = "#9B1C1C";
                scanStatus.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> ' + (data.message ? (data.message.length > 35 ? data.message.substring(0, 35) + '...' : data.message) : 'Gagal');
            }

            // Siapkan scanner kembali setelah 2 detik untuk scan berikutnya
            setTimeout(() => {
                isScanning = true;
                isProcessingScan = false;
                <?php if ($isWeekend): ?>
                scanStatus.style.backgroundColor = "rgba(245, 158, 11, 0.15)";
                scanStatus.style.color = "#B45309";
                scanStatus.innerHTML = '<i class="bi bi-calendar-x-fill me-1"></i> Presensi Ditutup (Hari Libur)';
                <?php else: ?>
                scanStatus.style.backgroundColor = "var(--light-blue)";
                scanStatus.style.color = "var(--dark-blue)";
                scanStatus.innerHTML = '<span class="scanner-live-dot me-1"></span> Kamera Aktif';
                <?php endif; ?>
            }, 2000);
        })
        .catch(err => {
            console.error("AJAX gagal, submit form standar:", err);
            barcodeInput.value = decodedText;
            scanForm.submit();
        });
    }

    function onScanFailure(error) {
        // Frame scanning cycle
    }

    // Pastikan video kamera selalu dalam mode mirror (flip horizontal)
    const readerContainer = document.getElementById('reader');
    if (readerContainer) {
        const videoObserver = new MutationObserver(() => {
            const video = readerContainer.querySelector('video');
            if (video) {
                video.style.setProperty('transform', 'scaleX(-1)', 'important');
                video.style.setProperty('-webkit-transform', 'scaleX(-1)', 'important');
            }
        });
        videoObserver.observe(readerContainer, { childList: true, subtree: true });
    }

    // Inisialisasi Html5Qrcode dengan kamera pemindai (Akselerasi BarcodeDetector C++ & Multi-Format)
    let html5QrCode = null;
    const scannerOptions = { 
        verbose: false,
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true
        },
        formatsToSupport: [ 
            Html5QrcodeSupportedFormats.QR_CODE,
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39
        ] 
    };

    try {
        html5QrCode = new Html5Qrcode("reader", scannerOptions);
    } catch (e) {
        html5QrCode = new Html5Qrcode("reader");
    }
    
    const config = { 
        fps: 25, 
        qrbox: { width: 240, height: 240 },
        aspectRatio: 1.0
    };

    scanStatus.style.backgroundColor = "var(--light-blue)";
    scanStatus.style.color = "var(--medium-blue)";
    scanStatus.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Meminta izin kamera...';

    html5QrCode.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanFailure
    ).then(() => {
        let ph = document.getElementById('readerPlaceholder');
        if (ph) ph.style.display = 'none';
        if (scannerTargetBox) scannerTargetBox.style.display = 'block';

        // Pastikan kamera dalam posisi mirror
        const video = document.querySelector('#reader video');
        if (video) {
            video.style.setProperty('transform', 'scaleX(-1)', 'important');
            video.style.setProperty('-webkit-transform', 'scaleX(-1)', 'important');
        }

        <?php if ($isWeekend): ?>
        scanStatus.style.backgroundColor = "rgba(245, 158, 11, 0.15)";
        scanStatus.style.color = "#B45309";
        scanStatus.innerHTML = '<i class="bi bi-calendar-x-fill me-1"></i> Presensi Ditutup (Hari Libur)';
        <?php else: ?>
        scanStatus.style.backgroundColor = "var(--light-blue)";
        scanStatus.style.color = "var(--dark-blue)";
        scanStatus.innerHTML = '<span class="scanner-live-dot me-1"></span> Kamera Aktif';
        <?php endif; ?>
    }).catch((err) => {
        let msg = "Akses kamera tidak diizinkan atau perangkat tidak memiliki kamera.";
        if (err && err.name === "NotAllowedError") {
            msg = "Akses kamera ditolak oleh browser. Harap izinkan kamera pada pengaturan browser Anda.";
        } else if (err && err.name === "NotFoundError") {
            msg = "Tidak ada perangkat kamera yang terdeteksi pada perangkat ini.";
        } else if (err && typeof err === 'string') {
            msg = err;
        }

        cameraErrorMessage.innerText = msg;
        cameraErrorAlert.classList.remove('d-none');

        scanStatus.style.backgroundColor = "#FDE8E8";
        scanStatus.style.color = "#9B1C1C";
        scanStatus.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Kamera Tidak Dapat Digunakan';
        console.error("Camera start failed", err);
    });

    window.addEventListener('beforeunload', function() {
        if (html5QrCode && html5QrCode.getState() === 2) {
            html5QrCode.stop().catch(err => console.error("Failed to stop scanner on unload", err));
        }
    });
});
</script>
