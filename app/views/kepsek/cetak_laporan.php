<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($filename ?? 'Laporan_Presensi') ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 15mm 15mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000000 !important;
            background-color: #ffffff !important;
            line-height: 1.35;
            margin: 0;
            padding: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 25px; }

        /* KOP SURAT RESMI SEKOLAH */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            border-bottom: 3px double #000000;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }
        .kop-text {
            text-align: center;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #000000;
            text-transform: uppercase;
        }
        .kop-text h2 {
            margin: 2px 0 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #000000;
        }
        .kop-text .kop-sub {
            margin: 2px 0 0;
            font-size: 11px;
            font-weight: 600;
            color: #222222;
        }
        .kop-text .kop-alamat {
            margin: 2px 0 0;
            font-size: 10px;
            color: #333333;
        }

        .judul-laporan {
            text-align: center;
            margin-bottom: 15px;
        }
        .judul-laporan h3 {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .judul-laporan p {
            margin: 2px 0 0;
            font-size: 10.5px;
            color: #444444;
        }

        /* METADATA INFO */
        .info-grid {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .info-grid td {
            vertical-align: top;
            border: none;
            padding: 0;
        }
        
        .filter-table {
            border-collapse: collapse;
            font-size: 10.5px;
        }
        .filter-table td {
            padding: 2px 4px;
            border: none;
        }

        .summary-card {
            border: 1px solid #000000;
            padding: 8px 12px;
            display: inline-block;
            font-size: 10.5px;
            background: #fafafa;
        }
        .summary-card table {
            border-collapse: collapse;
            width: 100%;
        }
        .summary-card td {
            padding: 2px 6px;
            border: none;
        }

        /* TABEL UTAMA REKAP */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
            font-size: 10.5px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000000;
            padding: 5px 6px;
            text-align: left;
        }
        table.data-table th {
            background-color: #e5e5e5 !important;
            color: #000000 !important;
            font-weight: bold;
            text-align: center;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* BADGES STATUS */
        .status-badge {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5px;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
            text-align: center;
        }
        .status-hadir { color: #065f46; font-weight: bold; }
        .status-izin { color: #0369a1; font-weight: bold; }
        .status-sakit { color: #7e22ce; font-weight: bold; }
        .status-alpa { color: #991b1b; font-weight: bold; }

        /* TANDA TANGAN */
        .ttd-container {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
            font-size: 11px;
        }

        .no-print-bar {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }
        .btn-close-window {
            background: #64748b;
            color: #ffffff;
            border: none;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 8px;
        }
        .btn-close-window:hover {
            background: #475569;
        }

        @media print {
            .no-print, .no-print-bar { display: none !important; }
            body { padding: 0; margin: 0; }
            table.data-table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Bar Kontrol Cetak (Hanya tampil di layar, otomatis hilang saat cetak/disimpan ke PDF) -->
    <div class="no-print-bar no-print">
        <div>
            <strong style="color:#0f172a; font-size:13px;">Dokumen Rekapitulasi Presensi Siap Dicetak</strong>
            <p style="margin:2px 0 0; font-size:11px; color:#475569;">Gunakan menu <b>Destination: Save as PDF (Simpan sebagai PDF)</b> pada dialog cetak browser Anda.</p>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">
                &#128438; Cetak / Simpan PDF
            </button>
            <button class="btn-close-window" onclick="window.close()">
                Tutup
            </button>
        </div>
    </div>

    <!-- KOP SURAT RESMI SEKOLAH -->
    <div class="kop-surat">
        <img src="<?= base_url('public/assets/images/logo_smk.png?v=2') ?>" alt="Logo SMK" class="kop-logo">
        <div class="kop-text">
            <h4>YAYASAN PENDIDIKAN DAN SOSIAL ISLAM NAHDLATUL ULAMA</h4>
            <h2>SMK NU WARGABINANGUN</h2>
            <div class="kop-sub">NPSN: 69947514 &bull; NSS: 402021703058 &bull; STATUS: TERAKREDITASI</div>
            <div class="kop-alamat">Jl. Raya Wargabinangun No. 01 Kec. Kaliwedi Kab. Cirebon 45165 - Jawa Barat</div>
            <div class="kop-alamat">Email: smknuwargabinangun@gmail.com &bull; Website: smknuwargabinangun.sch.id</div>
        </div>
    </div>

    <?php
    if (!function_exists('format_tanggal_indo_kepsek')) {
        function format_tanggal_indo_kepsek($dateStr, $withTime = false, $withDay = false) {
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
    $isSiswa = ($jenis === 'siswa');

    if ($isGuru) {
        $judulLaporan = 'LAPORAN REKAPITULASI PRESENSI GURU';
        $kategoriPeran = 'Guru / Tenaga Pendidik';
    } else {
        $judulLaporan = 'LAPORAN REKAPITULASI PRESENSI SISWA';
        $kategoriPeran = 'Siswa / Peserta Didik';
    }

    $statusText = !empty($status_filter) ? ucfirst(htmlspecialchars($status_filter)) : 'Semua Status (Hadir, Izin, Sakit, Alpa)';
    $tglLaporanStr = format_tanggal_indo_kepsek($tanggal, false, true);
    $waktuUnduhStr = format_tanggal_indo_kepsek(date('Y-m-d H:i:s'), true);
    ?>

    <!-- JUDUL DOKUMEN -->
    <div class="judul-laporan">
        <h3><?= $judulLaporan ?></h3>
        <p>Data Tanggal: <strong><?= $tglLaporanStr ?></strong></p>
    </div>

    <!-- INFORMASI FILTER & RINGKASAN DATA -->
    <table class="info-grid">
        <tr>
            <td style="width: 58%;">
                <table class="filter-table">
                    <tr>
                        <td style="width: 140px; font-weight: bold;">Kategori Peran</td>
                        <td>: <?= $kategoriPeran ?></td>
                    </tr>
                    <?php if ($isSiswa): ?>
                    <tr>
                        <td style="font-weight: bold;">Rombongan Belajar</td>
                        <td>: <?= !empty($kelas_filter) ? htmlspecialchars($kelas_filter) : 'Semua Kelas' ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="font-weight: bold;">Status Kehadiran</td>
                        <td>: <?= $statusText ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Waktu Pengunduhan</td>
                        <td>: <?= $waktuUnduhStr ?></td>
                    </tr>
                </table>
            </td>
            <td style="width: 42%; text-align: right;">
                <div class="summary-card">
                    <div style="font-weight: bold; border-bottom: 1px solid #ddd; padding-bottom: 3px; margin-bottom: 4px; text-align: center;">
                        RINGKASAN KEHADIRAN
                    </div>
                    <table>
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

    <!-- TABEL DATA PRESENSI -->
    <table class="data-table">
        <thead>
            <?php if ($isGuru): ?>
                <tr>
                    <th width="4%" class="text-center">No</th>
                    <th width="32%">Nama Guru</th>
                    <th width="18%" class="text-center">NIP</th>
                    <th width="11%">Jam Masuk</th>
                    <th width="11%">Jam Pulang</th>
                    <th width="11%">Status</th>
                    <th width="13%">Keterangan</th>
                </tr>
            <?php else: ?>
                <tr>
                    <th width="4%" class="text-center">No</th>
                    <th width="28%">Nama Siswa</th>
                    <th width="15%" class="text-center">NIS</th>
                    <th width="15%" class="text-center">Kelas</th>
                    <th width="10%">Jam Masuk</th>
                    <th width="10%">Jam Pulang</th>
                    <th width="10%">Status</th>
                    <th width="8%">Keterangan</th>
                </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php if (!empty($absensi)): ?>
                <?php 
                $no = 1;
                foreach ($absensi as $r): 
                    $rawStatus = strtolower($r->status ?? '');
                    if (in_array($rawStatus, ['hadir', 'terlambat'])) {
                        $badgeStyle = 'status-hadir';
                        $stText = 'HADIR';
                    } elseif ($rawStatus === 'izin') {
                        $badgeStyle = 'status-izin';
                        $stText = 'IZIN';
                    } elseif ($rawStatus === 'sakit') {
                        $badgeStyle = 'status-sakit';
                        $stText = 'SAKIT';
                    } else {
                        $badgeStyle = 'status-alpa';
                        $stText = 'ALPA';
                    }
                    $ketText = !empty($r->keterangan) ? htmlspecialchars($r->keterangan) : '-';
                ?>
                <?php if ($isGuru): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><b><?= htmlspecialchars($r->nama_lengkap ?? '-') ?></b></td>
                        <td class="text-center"><?= htmlspecialchars(!empty($r->nip) && $r->nip !== '-' ? $r->nip : '-') ?></td>
                        <td class="text-center"><?= !empty($r->jam_masuk) ? substr($r->jam_masuk, 0, 5) . ' WIB' : '-' ?></td>
                        <td class="text-center"><?= !empty($r->jam_pulang) ? substr($r->jam_pulang, 0, 5) . ' WIB' : '-' ?></td>
                        <td class="text-center">
                            <span class="status-badge <?= $badgeStyle ?>"><?= $stText ?></span>
                        </td>
                        <td><?= $ketText ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><b><?= htmlspecialchars($r->nama_lengkap ?? '-') ?></b></td>
                        <td class="text-center"><?= htmlspecialchars($r->nis ?? '-') ?></td>
                        <td class="text-center"><?= htmlspecialchars($r->nama_kelas ?? '-') ?></td>
                        <td class="text-center"><?= !empty($r->jam_masuk) ? substr($r->jam_masuk, 0, 5) . ' WIB' : '-' ?></td>
                        <td class="text-center"><?= !empty($r->jam_pulang) ? substr($r->jam_pulang, 0, 5) . ' WIB' : '-' ?></td>
                        <td class="text-center">
                            <span class="status-badge <?= $badgeStyle ?>"><?= $stText ?></span>
                        </td>
                        <td><?= $ketText ?></td>
                    </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $isGuru ? 7 : 8 ?>" class="text-center" style="padding: 20px;">
                        <i>Tidak ada catatan riwayat absensi pada tanggal dan filter yang dipilih.</i>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN / TANDA TANGAN RESMI -->
    <div class="ttd-container">
        <div class="ttd-box">
            <div>Cirebon, <?= format_tanggal_indo_kepsek(date('Y-m-d')) ?></div>
            <div style="margin-top: 4px;">Mengetahui,</div>
            <div style="font-weight: bold;">Kepala SMK NU WARGABINANGUN</div>
            <div style="height: 60px;"></div>
            <div style="font-weight: bold; text-decoration: underline;">Drs. H. Ahmad Fauzi, M.Pd</div>
            <div>NIP. 19750312 200003 1 004</div>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>

