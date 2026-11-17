<?php
if (!function_exists('format_tanggal_indo_kepsek_view')) {
    function format_tanggal_indo_kepsek_view($dateStr, $withTime = false, $withDay = false) {
        if (empty($dateStr)) return '-';
        $timestamp = strtotime($dateStr);
        if (!$timestamp) return $dateStr;
        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $d = date('d', $timestamp);
        $m = (int)date('m', $timestamp);
        $y = date('Y', $timestamp);
        $res = '';
        if ($withDay) {
            $dayName = $hariIndo[date('l', $timestamp)] ?? '';
            $res .= $dayName . ', ';
        }
        $res .= $d . ' ' . ($bulanIndo[$m] ?? date('F', $timestamp)) . ' ' . $y;
        if ($withTime) {
            $res .= ' - ' . date('H:i:s', $timestamp) . ' WIB';
        }
        return $res;
    }
}

$isGuru = ($jenis === 'guru');
$tglIndoLengkap = format_tanggal_indo_kepsek_view($tanggal, false, true);
$tglHariIniIndo = format_tanggal_indo_kepsek_view(date('Y-m-d'));
$waktuUnduhIndo = format_tanggal_indo_kepsek_view(date('Y-m-d H:i:s'), true);
?>

<style>
/* Sembunyikan elemen dokumen cetak di tampilan layar web */
.print-only-doc {
    display: none !important;
}

/* Isolasi Media Cetak: Otomatis Menjadi Dokumen Resmi Putih Bersih */
@media print {
    @page {
        size: A4 landscape;
        margin: 12mm 15mm 15mm 15mm;
    }
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    html, body, [data-bs-theme="dark"], body.bg-light {
        background: #FFFFFF !important;
        background-color: #FFFFFF !important;
        color: #000000 !important;
        padding: 0 !important;
        margin: 0 !important;
        font-family: Arial, Helvetica, sans-serif !important;
    }
    .main-content, .container-fluid, .content {
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    .no-print, .navbar-custom, #appSidebar, #sidebarBackdrop, .btn-hamburger, .header-profile-btn, .header-theme-toggle-btn, .chatbot-btn, .chatbot-window, footer {
        display: none !important;
    }
    .print-only-doc {
        display: block !important;
    }

    /* KOP SURAT RESMI */
    .kop-surat-doc {
        display: flex !important;
        align-items: center;
        justify-content: center;
        gap: 20px;
        border-bottom: 3px double #000000 !important;
        padding-bottom: 12px;
        margin-bottom: 15px;
    }
    .kop-logo-doc {
        width: 75px;
        height: 75px;
        object-fit: contain;
    }
    .kop-text-doc {
        text-align: center;
    }
    .kop-text-doc h4 {
        margin: 0;
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 0.5px;
        color: #000000 !important;
        text-transform: uppercase;
    }
    .kop-text-doc h2 {
        margin: 2px 0 0;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: 1px;
        color: #000000 !important;
    }
    .kop-text-doc .kop-sub-doc {
        margin: 2px 0 0;
        font-size: 11px;
        font-weight: 600;
        color: #222222 !important;
    }
    .kop-text-doc .kop-alamat-doc {
        margin: 2px 0 0;
        font-size: 10px;
        color: #333333 !important;
    }

    /* Judul Dokumen Laporan */
    .judul-laporan-doc {
        text-align: center;
        margin-bottom: 15px;
    }
    .judul-laporan-doc h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        text-decoration: underline;
        text-transform: uppercase;
        color: #000000 !important;
    }
    .judul-laporan-doc p {
        margin: 2px 0 0;
        font-size: 10.5px;
        color: #444444 !important;
    }

    /* Info Grid Metadata */
    .info-grid-doc {
        width: 100%;
        margin-bottom: 12px;
        border-collapse: collapse;
    }
    .info-grid-doc td {
        vertical-align: top;
        border: none !important;
        padding: 0;
    }
    .filter-table-doc {
        border-collapse: collapse;
        font-size: 10.5px;
    }
    .filter-table-doc td {
        padding: 2px 4px;
        border: none !important;
        color: #000000 !important;
    }
    .summary-box-doc {
        border: 1px solid #000000 !important;
        padding: 6px 10px;
        display: inline-block;
        font-size: 10px;
        background: #fafafa !important;
        color: #000000 !important;
    }
    .summary-box-doc td {
        padding: 1px 4px;
        border: none !important;
        color: #000000 !important;
    }

    /* Reset Card & Tabel */
    .card, .card-modern {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .card-body {
        padding: 0 !important;
    }
    .table-modern-container {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        overflow: visible !important;
    }
    table.table-modern {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 10px;
        margin-bottom: 15px;
        font-size: 10.5px !important;
        background-color: #ffffff !important;
        color: #000000 !important;
    }
    table.table-modern th, table.table-modern td {
        border: 1px solid #000000 !important;
        padding: 5px 6px !important;
        color: #000000 !important;
        background-color: #ffffff !important;
    }
    table.table-modern th {
        background-color: #e5e5e5 !important;
        font-weight: bold !important;
        text-align: center !important;
    }
    table.table-modern tbody tr:nth-child(even) td {
        background-color: #f9f9f9 !important;
    }
    .badge {
        background: none !important;
        border: none !important;
        padding: 0 !important;
        font-size: 10px !important;
        font-weight: bold !important;
    }
    .badge-soft-success { color: #065f46 !important; }
    .badge-soft-info { color: #0369a1 !important; }
    .badge-soft-primary { color: #7e22ce !important; }
    .badge-soft-danger { color: #991b1b !important; }

    /* Lembar Tanda Tangan */
    .ttd-container-doc {
        width: 100%;
        margin-top: 25px;
        page-break-inside: avoid;
    }
    .ttd-box-doc {
        float: right;
        width: 250px;
        text-align: center;
        font-size: 11px;
        color: #000000 !important;
    }
}
</style>

<!-- KOP SURAT & HEADER DOKUMEN CETAK (HANYA TAMPIL SAAT PRINT / SIMPAN PDF) -->
<div class="print-only-doc">
    <div class="kop-surat-doc">
        <img src="<?= base_url('public/assets/images/logo_smk.png?v=2') ?>" alt="Logo SMK" class="kop-logo-doc">
        <div class="kop-text-doc">
            <h4>YAYASAN PENDIDIKAN DAN SOSIAL ISLAM NAHDLATUL ULAMA</h4>
            <h2>SMK NU WARGABINANGUN</h2>
            <div class="kop-sub-doc">NPSN: 69947514 &bull; NSS: 402021703058 &bull; STATUS: TERAKREDITASI</div>
            <div class="kop-alamat-doc">Jl. Raya Wargabinangun No. 01 Kec. Kaliwedi Kab. Cirebon 45165 - Jawa Barat</div>
            <div class="kop-alamat-doc">Email: smknuwargabinangun@gmail.com &bull; Website: smknuwargabinangun.sch.id</div>
        </div>
    </div>

    <!-- Judul Dokumen Resmi -->
    <div class="judul-laporan-doc">
        <h3><?= $isGuru ? 'LAPORAN REKAPITULASI PRESENSI GURU' : 'LAPORAN REKAPITULASI PRESENSI SISWA' ?></h3>
        <p>Data Tanggal: <strong><?= $tglIndoLengkap ?></strong></p>
    </div>

    <!-- Metadata Informasi Dokumen -->
    <table class="info-grid-doc">
        <tr>
            <td style="width: 58%;">
                <table class="filter-table-doc">
                    <tr>
                        <td style="width: 140px; font-weight: bold;">Kategori Peran</td>
                        <td>: <?= $isGuru ? 'Guru / Tenaga Pendidik' : 'Siswa / Peserta Didik' ?></td>
                    </tr>
                    <?php if (!$isGuru): ?>
                    <tr>
                        <td style="font-weight: bold;">Rombongan Belajar</td>
                        <td>: <?= !empty($kelas_filter) ? htmlspecialchars($kelas_filter) : 'Semua Kelas' ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="font-weight: bold;">Status Kehadiran</td>
                        <td>: <?= !empty($status_filter) ? ucfirst(htmlspecialchars($status_filter)) : 'Semua Status (Hadir, Izin, Sakit, Alpa)' ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Waktu Pengunduhan</td>
                        <td>: <?= $waktuUnduhIndo ?></td>
                    </tr>
                </table>
            </td>
            <td style="width: 42%; text-align: right;">
                <div class="summary-box-doc">
                    <div style="font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 2px; margin-bottom: 3px; text-align: center;">
                        RINGKASAN KEHADIRAN
                    </div>
                    <table style="width: 100%;">
                        <tr>
                            <td>Total Data Tercatat</td>
                            <td>:</td>
                            <td class="text-right"><b><?= $rekap_count['total'] ?? count($absensi) ?></b></td>
                        </tr>
                        <tr>
                            <td style="color: #065f46;">Hadir</td>
                            <td>:</td>
                            <td class="text-right"><b><?= $rekap_count['hadir'] ?? 0 ?></b></td>
                        </tr>
                        <tr>
                            <td style="color: #0369a1;">Izin</td>
                            <td>:</td>
                            <td class="text-right"><b><?= $rekap_count['izin'] ?? 0 ?></b></td>
                        </tr>
                        <tr>
                            <td style="color: #7e22ce;">Sakit</td>
                            <td>:</td>
                            <td class="text-right"><b><?= $rekap_count['sakit'] ?? 0 ?></b></td>
                        </tr>
                        <tr>
                            <td style="color: #991b1b;">Alpa / Bolos</td>
                            <td>:</td>
                            <td class="text-right"><b><?= $rekap_count['alpa'] ?? 0 ?></b></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- Header Laporan Kehadiran (Layar Web) -->
<div class="no-print d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold mb-1" style="color: var(--dark-blue);">
            <i class="bi bi-file-earmark-bar-graph me-2" style="color: var(--medium-blue);"></i>Laporan Kehadiran
        </h1>
        <p class="text-secondary small mb-0">Laporan rekapitulasi data presensi harian siswa dan guru SMK NU WARGABINANGUN.</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 d-flex gap-2">
        <a href="<?= base_url('kepala-sekolah/cetak-laporan?tanggal=' . urlencode($tanggal) . '&jenis=' . urlencode($jenis) . '&status=' . urlencode($status_filter) . '&kelas_nama=' . urlencode($kelas_filter)) ?>" target="_blank" class="btn btn-primary btn-sm shadow-sm" style="border-radius: 8px;">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen Laporan
        </a>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-soft-blue" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Filter Bar Laporan (Layar Web) -->
<div class="no-print card card-modern border-0 mb-4 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom" style="border-color: var(--border-blue) !important;">
        <span class="fw-bold" style="color: var(--dark-blue);"><i class="bi bi-funnel me-1"></i> Parameter Laporan</span>
    </div>
    <div class="card-body p-3">
        <form action="<?= base_url('kepala-sekolah/laporan') ?>" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="tanggal" class="form-label small fw-bold text-secondary mb-1">Tanggal Presensi:</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm" value="<?= htmlspecialchars($tanggal) ?>" style="border-color: var(--border-blue); color: var(--dark-blue);">
            </div>
            <div class="col-md-2">
                <label for="jenis" class="form-label small fw-bold text-secondary mb-1">Kategori Presensi:</label>
                <select name="jenis" id="jenis" class="form-select form-select-sm" style="border-color: var(--border-blue); color: var(--dark-blue);">
                    <option value="siswa" <?= $jenis == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                    <option value="guru" <?= $jenis == 'guru' ? 'selected' : '' ?>>Guru</option>
                </select>
            </div>
            <?php if ($jenis === 'siswa'): ?>
                <div class="col-md-2">
                    <label for="kelas_nama" class="form-label small fw-bold text-secondary mb-1">Filter Kelas:</label>
                    <select name="kelas_nama" id="kelas_nama" class="form-select form-select-sm" style="border-color: var(--border-blue); color: var(--dark-blue);">
                        <option value="">Semua Kelas</option>
                        <?php if(!empty($all_kelas)): ?>
                            <?php foreach($all_kelas as $k): ?>
                                <option value="<?= htmlspecialchars($k->nama_kelas) ?>" <?= ($kelas_filter === $k->nama_kelas) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k->nama_kelas) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-md-2">
                <label for="status" class="form-label small fw-bold text-secondary mb-1">Status Kehadiran:</label>
                <select name="status" id="status" class="form-select form-select-sm" style="border-color: var(--border-blue); color: var(--dark-blue);">
                    <option value="">Semua Status</option>
                    <option value="hadir" <?= ($status_filter === 'hadir') ? 'selected' : '' ?>>Hadir</option>
                    <option value="izin" <?= ($status_filter === 'izin') ? 'selected' : '' ?>>Izin</option>
                    <option value="sakit" <?= ($status_filter === 'sakit') ? 'selected' : '' ?>>Sakit</option>
                    <option value="alpa" <?= ($status_filter === 'alpa') ? 'selected' : '' ?>>Alpa</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-soft-blue flex-grow-1"><i class="bi bi-search me-1"></i> Tampilkan</button>
                <a href="<?= base_url('kepala-sekolah/laporan') ?>" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Ringkasan Statistik Laporan (Layar Web) -->
<div class="no-print row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #10B981 !important;">
            <span class="text-secondary small fw-bold text-uppercase">Hadir</span>
            <h3 class="fw-bold my-1 text-success"><?= $rekap_count['hadir'] ?? 0 ?></h3>
            <span class="small text-muted">Tercatat Hadir</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #0EA5E9 !important;">
            <span class="text-secondary small fw-bold text-uppercase">Izin</span>
            <h3 class="fw-bold my-1 text-info"><?= $rekap_count['izin'] ?? 0 ?></h3>
            <span class="small text-muted">Pengajuan Izin</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #3B82F6 !important;">
            <span class="text-secondary small fw-bold text-uppercase">Sakit</span>
            <h3 class="fw-bold my-1 text-primary"><?= $rekap_count['sakit'] ?? 0 ?></h3>
            <span class="small text-muted">Keterangan Sakit</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #EF4444 !important;">
            <span class="text-secondary small fw-bold text-uppercase">Alpa / Bolos</span>
            <h3 class="fw-bold my-1 text-danger"><?= $rekap_count['alpa'] ?? 0 ?></h3>
            <span class="small text-muted">Tanpa Keterangan</span>
        </div>
    </div>
</div>

<!-- Tabel Laporan Kehadiran -->
<div class="card card-modern border-0 mb-4 shadow-sm">
    <div class="no-print card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--border-blue) !important;">
        <span class="fw-bold" style="color: var(--dark-blue);">
            <i class="bi bi-card-checklist me-1" style="color: var(--medium-blue);"></i> Data Presensi <?= ucfirst($jenis) ?> &bull; <?= $tglIndoLengkap ?>
        </span>
        <span class="badge bg-light text-secondary border">Total Data: <?= count($absensi) ?></span>
    </div>
    
    <div class="card-body p-0">
        <div class="table-modern-container">
            <table class="table table-modern mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <?php if ($jenis == 'siswa'): ?>
                            <th width="15%" class="text-center">NIS</th>
                            <th width="28%">Nama Siswa</th>
                            <th width="15%" class="text-center">Kelas</th>
                        <?php else: ?>
                            <th width="18%" class="text-center">NIP</th>
                            <th width="34%">Nama Guru</th>
                        <?php endif; ?>
                        <th width="11%" class="text-center">Jam Masuk</th>
                        <th width="11%" class="text-center">Jam Pulang</th>
                        <th width="10%" class="text-center">Status</th>
                        <th width="10%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($absensi)): ?>
                        <?php $no = 1; foreach ($absensi as $a): ?>
                            <tr>
                                <td class="text-center"><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= $no++ ?></span></td>
                                <?php if ($jenis == 'siswa'): ?>
                                    <td class="fw-semibold text-muted text-center"><?= htmlspecialchars($a->nis) ?></td>
                                    <td class="fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($a->nama_lengkap) ?></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border"><?= htmlspecialchars($a->nama_kelas ?? '-') ?></span></td>
                                <?php else: ?>
                                    <td class="fw-semibold text-muted text-center"><?= htmlspecialchars($a->nip ?? '-') ?></td>
                                    <td class="fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($a->nama_lengkap) ?></td>
                                <?php endif; ?>
                                <td class="text-center"><?= !empty($a->jam_masuk) ? '<span class="text-success fw-bold">' . substr($a->jam_masuk, 0, 5) . '</span>' : '<span class="text-muted">-</span>' ?></td>
                                <td class="text-center"><?= !empty($a->jam_pulang) ? '<span class="text-primary fw-bold">' . substr($a->jam_pulang, 0, 5) . '</span>' : '<span class="text-muted">-</span>' ?></td>
                                <td class="text-center">
                                    <?php 
                                        $sta = strtolower($a->status ?? '');
                                        if (in_array($sta, ['hadir', 'terlambat'])): 
                                    ?>
                                        <span class="badge badge-soft-success">Hadir</span>
                                    <?php elseif ($sta == 'izin'): ?>
                                        <span class="badge badge-soft-info">Izin</span>
                                    <?php elseif ($sta == 'sakit'): ?>
                                        <span class="badge badge-soft-primary">Sakit</span>
                                    <?php else: ?>
                                        <span class="badge badge-soft-danger">Alpa</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="small text-muted"><?= !empty($a->keterangan) ? htmlspecialchars($a->keterangan) : '-' ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $jenis == 'siswa' ? 8 : 7 ?>" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>
                                Tidak ada data absensi <?= ucfirst($jenis) ?> pada tanggal dan filter yang dipilih.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Lembar Pengesahan / Tanda Tangan Resmi (Hanya Tampil Saat Print / Simpan PDF) -->
<div class="print-only-doc ttd-container-doc">
    <div class="ttd-box-doc">
        <div>Cirebon, <?= $tglHariIniIndo ?></div>
        <div style="margin-top: 4px;">Mengetahui,</div>
        <div style="font-weight: bold;">Kepala SMK NU WARGABINANGUN</div>
        <div style="height: 60px;"></div>
        <div style="font-weight: bold; text-decoration: underline;">Drs. H. Ahmad Fauzi, M.Pd</div>
        <div>NIP. 19750312 200003 1 004</div>
    </div>
    <div style="clear: both;"></div>
</div>
