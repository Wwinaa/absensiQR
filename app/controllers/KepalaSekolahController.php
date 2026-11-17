<?php
/**
 * Controller Kepala Sekolah
 * Mengelola laporan kehadiran, pemantauan absensi siswa & guru,
 * serta modul catatan pembinaan / supervisi guru.
 */
class KepalaSekolahController extends Controller {

    public function index() {
        redirect('dashboard/kepala_sekolah');
    }

    public function laporan() {
        require_role(['kepala_sekolah']);
        
        $tanggal = isset($_GET['tanggal']) && !empty($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        $jenis = isset($_GET['jenis']) && in_array($_GET['jenis'], ['siswa', 'guru']) ? $_GET['jenis'] : 'siswa';
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
        $kelasFilter = isset($_GET['kelas_nama']) ? $_GET['kelas_nama'] : '';
        
        $absensiModel = $this->model('AbsensiModel');
        $kelasModel = $this->model('KelasModel');
        $allKelas = $kelasModel ? $kelasModel->getAllKelas() : [];

        // Ambil data absensi
        if ($jenis == 'siswa') {
            $absensi = $absensiModel->getAllAbsensiSiswaTanggal($tanggal);
            
            // Filter status jika dipilih
            if (!empty($statusFilter)) {
                $absensi = array_values(array_filter($absensi, function($a) use ($statusFilter) {
                    return strtolower($a->status ?? '') === strtolower($statusFilter);
                }));
            }
            // Filter kelas jika dipilih
            if (!empty($kelasFilter)) {
                $absensi = array_values(array_filter($absensi, function($a) use ($kelasFilter) {
                    return ($a->nama_kelas ?? '') === $kelasFilter;
                }));
            }
        } else {
            $absensi = $absensiModel->getAllAbsensiGuruTanggal($tanggal);
            
            if (!empty($statusFilter)) {
                $absensi = array_values(array_filter($absensi, function($a) use ($statusFilter) {
                    return strtolower($a->status ?? '') === strtolower($statusFilter);
                }));
            }
        }

        // Hitung rekapitulasi data
        $rekapCount = [
            'total' => count($absensi),
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0
        ];
        foreach ($absensi as $a) {
            $st = strtolower($a->status ?? '');
            if ($st === 'hadir') $rekapCount['hadir']++;
            elseif ($st === 'izin') $rekapCount['izin']++;
            elseif ($st === 'sakit') $rekapCount['sakit']++;
            elseif ($st === 'alpa' || $st === 'bolos') $rekapCount['alpa']++;
        }

        $data = [
            'title' => 'Laporan Kehadiran',
            'absensi' => $absensi,
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'status_filter' => $statusFilter,
            'kelas_filter' => $kelasFilter,
            'all_kelas' => $allKelas,
            'rekap_count' => $rekapCount
        ];
        
        $this->view('kepsek/laporan', $data);
    }

    public function cetak_laporan() {
        require_role(['kepala_sekolah']);
        
        $tanggal = isset($_GET['tanggal']) && !empty($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        $jenis = isset($_GET['jenis']) && in_array($_GET['jenis'], ['siswa', 'guru']) ? $_GET['jenis'] : 'siswa';
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
        $kelasFilter = isset($_GET['kelas_nama']) ? $_GET['kelas_nama'] : '';
        
        $absensiModel = $this->model('AbsensiModel');

        // Ambil data absensi
        if ($jenis == 'siswa') {
            $absensi = $absensiModel->getAllAbsensiSiswaTanggal($tanggal);
            
            if (!empty($statusFilter)) {
                $absensi = array_values(array_filter($absensi, function($a) use ($statusFilter) {
                    return strtolower($a->status ?? '') === strtolower($statusFilter);
                }));
            }
            if (!empty($kelasFilter)) {
                $absensi = array_values(array_filter($absensi, function($a) use ($kelasFilter) {
                    return ($a->nama_kelas ?? '') === $kelasFilter;
                }));
            }
        } else {
            $absensi = $absensiModel->getAllAbsensiGuruTanggal($tanggal);
            
            if (!empty($statusFilter)) {
                $absensi = array_values(array_filter($absensi, function($a) use ($statusFilter) {
                    return strtolower($a->status ?? '') === strtolower($statusFilter);
                }));
            }
        }

        // Hitung statistik
        $rekapCount = [
            'total' => count($absensi),
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0
        ];
        foreach ($absensi as $a) {
            $st = strtolower($a->status ?? '');
            if (in_array($st, ['hadir', 'terlambat'])) $rekapCount['hadir']++;
            elseif ($st === 'izin') $rekapCount['izin']++;
            elseif ($st === 'sakit') $rekapCount['sakit']++;
            elseif ($st === 'alpa' || $st === 'bolos') $rekapCount['alpa']++;
        }

        $filename = 'Laporan_Presensi_' . ucfirst($jenis) . '_' . date('Ymd', strtotime($tanggal));

        $data = [
            'filename' => $filename,
            'absensi' => $absensi,
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'status_filter' => $statusFilter,
            'kelas_filter' => $kelasFilter,
            'rekap_count' => $rekapCount
        ];
        
        $this->viewStandalone('kepsek/cetak_laporan', $data);
    }

    public function export_pdf() {
        $this->cetak_laporan();
    }

    public function catatan() {
        require_role(['kepala_sekolah']);
        
        $catatanModel = $this->model('CatatanGuruModel');
        $guruModel = $this->model('GuruModel');

        $filters = [
            'guru_id' => $_GET['guru_id'] ?? '',
            'kategori' => $_GET['kategori'] ?? '',
            'urgensi' => $_GET['urgensi'] ?? '',
            'status_baca' => $_GET['status_baca'] ?? '',
            'tanggal' => $_GET['tanggal'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $catatanList = $catatanModel->getAllCatatan($filters);
        $allGuru = $guruModel->getAllGuru();
        $stats = $catatanModel->getStats();

        $data = [
            'title' => 'Catatan untuk Guru',
            'catatan' => $catatanList,
            'all_guru' => $allGuru,
            'stats' => $stats,
            'filters' => $filters
        ];
        
        $this->view('kepsek/catatan', $data);
    }

    public function tambah_catatan() {
        require_role(['kepala_sekolah']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $guru_id = $_POST['guru_id'] ?? '';
            $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');
            $kategori = $_POST['kategori'] ?? 'Kedisiplinan';
            $urgensi = $_POST['urgensi'] ?? 'Biasa';
            $judul = trim($_POST['judul'] ?? '');
            $isi_catatan = trim($_POST['isi_catatan'] ?? '');

            if (empty($guru_id) || empty($isi_catatan)) {
                set_flash('error', 'Silakan pilih guru dan isi catatan pembinaan.');
                redirect('kepala-sekolah/catatan');
                return;
            }

            $catatanModel = $this->model('CatatanGuruModel');
            $success = $catatanModel->tambahCatatan([
                'guru_id' => $guru_id,
                'kepala_sekolah_id' => $_SESSION['user_id'],
                'tanggal' => $tanggal,
                'judul' => $judul,
                'kategori' => $kategori,
                'urgensi' => $urgensi,
                'isi_catatan' => $isi_catatan,
                'status_baca' => 'belum'
            ]);

            if ($success) {
                set_flash('success', 'Catatan untuk guru berhasil disimpan!');
            } else {
                set_flash('error', 'Gagal menyimpan catatan guru.');
            }
        }
        redirect('kepala-sekolah/catatan');
    }

    public function edit_catatan() {
        require_role(['kepala_sekolah']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $guru_id = $_POST['guru_id'] ?? '';
            $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');
            $kategori = $_POST['kategori'] ?? 'Kedisiplinan';
            $urgensi = $_POST['urgensi'] ?? 'Biasa';
            $judul = trim($_POST['judul'] ?? '');
            $isi_catatan = trim($_POST['isi_catatan'] ?? '');

            if (empty($id) || empty($guru_id) || empty($isi_catatan)) {
                set_flash('error', 'Data catatan tidak lengkap.');
                redirect('kepala-sekolah/catatan');
                return;
            }

            $catatanModel = $this->model('CatatanGuruModel');
            $success = $catatanModel->updateCatatan($id, [
                'guru_id' => $guru_id,
                'tanggal' => $tanggal,
                'judul' => $judul,
                'kategori' => $kategori,
                'urgensi' => $urgensi,
                'isi_catatan' => $isi_catatan
            ]);

            if ($success) {
                set_flash('success', 'Catatan guru berhasil diperbarui!');
            } else {
                set_flash('error', 'Gagal memperbarui catatan guru.');
            }
        }
        redirect('kepala-sekolah/catatan');
    }

    public function hapus_catatan($id = null) {
        require_role(['kepala_sekolah']);
        if (!empty($id)) {
            $catatanModel = $this->model('CatatanGuruModel');
            $success = $catatanModel->hapusCatatan($id);
            if ($success) {
                set_flash('success', 'Catatan guru berhasil dihapus.');
            } else {
                set_flash('error', 'Gagal menghapus catatan guru.');
            }
        }
        redirect('kepala-sekolah/catatan');
    }
}
