<?php
session_start();
// Simulate logged in user if not set
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Admin or some user
    $_SESSION['role'] = 'Guru'; 
}

$userContext = "Data belum tersedia.";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=db_absensi_smk;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'] ?? 'Guest';
        $userContext = "Role User: $role\n";

        if ($role === 'Guru' || $role === 'Wali Kelas') {
            $stmt = $pdo->prepare("SELECT id, nama FROM guru WHERE user_id = ?");
            $stmt->execute([$userId]);
            $guru = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($guru) {
                $userContext .= "Nama Guru: {$guru['nama']}\n";
                // Ambil jadwal mengajar hari ini
                $hariIni = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][date('w')];
                $stmtJadwal = $pdo->prepare("SELECT j.jam_mulai, j.mata_pelajaran, k.nama_kelas 
                                             FROM jadwal_mengajar j 
                                             JOIN kelas k ON j.kelas_id = k.id 
                                             WHERE j.guru_id = ? AND j.hari = ? ORDER BY j.jam_mulai ASC");
                $stmtJadwal->execute([$guru['id'], $hariIni]);
                $jadwals = $stmtJadwal->fetchAll(PDO::FETCH_ASSOC);
                
                $userContext .= "Jadwal Mengajar Anda Hari Ini ($hariIni):\n";
                if ($jadwals) {
                    foreach ($jadwals as $jdw) {
                        $userContext .= "- " . substr($jdw['jam_mulai'], 0, 5) . " – {$jdw['mata_pelajaran']} – Kelas: {$jdw['nama_kelas']}\n";
                    }
                } else {
                    $userContext .= "Tidak ada jadwal mengajar hari ini.\n";
                }
            } else {
                $userContext .= "Data guru tidak ditemukan untuk user_id: $userId\n";
            }
        }
    }
} catch (Exception $e) {
    $userContext = "Error: " . $e->getMessage();
}

echo "<pre>";
echo $userContext;
echo "\n\nAll Jadwal in DB:\n";
$stmt = $pdo->query("SELECT * FROM jadwal_mengajar");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";
