<?php
class SiswaController extends Controller {
    public function scan() {
        require_role(['siswa']);
        redirect('absensi/scan');
    }

    public function jadwal() {
        require_role(['siswa']);
        
        $user_id = $_SESSION['user_id'];
        $siswaModel = $this->model('SiswaModel');
        $siswa = $siswaModel->getSiswaByUserId($user_id);
        
        $hari = $_GET['hari'] ?? '';
        
        $jadwalModel = $this->model('JadwalModel');
        $jadwal = $siswa ? $jadwalModel->getJadwalSiswa($siswa->kelas_id, $hari) : [];
        
        $data = [
            'title' => 'Jadwal Pelajaran',
            'jadwal' => $jadwal,
            'hari_aktif' => $hari
        ];
        $this->view('siswa/jadwal/index', $data);
    }

    public function riwayat() {
        require_role(['siswa']);
        
        $user_id = $_SESSION['user_id'];
        $siswaModel = $this->model('SiswaModel');
        $siswa = $siswaModel->getSiswaByUserId($user_id);
        
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
        
        $totalData = $absensiModel->getRiwayatSiswaFiltered($siswa->id, $filters, 0, 0, true);
        $totalPages = ceil($totalData / $limit);
        
        $riwayat = $absensiModel->getRiwayatSiswaFiltered($siswa->id, $filters, $limit, $offset);
        $stats = $absensiModel->getStatsSiswa($siswa->id, $filters);
        
        $data = [
            'title'      => 'Riwayat Absensi Siswa',
            'siswa'      => $siswa,
            'riwayat'    => $riwayat,
            'stats'      => $stats,
            'filters'    => $filters,
            'page'       => $page,
            'totalPages' => $totalPages
        ];
        
        $this->view('siswa/riwayat', $data);
    }

    public function catatan_pembinaan() {
        require_role(['siswa']);
        $user_id = $_SESSION['user_id'];
        
        $siswaModel = $this->model('SiswaModel');
        $siswa = $siswaModel->getSiswaByUserId($user_id);
        
        $catatanModel = $this->model('CatatanModel');
        $catatan = [];
        if ($siswa) {
            $catatan = $catatanModel->getAllCatatan(['siswa_id' => $siswa->id, 'visibilitas' => 'Siswa']);
            if (!empty($catatan)) {
                $db = new Database();
                $db->query("UPDATE catatan_pembinaan SET status_baca = 1 WHERE siswa_id = :sid AND visibilitas = 'Siswa' AND (status_baca = 0 OR status_baca IS NULL)");
                $db->bind(':sid', $siswa->id);
                $db->execute();
            }
        }
        
        $data = [
            'title' => 'Catatan Pembinaan Saya',
            'catatan' => $catatan
        ];
        
        $this->view('siswa/catatan', $data);
    }

    public function mark_read($id) {
        require_role(['siswa']);
        $user_id = $_SESSION['user_id'];
        
        $siswaModel = $this->model('SiswaModel');
        $siswa = $siswaModel->getSiswaByUserId($user_id);
        
        if ($siswa) {
            $catatanModel = $this->model('CatatanModel');
            $catatan = $catatanModel->getCatatanById($id);
            if ($catatan && $catatan->siswa_id == $siswa->id && $catatan->visibilitas == 'Siswa') {
                $db = new Database();
                $db->query("UPDATE catatan_pembinaan SET status_baca = 1 WHERE id = :id");
                $db->bind(':id', $id);
                $db->execute();
                $this->flash('success', 'Catatan ditandai telah dibaca.');
            }
        }
        $this->redirect('siswa/catatan-pembinaan');
    }

    // ===================================================
    // PENGAJUAN IZIN / SAKIT
    // ===================================================

    public function pengajuan() {
        require_role(['siswa']);
        $user_id = $_SESSION['user_id'];

        $siswaModel    = $this->model('SiswaModel');
        $siswa         = $siswaModel->getSiswaByUserId($user_id);
        $pengajuanModel = $this->model('PengajuanModel');

        // Pagination
        $page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total      = $siswa ? $pengajuanModel->countPengajuanBySiswa($siswa->id) : 0;
        $totalPages = $total > 0 ? ceil($total / $limit) : 1;
        $list       = $siswa ? $pengajuanModel->getPengajuanBySiswa($siswa->id, $limit, $offset) : [];

        $data = [
            'title'      => 'Pengajuan Izin / Sakit',
            'siswa'      => $siswa,
            'list'       => $list,
            'page'       => $page,
            'totalPages' => $totalPages,
        ];
        $this->view('siswa/pengajuan/index', $data);
    }

    public function pengajuan_create() {
        require_role(['siswa']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('siswa/pengajuan');
        }

        $user_id        = $_SESSION['user_id'];
        $siswaModel     = $this->model('SiswaModel');
        $siswa          = $siswaModel->getSiswaByUserId($user_id);

        if (!$siswa) {
            $this->flash('error', 'Data siswa tidak ditemukan.');
            $this->redirect('siswa/pengajuan');
        }

        // Validasi input teks
        $jenis   = trim($_POST['jenis']   ?? '');
        $tanggal = trim($_POST['tanggal'] ?? '');
        $alasan  = trim($_POST['alasan']  ?? '');

        if (!in_array($jenis, ['Izin', 'Sakit'])) {
            $this->flash('error', 'Jenis pengajuan tidak valid.');
            $this->redirect('siswa/pengajuan');
        }
        if (empty($tanggal)) {
            $this->flash('error', 'Tanggal wajib diisi.');
            $this->redirect('siswa/pengajuan');
        }
        if (empty($alasan)) {
            $this->flash('error', 'Alasan wajib diisi.');
            $this->redirect('siswa/pengajuan');
        }

        // Cek double pengajuan (pengajuan hanya boleh 1x dalam 1 hari)
        $pengajuanModel = $this->model('PengajuanModel');
        if ($pengajuanModel->cekSudahPengajuanSiswa($siswa->id, $tanggal)) {
            $this->flash('error', 'Pengajuan gagal! Anda sudah melakukan pengajuan izin/sakit hari ini atau pada tanggal tersebut. Pengajuan hanya dapat dilakukan 1 kali dalam 1 hari.');
            $this->redirect('siswa/pengajuan');
        }

        // Cek apakah siswa sudah melakukan scan presensi hadir pada tanggal tersebut
        $absensiModel = $this->model('AbsensiModel');
        $cekAbsen = $absensiModel->cekAbsensiSiswaHariIni($siswa->id, $tanggal);
        if ($cekAbsen && (!empty($cekAbsen->jam_masuk) || strtolower($cekAbsen->status ?? '') === 'hadir')) {
            $jamInfo = !empty($cekAbsen->jam_masuk) ? ' pada pukul ' . substr($cekAbsen->jam_masuk, 0, 5) . ' WIB' : '';
            $this->flash('error', 'Pengajuan ditolak! Anda sudah tercatat melakukan absensi kehadiran' . $jamInfo . '. Tidak dapat mengajukan izin/sakit jika sudah melakukan scan absensi.');
            $this->redirect('siswa/pengajuan');
        }

        // Validasi dan upload foto (WAJIB)
        if (empty($_FILES['bukti_foto']['name'])) {
            $this->flash('error', 'Bukti foto wajib diunggah. Pengajuan gagal.');
            $this->redirect('siswa/pengajuan');
        }

        $uploadDir  = 'public/uploads/pengajuan/';
        $fileInfo   = $_FILES['bukti_foto'];
        $ext        = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

        if (!in_array($ext, $allowedExt)) {
            $this->flash('error', 'Format file tidak didukung. Gunakan JPG, PNG, WEBP, atau PDF.');
            $this->redirect('siswa/pengajuan');
        }
        if ($fileInfo['size'] > 5 * 1024 * 1024) {
            $this->flash('error', 'Ukuran file maksimal 5 MB.');
            $this->redirect('siswa/pengajuan');
        }
        if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Terjadi kesalahan saat mengunggah file.');
            $this->redirect('siswa/pengajuan');
        }

        $filename = 'pengajuan_' . $siswa->id . '_' . time() . '.' . $ext;
        if (!move_uploaded_file($fileInfo['tmp_name'], $uploadDir . $filename)) {
            $this->flash('error', 'Gagal menyimpan file. Pastikan folder upload memiliki izin tulis.');
            $this->redirect('siswa/pengajuan');
        }

        // Simpan ke database
        $pengajuanModel = $this->model('PengajuanModel');
        $ok = $pengajuanModel->createPengajuan([
            'siswa_id'   => $siswa->id,
            'jenis'      => $jenis,
            'tanggal'    => $tanggal,
            'alasan'     => $alasan,
            'bukti_foto' => $filename,
        ]);

        if ($ok) {
            // Langsung catat / update kehadiran sebagai izin atau sakit (tanpa menunggu persetujuan)
            $absensiModel = $this->model('AbsensiModel');
            $jenisStatus  = strtolower($jenis); // 'izin' atau 'sakit'
            $keterangan   = $alasan;

            $existing = $absensiModel->cekAbsensiSiswaHariIni($siswa->id, $tanggal);
            $db = new Database();
            if ($existing) {
                $db->query("UPDATE absensi_siswa SET status = :status, keterangan = :ket, jam_masuk = NULL, jam_pulang = NULL WHERE id = :id");
                $db->bind(':status', $jenisStatus);
                $db->bind(':ket',    $keterangan);
                $db->bind(':id',     $existing->id);
                $db->execute();
            } else {
                $db->query("INSERT INTO absensi_siswa (siswa_id, tanggal, jam_masuk, jam_pulang, status, keterangan) 
                            VALUES (:siswa_id, :tanggal, NULL, NULL, :status, :ket)");
                $db->bind(':siswa_id', $siswa->id);
                $db->bind(':tanggal',  $tanggal);
                $db->bind(':status',   $jenisStatus);
                $db->bind(':ket',      $keterangan);
                $db->execute();
            }

            $this->flash('success', "Pengajuan {$jenis} berhasil dikirim dan kehadiran telah otomatis tercatat.");
        } else {
            // Hapus file yang sudah terupload jika DB gagal
            @unlink($uploadDir . $filename);
            $this->flash('error', 'Gagal menyimpan pengajuan ke database.');
        }
        $this->redirect('siswa/pengajuan');
    }

    public function pengajuan_delete($id) {
        require_role(['siswa']);
        $this->flash('error', 'Pengajuan yang sudah dibuat tidak dapat dibatalkan.');
        $this->redirect('siswa/pengajuan');
    }
}
