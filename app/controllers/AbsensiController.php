<?php
class AbsensiController extends Controller {

    public function index() {
        $this->riwayat();
    }

    public function riwayat() {
        require_login();
        $role = $_SESSION['user_role'] ?? '';
        if ($role === 'siswa') {
            redirect('siswa/riwayat');
        } else if ($role === 'guru') {
            redirect('guru/riwayat');
        } else if ($role === 'wali_kelas') {
            redirect('wali-kelas/riwayat');
        } else {
            redirect('admin/rekap');
        }
    }

    public function scan() {
        if (!isset($_SESSION['user_role'])) {
            redirect('auth/login');
        }

        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'] ?? null;
        $tanggal = date('Y-m-d');
        $hari_map = [1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu', 7 => 'minggu'];
        $hari_ini = $hari_map[date('N')];
        $namaHari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'][date('N')];

        $model = $this->model('AbsensiModel');
        $jadwalModel = $this->model('JadwalModel');

        $currentUserData = null;
        $todayAbsensi = null;
        $todayJadwal = [];

        $db = new Database();

        $riwayatTerakhir = [];

        if ($userRole === 'siswa' && $userId) {
            $db->query("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.user_id = :uid");
            $db->bind(':uid', $userId);
            $siswa = $db->single();
            if ($siswa) {
                $todayAbsensi = $model->cekAbsensiSiswaHariIni($siswa->id, $tanggal);
                $todayJadwal = $jadwalModel->getJadwalSiswa($siswa->kelas_id, $hari_ini);
                $allRiwayat = $model->getRiwayatSiswa($siswa->id);
                $riwayatTerakhir = is_array($allRiwayat) ? array_slice($allRiwayat, 0, 5) : [];
                $currentUserData = [
                    'id' => $siswa->id,
                    'nama' => $siswa->nama_lengkap,
                    'role' => 'Siswa',
                    'nis' => $siswa->nis . ($siswa->nisn ? ' / ' . $siswa->nisn : ''),
                    'kelas' => $siswa->nama_kelas ?? '-'
                ];
            }
        } elseif (($userRole === 'guru' || $userRole === 'wali_kelas') && $userId) {
            $db->query("SELECT g.*, wkd.id as wali_id, k.nama_kelas as kelas_binaan 
                        FROM guru g 
                        LEFT JOIN wali_kelas_data wkd ON g.user_id = wkd.user_id 
                        LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id 
                        LEFT JOIN kelas k ON wk.kelas_id = k.id 
                        WHERE g.user_id = :uid");
            $db->bind(':uid', $userId);
            $guru = $db->single();
            if ($guru) {
                $todayAbsensi = $model->cekAbsensiGuruHariIni($guru->id, $tanggal);
                $todayJadwal = $jadwalModel->getJadwalGuru($guru->id, $hari_ini);
                $allRiwayat = $model->getRiwayatGuru($guru->id);
                $riwayatTerakhir = is_array($allRiwayat) ? array_slice($allRiwayat, 0, 5) : [];
                $currentUserData = [
                    'id' => $guru->id,
                    'nama' => $guru->nama_lengkap,
                    'role' => (!empty($guru->kelas_binaan) ? 'Wali Kelas ' . $guru->kelas_binaan : 'Guru'),
                    'nip' => $guru->nip ?? '-',
                    'kelas_binaan' => $guru->kelas_binaan ?? null
                ];
            }
        } else {
            // Role admin / kepala sekolah
            $allToday = $model->getAllAbsensiSiswaTanggal($tanggal);
            $riwayatTerakhir = is_array($allToday) ? array_slice($allToday, 0, 5) : [];
        }

        $data = [
            'title' => 'Scan Absensi',
            'user_role' => $userRole,
            'currentUserData' => $currentUserData,
            'todayAbsensi' => $todayAbsensi,
            'todayJadwal' => $todayJadwal,
            'riwayatTerakhir' => $riwayatTerakhir,
            'namaHari' => $namaHari
        ];
        
        $this->view('absensi/scan', $data);
    }

    public function proses_scan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST['ajax']);

            $respond = function($status, $message, $scanResult = null, $telegramPesan = null) use ($isAjax) {
                // Kirim notifikasi Telegram sebelum mengembalikan respon
                if (!empty($telegramPesan)) {
                    $this->kirimNotifikasiTelegram($telegramPesan);
                }

                if ($isAjax) {
                    $payload = json_encode([
                        'status' => $status,
                        'message' => $message,
                        'scan_result' => $scanResult
                    ]);

                    header('Content-Type: application/json; charset=utf-8');
                    echo $payload;
                    exit;
                } else {
                    set_flash($status === 'error' ? 'error' : ($status === 'info' ? 'info' : 'success'), $message);
                    if ($scanResult) {
                        $_SESSION['scan_result'] = $scanResult;
                    }
                    redirect('absensi/scan');
                    exit;
                }
            };

            // =========================================================================
            // 1. VALIDASI HARI KERJA (MODE DEMO: DIBEBASKAN UNTUK UJI COBA VIDEO)
            // =========================================================================
            /*
            if (date('N') == 7) {
                $respond('error', "Absensi gagal! Hari Minggu kegiatan belajar mengajar (KBM) libur, absensi tidak dapat dilakukan.");
                return;
            }
            */

            // =========================================================================
            // 2. PARSING REQUEST DATA (Mendukung Form POST & JSON Payload dari Fetch/cURL)
            // =========================================================================
            $rawInput = file_get_contents('php://input');
            if (!empty($rawInput)) {
                $jsonInput = json_decode($rawInput, true);
                if (is_array($jsonInput)) {
                    $_POST = array_merge($_POST, $jsonInput);
                }
            }

            // Menerima input barcode / id_pengguna / nis / nip
            $barcode = trim($_POST['barcode'] ?? $_POST['id_pengguna'] ?? $_POST['nis'] ?? $_POST['nip'] ?? '');
            $jenis = $_POST['jenis'] ?? 'siswa';
            $tanggal = date('Y-m-d');
            $jam = date('H:i:s');
            
            if (empty($barcode)) {
                $respond('error', 'Data presensi tidak valid! Parameter barcode, id_pengguna, nis, atau nip wajib diisi.');
                return;
            }

            $tahunAjaranModel = $this->model('TahunAjaranModel');
            $tahunAktif = $tahunAjaranModel->getActiveTahunAjaran();
            
            $model = $this->model('AbsensiModel');
            // Catatan: sinkronisasiStatusBolos() dijalankan di halaman dashboard/rekap, dinonaktifkan di AJAX scan agar proses instan tanpa lag
            $jadwalModel = $this->model('JadwalModel');
            
            $hari_map = [1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu', 7 => 'minggu'];
            $hari_ini = $hari_map[date('N')];

            // =========================================================================
            // 3. VALIDASI IDENTITAS PENGGUNA KE DATABASE (Siswa, Guru, atau Wali Kelas)
            // =========================================================================
            $siswa = $model->getSiswaByBarcode($barcode);
            if ($siswa) {
                $jenis = 'siswa';
            } else {
                $guru = $model->getGuruByBarcode($barcode);
                if ($guru) {
                    $jenis = 'guru';
                } else {
                    $wali = $model->getWaliKelasByBarcode($barcode);
                    if ($wali) {
                        $jenis = 'wali_kelas';
                    } else {
                        $respond('error', 'QR Code / Identitas tidak valid! Data pengguna tidak terdaftar di database.');
                        return;
                    }
                }
            }

            // === 1. SISWA ===
            if ($jenis === 'siswa') {
                $siswa = $model->getSiswaByBarcode($barcode);
                if (!$siswa) {
                    $respond('error', 'QR Code tidak valid atau data siswa tidak ditemukan.');
                    return;
                }

                // SECURITY: Prevent Siswa from scanning other people's QR code
                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'siswa') {
                    if ($siswa->user_id != $_SESSION['user_id']) {
                        $respond('error', 'Anda tidak diizinkan melakukan absensi untuk pengguna lain.');
                        return;
                    }
                }

                $cek = $model->cekAbsensiSiswaHariIni($siswa->id, $tanggal);
                $jadwal = $jadwalModel->getJadwalSiswa($siswa->kelas_id, $hari_ini);

                // Helper to get formatted recent history for JSON response
                $getRecentRiwayat = function() use ($model, $siswa) {
                    $raw = $model->getRiwayatSiswa($siswa->id);
                    $list = [];
                    if (is_array($raw)) {
                        foreach (array_slice($raw, 0, 7) as $r) {
                            $isLateItem = (stripos($r->keterangan ?? '', 'terlambat') !== false || strtolower($r->status ?? '') === 'terlambat');
                            $statusDisplay = $isLateItem ? 'Terlambat' : ucfirst($r->status);
                            $list[] = [
                                'tanggal' => date('d-m-Y', strtotime($r->tanggal)),
                                'jam_masuk' => !empty($r->jam_masuk) ? substr($r->jam_masuk, 0, 5) . ' WIB' : '-',
                                'jam_pulang' => !empty($r->jam_pulang) ? substr($r->jam_pulang, 0, 5) . ' WIB' : '-',
                                'status' => $statusDisplay,
                                'keterangan' => !empty($r->keterangan) ? $r->keterangan : '-'
                            ];
                        }
                    }
                    return $list;
                };

                // Cek pengajuan izin/sakit
                $dbPengajuan = new Database();
                $dbPengajuan->query("SELECT * FROM pengajuan_izin WHERE siswa_id = :sid AND tipe_pemohon = 'siswa' AND (tanggal = :tgl OR DATE(created_at) = :tgl2) ORDER BY id DESC LIMIT 1");
                $dbPengajuan->bind(':sid', $siswa->id);
                $dbPengajuan->bind(':tgl', $tanggal);
                $dbPengajuan->bind(':tgl2', $tanggal);
                $pIzin = $dbPengajuan->single();

                $isExcused = ($pIzin || ($cek && in_array(strtolower($cek->status), ['izin', 'sakit'])) || ($cek && strtolower($cek->status) === 'bolos'));
                if ($isExcused) {
                    $statusVal = $pIzin ? strtolower($pIzin->jenis) : strtolower($cek->status);
                    $ketVal = ($cek && !empty($cek->keterangan)) ? $cek->keterangan : ($pIzin ? $pIzin->alasan : '-');

                    if (!$cek) {
                        $model->catatMasukSiswa($siswa->id, $tanggal, NULL, $statusVal, $ketVal);
                    } elseif ($cek->jam_masuk !== null || $cek->jam_pulang !== null) {
                        $dbClean = new Database();
                        $dbClean->query("UPDATE absensi_siswa SET jam_masuk = NULL, jam_pulang = NULL WHERE id = :id");
                        $dbClean->bind(':id', $cek->id);
                        $dbClean->execute();
                    }

                    $scanResult = [
                        'nama' => $siswa->nama_lengkap,
                        'role' => 'Siswa',
                        'nis' => $siswa->nis . ($siswa->nisn ? ' / ' . $siswa->nisn : ''),
                        'kelas' => $siswa->nama_kelas ?? '-',
                        'tanggal' => date('d-m-Y'),
                        'jam_masuk' => '-',
                        'jam_pulang' => '-',
                        'status' => ucfirst($statusVal),
                        'keterangan' => $ketVal,
                        'jadwal' => $jadwal,
                        'riwayat' => $getRecentRiwayat(),
                        'is_sudah_absen' => true
                    ];
                    $respond('info', 'Kehadiran Anda hari ini telah tercatat sebagai ' . ucfirst($statusVal) . '.', $scanResult);
                    return;
                }

                // =========================================================================
                                // =========================================================================
                // LOGIKA ATURAN WAKTU PRESENSI SISWA (MODE BEBAS / DEMO VIDEO 24 JAM):
                // Bebas scan jam masuk & pulang kapan saja untuk kelancaran video demo.
                // =========================================================================
                $hasJamMasuk = ($cek && !empty($cek->jam_masuk) && $cek->jam_masuk !== '00:00:00');
                $hasJamPulang = ($cek && !empty($cek->jam_pulang) && $cek->jam_pulang !== '00:00:00');

                // KASUS 1: BELUM MEMILIKI JAM MASUK HARI INI -> CATAT MASUK BEBAS
                if (!$hasJamMasuk) {
                    $statusDb = 'hadir';
                    $statusNotif = 'hadir';
                    $ketVal = 'Hadir tepat waktu';
                    $statusLabel = 'Hadir';
                    $msgSukses = 'Presensi masuk berhasil dicatat! Pukul ' . substr($jam, 0, 5) . ' WIB.';

                    if ($cek) {
                        $dbUpdate = new Database();
                        $dbUpdate->query("UPDATE absensi_siswa SET jam_masuk = :jam, status = :st, keterangan = :ket WHERE id = :id");
                        $dbUpdate->bind(':jam', $jam);
                        $dbUpdate->bind(':st', $statusDb);
                        $dbUpdate->bind(':ket', $ketVal);
                        $dbUpdate->bind(':id', $cek->id);
                        $dbUpdate->execute();
                    } else {
                        $model->catatMasukSiswa($siswa->id, $tanggal, $jam, $statusDb, $ketVal);
                    }

                    // Kirim notifikasi Telegram Siswa Masuk (background)
                    $pesanTelegram = $this->formatNotifSiswaMasuk($siswa, $jam, $tanggal, $statusNotif, $ketVal);

                    $scanResult = [
                        'nama' => $siswa->nama_lengkap,
                        'role' => 'Siswa',
                        'nis' => $siswa->nis . ($siswa->nisn ? ' / ' . $siswa->nisn : ''),
                        'kelas' => $siswa->nama_kelas ?? '-',
                        'tanggal' => date('d-m-Y'),
                        'jam_masuk' => substr($jam, 0, 5) . ' WIB',
                        'jam_pulang' => '-',
                        'status' => $statusLabel,
                        'keterangan' => $ketVal,
                        'jadwal' => $jadwal,
                        'riwayat' => $getRecentRiwayat(),
                        'is_sudah_absen' => false
                    ];
                    $respond('success', $msgSukses, $scanResult, $pesanTelegram);
                    return;
                }

                // KASUS 2: SUDAH MEMILIKI JAM MASUK, CATAT PULANG BEBAS (Kapan Saja)
                if (!$hasJamPulang) {
                    $model->catatPulangSiswa($cek->id, $jam);

                    $isLateEntry = (stripos($cek->keterangan ?? '', 'terlambat') !== false || $cek->status === 'terlambat');
                    $statusNotifPulang = $isLateEntry ? 'Terlambat (Pulang)' : 'Hadir (Pulang)';
                    $ketPulang = $isLateEntry ? 'Terlambat hadir & pulang lengkap' : 'Kehadiran lengkap';
                    $statusResult = $isLateEntry ? 'Terlambat' : 'Hadir';

                    // Kirim notifikasi Telegram Siswa Pulang (background)
                    $pesanTelegram = $this->formatNotifSiswaPulang($siswa, $jam, $cek->jam_masuk, $tanggal, $statusNotifPulang, $ketPulang);

                    $scanResult = [
                        'nama' => $siswa->nama_lengkap,
                        'role' => 'Siswa',
                        'nis' => $siswa->nis . ($siswa->nisn ? ' / ' . $siswa->nisn : ''),
                        'kelas' => $siswa->nama_kelas ?? '-',
                        'tanggal' => date('d-m-Y'),
                        'jam_masuk' => substr($cek->jam_masuk, 0, 5) . ' WIB',
                        'jam_pulang' => substr($jam, 0, 5) . ' WIB',
                        'status' => $statusResult,
                        'keterangan' => $ketPulang,
                        'jadwal' => $jadwal,
                        'riwayat' => $getRecentRiwayat(),
                        'is_sudah_absen' => false
                    ];
                    $respond('success', 'Presensi pulang berhasil dicatat! Pukul ' . substr($jam, 0, 5) . ' WIB. Kehadiran Anda hari ini lengkap.', $scanResult, $pesanTelegram);
                    return;
                }

                // KASUS 3: SUDAH SCAN MASUK DAN PULANG (MODE DEMO: PERBARUI JAM PULANG & LAPOR SUKSES)
                $model->catatPulangSiswa($cek->id, $jam);
                $isLateEntry = (stripos($cek->keterangan ?? '', 'terlambat') !== false || $cek->status === 'terlambat');
                $ketPulang = 'Kehadiran lengkap';
                $statusResult = $isLateEntry ? 'Terlambat' : 'Hadir';

                $scanResult = [
                    'nama' => $siswa->nama_lengkap,
                    'role' => 'Siswa',
                    'nis' => $siswa->nis . ($siswa->nisn ? ' / ' . $siswa->nisn : ''),
                    'kelas' => $siswa->nama_kelas ?? '-',
                    'tanggal' => date('d-m-Y'),
                    'jam_masuk' => substr($cek->jam_masuk, 0, 5) . ' WIB',
                    'jam_pulang' => substr($jam, 0, 5) . ' WIB',
                    'status' => $statusResult,
                    'keterangan' => $ketPulang,
                    'jadwal' => $jadwal,
                    'riwayat' => $getRecentRiwayat(),
                    'is_sudah_absen' => true
                ];
                $respond('success', 'Presensi pulang berhasil diperbarui! Pukul ' . substr($jam, 0, 5) . ' WIB. Kehadiran hari ini lengkap.', $scanResult);
                return;
            }

            // === 2. GURU ===
            else if ($jenis === 'guru') {
                $guru = $model->getGuruByBarcode($barcode);
                if (!$guru) {
                    $respond('error', 'QR Code tidak valid atau data guru tidak ditemukan.');
                    return;
                }

                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'guru') {
                    if ($guru->user_id != $_SESSION['user_id']) {
                        $respond('error', 'Anda tidak diizinkan melakukan absensi untuk pengguna lain.');
                        return;
                    }
                }

                $jadwal = $jadwalModel->getJadwalGuru($guru->id, $hari_ini);
                $cek = $model->cekAbsensiGuruHariIni($guru->id, $tanggal);

                $getRecentRiwayat = function() use ($model, $guru) {
                    $raw = $model->getRiwayatGuru($guru->id);
                    $list = [];
                    if (is_array($raw)) {
                        foreach (array_slice($raw, 0, 7) as $r) {
                            $isLateItem = (stripos($r->keterangan ?? '', 'terlambat') !== false || strtolower($r->status ?? '') === 'terlambat');
                            $statusDisplay = $isLateItem ? 'Terlambat' : ucfirst($r->status);
                            $list[] = [
                                'tanggal' => date('d-m-Y', strtotime($r->tanggal)),
                                'jam_masuk' => !empty($r->jam_masuk) ? substr($r->jam_masuk, 0, 5) . ' WIB' : '-',
                                'jam_pulang' => !empty($r->jam_pulang) ? substr($r->jam_pulang, 0, 5) . ' WIB' : '-',
                                'status' => $statusDisplay,
                                'keterangan' => !empty($r->keterangan) ? $r->keterangan : '-'
                            ];
                        }
                    }
                    return $list;
                };

                // Cek pengajuan izin/sakit
                $dbPengajuan = new Database();
                $dbPengajuan->query("SELECT * FROM pengajuan_izin WHERE guru_id = :gid AND tipe_pemohon = 'guru' AND (tanggal = :tgl OR DATE(created_at) = :tgl2) ORDER BY id DESC LIMIT 1");
                $dbPengajuan->bind(':gid', $guru->id);
                $dbPengajuan->bind(':tgl', $tanggal);
                $dbPengajuan->bind(':tgl2', $tanggal);
                $pIzin = $dbPengajuan->single();

                $isExcused = ($pIzin || ($cek && in_array(strtolower($cek->status), ['izin', 'sakit'])) || ($cek && strtolower($cek->status) === 'bolos'));
                if ($isExcused) {
                    $statusVal = $pIzin ? strtolower($pIzin->jenis) : strtolower($cek->status);
                    $ketVal = ($cek && !empty($cek->keterangan)) ? $cek->keterangan : ($pIzin ? $pIzin->alasan : '-');

                    if (!$cek) {
                        $model->catatMasukGuru($guru->id, $tanggal, NULL, $statusVal, $ketVal);
                    } elseif ($cek->jam_masuk !== null || $cek->jam_pulang !== null) {
                        $dbClean = new Database();
                        $dbClean->query("UPDATE absensi_guru SET jam_masuk = NULL, jam_pulang = NULL WHERE id = :id");
                        $dbClean->bind(':id', $cek->id);
                        $dbClean->execute();
                    }

                    $scanResult = [
                        'nama' => $guru->nama_lengkap,
                        'role' => 'Guru',
                        'nip' => $guru->nip ?? '-',
                        'tanggal' => date('d-m-Y'),
                        'jam_masuk' => '-',
                        'jam_pulang' => '-',
                        'status' => ucfirst($statusVal),
                        'keterangan' => $ketVal,
                        'jadwal' => $jadwal,
                        'riwayat' => $getRecentRiwayat(),
                        'is_sudah_absen' => true
                    ];
                    $respond('info', 'Kehadiran Anda hari ini telah tercatat sebagai ' . ucfirst($statusVal) . '.', $scanResult);
                    return;
                }

                // =========================================================================
                                // =========================================================================
                // LOGIKA ATURAN WAKTU PRESENSI GURU (MODE BEBAS / DEMO VIDEO 24 JAM):
                // Bebas scan jam masuk & pulang kapan saja untuk kelancaran video demo.
                // =========================================================================
                $hasJamMasuk = ($cek && !empty($cek->jam_masuk) && $cek->jam_masuk !== '00:00:00');
                $hasJamPulang = ($cek && !empty($cek->jam_pulang) && $cek->jam_pulang !== '00:00:00');

                // KASUS 1: BELUM MEMILIKI JAM MASUK HARI INI -> CATAT MASUK BEBAS
                if (!$hasJamMasuk) {
                    $statusDb = 'hadir';
                    $statusNotif = 'hadir';
                    $ketVal = 'Hadir tepat waktu';
                    $statusLabel = 'Hadir';
                    $msgSukses = 'Presensi masuk guru berhasil dicatat! Pukul ' . substr($jam, 0, 5) . ' WIB.';

                    if ($cek) {
                        $dbUpdate = new Database();
                        $dbUpdate->query("UPDATE absensi_guru SET jam_masuk = :jam, status = :st, keterangan = :ket WHERE id = :id");
                        $dbUpdate->bind(':jam', $jam);
                        $dbUpdate->bind(':st', $statusDb);
                        $dbUpdate->bind(':ket', $ketVal);
                        $dbUpdate->bind(':id', $cek->id);
                        $dbUpdate->execute();
                    } else {
                        $model->catatMasukGuru($guru->id, $tanggal, $jam, $statusDb, $ketVal);
                    }

                    // SINKRONISASI: Jika guru ini juga terdaftar sebagai wali kelas, sinkronkan ke absensi_wali_kelas
                    $dbCheckWk = new Database();
                    $dbCheckWk->query("SELECT id FROM wali_kelas_data WHERE user_id = :uid OR nip = :nip");
                    $dbCheckWk->bind(':uid', $guru->user_id);
                    $dbCheckWk->bind(':nip', $guru->nip);
                    $wkdRow = $dbCheckWk->single();
                    if ($wkdRow) {
                        $dbSyncWk = new Database();
                        $dbSyncWk->query("INSERT INTO absensi_wali_kelas (wali_kelas_id, tanggal, jam_masuk, status, keterangan) 
                                         VALUES (:wkid, :tgl, :jam, :st, :ket)
                                         ON DUPLICATE KEY UPDATE jam_masuk = VALUES(jam_masuk), status = VALUES(status), keterangan = VALUES(keterangan)");
                        $dbSyncWk->bind(':wkid', $wkdRow->id);
                        $dbSyncWk->bind(':tgl', $tanggal);
                        $dbSyncWk->bind(':jam', $jam);
                        $dbSyncWk->bind(':st', $statusDb);
                        $dbSyncWk->bind(':ket', $ketVal);
                        $dbSyncWk->execute();
                    }

                    // Kirim notifikasi Telegram Guru Masuk (background)
                    $pesanTelegram = $this->formatNotifGuruMasuk($guru, $jam, $tanggal, $statusNotif, $ketVal);

                    $scanResult = [
                        'nama' => $guru->nama_lengkap,
                        'role' => 'Guru',
                        'nip' => $guru->nip ?? '-',
                        'tanggal' => date('d-m-Y'),
                        'jam_masuk' => substr($jam, 0, 5) . ' WIB',
                        'jam_pulang' => '-',
                        'status' => $statusLabel,
                        'keterangan' => $ketVal,
                        'jadwal' => $jadwal,
                        'riwayat' => $getRecentRiwayat(),
                        'is_sudah_absen' => false
                    ];
                    $respond('success', $msgSukses, $scanResult, $pesanTelegram);
                    return;
                }

                // KASUS 2: SUDAH MEMILIKI JAM MASUK, CATAT PULANG BEBAS (Kapan Saja)
                if (!$hasJamPulang) {
                    $model->catatPulangGuru($cek->id, $jam);

                    $isLateEntry = (stripos($cek->keterangan ?? '', 'terlambat') !== false || $cek->status === 'terlambat');
                    $statusNotifPulang = $isLateEntry ? 'Terlambat (Pulang)' : 'Hadir (Pulang)';
                    $ketPulang = $isLateEntry ? 'Terlambat hadir & pulang lengkap' : 'Kehadiran lengkap';
                    $statusResult = $isLateEntry ? 'Terlambat' : 'Hadir';

                    // SINKRONISASI: Jika guru ini juga wali kelas, sinkronkan kepulangan ke absensi_wali_kelas
                    $dbCheckWk = new Database();
                    $dbCheckWk->query("SELECT id FROM wali_kelas_data WHERE user_id = :uid OR nip = :nip");
                    $dbCheckWk->bind(':uid', $guru->user_id);
                    $dbCheckWk->bind(':nip', $guru->nip);
                    $wkdRow = $dbCheckWk->single();
                    if ($wkdRow) {
                        $dbSyncWk = new Database();
                        $dbSyncWk->query("UPDATE absensi_wali_kelas SET jam_pulang = :jp, 
                                          status = 'hadir', 
                                          keterangan = CASE WHEN keterangan LIKE '%Terlambat%' OR status = 'terlambat' THEN 'Terlambat hadir & pulang lengkap' ELSE 'Kehadiran lengkap' END 
                                          WHERE wali_kelas_id = :wkid AND tanggal = :tgl");
                        $dbSyncWk->bind(':jp', $jam);
                        $dbSyncWk->bind(':wkid', $wkdRow->id);
                        $dbSyncWk->bind(':tgl', $tanggal);
                        $dbSyncWk->execute();
                    }

                    // Kirim notifikasi Telegram Guru Pulang (background)
                    $pesanTelegram = $this->formatNotifGuruPulang($guru, $jam, $cek->jam_masuk, $tanggal, $statusNotifPulang, $ketPulang);

                    $scanResult = [
                        'nama' => $guru->nama_lengkap,
                        'role' => 'Guru',
                        'nip' => $guru->nip ?? '-',
                        'tanggal' => date('d-m-Y'),
                        'jam_masuk' => substr($cek->jam_masuk, 0, 5) . ' WIB',
                        'jam_pulang' => substr($jam, 0, 5) . ' WIB',
                        'status' => $statusResult,
                        'keterangan' => $ketPulang,
                        'jadwal' => $jadwal,
                        'riwayat' => $getRecentRiwayat(),
                        'is_sudah_absen' => false
                    ];
                    $respond('success', 'Presensi pulang guru berhasil dicatat! Pukul ' . substr($jam, 0, 5) . ' WIB. Kehadiran hari ini lengkap.', $scanResult, $pesanTelegram);
                    return;
                }

                // KASUS 3: SUDAH SCAN MASUK DAN PULANG (MODE DEMO: PERBARUI PULANG)
                $model->catatPulangGuru($cek->id, $jam);
                $isLateEntry = (stripos($cek->keterangan ?? '', 'terlambat') !== false || $cek->status === 'terlambat');
                $ketPulang = 'Kehadiran lengkap';
                $statusResult = $isLateEntry ? 'Terlambat' : 'Hadir';

                $scanResult = [
                    'nama' => $guru->nama_lengkap,
                    'role' => 'Guru',
                    'nip' => $guru->nip ?? '-',
                    'tanggal' => date('d-m-Y'),
                    'jam_masuk' => substr($cek->jam_masuk, 0, 5) . ' WIB',
                    'jam_pulang' => substr($jam, 0, 5) . ' WIB',
                    'status' => $statusResult,
                    'keterangan' => $ketPulang,
                    'jadwal' => $jadwal,
                    'riwayat' => $getRecentRiwayat(),
                    'is_sudah_absen' => true
                ];
                $respond('success', 'Presensi pulang guru berhasil dicatat! Pukul ' . substr($jam, 0, 5) . ' WIB. Kehadiran hari ini lengkap.', $scanResult);
                return;
            }

            // === 3. WALI KELAS ===
            else if ($jenis === 'wali_kelas') {
                $wali = $model->getWaliKelasByBarcode($barcode);
                if (!$wali) {
                    $respond('error', 'QR Code tidak valid atau data wali kelas tidak ditemukan.');
                    return;
                }

                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'wali_kelas') {
                    if ($wali->user_id != $_SESSION['user_id']) {
                        $respond('error', 'Anda tidak diizinkan melakukan absensi untuk pengguna lain.');
                        return;
                    }
                }

                $jadwal = $jadwalModel->getJadwalWaliKelas($wali->id, $hari_ini);
                $cek = $model->cekAbsensiWaliKelasHariIni($wali->id, $tanggal);

                $getRecentRiwayat = function() use ($model, $wali) {
                    $raw = $model->getRiwayatGuru($wali->id);
                    $list = [];
                    if (is_array($raw)) {
                        foreach (array_slice($raw, 0, 7) as $r) {
                            $isLateItem = (stripos($r->keterangan ?? '', 'terlambat') !== false || strtolower($r->status ?? '') === 'terlambat');
                            $statusDisplay = $isLateItem ? 'Terlambat' : ucfirst($r->status);
                            $list[] = [
                                'tanggal' => date('d-m-Y', strtotime($r->tanggal)),
                                'jam_masuk' => !empty($r->jam_masuk) ? substr($r->jam_masuk, 0, 5) . ' WIB' : '-',
                                'jam_pulang' => !empty($r->jam_pulang) ? substr($r->jam_pulang, 0, 5) . ' WIB' : '-',
                                'status' => $statusDisplay,
                                'keterangan' => !empty($r->keterangan) ? $r->keterangan : '-'
                            ];
                        }
                    }
                    return $list;
                };

                // Cek status izin / sakit / bolos
                $isExcused = ($cek && (in_array(strtolower($cek->status), ['izin', 'sakit']) || strtolower($cek->status) === 'bolos'));
                if ($isExcused) {
                    $statusVal = strtolower($cek->status);
                    $ketVal = !empty($cek->keterangan) ? $cek->keterangan : '-';

                    if ($cek->jam_masuk !== null || $cek->jam_pulang !== null) {
                        $dbClean = new Database();
                        $dbClean->query("UPDATE absensi_wali_kelas SET jam_masuk = NULL, jam_pulang = NULL WHERE id = :id");
                        $dbClean->bind(':id', $cek->id);
                        $dbClean->execute();
                    }

                    $scanResult = [
                        'nama' => $wali->nama_lengkap,
                        'role' => 'Wali Kelas',
                        'nip' => $wali->nip ?? '-',
                        'kelas_binaan' => $wali->nama_kelas ?? '-',
                        'tanggal' => date('d-m-Y'),
                        'jam_masuk' => '-',
                        'jam_pulang' => '-',
                        'status' => ucfirst($statusVal),
                        'keterangan' => $ketVal,
                        'jadwal' => $jadwal,
                        'riwayat' => $getRecentRiwayat(),
                        'is_sudah_absen' => true
                    ];
                    $respond('info', 'Kehadiran Anda hari ini telah tercatat sebagai ' . ucfirst($statusVal) . '.', $scanResult);
                    return;
                }

                // =========================================================================
                                // =========================================================================
                // LOGIKA ATURAN WAKTU PRESENSI WALI KELAS (MODE BEBAS / DEMO VIDEO 24 JAM):
                // Bebas scan jam masuk & pulang kapan saja untuk kelancaran video demo.
                // =========================================================================
                $hasJamMasuk = ($cek && !empty($cek->jam_masuk) && $cek->jam_masuk !== '00:00:00');
                $hasJamPulang = ($cek && !empty($cek->jam_pulang) && $cek->jam_pulang !== '00:00:00');

                // KASUS 1: BELUM MEMILIKI JAM MASUK HARI INI -> CATAT MASUK BEBAS
                if (!$hasJamMasuk) {
                    $statusDb = 'hadir';
                    $statusNotif = 'hadir';
                    $ketVal = 'Hadir tepat waktu';
                    $statusLabel = 'Hadir';
                    $msgSukses = 'Presensi masuk wali kelas berhasil dicatat! Pukul ' . substr($jam, 0, 5) . ' WIB.';

                    if ($cek) {
                        $dbUpdate = new Database();
                        $dbUpdate->query("UPDATE absensi_wali_kelas SET jam_masuk = :jam, status = :st, keterangan = :ket WHERE id = :id");
                        $dbUpdate->bind(':jam', $jam);
                        $dbUpdate->bind(':st', $statusDb);
                        $dbUpdate->bind(':ket', $ketVal);
                        $dbUpdate->bind(':id', $cek->id);
                        $dbUpdate->execute();
                    } else {
                        $model->catatMasukWaliKelas($wali->id, $tanggal, $jam, $statusDb, $ketVal);
                    }

                    // Kirim notifikasi Telegram Wali Kelas Masuk (background)
                    $pesanTelegram = $this->formatNotifWaliKelasMasuk($wali, $jam, $tanggal, $statusNotif, $ketVal);

                    $scanResult = [
                        'nama' => $wali->nama_lengkap,
                        'role' => 'Wali Kelas',
                        'nip' => $wali->nip ?? '-',
                        'kelas_binaan' => $wali->nama_kelas ?? '-',
                        'tanggal' => date('d-m-Y'),
                        'jam_masuk' => substr($jam, 0, 5) . ' WIB',
                        'jam_pulang' => '-',
                        'status' => $statusLabel,
                        'keterangan' => $ketVal,
                        'jadwal' => $jadwal,
                        'riwayat' => $getRecentRiwayat(),
                        'is_sudah_absen' => false
                    ];
                    $respond('success', $msgSukses, $scanResult, $pesanTelegram);
                    return;
                }

                // KASUS 2: SUDAH MEMILIKI JAM MASUK, CATAT PULANG BEBAS (Kapan Saja)
                if (!$hasJamPulang) {
                    $model->catatPulangWaliKelas($cek->id, $jam);

                    $isLateEntry = (stripos($cek->keterangan ?? '', 'terlambat') !== false || $cek->status === 'terlambat');
                    $statusNotifPulang = $isLateEntry ? 'Terlambat (Pulang)' : 'Hadir (Pulang)';
                    $ketPulang = $isLateEntry ? 'Terlambat hadir & pulang lengkap' : 'Kehadiran lengkap';
                    $statusResult = $isLateEntry ? 'Terlambat' : 'Hadir';

                    // Kirim notifikasi Telegram Wali Kelas Pulang (background)
                    $pesanTelegram = $this->formatNotifWaliKelasPulang($wali, $jam, $cek->jam_masuk, $tanggal, $statusNotifPulang, $ketPulang);

                    $scanResult = [
                        'nama' => $wali->nama_lengkap,
                        'role' => 'Wali Kelas',
                        'nip' => $wali->nip ?? '-',
                        'kelas_binaan' => $wali->nama_kelas ?? '-',
                        'tanggal' => date('d-m-Y'),
                        'jam_masuk' => substr($cek->jam_masuk, 0, 5) . ' WIB',
                        'jam_pulang' => substr($jam, 0, 5) . ' WIB',
                        'status' => $statusResult,
                        'keterangan' => $ketPulang,
                        'jadwal' => $jadwal,
                        'riwayat' => $getRecentRiwayat(),
                        'is_sudah_absen' => false
                    ];
                    $respond('success', 'Presensi pulang wali kelas berhasil dicatat! Pukul ' . substr($jam, 0, 5) . ' WIB. Kehadiran hari ini lengkap.', $scanResult, $pesanTelegram);
                    return;
                }

                // KASUS 3: SUDAH SCAN MASUK DAN PULANG (MODE DEMO: PERBARUI PULANG)
                $model->catatPulangWaliKelas($cek->id, $jam);
                $isLateEntry = (stripos($cek->keterangan ?? '', 'terlambat') !== false || $cek->status === 'terlambat');
                $ketPulang = 'Kehadiran lengkap';
                $statusResult = $isLateEntry ? 'Terlambat' : 'Hadir';

                $scanResult = [
                    'nama' => $wali->nama_lengkap,
                    'role' => 'Wali Kelas',
                    'nip' => $wali->nip ?? '-',
                    'kelas_binaan' => $wali->nama_kelas ?? '-',
                    'tanggal' => date('d-m-Y'),
                    'jam_masuk' => substr($cek->jam_masuk, 0, 5) . ' WIB',
                    'jam_pulang' => substr($jam, 0, 5) . ' WIB',
                    'status' => $statusResult,
                    'keterangan' => $ketPulang,
                    'jadwal' => $jadwal,
                    'riwayat' => $getRecentRiwayat(),
                    'is_sudah_absen' => true
                ];
                $respond('success', 'Presensi pulang wali kelas berhasil diperbarui! Pukul ' . substr($jam, 0, 5) . ' WIB. Kehadiran hari ini lengkap.', $scanResult);
                return;
            } else {
                $respond('error', 'Format identitas barcode / QR Code tidak dikenali.');
                return;
            }
        } else {
            redirect('absensi/scan');
        }
    }

    /**
     * Format tanggal Y-m-d ke format Bahasa Indonesia (contoh: Rabu, 21 Oktober 2026)
     */
    private function formatTanggalIndonesia($tanggal) {
        $hariArr = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $bulanArr = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $time = strtotime($tanggal);
        if (!$time) {
            return $tanggal;
        }

        $namaHari = $hariArr[date('l', $time)] ?? date('l', $time);
        $tgl = date('j', $time);
        $namaBulan = $bulanArr[(int)date('n', $time)] ?? date('F', $time);
        $tahun = date('Y', $time);

        return "{$namaHari}, {$tgl} {$namaBulan} {$tahun}";
    }

    /**
     * Format Notifikasi Telegram untuk Presensi Masuk Siswa
     */
    private function formatNotifSiswaMasuk($siswa, $jamMasuk, $tanggal, $status = 'Hadir', $keterangan = 'Hadir tepat waktu') {
        $tglIndo = $this->formatTanggalIndonesia($tanggal);
        $nama = htmlspecialchars($siswa->nama_lengkap ?? '-');
        $kelas = htmlspecialchars($siswa->nama_kelas ?? '-');
        $waktuMasuk = substr($jamMasuk, 0, 5) . ' WIB';
        $isLate = (strtolower($status) === 'terlambat');
        $statusStr = $isLate ? 'Terlambat (Masuk)' : 'Hadir (Masuk)';
        $statusIcon = $isLate ? '⚠️' : '✅';
        $ketStr = htmlspecialchars($keterangan ?: ($isLate ? 'Terlambat hadir' : 'Hadir tepat waktu'));
        $isSabtu = (date('N', strtotime($tanggal)) == 6);

        $msg = $isSabtu ? "📢 <b>PRESENSI MASUK SISWA (UJI COBA SABTU)</b>\n" : "📢 <b>PRESENSI MASUK SISWA</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "📅 <b>Hari, Tanggal:</b> {$tglIndo}\n";
        $msg .= "👤 <b>Nama:</b> {$nama}\n";
        $msg .= "🏫 <b>Kelas:</b> {$kelas}\n";
        $msg .= "⏰ <b>Jam Masuk:</b> {$waktuMasuk}\n";
        $msg .= "{$statusIcon} <b>Status:</b> {$statusStr}\n";
        $msg .= "📝 <b>Keterangan:</b> {$ketStr}\n";
        if ($isSabtu) {
            $msg .= "ℹ️ <b>Info:</b> Uji coba presensi hari Sabtu (tidak masuk rekap absensi)\n";
        }
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "<i>Sistem Presensi Digital SMK NU WARGABINANGUN</i>";

        return $msg;
    }

    /**
     * Format Notifikasi Telegram untuk Presensi Pulang Siswa
     */
    private function formatNotifSiswaPulang($siswa, $jamPulang, $jamMasuk, $tanggal, $status = 'Hadir (Pulang)', $keterangan = 'Kehadiran lengkap') {
        $tglIndo = $this->formatTanggalIndonesia($tanggal);
        $nama = htmlspecialchars($siswa->nama_lengkap ?? '-');
        $kelas = htmlspecialchars($siswa->nama_kelas ?? '-');
        $waktuMasuk = !empty($jamMasuk) ? substr($jamMasuk, 0, 5) . ' WIB' : '-';
        $waktuPulang = substr($jamPulang, 0, 5) . ' WIB';
        $isLate = (stripos($status, 'terlambat') !== false);
        $statusStr = $isLate ? 'Terlambat (Pulang)' : 'Hadir (Pulang)';
        $statusIcon = $isLate ? '⚠️' : '✅';
        $ketStr = htmlspecialchars($keterangan ?: ($isLate ? 'Terlambat hadir & pulang lengkap' : 'Kehadiran lengkap'));
        $isSabtu = (date('N', strtotime($tanggal)) == 6);

        $msg = $isSabtu ? "🏠 <b>PRESENSI PULANG SISWA (UJI COBA SABTU)</b>\n" : "🏠 <b>PRESENSI PULANG SISWA</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "📅 <b>Hari, Tanggal:</b> {$tglIndo}\n";
        $msg .= "👤 <b>Nama:</b> {$nama}\n";
        $msg .= "🏫 <b>Kelas:</b> {$kelas}\n";
        $msg .= "⏰ <b>Jam Masuk:</b> {$waktuMasuk}\n";
        $msg .= "🚪 <b>Jam Pulang:</b> {$waktuPulang}\n";
        $msg .= "{$statusIcon} <b>Status:</b> {$statusStr}\n";
        $msg .= "📝 <b>Keterangan:</b> {$ketStr}\n";
        if ($isSabtu) {
            $msg .= "ℹ️ <b>Info:</b> Uji coba presensi hari Sabtu (tidak masuk rekap absensi)\n";
        }
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "<i>Sistem Presensi Digital SMK NU WARGABINANGUN</i>";

        return $msg;
    }

    /**
     * Format Notifikasi Telegram untuk Presensi Masuk Guru
     */
    private function formatNotifGuruMasuk($guru, $jamMasuk, $tanggal, $status = 'Hadir', $keterangan = 'Hadir tepat waktu') {
        $tglIndo = $this->formatTanggalIndonesia($tanggal);
        $nama = htmlspecialchars($guru->nama_lengkap ?? '-');
        $nip = htmlspecialchars(!empty($guru->nip) ? $guru->nip : '-');
        $waktuMasuk = substr($jamMasuk, 0, 5) . ' WIB';
        $isLate = (strtolower($status) === 'terlambat');
        $statusStr = $isLate ? 'Terlambat (Masuk)' : 'Hadir (Masuk)';
        $statusIcon = $isLate ? '⚠️' : '✅';
        $ketStr = htmlspecialchars($keterangan ?: ($isLate ? 'Terlambat hadir' : 'Hadir tepat waktu'));
        $isSabtu = (date('N', strtotime($tanggal)) == 6);

        $msg = $isSabtu ? "📢 <b>PRESENSI MASUK GURU (UJI COBA SABTU)</b>\n" : "📢 <b>PRESENSI MASUK GURU</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "📅 <b>Hari, Tanggal:</b> {$tglIndo}\n";
        $msg .= "👤 <b>Nama:</b> {$nama}\n";
        $msg .= "👔 <b>Peran:</b> Guru Pengajar\n";
        $msg .= "🆔 <b>NIP:</b> {$nip}\n";
        $msg .= "⏰ <b>Jam Masuk:</b> {$waktuMasuk}\n";
        $msg .= "{$statusIcon} <b>Status:</b> {$statusStr}\n";
        $msg .= "📝 <b>Keterangan:</b> {$ketStr}\n";
        if ($isSabtu) {
            $msg .= "ℹ️ <b>Info:</b> Uji coba presensi hari Sabtu (tidak masuk rekap absensi)\n";
        }
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "<i>Sistem Presensi Digital SMK NU WARGABINANGUN</i>";

        return $msg;
    }

    /**
     * Format Notifikasi Telegram untuk Presensi Pulang Guru
     */
    private function formatNotifGuruPulang($guru, $jamPulang, $jamMasuk, $tanggal, $status = 'Hadir (Pulang)', $keterangan = 'Kehadiran lengkap') {
        $tglIndo = $this->formatTanggalIndonesia($tanggal);
        $nama = htmlspecialchars($guru->nama_lengkap ?? '-');
        $nip = htmlspecialchars(!empty($guru->nip) ? $guru->nip : '-');
        $waktuMasuk = !empty($jamMasuk) ? substr($jamMasuk, 0, 5) . ' WIB' : '-';
        $waktuPulang = substr($jamPulang, 0, 5) . ' WIB';
        $isLate = (stripos($status, 'terlambat') !== false);
        $statusStr = $isLate ? 'Terlambat (Pulang)' : 'Hadir (Pulang)';
        $statusIcon = $isLate ? '⚠️' : '✅';
        $ketStr = htmlspecialchars($keterangan ?: ($isLate ? 'Terlambat hadir & pulang lengkap' : 'Kehadiran lengkap'));
        $isSabtu = (date('N', strtotime($tanggal)) == 6);

        $msg = $isSabtu ? "🏠 <b>PRESENSI PULANG GURU (UJI COBA SABTU)</b>\n" : "🏠 <b>PRESENSI PULANG GURU</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "📅 <b>Hari, Tanggal:</b> {$tglIndo}\n";
        $msg .= "👤 <b>Nama:</b> {$nama}\n";
        $msg .= "👔 <b>Peran:</b> Guru Pengajar\n";
        $msg .= "🆔 <b>NIP:</b> {$nip}\n";
        $msg .= "⏰ <b>Jam Masuk:</b> {$waktuMasuk}\n";
        $msg .= "🚪 <b>Jam Pulang:</b> {$waktuPulang}\n";
        $msg .= "{$statusIcon} <b>Status:</b> {$statusStr}\n";
        $msg .= "📝 <b>Keterangan:</b> {$ketStr}\n";
        if ($isSabtu) {
            $msg .= "ℹ️ <b>Info:</b> Uji coba presensi hari Sabtu (tidak masuk rekap absensi)\n";
        }
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "<i>Sistem Presensi Digital SMK NU WARGABINANGUN</i>";

        return $msg;
    }

    /**
     * Format Notifikasi Telegram untuk Presensi Masuk Wali Kelas
     */
    private function formatNotifWaliKelasMasuk($wali, $jamMasuk, $tanggal, $status = 'Hadir', $keterangan = 'Hadir tepat waktu') {
        $tglIndo = $this->formatTanggalIndonesia($tanggal);
        $nama = htmlspecialchars($wali->nama_lengkap ?? '-');
        $nip = htmlspecialchars(!empty($wali->nip) ? $wali->nip : '-');
        $kelasBinaan = htmlspecialchars(!empty($wali->nama_kelas) ? $wali->nama_kelas : '-');
        $waktuMasuk = substr($jamMasuk, 0, 5) . ' WIB';
        $isLate = (strtolower($status) === 'terlambat');
        $statusStr = $isLate ? 'Terlambat (Masuk)' : 'Hadir (Masuk)';
        $statusIcon = $isLate ? '⚠️' : '✅';
        $ketStr = htmlspecialchars($keterangan ?: ($isLate ? 'Terlambat hadir' : 'Hadir tepat waktu'));
        $isSabtu = (date('N', strtotime($tanggal)) == 6);

        $msg = $isSabtu ? "📢 <b>PRESENSI MASUK WALI KELAS (UJI COBA SABTU)</b>\n" : "📢 <b>PRESENSI MASUK WALI KELAS</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "📅 <b>Hari, Tanggal:</b> {$tglIndo}\n";
        $msg .= "👤 <b>Nama:</b> {$nama}\n";
        $msg .= "👔 <b>Peran:</b> Wali Kelas\n";
        $msg .= "🏫 <b>Kelas Binaan:</b> {$kelasBinaan}\n";
        $msg .= "🆔 <b>NIP:</b> {$nip}\n";
        $msg .= "⏰ <b>Jam Masuk:</b> {$waktuMasuk}\n";
        $msg .= "{$statusIcon} <b>Status:</b> {$statusStr}\n";
        $msg .= "📝 <b>Keterangan:</b> {$ketStr}\n";
        if ($isSabtu) {
            $msg .= "ℹ️ <b>Info:</b> Uji coba presensi hari Sabtu (tidak masuk rekap absensi)\n";
        }
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "<i>Sistem Presensi Digital SMK NU WARGABINANGUN</i>";

        return $msg;
    }

    /**
     * Format Notifikasi Telegram untuk Presensi Pulang Wali Kelas
     */
    private function formatNotifWaliKelasPulang($wali, $jamPulang, $jamMasuk, $tanggal, $status = 'Hadir (Pulang)', $keterangan = 'Kehadiran lengkap') {
        $tglIndo = $this->formatTanggalIndonesia($tanggal);
        $nama = htmlspecialchars($wali->nama_lengkap ?? '-');
        $nip = htmlspecialchars(!empty($wali->nip) ? $wali->nip : '-');
        $kelasBinaan = htmlspecialchars(!empty($wali->nama_kelas) ? $wali->nama_kelas : '-');
        $waktuMasuk = !empty($jamMasuk) ? substr($jamMasuk, 0, 5) . ' WIB' : '-';
        $waktuPulang = substr($jamPulang, 0, 5) . ' WIB';
        $isLate = (stripos($status, 'terlambat') !== false);
        $statusStr = $isLate ? 'Terlambat (Pulang)' : 'Hadir (Pulang)';
        $statusIcon = $isLate ? '⚠️' : '✅';
        $ketStr = htmlspecialchars($keterangan ?: ($isLate ? 'Terlambat hadir & pulang lengkap' : 'Kehadiran lengkap'));
        $isSabtu = (date('N', strtotime($tanggal)) == 6);

        $msg = $isSabtu ? "🏠 <b>PRESENSI PULANG WALI KELAS (UJI COBA SABTU)</b>\n" : "🏠 <b>PRESENSI PULANG WALI KELAS</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "📅 <b>Hari, Tanggal:</b> {$tglIndo}\n";
        $msg .= "👤 <b>Nama:</b> {$nama}\n";
        $msg .= "👔 <b>Peran:</b> Wali Kelas\n";
        $msg .= "🏫 <b>Kelas Binaan:</b> {$kelasBinaan}\n";
        $msg .= "🆔 <b>NIP:</b> {$nip}\n";
        $msg .= "⏰ <b>Jam Masuk:</b> {$waktuMasuk}\n";
        $msg .= "🚪 <b>Jam Pulang:</b> {$waktuPulang}\n";
        $msg .= "{$statusIcon} <b>Status:</b> {$statusStr}\n";
        $msg .= "📝 <b>Keterangan:</b> {$ketStr}\n";
        if ($isSabtu) {
            $msg .= "ℹ️ <b>Info:</b> Uji coba presensi hari Sabtu (tidak masuk rekap absensi)\n";
        }
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "<i>Sistem Presensi Digital SMK NU WARGABINANGUN</i>";

        return $msg;
    }

    /**
     * Mengirimkan notifikasi kehadiran ke Telegram Bot API
     * 
     * Menggunakan konfigurasi:
     * - TELEGRAM_CHAT_ID: 6634167495
     * - TELEGRAM_BOT_TOKEN: 8754245306:AAEmHuO4fK0rMg71hUQr_XqDgkY3f_95pUk08:20
     * Endpoint Telegram: https://api.telegram.org/bot<TOKEN>/sendMessage
     * 
     * @param string $pesan Pesan format HTML yang akan dikirim
     * @return bool Status pengiriman berhasil atau tidak
     */
    private function kirimNotifikasiTelegram($pesan) {
        $chatId = defined('TELEGRAM_CHAT_ID') ? TELEGRAM_CHAT_ID : '6634167495';
        $token = defined('TELEGRAM_BOT_TOKEN') ? TELEGRAM_BOT_TOKEN : '8754245306:AAEmHuO4fK0rMg71hUQr_XqDgkY3f_95pUk';

        if (empty($token) || empty($chatId)) {
            return false;
        }

        // Sanitasi otomatis jika token mengandung suffix timestamp yang tidak sengaja ter-copy (misal 08:20 atau :20)
        $token = trim($token);
        $token = preg_replace('/(?:\d{1,2})?:\d{2}$/', '', $token);

        // Sub-fungsi untuk eksekusi request sendMessage Telegram
        $executeTelegramRequest = function($botToken) use ($chatId, $pesan) {
            $url = "https://api.telegram.org/bot" . trim($botToken) . "/sendMessage";
            $postData = [
                'chat_id' => $chatId,
                'text' => $pesan,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            ];

            $httpCode = 0;
            $response = null;
            $curlError = null;

            // 1. Eksekusi menggunakan cURL jika tersedia dengan IPv4 paksa & timeout memadai
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                if ($httpCode === 200) {
                    return [
                        'success' => true,
                        'http_code' => $httpCode,
                        'response' => $response,
                        'error' => null
                    ];
                }
            }

            // 2. Fallback menggunakan stream context / file_get_contents jika cURL gagal atau non-200
            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($postData),
                    'timeout' => 12
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ];
            $context = stream_context_create($options);
            $res = @file_get_contents($url, false, $context);
            if ($res !== false) {
                return [
                    'success' => true,
                    'http_code' => 200,
                    'response' => $res,
                    'error' => null
                ];
            }

            return [
                'success' => false,
                'http_code' => $httpCode ?: 500,
                'response' => $response,
                'error' => $curlError ?: 'file_get_contents failed'
            ];
        };

        // Percobaan pengiriman dengan token konfigurasi aktif
        $result = $executeTelegramRequest($token);
        if ($result['success']) {
            return true;
        }

        // Antisipasi: jika token memiliki trailing suffix seperti ':20' yang menyebabkan 404 dari Telegram,
        // coba bersihkan suffix setelah titik dua kedua
        if (substr_count($token, ':') > 1) {
            $lastColonPos = strrpos($token, ':');
            $cleanToken = substr($token, 0, $lastColonPos);
            $cleanResult = $executeTelegramRequest($cleanToken);
            if ($cleanResult['success']) {
                return true;
            }
        }

        // Catat di log server tanpa mengganggu alur respon presensi user
        error_log("Gagal kirim notifikasi Telegram: HTTP {$result['http_code']} - " . ($result['error'] ?: $result['response']));
        return false;
    }
}
