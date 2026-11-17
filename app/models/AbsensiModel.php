<?php
class AbsensiModel extends BaseModel {
    
    // ============================================
    // BARCODE / QR CODE LOOKUP (Mendukung Barcode, NIS/NIP, atau ID Pengguna)
    // ============================================
    public function getSiswaByBarcode($barcode) {
        $this->db->query("SELECT s.*, k.nama_kelas FROM siswa s 
                          LEFT JOIN kelas k ON s.kelas_id = k.id 
                          WHERE s.barcode = :b1 
                             OR s.nis = :b2 
                             OR s.nisn = :b3 
                             OR s.id = :b4 
                             OR s.user_id = :b5");
        $this->db->bind(':b1', $barcode);
        $this->db->bind(':b2', $barcode);
        $this->db->bind(':b3', $barcode);
        $this->db->bind(':b4', $barcode);
        $this->db->bind(':b5', $barcode);
        return $this->db->single();
    }

    public function getGuruByBarcode($barcode) {
        $this->db->query("SELECT * FROM guru 
                          WHERE barcode = :b1 
                             OR nip = :b2 
                             OR id = :b3 
                             OR user_id = :b4");
        $this->db->bind(':b1', $barcode);
        $this->db->bind(':b2', $barcode);
        $this->db->bind(':b3', $barcode);
        $this->db->bind(':b4', $barcode);
        return $this->db->single();
    }

    public function getWaliKelasByBarcode($barcode) {
        $this->db->query("SELECT wkd.*, k.nama_kelas 
                          FROM wali_kelas_data wkd 
                          LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id 
                          LEFT JOIN kelas k ON wk.kelas_id = k.id 
                          WHERE wkd.barcode = :b1 
                             OR wkd.nip = :b2 
                             OR wkd.id = :b3 
                             OR wkd.user_id = :b4");
        $this->db->bind(':b1', $barcode);
        $this->db->bind(':b2', $barcode);
        $this->db->bind(':b3', $barcode);
        $this->db->bind(':b4', $barcode);
        return $this->db->single();
    }

    // ============================================
    // CHECK EXISTING ABSENSI HARI INI
    // ============================================
    public function cekAbsensiSiswaHariIni($siswa_id, $tanggal) {
        $this->db->query("SELECT * FROM absensi_siswa WHERE siswa_id = :siswa_id AND tanggal = :tanggal");
        $this->db->bind(':siswa_id', $siswa_id);
        $this->db->bind(':tanggal', $tanggal);
        return $this->db->single();
    }

    public function cekAbsensiGuruHariIni($guru_id, $tanggal) {
        $this->db->query("SELECT * FROM absensi_guru WHERE guru_id = :guru_id AND tanggal = :tanggal");
        $this->db->bind(':guru_id', $guru_id);
        $this->db->bind(':tanggal', $tanggal);
        return $this->db->single();
    }

    public function cekAbsensiWaliKelasHariIni($wali_kelas_id, $tanggal) {
        $this->db->query("SELECT * FROM absensi_wali_kelas WHERE wali_kelas_id = :wali_kelas_id AND tanggal = :tanggal");
        $this->db->bind(':wali_kelas_id', $wali_kelas_id);
        $this->db->bind(':tanggal', $tanggal);
        return $this->db->single();
    }

    // ============================================
    // CATAT ABSENSI
    // ============================================
    public function catatMasukSiswa($siswa_id, $tanggal, $jam, $status = 'hadir', $keterangan = '') {
        $this->db->query("INSERT INTO absensi_siswa (siswa_id, tanggal, jam_masuk, status, keterangan) VALUES (:siswa_id, :tanggal, :jam_masuk, :status, :keterangan)");
        $this->db->bind(':siswa_id', $siswa_id);
        $this->db->bind(':tanggal', $tanggal);
        $this->db->bind(':jam_masuk', $jam);
        $this->db->bind(':status', $status);
        $this->db->bind(':keterangan', $keterangan);
        return $this->db->execute();
    }

    public function catatPulangSiswa($absensi_id, $jam) {
        $this->db->query("UPDATE absensi_siswa SET jam_pulang = :jam_pulang, 
                          status = 'hadir', 
                          keterangan = CASE WHEN keterangan LIKE '%Terlambat%' OR status = 'terlambat' THEN 'Terlambat hadir & pulang lengkap' ELSE 'Kehadiran lengkap' END 
                          WHERE id = :id");
        $this->db->bind(':jam_pulang', $jam);
        $this->db->bind(':id', $absensi_id);
        return $this->db->execute();
    }

    public function catatMasukGuru($guru_id, $tanggal, $jam, $status = 'hadir', $keterangan = '') {
        $this->db->query("INSERT INTO absensi_guru (guru_id, tanggal, jam_masuk, status, keterangan) VALUES (:guru_id, :tanggal, :jam_masuk, :status, :keterangan)");
        $this->db->bind(':guru_id', $guru_id);
        $this->db->bind(':tanggal', $tanggal);
        $this->db->bind(':jam_masuk', $jam);
        $this->db->bind(':status', $status);
        $this->db->bind(':keterangan', $keterangan);
        return $this->db->execute();
    }

    public function catatPulangGuru($absensi_id, $jam) {
        $this->db->query("UPDATE absensi_guru SET jam_pulang = :jam_pulang, 
                          status = 'hadir', 
                          keterangan = CASE WHEN keterangan LIKE '%Terlambat%' OR status = 'terlambat' THEN 'Terlambat hadir & pulang lengkap' ELSE 'Kehadiran lengkap' END 
                          WHERE id = :id");
        $this->db->bind(':jam_pulang', $jam);
        $this->db->bind(':id', $absensi_id);
        return $this->db->execute();
    }

    public function catatMasukWaliKelas($wali_kelas_id, $tanggal, $jam, $status = 'hadir', $keterangan = '') {
        $this->db->query("INSERT INTO absensi_wali_kelas (wali_kelas_id, tanggal, jam_masuk, status, keterangan) VALUES (:wali_kelas_id, :tanggal, :jam_masuk, :status, :keterangan)");
        $this->db->bind(':wali_kelas_id', $wali_kelas_id);
        $this->db->bind(':tanggal', $tanggal);
        $this->db->bind(':jam_masuk', $jam);
        $this->db->bind(':status', $status);
        $this->db->bind(':keterangan', $keterangan);
        return $this->db->execute();
    }

    public function catatPulangWaliKelas($absensi_id, $jam) {
        $this->db->query("UPDATE absensi_wali_kelas SET jam_pulang = :jam_pulang, 
                          status = 'hadir', 
                          keterangan = CASE WHEN keterangan LIKE '%Terlambat%' OR status = 'terlambat' THEN 'Terlambat hadir & pulang lengkap' ELSE 'Kehadiran lengkap' END 
                          WHERE id = :id");
        $this->db->bind(':jam_pulang', $jam);
        $this->db->bind(':id', $absensi_id);
        return $this->db->execute();
    }

    /**
     * Otomatis sinkronisasi status bolos/alpa jika tidak scan ulang barcode pulang
     */
    public function sinkronisasiStatusBolos() {
        $hari_map = [1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu', 7 => 'minggu'];
        $hari_ini = $hari_map[date('N')];
        $jamSekarang = date('H:i:s');
        $isSchoolDay = (date('N') <= 5); // Hanya Senin s/d Jumat (Sabtu & Minggu libur)

        // Pada hari libur (Sabtu & Minggu), tidak ada KBM dan tidak ada pencatatan alpa/bolos
        if (!$isSchoolDay) {
            return;
        }

        // 0. Sinkronisasi pengajuan izin/sakit hari ini ke tabel absensi masing-masing jika belum tercatat
        $this->db->query("SELECT * FROM pengajuan_izin WHERE tanggal = CURDATE()");
        $pengajuanHariIni = $this->db->resultSet();
        if ($pengajuanHariIni) {
            foreach ($pengajuanHariIni as $p) {
                $stLower = strtolower($p->jenis);
                $ketAlasan = $p->alasan;
                if ($p->tipe_pemohon === 'guru' || !empty($p->guru_id)) {
                    $this->db->query("SELECT id, status, jam_masuk, jam_pulang FROM absensi_guru WHERE guru_id = :gid AND tanggal = CURDATE()");
                    $this->db->bind(':gid', $p->guru_id);
                    $existG = $this->db->single();
                    if ($existG) {
                        if ($existG->status !== $stLower || $existG->jam_masuk !== null || $existG->jam_pulang !== null) {
                            $this->db->query("UPDATE absensi_guru SET status = :st, keterangan = :ket, jam_masuk = NULL, jam_pulang = NULL WHERE id = :id");
                            $this->db->bind(':st', $stLower);
                            $this->db->bind(':ket', $ketAlasan);
                            $this->db->bind(':id', $existG->id);
                            $this->db->execute();
                        }
                    } else {
                        $this->db->query("INSERT INTO absensi_guru (guru_id, tanggal, jam_masuk, jam_pulang, status, keterangan) VALUES (:gid, CURDATE(), NULL, NULL, :st, :ket)");
                        $this->db->bind(':gid', $p->guru_id);
                        $this->db->bind(':st', $stLower);
                        $this->db->bind(':ket', $ketAlasan);
                        $this->db->execute();
                    }
                } else {
                    $this->db->query("SELECT id, status, jam_masuk, jam_pulang FROM absensi_siswa WHERE siswa_id = :sid AND tanggal = CURDATE()");
                    $this->db->bind(':sid', $p->siswa_id);
                    $existS = $this->db->single();
                    if ($existS) {
                        if ($existS->status !== $stLower || $existS->jam_masuk !== null || $existS->jam_pulang !== null) {
                            $this->db->query("UPDATE absensi_siswa SET status = :st, keterangan = :ket, jam_masuk = NULL, jam_pulang = NULL WHERE id = :id");
                            $this->db->bind(':st', $stLower);
                            $this->db->bind(':ket', $ketAlasan);
                            $this->db->bind(':id', $existS->id);
                            $this->db->execute();
                        }
                    } else {
                        $this->db->query("INSERT INTO absensi_siswa (siswa_id, tanggal, jam_masuk, jam_pulang, status, keterangan) VALUES (:sid, CURDATE(), NULL, NULL, :st, :ket)");
                        $this->db->bind(':sid', $p->siswa_id);
                        $this->db->bind(':st', $stLower);
                        $this->db->bind(':ket', $ketAlasan);
                        $this->db->execute();
                    }
                }
            }
        }

        // 1. Hari-hari sebelumnya: jika hadir tapi jam_pulang kosong -> alpa/bolos
        $this->db->query("UPDATE absensi_siswa 
                          SET status = 'alpa', keterangan = 'tidak ada keterangan' 
                          WHERE tanggal < CURDATE() AND (jam_pulang IS NULL OR jam_pulang = '' OR jam_pulang = '00:00:00') AND status = 'hadir'");
        $this->db->execute();
        
        $this->db->query("UPDATE absensi_guru 
                          SET status = 'alpa', keterangan = 'tidak ada keterangan' 
                          WHERE tanggal < CURDATE() AND (jam_pulang IS NULL OR jam_pulang = '' OR jam_pulang = '00:00:00') AND status = 'hadir'");
        $this->db->execute();

        $this->db->query("UPDATE absensi_wali_kelas 
                          SET status = 'alpa', keterangan = 'tidak ada keterangan' 
                          WHERE tanggal < CURDATE() AND (jam_pulang IS NULL OR jam_pulang = '' OR jam_pulang = '00:00:00') AND status = 'hadir'");
        $this->db->execute();

        // 2. Hari ini: Jika sudah melewati jam operasional kepulangan (setelah pukul 18:00 WIB), dan belum scan pulang -> alpa (Bolos)
        $batasPulang = '18:00:00';
        if ($jamSekarang >= $batasPulang) {
            $this->db->query("UPDATE absensi_siswa 
                              SET status = 'alpa', keterangan = 'tidak ada keterangan' 
                              WHERE tanggal = CURDATE() AND (jam_pulang IS NULL OR jam_pulang = '' OR jam_pulang = '00:00:00') AND status = 'hadir'");
            $this->db->execute();
            
            $this->db->query("UPDATE absensi_guru 
                              SET status = 'alpa', keterangan = 'tidak ada keterangan' 
                              WHERE tanggal = CURDATE() AND (jam_pulang IS NULL OR jam_pulang = '' OR jam_pulang = '00:00:00') AND status = 'hadir'");
            $this->db->execute();

            $this->db->query("UPDATE absensi_wali_kelas 
                              SET status = 'alpa', keterangan = 'tidak ada keterangan' 
                              WHERE tanggal = CURDATE() AND (jam_pulang IS NULL OR jam_pulang = '' OR jam_pulang = '00:00:00') AND status = 'hadir'");
            $this->db->execute();
        }

        // 3. Hari ini jika sudah lewat batas waktu kepulangan ($batasPulang) pada hari sekolah:
        // Siapa saja siswa, guru & wali kelas yang sama sekali belum absen scan masuk dan tidak ada izin/sakit -> dicatat Alpa
        if ($isSchoolDay && $jamSekarang >= $batasPulang) {
            $this->db->query("INSERT INTO absensi_siswa (siswa_id, tanggal, jam_masuk, jam_pulang, status, keterangan)
                              SELECT s.id, CURDATE(), NULL, NULL, 'alpa', 'tidak ada keterangan'
                              FROM siswa s
                              WHERE s.id NOT IN (SELECT siswa_id FROM absensi_siswa WHERE tanggal = CURDATE())
                                AND s.id NOT IN (SELECT siswa_id FROM pengajuan_izin WHERE tanggal = CURDATE() AND tipe_pemohon = 'siswa')");
            $this->db->execute();

            $this->db->query("INSERT INTO absensi_guru (guru_id, tanggal, jam_masuk, jam_pulang, status, keterangan)
                              SELECT g.id, CURDATE(), NULL, NULL, 'alpa', 'tidak ada keterangan'
                              FROM guru g
                              WHERE g.id NOT IN (SELECT guru_id FROM absensi_guru WHERE tanggal = CURDATE())
                                AND g.id NOT IN (SELECT guru_id FROM pengajuan_izin WHERE tanggal = CURDATE() AND tipe_pemohon = 'guru')");
            $this->db->execute();

            $this->db->query("INSERT INTO absensi_wali_kelas (wali_kelas_id, tanggal, jam_masuk, jam_pulang, status, keterangan)
                              SELECT wkd.id, CURDATE(), NULL, NULL, 'alpa', 'tidak ada keterangan'
                              FROM wali_kelas_data wkd
                              WHERE wkd.id NOT IN (SELECT wali_kelas_id FROM absensi_wali_kelas WHERE tanggal = CURDATE())");
            $this->db->execute();
        }
    }

    // ============================================
    // RIWAYAT UNTUK DASHBOARD INDIVIDU
    // ============================================
    public function getRiwayatSiswa($siswa_id) {
        $this->db->query("SELECT * FROM absensi_siswa WHERE siswa_id = :siswa_id AND DAYOFWEEK(tanggal) NOT IN (1, 7) ORDER BY tanggal DESC");
        $this->db->bind(':siswa_id', $siswa_id);
        return $this->db->resultSet();
    }

    public function getRiwayatGuru($guru_id) {
        $this->db->query("SELECT * FROM absensi_guru WHERE guru_id = :guru_id AND DAYOFWEEK(tanggal) NOT IN (1, 7) ORDER BY tanggal DESC");
        $this->db->bind(':guru_id', $guru_id);
        return $this->db->resultSet();
    }

    // ============================================
    // WALI KELAS - ABSENSI KELAS
    // ============================================
    public function getAbsensiSiswaByKelasTanggal($kelas_id, $tanggal) {
        $dow = (int)date('w', strtotime($tanggal));
        if ($dow === 0 || $dow === 6) {
            return [];
        }
        $this->db->query("SELECT s.nis, s.nama_lengkap, a.jam_masuk, a.jam_pulang, a.status, a.keterangan 
                          FROM siswa s 
                          LEFT JOIN absensi_siswa a ON s.id = a.siswa_id AND a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7)
                          WHERE s.kelas_id = :kelas_id
                          ORDER BY s.nama_lengkap ASC");
        $this->db->bind(':kelas_id', $kelas_id);
        $this->db->bind(':tanggal', $tanggal);
        return $this->db->resultSet();
    }

    // ============================================
    // LAPORAN - KEPALA SEKOLAH / ADMIN
    // ============================================
    public function getAllAbsensiSiswaTanggal($tanggal) {
        $dow = (int)date('w', strtotime($tanggal));
        if ($dow === 0 || $dow === 6) {
            return [];
        }
        $this->db->query("SELECT s.nis, s.nama_lengkap, k.nama_kelas, a.jam_masuk, a.jam_pulang, a.status, a.keterangan 
                          FROM absensi_siswa a 
                          JOIN siswa s ON a.siswa_id = s.id 
                          LEFT JOIN kelas k ON s.kelas_id = k.id
                          WHERE a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7)
                          ORDER BY a.jam_masuk DESC");
        $this->db->bind(':tanggal', $tanggal);
        return $this->db->resultSet();
    }

    public function getAllAbsensiGuruTanggal($tanggal) {
        $dow = (int)date('w', strtotime($tanggal));
        if ($dow === 0 || $dow === 6) {
            return [];
        }
        $this->db->query("SELECT g.nip, g.nama_lengkap, a.jam_masuk, a.jam_pulang, a.status, a.keterangan 
                          FROM absensi_guru a 
                          JOIN guru g ON a.guru_id = g.id 
                          WHERE a.tanggal = :tanggal AND DAYOFWEEK(a.tanggal) NOT IN (1, 7)
                          ORDER BY a.jam_masuk DESC");
        $this->db->bind(':tanggal', $tanggal);
        return $this->db->resultSet();
    }
    // ============================================
    // REKAP & FILTER ABSENSI (ADMIN)
    // ============================================
    public function getRekapAdmin($filters = [], $limit = 10, $offset = 0, $count = false) {
        // Hanya merekap hari aktif sekolah (Sabtu dan Minggu tidak direkap)
        $whereSiswa = ["DAYOFWEEK(a.tanggal) NOT IN (1, 7)"];
        $whereGuru = ["DAYOFWEEK(a.tanggal) NOT IN (1, 7)"];
        $whereWali = ["DAYOFWEEK(a.tanggal) NOT IN (1, 7)"];
        $paramsSiswa = [];
        $paramsGuru = [];
        $paramsWali = [];
        
        $startDate = !empty($filters['start_date']) ? $filters['start_date'] : '2026-10-01';
        $whereSiswa[] = "a.tanggal >= :start_date_s";
        $paramsSiswa[':start_date_s'] = $startDate;
        $whereGuru[] = "a.tanggal >= :start_date_g";
        $paramsGuru[':start_date_g'] = $startDate;
        $whereWali[] = "a.tanggal >= :start_date_w";
        $paramsWali[':start_date_w'] = $startDate;
        if (!empty($filters['end_date'])) {
            $whereSiswa[] = "a.tanggal <= :end_date_s";
            $paramsSiswa[':end_date_s'] = $filters['end_date'];
            $whereGuru[] = "a.tanggal <= :end_date_g";
            $paramsGuru[':end_date_g'] = $filters['end_date'];
            $whereWali[] = "a.tanggal <= :end_date_w";
            $paramsWali[':end_date_w'] = $filters['end_date'];
        }
        if (!empty($filters['status'])) {
            $whereSiswa[] = "a.status = :status_s";
            $paramsSiswa[':status_s'] = $filters['status'];
            $whereGuru[] = "a.status = :status_g";
            $paramsGuru[':status_g'] = $filters['status'];
            $whereWali[] = "a.status = :status_w";
            $paramsWali[':status_w'] = $filters['status'];
        }
        if (!empty($filters['nama'])) {
            $whereSiswa[] = "(s.nama_lengkap LIKE :nama_s OR s.nis LIKE :nama_s2)";
            $paramsSiswa[':nama_s'] = "%" . $filters['nama'] . "%";
            $paramsSiswa[':nama_s2'] = "%" . $filters['nama'] . "%";
            
            $whereGuru[] = "(g.nama_lengkap LIKE :nama_g OR g.nip LIKE :nama_g2)";
            $paramsGuru[':nama_g'] = "%" . $filters['nama'] . "%";
            $paramsGuru[':nama_g2'] = "%" . $filters['nama'] . "%";

            $whereWali[] = "(wkd.nama_lengkap LIKE :nama_w OR wkd.nip LIKE :nama_w2)";
            $paramsWali[':nama_w'] = "%" . $filters['nama'] . "%";
            $paramsWali[':nama_w2'] = "%" . $filters['nama'] . "%";
        }
        if (!empty($filters['kelas_id'])) {
            $whereSiswa[] = "s.kelas_id = :kelas_id_s";
            $paramsSiswa[':kelas_id_s'] = $filters['kelas_id'];
            $whereGuru[] = "wk.kelas_id = :kelas_id_g";
            $paramsGuru[':kelas_id_g'] = $filters['kelas_id'];
            $whereWali[] = "wk.kelas_id = :kelas_id_w";
            $paramsWali[':kelas_id_w'] = $filters['kelas_id'];
        }
        
        $sqlSiswa = "SELECT 'Siswa' as role, s.nama_lengkap as nama, s.nis as id_nomor, s.nisn, '-' as mapel, k.nama_kelas as kelas, j.nama_jurusan as jurusan, NULL as wali_kelas_nama, a.tanggal, a.jam_masuk, a.jam_pulang, a.status, a.keterangan, a.id as absensi_id FROM absensi_siswa a JOIN siswa s ON a.siswa_id = s.id LEFT JOIN kelas k ON s.kelas_id = k.id LEFT JOIN jurusan j ON k.jurusan_id = j.id";
        if (count($whereSiswa) > 0) {
            $sqlSiswa .= " WHERE " . implode(" AND ", $whereSiswa);
        }
        
        $sqlGuru = "SELECT 'Guru' as role, g.nama_lengkap as nama, g.nip as id_nomor, '-' as nisn, COALESCE(GROUP_CONCAT(DISTINCT mp.nama_mapel SEPARATOR ', '), 'Tenaga Pendidik') as mapel, COALESCE(MAX(kwk.nama_kelas), '-') as kelas, '-' as jurusan, MAX(kwk.nama_kelas) as wali_kelas_nama, a.tanggal, a.jam_masuk, a.jam_pulang, a.status, a.keterangan, a.id as absensi_id 
                    FROM absensi_guru a 
                    JOIN guru g ON a.guru_id = g.id 
                    LEFT JOIN mata_pelajaran mp ON g.id = mp.guru_id 
                    LEFT JOIN wali_kelas_data wkd ON (g.nip = wkd.nip OR (g.user_id IS NOT NULL AND g.user_id = wkd.user_id)) 
                    LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id 
                    LEFT JOIN kelas kwk ON wk.kelas_id = kwk.id";
        if (count($whereGuru) > 0) {
            $sqlGuru .= " WHERE " . implode(" AND ", $whereGuru);
        }
        $sqlGuru .= " GROUP BY a.id, g.id";

        // sqlWali hanya mengambil data wali kelas jika belum ada rekaman absensi guru pada tanggal tersebut (mencegah duplikasi jika guru merangkap wali kelas)
        $whereWaliBase = ["ag.id IS NULL"];
        $allWhereWali = array_merge($whereWaliBase, $whereWali);
        $sqlWali = "SELECT 'Wali Kelas' as role, wkd.nama_lengkap as nama, wkd.nip as id_nomor, '-' as nisn, 'Wali Kelas' as mapel, COALESCE(k.nama_kelas, '-') as kelas, '-' as jurusan, k.nama_kelas as wali_kelas_nama, a.tanggal, a.jam_masuk, a.jam_pulang, a.status, a.keterangan, a.id as absensi_id 
                    FROM absensi_wali_kelas a 
                    JOIN wali_kelas_data wkd ON a.wali_kelas_id = wkd.id 
                    LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id 
                    LEFT JOIN kelas k ON wk.kelas_id = k.id 
                    LEFT JOIN guru g ON (wkd.nip = g.nip OR (wkd.user_id IS NOT NULL AND wkd.user_id = g.user_id)) 
                    LEFT JOIN absensi_guru ag ON (g.id = ag.guru_id AND a.tanggal = ag.tanggal)";
        if (count($allWhereWali) > 0) {
            $sqlWali .= " WHERE " . implode(" AND ", $allWhereWali);
        }
        
        $roleFilter = $filters['role'] ?? '';
        $sql = "";
        $params = [];
        
        if ($roleFilter === 'Siswa') {
            $sql = $sqlSiswa;
            $params = $paramsSiswa;
        } elseif ($roleFilter === 'Guru') {
            $sql = $sqlGuru;
            $params = $paramsGuru;
        } elseif ($roleFilter === 'Wali Kelas' || $roleFilter === 'wali_kelas') {
            $whereGuruWali = array_merge($whereGuru, ["wk.id IS NOT NULL"]);
            $sqlGuruWali = "SELECT 'Guru' as role, g.nama_lengkap as nama, g.nip as id_nomor, '-' as nisn, COALESCE(GROUP_CONCAT(DISTINCT mp.nama_mapel SEPARATOR ', '), 'Tenaga Pendidik') as mapel, COALESCE(MAX(kwk.nama_kelas), '-') as kelas, '-' as jurusan, MAX(kwk.nama_kelas) as wali_kelas_nama, a.tanggal, a.jam_masuk, a.jam_pulang, a.status, a.keterangan, a.id as absensi_id 
                            FROM absensi_guru a 
                            JOIN guru g ON a.guru_id = g.id 
                            LEFT JOIN mata_pelajaran mp ON g.id = mp.guru_id 
                            LEFT JOIN wali_kelas_data wkd ON (g.nip = wkd.nip OR (g.user_id IS NOT NULL AND g.user_id = wkd.user_id)) 
                            LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id 
                            LEFT JOIN kelas kwk ON wk.kelas_id = kwk.id 
                            WHERE " . implode(" AND ", $whereGuruWali) . " 
                            GROUP BY a.id, g.id";
            $sql = "($sqlGuruWali) UNION ALL ($sqlWali)";
            $params = array_merge($paramsGuru, $paramsWali);
        } else {
            $sql = "($sqlSiswa) UNION ALL ($sqlGuru) UNION ALL ($sqlWali)";
            $params = array_merge($paramsSiswa, $paramsGuru, $paramsWali);
        }
        
        if ($count) {
            $this->db->query("SELECT COUNT(*) as total FROM ($sql) as combined_table");
            foreach ($params as $k => $v) {
                $this->db->bind($k, $v);
            }
            $res = $this->db->single();
            return $res ? $res->total : 0;
        }
        
        $sql .= " ORDER BY tanggal DESC, jam_masuk DESC";
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $this->db->query($sql);
        foreach ($params as $k => $v) {
            $this->db->bind($k, $v);
        }
        if ($limit > 0) {
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        }
        
        return $this->db->resultSet();
    }

    public function getStatsAdmin($filters = []) {
        $data = $this->getRekapAdmin($filters, 0, 0, false);
        $stats = ['total' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'hadir' => 0];
        foreach ($data as $d) {
            $stats['total']++;
            $st = strtolower($d->status);
            if ($st == 'terlambat') $st = 'hadir';
            if (isset($stats[$st])) $stats[$st]++;
        }
        return $stats;
    }

    // ============================================
    // FILTER ABSENSI (SISWA)
    // ============================================
    public function getRiwayatSiswaFiltered($siswa_id, $filters = [], $limit = 10, $offset = 0, $count = false) {
        $where = ["a.siswa_id = :siswa_id", "DAYOFWEEK(a.tanggal) NOT IN (1, 7)"];
        $params = [':siswa_id' => $siswa_id];
        
        $startDate = !empty($filters['start_date']) ? $filters['start_date'] : '2026-10-01';
        $where[] = "a.tanggal >= :start_date";
        $params[':start_date'] = $startDate;
        
        if (!empty($filters['end_date'])) {
            $where[] = "a.tanggal <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        if (!empty($filters['status'])) {
            $where[] = "a.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        $sql = "SELECT a.* FROM absensi_siswa a";
        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($count) {
            $this->db->query("SELECT COUNT(*) as total FROM ($sql) as t");
            foreach ($params as $k => $v) {
                $this->db->bind($k, $v);
            }
            $res = $this->db->single();
            return $res ? $res->total : 0;
        }
        
        $sql .= " ORDER BY a.tanggal DESC, a.jam_masuk DESC";
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $this->db->query($sql);
        foreach ($params as $k => $v) {
            $this->db->bind($k, $v);
        }
        if ($limit > 0) {
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        }
        return $this->db->resultSet();
    }

    public function getStatsSiswa($siswa_id, $filters = []) {
        $data = $this->getRiwayatSiswaFiltered($siswa_id, $filters, 0, 0, false);
        $stats = ['total' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'hadir' => 0];
        foreach ($data as $d) {
            $stats['total']++;
            $st = strtolower($d->status);
            if ($st == 'terlambat') $st = 'hadir';
            if (isset($stats[$st])) $stats[$st]++;
        }
        return $stats;
    }

    // ============================================
    // FILTER ABSENSI (GURU)
    // ============================================
    public function getRiwayatGuruFiltered($guru_id, $filters = [], $limit = 10, $offset = 0, $count = false) {
        $where = ["a.guru_id = :guru_id", "DAYOFWEEK(a.tanggal) NOT IN (1, 7)"];
        $params = [':guru_id' => $guru_id];
        
        $startDate = !empty($filters['start_date']) ? $filters['start_date'] : '2026-10-01';
        $where[] = "a.tanggal >= :start_date";
        $params[':start_date'] = $startDate;
        
        if (!empty($filters['end_date'])) {
            $where[] = "a.tanggal <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        if (!empty($filters['status'])) {
            $where[] = "a.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        $sql = "SELECT a.* FROM absensi_guru a";
        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($count) {
            $this->db->query("SELECT COUNT(*) as total FROM ($sql) as t");
            foreach ($params as $k => $v) {
                $this->db->bind($k, $v);
            }
            $res = $this->db->single();
            return $res ? $res->total : 0;
        }
        
        $sql .= " ORDER BY a.tanggal DESC, a.jam_masuk DESC";
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $this->db->query($sql);
        foreach ($params as $k => $v) {
            $this->db->bind($k, $v);
        }
        if ($limit > 0) {
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        }
        return $this->db->resultSet();
    }

    public function getStatsGuru($guru_id, $filters = []) {
        $data = $this->getRiwayatGuruFiltered($guru_id, $filters, 0, 0, false);
        $stats = ['total' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'hadir' => 0];
        foreach ($data as $d) {
            $stats['total']++;
            $st = strtolower($d->status);
            if ($st == 'terlambat') $st = 'hadir';
            if (isset($stats[$st])) $stats[$st]++;
        }
        return $stats;
    }

    // ============================================
    // FILTER ABSENSI (WALI KELAS)
    // ============================================
    public function getRiwayatWaliKelasFiltered($wali_kelas_id, $filters = [], $limit = 10, $offset = 0, $count = false) {
        $where = ["a.wali_kelas_id = :wali_kelas_id", "DAYOFWEEK(a.tanggal) NOT IN (1, 7)"];
        $params = [':wali_kelas_id' => $wali_kelas_id];
        
        $startDate = !empty($filters['start_date']) ? $filters['start_date'] : '2026-10-01';
        $where[] = "a.tanggal >= :start_date";
        $params[':start_date'] = $startDate;
        if (!empty($filters['end_date'])) {
            $where[] = "a.tanggal <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        if (!empty($filters['status'])) {
            $where[] = "a.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        $sql = "SELECT a.* FROM absensi_wali_kelas a";
        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($count) {
            $this->db->query("SELECT COUNT(*) as total FROM ($sql) as t");
            foreach ($params as $k => $v) {
                $this->db->bind($k, $v);
            }
            $res = $this->db->single();
            return $res ? $res->total : 0;
        }
        
        $sql .= " ORDER BY a.tanggal DESC, a.jam_masuk DESC";
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $this->db->query($sql);
        foreach ($params as $k => $v) {
            $this->db->bind($k, $v);
        }
        if ($limit > 0) {
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        }
        return $this->db->resultSet();
    }

    public function getStatsWaliKelas($wali_kelas_id, $filters = []) {
        $data = $this->getRiwayatWaliKelasFiltered($wali_kelas_id, $filters, 0, 0, false);
        $stats = ['total' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'hadir' => 0];
        foreach ($data as $d) {
            $stats['total']++;
            $st = strtolower($d->status);
            if ($st == 'terlambat') $st = 'hadir';
            if (isset($stats[$st])) $stats[$st]++;
        }
        return $stats;
    }
}
