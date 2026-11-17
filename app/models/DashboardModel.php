<?php
class DashboardModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAdminStats($tanggal) {
        // Otomatis sinkronisasi status bolos jika tidak scan pulang sebelum menghitung stats
        if (class_exists('AbsensiModel')) {
            $absensiModel = new AbsensiModel();
            $absensiModel->sinkronisasiStatusBolos();
        }

        $stats = [
            'total_siswa' => 0,
            'total_guru' => 0,
            'total_kelas' => 0,
            'total_jurusan' => 0,
            'siswa_hadir' => 0,
            'guru_hadir' => 0,
            'siswa_izin' => 0,
            'siswa_sakit' => 0,
            'siswa_alpa' => 0,
            'guru_izin' => 0,
            'guru_sakit' => 0,
            'guru_alpa' => 0,
            'detail_siswa_hadir' => [],
            'detail_siswa_izin' => [],
            'detail_siswa_sakit' => [],
            'detail_siswa_alpa' => [],
            'detail_guru_hadir' => [],
            'detail_guru_izin' => [],
            'detail_guru_sakit' => [],
            'detail_guru_alpa' => [],
            'trend_siswa' => [],
            'trend_guru' => [],
            'trend_labels' => []
        ];

        // Basic Counts
        $this->db->query("SELECT COUNT(id) as count FROM guru");
        $stats['total_guru'] = $this->db->single()->count ?? 0;

        $this->db->query("SELECT COUNT(id) as count FROM siswa");
        $stats['total_siswa'] = $this->db->single()->count ?? 0;

        $this->db->query("SELECT COUNT(id) as count FROM kelas");
        $stats['total_kelas'] = $this->db->single()->count ?? 0;

        $this->db->query("SELECT COUNT(id) as count FROM jurusan");
        $stats['total_jurusan'] = $this->db->single()->count ?? 0;

        $dow = (int)date('w', strtotime($tanggal));
        $isWeekend = ($dow === 0 || $dow === 6);

        if (!$isWeekend) {
            // Absensi Siswa Hari Ini (Distinct per siswa to avoid overlaps)
            $this->db->query("SELECT LOWER(status) as st, COUNT(DISTINCT siswa_id) as jml FROM absensi_siswa WHERE tanggal = :tanggal AND DAYOFWEEK(tanggal) NOT IN (1, 7) GROUP BY LOWER(status)");
            $this->db->bind(':tanggal', $tanggal);
            $resSiswa = $this->db->resultSet();
            foreach ($resSiswa as $r) {
                $st = $r->st;
                if ($st == 'hadir') $stats['siswa_hadir'] += $r->jml;
                elseif ($st == 'izin') $stats['siswa_izin'] += $r->jml;
                elseif ($st == 'sakit') $stats['siswa_sakit'] += $r->jml;
                elseif ($st == 'alpa' || $st == 'bolos') $stats['siswa_alpa'] += $r->jml;
            }

            // Absensi Guru Hari Ini (Distinct per guru)
            $this->db->query("SELECT LOWER(status) as st, COUNT(DISTINCT guru_id) as jml FROM absensi_guru WHERE tanggal = :tanggal AND DAYOFWEEK(tanggal) NOT IN (1, 7) GROUP BY LOWER(status)");
            $this->db->bind(':tanggal', $tanggal);
            $resGuru = $this->db->resultSet();
            foreach ($resGuru as $r) {
                $st = $r->st;
                if ($st == 'hadir') $stats['guru_hadir'] += $r->jml;
                elseif ($st == 'izin') $stats['guru_izin'] += $r->jml;
                elseif ($st == 'sakit') $stats['guru_sakit'] += $r->jml;
                elseif ($st == 'alpa' || $st == 'bolos') $stats['guru_alpa'] += $r->jml;
            }

            // Detail lists siswa
            $this->db->query("SELECT a.*, s.nama_lengkap, s.nis, s.nisn, k.nama_kelas 
                              FROM absensi_siswa a 
                              JOIN siswa s ON a.siswa_id = s.id 
                              LEFT JOIN kelas k ON s.kelas_id = k.id 
                              WHERE a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7) AND LOWER(a.status) = 'hadir' 
                              ORDER BY s.nama_lengkap ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['detail_siswa_hadir'] = $this->db->resultSet();

            $this->db->query("SELECT a.*, s.nama_lengkap, s.nis, s.nisn, k.nama_kelas, p.alasan, p.bukti_foto 
                              FROM absensi_siswa a 
                              JOIN siswa s ON a.siswa_id = s.id 
                              LEFT JOIN kelas k ON s.kelas_id = k.id 
                              LEFT JOIN pengajuan_izin p ON (p.siswa_id = s.id AND p.tanggal = a.tanggal AND p.status = 'Disetujui') 
                              WHERE a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7) AND LOWER(a.status) = 'izin' 
                              ORDER BY s.nama_lengkap ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['detail_siswa_izin'] = $this->db->resultSet();

            $this->db->query("SELECT a.*, s.nama_lengkap, s.nis, s.nisn, k.nama_kelas, p.alasan, p.bukti_foto 
                              FROM absensi_siswa a 
                              JOIN siswa s ON a.siswa_id = s.id 
                              LEFT JOIN kelas k ON s.kelas_id = k.id 
                              LEFT JOIN pengajuan_izin p ON (p.siswa_id = s.id AND p.tanggal = a.tanggal AND p.status = 'Disetujui') 
                              WHERE a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7) AND LOWER(a.status) = 'sakit' 
                              ORDER BY s.nama_lengkap ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['detail_siswa_sakit'] = $this->db->resultSet();

            $this->db->query("SELECT a.*, s.nama_lengkap, s.nis, s.nisn, k.nama_kelas 
                              FROM absensi_siswa a 
                              JOIN siswa s ON a.siswa_id = s.id 
                              LEFT JOIN kelas k ON s.kelas_id = k.id 
                              WHERE a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7) AND (LOWER(a.status) = 'alpa' OR LOWER(a.status) = 'bolos') 
                              ORDER BY s.nama_lengkap ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['detail_siswa_alpa'] = $this->db->resultSet();

            // Detail lists guru
            $this->db->query("SELECT a.*, g.nama_lengkap, g.nip 
                              FROM absensi_guru a 
                              JOIN guru g ON a.guru_id = g.id 
                              WHERE a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7) AND LOWER(a.status) = 'hadir' 
                              ORDER BY g.nama_lengkap ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['detail_guru_hadir'] = $this->db->resultSet();

            $this->db->query("SELECT a.*, g.nama_lengkap, g.nip, p.alasan, p.bukti_foto 
                              FROM absensi_guru a 
                              JOIN guru g ON a.guru_id = g.id 
                              LEFT JOIN pengajuan_izin p ON (p.guru_id = g.id AND p.tanggal = a.tanggal AND p.status = 'Disetujui') 
                              WHERE a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7) AND LOWER(a.status) = 'izin' 
                              ORDER BY g.nama_lengkap ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['detail_guru_izin'] = $this->db->resultSet();

            $this->db->query("SELECT a.*, g.nama_lengkap, g.nip, p.alasan, p.bukti_foto 
                              FROM absensi_guru a 
                              JOIN guru g ON a.guru_id = g.id 
                              LEFT JOIN pengajuan_izin p ON (p.guru_id = g.id AND p.tanggal = a.tanggal AND p.status = 'Disetujui') 
                              WHERE a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7) AND LOWER(a.status) = 'sakit' 
                              ORDER BY g.nama_lengkap ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['detail_guru_sakit'] = $this->db->resultSet();

            $this->db->query("SELECT a.*, g.nama_lengkap, g.nip 
                              FROM absensi_guru a 
                              JOIN guru g ON a.guru_id = g.id 
                              WHERE a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7) AND (LOWER(a.status) = 'alpa' OR LOWER(a.status) = 'bolos') 
                              ORDER BY g.nama_lengkap ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['detail_guru_alpa'] = $this->db->resultSet();
        }

        // Trend 7 hari terakhir
        $tgl_arr = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl_arr[] = date('Y-m-d', strtotime("-{$i} days", strtotime($tanggal)));
            $stats['trend_labels'][] = date('d M', strtotime("-{$i} days", strtotime($tanggal)));
        }

        $startDate = $tgl_arr[0];
        $endDate = $tgl_arr[6];

        $this->db->query("SELECT tanggal, COUNT(DISTINCT CASE WHEN LOWER(status) IN ('hadir') THEN siswa_id ELSE NULL END) as hadir FROM absensi_siswa WHERE tanggal >= :start AND tanggal <= :end AND DAYOFWEEK(tanggal) NOT IN (1, 7) GROUP BY tanggal");
        $this->db->bind(':start', $startDate);
        $this->db->bind(':end', $endDate);
        $resTrendSiswa = $this->db->resultSet();
        $ts = [];
        foreach ($resTrendSiswa as $r) {
            $ts[$r->tanggal] = $r->hadir;
        }

        $this->db->query("SELECT tanggal, COUNT(DISTINCT CASE WHEN LOWER(status) IN ('hadir') THEN guru_id ELSE NULL END) as hadir FROM absensi_guru WHERE tanggal >= :start AND tanggal <= :end AND DAYOFWEEK(tanggal) NOT IN (1, 7) GROUP BY tanggal");
        $this->db->bind(':start', $startDate);
        $this->db->bind(':end', $endDate);
        $resTrendGuru = $this->db->resultSet();
        $tg = [];
        foreach ($resTrendGuru as $r) {
            $tg[$r->tanggal] = $r->hadir;
        }

        foreach ($tgl_arr as $t) {
            $stats['trend_siswa'][] = isset($ts[$t]) ? $ts[$t] : 0;
            $stats['trend_guru'][] = isset($tg[$t]) ? $tg[$t] : 0;
        }

        return $stats;
    }

    public function getGuruStats($guru_id, $tanggal) {
        $stats = [
            'total_hadir' => 0,
            'total_izin' => 0,
            'total_sakit' => 0,
            'total_alpa' => 0,
            'siswa_wali_stats' => null
        ];

        // Riwayat kehadiran guru (total)
        $this->db->query("SELECT LOWER(status) as st, COUNT(DISTINCT tanggal) as jml FROM absensi_guru WHERE guru_id = :id AND DAYOFWEEK(tanggal) NOT IN (1, 7) GROUP BY LOWER(status)");
        $this->db->bind(':id', $guru_id);
        $res = $this->db->resultSet();
        foreach ($res as $r) {
            $st = $r->st;
            if ($st == 'hadir') $stats['total_hadir'] += $r->jml;
            elseif ($st == 'izin') $stats['total_izin'] += $r->jml;
            elseif ($st == 'sakit') $stats['total_sakit'] += $r->jml;
            elseif ($st == 'alpa') $stats['total_alpa'] += $r->jml;
        }

        // Note: Guru is no longer Wali Kelas in this schema.
        // Siswa stats will be loaded in getWaliKelasStats if we create it.
        /*
        $this->db->query("
            SELECT k.id, k.nama_kelas 
            FROM kelas k 
            JOIN wali_kelas wk ON k.id = wk.kelas_id 
            WHERE wk.wali_kelas_data_id = :id 
            LIMIT 1
        ");
        $this->db->bind(':id', $guru_id);
        */
        $kelas = false;
        if ($kelas) {
            $stats['siswa_wali_stats'] = [
                'kelas_nama' => $kelas->nama_kelas,
                'total_siswa' => 0,
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alpa' => 0
            ];
            
            $this->db->query("SELECT COUNT(id) as count FROM siswa WHERE kelas_id = :kid");
            $this->db->bind(':kid', $kelas->id);
            $stats['siswa_wali_stats']['total_siswa'] = $this->db->single()->count ?? 0;

            $this->db->query("
                SELECT LOWER(a.status) as st, COUNT(DISTINCT a.siswa_id) as jml 
                FROM absensi_siswa a 
                JOIN siswa s ON a.siswa_id = s.id 
                WHERE s.kelas_id = :kid AND a.tanggal = :tgl 
                GROUP BY LOWER(a.status)
            ");
            $this->db->bind(':kid', $kelas->id);
            $this->db->bind(':tgl', $tanggal);
            $resWali = $this->db->resultSet();
            $totAbsen = 0;
            foreach ($resWali as $rw) {
                $st = $rw->st;
                if ($st == 'hadir') $stats['siswa_wali_stats']['hadir'] += $rw->jml;
                elseif ($st == 'izin') $stats['siswa_wali_stats']['izin'] += $rw->jml;
                elseif ($st == 'sakit') $stats['siswa_wali_stats']['sakit'] += $rw->jml;
                elseif ($st == 'alpa') $stats['siswa_wali_stats']['alpa'] += $rw->jml;
            }
        }

        return $stats;
    }

    public function getWaliKelasStats($wali_kelas_id, $tanggal) {
        $stats = [
            'total_siswa' => 0,
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'kelas_nama' => '-'
        ];

        $this->db->query("
            SELECT k.id, k.nama_kelas 
            FROM kelas k 
            JOIN wali_kelas wk ON k.id = wk.kelas_id 
            WHERE wk.wali_kelas_data_id = :id 
            LIMIT 1
        ");
        $this->db->bind(':id', $wali_kelas_id);
        $kelas = $this->db->single();
        if ($kelas) {
            $stats['kelas_nama'] = $kelas->nama_kelas;
            
            $this->db->query("SELECT COUNT(id) as count FROM siswa WHERE kelas_id = :kid");
            $this->db->bind(':kid', $kelas->id);
            $stats['total_siswa'] = $this->db->single()->count ?? 0;

            $this->db->query("
                SELECT LOWER(a.status) as st, COUNT(DISTINCT a.siswa_id) as jml 
                FROM absensi_siswa a 
                JOIN siswa s ON a.siswa_id = s.id 
                WHERE s.kelas_id = :kid AND a.tanggal = :tgl 
                GROUP BY LOWER(a.status)
            ");
            $this->db->bind(':kid', $kelas->id);
            $this->db->bind(':tgl', $tanggal);
            $res = $this->db->resultSet();
            
            foreach ($res as $r) {
                $st = $r->st;
                if ($st == 'hadir') $stats['hadir'] += $r->jml;
                elseif ($st == 'izin') $stats['izin'] += $r->jml;
                elseif ($st == 'sakit') $stats['sakit'] += $r->jml;
                elseif ($st == 'alpa') $stats['alpa'] += $r->jml;
            }

            // Detail lists per status
            $this->db->query("
                SELECT s.id, s.nama_lengkap, s.nis, s.nisn, a.jam_masuk, a.jam_pulang, a.status 
                FROM absensi_siswa a 
                JOIN siswa s ON a.siswa_id = s.id 
                WHERE s.kelas_id = :kid AND a.tanggal = :tgl AND LOWER(a.status) = 'hadir'
                ORDER BY s.nama_lengkap ASC
            ");
            $this->db->bind(':kid', $kelas->id);
            $this->db->bind(':tgl', $tanggal);
            $stats['list_hadir'] = $this->db->resultSet();

            $this->db->query("
                SELECT s.id, s.nama_lengkap, s.nis, s.nisn, a.status, COALESCE(p.alasan, a.keterangan, '-') as keterangan 
                FROM absensi_siswa a 
                JOIN siswa s ON a.siswa_id = s.id 
                LEFT JOIN pengajuan_izin p ON (p.siswa_id = s.id AND p.tanggal = a.tanggal AND p.status = 'Disetujui')
                WHERE s.kelas_id = :kid AND a.tanggal = :tgl AND LOWER(a.status) = 'izin'
                ORDER BY s.nama_lengkap ASC
            ");
            $this->db->bind(':kid', $kelas->id);
            $this->db->bind(':tgl', $tanggal);
            $stats['list_izin'] = $this->db->resultSet();

            $this->db->query("
                SELECT s.id, s.nama_lengkap, s.nis, s.nisn, a.status, COALESCE(p.alasan, a.keterangan, '-') as keterangan 
                FROM absensi_siswa a 
                JOIN siswa s ON a.siswa_id = s.id 
                LEFT JOIN pengajuan_izin p ON (p.siswa_id = s.id AND p.tanggal = a.tanggal AND p.status = 'Disetujui')
                WHERE s.kelas_id = :kid AND a.tanggal = :tgl AND LOWER(a.status) = 'sakit'
                ORDER BY s.nama_lengkap ASC
            ");
            $this->db->bind(':kid', $kelas->id);
            $this->db->bind(':tgl', $tanggal);
            $stats['list_sakit'] = $this->db->resultSet();

            $this->db->query("
                SELECT s.id, s.nama_lengkap, s.nis, s.nisn, a.status, COALESCE(a.keterangan, 'Tanpa Keterangan') as keterangan 
                FROM absensi_siswa a 
                JOIN siswa s ON a.siswa_id = s.id 
                WHERE s.kelas_id = :kid AND a.tanggal = :tgl AND (LOWER(a.status) = 'alpa' OR LOWER(a.status) = 'bolos')
                ORDER BY s.nama_lengkap ASC
            ");
            $this->db->bind(':kid', $kelas->id);
            $this->db->bind(':tgl', $tanggal);
            $stats['list_alpa'] = $this->db->resultSet();

            $this->db->query("
                SELECT s.id, s.nama_lengkap, s.nis, s.nisn, 
                       a.jam_masuk, a.jam_pulang, 
                       COALESCE(a.status, 'Belum Presensi') as status,
                       COALESCE(p.alasan, a.keterangan, '-') as keterangan
                FROM siswa s 
                LEFT JOIN absensi_siswa a ON (s.id = a.siswa_id AND a.tanggal = :tgl)
                LEFT JOIN pengajuan_izin p ON (p.siswa_id = s.id AND p.tanggal = :tgl2 AND p.status = 'Disetujui')
                WHERE s.kelas_id = :kid 
                ORDER BY s.nama_lengkap ASC
            ");
            $this->db->bind(':kid', $kelas->id);
            $this->db->bind(':tgl', $tanggal);
            $this->db->bind(':tgl2', $tanggal);
            $stats['list_semua'] = $this->db->resultSet();
        }
        return $stats;
    }

    public function getSiswaStats($siswa_id) {
        $stats = [
            'total_hadir' => 0,
            'total_izin' => 0,
            'total_sakit' => 0,
            'total_alpa' => 0
        ];

        // Riwayat kehadiran siswa (total)
        $this->db->query("SELECT LOWER(status) as st, COUNT(DISTINCT tanggal) as jml FROM absensi_siswa WHERE siswa_id = :id AND DAYOFWEEK(tanggal) NOT IN (1, 7) GROUP BY LOWER(status)");
        $this->db->bind(':id', $siswa_id);
        $res = $this->db->resultSet();
        foreach ($res as $r) {
            $st = $r->st;
            if ($st == 'hadir') $stats['total_hadir'] += $r->jml;
            elseif ($st == 'izin') $stats['total_izin'] += $r->jml;
            elseif ($st == 'sakit') $stats['total_sakit'] += $r->jml;
            elseif ($st == 'alpa') $stats['total_alpa'] += $r->jml;
        }

        return $stats;
    }

    /**
     * Statistik dan data pemantauan komprehensif untuk Kepala Sekolah
     */
    public function getKepalaSekolahStats($tanggal) {
        $stats = $this->getAdminStats($tanggal);
        $dow = (int)date('w', strtotime($tanggal));
        $isWeekend = ($dow === 0 || $dow === 6);

        if ($isWeekend) {
            $stats['rekap_kelas'] = [];
            $stats['monitoring_siswa'] = [];
            $stats['monitoring_guru'] = [];
        } else {
            // 1. Rekap Kehadiran per Kelas Siswa Hari Ini
            $this->db->query("SELECT k.id, k.nama_kelas, j.nama_jurusan,
                                     COUNT(s.id) as total_siswa,
                                     SUM(CASE WHEN LOWER(a.status) = 'hadir' THEN 1 ELSE 0 END) as hadir,
                                     SUM(CASE WHEN LOWER(a.status) = 'izin' THEN 1 ELSE 0 END) as izin,
                                     SUM(CASE WHEN LOWER(a.status) = 'sakit' THEN 1 ELSE 0 END) as sakit,
                                     SUM(CASE WHEN LOWER(a.status) IN ('alpa', 'bolos') THEN 1 ELSE 0 END) as alpa,
                                     SUM(CASE WHEN a.status IS NULL THEN 1 ELSE 0 END) as belum_absen
                              FROM kelas k
                              LEFT JOIN jurusan j ON k.jurusan_id = j.id
                              LEFT JOIN siswa s ON s.kelas_id = k.id
                              LEFT JOIN absensi_siswa a ON (a.siswa_id = s.id AND a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7))
                              GROUP BY k.id, k.nama_kelas, j.nama_jurusan
                              ORDER BY k.tingkat ASC, k.nama_kelas ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['rekap_kelas'] = $this->db->resultSet();

            // 2. Daftar Lengkap Seluruh Siswa dengan Status Kehadiran Hari Ini (Monitoring Real-time)
            $this->db->query("SELECT s.id, s.nis, s.nama_lengkap, k.nama_kelas, j.nama_jurusan,
                                     a.jam_masuk, a.jam_pulang, a.status, a.keterangan
                              FROM siswa s
                              LEFT JOIN kelas k ON s.kelas_id = k.id
                              LEFT JOIN jurusan j ON k.jurusan_id = j.id
                              LEFT JOIN absensi_siswa a ON (a.siswa_id = s.id AND a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7))
                              ORDER BY k.nama_kelas ASC, s.nama_lengkap ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['monitoring_siswa'] = $this->db->resultSet();

            // 3. Daftar Lengkap Seluruh Guru dengan Status Kehadiran Hari Ini (Monitoring Real-time)
            $this->db->query("SELECT g.id, g.nip, g.nama_lengkap,
                                     a.jam_masuk, a.jam_pulang, a.status, a.keterangan
                              FROM guru g
                              LEFT JOIN absensi_guru a ON (a.guru_id = g.id AND a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7))
                              WHERE g.status_aktif = 1
                              ORDER BY g.nama_lengkap ASC");
            $this->db->bind(':tanggal', $tanggal);
            $stats['monitoring_guru'] = $this->db->resultSet();
        }

        // 4. Catatan untuk Guru Terbaru dari Kepala Sekolah
        $this->db->query("SELECT cg.*, g.nama_lengkap as nama_guru, g.nip as nip_guru
                          FROM catatan_guru cg
                          JOIN guru g ON cg.guru_id = g.id
                          ORDER BY cg.created_at DESC LIMIT 5");
        $stats['catatan_guru_terbaru'] = $this->db->resultSet();

        // 5. Total Catatan Guru
        $this->db->query("SELECT COUNT(*) as total FROM catatan_guru");
        $resCat = $this->db->single();
        $stats['total_catatan_guru'] = $resCat ? (int)$resCat->total : 0;

        return $stats;
    }
}
