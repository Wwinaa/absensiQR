<?php
class GuruController extends Controller {
    public function absensi() {
        require_role(['guru', 'wali_kelas']);
        $data = ['title' => 'Absensi Guru'];
        $this->view('placeholder', $data);
    }

    public function jadwal() {
        require_role(['guru', 'wali_kelas']);
        
        $user_id = $_SESSION['user_id'];
        $guruModel = $this->model('GuruModel');
        $guru = $guruModel->getGuruByUserId($user_id);
        
        $hari = $_GET['hari'] ?? '';
        
        $jadwalModel = $this->model('JadwalModel');
        $jadwal = $guru ? $jadwalModel->getJadwalGuru($guru->id, $hari) : [];
        
        $data = [
            'title' => 'Jadwal / Jam Mengajar',
            'jadwal' => $jadwal,
            'hari_aktif' => $hari
        ];
        $this->view('guru/jadwal/index', $data);
    }

    public function riwayat() {
        require_role(['guru', 'wali_kelas']);
        
        $user_id = $_SESSION['user_id'];
        $guruModel = $this->model('GuruModel');
        $guru = $guruModel->getGuruByUserId($user_id);
        
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
        
        $totalData = $absensiModel->getRiwayatGuruFiltered($guru->id, $filters, 0, 0, true);
        $totalPages = ceil($totalData / $limit);
        
        $riwayat = $absensiModel->getRiwayatGuruFiltered($guru->id, $filters, $limit, $offset);
        $stats = $absensiModel->getStatsGuru($guru->id, $filters);
        
        $data = [
            'title'      => 'Riwayat Absensi Pribadi',
            'guru'       => $guru,
            'riwayat'    => $riwayat,
            'stats'      => $stats,
            'filters'    => $filters,
            'page'       => $page,
            'totalPages' => $totalPages
        ];
        
        $this->view('guru/riwayat', $data);
    }

    // ===================================================
    // PENGAJUAN IZIN / SAKIT (ROLE GURU)
    // ===================================================

    public function pengajuan() {
        require_role(['guru', 'wali_kelas']);
        $user_id = $_SESSION['user_id'];

        $guruModel      = $this->model('GuruModel');
        $guru           = $guruModel->getGuruByUserId($user_id);
        $pengajuanModel = $this->model('PengajuanModel');

        // Pagination
        $page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total      = $guru ? $pengajuanModel->countPengajuanByGuru($guru->id) : 0;
        $totalPages = $total > 0 ? ceil($total / $limit) : 1;
        $list       = $guru ? $pengajuanModel->getPengajuanByGuru($guru->id, $limit, $offset) : [];

        $data = [
            'title'      => 'Pengajuan Izin / Sakit Guru',
            'guru'       => $guru,
            'list'       => $list,
            'page'       => $page,
            'totalPages' => $totalPages,
        ];
        $this->view('guru/pengajuan/index', $data);
    }

    public function pengajuan_create() {
        require_role(['guru', 'wali_kelas']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('guru/pengajuan');
            return;
        }

        $user_id   = $_SESSION['user_id'];
        $guruModel = $this->model('GuruModel');
        $guru      = $guruModel->getGuruByUserId($user_id);

        if (!$guru) {
            $this->flash('error', 'Data guru tidak ditemukan.');
            $this->redirect('guru/pengajuan');
            return;
        }

        // Validasi input
        $jenis   = trim($_POST['jenis']   ?? '');
        $tanggal = trim($_POST['tanggal'] ?? '');
        $alasan  = trim($_POST['alasan']  ?? '');

        if (!in_array($jenis, ['Izin', 'Sakit'])) {
            $this->flash('error', 'Jenis pengajuan tidak valid.');
            $this->redirect('guru/pengajuan');
            return;
        }
        if (empty($tanggal)) {
            $this->flash('error', 'Tanggal izin/sakit wajib diisi.');
            $this->redirect('guru/pengajuan');
            return;
        }
        if (empty($alasan)) {
            $this->flash('error', 'Alasan pengajuan wajib diisi.');
            $this->redirect('guru/pengajuan');
            return;
        }

        // Cek double pengajuan (pengajuan hanya boleh 1x dalam 1 hari)
        $pengajuanModel = $this->model('PengajuanModel');
        if ($pengajuanModel->cekSudahPengajuanGuru($guru->id, $tanggal)) {
            $this->flash('error', 'Pengajuan gagal! Anda sudah melakukan pengajuan izin/sakit hari ini atau pada tanggal tersebut. Pengajuan hanya dapat dilakukan 1 kali dalam 1 hari.');
            $this->redirect('guru/pengajuan');
            return;
        }

        // Cek apakah guru sudah melakukan scan presensi hadir pada tanggal tersebut
        $absensiModel = $this->model('AbsensiModel');
        $cekAbsen = $absensiModel->cekAbsensiGuruHariIni($guru->id, $tanggal);
        if ($cekAbsen && (!empty($cekAbsen->jam_masuk) || strtolower($cekAbsen->status ?? '') === 'hadir')) {
            $jamInfo = !empty($cekAbsen->jam_masuk) ? ' pada pukul ' . substr($cekAbsen->jam_masuk, 0, 5) . ' WIB' : '';
            $this->flash('error', 'Pengajuan ditolak! Anda sudah tercatat melakukan absensi kehadiran' . $jamInfo . '. Tidak dapat mengajukan izin/sakit jika sudah melakukan scan absensi.');
            $this->redirect('guru/pengajuan');
            return;
        }

        // Validasi dan upload bukti foto (WAJIB)
        if (empty($_FILES['bukti_foto']['name'])) {
            $this->flash('error', 'Bukti foto wajib diunggah. Pengajuan gagal.');
            $this->redirect('guru/pengajuan');
            return;
        }

        $uploadDir  = 'public/uploads/pengajuan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmp    = $_FILES['bukti_foto']['tmp_name'];
        $fileName   = $_FILES['bukti_foto']['name'];
        $fileSize   = $_FILES['bukti_foto']['size'];
        $fileExt    = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        if (!in_array($fileExt, $allowedExt)) {
            $this->flash('error', 'Format file tidak diizinkan. Gunakan JPG, PNG, WEBP, atau PDF.');
            $this->redirect('guru/pengajuan');
            return;
        }

        if ($fileSize > 5 * 1024 * 1024) {
            $this->flash('error', 'Ukuran file maksimal 5 MB.');
            $this->redirect('guru/pengajuan');
            return;
        }

        $newFileName = 'pengajuan_guru_' . $guru->id . '_' . time() . '.' . $fileExt;
        $destPath    = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmp, $destPath)) {
            $this->flash('error', 'Gagal mengunggah bukti foto.');
            $this->redirect('guru/pengajuan');
            return;
        }

        $pengajuanModel = $this->model('PengajuanModel');
        $saved = $pengajuanModel->createPengajuanGuru([
            'guru_id'    => $guru->id,
            'jenis'      => $jenis,
            'tanggal'    => $tanggal,
            'alasan'     => $alasan,
            'bukti_foto' => $newFileName
        ]);

        if ($saved) {
            // Langsung catat / update kehadiran guru sebagai izin atau sakit (tanpa menunggu persetujuan)
            $absensiModel = $this->model('AbsensiModel');
            $jenisStatus  = strtolower($jenis); // 'izin' atau 'sakit'
            $keterangan   = $alasan;

            $existing = $absensiModel->cekAbsensiGuruHariIni($guru->id, $tanggal);
            $db = new Database();
            if ($existing) {
                $db->query("UPDATE absensi_guru SET status = :status, keterangan = :ket, jam_masuk = NULL, jam_pulang = NULL WHERE id = :id");
                $db->bind(':status', $jenisStatus);
                $db->bind(':ket',    $keterangan);
                $db->bind(':id',     $existing->id);
                $db->execute();
            } else {
                $db->query("INSERT INTO absensi_guru (guru_id, tanggal, jam_masuk, jam_pulang, status, keterangan) 
                            VALUES (:guru_id, :tanggal, NULL, NULL, :status, :ket)");
                $db->bind(':guru_id', $guru->id);
                $db->bind(':tanggal', $tanggal);
                $db->bind(':status',  $jenisStatus);
                $db->bind(':ket',     $keterangan);
                $db->execute();
            }

            $this->flash('success', 'Pengajuan ' . $jenis . ' berhasil dikirim dan kehadiran telah otomatis tercatat.');
        } else {
            if (file_exists($destPath)) unlink($destPath);
            $this->flash('error', 'Terjadi kesalahan sistem saat menyimpan pengajuan.');
        }

        $this->redirect('guru/pengajuan');
    }

    public function pengajuan_delete($id) {
        require_role(['guru', 'wali_kelas']);
        $this->flash('error', 'Pengajuan yang sudah dibuat tidak dapat dibatalkan.');
        $this->redirect('guru/pengajuan');
    }

    /**
     * Catatan dari Kepala Sekolah untuk Guru / Wali Kelas
     */
    public function catatan() {
        require_role(['guru', 'wali_kelas']);
        $user_id = $_SESSION['user_id'];
        $guruModel = $this->model('GuruModel');
        $guru = $guruModel->getGuruByUserId($user_id);

        $catatanModel = $this->model('CatatanGuruModel');
        $catatan = $guru ? $catatanModel->getCatatanByGuruId($guru->id) : [];

        // Tandai catatan sebagai sudah dibaca saat guru mengakses halaman ini
        if ($guru && !empty($catatan)) {
            foreach ($catatan as $c) {
                if ($c->status_baca === 'belum') {
                    $catatanModel->markAsRead($c->id, $guru->id);
                }
            }
        }

        $data = [
            'title' => 'Catatan dari Kepala Sekolah',
            'guru' => $guru,
            'catatan' => $catatan
        ];

        $this->view('guru/catatan_kepsek', $data);
    }
}
