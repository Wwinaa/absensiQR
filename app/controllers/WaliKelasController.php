<?php
class WaliKelasController extends Controller {
    public function absensi() {
        require_role(['wali_kelas']);
        
        $user_id = $_SESSION['user_id'];
        
        // The user is a wali kelas, fetch from wali_kelas_data
        $wkModel = $this->model('WaliKelasModel');
        $guru = $wkModel->getWaliKelasByUserId($user_id);
        
        $kelasModel = $this->model('KelasModel');
        // We need a method to get class by wali_kelas_id. Wait, KelasModel doesn't have it directly?
        // Let's assume we fetch all classes and find it, or write a custom query here.
        // For simplicity, assuming KelasModel has getKelasByWaliKelasId, or we use db directly here.
        // Since we are in the Controller, let's use the DB instance if needed, or add to KelasModel.
        $kelas = $kelasModel->getKelasByWaliKelas($guru->id);
        
        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        
        $absensi = [];
        if ($kelas) {
            $absensiModel = $this->model('AbsensiModel');
            $absensi = $absensiModel->getAbsensiSiswaByKelasTanggal($kelas->id, $tanggal);
        }

        $data = [
            'title' => 'Pantau Absensi Kelas',
            'kelas' => $kelas,
            'absensi' => $absensi,
            'tanggal' => $tanggal
        ];
        
        $this->view('walikelas/absensi', $data);
    }

    public function pemantauan() {
        require_role(['wali_kelas']);
        $user_id = $_SESSION['user_id'];
        
        $wkModel = $this->model('WaliKelasModel');
        $guru = $wkModel->getWaliKelasByUserId($user_id);
        
        $kelasModel = $this->model('KelasModel');
        $kelas = $kelasModel->getKelasByWaliKelas($guru->id);
        
        $siswaStats = [];
        if ($kelas) {
            $db = new Database();
            $db->query("
                SELECT s.id, s.nis, s.nama_lengkap,
                       SUM(CASE WHEN LOWER(a.status) = 'hadir' THEN 1 ELSE 0 END) as hadir,
                       SUM(CASE WHEN LOWER(a.status) = 'izin' THEN 1 ELSE 0 END) as izin,
                       SUM(CASE WHEN LOWER(a.status) = 'sakit' THEN 1 ELSE 0 END) as sakit,
                       SUM(CASE WHEN LOWER(a.status) = 'alpa' THEN 1 ELSE 0 END) as alpa,
                       COUNT(a.id) as total_hari
                FROM siswa s
                LEFT JOIN absensi_siswa a ON (s.id = a.siswa_id AND DAYOFWEEK(a.tanggal) NOT IN (1, 7))
                WHERE s.kelas_id = :kelas_id
                GROUP BY s.id
                ORDER BY s.nama_lengkap ASC
            ");
            $db->bind(':kelas_id', $kelas->id);
            $siswaStats = $db->resultSet();
        }

        $data = [
            'title' => 'Pemantauan Kehadiran Siswa',
            'kelas' => $kelas,
            'siswaStats' => $siswaStats
        ];
        
        $this->view('walikelas/pemantauan', $data);
    }

    public function catatan_pembinaan($action = 'index', $id = null) {
        require_role(['wali_kelas']);
        $user_id = $_SESSION['user_id'];
        
        $wkModel = $this->model('WaliKelasModel');
        $guru = $wkModel->getWaliKelasByUserId($user_id);
        
        $kelasModel = $this->model('KelasModel');
        $kelas = $kelasModel->getKelasByWaliKelas($guru->id);
        
        $catatanModel = $this->model('CatatanModel');
        $siswaModel = $this->model('SiswaModel');

        if ($action === 'tambah' && $this->isPost()) {
            if (!$kelas) {
                $this->flash('danger', 'Anda belum ditugaskan pada kelas mana pun.');
                $this->redirect('wali_kelas/catatan_pembinaan');
            }
            $data = [
                'siswa_id' => $_POST['siswa_id'],
                'kelas_id' => $kelas->id,
                'pembuat_user_id' => $user_id,
                'pembuat_role' => 'Wali Kelas',
                'kategori' => $_POST['kategori'],
                'isi_catatan' => $_POST['isi_catatan'],
                'visibilitas' => $_POST['visibilitas'] ?? 'Siswa'
            ];
            if ($catatanModel->tambahCatatan($data)) {
                $this->flash('success', 'Catatan pembinaan berhasil ditambahkan.');
            } else {
                $this->flash('danger', 'Gagal menambah catatan pembinaan.');
            }
            $this->redirect('wali_kelas/catatan_pembinaan');
        } elseif ($action === 'edit' && $this->isPost()) {
            $catatanId = $_POST['id'] ?? null;
            $catatan = $catatanId ? $catatanModel->getCatatanById($catatanId) : null;
            if ($catatan && ($catatan->pembuat_user_id == $user_id || ($kelas && $catatan->kelas_id == $kelas->id))) {
                $data = [
                    'id' => $catatanId,
                    'kategori' => $_POST['kategori'] ?? $catatan->kategori,
                    'isi_catatan' => $_POST['isi_catatan'] ?? $catatan->isi_catatan,
                    'visibilitas' => $_POST['visibilitas'] ?? 'Siswa'
                ];
                $catatanModel->editCatatan($data);
                $this->flash('success', 'Catatan pembinaan berhasil diedit.');
            } else {
                $this->flash('danger', 'Anda tidak berhak mengedit catatan ini.');
            }
            $this->redirect('wali-kelas/catatan-pembinaan');
        } elseif ($action === 'hapus' && $id) {
            $catatan = $catatanModel->getCatatanById($id);
            if ($catatan && ($catatan->pembuat_user_id == $user_id || ($kelas && $catatan->kelas_id == $kelas->id))) {
                $catatanModel->hapusCatatan($id);
                $this->flash('success', 'Catatan pembinaan berhasil dihapus.');
            } else {
                $this->flash('danger', 'Anda tidak berhak menghapus catatan ini.');
            }
            $this->redirect('wali-kelas/catatan-pembinaan');
        }

        $catatan = [];
        $siswa_list = [];
        if ($kelas) {
            $catatan = $catatanModel->getAllCatatan(['kelas_id' => $kelas->id]);
            $db = new Database();
            $db->query("SELECT id, nama_lengkap, nis FROM siswa WHERE kelas_id = :kelas_id ORDER BY nama_lengkap ASC");
            $db->bind(':kelas_id', $kelas->id);
            $siswa_list = $db->resultSet();
        }

        $data = [
            'title' => 'Catatan Pembinaan Siswa',
            'kelas' => $kelas,
            'catatan' => $catatan,
            'siswa_list' => $siswa_list,
            'user_id' => $user_id
        ];
        
        $this->view('walikelas/catatan', $data);
    }

    public function jadwal() {
        require_role(['wali_kelas']);
        
        $user_id = $_SESSION['user_id'];
        $wkModel = $this->model('WaliKelasModel');
        $wk = $wkModel->getWaliKelasByUserId($user_id);
        
        $hari = $_GET['hari'] ?? '';
        
        $jadwalModel = $this->model('JadwalModel');
        $jadwal = $wk ? $jadwalModel->getJadwalWaliKelas($wk->id, $hari) : [];
        $jadwalKelas = ($wk && !empty($wk->kelas_id)) ? $jadwalModel->getJadwalSiswa($wk->kelas_id, $hari) : [];
        
        $data = [
            'title' => 'Jadwal / Jam Mengajar Wali Kelas',
            'jadwal' => $jadwal,
            'jadwal_kelas' => $jadwalKelas,
            'hari_aktif' => $hari,
            'wali_kelas' => $wk
        ];
        $this->view('walikelas/jadwal', $data);
    }

    public function riwayat() {
        require_role(['wali_kelas']);
        
        $user_id = $_SESSION['user_id'];
        $wkModel = $this->model('WaliKelasModel');
        $wk = $wkModel->getWaliKelasByUserId($user_id);
        
        $absensiModel = $this->model('AbsensiModel');
        
        // Filter processing (Default dimulai dari 01/10/2026)
        $filters = [
            'start_date' => !empty($_GET['start_date']) ? $_GET['start_date'] : '2026-10-01',
            'end_date'   => $_GET['end_date'] ?? '',
            'status'     => $_GET['status'] ?? ''
        ];
        
        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $totalData = $wk ? $absensiModel->getRiwayatWaliKelasFiltered($wk->id, $filters, 0, 0, true) : 0;
        $totalPages = ceil($totalData / $limit);
        
        $riwayat = $wk ? $absensiModel->getRiwayatWaliKelasFiltered($wk->id, $filters, $limit, $offset) : [];
        $stats = $wk ? $absensiModel->getStatsWaliKelas($wk->id, $filters) : ['total' => 0, 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
        
        $data = [
            'title'      => 'Riwayat Absensi Pribadi (Wali Kelas)',
            'wali_kelas' => $wk,
            'riwayat'    => $riwayat,
            'stats'      => $stats,
            'filters'    => $filters,
            'page'       => $page,
            'totalPages' => $totalPages
        ];
        
        $this->view('walikelas/riwayat', $data);
    }
}
