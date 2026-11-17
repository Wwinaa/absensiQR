<?php
/**
 * Dashboard Controller
 * Handles role-based dashboards
 */
class DashboardController extends Controller {

    public function index() {
        // Base index requires login, redirects to specific role dashboard
        require_login();
        
        $role = $_SESSION['user_role'] ?? '';
        if (empty($role)) {
            // Jika role kosong, jangan redirect ke dashboard/ (akan terjadi loop)
            redirect('error/forbidden');
        } else {
            redirect('dashboard/' . $role);
        }
    }

    public function admin() {
        // Server-side authorization guard
        require_role(['admin']);
        $dashboardModel = $this->model('DashboardModel');
        $stats = $dashboardModel->getAdminStats(date('Y-m-d'));

        $data = [
            'title' => 'Dashboard Admin',
            'role_name' => 'Administrator',
            'stats' => $stats
        ];
        $this->view('dashboard/admin', $data);
    }

    public function kepala_sekolah() {
        require_role(['kepala_sekolah']);
        
        $tanggal = date('Y-m-d');

        $dashboardModel = $this->model('DashboardModel');
        $stats = $dashboardModel->getKepalaSekolahStats($tanggal);

        $kelasModel = $this->model('KelasModel');
        $allKelas = $kelasModel ? $kelasModel->getAllKelas() : [];

        // Ambil nama kepala sekolah dari database (tabel users)
        $userModel = $this->model('UserModel');
        $user = $userModel->getUserById($_SESSION['user_id']);
        $kepsek_nama = (!empty($user->nama_lengkap)) ? $user->nama_lengkap : 'Drs. H. Ahmad Fauzi, M.Pd';

        $data = [
            'title' => 'Dashboard Kepala Sekolah',
            'role_name' => 'Kepala Sekolah',
            'kepsek_nama' => $kepsek_nama,
            'stats' => $stats,
            'tanggal' => $tanggal,
            'all_kelas' => $allKelas
        ];
        $this->view('dashboard/kepala_sekolah', $data);
    }

    public function kepsek() {
        $this->kepala_sekolah();
    }

    public function guru() {
        require_role(['guru', 'wali_kelas']); // Walikelas is also a guru usually, but we have specific role 'wali_kelas'
        $guruModel = $this->model('GuruModel');
        $guru = $guruModel->getGuruByUserId($_SESSION['user_id']);
        $stats = null;
        if ($guru) {
            $jadwal = $guruModel->getJadwalHariIni($guru->id);
            $absensiModel = $this->model('AbsensiModel');
            $absensi = $absensiModel->cekAbsensiGuruHariIni($guru->id, date('Y-m-d'));

            $dashboardModel = $this->model('DashboardModel');
            $stats = $dashboardModel->getGuruStats($guru->id, date('Y-m-d'));
        }
        
        $data = [
            'title' => 'Dashboard Guru',
            'role_name' => 'Guru',
            'guru' => $guru,
            'jadwal' => $jadwal,
            'absensi' => $absensi,
            'stats' => $stats
        ];
        $this->view('dashboard/guru', $data);
    }

    public function wali_kelas() {
        require_role(['wali_kelas']);
        
        $wkModel = $this->model('WaliKelasModel');
        $wk = $wkModel->getWaliKelasByUserId($_SESSION['user_id']);
        
        $stats = null;
        $absensi = null;
        $jadwal = [];
        $personal_stats = null;

        if ($wk) {
            $dashboardModel = $this->model('DashboardModel');
            $stats = $dashboardModel->getWaliKelasStats($wk->id, date('Y-m-d'));

            $absensiModel = $this->model('AbsensiModel');
            $absensi = $absensiModel->cekAbsensiWaliKelasHariIni($wk->id, date('Y-m-d'));

            // SINKRONISASI: Wali kelas adalah juga seorang guru.
            // Jika scan QR tercatat di absensi_guru, pastikan absensi_wali_kelas tersinkronkan seketika!
            $guruModel = $this->model('GuruModel');
            $guruData = $guruModel->getGuruByUserId($wk->user_id);
            if ($guruData) {
                $absenGuru = $absensiModel->cekAbsensiGuruHariIni($guruData->id, date('Y-m-d'));
                if ($absenGuru && (in_array(strtolower($absenGuru->status), ['hadir', 'izin', 'sakit']) || !empty($absenGuru->jam_masuk))) {
                    if (!$absensi || strtolower($absensi->status) !== strtolower($absenGuru->status) || empty($absensi->jam_masuk)) {
                        $dbSync = new Database();
                        $dbSync->query("INSERT INTO absensi_wali_kelas (wali_kelas_id, tanggal, jam_masuk, jam_pulang, status, keterangan) 
                                       VALUES (:wkid, :tgl, :jm, :jp, :st, :ket)
                                       ON DUPLICATE KEY UPDATE jam_masuk = VALUES(jam_masuk), jam_pulang = VALUES(jam_pulang), status = VALUES(status), keterangan = VALUES(keterangan)");
                        $dbSync->bind(':wkid', $wk->id);
                        $dbSync->bind(':tgl', date('Y-m-d'));
                        $dbSync->bind(':jm', $absenGuru->jam_masuk);
                        $dbSync->bind(':jp', $absenGuru->jam_pulang);
                        $dbSync->bind(':st', $absenGuru->status);
                        $dbSync->bind(':ket', $absenGuru->keterangan);
                        $dbSync->execute();
                        
                        $absensi = $absenGuru;
                    }
                }
            }

            $personal_stats = $absensiModel->getStatsWaliKelas($wk->id);

            $days = [
                'Sunday' => 'minggu',
                'Monday' => 'senin',
                'Tuesday' => 'selasa',
                'Wednesday' => 'rabu',
                'Thursday' => 'kamis',
                'Friday' => 'jumat',
                'Saturday' => 'sabtu'
            ];
            $hariIni = $days[date('l')] ?? 'senin';
            $jadwalModel = $this->model('JadwalModel');
            $jadwal = $jadwalModel->getJadwalWaliKelas($wk->id, $hariIni);
        }

        $data = [
            'title' => 'Dashboard Wali Kelas',
            'role_name' => 'Wali Kelas & Pengajar',
            'wali_kelas' => $wk,
            'absensi' => $absensi,
            'jadwal' => $jadwal,
            'stats' => $stats,
            'personal_stats' => $personal_stats
        ];
        $this->view('dashboard/wali_kelas', $data);
    }

    public function siswa() {
        require_role(['siswa']);
        
        $siswaModel = $this->model('SiswaModel');
        $siswa = $siswaModel->getSiswaByUserId($_SESSION['user_id']);
        
        // Dapatkan nama kelas dan jurusan
        if ($siswa) {
            $siswaFull = $siswaModel->getSiswaById($siswa->id);
            $siswa = $siswaFull ?: $siswa;
        }

        $absensi = null;
        $stats = null;
        if ($siswa) {
            $absensiModel = $this->model('AbsensiModel');
            $absensi = $absensiModel->cekAbsensiSiswaHariIni($siswa->id, date('Y-m-d'));

            $dashboardModel = $this->model('DashboardModel');
            $stats = $dashboardModel->getSiswaStats($siswa->id);
        }

        $data = [
            'title' => 'Dashboard Siswa',
            'role_name' => 'Siswa',
            'siswa' => $siswa,
            'absensi' => $absensi,
            'stats' => $stats
        ];
        $this->view('dashboard/siswa', $data);
    }
}
