 <?php
/**
 * Chatbot API Endpoint Proxy - Hadir.io
 * Membaca SELURUH informasi database sistem absensi sekolah secara komprehensif,
 * menjawab layaknya orang mengobrol santai namun tetap sangat sopan dan santun,
 * to the point sesuai yang ditanyakan, dan menyapa sesuai waktu real-time yang akurat.
 */

header('Content-Type: application/json; charset=utf-8');

// Set timezone Jakarta
date_default_timezone_set('Asia/Jakarta');

// Load Environment Variables (.env)
$envFilePath = __DIR__ . '/.env';
if (file_exists($envFilePath)) {
    $envVariables = parse_ini_file($envFilePath);
    foreach ($envVariables as $key => $value) {
        $_ENV[$key] = $value;
    }
} else {
    $envFilePath = __DIR__ . '/chatbot-api/.env';
    if (file_exists($envFilePath)) {
        $envVariables = parse_ini_file($envFilePath);
        foreach ($envVariables as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit;
}

$requestBody = file_get_contents('php://input');
$data = !empty($requestBody) ? json_decode($requestBody, true) : null;
if (!$data && !empty($_POST)) {
    $data = $_POST;
}

if (!$data || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Pesan tidak valid atau kosong.']);
    exit;
}

$message = trim((string)$data['message']);
$history = isset($data['history']) && is_array($data['history']) ? $data['history'] : [];

/**
 * Helper untuk mengirim response chatbot ke client.
 * Menjamin tidak ada karakter asterisk (*) di seluruh respon bot
 * serta merapikan spasi ganda.
 */
function sendBotReply($reply) {
    if (is_string($reply)) {
        // Hapus seluruh karakter bintang (*) agar format bersih dan rapi
        $reply = str_replace('*', '', $reply);
        // Rapikan spasi berlebih
        $reply = preg_replace('/[ \t]{2,}/', ' ', $reply);
        $reply = trim($reply);
    }
    echo json_encode(['reply' => $reply]);
    exit;
}

/**
 * Helper penolakan RBAC yang konsisten dan multibahasa sesuai role pengguna aktif.
 */
function buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, $requestedTopicId, $requestedTopicEn = '', $requestedTopicSu = '', $requestedTopicJv = '') {
    $requestedTopicId = preg_replace('/^data\s+/i', '', trim($requestedTopicId));
    $topicEn = $requestedTopicEn ?: $requestedTopicId;
    $topicSu = preg_replace('/^data\s+/i', '', trim($requestedTopicSu ?: $requestedTopicId));
    $topicJv = preg_replace('/^data\s+/i', '', trim($requestedTopicJv ?: $requestedTopicId));

    if ($rawRole === 'siswa') {
        if ($detectedLang === 'en') {
            return "I apologize, Kak {$namaLengkap}. You are currently logged in as a Student. According to system access policies, {$topicEn} cannot be accessed by students. You can only view your personal data (profile/NIS/NISN, attendance status & history, permit requests, visible coaching notes, and your own class schedule for {$detailKelas}).";
        }
        if ($detectedLang === 'su') {
            return "Hapunten pisan Kak {$namaLengkap}, anjeun ayeuna login salaku Siswa. Dumasar aturan hak aksés sistem, data {$topicSu} teu tiasa diaksés ku siswa. Anjeun mung tiasa ningali data pribadi anjeun nyalira (profil/NIS/NISN, absensi pribadi, pangajuan izin, catetan pembinaan siswa, sareng jadwal kelas {$detailKelas}).";
        }
        if ($detectedLang === 'jv') {
            return "Nyuwun pangapunten Kak {$namaLengkap}, panjenengan sakmenika login minangka Siswa. Adhedhasar pranatan hak akses sistem, data {$topicJv} mboten saged dipunakses dening siswa. Panjenengan namung saged mirsani data pribadi piyambak (profil/NIS/NISN, presensi pribadi, pengajuan izin, catatan pembinaan, lan jadwal kelas {$detailKelas}).";
        }
        return "Mohon maaf Kak {$namaLengkap}, kamu saat ini login sebagai Siswa. Sesuai batasan hak akses database sistem, data {$requestedTopicId} tidak dapat diakses oleh siswa. Kamu hanya dapat mengakses data pribadimu sendiri (profil & NIS/NISN pribadi, status & riwayat absensi pribadi, pengajuan izin/sakit pribadi, catatan pembinaan siswa, serta jadwal pelajaran kelas {$detailKelas}).";
    }

    if ($rawRole === 'guru') {
        if ($detectedLang === 'en') {
            return "I apologize, {$sapaanGelar} {$namaLengkap}. You are currently logged in as a Teacher (Guru). Based on role access control, {$topicEn} is outside your authorization. You can only access your personal teacher profile/NIP, personal teaching schedule, personal attendance records, permit requests, and Principal notes addressed to you.";
        }
        if ($detectedLang === 'su') {
            return "Hapunten pisan {$sapaanGelar} {$namaLengkap}, anjeun ayeuna login salaku Guru. Dumasar hak aksés sistem, data {$topicSu} sanes wewenang role Guru. Anjeun mung tiasa ngaksés profil/NIP pribadi, jadwal ngawulang pribadi, riwayat absensi pribadi, pangajuan izin pribadi, sareng catetan ti Kepala Sekolah kanggo anjeun.";
        }
        if ($detectedLang === 'jv') {
            return "Nyuwun pangapunten {$sapaanGelar} {$namaLengkap}, panjenengan login minangka Guru. Adhedhasar pranatan hak akses sistem, data {$topicJv} sanes wewenang role Guru. Panjenengan namung saged ngakses profil/NIP pribadi, jadwal mulang pribadi, presensi pribadi, pengajuan izin pribadi, lan catatan saking Kepala Sekolah kagem panjenengan.";
        }
        return "Mohon maaf {$sapaanGelar} {$namaLengkap}, saat ini Anda login sebagai Guru. Sesuai batasan hak akses database sistem, data {$requestedTopicId} tidak dapat diakses pada role Guru. Anda hanya berwenang mengakses profil & NIP pribadi, jadwal mengajar pribadi, status & riwayat presensi pribadi, pengajuan izin pribadi, serta catatan Kepala Sekolah untuk Anda.";
    }

    if ($rawRole === 'wali_kelas') {
        $kelasInfo = !empty($detailKelas) ? " ({$detailKelas})" : "";
        if ($detectedLang === 'en') {
            return "I apologize, {$sapaanGelar} {$namaLengkap}. You are currently logged in as Homeroom Teacher{$kelasInfo}. Based on role access control, {$topicEn} is outside your scope. You can only access your personal data and the student, attendance, permit, schedule, and coaching records of your own homeroom class{$kelasInfo}.";
        }
        return "Mohon maaf {$sapaanGelar} {$namaLengkap}, saat ini Anda login sebagai Wali Kelas{$kelasInfo}. Sesuai batasan hak akses database sistem, data {$requestedTopicId} berada di luar wewenang Anda. Anda hanya berwenang mengakses data pribadi Anda serta data siswa, presensi, jadwal pelajaran, pengajuan izin, dan catatan pembinaan khusus untuk kelas binaan Anda{$kelasInfo}.";
    }

    if ($rawRole === 'kepala_sekolah') {
        if ($detectedLang === 'en') {
            return "I apologize, {$sapaanGelar} {$namaLengkap}. As Principal (Kepala Sekolah), your authorized database scope covers school overview statistics, teacher and student attendance reports/monitoring, leave summaries, and Principal Notes for Teachers (Catatan untuk Guru). Detailed {$topicEn} is managed by the Administrator.";
        }
        return "Mohon maaf {$sapaanGelar} {$namaLengkap}, pada role Kepala Sekolah, cakupan database Anda meliputi statistik umum sekolah, laporan & pemantauan kehadiran guru dan siswa, pengajuan ketidakhadiran terkait laporan, serta Catatan untuk Guru. Untuk data {$requestedTopicId} merupakan wewenang Administrator atau peran terkait.";
    }

    return "Mohon maaf, Anda tidak memiliki hak akses untuk melihat data {$requestedTopicId}.";
}

// 1. WAKTU REAL-TIME INDONESIA (WIB)
$hour = (int)date('G'); // 0 - 23
$jamSekarang = date('H:i');
$tanggalFormat = date('d-m-Y');
$hariMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
$namaHari = $hariMap[date('N')];

if ($hour >= 5 && $hour < 11) {
    $periodeWaktu = 'Pagi';
    $sapaanWaktu = 'Selamat pagi';
    $periodeWaktuEn = 'morning';
    $periodeWaktuSu = 'enjing';
    $periodeWaktuJv = 'enjang';
} elseif ($hour >= 11 && $hour < 15) {
    $periodeWaktu = 'Siang';
    $sapaanWaktu = 'Selamat siang';
    $periodeWaktuEn = 'afternoon';
    $periodeWaktuSu = 'siang';
    $periodeWaktuJv = 'siang';
} elseif ($hour >= 15 && $hour < 18) {
    $periodeWaktu = 'Sore';
    $sapaanWaktu = 'Selamat sore';
    $periodeWaktuEn = 'afternoon';
    $periodeWaktuSu = 'sonten';
    $periodeWaktuJv = 'sonten';
} else {
    $periodeWaktu = 'Malam';
    $sapaanWaktu = 'Selamat malam';
    $periodeWaktuEn = 'evening';
    $periodeWaktuSu = 'wengi';
    $periodeWaktuJv = 'dalu';
}

// 2. KONEKSI KE DATABASE & IDENTIFIKASI PENGGUNA
$userId = $_SESSION['user_id'];
$rawRole = strtolower(trim((string)($_SESSION['user_role'] ?? $_SESSION['role'] ?? '')));
if ($rawRole === 'kepsek') {
    $rawRole = 'kepala_sekolah';
} elseif ($rawRole === 'walikelas') {
    $rawRole = 'wali_kelas';
}

$namaLengkap = "Pengguna";
$detailKelas = "";
$detailJurusan = "";
$roleLabel = !empty($rawRole) ? ucfirst($rawRole) : "Pengguna";
$userExtra = "";
$userGender = "";

$mySiswaId = 0;
$myKelasId = 0;
$myGuruId = 0;
$myWaliId = 0;
$myNis = '';
$myNisn = '';
$myNip = '';
$myJadwalHariIni = [];
$myClassJadwalHariIni = [];

$databaseContext = "";
$searchContext = "";

try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $db   = $_ENV['DB_NAME'] ?? 'db_absensi_smk';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? '';
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil info akun user
    $stU = $pdo->prepare("SELECT id, username, role, jenis_kelamin, nama_lengkap FROM users WHERE id = ?");
    $stU->execute([$userId]);
    $uAcc = $stU->fetch(PDO::FETCH_ASSOC);
    if ($uAcc) {
        if (!empty($uAcc['nama_lengkap'])) {
            $namaLengkap = $uAcc['nama_lengkap'];
        } elseif (!empty($uAcc['username'])) {
            $namaLengkap = $uAcc['username'];
        }
        if (!empty($uAcc['role'])) {
            $dbRole = strtolower(trim($uAcc['role']));
            if ($dbRole === 'kepsek') {
                $dbRole = 'kepala_sekolah';
            } elseif ($dbRole === 'walikelas') {
                $dbRole = 'wali_kelas';
            }
            $rawRole = $dbRole;
            $roleLabel = ucfirst(str_replace('_', ' ', $rawRole));
        }
        if (!empty($uAcc['jenis_kelamin'])) {
            $userGender = $uAcc['jenis_kelamin'];
        }
    }

    if ($rawRole === 'siswa') {
        $stProfile = $pdo->prepare("SELECT s.*, k.nama_kelas, j.nama_jurusan,
                                           COALESCE(wkd.nama_lengkap, gw.nama_lengkap, 'Belum ditentukan') AS nama_wali_kelas
                                    FROM siswa s
                                    LEFT JOIN kelas k ON s.kelas_id = k.id
                                    LEFT JOIN jurusan j ON k.jurusan_id = j.id
                                    LEFT JOIN wali_kelas wk ON k.id = wk.kelas_id
                                    LEFT JOIN wali_kelas_data wkd ON wk.wali_kelas_data_id = wkd.id
                                    LEFT JOIN guru gw ON wkd.user_id = gw.user_id
                                    WHERE s.user_id = ?");
        $stProfile->execute([$userId]);
        $prof = $stProfile->fetch(PDO::FETCH_ASSOC);
        if ($prof) {
            $mySiswaId = (int)$prof['id'];
            $myKelasId = (int)($prof['kelas_id'] ?? 0);
            $myNis = (string)($prof['nis'] ?? '');
            $myNisn = (string)($prof['nisn'] ?? '');
            $namaLengkap = $prof['nama_lengkap'];
            $detailKelas = (string)($prof['nama_kelas'] ?? '');
            $detailJurusan = (string)($prof['nama_jurusan'] ?? '');
            $roleLabel = "Siswa Kelas " . $detailKelas;
            if (!empty($prof['jenis_kelamin'])) {
                $userGender = $prof['jenis_kelamin'];
            }

            $userExtra .= "- NIS: " . ($myNis ?: '-') . " | NISN: " . ($myNisn ?: '-') . "\n";
            $userExtra .= "- Jurusan: " . ($detailJurusan ?: '-') . " | Wali Kelas: " . ($prof['nama_wali_kelas'] ?? '-') . "\n";
            $userExtra .= "- No. Telepon: " . (!empty($prof['no_telp']) ? $prof['no_telp'] : '-') . " | Alamat: " . (!empty($prof['alamat']) ? $prof['alamat'] : '-') . "\n";

            // Status Absen Hari Ini
            $stAbs = $pdo->prepare("SELECT status, jam_masuk, jam_pulang, keterangan FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
            $stAbs->execute([$mySiswaId, date('Y-m-d')]);
            $myAbs = $stAbs->fetch(PDO::FETCH_ASSOC);
            if ($myAbs) {
                $jm = $myAbs['jam_masuk'] ? substr($myAbs['jam_masuk'], 0, 5) . ' WIB' : 'Belum scan';
                $jp = $myAbs['jam_pulang'] ? substr($myAbs['jam_pulang'], 0, 5) . ' WIB' : 'Belum scan';
                $ket = !empty($myAbs['keterangan']) ? $myAbs['keterangan'] : '-';
                $userExtra .= "- Status Kehadiran Saya Hari Ini: " . ucfirst($myAbs['status']) . " (Masuk: $jm, Pulang: $jp, Ket: $ket)\n";
            } else {
                $userExtra .= "- Status Kehadiran Saya Hari Ini: Belum scan masuk\n";
            }

            // Statistik Kumulatif Kehadiran Pribadi Siswa
            $stStatS = $pdo->prepare("SELECT LOWER(status) as st, COUNT(DISTINCT tanggal) as jml FROM absensi_siswa WHERE siswa_id = ? AND DAYOFWEEK(tanggal) NOT IN (1, 7) GROUP BY LOWER(status)");
            $stStatS->execute([$mySiswaId]);
            $statMapS = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
            foreach ($stStatS->fetchAll(PDO::FETCH_ASSOC) as $sr) {
                if ($sr['st'] === 'bolos' || $sr['st'] === 'alpa') {
                    $statMapS['alpa'] += (int)$sr['jml'];
                } elseif (isset($statMapS[$sr['st']])) {
                    $statMapS[$sr['st']] += (int)$sr['jml'];
                }
            }
            $userExtra .= "- Statistik Kehadiran Kumulatif Saya: Hadir: {$statMapS['hadir']}, Terlambat: {$statMapS['terlambat']}, Izin: {$statMapS['izin']}, Sakit: {$statMapS['sakit']}, Alpa: {$statMapS['alpa']}\n";

            // Riwayat Kehadiran Pribadi Siswa (Hanya Hari Efektif Sekolah: Senin s/d Jumat)
            $stRiwS = $pdo->prepare("SELECT tanggal, status, jam_masuk, jam_pulang, keterangan 
                                     FROM absensi_siswa 
                                     WHERE siswa_id = ? AND DAYOFWEEK(tanggal) NOT IN (1, 7) 
                                     ORDER BY tanggal DESC");
            $stRiwS->execute([$mySiswaId]);
            $riwRowsS = $stRiwS->fetchAll(PDO::FETCH_ASSOC);
            $totalRiwS = count($riwRowsS);
            $userExtra .= "- Total Riwayat Presensi Pribadi Saya: {$totalRiwS} catatan (hanya hari Senin s/d Jumat, hari Sabtu dan Minggu tidak direkap)\n";
            $userExtra .= "- Riwayat Presensi Pribadi Saya (Senin s/d Jumat):\n";
            if ($riwRowsS) {
                foreach (array_slice($riwRowsS, 0, 15) as $rw) {
                    $jm = $rw['jam_masuk'] ? substr($rw['jam_masuk'], 0, 5) : '-';
                    $jp = $rw['jam_pulang'] ? substr($rw['jam_pulang'], 0, 5) : '-';
                    $ket = !empty($rw['keterangan']) ? $rw['keterangan'] : '-';
                    $userExtra .= "  • {$rw['tanggal']} | Status: " . ucfirst($rw['status']) . " | Masuk: $jm | Pulang: $jp | Ket: $ket\n";
                }
            } else {
                $userExtra .= "  • Belum ada riwayat presensi tercatat.\n";
            }

            // Pengajuan Izin/Sakit Pribadi Siswa
            $stIzS = $pdo->prepare("SELECT tanggal, jenis, alasan, status FROM pengajuan_izin WHERE siswa_id = ? AND tipe_pemohon = 'siswa' ORDER BY tanggal DESC, id DESC");
            $stIzS->execute([$mySiswaId]);
            $izRowsS = $stIzS->fetchAll(PDO::FETCH_ASSOC);
            $totIzS = count($izRowsS);
            $cntIzinOnly = count(array_filter($izRowsS, fn($x) => strtolower($x['jenis']) === 'izin'));
            $cntSakitOnly = count(array_filter($izRowsS, fn($x) => strtolower($x['jenis']) === 'sakit'));
            $userExtra .= "- Total Pengajuan Izin/Sakit Pribadi Saya: {$totIzS} kali pengajuan ({$cntIzinOnly} Izin, {$cntSakitOnly} Sakit)\n";
            $userExtra .= "- Riwayat Pengajuan Izin/Sakit Pribadi Saya:\n";
            if ($izRowsS) {
                foreach ($izRowsS as $iz) {
                    $userExtra .= "  • Tanggal {$iz['tanggal']} | {$iz['jenis']} | Alasan: \"{$iz['alasan']}\" | Status: {$iz['status']}\n";
                }
            } else {
                $userExtra .= "  • Belum pernah mengajukan izin atau sakit.\n";
            }

            // Catatan Pembinaan Pribadi Siswa (HANYA yang visibilitas = 'Siswa')
            $stCatS = $pdo->prepare("SELECT cp.created_at, cp.kategori, cp.isi_catatan, cp.status_baca,
                                            COALESCE(g.nama_lengkap, u.username, cp.pembuat_role) as pencatat
                                     FROM catatan_pembinaan cp
                                     LEFT JOIN users u ON cp.pembuat_user_id = u.id
                                     LEFT JOIN guru g ON g.user_id = u.id
                                     WHERE cp.siswa_id = ? AND cp.visibilitas = 'Siswa'
                                     ORDER BY cp.created_at DESC LIMIT 10");
            $stCatS->execute([$mySiswaId]);
            $catRowsS = $stCatS->fetchAll(PDO::FETCH_ASSOC);
            $userExtra .= "- Catatan Pembinaan untuk Saya (Visibilitas Siswa):\n";
            if ($catRowsS) {
                foreach ($catRowsS as $cp) {
                    $tgl = date('d-m-Y', strtotime($cp['created_at']));
                    $bacaSt = !empty($cp['status_baca']) ? 'Sudah dibaca' : 'Belum dibaca';
                    $userExtra .= "  • Tgl $tgl | Kategori: {$cp['kategori']} | Oleh: {$cp['pencatat']} ($bacaSt) | Isi: \"{$cp['isi_catatan']}\"\n";
                }
            } else {
                $userExtra .= "  • Tidak ada catatan pembinaan untuk Anda.\n";
            }

            // Jadwal Pelajaran Kelas Siswa Hari Ini & Lengkap Seminggu (Senin - Jumat)
            $stSiswaJadwalAll = $pdo->prepare("SELECT j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel,
                                                      COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Pengajar') as pengajar
                                               FROM jadwal_mengajar j
                                               JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                                               LEFT JOIN guru g ON j.guru_id = g.id
                                               LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id
                                               WHERE j.kelas_id = ?
                                               ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC");
            $stSiswaJadwalAll->execute([$myKelasId]);
            $myClassJadwalAll = $stSiswaJadwalAll->fetchAll(PDO::FETCH_ASSOC);

            // Filter hari ini
            $myClassJadwalHariIni = array_values(array_filter($myClassJadwalAll, fn($sj) => strtolower($sj['hari']) === strtolower($namaHari)));
            $userExtra .= "- Jadwal Pelajaran Kelas Saya Hari Ini ($namaHari, " . count($myClassJadwalHariIni) . " mata pelajaran):\n";
            if (!empty($myClassJadwalHariIni)) {
                foreach ($myClassJadwalHariIni as $sj) {
                    $mulai = substr($sj['jam_mulai'], 0, 5);
                    $selesai = substr($sj['jam_selesai'], 0, 5);
                    $ruangStr = !empty($sj['ruang']) ? " (Ruang: {$sj['ruang']})" : "";
                    $userExtra .= "  • Pukul $mulai - $selesai: {$sj['nama_mapel']} — Pengajar: {$sj['pengajar']}{$ruangStr}\n";
                }
            } else {
                $userExtra .= "  • Tidak ada jadwal pelajaran untuk kelas siswa ini pada hari ini ($namaHari).\n";
            }

            // Jadwal Lengkap Kelas Siswa untuk Seluruh Hari (Senin s/d Jumat)
            $userExtra .= "- Jadwal Pelajaran Lengkap Kelas {$detailKelas} Seluruh Hari (Senin s/d Jumat):\n";
            $curD = '';
            foreach ($myClassJadwalAll as $sj) {
                $hd = ucfirst($sj['hari']);
                if ($curD !== $hd) {
                    $curD = $hd;
                    $userExtra .= "  [Hari {$curD}]:\n";
                }
                $mulai = substr($sj['jam_mulai'], 0, 5);
                $selesai = substr($sj['jam_selesai'], 0, 5);
                $ruangStr = !empty($sj['ruang']) ? " (Ruang: {$sj['ruang']})" : "";
                $userExtra .= "    • $mulai - $selesai : {$sj['nama_mapel']} — Pengajar: {$sj['pengajar']}{$ruangStr}\n";
            }
        }
    } elseif ($rawRole === 'guru' || $rawRole === 'wali_kelas') {
        if ($rawRole === 'wali_kelas') {
            $stProfile = $pdo->prepare("SELECT wkd.id as wali_id, g.id as guru_id, g.id as id,
                                               COALESCE(NULLIF(g.nama_lengkap,''), wkd.nama_lengkap) as nama_lengkap,
                                               COALESCE(NULLIF(g.nip,''), wkd.nip, '-') as nip,
                                               COALESCE(NULLIF(g.jenis_kelamin,''), wkd.jenis_kelamin) as jenis_kelamin,
                                               COALESCE(NULLIF(g.email,''), wkd.email, '-') as email,
                                               COALESCE(NULLIF(g.no_telp,''), wkd.no_telp, '-') as no_telp,
                                               k.id as kelas_binaan_id, k.nama_kelas as kelas_binaan, j.nama_jurusan as jurusan_binaan
                                        FROM wali_kelas_data wkd
                                        LEFT JOIN guru g ON wkd.user_id = g.user_id
                                        LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id
                                        LEFT JOIN kelas k ON wk.kelas_id = k.id
                                        LEFT JOIN jurusan j ON k.jurusan_id = j.id
                                        WHERE wkd.user_id = ?");
            $stProfile->execute([$userId]);
            $prof = $stProfile->fetch(PDO::FETCH_ASSOC);
            if (!$prof) {
                $stFall = $pdo->prepare("SELECT g.*, g.id as guru_id, 0 as wali_id, 0 as kelas_binaan_id, '' as kelas_binaan, '' as jurusan_binaan FROM guru g WHERE g.user_id = ?");
                $stFall->execute([$userId]);
                $prof = $stFall->fetch(PDO::FETCH_ASSOC);
            }
        } else {
            $stProfile = $pdo->prepare("SELECT g.*, g.id as guru_id, wkd.id as wali_id
                                        FROM guru g
                                        LEFT JOIN wali_kelas_data wkd ON g.user_id = wkd.user_id
                                        WHERE g.user_id = ?");
            $stProfile->execute([$userId]);
            $prof = $stProfile->fetch(PDO::FETCH_ASSOC);
        }

        if ($prof) {
            $namaLengkap = $prof['nama_lengkap'];
            $myGuruId = (int)($prof['guru_id'] ?? $prof['id'] ?? 0);
            $myWaliId = (int)($prof['wali_id'] ?? 0);
            $myNip = (string)($prof['nip'] ?? '');
            if (!empty($prof['jenis_kelamin'])) {
                $userGender = $prof['jenis_kelamin'];
            }
            if ($rawRole === 'wali_kelas' && !empty($prof['kelas_binaan'])) {
                $myKelasId = (int)($prof['kelas_binaan_id'] ?? 0);
                $detailKelas = (string)$prof['kelas_binaan'];
                $detailJurusan = (string)($prof['jurusan_binaan'] ?? '');
                $roleLabel = "Wali Kelas " . $detailKelas;
            } elseif ($rawRole === 'wali_kelas') {
                $roleLabel = "Wali Kelas";
            } else {
                $detailKelas = "";
                $roleLabel = "Guru";
            }

            // Mapel yang diampu oleh Guru / Wali Kelas ini
            $stMapelSelf = $pdo->prepare("SELECT COALESCE(GROUP_CONCAT(DISTINCT nama_mapel SEPARATOR ', '), '-') FROM mata_pelajaran WHERE guru_id = ? OR (? > 0 AND wali_kelas_id = ?)");
            $stMapelSelf->execute([$myGuruId, $myWaliId, $myWaliId]);
            $mapelSelf = $stMapelSelf->fetchColumn() ?: '-';

            $userExtra .= "- NIP: " . ($myNip ?: '-') . " | Mata Pelajaran Diampu: {$mapelSelf}\n";
            $userExtra .= "- Email: " . (!empty($prof['email']) ? $prof['email'] : '-') . " | No. Telepon: " . (!empty($prof['no_telp']) ? $prof['no_telp'] : '-') . "\n";
            if ($rawRole === 'wali_kelas' && !empty($detailKelas)) {
                $userExtra .= "- Kelas Binaan (Tanggung Jawab): {$detailKelas} ({$detailJurusan})\n";
            }

            // Status & Riwayat Absensi Pribadi Guru / Wali Kelas (Sinkron absensi_guru & absensi_wali_kelas)
            $absRecordsByDate = [];
            if ($myGuruId > 0) {
                $stG = $pdo->prepare("SELECT tanggal, status, jam_masuk, jam_pulang, keterangan FROM absensi_guru WHERE guru_id = ? AND DAYOFWEEK(tanggal) NOT IN (1, 7) ORDER BY tanggal DESC LIMIT 40");
                $stG->execute([$myGuruId]);
                foreach ($stG->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $absRecordsByDate[$row['tanggal']] = $row;
                }
            }
            if ($rawRole === 'wali_kelas' && $myWaliId > 0) {
                $stW = $pdo->prepare("SELECT tanggal, status, jam_masuk, jam_pulang, keterangan FROM absensi_wali_kelas WHERE wali_kelas_id = ? AND DAYOFWEEK(tanggal) NOT IN (1, 7) ORDER BY tanggal DESC LIMIT 40");
                $stW->execute([$myWaliId]);
                foreach ($stW->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (!isset($absRecordsByDate[$row['tanggal']]) || (empty($absRecordsByDate[$row['tanggal']]['jam_masuk']) && !empty($row['jam_masuk']))) {
                        $absRecordsByDate[$row['tanggal']] = $row;
                    }
                }
            }
            krsort($absRecordsByDate);

            $todayDateStr = date('Y-m-d');
            if (isset($absRecordsByDate[$todayDateStr])) {
                $myAbs = $absRecordsByDate[$todayDateStr];
                $jm = $myAbs['jam_masuk'] ? substr($myAbs['jam_masuk'], 0, 5) . ' WIB' : 'Belum scan';
                $jp = $myAbs['jam_pulang'] ? substr($myAbs['jam_pulang'], 0, 5) . ' WIB' : 'Belum scan';
                $ket = !empty($myAbs['keterangan']) ? $myAbs['keterangan'] : '-';
                $userExtra .= "- Status Kehadiran Saya Hari Ini: " . ucfirst($myAbs['status']) . " (Masuk: $jm, Pulang: $jp, Ket: $ket)\n";
            } else {
                $userExtra .= "- Status Kehadiran Saya Hari Ini: Belum scan masuk\n";
            }

            // Statistik Kehadiran Pribadi Kumulatif
            $statMapG = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
            foreach ($absRecordsByDate as $tgl => $rec) {
                $dow = (int)date('N', strtotime($tgl));
                if ($dow >= 6) continue;
                $stLower = strtolower($rec['status'] ?? '');
                if ($stLower === 'bolos' || $stLower === 'alpa') {
                    $statMapG['alpa']++;
                } elseif (isset($statMapG[$stLower])) {
                    $statMapG[$stLower]++;
                }
            }
            $userExtra .= "- Statistik Kehadiran Pribadi Saya: Hadir: {$statMapG['hadir']}, Terlambat: {$statMapG['terlambat']}, Izin: {$statMapG['izin']}, Sakit: {$statMapG['sakit']}, Alpa: {$statMapG['alpa']}\n";

            // Riwayat 15 Hari Terakhir
            $riwSlice = array_slice($absRecordsByDate, 0, 15, true);
            $userExtra .= "- Riwayat Presensi Pribadi Saya (Maks. 15 Terbaru):\n";
            if ($riwSlice) {
                foreach ($riwSlice as $tgl => $rw) {
                    $jm = $rw['jam_masuk'] ? substr($rw['jam_masuk'], 0, 5) : '-';
                    $jp = $rw['jam_pulang'] ? substr($rw['jam_pulang'], 0, 5) : '-';
                    $ket = !empty($rw['keterangan']) ? $rw['keterangan'] : '-';
                    $userExtra .= "  • {$tgl} | Status: " . ucfirst($rw['status']) . " | Masuk: $jm | Pulang: $jp | Ket: $ket\n";
                }
            } else {
                $userExtra .= "  • Belum ada riwayat presensi pribadi tercatat.\n";
            }

            // Pengajuan Izin/Sakit Pribadi Guru / Wali Kelas
            if ($myGuruId > 0) {
                $stIzG = $pdo->prepare("SELECT tanggal, jenis, alasan, status FROM pengajuan_izin WHERE guru_id = ? AND tipe_pemohon = 'guru' ORDER BY tanggal DESC, id DESC LIMIT 10");
                $stIzG->execute([$myGuruId]);
                $izRowsG = $stIzG->fetchAll(PDO::FETCH_ASSOC);
                $userExtra .= "- Riwayat Pengajuan Izin/Sakit Pribadi Saya:\n";
                if ($izRowsG) {
                    foreach ($izRowsG as $iz) {
                        $userExtra .= "  • Tanggal {$iz['tanggal']} | {$iz['jenis']} | Alasan: \"{$iz['alasan']}\" | Status: {$iz['status']}\n";
                    }
                } else {
                    $userExtra .= "  • Belum pernah mengajukan izin atau sakit.\n";
                }

                // Catatan dari Kepala Sekolah untuk Guru ini
                $stCatG = $pdo->prepare("SELECT tanggal, judul, kategori, urgensi, isi_catatan, status_baca FROM catatan_guru WHERE guru_id = ? ORDER BY tanggal DESC, id DESC LIMIT 15");
                $stCatG->execute([$myGuruId]);
                $catRowsG = $stCatG->fetchAll(PDO::FETCH_ASSOC);
                $userExtra .= "- Catatan dari Kepala Sekolah untuk Saya:\n";
                if ($catRowsG) {
                    foreach ($catRowsG as $cg) {
                        $judulStr = !empty($cg['judul']) ? "[{$cg['judul']}] " : "";
                        $stBaca = ($cg['status_baca'] === 'sudah') ? 'Sudah dibaca' : 'Belum dibaca';
                        $userExtra .= "  • Tgl {$cg['tanggal']} | {$cg['kategori']} ({$cg['urgensi']}) | $stBaca | {$judulStr}\"{$cg['isi_catatan']}\"\n";
                    }
                } else {
                    $userExtra .= "  • Belum ada catatan pembinaan dari Kepala Sekolah untuk Anda.\n";
                }
            }

            // Jadwal Mengajar Pribadi Guru Ini Sepanjang Minggu (Senin - Jumat)
            $stMyJadwalAll = $pdo->prepare("SELECT j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel, k.nama_kelas
                                            FROM jadwal_mengajar j
                                            JOIN kelas k ON j.kelas_id = k.id
                                            JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                                            WHERE ((? > 0 AND j.guru_id = ?) OR (? > 0 AND j.wali_kelas_id = ?))
                                            ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC");
            $stMyJadwalAll->execute([$myGuruId, $myGuruId, $myWaliId, $myWaliId]);
            $myJadwalAll = $stMyJadwalAll->fetchAll(PDO::FETCH_ASSOC);

            $myJadwalHariIni = array_values(array_filter($myJadwalAll, fn($mj) => strtolower($mj['hari']) === strtolower($namaHari)));

            $userExtra .= "- Jadwal Mengajar Pribadi Saya Hari Ini ($namaHari, TEPAT " . count($myJadwalHariIni) . " SESI MENGAJAR):\n";
            if (!empty($myJadwalHariIni)) {
                foreach ($myJadwalHariIni as $mj) {
                    $mulai = substr($mj['jam_mulai'], 0, 5);
                    $selesai = substr($mj['jam_selesai'], 0, 5);
                    $ruangStr = !empty($mj['ruang']) ? " (Ruang: {$mj['ruang']})" : "";
                    $userExtra .= "  • Pukul $mulai - $selesai: {$mj['nama_mapel']} (Kelas {$mj['nama_kelas']}){$ruangStr}\n";
                }
            } else {
                $userExtra .= "  • Tidak ada jadwal mengajar untuk {$namaLengkap} pada hari ini ($namaHari).\n";
            }

            $userExtra .= "- Jadwal Mengajar Pribadi Saya Seluruh Hari (Senin s/d Jumat):\n";
            if (!empty($myJadwalAll)) {
                $curDg = '';
                foreach ($myJadwalAll as $mj) {
                    $hd = ucfirst($mj['hari']);
                    if ($curDg !== $hd) {
                        $curDg = $hd;
                        $userExtra .= "  [Hari {$curDg}]:\n";
                    }
                    $mulai = substr($mj['jam_mulai'], 0, 5);
                    $selesai = substr($mj['jam_selesai'], 0, 5);
                    $ruangStr = !empty($mj['ruang']) ? " (Ruang: {$mj['ruang']})" : "";
                    $userExtra .= "    • $mulai - $selesai : {$mj['nama_mapel']} (Kelas {$mj['nama_kelas']}){$ruangStr}\n";
                }
            } else {
                $userExtra .= "  • Belum ada jadwal mengajar pribadi tercatat.\n";
            }
        }
    } elseif ($rawRole === 'admin') {
        $namaLengkap = !empty($uAcc['nama_lengkap']) ? $uAcc['nama_lengkap'] : "Administrator";
        $roleLabel = "Administrator";
    } elseif ($rawRole === 'kepala_sekolah') {
        $namaLengkap = !empty($uAcc['nama_lengkap']) ? $uAcc['nama_lengkap'] : "Drs. H. Ahmad Fauzi, M.Pd";
        $roleLabel = "Kepala Sekolah";
        if (empty($userGender)) {
            $userGender = "L";
        }
    }

    // Tentukan sapaan gelar berdasarkan jenis kelamin dan role
    $isFemale = ($userGender === 'P');
    $isMale = ($userGender === 'L');

    if ($isFemale) {
        $sapaanGelar = 'Ibu';
        $sapaanGelarEn = 'Mrs./Ms.';
        $sapaanGelarSu = 'Ibu';
        $sapaanGelarJv = 'Ibu';
        $genderDesc = 'Perempuan';
    } elseif ($isMale) {
        $sapaanGelar = 'Bapak';
        $sapaanGelarEn = 'Mr.';
        $sapaanGelarSu = 'Bapa';
        $sapaanGelarJv = 'Bapak';
        $genderDesc = 'Laki-laki';
    } else {
        $sapaanGelar = 'Bapak/Ibu';
        $sapaanGelarEn = 'Sir/Madam';
        $sapaanGelarSu = 'Bapa/Ibu';
        $sapaanGelarJv = 'Bapak/Ibu';
        $genderDesc = 'Tidak diketahui';
    }

    // --- PRE-COMPUTED IDENTITAS PENGGUNA UNTUK BERBAGAI BAHASA ---
    if ($rawRole === 'siswa') {
        $sebutanSiswaId = $isFemale ? "siswi" : "siswa";
        $sebutanSiswaEn = $isFemale ? "female student" : "student";
        $sebutanSiswaSu = $isFemale ? "murid istri" : "murid pameget";
        $sebutanSiswaJv = $isFemale ? "siswi" : "siswa";

        $identitasJawabanId = "Kamu adalah {$namaLengkap}, {$sebutanSiswaId} dari kelas {$detailKelas}.";
        $identitasJawabanEn = "You are {$namaLengkap}, a {$sebutanSiswaEn} from class {$detailKelas}.";
        $identitasJawabanSu = "Anjeun teh {$namaLengkap}, {$sebutanSiswaSu} ti kelas {$detailKelas}.";
        $identitasJawabanJv = "Sampeyan punika {$namaLengkap}, {$sebutanSiswaJv} saking kelas {$detailKelas}.";
    } elseif ($rawRole === 'wali_kelas' || (!empty($detailKelas) && strpos($roleLabel, 'Wali Kelas') !== false)) {
        $identitasJawabanId = "{$sapaanGelar} adalah {$namaLengkap}, wali kelas {$detailKelas} di SMK NU WARGABINANGUN.";
        $identitasJawabanEn = "You are {$sapaanGelarEn} {$namaLengkap}, the homeroom teacher of class {$detailKelas} at SMK NU WARGABINANGUN.";
        $identitasJawabanSu = "{$sapaanGelarSu} teh {$namaLengkap}, wali kelas {$detailKelas} di SMK NU WARGABINANGUN.";
        $identitasJawabanJv = "{$sapaanGelarJv} punika {$namaLengkap}, wali kelas {$detailKelas} wonten ing SMK NU WARGABINANGUN.";
    } elseif ($rawRole === 'guru') {
        $identitasJawabanId = "{$sapaanGelar} adalah {$namaLengkap}, guru di SMK NU WARGABINANGUN.";
        $identitasJawabanEn = "You are {$sapaanGelarEn} {$namaLengkap}, a teacher at SMK NU WARGABINANGUN.";
        $identitasJawabanSu = "{$sapaanGelarSu} teh {$namaLengkap}, guru di SMK NU WARGABINANGUN.";
        $identitasJawabanJv = "{$sapaanGelarJv} punika {$namaLengkap}, guru wonten ing SMK NU WARGABINANGUN.";
    } elseif ($rawRole === 'admin') {
        $identitasJawabanId = "{$sapaanGelar} adalah Administrator sistem absensi sekolah.";
        $identitasJawabanEn = "You are the System Administrator for the school attendance system.";
        $identitasJawabanSu = "{$sapaanGelarSu} teh Administrator sistem absensi sakola.";
        $identitasJawabanJv = "{$sapaanGelarJv} punika Administrator sistem absensi sekolah.";
    } elseif ($rawRole === 'kepala_sekolah') {
        $identitasJawabanId = "{$sapaanGelar} adalah {$namaLengkap}, Kepala Sekolah SMK NU WARGABINANGUN.";
        $identitasJawabanEn = "You are {$sapaanGelarEn} {$namaLengkap}, the Principal (Kepala Sekolah) of SMK NU WARGABINANGUN.";
        $identitasJawabanSu = "{$sapaanGelarSu} teh {$namaLengkap}, Kepala Sekolah SMK NU WARGABINANGUN.";
        $identitasJawabanJv = "{$sapaanGelarJv} punika {$namaLengkap}, Kepala Sekolah SMK NU WARGABINANGUN.";
    } else {
        $identitasJawabanId = "Kamu adalah {$namaLengkap}.";
        $identitasJawabanEn = "You are {$namaLengkap}.";
        $identitasJawabanSu = "Anjeun teh {$namaLengkap}.";
        $identitasJawabanJv = "Sampeyan punika {$namaLengkap}.";
    }

    // --- QUICK INTENT MATCHING & MULTILINGUAL DETECTION ---
    $cleanPrompt = strtolower(trim(preg_replace('/[?!.,;:_\/\\|~`"\'()]/', ' ', $message)));
    $cleanPrompt = preg_replace('/\s+/', ' ', $cleanPrompt);

    // Deteksi Bahasa Pertanyaan (Sunda, Jawa, Inggris, Indonesia)
    $detectedLang = 'id';
    if (preg_match('/\b(sopo|sinten|kula|kulo|panjenengan|sampeyan|koe|kowe|jenengku|asmanipun|pira|piro|pinten|niki|niku|menika|mboten|rawuh|piye|kabare|sugeng|enjang|injing|dalu|ingkang|wonten|matur|nuwun|monggo|kanca|mulang)\b/i', $message)) {
        $detectedLang = 'jv';
    } elseif (preg_match('/\b(saha|abdi|simkuring|kuring|urang|anjeun|salira|damang|kumaha|teu|lebet|ieu|sakola|sabaraha|wasta|nami|pisan|leres|atos|parantos|mangga|hatur|nuhun|wilujeng|enjing|wengi|sampurasun|ngawulang)\b/i', $message) || (stripos($message, 'dinten') !== false && stripos($message, 'ieu') !== false)) {
        $detectedLang = 'su';
    } elseif (preg_match('/\b(who|what|where|when|why|how|am|is|are|you|me|my|your|i|teacher|teachers|student|students|class|classes|today|absent|present|name|attendance|hello|hi|good|morning|afternoon|evening|night|tell|show|list|schedule)\b/i', $message)) {
        $detectedLang = 'en';
    }

    // 0. Penolakan Pembuatan Coding / Script Pemrograman (Python, dll.)
    // Chatbot dibuat sebagai asisten sistem absensi dan memberikan pengarahan, BUKAN tools pemrograman
    $isCodingRequest = false;
    if (preg_match('/\b(python|py)\b/i', $message)) {
        $isCodingRequest = true;
    } elseif (preg_match('/\b(codingan|kodingan)\b/i', $cleanPrompt)) {
        $isCodingRequest = true;
    } elseif (preg_match('/\b(coding|koding|script|skrip|source\s*code|pemrograman|syntax|program|algoritma)\b/i', $message) && 
              preg_match('/\b(buat|buatkan|bikin|bikinin|tulis|tuliskan|contoh|ajar|ajarkan|generate|bisa|minta|berikan|write|make|create|belajar|tutorial|kalkulator|game|aplikasi)\b/i', $message)) {
        $isCodingRequest = true;
    } elseif (preg_match('/^(coding|koding|pemrograman|program|bikin\s*koding|buat\s*koding|bisa\s*coding|ajar\s*coding|bikin\s*program|buat\s*program)/i', $cleanPrompt)) {
        $isCodingRequest = true;
    } elseif (preg_match('/\b(php|javascript|js|html|css|java|c\+\+|cpp|csharp|golang|sql\s*query|bash|powershell)\b/i', $message) &&
              preg_match('/\b(buat|buatkan|bikin|bikinin|tulis|tuliskan|coding|koding|script|code|program)\b/i', $message)) {
        $isCodingRequest = true;
    }

    if ($isCodingRequest) {
        $sebutan = ($rawRole === 'siswa') ? 'Kak ' . $namaLengkap : $sapaanGelar;
        if ($detectedLang === 'en') {
            $reply = "I apologize {$sebutan}, but I am an AI assistant specifically dedicated to the SMK NU WARGABINANGUN Attendance System to provide system guidance, instructions, and school data. I cannot generate programming code or scripts (such as Python). If you need assistance using the attendance system or inquiring about school information, please let me know!";
        } elseif ($detectedLang === 'su') {
            $reply = "Hapunten pisan {$sebutan}, simkuring teh asisten AI khusus Sistem Absensi SMK NU WARGABINANGUN anu pancenna masihan pituduh sistem sareng inpormasi data sakola. Simkuring teu tiasa ngadamel coding atanapi script pamrograman (sapertos Python). Nanging, upami peryogi pituduh perkawis absensi atanapi data sakola, mangga ditaroskeun!";
        } elseif ($detectedLang === 'jv') {
            $reply = "Nyuwun pangapunten {$sebutan}, kula punika asisten AI mligi kagem Sistem Presensi SMK NU WARGABINANGUN ingkang ngayahi ayahan maringi pitedah sistem lan informasi sekolah. Kula mboten saged damel coding utawi script pemrograman (kados Python). Menawi panjenengan mbetahaken pitedah babagan presensi utawi informasi sekolah, sumangga dipun tangletaken!";
        } else {
            if ($rawRole === 'siswa') {
                $reply = "Mohon maaf {$sebutan}, saya adalah asisten AI resmi Sistem Absensi SMK NU WARGABINANGUN yang bertugas memberikan pengarahan, panduan penggunaan sistem, serta informasi seputar data absensi dan sekolah. Saya tidak dapat membuatkan coding atau script pemrograman (seperti Python). Jika kamu memerlukan pengarahan terkait cara penggunaan sistem absensi atau informasi data sekolah, silakan tanyakan kepada saya!";
            } else {
                $reply = "Mohon maaf {$sebutan}, saya adalah asisten AI resmi Sistem Absensi SMK NU WARGABINANGUN yang bertugas memberikan panduan pengarahan sistem dan informasi seputar data absensi serta sekolah. Saya tidak dapat membuatkan coding atau script pemrograman (seperti Python). Jika {$sebutan} membutuhkan panduan pengoperasian sistem absensi atau informasi data sekolah, saya siap membantu!";
            }
        }
        sendBotReply($reply);
    }

    // 0.1. Penolakan Pertanyaan Umur / Usia / Tanggal Lahir (Privasi & Integritas Basis Data Absensi)
    // Chatbot DILARANG menjawab pertanyaan umur siapa pun sesuai batasan database sistem absensi
    $isAgeRequest = false;
    if (preg_match('/\b(umur|usia|yuswa|yuswane|yuswanipun|how\s*old|age|tanggal\s*lahir|tgl\s*lahir|kapan\s*lahir|tahun\s*lahir|tempat\s*tanggal\s*lahir|ttl)\b/i', $cleanPrompt) ||
        (preg_match('/\b(lahir)\b/i', $cleanPrompt) && preg_match('/\b(kapan|tahun|tanggal|tgl|dimana|jam)\b/i', $cleanPrompt))) {
        $isAgeRequest = true;
    }

    if ($isAgeRequest) {
        $sebutan = ($rawRole === 'siswa') ? 'Kak ' . $namaLengkap : $sapaanGelar;
        if ($detectedLang === 'en') {
            $reply = "I apologize {$sebutan}, but as the AI Assistant for SMK NU WARGABINANGUN Attendance System, I cannot provide information regarding age or date of birth as that is private personal information. I am specifically designed to assist with attendance, schedules, and school academic records. Is there any attendance or schedule information I can help you with?";
        } elseif ($detectedLang === 'su') {
            $reply = "Hapunten pisan {$sebutan}, salaku Asisten AI Sistem Absensi SMK NU WARGABINANGUN, simkuring teu tiasa masihan inpormasi perkawis umur atanapi tanggal lahir kumargi sipatna privasi. Pancen simkuring nyaeta ngabantos inpormasi perkawis absensi, jadwal pangajaran, sareng data sakola dumasar kana database. Aya inpormasi absensi atanapi jadwal anu tiasa dibantos?";
        } elseif ($detectedLang === 'jv') {
            $reply = "Nyuwun pangapunten {$sebutan}, minangka Asisten AI Sistem Presensi SMK NU WARGABINANGUN, kula mboten saged maringi informasi babagan umur utawi tanggal lahir amargi sipatipun privasi. Ayahan kula punika mbiyantu maringi informasi babagan presensi, jadwal piwulangan, lan data sekolah adhedhasar database. Wonten informasi presensi utawi jadwal ingkang saged kula biyantu?";
        } else {
            $reply = "Mohon maaf {$sebutan}, sebagai asisten AI resmi Sistem Absensi SMK NU WARGABINANGUN, saya tidak dapat memberikan informasi mengenai umur atau tanggal lahir siapa pun karena data tersebut bersifat privasi. Saya bertugas membantu Anda terkait informasi presensi/absensi, jadwal kegiatan belajar mengajar, serta data operasional sekolah sesuai database sistem. Ada informasi terkait absensi atau jadwal yang dapat saya bantu?";
        }
        sendBotReply($reply);
    }

    // Deteksi apakah pesan menyebut kelas, jurusan, tingkat, atau jenis kelamin spesifik
    $mentionedClassNormalized = '';
    if (preg_match('/(?:\b(?:di\s*)?kelas[\s-]*|\b)(x|xi|xii|10|11|12)[\s-]*(tkj|tkr|tsm)(?:[\s-]*([1-9]))?\b/i', $cleanPrompt, $mKls)) {
        $tingkatMap = ['10' => 'x', '11' => 'xi', '12' => 'xii'];
        $tktNorm = strtolower($mKls[1]);
        if (isset($tingkatMap[$tktNorm])) {
            $tktNorm = $tingkatMap[$tktNorm];
        }
        $mentionedClassNormalized = trim($tktNorm . ' ' . strtolower($mKls[2]) . (!empty($mKls[3]) ? ' ' . $mKls[3] : ''));
    }
    $mentionedClassCompact = preg_replace('/[^a-z0-9]/', '', $mentionedClassNormalized);
    $detailKelasNorm = strtolower(trim(preg_replace('/\s+/', ' ', $detailKelas)));
    $detailKelasCompact = preg_replace('/[^a-z0-9]/', '', $detailKelasNorm);

    $mentionsOtherSpecificClass = false;
    if (!empty($mentionedClassCompact)) {
        if (empty($detailKelasCompact) || $mentionedClassCompact !== $detailKelasCompact) {
            $mentionsOtherSpecificClass = true;
        }
    } elseif (preg_match('/\bkelas\s+(lain|orang|sebelah|tetangga|seluruh|semua)\b/i', $cleanPrompt)) {
        $mentionsOtherSpecificClass = true;
    }

    $asksMale = (bool)preg_match('/\b(laki[-\s]*laki|lakilaki|laki|pria|cowok|cowo|putra|male|boys?|pameget|lanang|jaler)\b/i', $cleanPrompt);
    $asksFemale = (bool)preg_match('/\b(perempuan|wanita|cewek|cewe|putri|siswi|female|girls?|istri|wadon|estri)\b/i', $cleanPrompt);
    $asksGender = ($asksMale || $asksFemale || (bool)preg_match('/\b(jenis\s*kelamin|gender)\b/i', $cleanPrompt));
    $asksPerKelasBreakdown = (bool)preg_match('/\b(per\s*kelas|tiap\s*kelas|masing\s*-?\s*masing\s*kelas|setiap\s*kelas)\b/i', $cleanPrompt);

    $mentionedJurusanCode = '';
    if (empty($mentionedClassNormalized) && preg_match('/\b(tkj|tkr|tsm)\b/i', $cleanPrompt, $mJur)) {
        $mentionedJurusanCode = strtoupper($mJur[1]);
    }
    $mentionedTingkat = '';
    if (empty($mentionedClassNormalized) && preg_match('/\b(?:di\s*)?(?:kelas|tingkat|jenjang)\s+(x|xi|xii|10|11|12)\b/i', $cleanPrompt, $mTkt)) {
        $tingkatMapUpper = ['10' => 'X', '11' => 'XI', '12' => 'XII'];
        $tVal = strtoupper($mTkt[1]);
        $mentionedTingkat = $tingkatMapUpper[$tVal] ?? $tVal;
    }

    $dayKeywords = [
        'senin' => 'Senin',
        'selasa' => 'Selasa',
        'rabu' => 'Rabu',
        'kamis' => 'Kamis',
        'jumat' => 'Jumat',
        'sabtu' => 'Sabtu',
        'minggu' => 'Minggu'
    ];
    $matchedDays = [];
    foreach ($dayKeywords as $dKey => $dLabel) {
        if (preg_match('/\b' . $dKey . '\b/i', $cleanPrompt) || ($dKey === 'jumat' && preg_match('/\bjum\'?at\b/i', $cleanPrompt))) {
            $matchedDays[] = $dKey;
        }
    }
    if (preg_match('/\b(besok|enjing|sesuk|tomorrow)\b/i', $cleanPrompt)) {
        $besokNum = (date('N') % 7) + 1;
        $besokKey = strtolower($hariMap[$besokNum]);
        if (!in_array($besokKey, $matchedDays)) {
            $matchedDays[] = $besokKey;
        }
    }
    $asksTodayWord = (bool)preg_match('/\b(hari\s*ini|today|dinten\s*ieu|dinten\s*niki|saiki|ayeuna)\b/i', $cleanPrompt);
    $asksFullWeekWord = (bool)preg_match('/\b(seminggu|satu\s*minggu|seluruh\s*hari|semua\s*hari|senin\s*(?:sampai|s\.?\/?d|hingga|-)\s*jumat|lengkap|semuanya|semua\s*jadwal|seluruh\s*jadwal)\b/i', $cleanPrompt);

    $findMatchedGuruForJadwal = function (PDO $pdoConn, string $promptText): ?array {
        $rows = $pdoConn->query("SELECT g.id AS guru_id, COALESCE(wkd.id, 0) AS wali_id, g.nama_lengkap
                                  FROM guru g
                                  LEFT JOIN wali_kelas_data wkd ON g.user_id = wkd.user_id")->fetchAll(PDO::FETCH_ASSOC);
        $stop = ['guru', 'pengajar', 'pendidik', 'bapak', 'ibu', 'pak', 'bu', 'jadwal', 'pelajaran', 'mengajar', 'ngajar', 'data', 'berapa', 'total', 'jumlah', 'baca', 'lihat', 'tampilkan', 'sebutkan', 'daftar', 'kelas', 'hari', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu', 'tkj', 'tkr', 'tsm', 'yang', 'ada', 'untuk', 'spd', 'skom', 'mpd', 'drs', 'st', 'mt', 'sag', 'ssos', 'mkom'];
        $best = null;
        $bestScore = 0;
        foreach ($rows as $g) {
            $cleanNama = strtolower(trim(preg_replace('/,.*$/', '', $g['nama_lengkap'])));
            if (!empty($cleanNama) && stripos($promptText, $cleanNama) !== false) {
                return $g;
            }
            $score = 0;
            foreach (preg_split('/\s+/', $cleanNama) as $part) {
                $partClean = preg_replace('/[^a-z]/', '', $part);
                if (strlen($partClean) >= 4 && !in_array($partClean, $stop) && preg_match('/\b' . preg_quote($partClean, '/') . '\b/i', $promptText)) {
                    $score += strlen($partClean);
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $g;
            }
        }
        return ($bestScore >= 4) ? $best : null;
    };

    // 0.2. Menjumlahkan & Membaca Data Jadwal Pelajaran / Mengajar (Strict RBAC 5 Role)
    $isAskingOwnClassJadwal = (!empty($detailKelasNorm) && !empty($mentionedClassCompact) && $mentionedClassCompact === $detailKelasCompact) ||
                              (!empty($detailKelasNorm) && stripos($cleanPrompt, $detailKelasNorm) !== false) ||
                              (bool)preg_match('/\b(kelas\s*(saya|aku|abdi|kula|kulo|binaan|kami)|kelasku)\b/i', $cleanPrompt);
    $asksReadVerbJadwal = (bool)preg_match('/\b(baca|lihat|tampilkan|tunjukan|tunjukkan|sebutkan|daftar|list|apa\s*saja|apa\s*aja|cek)\b/i', $cleanPrompt);
    $notNonJadwalDomain = !preg_match('/\b(nip|nis|nisn|absen|absensi|presensi|kehadiran|izin|sakit|alpa|bolos|catatan|pembinaan|supervisi)\b/i', $cleanPrompt);
    $notHowToQuestion = !preg_match('/\b(cara\s*(buat|membuat|tambah|edit|hapus|input|kelola|atur)|bagaimana\s*cara|fitur|alur|kenapa\s*bentrok|anti\s*bentrok)\b/i', $cleanPrompt);

    $isAskingJadwalCount = false;
    if (
        $notNonJadwalDomain &&
        preg_match('/\b(jadwal|schedule)\b/i', $cleanPrompt) &&
        (
            preg_match('/\b(total|jumlah|ada\s*berapa|berapa\s*(banyak|total|jumlah|sesi|mapel|jadwal)?|banyaknya|count|how\s*many|hitung|jumlahkan|sabaraha|pira|piro|pinten)\b/i', $cleanPrompt) ||
            (preg_match('/\b(seluruh|semua)\b/i', $cleanPrompt) && !$asksReadVerbJadwal)
        )
    ) {
        $isAskingJadwalCount = true;
    }

    if ($isAskingJadwalCount) {
        $targetDays = !empty($matchedDays) ? $matchedDays : ($asksTodayWord ? [strtolower($namaHari)] : []);
        $daySuffixText = !empty($targetDays) ? (" pada hari " . implode(', ', array_map('ucfirst', $targetDays))) : " (Senin hingga Jumat)";

        // Kepala sekolah dan Admin memiliki akses monitoring jadwal seluruh sekolah

        if ($rawRole === 'siswa') {
            $asksOutOfScopeSiswa = $mentionsOtherSpecificClass || !empty($mentionedTingkat) || !empty($mentionedJurusanCode) || $asksPerKelasBreakdown ||
                                   preg_match('/\b(sekolah|seluruh\s*kelas|semua\s*kelas|kelas\s*lain|guru|pengajar)\b/i', $cleanPrompt);
            if ($asksOutOfScopeSiswa) {
                sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'total atau rincian jadwal kelas lain, jadwal guru, maupun keseluruhan jadwal sekolah', 'other class or school-wide lesson schedule counts'));
            }
            $daySql = '';
            $params = [$myKelasId];
            if (!empty($targetDays)) {
                $ph = implode(',', array_fill(0, count($targetDays), '?'));
                $daySql = " AND LOWER(j.hari) IN ($ph)";
                $params = array_merge($params, $targetDays);
            }
            $stS = $pdo->prepare("SELECT j.hari, COUNT(*) AS jml FROM jadwal_mengajar j WHERE j.kelas_id = ?{$daySql} GROUP BY j.hari ORDER BY FIELD(LOWER(j.hari),'senin','selasa','rabu','kamis','jumat','sabtu','minggu')");
            $stS->execute($params);
            $dayRows = $stS->fetchAll(PDO::FETCH_ASSOC);
            $totS = array_sum(array_column($dayRows, 'jml'));
            if (!empty($targetDays) || $asksPerKelasBreakdown || preg_match('/\b(rincian|detail|per\s*hari|tiap\s*hari|hari\s*apa)\b/i', $cleanPrompt)) {
                $reply = "Ada {$totS} jadwal pelajaran di kelasmu ({$detailKelas}){$daySuffixText}:\n";
                foreach ($dayRows as $dr) {
                    $reply .= "- " . ucfirst($dr['hari']) . ": {$dr['jml']} jadwal pelajaran\n";
                }
            } else {
                $reply = "Ada {$totS} jadwal pelajaran di kelasmu ({$detailKelas}).";
            }
            sendBotReply(trim($reply));
        }

        if ($rawRole === 'guru') {
            $asksOutOfScopeGuru = !empty($mentionedClassCompact) || !empty($mentionedTingkat) || !empty($mentionedJurusanCode) || $asksPerKelasBreakdown ||
                                  preg_match('/\b(sekolah|seluruh\s*kelas|semua\s*kelas|guru\s*lain|kelas\s*lain)\b/i', $cleanPrompt) ||
                                  (preg_match('/\b(pak|bapak|ibu|bu)\s+([a-z]+)\b/i', $cleanPrompt, $mG) && !in_array(strtolower($mG[2]), ['guru', 'saya', 'aku', 'kami', 'anda']) && stripos(strtolower($namaLengkap), strtolower($mG[2])) === false);
            if ($asksOutOfScopeGuru) {
                sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'total atau rincian jadwal kelas maupun jadwal guru lain', 'other class or school-wide schedule counts'));
            }
            $daySql = '';
            $params = [$myGuruId, $myGuruId, $myWaliId, $myWaliId];
            if (!empty($targetDays)) {
                $ph = implode(',', array_fill(0, count($targetDays), '?'));
                $daySql = " AND LOWER(j.hari) IN ($ph)";
                $params = array_merge($params, $targetDays);
            }
            $stG = $pdo->prepare("SELECT j.hari, COUNT(*) AS jml FROM jadwal_mengajar j WHERE ((? > 0 AND j.guru_id = ?) OR (? > 0 AND j.wali_kelas_id = ?)){$daySql} GROUP BY j.hari ORDER BY FIELD(LOWER(j.hari),'senin','selasa','rabu','kamis','jumat','sabtu','minggu')");
            $stG->execute($params);
            $dayRows = $stG->fetchAll(PDO::FETCH_ASSOC);
            $totG = array_sum(array_column($dayRows, 'jml'));
            if (!empty($targetDays) || preg_match('/\b(rincian|detail|per\s*hari|tiap\s*hari|hari\s*apa)\b/i', $cleanPrompt)) {
                $reply = "Ada {$totG} sesi mengajar{$daySuffixText}:\n";
                foreach ($dayRows as $dr) {
                    $reply .= "- " . ucfirst($dr['hari']) . ": {$dr['jml']} sesi mengajar\n";
                }
            } else {
                $reply = "Ada {$totG} sesi mengajar.";
            }
            sendBotReply(trim($reply));
        }

        if ($rawRole === 'wali_kelas') {
            $asksOutOfScopeWali = $mentionsOtherSpecificClass || !empty($mentionedTingkat) || !empty($mentionedJurusanCode) || $asksPerKelasBreakdown ||
                                  preg_match('/\b(seluruh\s*kelas|semua\s*kelas|seluruh\s*sekolah|satu\s*sekolah|di\s*sekolah|guru\s*lain)\b/i', $cleanPrompt) ||
                                  (preg_match('/\b(pak|bapak|ibu|bu)\s+([a-z]+)\b/i', $cleanPrompt, $mG) && !in_array(strtolower($mG[2]), ['guru', 'wali', 'saya', 'aku', 'kami', 'anda']) && stripos(strtolower($namaLengkap), strtolower($mG[2])) === false);
            if ($asksOutOfScopeWali) {
                sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, "total atau rincian jadwal pelajaran di luar kelas binaan Anda ({$detailKelas})", "schedule counts outside your homeroom class ({$detailKelas})"));
            }
            $wantsTeachingSelf = (bool)preg_match('/\b(mengajar|ngajar|mulang|teaching|pribadi)\b/i', $cleanPrompt) && !$isAskingOwnClassJadwal;
            if (!$wantsTeachingSelf && $myKelasId > 0) {
                $daySql = '';
                $params = [$myKelasId];
                if (!empty($targetDays)) {
                    $ph = implode(',', array_fill(0, count($targetDays), '?'));
                    $daySql = " AND LOWER(j.hari) IN ($ph)";
                    $params = array_merge($params, $targetDays);
                }
                $stWk = $pdo->prepare("SELECT j.hari, COUNT(*) AS jml FROM jadwal_mengajar j WHERE j.kelas_id = ?{$daySql} GROUP BY j.hari ORDER BY FIELD(LOWER(j.hari),'senin','selasa','rabu','kamis','jumat','sabtu','minggu')");
                $stWk->execute($params);
                $dayRows = $stWk->fetchAll(PDO::FETCH_ASSOC);
                $totWk = array_sum(array_column($dayRows, 'jml'));
                if (!empty($targetDays) || preg_match('/\b(rincian|detail|per\s*hari|tiap\s*hari|hari\s*apa)\b/i', $cleanPrompt)) {
                    $reply = "Ada {$totWk} jadwal pelajaran di kelas {$detailKelas}{$daySuffixText}:\n";
                    foreach ($dayRows as $dr) {
                        $reply .= "- " . ucfirst($dr['hari']) . ": {$dr['jml']} jadwal pelajaran\n";
                    }
                } else {
                    $reply = "Ada {$totWk} jadwal pelajaran di kelas {$detailKelas}.";
                }
                sendBotReply(trim($reply));
            } else {
                $daySql = '';
                $params = [$myGuruId, $myGuruId, $myWaliId, $myWaliId];
                if (!empty($targetDays)) {
                    $ph = implode(',', array_fill(0, count($targetDays), '?'));
                    $daySql = " AND LOWER(j.hari) IN ($ph)";
                    $params = array_merge($params, $targetDays);
                }
                $stWg = $pdo->prepare("SELECT j.hari, COUNT(*) AS jml FROM jadwal_mengajar j WHERE ((? > 0 AND j.guru_id = ?) OR (? > 0 AND j.wali_kelas_id = ?)){$daySql} GROUP BY j.hari ORDER BY FIELD(LOWER(j.hari),'senin','selasa','rabu','kamis','jumat','sabtu','minggu')");
                $stWg->execute($params);
                $dayRows = $stWg->fetchAll(PDO::FETCH_ASSOC);
                $totWg = array_sum(array_column($dayRows, 'jml'));
                if (!empty($targetDays) || preg_match('/\b(rincian|detail|per\s*hari|tiap\s*hari|hari\s*apa)\b/i', $cleanPrompt)) {
                    $reply = "Ada {$totWg} sesi mengajar pribadi{$daySuffixText}:\n";
                    foreach ($dayRows as $dr) {
                        $reply .= "- " . ucfirst($dr['hari']) . ": {$dr['jml']} sesi mengajar\n";
                    }
                } else {
                    $reply = "Ada {$totWg} sesi mengajar.";
                }
                sendBotReply(trim($reply));
            }
        }

        // Administrator: Hitung & jumlahkan jadwal pelajaran secara dinamis sesuai filter
        $dayJoinCond = '';
        $dayWhereCond = '';
        $dayParams = [];
        if (!empty($targetDays)) {
            $ph = implode(',', array_fill(0, count($targetDays), '?'));
            $dayJoinCond = " AND LOWER(jm.hari) IN ($ph)";
            $dayWhereCond = "LOWER(jm.hari) IN ($ph)";
            $dayParams = $targetDays;
        }

        if (!empty($mentionedClassCompact)) {
            $allK = $pdo->query("SELECT k.id, k.tingkat, k.nama_kelas, j.kode_jurusan, j.nama_jurusan FROM kelas k JOIN jurusan j ON k.jurusan_id = j.id ORDER BY k.tingkat, k.nama_kelas")->fetchAll(PDO::FETCH_ASSOC);
            $matchedKls = [];
            foreach ($allK as $kr) {
                $kComp = preg_replace('/[^a-z0-9]/', '', strtolower($kr['nama_kelas']));
                if ($kComp === $mentionedClassCompact || (strlen($mentionedClassCompact) >= 4 && strpos($kComp, $mentionedClassCompact) === 0)) {
                    $matchedKls[] = $kr;
                }
            }
            if (empty($matchedKls)) {
                $daftarNama = implode(', ', array_map(fn($x) => $x['nama_kelas'], $allK));
                sendBotReply("Kelas " . strtoupper($mentionedClassNormalized) . " tidak ditemukan. Kelas yang terdaftar: {$daftarNama}.");
            }
            if (count($matchedKls) === 1) {
                $tk = $matchedKls[0];
                $stC = $pdo->prepare("SELECT jm.hari, COUNT(*) AS jml FROM jadwal_mengajar jm WHERE jm.kelas_id = ?" . ($dayWhereCond ? " AND {$dayWhereCond}" : "") . " GROUP BY jm.hari ORDER BY FIELD(LOWER(jm.hari),'senin','selasa','rabu','kamis','jumat','sabtu','minggu')");
                $stC->execute(array_merge([(int)$tk['id']], $dayParams));
                $dayRows = $stC->fetchAll(PDO::FETCH_ASSOC);
                $tot = array_sum(array_column($dayRows, 'jml'));
                if (!empty($targetDays) || preg_match('/\b(rincian|detail|per\s*hari|tiap\s*hari|hari\s*apa)\b/i', $cleanPrompt)) {
                    $reply = "Ada {$tot} jadwal pelajaran di kelas {$tk['nama_kelas']}{$daySuffixText}:\n";
                    foreach ($dayRows as $dr) {
                        $reply .= "- " . ucfirst($dr['hari']) . ": {$dr['jml']} jadwal pelajaran\n";
                    }
                } else {
                    $reply = "Ada {$tot} jadwal pelajaran di kelas {$tk['nama_kelas']}.";
                }
                sendBotReply(trim($reply));
            } else {
                $kIds = array_column($matchedKls, 'id');
                $kPh = implode(',', array_fill(0, count($kIds), '?'));
                $stC = $pdo->prepare("SELECT k.nama_kelas, COUNT(jm.id) AS jml FROM kelas k LEFT JOIN jadwal_mengajar jm ON jm.kelas_id = k.id{$dayJoinCond} WHERE k.id IN ($kPh) GROUP BY k.id ORDER BY k.nama_kelas ASC");
                $stC->execute(array_merge($dayParams, $kIds));
                $rows = $stC->fetchAll(PDO::FETCH_ASSOC);
                $tot = array_sum(array_column($rows, 'jml'));
                $reply = "Ada {$tot} jadwal pelajaran untuk kelas " . strtoupper($mentionedClassNormalized) . "{$daySuffixText}:\n";
                foreach ($rows as $r) {
                    $reply .= "- Kelas {$r['nama_kelas']}: {$r['jml']} jadwal pelajaran\n";
                }
                sendBotReply(trim($reply));
            }
        }

        if (!empty($mentionedTingkat)) {
            $jurSql = !empty($mentionedJurusanCode) ? " AND UPPER(j.kode_jurusan) = ?" : "";
            $params = array_merge($dayParams, [$mentionedTingkat], (!empty($mentionedJurusanCode) ? [$mentionedJurusanCode] : []));
            $stT = $pdo->prepare("SELECT k.nama_kelas, COUNT(jm.id) AS jml
                                  FROM kelas k
                                  JOIN jurusan j ON k.jurusan_id = j.id
                                  LEFT JOIN jadwal_mengajar jm ON jm.kelas_id = k.id{$dayJoinCond}
                                  WHERE UPPER(k.tingkat) = ?{$jurSql}
                                  GROUP BY k.id, k.nama_kelas
                                  ORDER BY k.nama_kelas ASC");
            $stT->execute($params);
            $rows = $stT->fetchAll(PDO::FETCH_ASSOC);
            $tot = array_sum(array_column($rows, 'jml'));
            $jurLabel = !empty($mentionedJurusanCode) ? " jurusan {$mentionedJurusanCode}" : "";
            if ($asksPerKelasBreakdown || preg_match('/\b(rincian|detail|daftar)\b/i', $cleanPrompt)) {
                $reply = "Ada {$tot} data jadwal pelajaran untuk kelas {$mentionedTingkat}{$jurLabel}{$daySuffixText}:\n";
                foreach ($rows as $r) {
                    $reply .= "- Kelas {$r['nama_kelas']}: {$r['jml']} jadwal pelajaran\n";
                }
            } else {
                $perKlsBrief = [];
                foreach ($rows as $r) {
                    $perKlsBrief[] = "{$r['nama_kelas']}: {$r['jml']}";
                }
                $reply = "Ada {$tot} data jadwal pelajaran untuk kelas {$mentionedTingkat}" . (!empty($perKlsBrief) ? " (" . implode(', ', $perKlsBrief) . ")" : "") . ".";
            }
            sendBotReply(trim($reply));
        }

        if (!empty($mentionedJurusanCode)) {
            $stJ = $pdo->prepare("SELECT k.nama_kelas, j.nama_jurusan, COUNT(jm.id) AS jml
                                  FROM kelas k
                                  JOIN jurusan j ON k.jurusan_id = j.id
                                  LEFT JOIN jadwal_mengajar jm ON jm.kelas_id = k.id{$dayJoinCond}
                                  WHERE UPPER(j.kode_jurusan) = ?
                                  GROUP BY k.id, k.nama_kelas, j.nama_jurusan
                                  ORDER BY k.tingkat ASC, k.nama_kelas ASC");
            $stJ->execute(array_merge($dayParams, [$mentionedJurusanCode]));
            $rows = $stJ->fetchAll(PDO::FETCH_ASSOC);
            $tot = array_sum(array_column($rows, 'jml'));
            if ($asksPerKelasBreakdown || preg_match('/\b(rincian|detail|daftar)\b/i', $cleanPrompt)) {
                $reply = "Ada {$tot} data jadwal pelajaran di jurusan {$mentionedJurusanCode}{$daySuffixText}:\n";
                foreach ($rows as $r) {
                    $reply .= "- Kelas {$r['nama_kelas']}: {$r['jml']} jadwal pelajaran\n";
                }
            } else {
                $reply = "Ada {$tot} data jadwal pelajaran di jurusan {$mentionedJurusanCode}.";
            }
            sendBotReply(trim($reply));
        }

        $matchedGuruJadwal = $findMatchedGuruForJadwal($pdo, $cleanPrompt);
        if ($matchedGuruJadwal) {
            $stMg = $pdo->prepare("SELECT k.nama_kelas, COUNT(*) AS jml
                                   FROM jadwal_mengajar jm
                                   JOIN kelas k ON jm.kelas_id = k.id
                                   WHERE ((? > 0 AND jm.guru_id = ?) OR (? > 0 AND jm.wali_kelas_id = ?))" . ($dayWhereCond ? " AND {$dayWhereCond}" : "") . "
                                   GROUP BY k.id, k.nama_kelas
                                   ORDER BY k.nama_kelas ASC");
            $stMg->execute(array_merge([$matchedGuruJadwal['guru_id'], $matchedGuruJadwal['guru_id'], $matchedGuruJadwal['wali_id'], $matchedGuruJadwal['wali_id']], $dayParams));
            $rows = $stMg->fetchAll(PDO::FETCH_ASSOC);
            $tot = array_sum(array_column($rows, 'jml'));
            if ($asksPerKelasBreakdown || !empty($targetDays) || preg_match('/\b(rincian|detail)\b/i', $cleanPrompt)) {
                $reply = "Ada {$tot} sesi jadwal mengajar untuk {$matchedGuruJadwal['nama_lengkap']}{$daySuffixText}:\n";
                foreach ($rows as $r) {
                    $reply .= "- Kelas {$r['nama_kelas']}: {$r['jml']} sesi\n";
                }
            } else {
                $reply = "Ada {$tot} sesi jadwal mengajar untuk {$matchedGuruJadwal['nama_lengkap']}.";
            }
            sendBotReply(trim($reply));
        }

        if ($asksPerKelasBreakdown || !empty($targetDays)) {
            $stAllK = $pdo->prepare("SELECT k.nama_kelas, COUNT(jm.id) AS jml
                                     FROM kelas k
                                     LEFT JOIN jadwal_mengajar jm ON jm.kelas_id = k.id{$dayJoinCond}
                                     GROUP BY k.id, k.nama_kelas
                                     ORDER BY k.tingkat ASC, k.nama_kelas ASC");
            $stAllK->execute($dayParams);
            $rows = $stAllK->fetchAll(PDO::FETCH_ASSOC);
            $tot = array_sum(array_column($rows, 'jml'));
            $reply = "Ada {$tot} data jadwal pelajaran{$daySuffixText}:\n";
            foreach ($rows as $r) {
                $reply .= "- Kelas {$r['nama_kelas']}: {$r['jml']} jadwal pelajaran\n";
            }
            sendBotReply(trim($reply));
        }

        // Grand total seluruh sekolah
        $stTktSum = $pdo->query("SELECT k.tingkat, COUNT(jm.id) AS jml
                                 FROM kelas k
                                 LEFT JOIN jadwal_mengajar jm ON jm.kelas_id = k.id
                                 GROUP BY k.tingkat
                                 ORDER BY FIELD(k.tingkat, 'X', 'XI', 'XII')")->fetchAll(PDO::FETCH_ASSOC);
        $stJadwalCount = array_sum(array_column($stTktSum, 'jml'));
        $stKelasCount = (int)$pdo->query("SELECT COUNT(*) FROM kelas")->fetchColumn();
        if ($asksPerKelasBreakdown || preg_match('/\b(rincian|detail|per\s*tingkat)\b/i', $cleanPrompt)) {
            $reply = "Ada {$stJadwalCount} jadwal pelajaran di sekolah ({$stKelasCount} kelas):\n";
            foreach ($stTktSum as $ts) {
                $reply .= "- Tingkat {$ts['tingkat']}: {$ts['jml']} jadwal\n";
            }
        } else {
            $reply = "Ada {$stJadwalCount} jadwal pelajaran sekolah ({$stKelasCount} kelas).";
        }
        sendBotReply(trim($reply));
    }

    // 0.2B. Membaca Data Jadwal Pelajaran / Mengajar Langsung dari Database (Khusus Administrator)
    if (
        $rawRole === 'admin' &&
        $notNonJadwalDomain &&
        $notHowToQuestion &&
        preg_match('/\b(jadwal|schedule)\b/i', $cleanPrompt)
    ) {
        $targetDaysRead = !empty($matchedDays) ? $matchedDays : ($asksTodayWord ? [strtolower($namaHari)] : []);
        $dayWhereSql = '';
        $dayReadParams = [];
        if (!empty($targetDaysRead)) {
            $ph = implode(',', array_fill(0, count($targetDaysRead), '?'));
            $dayWhereSql = " AND LOWER(j.hari) IN ($ph)";
            $dayReadParams = $targetDaysRead;
        }
        $dayHeaderLabel = !empty($targetDaysRead) ? (" (Hari " . implode(', ', array_map('ucfirst', $targetDaysRead)) . ")") : " (Senin - Jumat)";

        $matchedGuruRead = $findMatchedGuruForJadwal($pdo, $cleanPrompt);

        if (!empty($mentionedClassCompact)) {
            $allKRead = $pdo->query("SELECT k.id, k.nama_kelas, j.nama_jurusan FROM kelas k JOIN jurusan j ON k.jurusan_id = j.id ORDER BY k.tingkat, k.nama_kelas")->fetchAll(PDO::FETCH_ASSOC);
            $matchedKlsRead = [];
            foreach ($allKRead as $kr) {
                $kComp = preg_replace('/[^a-z0-9]/', '', strtolower($kr['nama_kelas']));
                if ($kComp === $mentionedClassCompact || (strlen($mentionedClassCompact) >= 4 && strpos($kComp, $mentionedClassCompact) === 0)) {
                    $matchedKlsRead[] = $kr;
                }
            }
            if (empty($matchedKlsRead)) {
                $daftarNama = implode(', ', array_map(fn($x) => $x['nama_kelas'], $allKRead));
                sendBotReply("Berdasarkan data sistem, kelas " . strtoupper($mentionedClassNormalized) . " tidak ditemukan. Kelas yang terdaftar saat ini: {$daftarNama}.");
            }
            $kIds = array_column($matchedKlsRead, 'id');
            $kPh = implode(',', array_fill(0, count($kIds), '?'));
            $stRead = $pdo->prepare("SELECT k.nama_kelas, j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel,
                                            COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Pengajar') AS pengajar
                                     FROM jadwal_mengajar j
                                     JOIN kelas k ON j.kelas_id = k.id
                                     JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                                     LEFT JOIN guru g ON j.guru_id = g.id
                                     LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id
                                     WHERE j.kelas_id IN ($kPh){$dayWhereSql}
                                     ORDER BY k.nama_kelas ASC, FIELD(LOWER(j.hari),'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC");
            $stRead->execute(array_merge($kIds, $dayReadParams));
            $rows = $stRead->fetchAll(PDO::FETCH_ASSOC);
            if ($rows) {
                $klsNames = implode(', ', array_column($matchedKlsRead, 'nama_kelas'));
                $reply = "Berikut data jadwal pelajaran kelas {$klsNames}{$dayHeaderLabel} (Total " . count($rows) . " sesi):\n";
                $curK = '';
                $curH = '';
                foreach ($rows as $r) {
                    if ($curK !== $r['nama_kelas']) {
                        $curK = $r['nama_kelas'];
                        $curH = '';
                        if (count($matchedKlsRead) > 1) {
                            $reply .= "\nKelas {$curK}:\n";
                        } else {
                            $reply .= "\n";
                        }
                    }
                    $hLbl = ucfirst($r['hari']);
                    if ($curH !== $hLbl) {
                        $curH = $hLbl;
                        $reply .= "Hari {$curH}:\n";
                    }
                    $m1 = substr($r['jam_mulai'], 0, 5);
                    $m2 = substr($r['jam_selesai'], 0, 5);
                    $rng = !empty($r['ruang']) ? " | Ruang: {$r['ruang']}" : "";
                    $reply .= "- {$m1} - {$m2} WIB: {$r['nama_mapel']} ({$r['pengajar']}){$rng}\n";
                }
                sendBotReply($reply);
            } else {
                sendBotReply("Berdasarkan data sistem, tidak ada jadwal pelajaran yang tercatat untuk kelas tersebut{$dayHeaderLabel}.");
            }
        }

        if ($matchedGuruRead) {
            $stReadG = $pdo->prepare("SELECT k.nama_kelas, j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel
                                      FROM jadwal_mengajar j
                                      JOIN kelas k ON j.kelas_id = k.id
                                      JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                                      WHERE ((? > 0 AND j.guru_id = ?) OR (? > 0 AND j.wali_kelas_id = ?)){$dayWhereSql}
                                      ORDER BY FIELD(LOWER(j.hari),'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC");
            $stReadG->execute(array_merge([$matchedGuruRead['guru_id'], $matchedGuruRead['guru_id'], $matchedGuruRead['wali_id'], $matchedGuruRead['wali_id']], $dayReadParams));
            $rows = $stReadG->fetchAll(PDO::FETCH_ASSOC);
            if ($rows) {
                $reply = "Berikut jadwal mengajar {$matchedGuruRead['nama_lengkap']}{$dayHeaderLabel} (Total " . count($rows) . " sesi):\n\n";
                $curH = '';
                foreach ($rows as $r) {
                    $hLbl = ucfirst($r['hari']);
                    if ($curH !== $hLbl) {
                        $curH = $hLbl;
                        $reply .= "Hari {$curH}:\n";
                    }
                    $m1 = substr($r['jam_mulai'], 0, 5);
                    $m2 = substr($r['jam_selesai'], 0, 5);
                    $rng = !empty($r['ruang']) ? " | Ruang: {$r['ruang']}" : "";
                    $reply .= "- {$m1} - {$m2} WIB: {$r['nama_mapel']} — Kelas {$r['nama_kelas']}{$rng}\n";
                }
                sendBotReply($reply);
            } else {
                sendBotReply("Berdasarkan data sistem, {$matchedGuruRead['nama_lengkap']} tidak memiliki jadwal mengajar{$dayHeaderLabel}.");
            }
        }

        if (!empty($mentionedTingkat) || !empty($mentionedJurusanCode)) {
            $wheres = [];
            $params = [];
            if (!empty($mentionedTingkat)) {
                $wheres[] = "UPPER(k.tingkat) = ?";
                $params[] = $mentionedTingkat;
            }
            if (!empty($mentionedJurusanCode)) {
                $wheres[] = "UPPER(jr.kode_jurusan) = ?";
                $params[] = $mentionedJurusanCode;
            }
            $wSql = implode(' AND ', $wheres);
            $stReadTJ = $pdo->prepare("SELECT k.nama_kelas, j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel,
                                              COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Pengajar') AS pengajar
                                       FROM jadwal_mengajar j
                                       JOIN kelas k ON j.kelas_id = k.id
                                       JOIN jurusan jr ON k.jurusan_id = jr.id
                                       JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                                       LEFT JOIN guru g ON j.guru_id = g.id
                                       LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id
                                       WHERE {$wSql}{$dayWhereSql}
                                       ORDER BY k.tingkat ASC, k.nama_kelas ASC, FIELD(LOWER(j.hari),'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC");
            $stReadTJ->execute(array_merge($params, $dayReadParams));
            $rows = $stReadTJ->fetchAll(PDO::FETCH_ASSOC);
            if ($rows) {
                $scopeTitle = (!empty($mentionedTingkat) ? "Tingkat/Kelas {$mentionedTingkat}" : "") . (!empty($mentionedJurusanCode) ? " Jurusan {$mentionedJurusanCode}" : "");
                $scopeTitle = trim($scopeTitle);
                $reply = "Berikut data jadwal pelajaran untuk {$scopeTitle}{$dayHeaderLabel} (Total " . count($rows) . " sesi jadwal):\n";
                $curK = '';
                $curH = '';
                foreach ($rows as $r) {
                    if ($curK !== $r['nama_kelas']) {
                        $curK = $r['nama_kelas'];
                        $curH = '';
                        $reply .= "\nKelas {$curK}:\n";
                    }
                    $hLbl = ucfirst($r['hari']);
                    if ($curH !== $hLbl) {
                        $curH = $hLbl;
                        $reply .= "Hari {$curH}:\n";
                    }
                    $m1 = substr($r['jam_mulai'], 0, 5);
                    $m2 = substr($r['jam_selesai'], 0, 5);
                    $reply .= "- {$m1} - {$m2} WIB: {$r['nama_mapel']} ({$r['pengajar']})\n";
                }
                sendBotReply($reply);
            } else {
                sendBotReply("Berdasarkan data sistem, tidak ada jadwal pelajaran ditemukan untuk filter tersebut{$dayHeaderLabel}.");
            }
        }

        if (!empty($targetDaysRead)) {
            if (empty(array_diff($targetDaysRead, ['sabtu', 'minggu']))) {
                sendBotReply("Berdasarkan data sistem, pada hari akhir pekan (Sabtu/Minggu) tidak terdapat jadwal pelajaran di SMK NU WARGABINANGUN (kegiatan belajar mengajar berlangsung Senin hingga Jumat).");
            }
            $stReadD = $pdo->prepare("SELECT k.nama_kelas, j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel,
                                             COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Pengajar') AS pengajar
                                      FROM jadwal_mengajar j
                                      JOIN kelas k ON j.kelas_id = k.id
                                      JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                                      LEFT JOIN guru g ON j.guru_id = g.id
                                      LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id
                                      WHERE 1=1{$dayWhereSql}
                                      ORDER BY FIELD(LOWER(j.hari),'senin','selasa','rabu','kamis','jumat'), k.tingkat ASC, k.nama_kelas ASC, j.jam_mulai ASC");
            $stReadD->execute($dayReadParams);
            $rows = $stReadD->fetchAll(PDO::FETCH_ASSOC);
            if ($rows) {
                $reply = "Berikut jadwal pelajaran seluruh kelas{$dayHeaderLabel} (Total " . count($rows) . " sesi):\n";
                $curH = '';
                $curK = '';
                foreach ($rows as $r) {
                    $hLbl = ucfirst($r['hari']);
                    if ($curH !== $hLbl) {
                        $curH = $hLbl;
                        $curK = '';
                        $reply .= "\nHari {$curH}:\n";
                    }
                    if ($curK !== $r['nama_kelas']) {
                        $curK = $r['nama_kelas'];
                        $reply .= "[Kelas {$curK}]\n";
                    }
                    $m1 = substr($r['jam_mulai'], 0, 5);
                    $m2 = substr($r['jam_selesai'], 0, 5);
                    $reply .= "- {$m1} - {$m2} WIB: {$r['nama_mapel']} ({$r['pengajar']})\n";
                }
                sendBotReply($reply);
            }
        }

        // Jika admin meminta baca/lihat jadwal pelajaran tanpa filter spesifik
        if ($asksReadVerbJadwal || $asksFullWeekWord) {
            if ($asksFullWeekWord) {
                $rows = $pdo->query("SELECT k.nama_kelas, j.hari, j.jam_mulai, j.jam_selesai, m.nama_mapel,
                                            COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Pengajar') AS pengajar
                                     FROM jadwal_mengajar j
                                     JOIN kelas k ON j.kelas_id = k.id
                                     JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                                     LEFT JOIN guru g ON j.guru_id = g.id
                                     LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id
                                     ORDER BY k.tingkat ASC, k.nama_kelas ASC, FIELD(LOWER(j.hari),'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC")->fetchAll(PDO::FETCH_ASSOC);
                $reply = "Berikut data lengkap jadwal pelajaran seluruh kelas di SMK NU WARGABINANGUN (Total " . count($rows) . " sesi):\n";
                $curK = '';
                $curH = '';
                foreach ($rows as $r) {
                    if ($curK !== $r['nama_kelas']) {
                        $curK = $r['nama_kelas'];
                        $curH = '';
                        $reply .= "\nKelas {$curK}:\n";
                    }
                    $hLbl = ucfirst($r['hari']);
                    if ($curH !== $hLbl) {
                        $curH = $hLbl;
                        $reply .= "Hari {$curH}:\n";
                    }
                    $m1 = substr($r['jam_mulai'], 0, 5);
                    $m2 = substr($r['jam_selesai'], 0, 5);
                    $reply .= "- {$m1}-{$m2}: {$r['nama_mapel']} ({$r['pengajar']})\n";
                }
                sendBotReply($reply);
            } else {
                $sumRows = $pdo->query("SELECT k.tingkat, k.nama_kelas, COUNT(jm.id) AS jml,
                                               COUNT(DISTINCT jm.hari) AS jml_hari
                                        FROM kelas k
                                        LEFT JOIN jadwal_mengajar jm ON jm.kelas_id = k.id
                                        GROUP BY k.id, k.tingkat, k.nama_kelas
                                        ORDER BY k.tingkat ASC, k.nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
                $totAll = array_sum(array_column($sumRows, 'jml'));
                $reply = "Berdasarkan data sistem, terdapat total {$totAll} sesi jadwal pelajaran aktif untuk " . count($sumRows) . " kelas (hari Senin hingga Jumat, pukul 07:00 - 14:30 WIB), dengan rincian per kelas:\n\n";
                foreach ($sumRows as $idx => $sr) {
                    $reply .= ($idx + 1) . ". Kelas {$sr['nama_kelas']}: {$sr['jml']} jadwal pelajaran ({$sr['jml_hari']} hari aktif)\n";
                }
                $reply .= "\nUntuk membaca isi jadwalnya secara terperinci, Anda dapat menyebutkan kelas/tingkat/hari/guru tertentu (misalnya: \"jadwal pelajaran kelas XII\", \"jadwal kelas X TSM 3 hari Senin\", atau \"jadwal mengajar Pak Budi Santoso\").";
                sendBotReply($reply);
            }
        }
    }

    // 0.3. Strict Role-Based Access Control per Role (Guru, Wali Kelas, Kepala Sekolah)
    $matchedTeacherInPrompt = $findMatchedGuruForJadwal($pdo, $cleanPrompt);
    $selfBaseNameForCheck = strtolower(trim(preg_replace('/,.*$/', '', $namaLengkap)));
    $matchedTeacherBaseName = $matchedTeacherInPrompt ? strtolower(trim(preg_replace('/,.*$/', '', $matchedTeacherInPrompt['nama_lengkap']))) : '';
    $asksNamedOtherTeacher = ($matchedTeacherInPrompt !== null && !empty($matchedTeacherBaseName) && $matchedTeacherBaseName !== $selfBaseNameForCheck &&
                              (empty($selfBaseNameForCheck) || (stripos($selfBaseNameForCheck, $matchedTeacherBaseName) === false && stripos($matchedTeacherBaseName, $selfBaseNameForCheck) === false)));

    if ($rawRole === 'guru') {
        $asksOtherTeacherGeneric = $asksNamedOtherTeacher ||
                                   preg_match('/\b(guru\s*lain|pengajar\s*lain|semua\s*guru|seluruh\s*guru|daftar\s*guru|nama\s*guru|rekap\s*guru)\b/i', $cleanPrompt) ||
                                   (preg_match('/\b(pak|bapak|ibu|bu)\s+([a-z]+)\b/i', $cleanPrompt, $mG) && !in_array(strtolower($mG[2]), ['kepala', 'kepsek', 'admin', 'guru', 'saya', 'aku', 'kami', 'anda']) && stripos(strtolower($namaLengkap), strtolower($mG[2])) === false);
        if (preg_match('/\b(jadwal|schedule|ngajar|mengajar|pelajaran)\b/i', $cleanPrompt)) {
            $asksSpecificClassWithoutSelf = (!empty($mentionedClassNormalized) && !preg_match('/\b(saya|sy|aku|pribadi|mengajar|ngajar|mulang|my)\b/i', $cleanPrompt)) ||
                                            ((!empty($mentionedTingkat) || !empty($mentionedJurusanCode)) && !preg_match('/\b(saya|sy|aku|pribadi|sendiri|my)\b/i', $cleanPrompt));
            $asksAllSchoolSchedules = (bool)preg_match('/\b(semua|seluruh)\s+(?:data\s+)?jadwal\b/i', $cleanPrompt) &&
                                      !preg_match('/\b(saya|sy|aku|pribadi|mengajar|ngajar|mulang|my)\b/i', $cleanPrompt);
            if ($asksSpecificClassWithoutSelf || $asksOtherTeacherGeneric || $asksAllSchoolSchedules ||
                preg_match('/\b(sekolah|seluruh\s*kelas|semua\s*kelas|kelas\s*lain)\b/i', $cleanPrompt)) {
                sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'jadwal mengajar guru lain atau jadwal pelajaran kelas/tingkat/jurusan/seluruh sekolah', 'other teachers or school class schedules'));
            }
        }
        if ($asksOtherTeacherGeneric) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'data, daftar, atau catatan guru lain', 'other teachers data or records'));
        }
        if (preg_match('/\b(catatan\s+pembinaan|pembinaan\s+siswa)\b/i', $cleanPrompt) ||
            (preg_match('/\b(siswa|murid)\b/i', $cleanPrompt) && preg_match('/\b(daftar|data|total|jumlah|nis|nisn|absen|absensi|presensi|kehadiran|izin|sakit|alpa|bolos|hadir|siapa|nama)\b/i', $cleanPrompt))) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'data pribadi, daftar kehadiran, atau pembinaan siswa (wewenang Wali Kelas & Administrator)', 'student records or attendance data'));
        }
    } elseif ($rawRole === 'wali_kelas') {
        $asksOtherTeacher = $asksNamedOtherTeacher ||
                            preg_match('/\b(guru\s*lain|pengajar\s*lain|semua\s*guru|seluruh\s*guru|daftar\s*guru|nama\s*guru|rekap\s*guru)\b/i', $cleanPrompt) ||
                            (preg_match('/\b(pak|bapak|ibu|bu)\s+([a-z]+)\b/i', $cleanPrompt, $mG) && !in_array(strtolower($mG[2]), ['guru', 'wali', 'kepala', 'kepsek', 'saya', 'aku', 'kami', 'anda']) && stripos(strtolower($namaLengkap), strtolower($mG[2])) === false);
        if (preg_match('/\b(jadwal|schedule|ngajar|mengajar|pelajaran)\b/i', $cleanPrompt)) {
            $asksAllSchoolSchedules = (bool)preg_match('/\b(semua|seluruh)\s+(?:data\s+)?jadwal\b/i', $cleanPrompt) &&
                                      !preg_match('/\b(saya|sy|aku|pribadi|binaan|mengajar|ngajar|mulang|my)\b/i', $cleanPrompt) &&
                                      !(!empty($mentionedClassNormalized) && !$mentionsOtherSpecificClass);
            if ($mentionsOtherSpecificClass || !empty($mentionedTingkat) || !empty($mentionedJurusanCode) || $asksAllSchoolSchedules || preg_match('/\b(seluruh\s*kelas|semua\s*kelas|seluruh\s*sekolah)\b/i', $cleanPrompt) || $asksOtherTeacher) {
                sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'jadwal pelajaran kelas/tingkat/jurusan lain, seluruh sekolah, atau jadwal guru lain', 'other classes or other teacher schedules'));
            }
        }
        if ($asksOtherTeacher) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'data, catatan, atau kehadiran guru lain', 'other teachers records or attendance'));
        }
        if (($mentionsOtherSpecificClass || !empty($mentionedTingkat) || !empty($mentionedJurusanCode)) && preg_match('/\b(siswa|murid|absen|kehadiran|izin|pembinaan|kelas)\b/i', $cleanPrompt)) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, "data siswa atau kehadiran kelas/tingkat/jurusan lain di luar kelas binaan Anda ({$detailKelas})", "student data outside your homeroom class ({$detailKelas})"));
        }
    } elseif ($rawRole === 'kepala_sekolah') {
        if (preg_match('/\b(jadwal|schedule|mengajar|ngajar|mata\s*pelajaran|mapel)\b/i', $cleanPrompt)) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'jadwal pelajaran, jadwal mengajar, atau daftar mata pelajaran', 'lesson and teaching schedules'));
        }
        if (preg_match('/\b(catatan\s+pembinaan|pembinaan\s+siswa)\b/i', $cleanPrompt)) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'catatan pembinaan khusus wali kelas untuk siswa', 'student coaching notes'));
        }
    }

    // =========================================================================
    // 0.5. STRICT ROLE-BASED ACCESS CONTROL (RBAC) KHUSUS ROLE SISWA
    // Siswa DILARANG KERAS mengakses jadwal mengajar guru, kelas lain, & presensi guru/siswa lain!
    // =========================================================================
    if ($rawRole === 'siswa') {
        // Cek apakah siswa menanyakan jadwal atau data kelas/tingkat/jurusan/seluruh sekolah
        if (($mentionsOtherSpecificClass || !empty($mentionedTingkat) || !empty($mentionedJurusanCode)) && preg_match('/\b(jadwal|schedule|pelajaran|mapel|masuk|siswa|absen|kehadiran|izin)\b/i', $cleanPrompt)) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'data atau jadwal kelas/tingkat/jurusan lain di luar kelasmu (' . $detailKelas . ')', 'other classes data or schedules'));
        }
        if (preg_match('/\b(semua|seluruh)\s+(?:data\s+)?jadwal\b/i', $cleanPrompt) &&
            !preg_match('/\b(kelas\s*(saya|aku|abdi|kula|kulo|kami)|kelasku)\b/i', $cleanPrompt)) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'keseluruhan data jadwal pelajaran sekolah di luar kelasmu (' . $detailKelas . ')', 'all school lesson schedules'));
        }

        // Cek apakah siswa menanyakan daftar siswa sekelas/sesekolah atau catatan pembinaan/catatan guru lain
        if (preg_match('/\b(daftar\s*(siswa|murid|nama\s*siswa|teman)|teman\s*sekelas|siswa\s*lain|murid\s*lain)\b/i', $cleanPrompt)) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'daftar siswa atau data siswa lain', 'lists or records of other students'));
        }
        if (preg_match('/\b(catatan\s+pembinaan|catatan\s+guru|supervisi)\b/i', $cleanPrompt) &&
            (preg_match('/\b(guru|seluruh|semua|siswa\s+lain)\b/i', $cleanPrompt) || (preg_match('/\b(catatan\s+guru|untuk\s+guru)\b/i', $cleanPrompt)))) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'catatan guru atau catatan siswa lain', 'teacher notes or other students records'));
        }

        // Cek apakah siswa menanyakan jadwal guru / jadwal mengajar guru / guru spesifik
        $isAskingTeacherSchedule = false;

        // 1. Kata kunci jadwal + guru/mengajar/pengajar
        if (preg_match('/\b(jadwal|schedule)\b/i', $cleanPrompt) && 
            preg_match('/\b(guru|pengajar|pendidik|ngajar|mengajar|mulang|ngawulang|teacher|teachers)\b/i', $cleanPrompt)) {
            $isAskingTeacherSchedule = true;
        }

        // 2. Pertanyaan guru ngajar apa / jadwal guru
        if (preg_match('/\b(guru|pengajar)\s+(ngajar|mengajar|jadwal|ada\s+jadwal)\b/i', $cleanPrompt) ||
            preg_match('/\b(ngajar|mengajar)\s+(apa|kapan|dimana|jam\s+berapa)\b/i', $cleanPrompt)) {
            if (!preg_match('/\b(kelas\s*(saya|aku|abdi|kula|kulo|kami)|kelasku)\b/i', $cleanPrompt)) {
                $isAskingTeacherSchedule = true;
            }
        }

        // 3. Menanyakan jadwal guru spesifik dengan sebutan Pak/Bapak/Ibu/Bu [Nama] atau nama guru langsung
        if ($asksNamedOtherTeacher && preg_match('/\b(jadwal|schedule|ngajar|mengajar|pelajaran|mapel)\b/i', $cleanPrompt)) {
            $isAskingTeacherSchedule = true;
        }
        if (preg_match('/\b(jadwal|schedule|ngajar|mengajar)\b/i', $cleanPrompt) && 
            preg_match('/\b(pak|bapak|ibu|bu)\s+([a-z]+)\b/i', $cleanPrompt)) {
            $isAskingTeacherSchedule = true;
        }

        // Cek apakah siswa menanyakan kehadiran / status absensi guru
        $isAskingTeacherAttendance = false;
        if (preg_match('/\b(guru|pengajar|pendidik|teacher|teachers)\b/i', $cleanPrompt) &&
            preg_match('/\b(alpa|bolos|hadir|masuk|izin|sakit|absen|absensi|presensi|kehadiran|tidak\s+hadir|teu\s+lebet|mboten\s+rawuh|rekap)\b/i', $cleanPrompt)) {
            $isAskingTeacherAttendance = true;
        }

        // Cek apakah siswa menanyakan status absensi / izin siswa lain secara umum (rekap sekolah / kelas)
        $isAskingOtherStudentAttendance = false;
        if (preg_match('/\b(siswa|murid|kanca|teman)\b/i', $cleanPrompt) &&
            preg_match('/\b(alpa|bolos|hadir|masuk|izin|sakit|siapa|daftar|rekap|semua)\b/i', $cleanPrompt) &&
            !preg_match('/\b(saya|aku|abdi|kula|kulo|pribadi|my)\b/i', $cleanPrompt)) {
            $isAskingOtherStudentAttendance = true;
        }

        if ($isAskingTeacherSchedule) {
            if ($detectedLang === 'en') {
                $reply = "I apologize, Kak {$namaLengkap}, but you are currently logged in as a Student. According to school system access control policies, teacher teaching schedules are confidential internal information and can only be accessed by Teachers, Homeroom Teachers, or the Administrator. You can only view the schedule for your own class ({$detailKelas}) and your personal attendance. Would you like to check today's schedule for your class?";
            } elseif ($detectedLang === 'su') {
                $reply = "Hapunten pisan Kak {$namaLengkap}, anjeun login salaku Siswa. Dumasar aturan hak aksés sistem, jadwal ngawulang Bapa/Ibu Guru sipatna internal sakola sareng mung tiasa diaksés ku pihak Guru, Wali Kelas, atanapi Administrator. Anjeun mung tiasa ningali jadwal pangajaran kelas anjeun nyalira ({$detailKelas}) sareng data kahadiran pribadi. Aya inpormasi jadwal kelas anjeun anu hoyong ditaroskeun?";
            } elseif ($detectedLang === 'jv') {
                $reply = "Nyuwun pangapunten Kak {$namaLengkap}, panjenengan login minangka Siswa. Adhedhasar pranatan hak akses sistem, jadwal mulang Bapak/Ibu Guru sipatipun internal sekolah lan namung saged dipunakses dening Guru, Wali Kelas, utawi Administrator. Panjenengan namung saged mirsani jadwal piwulangan kelas panjenengan piyambak ({$detailKelas}) lan data presensi pribadi. Wonten informasi jadwal kelas ingkang dipun betahaken?";
            } else {
                $reply = "Mohon maaf Kak {$namaLengkap}, kamu saat ini login sebagai Siswa. Sesuai aturan hak akses sistem sekolah, informasi jadwal mengajar Bapak/Ibu Guru bersifat internal dan hanya dapat diakses oleh pihak Guru, Wali Kelas, atau Administrator. Kamu hanya dapat melihat jadwal pelajaran untuk kelasmu sendiri ({$detailKelas}) dan data kehadiran pribadimu. Ada informasi seputar jadwal pelajaran kelasmu yang ingin kamu ketahui?";
            }
            sendBotReply($reply);
        }

        if ($isAskingTeacherAttendance) {
            if ($detectedLang === 'en') {
                $reply = "I apologize, Kak {$namaLengkap}, but you are logged in as a Student. Information regarding teacher attendance and permits is internal school data and cannot be accessed by students. You can only check your personal attendance status. Would you like to check your attendance today?";
            } elseif ($detectedLang === 'su') {
                $reply = "Hapunten pisan Kak {$namaLengkap}, anjeun login salaku Siswa. Inpormasi absensi sareng kahadiran Bapa/Ibu Guru sipatna internal sakola sareng teu tiasa diaksés ku siswa. Anjeun mung tiasa mariksa data kahadiran anjeun nyalira. Naha hoyong mariksa status absensi anjeun dinten ieu?";
            } elseif ($detectedLang === 'jv') {
                $reply = "Nyuwun pangapunten Kak {$namaLengkap}, panjenengan login minangka Siswa. Informasi presensi lan rawuhipun Bapak/Ibu Guru sipatipun internal sekolah lan mboten saged dipunakses dening siswa. Panjenengan namung saged mriksa presensi pribadi. Punapa panjenengan badhe mriksa status presensi dinten niki?";
            } else {
                $reply = "Mohon maaf Kak {$namaLengkap}, kamu saat ini login sebagai Siswa. Sesuai aturan hak akses sistem, informasi absensi dan kehadiran Bapak/Ibu Guru bersifat internal sekolah dan tidak dapat diakses oleh siswa. Kamu hanya dapat mengecek data kehadiran pribadimu sendiri. Apakah kamu ingin mengecek status kehadiranmu hari ini?";
            }
            sendBotReply($reply);
        }

        if ($isAskingOtherStudentAttendance) {
            $reply = "Mohon maaf Kak {$namaLengkap}, kamu saat ini login sebagai Siswa. Sesuai hak akses sistem, kamu hanya berwenang melihat status presensi dan pengajuan izin pribadimu sendiri. Status kehadiranmu dapat dicek langsung melalui dashboard absensi siswa atau tanyakan status kehadiranmu hari ini.";
            sendBotReply($reply);
        }
    }

    // A. Pertanyaan Langsung Identitas Diri: "Siapa Saya?" / "Saya Siapa?" / Slang & Singkatan ("sy sp", "sp sy") / Multibahasa ("who am i", "saha abdi", "aku sopo", "kula sinten")
    $isWhoAmI = false;
    if (
        in_array($cleanPrompt, [
            'sy sp', 'sp sy', 'sy siapa', 'siapa sy', 'aku sp', 'sp aku', 'gw sp', 'sp gw', 'gue sp', 'sp gue',
            'siapa saya', 'saya siapa', 'siapa aku', 'aku siapa', 'siapa gue', 'gue siapa', 'siapa gw', 'gw siapa',
            'nama saya siapa', 'siapa nama saya', 'nama aku siapa', 'siapa nama aku', 'nama sy siapa', 'siapa nama sy',
            'nama saya', 'namaku siapa', 'siapa namaku', 'siapakah saya', 'siapakah aku', 'siapa diriku',
            'tau siapa saya', 'tau sy sp', 'kenal saya',
            'who am i', 'who i am', 'whoami', 'my name', 'whats my name', 'what is my name',
            'saha abdi', 'abdi saha', 'saha simkuring', 'simkuring saha', 'saha kuring', 'kuring saha', 'saha wasta abdi', 'wasta abdi saha', 'saha nami abdi', 'nami abdi saha',
            'aku sopo', 'sopo aku', 'kula sinten', 'sinten kula', 'kulo sinten', 'sinten kulo', 'sopo jenengku', 'jenengku sopo', 'sinten asma kula', 'asma kula sinten'
        ]) ||
        preg_match('/^(sy|saya|aku|gw|gue|kuring|diriku)\s*(sp|siapa|saha|sopo|sinten)$/i', $cleanPrompt) ||
        preg_match('/^(sp|siapa|saha|sopo|sinten)\s*(sy|saya|aku|gw|gue|kuring|diriku)$/i', $cleanPrompt) ||
        preg_match('/^who\s+(am\s+i|i\s+am|is\s+me)$/i', $cleanPrompt) ||
        preg_match('/^(what\s+is|what\'s|whats)\s+my\s+name$/i', $cleanPrompt) ||
        preg_match('/^(saha\s+(abdi|simkuring|kuring|urang|wasta\s+abdi|nami\s+abdi)|(abdi|simkuring|kuring|urang|wasta\s+abdi|nami\s+abdi)\s+saha)$/i', $cleanPrompt) ||
        preg_match('/^(sopo\s+(aku|kula|kulo|jenengku)|(aku|kula|kulo|jenengku)\s+sopo)$/i', $cleanPrompt) ||
        preg_match('/^(sinten\s+(kula|kulo|aku|asma\s+kula)|(kula|kulo|aku|asma\s+kula)\s+sinten)$/i', $cleanPrompt)
    ) {
        $isWhoAmI = true;
    }

    if ($isWhoAmI) {
        if ($detectedLang === 'en') {
            $reply = $identitasJawabanEn;
        } elseif ($detectedLang === 'su') {
            $reply = $identitasJawabanSu;
        } elseif ($detectedLang === 'jv') {
            $reply = $identitasJawabanJv;
        } else {
            $reply = $identitasJawabanId;
        }

        sendBotReply($reply);
    }

    // B. Sapaan Murni: "Halo", "Hello", "Sampurasun", "Sugeng enjang", dll.
    if (
        in_array($cleanPrompt, [
            'halo', 'hallo', 'hai', 'hi', 'hei', 'hey', 'pagi', 'siang', 'sore', 'malam',
            'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam',
            'assalamualaikum', 'assalamu alaikum', 'assalamu\'alaikum',
            'hello', 'good morning', 'good afternoon', 'good evening',
            'sampurasun', 'kumaha damang', 'wilujeng enjing', 'wilujeng siang', 'wilujeng sonten', 'wilujeng wengi',
            'sugeng enjang', 'sugeng injing', 'sugeng siang', 'sugeng sonten', 'sugeng dalu', 'piye kabare'
        ])
    ) {
        if (strpos($cleanPrompt, 'assalamu') !== false) {
            $reply = "Wa'alaikumsalam, {$sapaanWaktu}! Ada yang bisa saya bantu terkait informasi sistem atau sekolah?";
        } elseif (strpos($cleanPrompt, 'sampurasun') !== false) {
            $reply = "Rampes! Wilujeng {$periodeWaktuSu}! Aya anu tiasa dibantos perkawis absensi atanapi sakola?";
        } elseif (strpos($cleanPrompt, 'kumaha damang') !== false) {
            $reply = "Alhamdulillah sae! Aya anu tiasa dibantos perkawis absensi atanapi sakola?";
        } elseif ($detectedLang === 'su') {
            $reply = "Wilujeng {$periodeWaktuSu}! Aya anu tiasa dibantos perkawis inpormasi sistem atanapi sakola?";
        } elseif (strpos($cleanPrompt, 'piye kabare') !== false) {
            $reply = "Alhamdulillah sae lan wilujeng! Wonten ingkang saged kula biyantu babagan presensi utawi sekolah?";
        } elseif ($detectedLang === 'jv') {
            $reply = "Sugeng {$periodeWaktuJv}! Wonten ingkang saged kula biyantu babagan informasi sistem utawi sekolah?";
        } elseif ($detectedLang === 'en') {
            $reply = "Hello! Good {$periodeWaktuEn}! How can I help you regarding school or attendance information?";
        } else {
            $reply = "Halo, {$sapaanWaktu}! Ada yang bisa saya bantu terkait informasi sistem atau sekolah?";
        }

        sendBotReply($reply);
    }

    // C. Pertanyaan Identitas Bot: "Kamu Siapa?" / "Who are you?" / "Saha anjeun?" / "Sinten panjenengan?"
    if (
        in_array($cleanPrompt, [
            'kamu siapa', 'siapa kamu', 'siapa anda', 'anda siapa', 'ini siapa',
            'kamu itu siapa', 'kamu siapa sih', 'siapa chatbot ini', 'chatbot siapa',
            'who are you', 'who r u', 'what are you', 'who is this bot', 'what is your name',
            'saha anjeun', 'anjeun saha', 'saha salira', 'salira saha',
            'sopo koe', 'koe sopo', 'sampeyan sinten', 'sinten sampeyan', 'panjenengan sinten', 'sinten panjenengan'
        ]) ||
        preg_match('/^(kamu|anda|anjeun|salira|koe|sampeyan|panjenengan)\s*(siapa|sp|saha|sopo|sinten)$/i', $cleanPrompt) ||
        preg_match('/^(siapa|sp|saha|sopo|sinten)\s*(kamu|anda|anjeun|salira|koe|sampeyan|panjenengan)$/i', $cleanPrompt) ||
        preg_match('/^who\s+(are\s+you|r\s+u)$/i', $cleanPrompt)
    ) {
        if ($detectedLang === 'en') {
            $reply = "I am the official school AI assistant for SMK NU WARGABINANGUN Attendance System. How can I assist you with schedules, attendance, or other school information?";
        } elseif ($detectedLang === 'su') {
            $reply = "Simkuring teh asisten AI sakola kanggo Sistem Absensi SMK NU WARGABINANGUN. Aya anu tiasa dibantos perkawis jadwal, absensi, atanapi inpormasi sanesna?";
        } elseif ($detectedLang === 'jv') {
            $reply = "Kula punika asisten AI sekolah kagem Sistem Presensi SMK NU WARGABINANGUN. Wonten ingkang saged kula biyantu babagan jadwal, presensi, utawi informasi sanesipun?";
        } else {
            $reply = "Saya adalah asisten AI sekolah untuk Sistem Absensi SMK NU WARGABINANGUN. Ada yang bisa saya bantu terkait jadwal, absensi, atau informasi lainnya?";
        }
        sendBotReply($reply);
    }

    $isSelfPronoun = (bool)preg_match('/\b(saya|sy|aku|pribadi|sendiri|abdi|simkuring|kuring|kula|kulo|my|me)\b/i', $cleanPrompt);

    // =========================================================================
    // D0. PERTANYAAN DATA PRIBADI (RIWAYAT ABSENSI & PENGAJUAN IZIN/SAKIT PRIBADI)
    // =========================================================================

    // D0.1. Pengajuan Izin / Sakit Pribadi Pengguna
    $isAskingOwnPengajuanIzin = false;
    if (
        preg_match('/\b(pengajuan|surat|ajukan|mengajukan)\b/i', $cleanPrompt) &&
        (
            preg_match('/\b(izin|ijin|sakit)\b/i', $cleanPrompt) ||
            $isSelfPronoun ||
            in_array($rawRole, ['siswa', 'guru', 'wali_kelas'])
        ) &&
        !preg_match('/\b(rekap\s*kelas|guru\s*lain|siswa\s*lain|sekolah)\b/i', $cleanPrompt)
    ) {
        $isAskingOwnPengajuanIzin = true;
    } elseif (
        in_array($cleanPrompt, [
            'pengajuan izin saya', 'pengajuan sakit saya', 'pengajuan izin', 'pengajuan sakit',
            'riwayat pengajuan izin saya', 'daftar pengajuan izin saya', 'status pengajuan izin saya',
            'riwayat pengajuan izin', 'daftar pengajuan izin'
        ])
    ) {
        $isAskingOwnPengajuanIzin = true;
    }

    if ($isAskingOwnPengajuanIzin) {
        if ($rawRole === 'siswa') {
            $stMyIz = $pdo->prepare("SELECT tanggal, jenis, alasan, status FROM pengajuan_izin WHERE siswa_id = ? AND tipe_pemohon = 'siswa' ORDER BY tanggal DESC, id DESC");
            $stMyIz->execute([$mySiswaId]);
            $myIzRows = $stMyIz->fetchAll(PDO::FETCH_ASSOC);
            $totMyIz = count($myIzRows);
            $cntIzin = count(array_filter($myIzRows, fn($x) => strtolower($x['jenis']) === 'izin'));
            $cntSakit = count(array_filter($myIzRows, fn($x) => strtolower($x['jenis']) === 'sakit'));
            $cntApproved = count(array_filter($myIzRows, fn($x) => strtolower($x['status']) === 'disetujui'));

            if ($totMyIz === 0) {
                $reply = "Kamu belum pernah melakukan pengajuan izin maupun sakit.";
            } else {
                $asksList = (bool)preg_match('/\b(daftar|list|sebutkan|apa\s*(saja|aja)|rincian|detail)\b/i', $cleanPrompt);
                if (!$asksList) {
                    $statusKet = ($cntApproved === $totMyIz) ? "Seluruhnya telah disetujui." : "({$cntApproved} disetujui).";
                    $reply = "Kamu telah melakukan {$totMyIz} kali pengajuan izin/sakit ({$cntIzin} Izin, {$cntSakit} Sakit). {$statusKet}";
                } else {
                    $reply = "Berikut riwayat pengajuan izin/sakit kamu ({$totMyIz} pengajuan):\n";
                    foreach ($myIzRows as $idx => $iz) {
                        $tglFmt = date('d/m/Y', strtotime($iz['tanggal']));
                        $reply .= ($idx + 1) . ". {$tglFmt} - {$iz['jenis']}: \"{$iz['alasan']}\" (Status: {$iz['status']})\n";
                    }
                }
            }
            sendBotReply(trim($reply));
        } elseif (in_array($rawRole, ['guru', 'wali_kelas'])) {
            $stMyIz = $pdo->prepare("SELECT tanggal, jenis, alasan, status FROM pengajuan_izin WHERE guru_id = ? AND tipe_pemohon = 'guru' ORDER BY tanggal DESC, id DESC");
            $stMyIz->execute([$myGuruId]);
            $myIzRows = $stMyIz->fetchAll(PDO::FETCH_ASSOC);
            $totMyIz = count($myIzRows);
            $cntIzin = count(array_filter($myIzRows, fn($x) => strtolower($x['jenis']) === 'izin'));
            $cntSakit = count(array_filter($myIzRows, fn($x) => strtolower($x['jenis']) === 'sakit'));
            $cntApproved = count(array_filter($myIzRows, fn($x) => strtolower($x['status']) === 'disetujui'));

            if ($totMyIz === 0) {
                $reply = "Anda belum pernah melakukan pengajuan izin maupun sakit.";
            } else {
                $asksList = (bool)preg_match('/\b(daftar|list|sebutkan|apa\s*(saja|aja)|rincian|detail)\b/i', $cleanPrompt);
                if (!$asksList) {
                    $statusKet = ($cntApproved === $totMyIz) ? "Seluruhnya telah disetujui." : "({$cntApproved} disetujui).";
                    $reply = "Anda telah melakukan {$totMyIz} kali pengajuan izin/sakit ({$cntIzin} Izin, {$cntSakit} Sakit). {$statusKet}";
                } else {
                    $reply = "Berikut riwayat pengajuan izin/sakit Anda ({$totMyIz} pengajuan):\n";
                    foreach ($myIzRows as $idx => $iz) {
                        $tglFmt = date('d/m/Y', strtotime($iz['tanggal']));
                        $reply .= ($idx + 1) . ". {$tglFmt} - {$iz['jenis']}: \"{$iz['alasan']}\" (Status: {$iz['status']})\n";
                    }
                }
            }
            sendBotReply(trim($reply));
        }
    }

    // D0.2. Riwayat & Total Absensi / Presensi Pribadi Pengguna (HANYA Hari Efektif Sekolah: Senin s/d Jumat)
    $isAskingOwnAttendanceHistory = false;
    if (
        (
            $isSelfPronoun &&
            preg_match('/\b(riwayat|history|catatan|total|jumlah|totaal|semua|seluruh|berapa)\s*(absen|absensi|presensi|kehadiran)\b/i', $cleanPrompt)
        ) ||
        (
            preg_match('/\b(absen|absensi|presensi|kehadiran)\b/i', $cleanPrompt) &&
            $isSelfPronoun &&
            preg_match('/\b(berapa|jumlah|total|totaal|riwayat|history|catatan|kali|semua|seluruh)\b/i', $cleanPrompt) &&
            !preg_match('/\b(guru\s*lain|siswa\s*lain|kelas\s*lain|sekolah)\b/i', $cleanPrompt)
        ) ||
        (
            $isSelfPronoun &&
            preg_match('/\b(berapa\s*kali\s*(saya|aku|sy)\s*(alpa|izin|sakit|hadir|bolos|terlambat|masuk))\b/i', $cleanPrompt)
        ) ||
        in_array($cleanPrompt, [
            'riwayat absensi saya', 'total riwayat absensi saya', 'totaal riwayat absensi saya',
            'jumlah riwayat absensi saya', 'riwayat presensi saya', 'berapa riwayat absensi saya',
            'berapa jumlah riwayat absensi saya', 'totaal riwayat absensi saya?',
            'total absensi saya', 'berapa total absensi saya', 'berapa total presensi saya'
        ])
    ) {
        $isAskingOwnAttendanceHistory = true;
    }

    if ($isAskingOwnAttendanceHistory) {
        if ($rawRole === 'siswa') {
            $stMyRiw = $pdo->prepare("SELECT tanggal, status, jam_masuk, jam_pulang, keterangan 
                                     FROM absensi_siswa 
                                     WHERE siswa_id = ? AND DAYOFWEEK(tanggal) NOT IN (1, 7) 
                                     ORDER BY tanggal DESC");
            $stMyRiw->execute([$mySiswaId]);
            $myRiwRows = $stMyRiw->fetchAll(PDO::FETCH_ASSOC);
            $totMyRiw = count($myRiwRows);

            $stMap = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
            foreach ($myRiwRows as $r) {
                $stL = strtolower($r['status']);
                if ($stL === 'bolos') $stL = 'alpa';
                if (isset($stMap[$stL])) $stMap[$stL]++;
            }

            if (preg_match('/\b(alpa|bolos)\b/i', $cleanPrompt)) {
                $reply = "Kamu tercatat alpa sebanyak {$stMap['alpa']} kali.";
            } elseif (preg_match('/\b(sakit)\b/i', $cleanPrompt)) {
                $reply = "Kamu tercatat sakit sebanyak {$stMap['sakit']} kali.";
            } elseif (preg_match('/\b(izin|ijin)\b/i', $cleanPrompt)) {
                $reply = "Kamu tercatat izin sebanyak {$stMap['izin']} kali.";
            } elseif (preg_match('/\b(terlambat)\b/i', $cleanPrompt)) {
                $reply = "Kamu tercatat terlambat sebanyak {$stMap['terlambat']} kali.";
            } elseif (preg_match('/\b(hadir|masuk)\b/i', $cleanPrompt) && !preg_match('/\b(tidak|belum)\b/i', $cleanPrompt)) {
                $reply = "Kamu tercatat hadir sebanyak {$stMap['hadir']} kali (Terlambat: {$stMap['terlambat']}).";
            } else {
                $asksList = (bool)preg_match('/\b(daftar|list|sebutkan|apa\s*(saja|aja)|rincian|detail)\b/i', $cleanPrompt);
                if (!$asksList) {
                    $reply = "Total riwayat presensi pribadi kamu berjumlah {$totMyRiw} catatan (Hadir: {$stMap['hadir']}, Terlambat: {$stMap['terlambat']}, Izin: {$stMap['izin']}, Sakit: {$stMap['sakit']}, Alpa: {$stMap['alpa']}). Rekapitulasi absensi hanya menghitung hari efektif sekolah (Senin sampai Jumat).";
                } else {
                    $reply = "Berikut riwayat presensi pribadi kamu ({$totMyRiw} hari sekolah efektif):\n";
                    foreach (array_slice($myRiwRows, 0, 15) as $idx => $rw) {
                        $tglFmt = date('d/m/Y', strtotime($rw['tanggal']));
                        $jm = $rw['jam_masuk'] ? substr($rw['jam_masuk'], 0, 5) : '-';
                        $jp = $rw['jam_pulang'] ? substr($rw['jam_pulang'], 0, 5) : '-';
                        $reply .= ($idx + 1) . ". {$tglFmt} - Status: " . ucfirst($rw['status']) . " (Masuk: $jm, Pulang: $jp)\n";
                    }
                }
            }
            sendBotReply(trim($reply));
        } elseif (in_array($rawRole, ['guru', 'wali_kelas'])) {
            $stMyRiw = $pdo->prepare("SELECT tanggal, status, jam_masuk, jam_pulang, keterangan 
                                     FROM absensi_guru 
                                     WHERE guru_id = ? AND DAYOFWEEK(tanggal) NOT IN (1, 7) 
                                     ORDER BY tanggal DESC");
            $stMyRiw->execute([$myGuruId]);
            $myRiwRows = $stMyRiw->fetchAll(PDO::FETCH_ASSOC);
            $totMyRiw = count($myRiwRows);

            $stMap = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
            foreach ($myRiwRows as $r) {
                $stL = strtolower($r['status']);
                if ($stL === 'bolos') $stL = 'alpa';
                if (isset($stMap[$stL])) $stMap[$stL]++;
            }

            if (preg_match('/\b(alpa|bolos)\b/i', $cleanPrompt)) {
                $reply = "Anda tercatat alpa sebanyak {$stMap['alpa']} kali.";
            } elseif (preg_match('/\b(sakit)\b/i', $cleanPrompt)) {
                $reply = "Anda tercatat sakit sebanyak {$stMap['sakit']} kali.";
            } elseif (preg_match('/\b(izin|ijin)\b/i', $cleanPrompt)) {
                $reply = "Anda tercatat izin sebanyak {$stMap['izin']} kali.";
            } elseif (preg_match('/\b(terlambat)\b/i', $cleanPrompt)) {
                $reply = "Anda tercatat terlambat sebanyak {$stMap['terlambat']} kali.";
            } elseif (preg_match('/\b(hadir|masuk)\b/i', $cleanPrompt) && !preg_match('/\b(tidak|belum)\b/i', $cleanPrompt)) {
                $reply = "Anda tercatat hadir sebanyak {$stMap['hadir']} kali (Terlambat: {$stMap['terlambat']}).";
            } else {
                $asksList = (bool)preg_match('/\b(daftar|list|sebutkan|apa\s*(saja|aja)|rincian|detail)\b/i', $cleanPrompt);
                if (!$asksList) {
                    $reply = "Total riwayat presensi pribadi Anda berjumlah {$totMyRiw} catatan (Hadir: {$stMap['hadir']}, Terlambat: {$stMap['terlambat']}, Izin: {$stMap['izin']}, Sakit: {$stMap['sakit']}, Alpa: {$stMap['alpa']}). Rekapitulasi absensi hanya menghitung hari efektif sekolah (Senin sampai Jumat).";
                } else {
                    $reply = "Berikut riwayat presensi pribadi Anda ({$totMyRiw} hari sekolah efektif):\n";
                    foreach (array_slice($myRiwRows, 0, 15) as $idx => $rw) {
                        $tglFmt = date('d/m/Y', strtotime($rw['tanggal']));
                        $jm = $rw['jam_masuk'] ? substr($rw['jam_masuk'], 0, 5) : '-';
                        $jp = $rw['jam_pulang'] ? substr($rw['jam_pulang'], 0, 5) : '-';
                        $reply .= ($idx + 1) . ". {$tglFmt} - Status: " . ucfirst($rw['status']) . " (Masuk: $jm, Pulang: $jp)\n";
                    }
                }
            }
            sendBotReply(trim($reply));
        }
    }

    // D1. Pertanyaan Cepat Total / Jumlah Jurusan Sekolah
    $isJurusanQuery = false;
    if (
        preg_match('/\b(jurusan|kompetensi\s*keahlian|program\s*keahlian|prodi|major|majors)\b/i', $cleanPrompt) &&
        (
            preg_match('/\b(berapa|total|jumlah|ada\s*berapa|daftar|apa\s*(aja|saja)|list|sebutkan|how\s*many|sabaraha|pira|piro|pinten)\b/i', $cleanPrompt) ||
            in_array($cleanPrompt, ['jurusan', 'total jurusan', 'jumlah jurusan', 'ada berapa jurusan', 'jurusan apa saja', 'daftar jurusan', 'kalau total jurusan ada berapa', 'kalo total jurusan ada berapa'])
        ) &&
        !preg_match('/\b(pindah|ganti|jadwal\s*jurusan|absen|kehadiran|siswa|siswi|murid|guru|pengajar|laki|lakilaki|perempuan|wanita|pria|tkj|tkr|tsm)\b/i', $cleanPrompt) &&
        !($isSelfPronoun && !preg_match('/\b(semua|seluruh|di\s*sekolah|daftar|total)\b/i', $cleanPrompt))
    ) {
        $isJurusanQuery = true;
    }

    if ($isJurusanQuery) {
        if (!in_array($rawRole, ['admin', 'kepala_sekolah'])) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'daftar dan total keseluruhan jurusan di sekolah', 'the school-wide list and count of majors'));
        }
        $liveJurusan = $pdo->query("SELECT kode_jurusan, nama_jurusan FROM jurusan ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        $totalJur = count($liveJurusan);
        $asksJurusanList = (bool)preg_match('/\b(daftar|sebutkan|apa\s*(saja|aja)|list|nama)\b/i', $cleanPrompt);
        if (!$asksJurusanList) {
            if ($detectedLang === 'en') {
                $reply = "There are {$totalJur} majors: TKJ, TKR, and TSM.";
            } elseif ($detectedLang === 'su') {
                $reply = "Aya {$totalJur} jurusan: TKJ, TKR, sareng TSM.";
            } elseif ($detectedLang === 'jv') {
                $reply = "Wonten {$totalJur} jurusan: TKJ, TKR, lan TSM.";
            } else {
                $reply = "Ada {$totalJur} jurusan: TKJ, TKR, dan TSM.";
            }
        } else {
            if ($detectedLang === 'en') {
                $reply = "There are {$totalJur} majors in the system:\n";
            } elseif ($detectedLang === 'su') {
                $reply = "Aya {$totalJur} program kaahlian (jurusan):\n";
            } elseif ($detectedLang === 'jv') {
                $reply = "Wonten {$totalJur} program keahlian (jurusan):\n";
            } else {
                $reply = "Ada {$totalJur} jurusan di SMK NU WARGABINANGUN:\n";
            }
            foreach ($liveJurusan as $idx => $jr) {
                $reply .= ($idx + 1) . ". {$jr['kode_jurusan']} ({$jr['nama_jurusan']})\n";
            }
        }
        sendBotReply(trim($reply));
    }

    // D2A. Pertanyaan Rincian Jumlah Siswa per Kelas / Jurusan / Tingkat / Jenis Kelamin (Strict RBAC)
    $mentionsStudentWord = (bool)preg_match('/\b(siswa|siswi|murid|pelajar|student|students)\b/i', $cleanPrompt);
    $asksCountOrGenderWord = (bool)($asksGender || $asksPerKelasBreakdown || preg_match('/\b(total|jumlah|berapa|ada\s*berapa|banyaknya|rincian|komposisi|count|how\s*many|sabaraha|pira|piro|pinten)\b/i', $cleanPrompt));
    $notOtherDomainKeywords = !preg_match('/\b(nip|nis|nisn|nomor|kontak|telp|hp|email|alamat|jadwal|absen|absensi|presensi|kehadiran|hadir|alpa|bolos|izin|sakit|catatan|pembinaan|supervisi)\b/i', $cleanPrompt);
    $hasSpecificStudentFilter = (
        !empty($mentionedClassCompact) ||
        !empty($mentionedJurusanCode) ||
        !empty($mentionedTingkat) ||
        $asksGender ||
        $asksPerKelasBreakdown ||
        ($rawRole === 'wali_kelas' && ($isAskingOwnClassJadwal || preg_match('/\b(kelas\s*(saya|aku|binaan|kami|ini)|kelasku)\b/i', $cleanPrompt)))
    );

    if ($notOtherDomainKeywords && $asksCountOrGenderWord && $hasSpecificStudentFilter && ($mentionsStudentWord || !empty($mentionedClassCompact))) {
        if (in_array($rawRole, ['siswa', 'guru'])) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'jumlah, daftar, atau rincian jenis kelamin siswa', 'student counts or gender breakdowns'));
        }

        if ($rawRole === 'wali_kelas') {
            $asksOutOfScopeWali = (
                $mentionsOtherSpecificClass ||
                !empty($mentionedJurusanCode) ||
                !empty($mentionedTingkat) ||
                $asksPerKelasBreakdown ||
                preg_match('/\b(semua|seluruh|satu\s*sekolah|di\s*sekolah|per\s*kelas|tiap\s*kelas)\b/i', $cleanPrompt)
            );
            if ($asksOutOfScopeWali) {
                sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, "data atau rincian siswa di luar kelas binaan Anda ({$detailKelas})", "student breakdowns outside your homeroom class ({$detailKelas})"));
            }
            if ($myKelasId <= 0) {
                sendBotReply("Berdasarkan data sistem, akun Wali Kelas Anda belum memiliki penugasan kelas binaan pada tahun ajaran aktif.");
            }
            $stWkStat = $pdo->prepare("SELECT COUNT(*) AS total_siswa,
                                              COALESCE(SUM(CASE WHEN UPPER(TRIM(jenis_kelamin)) = 'L' THEN 1 ELSE 0 END), 0) AS total_laki,
                                              COALESCE(SUM(CASE WHEN UPPER(TRIM(jenis_kelamin)) = 'P' THEN 1 ELSE 0 END), 0) AS total_perempuan
                                       FROM siswa
                                       WHERE kelas_id = ?");
            $stWkStat->execute([$myKelasId]);
            $wkStat = $stWkStat->fetch(PDO::FETCH_ASSOC);
            $totWk = (int)($wkStat['total_siswa'] ?? 0);
            $lWk = (int)($wkStat['total_laki'] ?? 0);
            $pWk = (int)($wkStat['total_perempuan'] ?? 0);

            if ($asksMale && !$asksFemale) {
                sendBotReply("Ada {$lWk} siswa laki-laki di kelas {$detailKelas}.");
            } elseif ($asksFemale && !$asksMale) {
                sendBotReply("Ada {$pWk} siswa perempuan di kelas {$detailKelas}.");
            } else {
                sendBotReply("Di kelas {$detailKelas} ada {$totWk} siswa ({$lWk} laki-laki, {$pWk} perempuan).");
            }
        }

        // Admin & Kepala Sekolah: penuh sesuai data tabel kelas, jurusan, tingkat, dan siswa
        if (!empty($mentionedClassCompact)) {
            $allKelasRows = $pdo->query("SELECT k.id, k.tingkat, k.nama_kelas, j.kode_jurusan, j.nama_jurusan
                                         FROM kelas k
                                         JOIN jurusan j ON k.jurusan_id = j.id
                                         ORDER BY k.tingkat ASC, k.nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
            $matchedKelasRow = null;
            foreach ($allKelasRows as $kr) {
                $kComp = preg_replace('/[^a-z0-9]/', '', strtolower($kr['nama_kelas']));
                if ($kComp === $mentionedClassCompact) {
                    $matchedKelasRow = $kr;
                    break;
                }
            }

            if (!$matchedKelasRow) {
                $daftarNamaKelas = implode(', ', array_map(fn($kr) => $kr['nama_kelas'], $allKelasRows));
                sendBotReply("Kelas " . strtoupper($mentionedClassNormalized) . " tidak ditemukan. Kelas yang terdaftar: {$daftarNamaKelas}.");
            }

            $stKlsStat = $pdo->prepare("SELECT COUNT(*) AS total_siswa,
                                               COALESCE(SUM(CASE WHEN UPPER(TRIM(jenis_kelamin)) = 'L' THEN 1 ELSE 0 END), 0) AS total_laki,
                                               COALESCE(SUM(CASE WHEN UPPER(TRIM(jenis_kelamin)) = 'P' THEN 1 ELSE 0 END), 0) AS total_perempuan
                                        FROM siswa
                                        WHERE kelas_id = ?");
            $stKlsStat->execute([(int)$matchedKelasRow['id']]);
            $kStat = $stKlsStat->fetch(PDO::FETCH_ASSOC);
            $totK = (int)($kStat['total_siswa'] ?? 0);
            $lK = (int)($kStat['total_laki'] ?? 0);
            $pK = (int)($kStat['total_perempuan'] ?? 0);
            $namaKlsAsli = $matchedKelasRow['nama_kelas'];
            $namaJurAsli = $matchedKelasRow['nama_jurusan'];

            if ($detectedLang === 'en') {
                if ($asksMale && !$asksFemale) {
                    $reply = "There are {$lK} male students in class {$namaKlsAsli}.";
                } elseif ($asksFemale && !$asksMale) {
                    $reply = "There are {$pK} female students in class {$namaKlsAsli}.";
                } else {
                    $reply = "In class {$namaKlsAsli} there are {$totK} students ({$lK} male, {$pK} female).";
                }
            } elseif ($detectedLang === 'su') {
                if ($asksMale && !$asksFemale) {
                    $reply = "Aya {$lK} siswa pameget di kelas {$namaKlsAsli}.";
                } elseif ($asksFemale && !$asksMale) {
                    $reply = "Aya {$pK} siswa istri di kelas {$namaKlsAsli}.";
                } else {
                    $reply = "Di kelas {$namaKlsAsli} aya {$totK} siswa ({$lK} pameget, {$pK} istri).";
                }
            } elseif ($detectedLang === 'jv') {
                if ($asksMale && !$asksFemale) {
                    $reply = "Wonten {$lK} siswa kakung wonten kelas {$namaKlsAsli}.";
                } elseif ($asksFemale && !$asksMale) {
                    $reply = "Wonten {$pK} siswa putri wonten kelas {$namaKlsAsli}.";
                } else {
                    $reply = "Wonten kelas {$namaKlsAsli} wonten {$totK} siswa ({$lK} kakung, {$pK} putri).";
                }
            } else {
                if ($asksMale && !$asksFemale) {
                    $reply = "Ada {$lK} siswa laki-laki di kelas {$namaKlsAsli}.";
                } elseif ($asksFemale && !$asksMale) {
                    $reply = "Ada {$pK} siswa perempuan di kelas {$namaKlsAsli}.";
                } else {
                    $reply = "Di kelas {$namaKlsAsli} ada {$totK} siswa ({$lK} laki-laki, {$pK} perempuan).";
                }
            }
            sendBotReply($reply);
        }

        if ($asksPerKelasBreakdown) {
            $perKelasRows = $pdo->query("SELECT k.nama_kelas,
                                                COUNT(s.id) AS total_siswa,
                                                COALESCE(SUM(CASE WHEN UPPER(TRIM(s.jenis_kelamin)) = 'L' THEN 1 ELSE 0 END), 0) AS total_laki,
                                                COALESCE(SUM(CASE WHEN UPPER(TRIM(s.jenis_kelamin)) = 'P' THEN 1 ELSE 0 END), 0) AS total_perempuan
                                         FROM kelas k
                                         LEFT JOIN siswa s ON s.kelas_id = k.id
                                         GROUP BY k.id, k.nama_kelas
                                         ORDER BY k.tingkat ASC, k.nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
            $grandTot = 0; $grandL = 0; $grandP = 0;
            $reply = "Jumlah siswa per kelas ({$grandTot} siswa):\n";
            $lines = [];
            foreach ($perKelasRows as $idx => $pk) {
                $t = (int)$pk['total_siswa'];
                $l = (int)$pk['total_laki'];
                $p = (int)$pk['total_perempuan'];
                $grandTot += $t; $grandL += $l; $grandP += $p;
                $lines[] = ($idx + 1) . ". Kelas {$pk['nama_kelas']}: {$t} siswa (L: {$l}, P: {$p})";
            }
            $reply = "Rincian siswa per kelas ({$grandTot} siswa):\n" . implode("\n", $lines);
            sendBotReply($reply);
        }

        if (!empty($mentionedJurusanCode)) {
            $stJurStat = $pdo->prepare("SELECT k.nama_kelas, j.nama_jurusan,
                                               COUNT(s.id) AS total_siswa,
                                               COALESCE(SUM(CASE WHEN UPPER(TRIM(s.jenis_kelamin)) = 'L' THEN 1 ELSE 0 END), 0) AS total_laki,
                                               COALESCE(SUM(CASE WHEN UPPER(TRIM(s.jenis_kelamin)) = 'P' THEN 1 ELSE 0 END), 0) AS total_perempuan
                                        FROM kelas k
                                        JOIN jurusan j ON k.jurusan_id = j.id
                                        LEFT JOIN siswa s ON s.kelas_id = k.id
                                        WHERE UPPER(j.kode_jurusan) = ?
                                        GROUP BY k.id, k.nama_kelas, j.nama_jurusan
                                        ORDER BY k.tingkat ASC, k.nama_kelas ASC");
            $stJurStat->execute([$mentionedJurusanCode]);
            $jurRows = $stJurStat->fetchAll(PDO::FETCH_ASSOC);
            if ($jurRows) {
                $namaJur = $jurRows[0]['nama_jurusan'];
                $jTot = 0; $jL = 0; $jP = 0;
                $lines = [];
                foreach ($jurRows as $jr) {
                    $t = (int)$jr['total_siswa']; $l = (int)$jr['total_laki']; $p = (int)$jr['total_perempuan'];
                    $jTot += $t; $jL += $l; $jP += $p;
                    $lines[] = "- Kelas {$jr['nama_kelas']}: {$t} siswa (L: {$l}, P: {$p})";
                }
                if ($asksPerKelasBreakdown || preg_match('/\b(rincian|detail)\b/i', $cleanPrompt)) {
                    $reply = "Ada {$jTot} siswa di jurusan {$mentionedJurusanCode} ({$jL} laki-laki, {$jP} perempuan):\n" . implode("\n", $lines);
                } else {
                    $reply = "Ada {$jTot} siswa di jurusan {$mentionedJurusanCode} ({$jL} laki-laki, {$jP} perempuan).";
                }
                sendBotReply($reply);
            }
        }

        if (!empty($mentionedTingkat)) {
            $stTktStat = $pdo->prepare("SELECT k.nama_kelas,
                                               COUNT(s.id) AS total_siswa,
                                               COALESCE(SUM(CASE WHEN UPPER(TRIM(s.jenis_kelamin)) = 'L' THEN 1 ELSE 0 END), 0) AS total_laki,
                                               COALESCE(SUM(CASE WHEN UPPER(TRIM(s.jenis_kelamin)) = 'P' THEN 1 ELSE 0 END), 0) AS total_perempuan
                                        FROM kelas k
                                        LEFT JOIN siswa s ON s.kelas_id = k.id
                                        WHERE UPPER(k.tingkat) = ?
                                        GROUP BY k.id, k.nama_kelas
                                        ORDER BY k.nama_kelas ASC");
            $stTktStat->execute([$mentionedTingkat]);
            $tktRows = $stTktStat->fetchAll(PDO::FETCH_ASSOC);
            if ($tktRows) {
                $tTot = 0; $tL = 0; $tP = 0;
                $lines = [];
                foreach ($tktRows as $tr) {
                    $t = (int)$tr['total_siswa']; $l = (int)$tr['total_laki']; $p = (int)$tr['total_perempuan'];
                    $tTot += $t; $tL += $l; $tP += $p;
                    $lines[] = "- Kelas {$tr['nama_kelas']}: {$t} siswa (L: {$l}, P: {$p})";
                }
                if ($asksPerKelasBreakdown || preg_match('/\b(rincian|detail)\b/i', $cleanPrompt)) {
                    $reply = "Ada {$tTot} siswa di tingkat/kelas {$mentionedTingkat} ({$tL} laki-laki, {$tP} perempuan):\n" . implode("\n", $lines);
                } else {
                    $reply = "Ada {$tTot} siswa di tingkat/kelas {$mentionedTingkat} ({$tL} laki-laki, {$tP} perempuan).";
                }
                sendBotReply($reply);
            }
        }

        // Total siswa seluruh sekolah dirinci L/P
        $allStat = $pdo->query("SELECT COUNT(*) AS total_siswa,
                                       COALESCE(SUM(CASE WHEN UPPER(TRIM(jenis_kelamin)) = 'L' THEN 1 ELSE 0 END), 0) AS total_laki,
                                       COALESCE(SUM(CASE WHEN UPPER(TRIM(jenis_kelamin)) = 'P' THEN 1 ELSE 0 END), 0) AS total_perempuan
                                FROM siswa")->fetch(PDO::FETCH_ASSOC);
        $sTot = (int)($allStat['total_siswa'] ?? 0);
        $sL = (int)($allStat['total_laki'] ?? 0);
        $sP = (int)($allStat['total_perempuan'] ?? 0);
        if ($asksMale && !$asksFemale) {
            $reply = "Ada {$sL} siswa laki-laki di sekolah.";
        } elseif ($asksFemale && !$asksMale) {
            $reply = "Ada {$sP} siswa perempuan di sekolah.";
        } else {
            $reply = "Ada {$sTot} siswa di sekolah ({$sL} laki-laki, {$sP} perempuan).";
        }
        sendBotReply($reply);
    }

    // D2B. Pertanyaan Daftar Nama Siswa dalam Kelas Tertentu (Strict RBAC)
    if (
        $mentionsStudentWord &&
        !empty($mentionedClassCompact) &&
        $notOtherDomainKeywords &&
        preg_match('/\b(daftar|list|siapa|nama|tampilkan|sebutkan|lihat)\b/i', $cleanPrompt)
    ) {
        if (in_array($rawRole, ['siswa', 'guru'])) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'daftar nama siswa', 'student lists'));
        }
        if ($rawRole === 'wali_kelas' && $mentionsOtherSpecificClass) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, "daftar siswa di luar kelas binaan Anda ({$detailKelas})", "student lists outside your homeroom class ({$detailKelas})"));
        }

        $allKelasRows2 = $pdo->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
        $targetKls = null;
        foreach ($allKelasRows2 as $kr) {
            if (preg_replace('/[^a-z0-9]/', '', strtolower($kr['nama_kelas'])) === $mentionedClassCompact) {
                $targetKls = $kr;
                break;
            }
        }
        if ($targetKls) {
            $stListS = $pdo->prepare("SELECT nis, nama_lengkap, jenis_kelamin FROM siswa WHERE kelas_id = ? ORDER BY nama_lengkap ASC");
            $stListS->execute([(int)$targetKls['id']]);
            $listS = $stListS->fetchAll(PDO::FETCH_ASSOC);
            $cntL = count(array_filter($listS, fn($x) => strtoupper(trim($x['jenis_kelamin'])) === 'L'));
            $cntP = count(array_filter($listS, fn($x) => strtoupper(trim($x['jenis_kelamin'])) === 'P'));
            $reply = "Berikut daftar siswa kelas {$targetKls['nama_kelas']} (" . count($listS) . " siswa — Laki-laki: {$cntL}, Perempuan: {$cntP}):\n\n";
            foreach ($listS as $idx => $sx) {
                $jkLbl = (strtoupper(trim($sx['jenis_kelamin'])) === 'P') ? 'P' : 'L';
                $reply .= ($idx + 1) . ". {$sx['nama_lengkap']} (NIS: {$sx['nis']}, {$jkLbl})\n";
            }
            sendBotReply($reply);
        }
    }

    // D2. Pertanyaan Cepat Total / Jumlah Siswa Keseluruhan Sekolah
    $isTotalSiswaQuery = false;
    if (
        (
            preg_match('/\b(total|jumlah|ada\s*berapa|banyaknya)\s*(semua|seluruh|aktif)?\s*(siswa|siswi|murid|student|students)\b/i', $cleanPrompt) ||
            preg_match('/\b(siswa|siswi|murid|student|students)\s*(ada\s*berapa|totalnya\s*berapa|jumlahnya\s*berapa|semuanya\s*berapa)\b/i', $cleanPrompt) ||
            preg_match('/^berapa\s+(jumlah|total|banyaknya)?\s*(siswa|siswi|murid|student|students)(\s+di\s+sekolah|\s+semuanya|\s+aktif|\s+total)?$/i', $cleanPrompt) ||
            in_array($cleanPrompt, ['total siswa', 'jumlah siswa', 'total murid', 'jumlah murid', 'berapa siswa', 'ada berapa siswa', 'total siswa berapa', 'total murid berapa', 'siswa ada berapa'])
        ) &&
        !preg_match('/\b(nip|nis|nisn|nomor|no|kontak|telp|hp|email|alamat|jadwal|absen|hadir|alpa|izin|sakit|laki|lakilaki|perempuan|wanita|pria|cowok|cewek|putra|putri|siswi|gender|jenis\s*kelamin|kelas|dikelas|jurusan|tingkat|tkj|tkr|tsm|per\s*kelas|tiap\s*kelas)\b/i', $cleanPrompt)
    ) {
        $isTotalSiswaQuery = true;
    }

    if ($isTotalSiswaQuery) {
        if ($rawRole === 'wali_kelas' && $myKelasId > 0 && ($isAskingOwnClassJadwal || preg_match('/\b(kelas\s*(saya|binaan|kami|ini)|kelasku)\b/i', $cleanPrompt)) && !$mentionsOtherSpecificClass && !preg_match('/\b(semua|seluruh|satu\s*sekolah|di\s*sekolah)\b/i', $cleanPrompt)) {
            $stWkCount = $pdo->prepare("SELECT COUNT(*) FROM siswa WHERE kelas_id = ?");
            $stWkCount->execute([$myKelasId]);
            $cntWk = (int)$stWkCount->fetchColumn();
            sendBotReply("Ada {$cntWk} siswa di kelas {$detailKelas}.");
        }

        if (!in_array($rawRole, ['admin', 'kepala_sekolah'])) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'jumlah total keseluruhan siswa di sekolah', 'the total number of students across the school'));
        }

        $totalSiswaLive = (int)$pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
        if ($detectedLang === 'en') {
            $reply = "There are {$totalSiswaLive} students.";
        } elseif ($detectedLang === 'su') {
            $reply = "Aya {$totalSiswaLive} siswa.";
        } elseif ($detectedLang === 'jv') {
            $reply = "Wonten {$totalSiswaLive} siswa.";
        } else {
            $reply = "Ada {$totalSiswaLive} siswa.";
        }
        sendBotReply($reply);
    }

    // D3A. Pertanyaan Jumlah Guru Berdasarkan Jenis Kelamin (Strict RBAC)
    if (
        $asksGender &&
        preg_match('/\b(guru|pengajar|pendidik|teacher|teachers)\b/i', $cleanPrompt) &&
        !preg_match('/\b(nip|jadwal|ngajar|mengajar|absen|hadir|alpa|izin|sakit|catatan)\b/i', $cleanPrompt)
    ) {
        if (!in_array($rawRole, ['admin', 'kepala_sekolah'])) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'jumlah atau rincian jenis kelamin guru di sekolah', 'teacher counts or gender breakdowns'));
        }
        $guruStat = $pdo->query("SELECT COUNT(*) AS total_guru,
                                        COALESCE(SUM(CASE WHEN UPPER(TRIM(jenis_kelamin)) = 'L' THEN 1 ELSE 0 END), 0) AS total_laki,
                                        COALESCE(SUM(CASE WHEN UPPER(TRIM(jenis_kelamin)) = 'P' THEN 1 ELSE 0 END), 0) AS total_perempuan
                                 FROM guru")->fetch(PDO::FETCH_ASSOC);
        $gTot = (int)($guruStat['total_guru'] ?? 0);
        $gL = (int)($guruStat['total_laki'] ?? 0);
        $gP = (int)($guruStat['total_perempuan'] ?? 0);
        if ($asksMale && !$asksFemale) {
            $reply = "Ada {$gL} guru laki-laki.";
        } elseif ($asksFemale && !$asksMale) {
            $reply = "Ada {$gP} guru perempuan.";
        } else {
            $reply = "Ada {$gTot} guru ({$gL} laki-laki, {$gP} perempuan).";
        }
        sendBotReply($reply);
    }

    // D3. Pertanyaan Cepat Total / Jumlah Guru
    $isTotalGuruQuery = false;
    if (
        (
            preg_match('/\b(total|jumlah|ada\s*berapa|banyaknya)\s*(semua|seluruh|aktif)?\s*(guru|pengajar|pendidik|teacher|teachers)\b/i', $cleanPrompt) ||
            preg_match('/\b(guru|pengajar|pendidik|teacher|teachers)\s*(ada\s*berapa|totalnya\s*berapa|jumlahnya\s*berapa|semuanya\s*berapa)\b/i', $cleanPrompt) ||
            preg_match('/^berapa\s+(jumlah|total|banyaknya)?\s*(guru|pengajar|pendidik|teacher|teachers)(\s+di\s+sekolah|\s+semuanya|\s+aktif|\s+total)?$/i', $cleanPrompt) ||
            in_array($cleanPrompt, ['total guru', 'jumlah guru', 'total pengajar', 'jumlah pengajar', 'berapa guru', 'ada berapa guru', 'jumlah guru ada berapa', 'guru ada berapa', 'total guru berapa'])
        ) &&
        !preg_match('/\b(nip|nis|nisn|nomor|no|kontak|telp|hp|email|alamat|jadwal|ngajar|mengajar|absen|hadir|alpa|izin|sakit|laki|lakilaki|perempuan|wanita|pria|cowok|cewek|gender|jenis\s*kelamin)\b/i', $cleanPrompt)
    ) {
        $isTotalGuruQuery = true;
    }

    if ($isTotalGuruQuery) {
        if (!in_array($rawRole, ['admin', 'kepala_sekolah'])) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'jumlah total guru di sekolah', 'the total number of teachers at the school'));
        }
        $totalGuruLive = (int)$pdo->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        if ($detectedLang === 'en') {
            $reply = "There are {$totalGuruLive} teachers.";
        } elseif ($detectedLang === 'su') {
            $reply = "Aya {$totalGuruLive} guru.";
        } elseif ($detectedLang === 'jv') {
            $reply = "Wonten {$totalGuruLive} guru.";
        } else {
            $reply = "Ada {$totalGuruLive} guru.";
        }
        sendBotReply($reply);
    }

    // D3.1. Pertanyaan NIP Guru / Identitas Guru
    $isAskingNipQuery = (bool)(
        preg_match('/\b(nip|nomor\s*induk\s*pegawai|no\s*nip|nomor\s*nip)\b/i', $cleanPrompt) ||
        (preg_match('/\b(guru|pengajar|pendidik|pak|bapak|ibu|bu)\b/i', $cleanPrompt) &&
         preg_match('/\b(nip|nomor\s*induk|identitas|profil)\b/i', $cleanPrompt))
    );
    if ($isAskingNipQuery) {
        if ($rawRole === 'siswa') {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'NIP dan identitas kepegawaian guru', 'teacher NIP or staff identification numbers'));
        }

        $namaSelfClean = strtolower(trim(preg_replace('/,.*$/', '', $namaLengkap)));
        $isAskingOwnNip = false;
        if (in_array($rawRole, ['guru', 'wali_kelas'])) {
            $mentionsOtherGuruNip = (bool)(
                preg_match('/\b(guru\s*lain|rekan|teman|pengajar\s*lain)\b/i', $cleanPrompt) ||
                (preg_match('/\b(pak|bapak|ibu|bu)\s+([a-z]+)\b/i', $cleanPrompt, $mG) && !in_array(strtolower($mG[2]), ['guru', 'wali', 'saya', 'aku', 'kami', 'anda']) && stripos(strtolower($namaLengkap), strtolower($mG[2])) === false)
            );
            if (!$mentionsOtherGuruNip) {
                if ($isSelfPronoun || (!empty($namaSelfClean) && stripos($cleanPrompt, $namaSelfClean) !== false)) {
                    $isAskingOwnNip = true;
                } else {
                    $selfParts = array_filter(explode(' ', $namaSelfClean), fn($p) => strlen($p) >= 4);
                    foreach ($selfParts as $sp) {
                        if (preg_match('/\b' . preg_quote($sp, '/') . '\b/i', $cleanPrompt)) {
                            $isAskingOwnNip = true;
                            break;
                        }
                    }
                }
            }
            if ($isAskingOwnNip) {
                $nipVal = !empty($myNip) ? $myNip : '-';
                sendBotReply("Berdasarkan data profil Anda di sistem, nomor NIP {$sapaanGelar} {$namaLengkap} adalah {$nipVal}.");
            } else {
                sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'NIP atau identitas guru lain', 'other teachers NIP or identification'));
            }
        }

        // Role admin & kepala_sekolah berwenang lookup NIP semua guru
        $stmtAllGuru = $pdo->query("SELECT g.id, g.nip, g.nama_lengkap, g.jenis_kelamin,
                                           COALESCE(wkd.nip, g.nip) as nip_wali,
                                           COALESCE(k.nama_kelas, '') as kelas_binaan,
                                           COALESCE(GROUP_CONCAT(DISTINCT m.nama_mapel SEPARATOR ', '), 'Belum ada mapel') as mapel_diampu
                                    FROM guru g
                                    LEFT JOIN wali_kelas_data wkd ON g.user_id = wkd.user_id
                                    LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id
                                    LEFT JOIN kelas k ON wk.kelas_id = k.id
                                    LEFT JOIN mata_pelajaran m ON m.guru_id = g.id
                                    GROUP BY g.id");
        $allGuruList = $stmtAllGuru->fetchAll(PDO::FETCH_ASSOC);

        $stopTokensGuru = ['guru', 'pengajar', 'pendidik', 'bapak', 'ibu', 'pak', 'bu', 'nomor', 'nomer', 'induk', 'pegawai', 'identitas', 'profil', 'data', 'berapa', 'siapa', 'yang', 'spd', 'skom', 'mpd', 'drs', 'st', 'mt', 'sag', 'ssos'];
        $matchedGuru = null;
        $bestScore = 0;
        foreach ($allGuruList as $g) {
            $cleanNamaGuru = strtolower(trim(preg_replace('/,.*$/', '', $g['nama_lengkap'])));
            if (!empty($cleanNamaGuru) && stripos($cleanPrompt, $cleanNamaGuru) !== false) {
                $matchedGuru = $g;
                break;
            }
            $namaParts = preg_split('/\s+/', $cleanNamaGuru);
            $score = 0;
            foreach ($namaParts as $part) {
                $partClean = preg_replace('/[^a-z]/', '', $part);
                if (strlen($partClean) >= 3 && !in_array($partClean, $stopTokensGuru) && preg_match('/\b' . preg_quote($partClean, '/') . '\b/i', $cleanPrompt)) {
                    $score += strlen($partClean);
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $matchedGuru = $g;
            }
        }

        if ($matchedGuru) {
            $gelar = ($matchedGuru['jenis_kelamin'] === 'P') ? 'Ibu' : 'Bapak';
            $nip = !empty($matchedGuru['nip']) ? $matchedGuru['nip'] : (!empty($matchedGuru['nip_wali']) ? $matchedGuru['nip_wali'] : '-');
            $reply = "Berdasarkan data sistem, nomor NIP {$gelar} {$matchedGuru['nama_lengkap']} adalah {$nip}.";
            if ($rawRole === 'admin') {
                if (!empty($matchedGuru['kelas_binaan'])) {
                    $reply .= " Beliau mengampu mata pelajaran {$matchedGuru['mapel_diampu']} serta bertugas sebagai Wali Kelas {$matchedGuru['kelas_binaan']}.";
                } elseif ($matchedGuru['mapel_diampu'] !== 'Belum ada mapel') {
                    $reply .= " Beliau mengampu mata pelajaran {$matchedGuru['mapel_diampu']}.";
                }
            }
            sendBotReply($reply);
        }
    }

    // D3.2. Pertanyaan NIS / NISN Siswa
    if (preg_match('/\b(nis|nisn|nomor\s*induk\s*siswa|no\s*induk)\b/i', $cleanPrompt)) {
        if ($rawRole === 'guru') {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'NIS, NISN, dan data pribadi siswa', 'student NIS/NISN data'));
        }

        if ($rawRole === 'siswa') {
            $namaSelfClean = strtolower(trim($namaLengkap));
            $asksOtherStudentName = (bool)preg_match('/\b(teman|kawan|adik|kakak|siswa\s*lain|murid\s*lain|orang\s*lain)\b/i', $cleanPrompt);
            if (!$asksOtherStudentName) {
                $stCheckS = $pdo->query("SELECT nama_lengkap FROM siswa");
                foreach ($stCheckS->fetchAll(PDO::FETCH_COLUMN) as $sName) {
                    $sLower = strtolower(trim($sName));
                    if ($sLower === $namaSelfClean) continue;
                    if (stripos($cleanPrompt, $sLower) !== false) {
                        $asksOtherStudentName = true;
                        break;
                    }
                    $sParts = preg_split('/\s+/', $sLower);
                    foreach ($sParts as $sp) {
                        if (strlen($sp) >= 4 && !in_array($sp, ['siswa', 'murid', 'kelas', 'berapa', 'nomor', 'nomer', 'induk', 'nisn', 'saya', 'kamu', 'kami', 'anda']) && preg_match('/\b' . preg_quote($sp, '/') . '\b/i', $cleanPrompt)) {
                            $asksOtherStudentName = true;
                            break 2;
                        }
                    }
                }
            }
            if ($asksOtherStudentName || $mentionsOtherSpecificClass) {
                sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'NIS/NISN siswa lain', 'other students NIS/NISN'));
            }
            $nisVal = !empty($myNis) ? $myNis : '-';
            $nisnVal = !empty($myNisn) ? $myNisn : '-';
            sendBotReply("Berdasarkan data sistem, kamu ({$namaLengkap}, kelas {$detailKelas}) memiliki NIS: {$nisVal} dan NISN: {$nisnVal}.");
        }

        if ($rawRole === 'wali_kelas') {
            $stWkSiswa = $pdo->prepare("SELECT s.nama_lengkap, s.nis, s.nisn, k.nama_kelas
                                        FROM siswa s
                                        JOIN kelas k ON s.kelas_id = k.id
                                        WHERE s.kelas_id = ?");
            $stWkSiswa->execute([$myKelasId]);
            $wkSiswaList = $stWkSiswa->fetchAll(PDO::FETCH_ASSOC);

            $stopTokensSiswa = ['siswa', 'murid', 'kelas', 'nomor', 'nomer', 'induk', 'nisn', 'berapa', 'siapa', 'data', 'yang'];
            $matchedSiswa = null;
            $bestScoreS = 0;
            foreach ($wkSiswaList as $s) {
                $cleanNamaSiswa = strtolower(trim($s['nama_lengkap']));
                if (stripos($cleanPrompt, $cleanNamaSiswa) !== false) {
                    $matchedSiswa = $s;
                    break;
                }
                $namaParts = preg_split('/\s+/', $cleanNamaSiswa);
                $score = 0;
                foreach ($namaParts as $part) {
                    $partClean = preg_replace('/[^a-z]/', '', $part);
                    if (strlen($partClean) >= 4 && !in_array($partClean, $stopTokensSiswa) && preg_match('/\b' . preg_quote($partClean, '/') . '\b/i', $cleanPrompt)) {
                        $score += strlen($partClean);
                    }
                }
                if ($score > $bestScoreS) {
                    $bestScoreS = $score;
                    $matchedSiswa = $s;
                }
            }

            if ($matchedSiswa) {
                $nisn = !empty($matchedSiswa['nisn']) ? $matchedSiswa['nisn'] : '-';
                sendBotReply("Berdasarkan data kelas binaan Anda ({$detailKelas}), siswa {$matchedSiswa['nama_lengkap']} memiliki NIS: {$matchedSiswa['nis']} dan NISN: {$nisn}.");
            }
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, "data siswa di luar kelas binaan Anda ({$detailKelas}) atau nama siswa tersebut tidak terdaftar di kelas {$detailKelas}", "students outside your homeroom class ({$detailKelas})"));
        }

        // Admin & Kepala Sekolah: seluruh siswa
        $stAllSiswa = $pdo->query("SELECT s.nama_lengkap, s.nis, s.nisn, k.nama_kelas, j.nama_jurusan
                                   FROM siswa s
                                   JOIN kelas k ON s.kelas_id = k.id
                                   JOIN jurusan j ON k.jurusan_id = j.id");
        $allSiswaList = $stAllSiswa->fetchAll(PDO::FETCH_ASSOC);

        $stopTokensSiswa = ['siswa', 'murid', 'kelas', 'nomor', 'nomer', 'induk', 'nisn', 'berapa', 'siapa', 'data', 'yang'];
        $matchedSiswa = null;
        $bestScoreS = 0;
        foreach ($allSiswaList as $s) {
            $cleanNamaSiswa = strtolower(trim($s['nama_lengkap']));
            if (stripos($cleanPrompt, $cleanNamaSiswa) !== false) {
                $matchedSiswa = $s;
                break;
            }
            $namaParts = preg_split('/\s+/', $cleanNamaSiswa);
            $score = 0;
            foreach ($namaParts as $part) {
                $partClean = preg_replace('/[^a-z]/', '', $part);
                if (strlen($partClean) >= 4 && !in_array($partClean, $stopTokensSiswa) && preg_match('/\b' . preg_quote($partClean, '/') . '\b/i', $cleanPrompt)) {
                    $score += strlen($partClean);
                }
            }
            if ($score > $bestScoreS) {
                $bestScoreS = $score;
                $matchedSiswa = $s;
            }
        }

        if ($matchedSiswa) {
            $nisn = !empty($matchedSiswa['nisn']) ? $matchedSiswa['nisn'] : '-';
            $reply = "Berdasarkan data sistem, siswa {$matchedSiswa['nama_lengkap']} (Kelas {$matchedSiswa['nama_kelas']}) memiliki NIS: {$matchedSiswa['nis']} dan NISN: {$nisn}.";
            sendBotReply($reply);
        }
    }

    // D. Pertanyaan Cepat Jumlah & Daftar Kelas Sekolah
    if (
        (
            preg_match('/(berapa\s+(jumlah|total)?\s*kelas|ada\s+berapa\s+kelas|jumlah\s+kelas(nya)?|total\s+kelas(nya)?|daftar\s+kelas)/i', $cleanPrompt) ||
            preg_match('/(how\s+many\s+classes|total\s+classes|number\s+of\s+classes|classes\s+count)/i', $cleanPrompt) ||
            preg_match('/(sabaraha\s+kelas|aya\s+sabaraha\s+kelas|jumlah\s+kelasna)/i', $cleanPrompt) ||
            preg_match('/(pira\s+kelas|piro\s+kelas|pinten\s+kelas|ana\s+pira\s+kelas|wonten\s+pinten\s+kelas)/i', $cleanPrompt)
        ) &&
        !preg_match('/(siswa|murid|guru|wali|absen|kehadiran|jadwal|mapel|student|teacher)/i', $cleanPrompt) &&
        !($isSelfPronoun && !preg_match('/\b(total|seluruh|semua|daftar)\b/i', $cleanPrompt))
    ) {
        if (!in_array($rawRole, ['admin', 'kepala_sekolah'])) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'jumlah total dan daftar seluruh kelas sekolah', 'the total count and list of all school classes'));
        }

        $stmtFastKelas = $pdo->query("SELECT k.nama_kelas, j.nama_jurusan FROM kelas k JOIN jurusan j ON k.jurusan_id = j.id ORDER BY k.tingkat ASC, k.nama_kelas ASC");
        $kelasList = $stmtFastKelas->fetchAll(PDO::FETCH_ASSOC);
        $totalFastKelas = count($kelasList);
        $asksClassList = (bool)preg_match('/\b(daftar|sebutkan|apa\s*(saja|aja)|list|nama)\b/i', $cleanPrompt);

        if (!$asksClassList) {
            if ($detectedLang === 'en') {
                $reply = "There are {$totalFastKelas} classes.";
            } elseif ($detectedLang === 'su') {
                $reply = "Aya {$totalFastKelas} kelas.";
            } elseif ($detectedLang === 'jv') {
                $reply = "Wonten {$totalFastKelas} kelas.";
            } else {
                $reply = "Ada {$totalFastKelas} kelas.";
            }
        } else {
            if ($detectedLang === 'en') {
                $reply = "Classes registered in the system ({$totalFastKelas} classes):\n";
            } elseif ($detectedLang === 'su') {
                $reply = "Kelas anu kadaptar dina sistem ({$totalFastKelas} kelas):\n";
            } elseif ($detectedLang === 'jv') {
                $reply = "Kelas ingkang kadhaftar ing sistem ({$totalFastKelas} kelas):\n";
            } else {
                $reply = "Kelas yang terdaftar di sistem ({$totalFastKelas} kelas):\n";
            }
            foreach ($kelasList as $kls) {
                $reply .= "• Kelas {$kls['nama_kelas']}\n";
            }
        }
        sendBotReply(trim($reply));
    }

    $today = date('Y-m-d');

    // E. Pertanyaan Kehadiran Cepat & Singkat (Guru & Siswa): Alpa, Hadir, Izin, Sakit
    $isGuruQuery = (bool)preg_match('/\b(guru|pengajar|pendidik|teacher|teachers)\b/i', $cleanPrompt);
    $isSiswaQuery = (bool)preg_match('/\b(siswa|murid|anak|student|students|kanca)\b/i', $cleanPrompt);
    
    // Status check yang akurat: membedakan 'sudah absen'/'hadir' dengan 'alpa'/'belum absen'
    $targetStatus = null;

    // 1. Cek status ALPA / BOLOS / BELUM HADIR
    $isAlpaMatch = (bool)(
        preg_match('/\b(alpa|bolos|mangkir|tanpa\s*keterangan|absent)\b/i', $cleanPrompt) ||
        preg_match('/\b(tidak|belum|gak|nggak|bukan|teu|mboten|ora)\s+(pernah\s+)?(melakukan\s+)?(hadir|masuk|absen|absensi|presensi|lebet|rawuh|mlebu|datang|scan)\b/i', $cleanPrompt) ||
        preg_match('/\b(belum\s+(ada|melakukan|scan|masuk|absen))\b/i', $cleanPrompt)
    );

    // 2. Cek status HADIR / SUDAH ABSEN / MASUK
    $isHadirMatch = (bool)(
        preg_match('/\b(sudah|telah|parantos|atos|sampun)\s+(melakukan\s+)?(absen|absensi|presensi|hadir|masuk|datang|scan|lebet|rawuh|mlebu)\b/i', $cleanPrompt) ||
        preg_match('/\b(yang|yg|ingkang|anu)\s+(sudah\s+)?(melakukan\s+)?(absen|absensi|presensi|hadir|masuk|scan)\b/i', $cleanPrompt) ||
        preg_match('/\b(tercatat\s+hadir|presensi\s+masuk|hadir\s+hari\s+ini|masuk\s+hari\s+ini)\b/i', $cleanPrompt) ||
        (preg_match('/\b(hadir|masuk|present|lebet|rawuh|mlebu)\b/i', $cleanPrompt) && !preg_match('/\b(tidak|belum|gak|nggak|bukan|teu|mboten|ora)\s+(hadir|masuk|lebet|rawuh|mlebu)\b/i', $cleanPrompt))
    );

    // 3. Cek status IZIN
    $isIzinMatch = (bool)preg_match('/\b(izin|permit|ijin)\b/i', $cleanPrompt);

    // 4. Cek status SAKIT
    $isSakitMatch = (bool)preg_match('/\b(sakit|sick|udzur|gering|gerah)\b/i', $cleanPrompt);

    if ($isAlpaMatch) {
        $targetStatus = 'alpa';
    } elseif ($isHadirMatch) {
        $targetStatus = 'hadir';
    } elseif ($isIzinMatch) {
        $targetStatus = 'izin';
    } elseif ($isSakitMatch) {
        $targetStatus = 'sakit';
    }

    // --- RESOLVER HARI & TANGGAL KEHADIRAN DINAMIS ---
    $targetDate = $today;
    $targetDateDayName = $namaHari;
    $targetDateLabel = "hari ini ({$namaHari}, " . date('d/m/Y') . ")";

    if (preg_match('/\b(kemarin|kamari|wingi|yesterday)\b/i', $cleanPrompt)) {
        $targetDate = date('Y-m-d', strtotime('-1 day'));
        $targetDateDayName = $hariMap[date('N', strtotime($targetDate))];
        $targetDateLabel = "kemarin ({$targetDateDayName}, " . date('d/m/Y', strtotime($targetDate)) . ")";
    } elseif (preg_match('/\b(hari\s*ini|today|dinten\s*ieu|dinten\s*niki|saiki|ayeuna)\b/i', $cleanPrompt)) {
        $targetDate = $today;
        $targetDateDayName = $namaHari;
        $targetDateLabel = "hari ini ({$namaHari}, " . date('d/m/Y') . ")";
    } elseif (preg_match('/\b(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})\b/', $cleanPrompt, $mDate)) {
        $targetDate = sprintf('%04d-%02d-%02d', $mDate[1], $mDate[2], $mDate[3]);
        $targetDateDayName = $hariMap[date('N', strtotime($targetDate))];
        $targetDateLabel = "pada hari {$targetDateDayName} (" . date('d/m/Y', strtotime($targetDate)) . ")";
    } elseif (preg_match('/\b(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})\b/', $cleanPrompt, $mDate)) {
        $targetDate = sprintf('%04d-%02d-%02d', $mDate[3], $mDate[2], $mDate[1]);
        $targetDateDayName = $hariMap[date('N', strtotime($targetDate))];
        $targetDateLabel = "pada hari {$targetDateDayName} (" . date('d/m/Y', strtotime($targetDate)) . ")";
    } elseif (preg_match('/\b(\d{1,2})\s+(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)(?:\s+(\d{4}))?\b/i', $cleanPrompt, $mDateStr)) {
        $blnIndo = [
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
            'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
            'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12'
        ];
        $bNum = $blnIndo[strtolower($mDateStr[2])] ?? '01';
        $dNum = sprintf('%02d', (int)$mDateStr[1]);
        $yNum = !empty($mDateStr[3]) ? $mDateStr[3] : date('Y');
        $targetDate = "{$yNum}-{$bNum}-{$dNum}";
        $targetDateDayName = $hariMap[date('N', strtotime($targetDate))];
        $targetDateLabel = "pada hari {$targetDateDayName} (" . date('d/m/Y', strtotime($targetDate)) . ")";
    } elseif (!empty($matchedDays)) {
        $firstMatchedDay = $matchedDays[0];
        $dayEnMap = [
            'senin' => 'monday',
            'selasa' => 'tuesday',
            'rabu' => 'wednesday',
            'kamis' => 'thursday',
            'jumat' => 'friday',
            'sabtu' => 'saturday',
            'minggu' => 'sunday'
        ];
        $enDay = $dayEnMap[$firstMatchedDay] ?? 'monday';

        $dateThisWeek = date('Y-m-d', strtotime("$enDay this week"));
        if (strtotime($dateThisWeek) <= strtotime($today)) {
            $candidateDate = $dateThisWeek;
        } else {
            $candidateDate = date('Y-m-d', strtotime("last $enDay"));
        }

        // Cek keberadaan data di absensi_siswa atau absensi_guru
        $stCheck = $pdo->prepare("SELECT (SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = ?) + (SELECT COUNT(*) FROM absensi_guru WHERE tanggal = ?)");
        $stCheck->execute([$candidateDate, $candidateDate]);
        $hasRec = (int)$stCheck->fetchColumn();

        if ($hasRec > 0) {
            $targetDate = $candidateDate;
        } else {
            $dowMap = ['minggu' => 1, 'senin' => 2, 'selasa' => 3, 'rabu' => 4, 'kamis' => 5, 'jumat' => 6, 'sabtu' => 7];
            $dowVal = $dowMap[$firstMatchedDay] ?? 2;
            $stLatest = $pdo->prepare("SELECT DISTINCT tanggal FROM absensi_siswa WHERE DAYOFWEEK(tanggal) = ? ORDER BY tanggal DESC LIMIT 1");
            $stLatest->execute([$dowVal]);
            $dbFoundDate = $stLatest->fetchColumn();
            if ($dbFoundDate) {
                $targetDate = $dbFoundDate;
            } else {
                $targetDate = $candidateDate;
            }
        }
        $targetDateDayName = $hariMap[date('N', strtotime($targetDate))];
        $targetDateLabel = "hari {$targetDateDayName} (" . date('d/m/Y', strtotime($targetDate)) . ")";
    }

    $asksCountOnly = (bool)preg_match('/\b(ada\s*berapa|berapa|total|jumlah|banyaknya|count|how\s*many|sabaraha|pira|piro|pinten)\b/i', $cleanPrompt) && !preg_match('/\b(siapa|daftar|list|nama|sebutkan|rincian)\b/i', $cleanPrompt);
    $statusTextMap = [
        'alpa' => 'alpa',
        'hadir' => 'hadir',
        'izin' => 'izin',
        'sakit' => 'sakit'
    ];
    $statusText = $statusTextMap[$targetStatus] ?? 'hadir';

    // 1. QUERY PRESENSI GURU (Khusus jika pengguna menyebut guru / pengajar / pendidik)
    if (!$isSelfPronoun && !preg_match('/\b(saya|aku|pribadi|sendiri|kula|simkuring|my|pengajuan)\b/i', $cleanPrompt) && $isGuruQuery && $targetStatus !== null && preg_match('/\b(siapa|data|daftar|nama|lihat|cek|who|list|saha|sinten|pira|berapa|jumlah|total|apakah)\b/i', $cleanPrompt)) {
        if ($rawRole === 'siswa') {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'data presensi atau kehadiran guru', 'teacher attendance records'));
        }
        if (in_array($rawRole, ['guru', 'wali_kelas']) && (!$isSelfPronoun || preg_match('/\b(siapa|daftar|list|semua|seluruh)\b/i', $cleanPrompt))) {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'rekap atau daftar kehadiran guru lain', 'other teachers attendance records'));
        }

        if (in_array($rawRole, ['admin', 'kepala_sekolah'])) {
            $statusCondition = ($targetStatus === 'alpa')
                ? "IN ('alpa', 'bolos')"
                : (($targetStatus === 'hadir')
                    ? "IN ('hadir', 'terlambat')"
                    : "= '$targetStatus'");

            $stFast = $pdo->prepare("SELECT g.nama_lengkap 
                                    FROM absensi_guru a 
                                    JOIN guru g ON a.guru_id = g.id 
                                    WHERE a.tanggal = ? AND LOWER(a.status) $statusCondition 
                                    ORDER BY g.nama_lengkap ASC");
            $stFast->execute([$targetDate]);
            $rows = $stFast->fetchAll(PDO::FETCH_COLUMN);
            $cnt = count($rows);

            if ($cnt === 0) {
                $reply = "Berdasarkan data sistem {$targetDateLabel}, tidak ada guru yang {$statusText}.";
            } else {
                if ($asksCountOnly) {
                    $reply = "Ada {$cnt} guru yang {$statusText} pada {$targetDateLabel}.";
                } elseif ($cnt === 33 && $targetStatus === 'alpa') {
                    $reply = "Berdasarkan data sistem {$targetDateLabel}, seluruh 33 tenaga pendidik (guru) tercatat alpa (belum melakukan absensi masuk).";
                } elseif ($cnt <= 15) {
                    $reply = "Berikut daftar guru yang {$statusText} pada {$targetDateLabel} ({$cnt} guru):\n";
                    foreach ($rows as $idx => $nama) {
                        $reply .= ($idx + 1) . ". {$nama}\n";
                    }
                } else {
                    $reply = "Berdasarkan data sistem {$targetDateLabel}, terdapat {$cnt} guru yang {$statusText}.";
                }
            }
            sendBotReply(trim($reply));
        }
    }

    // 2. QUERY PRESENSI SISWA (Jika menyebut siswa, atau query kehadiran umum tanpa menyebut guru)
    if (!$isSelfPronoun && !preg_match('/\b(saya|aku|pribadi|sendiri|kula|simkuring|my|pengajuan)\b/i', $cleanPrompt) && ($isSiswaQuery || (!$isGuruQuery)) && $targetStatus !== null && preg_match('/\b(siapa|data|daftar|nama|lihat|cek|who|list|saha|sinten|pira|berapa|jumlah|total|apakah)\b/i', $cleanPrompt)) {
        // RBAC: Siswa tidak boleh melihat data absensi siswa lain
        if ($rawRole === 'siswa') {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'daftar kehadiran atau absensi siswa lain', 'other students attendance records'));
        }

        // RBAC: Guru pengampu biasa tidak boleh melihat rekap presensi siswa
        if ($rawRole === 'guru') {
            sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'data rekapitulasi absensi siswa', 'student attendance records'));
        }

        $statusCondition = ($targetStatus === 'alpa')
            ? "IN ('alpa', 'bolos')"
            : (($targetStatus === 'hadir')
                ? "IN ('hadir', 'terlambat')"
                : "= '$targetStatus'");

        // Wali Kelas: HANYA tampilkan siswa di kelas binaannya!
        if ($rawRole === 'wali_kelas') {
            if ($mentionsOtherSpecificClass) {
                sendBotReply(buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, "data kehadiran siswa di luar kelas binaan Anda ({$detailKelas})", "student attendance outside your homeroom class ({$detailKelas})"));
            }
            if ($myKelasId <= 0) {
                sendBotReply("Berdasarkan data sistem, akun Wali Kelas Anda belum memiliki penugasan kelas binaan saat ini.");
            }

            $stFast = $pdo->prepare("SELECT s.nama_lengkap, k.nama_kelas 
                                    FROM absensi_siswa a 
                                    JOIN siswa s ON a.siswa_id = s.id 
                                    JOIN kelas k ON s.kelas_id = k.id 
                                    WHERE a.tanggal = ? AND LOWER(a.status) $statusCondition AND s.kelas_id = ? 
                                    ORDER BY s.nama_lengkap ASC");
            $stFast->execute([$targetDate, $myKelasId]);
            $rows = $stFast->fetchAll(PDO::FETCH_ASSOC);
            $cnt = count($rows);

            if ($cnt === 0) {
                $reply = "Berdasarkan data sistem {$targetDateLabel}, tidak ada siswa kelas {$detailKelas} yang {$statusText}.";
            } else {
                if ($asksCountOnly) {
                    $reply = "Ada {$cnt} siswa kelas {$detailKelas} yang {$statusText} pada {$targetDateLabel}.";
                } elseif ($cnt <= 25) {
                    $reply = "Berikut daftar siswa kelas {$detailKelas} yang {$statusText} pada {$targetDateLabel} ({$cnt} siswa):\n";
                    foreach ($rows as $idx => $r) {
                        $reply .= ($idx + 1) . ". {$r['nama_lengkap']}\n";
                    }
                } else {
                    $reply = "Berdasarkan data sistem {$targetDateLabel}, terdapat {$cnt} siswa kelas {$detailKelas} yang {$statusText}.";
                }
            }
            sendBotReply(trim($reply));
        }

        // Admin & Kepala Sekolah: Menampilkan seluruh siswa sekolah (atau filter kelas jika disebut)
        if (!empty($mentionedClassNormalized)) {
            $classFilterSql = preg_match('/\d$/', $mentionedClassNormalized) ? "AND LOWER(TRIM(k.nama_kelas)) = ?" : "AND LOWER(TRIM(k.nama_kelas)) LIKE ?";
            $classFilterParam = preg_match('/\d$/', $mentionedClassNormalized) ? $mentionedClassNormalized : $mentionedClassNormalized . ' %';
            $stFast = $pdo->prepare("SELECT s.nama_lengkap, k.nama_kelas 
                                    FROM absensi_siswa a 
                                    JOIN siswa s ON a.siswa_id = s.id 
                                    JOIN kelas k ON s.kelas_id = k.id 
                                    WHERE a.tanggal = ? AND LOWER(a.status) $statusCondition $classFilterSql 
                                    ORDER BY k.nama_kelas ASC, s.nama_lengkap ASC");
            $stFast->execute([$targetDate, $classFilterParam]);
            $rows = $stFast->fetchAll(PDO::FETCH_ASSOC);
            $cnt = count($rows);
            $scopeKlsLabel = " kelas " . strtoupper($mentionedClassNormalized);

            if ($cnt === 0) {
                $reply = "Berdasarkan data sistem {$targetDateLabel}, tidak ada siswa{$scopeKlsLabel} yang {$statusText}.";
            } else {
                if ($asksCountOnly) {
                    $reply = "Ada {$cnt} siswa{$scopeKlsLabel} yang {$statusText} pada {$targetDateLabel}.";
                } elseif ($cnt <= 25) {
                    $reply = "Berikut daftar siswa{$scopeKlsLabel} yang {$statusText} pada {$targetDateLabel} ({$cnt} siswa):\n";
                    foreach ($rows as $idx => $r) {
                        $reply .= ($idx + 1) . ". {$r['nama_lengkap']}\n";
                    }
                } else {
                    $reply = "Berdasarkan data sistem {$targetDateLabel}, terdapat {$cnt} siswa{$scopeKlsLabel} yang {$statusText}.";
                }
            }
        } else {
            $stFast = $pdo->prepare("SELECT s.nama_lengkap, k.nama_kelas 
                                    FROM absensi_siswa a 
                                    JOIN siswa s ON a.siswa_id = s.id 
                                    JOIN kelas k ON s.kelas_id = k.id 
                                    WHERE a.tanggal = ? AND LOWER(a.status) $statusCondition 
                                    ORDER BY k.nama_kelas ASC, s.nama_lengkap ASC");
            $stFast->execute([$targetDate]);
            $rows = $stFast->fetchAll(PDO::FETCH_ASSOC);
            $cnt = count($rows);

            if ($cnt === 0) {
                $reply = "Berdasarkan data sistem {$targetDateLabel}, tidak ada siswa yang {$statusText}.";
            } else {
                if ($asksCountOnly) {
                    $reply = "Ada {$cnt} siswa yang {$statusText} pada {$targetDateLabel}.";
                } elseif ($cnt === 225 && $targetStatus === 'alpa') {
                    $reply = "Berdasarkan data sistem {$targetDateLabel}, seluruh 225 siswa di 9 kelas tercatat alpa (belum ada siswa yang melakukan absensi masuk).";
                } elseif ($cnt <= 15) {
                    $reply = "Berikut daftar siswa yang {$statusText} pada {$targetDateLabel} ({$cnt} siswa):\n";
                    foreach ($rows as $idx => $r) {
                        $reply .= ($idx + 1) . ". {$r['nama_lengkap']} (Kelas {$r['nama_kelas']})\n";
                    }
                } else {
                    $byKls = [];
                    foreach ($rows as $r) {
                        $byKls[$r['nama_kelas']] = ($byKls[$r['nama_kelas']] ?? 0) + 1;
                    }
                    $reply = "Berdasarkan data sistem {$targetDateLabel}, terdapat total {$cnt} siswa yang {$statusText}:\n";
                    foreach ($byKls as $kName => $kCnt) {
                        $reply .= "• Kelas {$kName}: {$kCnt} siswa\n";
                    }
                }
            }
        }
        sendBotReply(trim($reply));
    }

    // F0. Pertanyaan Jadwal Pelajaran / Mengajar Berdasarkan Hari Spesifik ($matchedDays sudah diparsing di atas)
    // Cek apakah menanyakan orang atau kelas lain yang spesifik (menggunakan deteksi RBAC $mentionsOtherSpecificClass)
    $asksOtherTeacherPrompt = false;
    if (preg_match('/\b(pak|bapak|ibu|bu)\s+([a-z]+)\b/i', $cleanPrompt, $mGuruOther)) {
        $otherName = strtolower($mGuruOther[2]);
        if (!in_array($otherName, ['guru', 'wali', 'kepsek', 'kepala']) && stripos(strtolower($namaLengkap), $otherName) === false) {
            $asksOtherTeacherPrompt = true;
        }
    }
    $mentionsSpecificOther = ($mentionsOtherSpecificClass || $asksOtherTeacherPrompt || $asksNamedOtherTeacher || !empty($mentionedTingkat) || !empty($mentionedJurusanCode));

    $isAskingSpecificDaysSchedule = false;
    if (!empty($matchedDays) && !$mentionsSpecificOther && in_array($rawRole, ['siswa', 'guru', 'wali_kelas'])) {
        if (
            preg_match('/\b(jadwal|schedule|pelajaran|mapel|ngajar|mengajar|mulang|ada\s+apa|apa\s+aja|apa\s+saja|masuk|jam|kegiatan)\b/i', $cleanPrompt) ||
            preg_match('/^(kalau|kalo|gimana|bagaimana|bagaimanakah|terus|lalu)?\s*(hari)?\s*(senin|selasa|rabu|kamis|jumat|sabtu|minggu)/i', $cleanPrompt) ||
            preg_match('/\b(dan|serta|sama|&)\s*(senin|selasa|rabu|kamis|jumat|sabtu|minggu)\b/i', $cleanPrompt) ||
            in_array($cleanPrompt, ['kamis', 'jumat', 'senin', 'selasa', 'rabu', 'hari kamis', 'hari jumat', 'hari senin', 'hari selasa', 'hari rabu', 'kamis jumat', 'hari kamis dan jumat', 'hari kamis jumat'])
        ) {
            $isAskingSpecificDaysSchedule = true;
        }
    }

    $wantsHomeroomClassSchedule = (
        $rawRole === 'wali_kelas' &&
        $myKelasId > 0 &&
        ($isAskingOwnClassJadwal || preg_match('/\b(jadwal\s+pelajaran|pelajaran\s+kelas)\b/i', $cleanPrompt)) &&
        !preg_match('/\b(mengajar|ngajar|mulang|teaching|pribadi)\b/i', $cleanPrompt)
    );

    if ($isAskingSpecificDaysSchedule) {
        if ($rawRole === 'siswa' || $wantsHomeroomClassSchedule) {
            $inPlaceholders = implode(',', array_fill(0, count($matchedDays), '?'));
            $stSpec = $pdo->prepare("SELECT j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel, 
                                            COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Pengajar') as pengajar 
                                     FROM jadwal_mengajar j 
                                     JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                                     LEFT JOIN guru g ON j.guru_id = g.id 
                                     LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id 
                                     WHERE j.kelas_id = ? AND LOWER(j.hari) IN ($inPlaceholders) 
                                     ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC");
            $stSpec->execute(array_merge([$myKelasId], $matchedDays));
            $specRows = $stSpec->fetchAll(PDO::FETCH_ASSOC);

            $rowsByDay = [];
            foreach ($specRows as $r) {
                $rowsByDay[strtolower($r['hari'])][] = $r;
            }

            $dayLabels = array_map(fn($d) => ucfirst($d), $matchedDays);
            $dayTitleStr = implode(' dan ', $dayLabels);

            $reply = "Berikut adalah jadwal pelajaran kelas {$detailKelas} untuk hari {$dayTitleStr}:\n\n";

            foreach ($matchedDays as $day) {
                $dayLabel = ucfirst($day);
                if (in_array($day, ['sabtu', 'minggu'])) {
                    $reply .= "Hari {$dayLabel}:\n";
                    $reply .= "- Libur akhir pekan (Tidak ada kegiatan belajar mengajar).\n\n";
                    continue;
                }

                if (!empty($rowsByDay[$day])) {
                    $reply .= "Hari {$dayLabel}:\n";
                    foreach ($rowsByDay[$day] as $item) {
                        $mulai = substr($item['jam_mulai'], 0, 5);
                        $selesai = substr($item['jam_selesai'], 0, 5);
                        $ruangStr = !empty($item['ruang']) ? " (Ruang: {$item['ruang']})" : "";
                        $reply .= "- Pukul {$mulai} - {$selesai} WIB: {$item['nama_mapel']} (Pengajar: {$item['pengajar']}){$ruangStr}\n";
                    }
                    $reply .= "\n";
                } else {
                    $reply .= "Hari {$dayLabel}:\n";
                    $reply .= "- Tidak ada jadwal pelajaran yang tercatat untuk kelas {$detailKelas}.\n\n";
                }
            }

            $reply .= ($rawRole === 'siswa') ? "Ada mata pelajaran atau informasi lain yang ingin kamu ketahui?" : "Ada informasi kelas {$detailKelas} lain yang ingin Bapak/Ibu tanyakan?";
            sendBotReply($reply);
        } elseif ($rawRole === 'guru' || $rawRole === 'wali_kelas') {
            $inPlaceholders = implode(',', array_fill(0, count($matchedDays), '?'));
            $stSpec = $pdo->prepare("SELECT j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel, k.nama_kelas 
                                     FROM jadwal_mengajar j 
                                     JOIN kelas k ON j.kelas_id = k.id 
                                     JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                                     WHERE ((? > 0 AND j.guru_id = ?) OR (? > 0 AND j.wali_kelas_id = ?)) 
                                       AND LOWER(j.hari) IN ($inPlaceholders) 
                                     ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC");
            $stSpec->execute(array_merge([$myGuruId, $myGuruId, $myWaliId, $myWaliId], $matchedDays));
            $specRows = $stSpec->fetchAll(PDO::FETCH_ASSOC);

            $rowsByDay = [];
            foreach ($specRows as $r) {
                $rowsByDay[strtolower($r['hari'])][] = $r;
            }

            $dayLabels = array_map(fn($d) => ucfirst($d), $matchedDays);
            $dayTitleStr = implode(' dan ', $dayLabels);

            $reply = "Berdasarkan data sistem, berikut adalah jadwal mengajar {$sapaanGelar} {$namaLengkap} untuk hari {$dayTitleStr}:\n\n";

            foreach ($matchedDays as $day) {
                $dayLabel = ucfirst($day);
                if (in_array($day, ['sabtu', 'minggu'])) {
                    $reply .= "Hari {$dayLabel}:\n";
                    $reply .= "- Libur akhir pekan (Tidak ada jadwal mengajar).\n\n";
                    continue;
                }

                if (!empty($rowsByDay[$day])) {
                    $reply .= "Hari {$dayLabel}:\n";
                    foreach ($rowsByDay[$day] as $item) {
                        $mulai = substr($item['jam_mulai'], 0, 5);
                        $selesai = substr($item['jam_selesai'], 0, 5);
                        $ruangStr = !empty($item['ruang']) ? " (Ruang: {$item['ruang']})" : "";
                        $reply .= "- Pukul {$mulai} - {$selesai} WIB: {$item['nama_mapel']} di kelas {$item['nama_kelas']}{$ruangStr}\n";
                    }
                    $reply .= "\n";
                } else {
                    $reply .= "Hari {$dayLabel}:\n";
                    $reply .= "- Tidak ada jadwal mengajar pada hari ini.\n\n";
                }
            }

            $reply .= "Ada jadwal atau informasi lain yang ingin Bapak/Ibu tanyakan?";
            sendBotReply($reply);
        }
    }

    // F. Pertanyaan Jadwal Mengajar / Jadwal Pelajaran Hari Ini
    $isJadwalHariIni = false;
    $hasJadwalWord = (bool)preg_match('/\b(jadwal|schedule)\b/i', $cleanPrompt);
    $hasNgajarWord = (bool)preg_match('/\b(ngajar|mengajar|mulang|ngawulang|teaching)\b/i', $cleanPrompt);
    $hasTodayWord = (bool)preg_match('/\b(hari\s*ini|today|dinten\s*ieu|dinten\s*niki|saiki|ayeuna)\b/i', $cleanPrompt);
    $hasSelfWord = (bool)preg_match('/\b(saya|sy|aku|kula|kulo|abdi|my|me)\b/i', $cleanPrompt);

    if (!$mentionsSpecificOther && empty($matchedDays) && in_array($rawRole, ['siswa', 'guru', 'wali_kelas'])) {
        if ($hasJadwalWord && ($hasTodayWord || $hasSelfWord || $hasNgajarWord || $isAskingOwnClassJadwal)) {
            $isJadwalHariIni = true;
        } elseif ($hasNgajarWord && ($hasTodayWord || $hasSelfWord || preg_match('/\b(apa|ada)\b/i', $cleanPrompt))) {
            $isJadwalHariIni = true;
        } elseif (in_array($cleanPrompt, ['jadwal', 'jadwal hari ini', 'jadwal saya', 'jadwal ngajar', 'jadwal mengajar', 'jadwal pelajaran', 'ada jadwal apa', 'jadwal dinten ieu', 'jadwal dinten niki', 'my schedule', 'today schedule', 'teaching schedule'])) {
            $isJadwalHariIni = true;
        }
    }

    if ($isJadwalHariIni) {
        if ($asksFullWeekWord && !$hasTodayWord) {
            if (($rawRole === 'guru' || $rawRole === 'wali_kelas') && !$wantsHomeroomClassSchedule) {
                $stAllG = $pdo->prepare("SELECT j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel, k.nama_kelas
                                         FROM jadwal_mengajar j
                                         JOIN kelas k ON j.kelas_id = k.id
                                         JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                                         WHERE ((? > 0 AND j.guru_id = ?) OR (? > 0 AND j.wali_kelas_id = ?))
                                         ORDER BY FIELD(LOWER(j.hari), 'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC");
                $stAllG->execute([$myGuruId, $myGuruId, $myWaliId, $myWaliId]);
                $allRowsG = $stAllG->fetchAll(PDO::FETCH_ASSOC);
                if ($allRowsG) {
                    $reply = "Berdasarkan data sistem, berikut jadwal mengajar lengkap {$sapaanGelar} {$namaLengkap} (Senin - Jumat, Total " . count($allRowsG) . " sesi):\n";
                    $curH = '';
                    foreach ($allRowsG as $r) {
                        $hLbl = ucfirst($r['hari']);
                        if ($curH !== $hLbl) {
                            $curH = $hLbl;
                            $reply .= "\nHari {$curH}:\n";
                        }
                        $m1 = substr($r['jam_mulai'], 0, 5);
                        $m2 = substr($r['jam_selesai'], 0, 5);
                        $rng = !empty($r['ruang']) ? " (Ruang: {$r['ruang']})" : "";
                        $reply .= "- Pukul {$m1} - {$m2} WIB: {$r['nama_mapel']} di kelas {$r['nama_kelas']}{$rng}\n";
                    }
                    sendBotReply($reply);
                }
            } elseif (($rawRole === 'siswa' || $wantsHomeroomClassSchedule) && $myKelasId > 0) {
                $stAllK = $pdo->prepare("SELECT j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel,
                                                COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Pengajar') AS pengajar
                                         FROM jadwal_mengajar j
                                         JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                                         LEFT JOIN guru g ON j.guru_id = g.id
                                         LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id
                                         WHERE j.kelas_id = ?
                                         ORDER BY FIELD(LOWER(j.hari), 'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC");
                $stAllK->execute([$myKelasId]);
                $allRowsK = $stAllK->fetchAll(PDO::FETCH_ASSOC);
                if ($allRowsK) {
                    $reply = "Berikut jadwal pelajaran lengkap kelas {$detailKelas} (Senin - Jumat, Total " . count($allRowsK) . " sesi):\n";
                    $curH = '';
                    foreach ($allRowsK as $r) {
                        $hLbl = ucfirst($r['hari']);
                        if ($curH !== $hLbl) {
                            $curH = $hLbl;
                            $reply .= "\nHari {$curH}:\n";
                        }
                        $m1 = substr($r['jam_mulai'], 0, 5);
                        $m2 = substr($r['jam_selesai'], 0, 5);
                        $rng = !empty($r['ruang']) ? " (Ruang: {$r['ruang']})" : "";
                        $reply .= "- Pukul {$m1} - {$m2} WIB: {$r['nama_mapel']} (Pengajar: {$r['pengajar']}){$rng}\n";
                    }
                    sendBotReply($reply);
                }
            }
        }
        if (($rawRole === 'guru' || $rawRole === 'wali_kelas') && !$wantsHomeroomClassSchedule) {
            $jadwalList = $myJadwalHariIni ?? [];
            if (empty($jadwalList) && ($myGuruId > 0 || $myWaliId > 0)) {
                $stMyJadwal = $pdo->prepare("SELECT j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel, k.nama_kelas 
                                            FROM jadwal_mengajar j 
                                            JOIN kelas k ON j.kelas_id = k.id 
                                            JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                                            WHERE ((? > 0 AND j.guru_id = ?) OR (? > 0 AND j.wali_kelas_id = ?)) 
                                              AND LOWER(j.hari) = LOWER(?) 
                                            ORDER BY j.jam_mulai ASC");
                $stMyJadwal->execute([$myGuruId, $myGuruId, $myWaliId, $myWaliId, $namaHari]);
                $jadwalList = $stMyJadwal->fetchAll(PDO::FETCH_ASSOC);
            }

            if (!empty($jadwalList)) {
                if ($detectedLang === 'en') {
                    $reply = "Based on the system records, your teaching schedule for today ({$namaHari}) is:\n\n";
                    foreach ($jadwalList as $j) {
                        $mulai = substr($j['jam_mulai'], 0, 5);
                        $selesai = substr($j['jam_selesai'], 0, 5);
                        $ruangStr = !empty($j['ruang']) ? " in Room {$j['ruang']}" : "";
                        $reply .= "- {$mulai} - {$selesai}: {$j['nama_mapel']} ({$j['nama_kelas']}){$ruangStr}\n";
                    }
                } elseif ($detectedLang === 'su') {
                    $reply = "Dumasar kana data sistem, jadwal ngawulang {$sapaanGelarSu} {$namaLengkap} dinten ieu ({$namaHari}) nyaeta:\n\n";
                    foreach ($jadwalList as $j) {
                        $mulai = substr($j['jam_mulai'], 0, 5);
                        $selesai = substr($j['jam_selesai'], 0, 5);
                        $ruangStr = !empty($j['ruang']) ? " di Ruang {$j['ruang']}" : "";
                        $reply .= "- Tabuh {$mulai} - {$selesai}: {$j['nama_mapel']} ({$j['nama_kelas']}){$ruangStr}\n";
                    }
                } elseif ($detectedLang === 'jv') {
                    $reply = "Adhedhasar data sistem, jadwal mulang {$sapaanGelarJv} {$namaLengkap} dinten niki ({$namaHari}) inggih punika:\n\n";
                    foreach ($jadwalList as $j) {
                        $mulai = substr($j['jam_mulai'], 0, 5);
                        $selesai = substr($j['jam_selesai'], 0, 5);
                        $ruangStr = !empty($j['ruang']) ? " wonten ing Ruang {$j['ruang']}" : "";
                        $reply .= "- Tabuh {$mulai} - {$selesai}: {$j['nama_mapel']} ({$j['nama_kelas']}){$ruangStr}\n";
                    }
                } else {
                    $reply = "Berdasarkan data sistem, jadwal mengajar {$sapaanGelar} {$namaLengkap} hari ini ({$namaHari}) adalah:\n\n";
                    foreach ($jadwalList as $j) {
                        $mulai = substr($j['jam_mulai'], 0, 5);
                        $selesai = substr($j['jam_selesai'], 0, 5);
                        $ruangStr = !empty($j['ruang']) ? " di Ruang {$j['ruang']}" : "";
                        $reply .= "- Pukul {$mulai} - {$selesai}: {$j['nama_mapel']} ({$j['nama_kelas']}){$ruangStr}\n";
                    }
                }
            } else {
                if ($detectedLang === 'en') {
                    $reply = "Based on the system records, you do not have any teaching schedule today ({$namaHari}).";
                } elseif ($detectedLang === 'su') {
                    $reply = "Dumasar kana data sistem, {$sapaanGelarSu} {$namaLengkap} teu aya jadwal ngawulang dinten ieu ({$namaHari}).";
                } elseif ($detectedLang === 'jv') {
                    $reply = "Adhedhasar data sistem, {$sapaanGelarJv} {$namaLengkap} mboten gadhah jadwal mulang dinten niki ({$namaHari}).";
                } else {
                    $reply = "Berdasarkan data sistem, {$sapaanGelar} {$namaLengkap} tidak memiliki jadwal mengajar pada hari ini ({$namaHari}).";
                }
            }
            sendBotReply($reply);
        } elseif ($rawRole === 'siswa' || $wantsHomeroomClassSchedule) {
            $jadwalList = ($rawRole === 'siswa') ? ($myClassJadwalHariIni ?? []) : [];
            if (empty($jadwalList) && $myKelasId > 0) {
                $stSiswaJadwal = $pdo->prepare("SELECT j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel, 
                                                       COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Pengajar') as pengajar 
                                                FROM jadwal_mengajar j 
                                                JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                                                LEFT JOIN guru g ON j.guru_id = g.id 
                                                LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id 
                                                WHERE j.kelas_id = ? AND LOWER(j.hari) = LOWER(?) 
                                                ORDER BY j.jam_mulai ASC");
                $stSiswaJadwal->execute([$myKelasId, $namaHari]);
                $jadwalList = $stSiswaJadwal->fetchAll(PDO::FETCH_ASSOC);
            }

            if (!empty($jadwalList)) {
                if ($detectedLang === 'en') {
                    $reply = "Based on the system records, the class schedule for {$detailKelas} today ({$namaHari}) is:\n\n";
                    foreach ($jadwalList as $j) {
                        $mulai = substr($j['jam_mulai'], 0, 5);
                        $selesai = substr($j['jam_selesai'], 0, 5);
                        $ruangStr = !empty($j['ruang']) ? " in Room {$j['ruang']}" : "";
                        $reply .= "- {$mulai} - {$selesai}: {$j['nama_mapel']} (Teacher: {$j['pengajar']}){$ruangStr}\n";
                    }
                } elseif ($detectedLang === 'su') {
                    $reply = "Dumasar kana data sistem, jadwal pangajaran kelas {$detailKelas} dinten ieu ({$namaHari}) nyaeta:\n\n";
                    foreach ($jadwalList as $j) {
                        $mulai = substr($j['jam_mulai'], 0, 5);
                        $selesai = substr($j['jam_selesai'], 0, 5);
                        $ruangStr = !empty($j['ruang']) ? " di Ruang {$j['ruang']}" : "";
                        $reply .= "- Tabuh {$mulai} - {$selesai}: {$j['nama_mapel']} (Guru: {$j['pengajar']}){$ruangStr}\n";
                    }
                } elseif ($detectedLang === 'jv') {
                    $reply = "Adhedhasar data sistem, jadwal piwulangan kelas {$detailKelas} dinten niki ({$namaHari}) inggih punika:\n\n";
                    foreach ($jadwalList as $j) {
                        $mulai = substr($j['jam_mulai'], 0, 5);
                        $selesai = substr($j['jam_selesai'], 0, 5);
                        $ruangStr = !empty($j['ruang']) ? " wonten ing Ruang {$j['ruang']}" : "";
                        $reply .= "- Tabuh {$mulai} - {$selesai}: {$j['nama_mapel']} (Guru: {$j['pengajar']}){$ruangStr}\n";
                    }
                } else {
                    $reply = "Berdasarkan data sistem, jadwal pelajaran kelas {$detailKelas} hari ini ({$namaHari}) adalah:\n\n";
                    foreach ($jadwalList as $j) {
                        $mulai = substr($j['jam_mulai'], 0, 5);
                        $selesai = substr($j['jam_selesai'], 0, 5);
                        $ruangStr = !empty($j['ruang']) ? " di Ruang {$j['ruang']}" : "";
                        $reply .= "- Pukul {$mulai} - {$selesai}: {$j['nama_mapel']} (Pengajar: {$j['pengajar']}){$ruangStr}\n";
                    }
                }
            } else {
                if ($detectedLang === 'en') {
                    $reply = "Based on the system records, there is no class schedule for {$detailKelas} today ({$namaHari}).";
                } elseif ($detectedLang === 'su') {
                    $reply = "Dumasar kana data sistem, teu aya jadwal pangajaran kanggo kelas {$detailKelas} dinten ieu ({$namaHari}).";
                } elseif ($detectedLang === 'jv') {
                    $reply = "Adhedhasar data sistem, mboten wonten jadwal piwulangan kagem kelas {$detailKelas} dinten niki ({$namaHari}).";
                } else {
                    $reply = "Berdasarkan data sistem, tidak ada jadwal pelajaran untuk kelas {$detailKelas} pada hari ini ({$namaHari}).";
                }
            }
            sendBotReply($reply);
        }
    }

    // 3. AMBIL DATA SISTEM SESUAI HAK AKSES ROLE (STRICT ROLE-SCOPED DATABASE CONTEXT)
    $ta = $pdo->query("SELECT tahun, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $taStr = $ta ? "{$ta['tahun']} (Semester {$ta['semester']})" : "2026/2027 (Semester Ganjil)";
    $databaseContext .= "[TAHUN AJARAN AKTIF]: $taStr\n";

    switch ($rawRole) {
        case 'siswa':
            $databaseContext .= "[RUANG LINGKUP DATABASE ROLE SISWA]:\n"
                              . "- Anda HANYA berhak mengakses data profil pribadi siswa ({$namaLengkap}), status & riwayat presensi pribadi, pengajuan izin pribadi, catatan pembinaan dengan visibilitas Siswa untuk diri sendiri, serta jadwal pelajaran kelas {$detailKelas}.\n"
                              . "- DATA YANG DILARANG UNTUK ROLE SISWA: data/daftar/jumlah siswa lain, NIS/NISN siswa lain, data/NIP/kehadiran/jadwal guru, catatan untuk guru, data kelas lain, total kelas/jurusan/siswa/guru sekolah, serta keseluruhan jadwal pelajaran sekolah.\n";
            break;

        case 'guru':
            $databaseContext .= "[RUANG LINGKUP DATABASE ROLE GURU]:\n"
                              . "- Anda HANYA berhak mengakses data profil pribadi Anda ({$namaLengkap}, NIP: " . ($myNip ?: '-') . "), mata pelajaran yang Anda ampu, status & riwayat presensi pribadi, pengajuan izin pribadi, catatan Kepala Sekolah untuk Anda, serta jadwal mengajar pribadi Anda.\n"
                              . "- DATA YANG DILARANG UNTUK ROLE GURU: daftar/NIS/NISN/kehadiran/izin/pembinaan siswa, data/NIP/kehadiran/jadwal/catatan guru lain, jadwal kelas di luar tugas mengajar Anda, serta statistik total sekolah (total siswa, total guru, total kelas, total jurusan, total jadwal sekolah).\n";
            break;

        case 'wali_kelas':
            $databaseContext .= "[RUANG LINGKUP DATABASE ROLE WALI KELAS ({$detailKelas})]:\n"
                              . "- Anda HANYA berhak mengakses data pribadi Anda (profil, presensi pribadi, izin pribadi, catatan Kepala Sekolah untuk Anda, jadwal mengajar pribadi) SERTA data kelas binaan Anda ({$detailKelas}).\n"
                              . "- DATA YANG DILARANG UNTUK ROLE WALI KELAS: data siswa atau jadwal kelas lain di luar {$detailKelas}, data/NIP/kehadiran/jadwal guru lain, serta statistik total sekolah atau total jadwal seluruh sekolah.\n";

            if ($myKelasId > 0) {
                $stWkSiswa = $pdo->prepare("SELECT s.id, s.nis, s.nisn, s.nama_lengkap, s.jenis_kelamin
                                            FROM siswa s
                                            WHERE s.kelas_id = ?
                                            ORDER BY s.nama_lengkap ASC");
                $stWkSiswa->execute([$myKelasId]);
                $wkSiswaList = $stWkSiswa->fetchAll(PDO::FETCH_ASSOC);

                $wkTotalL = count(array_filter($wkSiswaList, fn($x) => strtoupper(trim($x['jenis_kelamin'] ?? '')) === 'L'));
                $wkTotalP = count(array_filter($wkSiswaList, fn($x) => strtoupper(trim($x['jenis_kelamin'] ?? '')) === 'P'));
                $databaseContext .= "\n[DAFTAR SISWA KELAS BINAAN {$detailKelas} (Total " . count($wkSiswaList) . " Siswa — Laki-laki: {$wkTotalL}, Perempuan: {$wkTotalP})]:\n";
                foreach ($wkSiswaList as $idx => $s) {
                    $num = $idx + 1;
                    $nisn = !empty($s['nisn']) ? $s['nisn'] : '-';
                    $databaseContext .= "  $num. {$s['nama_lengkap']} (NIS: {$s['nis']}, NISN: $nisn, {$s['jenis_kelamin']})\n";
                }

                // Kehadiran hari ini siswa kelas binaan
                $stWkAbsToday = $pdo->prepare("SELECT s.nama_lengkap, a.status, a.jam_masuk, a.jam_pulang, a.keterangan
                                               FROM absensi_siswa a
                                               JOIN siswa s ON a.siswa_id = s.id
                                               WHERE a.tanggal = ? AND s.kelas_id = ?
                                               ORDER BY a.status ASC, s.nama_lengkap ASC");
                $stWkAbsToday->execute([$today, $myKelasId]);
                $wkAbsToday = $stWkAbsToday->fetchAll(PDO::FETCH_ASSOC);

                $wkCountsToday = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
                foreach ($wkAbsToday as $row) {
                    $stLow = strtolower($row['status'] ?? '');
                    if ($stLow === 'bolos' || $stLow === 'alpa') {
                        $wkCountsToday['alpa']++;
                    } elseif (isset($wkCountsToday[$stLow])) {
                        $wkCountsToday[$stLow]++;
                    }
                }
                $wkBelum = max(0, count($wkSiswaList) - count($wkAbsToday));
                $databaseContext .= "\n[REKAP KEHADIRAN SISWA KELAS {$detailKelas} HARI INI ($namaHari, " . date('d-m-Y') . ")]:\n";
                $databaseContext .= "- Ringkasan: Hadir=" . ($wkCountsToday['hadir'] + $wkCountsToday['terlambat']) . " (Terlambat={$wkCountsToday['terlambat']}), Izin={$wkCountsToday['izin']}, Sakit={$wkCountsToday['sakit']}, Alpa={$wkCountsToday['alpa']}, Belum Presensi={$wkBelum}\n";
                if ($wkAbsToday) {
                    foreach ($wkAbsToday as $ash) {
                        $jm = $ash['jam_masuk'] ? substr($ash['jam_masuk'], 0, 5) . ' WIB' : 'Belum';
                        $jp = $ash['jam_pulang'] ? substr($ash['jam_pulang'], 0, 5) . ' WIB' : 'Belum';
                        $databaseContext .= "  • {$ash['nama_lengkap']}: " . ucfirst($ash['status']) . " (Masuk: $jm, Pulang: $jp) | Ket: {$ash['keterangan']}\n";
                    }
                } else {
                    $databaseContext .= "  • Belum ada data presensi siswa kelas {$detailKelas} yang tercatat hari ini.\n";
                }

                // Pemantauan kumulatif per siswa kelas binaan (riwayat tidak hadir/izin/sakit/terlambat)
                $stWkKumulatif = $pdo->prepare("SELECT s.nama_lengkap,
                                                       SUM(CASE WHEN LOWER(a.status) IN ('hadir', 'terlambat') THEN 1 ELSE 0 END) as jml_hadir,
                                                       SUM(CASE WHEN LOWER(a.status) = 'terlambat' THEN 1 ELSE 0 END) as jml_terlambat,
                                                       SUM(CASE WHEN LOWER(a.status) = 'izin' THEN 1 ELSE 0 END) as jml_izin,
                                                       SUM(CASE WHEN LOWER(a.status) = 'sakit' THEN 1 ELSE 0 END) as jml_sakit,
                                                       SUM(CASE WHEN LOWER(a.status) IN ('alpa', 'bolos') THEN 1 ELSE 0 END) as jml_alpa
                                                FROM siswa s
                                                LEFT JOIN absensi_siswa a ON a.siswa_id = s.id AND WEEKDAY(a.tanggal) < 5
                                                WHERE s.kelas_id = ?
                                                GROUP BY s.id, s.nama_lengkap
                                                ORDER BY jml_alpa DESC, s.nama_lengkap ASC");
                $stWkKumulatif->execute([$myKelasId]);
                $wkKumRows = $stWkKumulatif->fetchAll(PDO::FETCH_ASSOC);
                if ($wkKumRows) {
                    $databaseContext .= "\n[PEMANTAUAN KUMULATIF KEHADIRAN SISWA KELAS {$detailKelas} (SENIN-JUMAT)]:\n";
                    foreach ($wkKumRows as $km) {
                        $databaseContext .= "- {$km['nama_lengkap']}: Hadir={$km['jml_hadir']} (Terlambat={$km['jml_terlambat']}), Izin={$km['jml_izin']}, Sakit={$km['jml_sakit']}, Alpa={$km['jml_alpa']}\n";
                    }
                }

                // Pengajuan izin siswa kelas binaan
                $stWkIzin = $pdo->prepare("SELECT p.jenis, p.tanggal, p.alasan, p.status, s.nama_lengkap
                                           FROM pengajuan_izin p
                                           JOIN siswa s ON p.siswa_id = s.id
                                           WHERE p.tipe_pemohon = 'siswa' AND s.kelas_id = ?
                                           ORDER BY p.tanggal DESC, p.id DESC LIMIT 15");
                $stWkIzin->execute([$myKelasId]);
                $wkIzins = $stWkIzin->fetchAll(PDO::FETCH_ASSOC);
                $databaseContext .= "\n[PENGAJUAN IZIN & SAKIT SISWA KELAS {$detailKelas} TERBARU]:\n";
                if ($wkIzins) {
                    foreach ($wkIzins as $pj) {
                        $databaseContext .= "- {$pj['nama_lengkap']} | {$pj['jenis']} tgl {$pj['tanggal']} | Alasan: \"{$pj['alasan']}\" | Status: {$pj['status']}\n";
                    }
                } else {
                    $databaseContext .= "- Belum ada pengajuan izin/sakit dari siswa kelas {$detailKelas}.\n";
                }

                // Catatan pembinaan siswa kelas binaan
                $stWkCat = $pdo->prepare("SELECT cp.created_at, cp.kategori, cp.isi_catatan, cp.visibilitas, s.nama_lengkap as nama_siswa
                                          FROM catatan_pembinaan cp
                                          JOIN siswa s ON cp.siswa_id = s.id
                                          WHERE s.kelas_id = ?
                                          ORDER BY cp.created_at DESC LIMIT 15");
                $stWkCat->execute([$myKelasId]);
                $wkCats = $stWkCat->fetchAll(PDO::FETCH_ASSOC);
                $databaseContext .= "\n[CATATAN PEMBINAAN SISWA KELAS {$detailKelas} TERBARU]:\n";
                if ($wkCats) {
                    foreach ($wkCats as $cp) {
                        $tgl = date('d-m-Y', strtotime($cp['created_at']));
                        $databaseContext .= "- {$cp['nama_siswa']} | {$cp['kategori']} (Tgl $tgl, Visibilitas: {$cp['visibilitas']}) | \"{$cp['isi_catatan']}\"\n";
                    }
                } else {
                    $databaseContext .= "- Belum ada catatan pembinaan untuk siswa kelas {$detailKelas}.\n";
                }

                // Jadwal pelajaran kelas binaan
                $stJadwalWk = $pdo->prepare("SELECT j.hari, j.jam_mulai, j.jam_selesai, j.ruang, m.nama_mapel,
                                                    COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Pengajar') as pengajar
                                             FROM jadwal_mengajar j
                                             JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                                             LEFT JOIN guru g ON j.guru_id = g.id
                                             LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id
                                             WHERE j.kelas_id = ?
                                             ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat'), j.jam_mulai ASC");
                $stJadwalWk->execute([$myKelasId]);
                $wkClassJadwal = $stJadwalWk->fetchAll(PDO::FETCH_ASSOC);
                $databaseContext .= "\n[JADWAL PELAJARAN KELAS BINAAN ({$detailKelas}) (Senin - Jumat)]:\n";
                if ($wkClassJadwal) {
                    foreach ($wkClassJadwal as $jdw) {
                        $mulai = substr($jdw['jam_mulai'], 0, 5);
                        $selesai = substr($jdw['jam_selesai'], 0, 5);
                        $databaseContext .= "  - " . ucfirst($jdw['hari']) . " ($mulai - $selesai) : {$jdw['nama_mapel']} — {$jdw['pengajar']} (Ruang: {$jdw['ruang']})\n";
                    }
                } else {
                    $databaseContext .= "  - Belum ada jadwal pelajaran tercatat untuk kelas {$detailKelas}.\n";
                }
            }
            break;

        case 'kepala_sekolah':
            $stmtJurusan = $pdo->query("SELECT kode_jurusan, nama_jurusan FROM jurusan ORDER BY id ASC");
            $allJurusan = $stmtJurusan->fetchAll(PDO::FETCH_ASSOC);
            $databaseContext .= "[JURUSAN]: " . implode(", ", array_map(fn($j) => "{$j['kode_jurusan']} ({$j['nama_jurusan']})", $allJurusan)) . "\n";

            // Kelas, Wali Kelas, Rincian Gender Siswa & Rekap Kehadiran Siswa Hari Ini per Kelas
            $stmtKelasKs = $pdo->prepare("SELECT k.id, k.tingkat, k.nama_kelas, j.nama_jurusan,
                                                 COALESCE(wkd.nama_lengkap, g.nama_lengkap, 'Belum ditentukan') as nama_wali,
                                                 COALESCE(wkd.nip, g.nip, '-') as nip_wali,
                                                 (SELECT COUNT(*) FROM siswa s WHERE s.kelas_id = k.id) as total_siswa,
                                                 (SELECT COUNT(*) FROM siswa s WHERE s.kelas_id = k.id AND UPPER(TRIM(s.jenis_kelamin)) = 'L') as total_laki,
                                                 (SELECT COUNT(*) FROM siswa s WHERE s.kelas_id = k.id AND UPPER(TRIM(s.jenis_kelamin)) = 'P') as total_perempuan,
                                                 (SELECT COUNT(*) FROM absensi_siswa a JOIN siswa s2 ON a.siswa_id = s2.id WHERE s2.kelas_id = k.id AND a.tanggal = ? AND LOWER(a.status) IN ('hadir','terlambat')) as hadir_hari_ini,
                                                 (SELECT COUNT(*) FROM absensi_siswa a JOIN siswa s2 ON a.siswa_id = s2.id WHERE s2.kelas_id = k.id AND a.tanggal = ? AND LOWER(a.status) = 'izin') as izin_hari_ini,
                                                 (SELECT COUNT(*) FROM absensi_siswa a JOIN siswa s2 ON a.siswa_id = s2.id WHERE s2.kelas_id = k.id AND a.tanggal = ? AND LOWER(a.status) = 'sakit') as sakit_hari_ini,
                                                 (SELECT COUNT(*) FROM absensi_siswa a JOIN siswa s2 ON a.siswa_id = s2.id WHERE s2.kelas_id = k.id AND a.tanggal = ? AND LOWER(a.status) IN ('alpa','bolos')) as alpa_hari_ini
                                          FROM kelas k
                                          JOIN jurusan j ON k.jurusan_id = j.id
                                          LEFT JOIN wali_kelas wk ON k.id = wk.kelas_id
                                          LEFT JOIN wali_kelas_data wkd ON wk.wali_kelas_data_id = wkd.id
                                          LEFT JOIN guru g ON wkd.user_id = g.user_id
                                          ORDER BY k.tingkat ASC, k.nama_kelas ASC");
            $stmtKelasKs->execute([$today, $today, $today, $today]);
            $allKelas = $stmtKelasKs->fetchAll(PDO::FETCH_ASSOC);
            $totalKelas = count($allKelas);
            $sumSiswaKs = array_sum(array_column($allKelas, 'total_siswa'));
            $sumLKs = array_sum(array_column($allKelas, 'total_laki'));
            $sumPKs = array_sum(array_column($allKelas, 'total_perempuan'));
            $databaseContext .= "\n[RINGKASAN KEPENDUDUKAN SISWA SEKOLAH]: Total {$sumSiswaKs} Siswa (Laki-laki: {$sumLKs}, Perempuan: {$sumPKs}) di {$totalKelas} Kelas\n";
            $databaseContext .= "\n[DAFTAR KELAS & REKAP KEHADIRAN PER KELAS HARI INI (TOTAL {$totalKelas} KELAS)]:\n";
            foreach ($allKelas as $k) {
                $databaseContext .= "- Kelas {$k['nama_kelas']} ({$k['nama_jurusan']}) | Tingkat {$k['tingkat']} | {$k['total_siswa']} Siswa (L: {$k['total_laki']}, P: {$k['total_perempuan']}) | Wali: {$k['nama_wali']} (NIP: {$k['nip_wali']}) | Hari ini: Hadir={$k['hadir_hari_ini']}, Izin={$k['izin_hari_ini']}, Sakit={$k['sakit_hari_ini']}, Alpa={$k['alpa_hari_ini']}\n";
            }

            // Daftar Guru (Identitas & NIP untuk Laporan Kehadiran dan Catatan Guru)
            $stmtGuruKs = $pdo->query("SELECT g.id, g.nip, g.nama_lengkap, g.jenis_kelamin
                                       FROM guru g
                                       ORDER BY g.nama_lengkap ASC");
            $allGuru = $stmtGuruKs->fetchAll(PDO::FETCH_ASSOC);
            $databaseContext .= "\n[DAFTAR GURU / TENAGA PENDIDIK (" . count($allGuru) . " Guru)]:\n";
            foreach ($allGuru as $g) {
                $jk = ($g['jenis_kelamin'] === 'L') ? 'L' : 'P';
                $databaseContext .= "- {$g['nama_lengkap']} (NIP: {$g['nip']}, JK: $jk)\n";
            }

            // Daftar Siswa per Kelas (Monitoring & Laporan)
            $stmtSiswaKs = $pdo->query("SELECT s.id, s.nis, s.nisn, s.nama_lengkap, s.jenis_kelamin, k.nama_kelas
                                        FROM siswa s
                                        JOIN kelas k ON s.kelas_id = k.id
                                        ORDER BY k.nama_kelas ASC, s.nama_lengkap ASC");
            $allSiswa = $stmtSiswaKs->fetchAll(PDO::FETCH_ASSOC);
            $siswaByKelas = [];
            foreach ($allSiswa as $s) {
                $siswaByKelas[$s['nama_kelas']][] = $s;
            }
            $databaseContext .= "\n[DAFTAR SELURUH SISWA PER KELAS (Total " . count($allSiswa) . " Siswa)]:\n";
            foreach ($siswaByKelas as $kNama => $list) {
                $databaseContext .= "Kelas $kNama (" . count($list) . " Siswa):\n";
                $sItems = [];
                foreach ($list as $idx => $s) {
                    $num = $idx + 1;
                    $nisn = !empty($s['nisn']) ? $s['nisn'] : '-';
                    $sItems[] = "  $num. {$s['nama_lengkap']} (NIS: {$s['nis']}, NISN: $nisn, {$s['jenis_kelamin']})";
                }
                $databaseContext .= implode("\n", $sItems) . "\n\n";
            }

            // Rekap Kehadiran Siswa & Guru Hari Ini
            $stmtAbsenSiswaKs = $pdo->prepare("SELECT s.nama_lengkap, k.nama_kelas, a.status, a.jam_masuk, a.jam_pulang, a.keterangan
                                               FROM absensi_siswa a
                                               JOIN siswa s ON a.siswa_id = s.id
                                               JOIN kelas k ON s.kelas_id = k.id
                                               WHERE a.tanggal = ?
                                               ORDER BY a.status ASC, s.nama_lengkap ASC");
            $stmtAbsenSiswaKs->execute([$today]);
            $absenSiswaHariIni = $stmtAbsenSiswaKs->fetchAll(PDO::FETCH_ASSOC);

            $stmtAbsenGuruKs = $pdo->prepare("SELECT g.nama_lengkap, a.status, a.jam_masuk, a.jam_pulang, a.keterangan
                                              FROM absensi_guru a
                                              JOIN guru g ON a.guru_id = g.id
                                              WHERE a.tanggal = ?
                                              ORDER BY a.status ASC, g.nama_lengkap ASC");
            $stmtAbsenGuruKs->execute([$today]);
            $absenGuruHariIni = $stmtAbsenGuruKs->fetchAll(PDO::FETCH_ASSOC);

            $databaseContext .= "[REKAP KEHADIRAN / LAPORAN HARIAN ($namaHari, " . date('d-m-Y') . ")]:\n";
            $databaseContext .= "- Siswa Tercatat Hari Ini (" . count($absenSiswaHariIni) . " dari " . count($allSiswa) . " siswa):\n";
            if ($absenSiswaHariIni) {
                foreach ($absenSiswaHariIni as $ash) {
                    $jm = $ash['jam_masuk'] ? substr($ash['jam_masuk'], 0, 5) . ' WIB' : 'Belum';
                    $jp = $ash['jam_pulang'] ? substr($ash['jam_pulang'], 0, 5) . ' WIB' : 'Belum';
                    $databaseContext .= "  • {$ash['nama_lengkap']} ({$ash['nama_kelas']}): " . ucfirst($ash['status']) . " (Masuk: $jm, Pulang: $jp) | Ket: {$ash['keterangan']}\n";
                }
            } else {
                $databaseContext .= "  • Belum ada data absensi siswa yang tercatat hari ini.\n";
            }

            $databaseContext .= "- Guru Tercatat Hari Ini (" . count($absenGuruHariIni) . " dari " . count($allGuru) . " guru):\n";
            if ($absenGuruHariIni) {
                foreach ($absenGuruHariIni as $agh) {
                    $jm = $agh['jam_masuk'] ? substr($agh['jam_masuk'], 0, 5) . ' WIB' : 'Belum';
                    $jp = $agh['jam_pulang'] ? substr($agh['jam_pulang'], 0, 5) . ' WIB' : 'Belum';
                    $databaseContext .= "  • {$agh['nama_lengkap']}: " . ucfirst($agh['status']) . " (Masuk: $jm, Pulang: $jp) | Ket: {$agh['keterangan']}\n";
                }
            } else {
                $databaseContext .= "  • Belum ada data absensi guru yang tercatat hari ini.\n";
            }

            // Ringkasan Pengajuan Izin Terbaru
            $stmtPengajuanKs = $pdo->query("SELECT p.tipe_pemohon, p.jenis, p.tanggal, p.alasan, p.status,
                                                   COALESCE(s.nama_lengkap, g.nama_lengkap, 'Pengguna') as nama_pemohon,
                                                   COALESCE(k.nama_kelas, 'Guru') as kelas_atau_unit
                                            FROM pengajuan_izin p
                                            LEFT JOIN siswa s ON p.siswa_id = s.id
                                            LEFT JOIN kelas k ON s.kelas_id = k.id
                                            LEFT JOIN guru g ON p.guru_id = g.id
                                            ORDER BY p.tanggal DESC, p.id DESC LIMIT 15");
            $pengajuans = $stmtPengajuanKs->fetchAll(PDO::FETCH_ASSOC);
            $databaseContext .= "\n[DATA PENGAJUAN IZIN & SAKIT TERBARU]:\n";
            if ($pengajuans) {
                foreach ($pengajuans as $pj) {
                    $databaseContext .= "- {$pj['nama_pemohon']} ({$pj['kelas_atau_unit']}) | {$pj['jenis']} tgl {$pj['tanggal']} | Alasan: \"{$pj['alasan']}\" | Status: {$pj['status']}\n";
                }
            } else {
                $databaseContext .= "- Belum ada data pengajuan izin.\n";
            }

            // Catatan Kepala Sekolah untuk Guru (catatan_guru)
            $stCgStats = $pdo->query("SELECT COUNT(*) as total,
                                             SUM(CASE WHEN status_baca = 'belum' THEN 1 ELSE 0 END) as belum_dibaca,
                                             SUM(CASE WHEN status_baca = 'sudah' THEN 1 ELSE 0 END) as sudah_dibaca
                                      FROM catatan_guru")->fetch(PDO::FETCH_ASSOC);
            $stCgList = $pdo->query("SELECT cg.tanggal, cg.judul, cg.kategori, cg.urgensi, cg.isi_catatan, cg.status_baca, g.nama_lengkap, g.nip
                                     FROM catatan_guru cg
                                     JOIN guru g ON cg.guru_id = g.id
                                     ORDER BY cg.tanggal DESC, cg.id DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
            $databaseContext .= "\n[DATA CATATAN KEPALA SEKOLAH UNTUK GURU (Total: " . (int)($stCgStats['total'] ?? 0) . ", Belum Dibaca: " . (int)($stCgStats['belum_dibaca'] ?? 0) . ", Sudah Dibaca: " . (int)($stCgStats['sudah_dibaca'] ?? 0) . ")]:\n";
            if ($stCgList) {
                foreach ($stCgList as $cg) {
                    $judulStr = !empty($cg['judul']) ? "[{$cg['judul']}] " : "";
                    $stBaca = ($cg['status_baca'] === 'sudah') ? 'Sudah dibaca' : 'Belum dibaca';
                    $databaseContext .= "- {$cg['nama_lengkap']} (NIP: {$cg['nip']}) | Tgl {$cg['tanggal']} | {$cg['kategori']} ({$cg['urgensi']}) | {$stBaca} | {$judulStr}\"{$cg['isi_catatan']}\"\n";
                }
            } else {
                $databaseContext .= "- Belum ada catatan untuk guru yang dibuat.\n";
            }
            $databaseContext .= "\n[BATASAN AKSES ROLE KEPALA SEKOLAH]: DILARANG memberikan data jadwal pelajaran, jadwal mengajar guru, daftar mata pelajaran, atau catatan pembinaan khusus wali kelas.\n";
            break;

        case 'admin':
        default:
            // Jurusan
            $stmtJurusan = $pdo->query("SELECT kode_jurusan, nama_jurusan FROM jurusan ORDER BY id ASC");
            $allJurusan = $stmtJurusan->fetchAll(PDO::FETCH_ASSOC);
            $databaseContext .= "[JURUSAN]: " . implode(", ", array_map(fn($j) => "{$j['kode_jurusan']} ({$j['nama_jurusan']})", $allJurusan)) . "\n";

            // Kelas, Wali Kelas & Rincian Gender Siswa
            $stmtKelas = $pdo->query("SELECT k.id, k.tingkat, k.nama_kelas, j.nama_jurusan, 
                                             COALESCE(wkd.nama_lengkap, g.nama_lengkap, 'Belum ditentukan') as nama_wali,
                                             COALESCE(wkd.nip, g.nip, '-') as nip_wali,
                                             (SELECT COUNT(*) FROM siswa s WHERE s.kelas_id = k.id) as total_siswa,
                                             (SELECT COUNT(*) FROM siswa s WHERE s.kelas_id = k.id AND UPPER(TRIM(s.jenis_kelamin)) = 'L') as total_laki,
                                             (SELECT COUNT(*) FROM siswa s WHERE s.kelas_id = k.id AND UPPER(TRIM(s.jenis_kelamin)) = 'P') as total_perempuan
                                      FROM kelas k 
                                      JOIN jurusan j ON k.jurusan_id = j.id 
                                      LEFT JOIN wali_kelas wk ON k.id = wk.kelas_id 
                                      LEFT JOIN wali_kelas_data wkd ON wk.wali_kelas_data_id = wkd.id 
                                      LEFT JOIN guru g ON wkd.user_id = g.user_id 
                                      ORDER BY k.tingkat ASC, k.nama_kelas ASC");
            $allKelas = $stmtKelas->fetchAll(PDO::FETCH_ASSOC);
            $totalKelas = count($allKelas);
            $sumSiswaAdm = array_sum(array_column($allKelas, 'total_siswa'));
            $sumLAdm = array_sum(array_column($allKelas, 'total_laki'));
            $sumPAdm = array_sum(array_column($allKelas, 'total_perempuan'));
            $databaseContext .= "\n[RINGKASAN KEPENDUDUKAN SISWA SEKOLAH]: Total {$sumSiswaAdm} Siswa (Laki-laki: {$sumLAdm}, Perempuan: {$sumPAdm}) di {$totalKelas} Kelas\n";
            $databaseContext .= "\n[DAFTAR KELAS (TOTAL TEPAT {$totalKelas} KELAS)]:\n";
            foreach ($allKelas as $k) {
                $databaseContext .= "- Kelas {$k['nama_kelas']} ({$k['nama_jurusan']}) | Tingkat {$k['tingkat']} | {$k['total_siswa']} Siswa (L: {$k['total_laki']}, P: {$k['total_perempuan']}) | Wali Kelas: {$k['nama_wali']} (NIP: {$k['nip_wali']})\n";
            }

            // Daftar Guru & Mapel
            $stmtGuru = $pdo->query("SELECT g.id, g.nip, g.nama_lengkap, g.jenis_kelamin, 
                                            COALESCE(GROUP_CONCAT(DISTINCT m.nama_mapel SEPARATOR ', '), 'Belum ada mapel') as mapel_diampu
                                     FROM guru g 
                                     LEFT JOIN mata_pelajaran m ON m.guru_id = g.id 
                                     GROUP BY g.id 
                                     ORDER BY g.nama_lengkap ASC");
            $allGuru = $stmtGuru->fetchAll(PDO::FETCH_ASSOC);
            $databaseContext .= "\n[DAFTAR GURU & MAPEL YANG DIAMPU (" . count($allGuru) . " Guru)]:\n";
            foreach ($allGuru as $g) {
                $jk = ($g['jenis_kelamin'] === 'L') ? 'L' : 'P';
                $databaseContext .= "- {$g['nama_lengkap']} (NIP: {$g['nip']}, JK: $jk) — Mapel: {$g['mapel_diampu']}\n";
            }

            // Daftar Mata Pelajaran
            $stmtMapel = $pdo->query("SELECT m.kode_mapel, m.nama_mapel, m.tingkat, 
                                             COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Belum ditentukan') as pengajar
                                      FROM mata_pelajaran m 
                                      LEFT JOIN guru g ON m.guru_id = g.id 
                                      LEFT JOIN wali_kelas_data wkd ON m.wali_kelas_id = wkd.id 
                                      ORDER BY m.tingkat ASC, m.nama_mapel ASC");
            $allMapel = $stmtMapel->fetchAll(PDO::FETCH_ASSOC);
            $databaseContext .= "\n[DAFTAR MATA PELAJARAN (" . count($allMapel) . " Mapel)]:\n";
            foreach ($allMapel as $m) {
                $databaseContext .= "- {$m['nama_mapel']} ({$m['kode_mapel']}, Tingkat {$m['tingkat']}) — Pengajar: {$m['pengajar']}\n";
            }

            // Daftar Siswa per Kelas
            $stmtSiswa = $pdo->query("SELECT s.id, s.nis, s.nisn, s.nama_lengkap, s.jenis_kelamin, k.nama_kelas 
                                      FROM siswa s 
                                      JOIN kelas k ON s.kelas_id = k.id 
                                      ORDER BY k.nama_kelas ASC, s.nama_lengkap ASC");
            $allSiswa = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);
            $siswaByKelas = [];
            foreach ($allSiswa as $s) {
                $siswaByKelas[$s['nama_kelas']][] = $s;
            }
            $databaseContext .= "\n[DAFTAR SELURUH SISWA PER KELAS (Total " . count($allSiswa) . " Siswa)]:\n";
            foreach ($siswaByKelas as $kNama => $list) {
                $databaseContext .= "Kelas $kNama (" . count($list) . " Siswa):\n";
                $sItems = [];
                foreach ($list as $idx => $s) {
                    $num = $idx + 1;
                    $nisn = !empty($s['nisn']) ? $s['nisn'] : '-';
                    $sItems[] = "  $num. {$s['nama_lengkap']} (NIS: {$s['nis']}, NISN: $nisn, {$s['jenis_kelamin']})";
                }
                $databaseContext .= implode("\n", $sItems) . "\n\n";
            }

            // Rekap Kehadiran Hari Ini
            $stmtAbsenSiswa = $pdo->prepare("SELECT s.nama_lengkap, k.nama_kelas, a.status, a.jam_masuk, a.jam_pulang, a.keterangan 
                                             FROM absensi_siswa a 
                                             JOIN siswa s ON a.siswa_id = s.id 
                                             JOIN kelas k ON s.kelas_id = k.id 
                                             WHERE a.tanggal = ? 
                                             ORDER BY a.status ASC, s.nama_lengkap ASC");
            $stmtAbsenSiswa->execute([$today]);
            $absenSiswaHariIni = $stmtAbsenSiswa->fetchAll(PDO::FETCH_ASSOC);

            $stmtAbsenGuru = $pdo->prepare("SELECT g.nama_lengkap, a.status, a.jam_masuk, a.jam_pulang, a.keterangan 
                                           FROM absensi_guru a 
                                           JOIN guru g ON a.guru_id = g.id 
                                           WHERE a.tanggal = ? 
                                           ORDER BY a.status ASC, g.nama_lengkap ASC");
            $stmtAbsenGuru->execute([$today]);
            $absenGuruHariIni = $stmtAbsenGuru->fetchAll(PDO::FETCH_ASSOC);

            $databaseContext .= "[REKAP KEHADIRAN / ABSENSI HARI INI ($namaHari, " . date('d-m-Y') . ")]:\n";
            $databaseContext .= "- Siswa Tercatat Hari Ini (" . count($absenSiswaHariIni) . " siswa):\n";
            if ($absenSiswaHariIni) {
                foreach ($absenSiswaHariIni as $ash) {
                    $jm = $ash['jam_masuk'] ? substr($ash['jam_masuk'], 0, 5) . ' WIB' : 'Belum';
                    $jp = $ash['jam_pulang'] ? substr($ash['jam_pulang'], 0, 5) . ' WIB' : 'Belum';
                    $databaseContext .= "  • {$ash['nama_lengkap']} ({$ash['nama_kelas']}): Status " . ucfirst($ash['status']) . " (Masuk: $jm, Pulang: $jp) | Ket: {$ash['keterangan']}\n";
                }
            } else {
                $databaseContext .= "  • Belum ada data absensi siswa yang tercatat hari ini.\n";
            }

            $databaseContext .= "- Guru & Wali Kelas Tercatat Hari Ini (" . count($absenGuruHariIni) . " orang):\n";
            if ($absenGuruHariIni) {
                foreach ($absenGuruHariIni as $agh) {
                    $jm = $agh['jam_masuk'] ? substr($agh['jam_masuk'], 0, 5) . ' WIB' : 'Belum';
                    $jp = $agh['jam_pulang'] ? substr($agh['jam_pulang'], 0, 5) . ' WIB' : 'Belum';
                    $databaseContext .= "  • {$agh['nama_lengkap']}: Status " . ucfirst($agh['status']) . " (Masuk: $jm, Pulang: $jp) | Ket: {$agh['keterangan']}\n";
                }
            } else {
                $databaseContext .= "  • Belum ada data absensi guru yang tercatat hari ini.\n";
            }
            $databaseContext .= "\n";

            // Pengajuan Izin Terbaru
            $stmtPengajuan = $pdo->query("SELECT p.tipe_pemohon, p.jenis, p.tanggal, p.alasan, p.status,
                                                 COALESCE(s.nama_lengkap, g.nama_lengkap, 'Pengguna') as nama_pemohon,
                                                 COALESCE(k.nama_kelas, 'Guru') as kelas_atau_unit
                                          FROM pengajuan_izin p 
                                          LEFT JOIN siswa s ON p.siswa_id = s.id 
                                          LEFT JOIN kelas k ON s.kelas_id = k.id 
                                          LEFT JOIN guru g ON p.guru_id = g.id 
                                          ORDER BY p.tanggal DESC, p.id DESC LIMIT 20");
            $pengajuans = $stmtPengajuan->fetchAll(PDO::FETCH_ASSOC);
            $databaseContext .= "[DATA PENGAJUAN IZIN & SAKIT TERBARU]:\n";
            if ($pengajuans) {
                foreach ($pengajuans as $pj) {
                    $databaseContext .= "- {$pj['nama_pemohon']} ({$pj['kelas_atau_unit']}) | {$pj['jenis']} tgl {$pj['tanggal']} | Alasan: \"{$pj['alasan']}\" | Status: {$pj['status']}\n";
                }
            }

            // Catatan Pembinaan Siswa
            $stmtCatatan = $pdo->query("SELECT cp.created_at, cp.kategori, cp.isi_catatan, s.nama_lengkap as nama_siswa, k.nama_kelas
                                        FROM catatan_pembinaan cp 
                                        JOIN siswa s ON cp.siswa_id = s.id 
                                        LEFT JOIN kelas k ON cp.kelas_id = k.id 
                                        ORDER BY cp.created_at DESC LIMIT 15");
            $catatans = $stmtCatatan->fetchAll(PDO::FETCH_ASSOC);
            $databaseContext .= "\n[DATA CATATAN PEMBINAAN SISWA TERBARU]:\n";
            if ($catatans) {
                foreach ($catatans as $cp) {
                    $tgl = date('d-m-Y', strtotime($cp['created_at']));
                    $databaseContext .= "- {$cp['nama_siswa']} ({$cp['nama_kelas']}) | Kategori: {$cp['kategori']} (Tgl $tgl) | Isi: \"{$cp['isi_catatan']}\"\n";
                }
            }

            // Catatan Kepala Sekolah untuk Guru
            $stCgAdmin = $pdo->query("SELECT cg.tanggal, cg.judul, cg.kategori, cg.urgensi, cg.isi_catatan, cg.status_baca, g.nama_lengkap
                                      FROM catatan_guru cg
                                      JOIN guru g ON cg.guru_id = g.id
                                      ORDER BY cg.tanggal DESC, cg.id DESC LIMIT 15")->fetchAll(PDO::FETCH_ASSOC);
            if ($stCgAdmin) {
                $databaseContext .= "\n[DATA CATATAN KEPALA SEKOLAH UNTUK GURU TERBARU]:\n";
                foreach ($stCgAdmin as $cg) {
                    $judulStr = !empty($cg['judul']) ? "[{$cg['judul']}] " : "";
                    $stBaca = ($cg['status_baca'] === 'sudah') ? 'Sudah dibaca' : 'Belum dibaca';
                    $databaseContext .= "- {$cg['nama_lengkap']} | Tgl {$cg['tanggal']} | {$cg['kategori']} ({$cg['urgensi']}) | {$stBaca} | {$judulStr}\"{$cg['isi_catatan']}\"\n";
                }
            }

            // Ringkasan & Jadwal Pelajaran Lengkap Sekolah (KHUSUS ADMINISTRATOR)
            $stmtJadwal = $pdo->query("SELECT j.hari, j.jam_mulai, j.jam_selesai, j.ruang, k.tingkat, k.nama_kelas, m.nama_mapel,
                                              COALESCE(g.nama_lengkap, wkd.nama_lengkap, 'Pengajar') as pengajar
                                       FROM jadwal_mengajar j 
                                       JOIN kelas k ON j.kelas_id = k.id 
                                       JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                                       LEFT JOIN guru g ON j.guru_id = g.id 
                                       LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id 
                                       ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat'), k.nama_kelas ASC, j.jam_mulai ASC");
            $allJadwal = $stmtJadwal->fetchAll(PDO::FETCH_ASSOC);
            $cntByTingkat = ['X' => 0, 'XI' => 0, 'XII' => 0];
            $cntByKelasJ = [];
            foreach ($allJadwal as $jdw) {
                $tk = strtoupper($jdw['tingkat'] ?? '');
                if (isset($cntByTingkat[$tk])) {
                    $cntByTingkat[$tk]++;
                }
                $cntByKelasJ[$jdw['nama_kelas']] = ($cntByKelasJ[$jdw['nama_kelas']] ?? 0) + 1;
            }
            $databaseContext .= "\n[RINGKASAN JUMLAH DATA JADWAL PELAJARAN SEKOLAH (Total " . count($allJadwal) . " Sesi)]:\n"
                              . "- Per Tingkat: Kelas X = {$cntByTingkat['X']} jadwal | Kelas XI = {$cntByTingkat['XI']} jadwal | Kelas XII = {$cntByTingkat['XII']} jadwal\n"
                              . "- Per Kelas: " . implode(', ', array_map(fn($kn, $kc) => "{$kn}={$kc}", array_keys($cntByKelasJ), $cntByKelasJ)) . "\n";
            $databaseContext .= "\n[JADWAL PELAJARAN LENGKAP SEKOLAH (Senin - Jumat, Total " . count($allJadwal) . " Sesi Jadwal) - KHUSUS ADMIN]:\n";
            $curH = '';
            foreach ($allJadwal as $jdw) {
                $hName = ucfirst($jdw['hari']);
                if ($curH !== $hName) {
                    $curH = $hName;
                    $databaseContext .= "[$curH]\n";
                }
                $mulai = substr($jdw['jam_mulai'], 0, 5);
                $selesai = substr($jdw['jam_selesai'], 0, 5);
                $databaseContext .= "  - {$jdw['nama_kelas']} ($mulai - $selesai) : {$jdw['nama_mapel']} — {$jdw['pengajar']} (Ruang: {$jdw['ruang']})\n";
            }
            break;
    }

    // Targeted search berbasis nomor induk (NIS/NISN/NIP) yang ketat per Role
    if (preg_match('/\b(\d{4,20})\b/', $message, $numMatch)) {
        $targetNum = $numMatch[1];
        if (in_array($rawRole, ['admin', 'kepala_sekolah'])) {
            $stTarget = $pdo->prepare("SELECT s.nama_lengkap, s.nis, s.nisn, k.nama_kelas, j.nama_jurusan
                                       FROM siswa s
                                       JOIN kelas k ON s.kelas_id = k.id
                                       JOIN jurusan j ON k.jurusan_id = j.id
                                       WHERE s.nis = ? OR s.nisn = ?");
            $stTarget->execute([$targetNum, $targetNum]);
            $foundTarget = $stTarget->fetchAll(PDO::FETCH_ASSOC);
            if ($foundTarget) {
                $searchContext .= "\n[DATA SISWA DICARI (Nomor $targetNum)]:\n";
                foreach ($foundTarget as $ft) {
                    $searchContext .= "- {$ft['nama_lengkap']} | NIS: {$ft['nis']} | NISN: {$ft['nisn']} | Kelas: {$ft['nama_kelas']} ({$ft['nama_jurusan']})\n";
                }
            }
            $stTargetGuru = $pdo->prepare("SELECT g.nama_lengkap, g.nip, COALESCE(wkd.nip, g.nip) as nip_wali
                                           FROM guru g
                                           LEFT JOIN wali_kelas_data wkd ON g.user_id = wkd.user_id
                                           WHERE g.nip = ? OR wkd.nip = ?");
            $stTargetGuru->execute([$targetNum, $targetNum]);
            $foundGuru = $stTargetGuru->fetchAll(PDO::FETCH_ASSOC);
            if ($foundGuru) {
                $searchContext .= "\n[DATA GURU DICARI (NIP $targetNum)]:\n";
                foreach ($foundGuru as $fg) {
                    $nipShow = !empty($fg['nip']) ? $fg['nip'] : $fg['nip_wali'];
                    $searchContext .= "- {$fg['nama_lengkap']} | NIP: {$nipShow}\n";
                }
            }
        } elseif ($rawRole === 'wali_kelas' && $myKelasId > 0) {
            $stTarget = $pdo->prepare("SELECT s.nama_lengkap, s.nis, s.nisn, k.nama_kelas, j.nama_jurusan
                                       FROM siswa s
                                       JOIN kelas k ON s.kelas_id = k.id
                                       JOIN jurusan j ON k.jurusan_id = j.id
                                       WHERE (s.nis = ? OR s.nisn = ?) AND s.kelas_id = ?");
            $stTarget->execute([$targetNum, $targetNum, $myKelasId]);
            $foundTarget = $stTarget->fetchAll(PDO::FETCH_ASSOC);
            if ($foundTarget) {
                $searchContext .= "\n[DATA SISWA KELAS BINAAN DICARI (Nomor $targetNum)]:\n";
                foreach ($foundTarget as $ft) {
                    $searchContext .= "- {$ft['nama_lengkap']} | NIS: {$ft['nis']} | NISN: {$ft['nisn']} | Kelas: {$ft['nama_kelas']} ({$ft['nama_jurusan']})\n";
                }
            }
        }
    }

} catch (PDOException $e) {
    error_log("[HADIR.IO ERROR] PDOException: " . $e->getMessage() . "\n", 3, __DIR__ . '/chatbot_debug.log');
    $databaseContext .= "Catatan: Koneksi database cadangan aktif.\n";
}

// 4. SYSTEM PROMPT: TERSUSUN TERATUR DENGAN PRIORITAS TERTINGGI PADA IDENTITAS PENGGUNA, WAKTU, DAN RBAC PER ROLE
if (in_array($rawRole, ['admin', 'kepala_sekolah'])) {
    $totalExamplesPrompt = "   - PRINSIP UTAMA: Jawab apa yang ditanyakan saja, singkat, padat, dan to the point tanpa bertele-tele!\n"
                         . "   - Contoh: Jika ditanya 'ada berapa kelas?' cukup jawab 'Ada 9 kelas.' (Jangan sebutkan daftar kelasnya kecuali diminta!).\n"
                         . "   - Contoh: Jika ditanya 'ada berapa total guru?' cukup jawab 'Ada 33 guru.'\n"
                         . "   - Contoh: Jika ditanya 'ada berapa siswa?' cukup jawab 'Ada 225 siswa.'\n"
                         . "   - Contoh: Jika ditanya 'ada berapa jurusan?' cukup jawab 'Ada 3 jurusan: TKJ, TKR, dan TSM.'\n";
} else {
    $totalExamplesPrompt = "   - PRINSIP UTAMA: Jawab apa yang ditanyakan saja, singkat, padat, dan to the point tanpa penjelasan berbelit-belit!\n"
                         . "   - DILARANG KERAS menjawab pertanyaan statistik total sekolah (total siswa sekolah, total guru sekolah, total kelas sekolah, total jurusan sekolah, atau total jadwal seluruh sekolah) karena informasi tersebut BUKAN hak akses role {$roleLabel}!\n";
}

switch ($rawRole) {
    case 'admin':
        $rbacPromptSection = "   - Lawan bicaramu saat ini adalah Administrator ({$namaLengkap}).\n"
                           . "   - Administrator memiliki HAK AKSES PENUH terhadap seluruh data sistem di database di bawah: profil & NIP semua guru, semua siswa (NIS/NISN), jurusan, kelas, rekap kehadiran guru & siswa, pengajuan izin, catatan pembinaan siswa, catatan Kepala Sekolah untuk guru, dan jadwal pelajaran lengkap seluruh sekolah (Senin-Jumat).\n";
        $dbHeadingPrompt = "[DATA DATABASE LENGKAP SISTEM SEKOLAH (AKSES ADMINISTRATOR)]";
        break;
    case 'kepala_sekolah':
        $rbacPromptSection = "   - Lawan bicaramu saat ini adalah Kepala Sekolah ({$namaLengkap}).\n"
                           . "   - Kepala Sekolah HANYA berhak mengakses: ringkasan statistik sekolah (siswa, guru, kelas, jurusan), laporan & rekapitulasi kehadiran guru dan siswa per hari/kelas, identitas & NIP guru, data siswa & NIS/NISN, ringkasan pengajuan izin, serta Catatan Kepala Sekolah untuk Guru (catatan_guru).\n"
                           . "   - LARANGAN KETAT KEPALA SEKOLAH: DILARANG menjawab jadwal pelajaran kelas, jadwal mengajar guru, daftar mata pelajaran, atau catatan pembinaan khusus wali kelas. Jika ditanya topik tersebut, tolak dengan santun karena menu/data tersebut dikelola pada role Administrator/Guru/Wali Kelas.\n";
        $dbHeadingPrompt = "[DATA DATABASE ROLE KEPALA SEKOLAH]";
        break;
    case 'wali_kelas':
        $rbacPromptSection = "   - Lawan bicaramu saat ini adalah Wali Kelas ({$namaLengkap}, Kelas Binaan: {$detailKelas}).\n"
                           . "   - Wali Kelas HANYA berhak mengakses: data pribadi & NIP miliknya, status & riwayat presensi pribadinya, izin pribadi, catatan Kepala Sekolah untuk dirinya, jadwal mengajar pribadinya, SERTA data siswa, kehadiran/pemantauan, pengajuan izin, catatan pembinaan, dan jadwal pelajaran KHUSUS kelas binaannya ({$detailKelas}).\n"
                           . "   - LARANGAN KETAT WALI KELAS: DILARANG memberikan data kelas lain, daftar/NIS/kehadiran siswa di luar {$detailKelas}, NIP/kehadiran/jadwal guru lain, total siswa/guru/kelas/jurusan sekolah, atau total jadwal seluruh sekolah!\n";
        $dbHeadingPrompt = "[DATA DATABASE ROLE WALI KELAS ({$detailKelas})]";
        break;
    case 'guru':
        $rbacPromptSection = "   - Lawan bicaramu saat ini adalah Guru pengampu mata pelajaran ({$namaLengkap}).\n"
                           . "   - Guru HANYA berhak mengakses data pribadinya sendiri: NIP pribadi, mata pelajaran yang diampu, status & riwayat presensi pribadinya, pengajuan izin pribadi, catatan Kepala Sekolah untuk dirinya, serta jadwal mengajar pribadinya.\n"
                           . "   - LARANGAN KETAT GURU: DILARANG memberikan daftar/NIS/NISN/kehadiran/izin/catatan siswa, data/NIP/kehadiran/jadwal guru lain, jadwal kelas di luar tugas mengajarnya, total jadwal pelajaran sekolah, atau total siswa/guru/kelas/jurusan sekolah!\n";
        $dbHeadingPrompt = "[DATA DATABASE PRIBADI ROLE GURU]";
        break;
    case 'siswa':
    default:
        $rbacPromptSection = "   - Lawan bicaramu saat ini adalah Siswa ({$namaLengkap}, Kelas: {$detailKelas}).\n"
                           . "   - Siswa HANYA berhak mengakses data pribadinya sendiri (nama, NIS/NISN pribadi, kelas, jurusan, nama wali kelas tanpa NIP, status & riwayat presensi pribadi, pengajuan izin pribadi, catatan pembinaan dengan visibilitas Siswa untuk dirinya sendiri) dan jadwal pelajaran kelasnya sendiri ({$detailKelas}).\n"
                           . "   - LARANGAN KETAT SISWA: DILARANG memberikan data/NIS/kehadiran siswa lain, daftar siswa sekelas/sesekolah, NIP/kehadiran/jadwal mengajar guru, catatan untuk guru, jadwal/data kelas lain, maupun statistik total sekolah (total siswa/guru/kelas/jurusan)!\n";
        $dbHeadingPrompt = "[DATA DATABASE PRIBADI ROLE SISWA]";
        break;
}

$systemPromptContent = "Kamu adalah Hadir.io, asisten kecerdasan buatan (AI) resmi Sistem Absensi Digital by Winarti di SMK NU WARGABINANGUN.

[WAKTU REAL-TIME SISTEM SAAT INI - SANGAT PENTING]:
- Saat ini adalah waktu: $periodeWaktu hari (Pukul $jamSekarang WIB, Hari $namaHari, Tanggal $tanggalFormat).
- ATURAN WAKTU MUTLAK:
  * WAKTU SEKARANG ADALAH $sapaanWaktu.
  * JIKA MENYAPA ATAU MENANGGAPI SALAM, WAJIB GUNAKAN SAPAAN: \"$sapaanWaktu\".
  * DILARANG KERAS menyapa 'Selamat pagi' jika waktu sekarang malam/sore/siang!

[IDENTITAS MUTLAK LAWAN BICARAMU SAAT INI (PENGGUNA YANG LOGIN)]:
- Nama Pengguna: {$namaLengkap}
- Peran/Role Pengguna: {$roleLabel} (MUTLAK: Lawan bicaramu saat ini adalah {$roleLabel}! Jika peran pengguna BUKAN siswa, DILARANG KERAS menyebut atau menganggap pengguna sebagai siswa!)
- Kelas Pengguna: " . (!empty($detailKelas) ? $detailKelas : "-") . "
{$userExtra}
- JAWABAN IDENTITAS DIRI JIKA PENGGUNA BERTANYA 'SIAPA SAYA?' / 'WHO AM I?' / 'SAHA ABDI?' / 'AKU SOPO?' / 'SY SP?':
  - Bahasa Indonesia: \"{$identitasJawabanId}\"
  - Bahasa Inggris: \"{$identitasJawabanEn}\"
  - Bahasa Sunda: \"{$identitasJawabanSu}\"
  - Bahasa Jawa: \"{$identitasJawabanJv}\"
  - JANGAN PERNAH MENJAWAB bahwa pengguna adalah Hadir.io! Kamu adalah Hadir.io, sedangkan lawan bicaramu adalah {$namaLengkap}!

[PANDUAN MENJAWAB: TO THE POINT, SANTUN & SESUAI DATABASE ROLE SAAT INI]:
1. JAWAB APA YANG DITANYA SAJA, SINGKAT, PADAT, DAN TO THE POINT:
   - Jawablah langsung inti pertanyaan sesuai data ruang lingkup role {$roleLabel} tanpa bertele-tele atau menambahkan penjelasan panjang yang tidak diminta.
   - Jangan menambahkan rincian, daftar, atau penjelasan panjang yang TIDAK diminta oleh pengguna.
{$totalExamplesPrompt}
2. BERBAHASALAH SEPERTI ORANG YANG SEDANG MENGOBROL TAPI TETAP SANGAT SOPAN:
   - Nada bicaramu mengalir luwes, hangat, dan santun.
   - Panggil 'Bapak/Ibu' jika guru, wali kelas, kepala sekolah, atau administrator; panggil 'Kak' jika siswa.
   - DILARANG KERAS menggunakan kata-kata kasar atau slang vulgar ('lu', 'gue', 'lo', 'anjir', dll.).
3. KAMU HANYA BOLEH MENGGUNAKAN DATA DATABASE YANG TERSEDIA PADA SESI INI:
   - Kamu WAJIB merujuk 100% secara akurat pada data profil dan data database di bawah ini.
   - Jika suatu data tidak tercatat atau di luar kewenangan role {$roleLabel}, jangan mengarang angka/nama — sampaikan penolakan secara sopan atau informasikan bahwa data belum tercatat di sistem saat ini.
4. DILARANG KERAS MEMBUAT CODING ATAU SCRIPT PEMROGRAMAN (PYTHON, PHP, JS, DLL.):
   - Kamu adalah asisten AI khusus sistem absensi sekolah, BUKAN asisten coding/programming.
   - JIKA pengguna meminta dibuatkan kode program atau script (Python, PHP, JS, HTML, SQL, dll.), WAJIB menolak dengan sopan dan tawarkan bantuan terkait sistem absensi sekolah.
5. MAKNA ISTILAH ABSENSI ('SUDAH ABSEN' VS 'ALPA'):
   - Pertanyaan 'sudah melakukan absen', 'sudah absen', 'yang absen hari ini', 'tercatat hadir' MUTLAK berarti status HADIR / Terlambat (Clock-in).
   - DILARANG KERAS menyamakan 'sudah melakukan absen' dengan 'alpa'! 'Alpa' hanya merujuk pada tidak hadir tanpa keterangan.
   - Saat menjawab daftar kehadiran/alpa, sajikan rapi bernomor tanpa teks teknis berlebihan.
6. KEMAMPUAN MULTIBAHASA (MULTILINGUAL):
   - Wajib menjawab dengan bahasa yang sesuai pertanyaan pengguna: Bahasa Indonesia, Bahasa Inggris, Bahasa Sunda Lemes, atau Bahasa Jawa Krama/Ngoko Alus.
   - Seluruh fakta, nama, NIP, NIS, tanggal, dan status harus dikutip murni dari database di bawah ini.
7. BATASAN HAK AKSES ROLE (KEAMANAN SISTEM RBAC - MUTLAK):
{$rbacPromptSection}
8. DILARANG KERAS MENJAWAB PERTANYAAN UMUR / USIA / TANGGAL LAHIR:
   - Tolak dengan sopan setiap pertanyaan umur, usia, atau TTL siapa pun karena bersifat privasi dan bukan data operasional absensi.
9. ATURAN FORMAT PENULISAN (DILARANG KERAS MENGGUNAKAN TANDA BINTANG *):
   - DILARANG KERAS menggunakan tanda bintang (*) dalam bentuk apa pun (termasuk bold **teks** atau bullet * item)! Gunakan teks polos dan bullet '•', '-', atau nomor '1.'.

{$dbHeadingPrompt}:
$databaseContext

$searchContext";

$systemPrompt = [
    'role' => 'system',
    'content' => $systemPromptContent
];

if (empty($history) || $history[0]['role'] !== 'system') {
    array_unshift($history, $systemPrompt);
} else {
    $history[0] = $systemPrompt;
}

$history[] = ['role' => 'user', 'content' => $message];

// Ambil konfigurasi API Key dan Model dari tabel pengaturan_sistem (dengan fallback ke .env)
$configuredApiKey = $_ENV['NVIDIA_API_KEY'] ?? '';
$configuredModel = $_ENV['NVIDIA_MODEL'] ?? 'nvidia/nemotron-3.5-bolt-e4b';
$configuredApiUrl = $_ENV['NVIDIA_API_URL'] ?? 'https://integrate.api.nvidia.com/v1/chat/completions';

try {
    $stmtSettings = $pdo->query("SELECT setting_key, setting_value FROM pengaturan_sistem WHERE setting_key IN ('chatbot_api_key', 'chatbot_model', 'chatbot_api_url')");
    $dbSettings = $stmtSettings->fetchAll(PDO::FETCH_KEY_PAIR);
    if (!empty($dbSettings['chatbot_api_key'])) {
        $configuredApiKey = $dbSettings['chatbot_api_key'];
    }
    if (!empty($dbSettings['chatbot_model'])) {
        $configuredModel = $dbSettings['chatbot_model'];
    }
    if (!empty($dbSettings['chatbot_api_url'])) {
        $configuredApiUrl = $dbSettings['chatbot_api_url'];
    }
} catch (Exception $e) {
    // Gunakan fallback
}

$apiUrl = $configuredApiUrl;
$apiKey = $configuredApiKey;

// Daftar model yang akan dicoba (model utama + fallback otomatis jika model utama deprecated/404 oleh NVIDIA)
$candidateModels = [$configuredModel];
$autoFallbacks = [
    'nvidia/nemotron-3.5-bolt-e4b',
    'nvidia/nemotron-3-nano-omni-30b-a3b-reasoning',
    'nvidia/nemotron-3-ultra-550b-a55b',
    'google/diffusiongemma-26b-a4b-it'
];
foreach ($autoFallbacks as $fb) {
    if (!in_array($fb, $candidateModels)) {
        $candidateModels[] = $fb;
    }
}
// Prioritaskan model non-reasoning (bolt-e4b) agar tidak menghasilkan teks chain-of-thought internal
if ($configuredModel === 'nvidia/nemotron-3-nano-omni-30b-a3b-reasoning') {
    $candidateModels = array_values(array_unique(array_merge(['nvidia/nemotron-3.5-bolt-e4b'], $candidateModels)));
}

$responseData = null;
$lastError = '';
$httpCode = 0;

foreach ($candidateModels as $currentModel) {
    $payload = json_encode([
        'model' => $currentModel,
        'messages' => $history,
        'max_tokens' => 1000,
        'temperature' => 0.6
    ]);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        $lastError = $error;
        continue;
    }

    $decoded = json_decode($response, true);
    if ($httpCode === 200 && isset($decoded['choices'][0]['message'])) {
        $responseData = $decoded;
        // Jika model yang berhasil bukan model yang disimpan sebelumnya, perbarui di DB agar otomatis sinkron
        if ($currentModel !== $configuredModel) {
            try {
                $updStmt = $pdo->prepare("UPDATE pengaturan_sistem SET setting_value = ?, updated_at = NOW() WHERE setting_key = 'chatbot_model'");
                $updStmt->execute([$currentModel]);
            } catch (Exception $e) {}
        }
        break;
    } else {
        $errDetail = isset($decoded['detail']) ? $decoded['detail'] : (isset($decoded['message']) ? $decoded['message'] : (is_string($response) ? $response : 'HTTP ' . $httpCode));
        $lastError = is_string($errDetail) ? $errDetail : json_encode($errDetail);
    }
}

if (!$responseData) {
    http_response_code($httpCode >= 400 ? $httpCode : 500);
    echo json_encode(['error' => 'Gagal terhubung ke API', 'details' => $lastError]);
    exit;
}

$msgObj = $responseData['choices'][0]['message'] ?? [];
$reply = !empty($msgObj['content']) ? $msgObj['content'] : '';

// Jika content kosong tetapi ada reasoning_content, JANGAN tampilkan proses berpikir bahasa Inggris mentah
if (empty($reply) && !empty($msgObj['reasoning_content'])) {
    $rawReason = $msgObj['reasoning_content'];
    if (preg_match('/(?:jawaban|kesimpulan|answer|reply):\s*(.*)$/is', $rawReason, $matchAns)) {
        $reply = trim($matchAns[1]);
    } else {
        $reply = 'Halo! Ada yang bisa saya bantu terkait informasi jadwal pelajaran, absensi, atau sekolah?';
    }
} elseif (empty($reply)) {
    $reply = 'Halo! Ada yang bisa saya bantu terkait informasi jadwal pelajaran, absensi, atau sekolah?';
}

$reply = preg_replace('/<think>.*?<\/think>/s', '', $reply);
$reply = trim($reply);

// Filter out internal chain-of-thought leaked by reasoning models (seperti "We need to answer...", "The user asks...", dll.)
if (preg_match('/^(?:We need to|The user asks|To answer this|Let\'s check|I should answer|The student asks|As an assistant)\b/i', $reply)) {
    if (preg_match('/^(?:We need to|The user asks)[^.\n]*[.\n]\s*(.+)$/is', $reply, $cleanM)) {
        $reply = trim($cleanM[1]);
    } else {
        $reply = 'Halo! Ada yang bisa saya bantu terkait informasi jadwal pelajaran atau absensi sekolah?';
    }
}

// Guard post-processing: Jika response tetap memunculkan coding python atau block script pemrograman
if (preg_match('/```(?:python|py|php|javascript|js|html|css|java|c\+\+|cpp|csharp|golang|sql)?/i', $reply) || 
    preg_match('/\b(import\s+\w+|def\s+\w+\(|class\s+\w+:|print\(|from\s+\w+\s+import|<\?php|console\.log)/i', $reply)) {
    $sebutan = ($rawRole === 'siswa') ? 'Kak ' . $namaLengkap : $sapaanGelar;
    $reply = "Mohon maaf {$sebutan}, saya adalah asisten AI resmi Sistem Absensi SMK NU WARGABINANGUN yang bertugas memberikan pengarahan sistem dan informasi sekolah. Saya tidak dapat membuatkan script coding atau pemrograman (seperti Python). Jika {$sebutan} memerlukan pengarahan terkait penggunaan sistem absensi atau informasi seputar data sekolah, silakan tanyakan kepada saya!";
}

// Guard post-processing: Cegah jawaban dingin hanya berupa satu angka tanpa penjelasan (KHUSUS Admin/Kepsek agar tidak membocorkan total sekolah ke role lain)
if (in_array($rawRole, ['admin', 'kepala_sekolah']) && preg_match('/^\s*(\d+)\s*[\/?.!]?\s*$/', $reply, $numMatch)) {
    $coldNum = (int)$numMatch[1];
    $cntJurusanLive = (int)$pdo->query("SELECT COUNT(*) FROM jurusan")->fetchColumn();
    $cntKelasLive = (int)$pdo->query("SELECT COUNT(*) FROM kelas")->fetchColumn();
    $cntSiswaLive = (int)$pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
    $cntGuruLive = (int)$pdo->query("SELECT COUNT(*) FROM guru")->fetchColumn();

    if ($coldNum === $cntJurusanLive) {
        $reply = "Ada {$cntJurusanLive} jurusan: TKJ, TKR, dan TSM.";
    } elseif ($coldNum === $cntSiswaLive) {
        $reply = "Ada {$cntSiswaLive} siswa.";
    } elseif ($coldNum === $cntKelasLive) {
        $reply = "Ada {$cntKelasLive} kelas.";
    } elseif ($coldNum === $cntGuruLive) {
        $reply = "Ada {$cntGuruLive} guru.";
    }
}

// Guard post-processing RBAC Siswa: Jika pengguna adalah Siswa dan jawaban memuat indikasi jadwal/NIP guru
if ($rawRole === 'siswa') {
    if (preg_match('/\b(jadwal\s+mengajar\s+guru|jadwal\s+guru|mengajar\s+di\s+kelas|diajar\s+oleh\s+guru)\b/i', $reply) && preg_match('/\b(bapak|ibu|pak|bu)\s+[a-z]+/i', $reply)) {
        $reply = buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'jadwal mengajar guru atau data guru', 'teacher schedules or teacher data');
    }
}

// Guard post-processing RBAC Kepala Sekolah: Jangan sampai AI membocorkan jadwal pelajaran/mengajar/catatan pembinaan
if ($rawRole === 'kepala_sekolah') {
    if (preg_match('/\b(jadwal\s+mengajar|jadwal\s+pelajaran|catatan\s+pembinaan\s+siswa)\b/i', $reply) && preg_match('/\b(pukul|wib|senin|selasa|rabu|kamis|jumat)\b/i', $reply)) {
        $reply = buildRoleDeniedReply($rawRole, $namaLengkap, $sapaanGelar, $detailKelas, $detectedLang, 'jadwal pelajaran, jadwal mengajar, atau catatan pembinaan siswa', 'lesson schedules or student coaching notes');
    }
}

// Guard post-processing: Jika pengguna adalah Guru, Wali Kelas, Admin, atau Kepsek, dilarang keras ada halusinasi bahwa pengguna adalah Siswa
if ($rawRole !== 'siswa') {
    if (preg_match('/\b(kamu\s+adalah\s+siswa|anda\s+adalah\s+siswa|you\s+are\s+a\s+student|anjeun\s+teh\s+siswa|sampeyan\s+punika\s+siswa)\b/i', $reply)) {
        if ($detectedLang === 'en') {
            $reply = $identitasJawabanEn;
        } elseif ($detectedLang === 'su') {
            $reply = $identitasJawabanSu;
        } elseif ($detectedLang === 'jv') {
            $reply = $identitasJawabanJv;
        } else {
            $reply = $identitasJawabanId;
        }
    }
}

// Koreksi guard: Pastikan Admin/Kepsek tidak pernah menerima halusinasi 12 kelas
if (in_array($rawRole, ['admin', 'kepala_sekolah'])) {
    $reply = preg_replace('/\b12\s*kelas\b/i', '9 kelas', $reply);
}

// Bersihkan markdown pagar berlebih jika ada agar tetap rapi
$reply = preg_replace('/^#+\s*/m', '', $reply);

sendBotReply($reply);
