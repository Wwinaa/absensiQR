<?php
/**
 * Admin Controller for Master Data
 */
class AdminController extends Controller {

    private $userModel;
    private $jurusanModel;
    private $tahunAjaranModel;
    private $mataPelajaranModel;
    private $jadwalModel;
    private $kelasModel;
    private $guruModel;
    private $siswaModel;
    private $waliKelasModel;
    private $settingModel;

    public function __construct() {
        require_role(['admin']);
        $this->userModel = $this->model('UserModel');
        $this->jurusanModel = $this->model('JurusanModel');
        $this->tahunAjaranModel = $this->model('TahunAjaranModel');
        $this->mataPelajaranModel = $this->model('MataPelajaranModel');
        $this->jadwalModel = $this->model('JadwalModel');
        $this->kelasModel = $this->model('KelasModel');
        $this->guruModel = $this->model('GuruModel');
        $this->siswaModel = $this->model('SiswaModel');
        $this->waliKelasModel = $this->model('WaliKelasModel');
        $this->settingModel = $this->model('SettingModel');
    }

    public function index() {
        redirect('dashboard');
    }

    // ---------------------------------------------------------
    // PENGATURAN
    // ---------------------------------------------------------
    public function pengaturan() {
        $tahunAjaranModel = $this->model('TahunAjaranModel');
        $active_tahun = $tahunAjaranModel->getAllActiveTahunAjaran();
        $aiConfig = $this->settingModel->getChatbotConfig();

        $data = [
            'title' => 'Pengaturan Admin',
            'active_tahun' => $active_tahun,
            'aiConfig' => $aiConfig
        ];
        
        $this->view('admin/pengaturan/index', $data);
    }

    // ---------------------------------------------------------
    // KONFIGURASI AI CHATBOT (API KEY & MODELING)
    // ---------------------------------------------------------
    public function pengaturan_ai() {
        $config = $this->settingModel->getChatbotConfig();

        // Model default populer untuk rekomendasi pilihan cepat (model aktif yang didukung)
        $popularModels = [
            'nvidia/nemotron-3.5-bolt-e4b' => 'NVIDIA Nemotron 3.5 Bolt (Default - Super Cepat, Ringan & Akurat)',
            'nvidia/nemotron-3.5-lightning-30b-a3b' => 'NVIDIA Nemotron 3.5 Lightning 30B (Penalaran Tinggi & Cepat)',
            'nvidia/nemotron-3-nano-omni-30b-a3b-reasoning' => 'NVIDIA Nemotron 3 Nano Omni 30B (Penalaran Cerdas)',
            'nvidia/nemotron-3-ultra-550b-a55b' => 'NVIDIA Nemotron 3 Ultra 550B (Model Unggulan Terbesar)',
            'google/diffusiongemma-26b-a4b-it' => 'Google Gemma 26B IT (Model Multilingual)'
        ];

        $data = [
            'title' => 'Konfigurasi AI Chatbot',
            'config' => $config,
            'popularModels' => $popularModels
        ];

        $this->view('admin/pengaturan_ai/index', $data);
    }

    public function simpan_pengaturan_ai() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/pengaturan_ai');
            return;
        }

        $apiKey = trim($_POST['api_key'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $customModel = trim($_POST['custom_model'] ?? '');

        // Jika user memilih custom model
        if ($model === 'custom' && !empty($customModel)) {
            $model = $customModel;
        }

        if (empty($apiKey)) {
            flash('danger', 'API Key AI tidak boleh kosong.');
            redirect('admin/pengaturan_ai');
            return;
        }

        if (empty($model)) {
            $model = 'nvidia/nemotron-3.5-bolt-e4b';
        }

        $this->settingModel->saveChatbotConfig($apiKey, $model);
        flash('success', 'Konfigurasi API AI Key dan Model Chatbot berhasil disimpan.');
        redirect('admin/pengaturan_ai');
    }

    public function fetch_ai_models() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
            exit;
        }

        $apiKey = trim($_POST['api_key'] ?? '');
        if (empty($apiKey)) {
            $config = $this->settingModel->getChatbotConfig();
            $apiKey = $config['api_key'];
        }

        if (empty($apiKey)) {
            echo json_encode(['success' => false, 'message' => 'API Key belum diisi. Masukkan API Key terlebih dahulu.']);
            exit;
        }

        $ch = curl_init('https://integrate.api.nvidia.com/v1/models');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo json_encode(['success' => false, 'message' => 'Gagal menghubungi server API: ' . $curlError]);
            exit;
        }

        $result = json_decode($response, true);
        if ($httpCode !== 200 || !isset($result['data']) || !is_array($result['data'])) {
            $errDetail = $result['message'] ?? $result['error']['message'] ?? "Kode respon HTTP: {$httpCode}";
            echo json_encode(['success' => false, 'message' => 'Gagal mengambil model dari API: ' . $errDetail]);
            exit;
        }

        $modelList = [];
        foreach ($result['data'] as $item) {
            if (isset($item['id'])) {
                $modelList[] = [
                    'id' => $item['id'],
                    'name' => $item['id']
                ];
            }
        }

        usort($modelList, fn($a, $b) => strcmp($a['id'], $b['id']));

        echo json_encode([
            'success' => true,
            'count' => count($modelList),
            'models' => $modelList
        ]);
        exit;
    }

    public function test_ai_connection() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
            exit;
        }

        $apiKey = trim($_POST['api_key'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $customModel = trim($_POST['custom_model'] ?? '');

        if ($model === 'custom' && !empty($customModel)) {
            $model = $customModel;
        }

        if (empty($apiKey)) {
            $config = $this->settingModel->getChatbotConfig();
            $apiKey = $config['api_key'];
        }
        if (empty($model)) {
            $config = $this->settingModel->getChatbotConfig();
            $model = $config['model'];
        }

        if (empty($apiKey)) {
            echo json_encode(['success' => false, 'message' => 'API Key wajib diisi untuk melakukan pengujian.']);
            exit;
        }

        $payload = json_encode([
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => 'Halo! Jawab singkat dalam 1 kalimat bahwa koneksi API AI berhasil dan sebutkan modelmu.']
            ],
            'max_tokens' => 100,
            'temperature' => 0.5
        ]);

        $ch = curl_init('https://integrate.api.nvidia.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo json_encode(['success' => false, 'message' => 'Koneksi gagal: ' . $curlError]);
            exit;
        }

        $result = json_decode($response, true);
        if ($httpCode >= 400 || !isset($result['choices'][0]['message']['content'])) {
            $errDetail = $result['message'] ?? $result['error']['message'] ?? "Kode error HTTP {$httpCode}";
            echo json_encode(['success' => false, 'message' => 'Uji koneksi gagal dari penyedia AI: ' . $errDetail]);
            exit;
        }

        $reply = trim($result['choices'][0]['message']['content']);
        echo json_encode([
            'success' => true,
            'message' => 'Koneksi ke API AI berhasil!',
            'model' => $model,
            'reply' => $reply
        ]);
        exit;
    }

    // ---------------------------------------------------------
    // JURUSAN
    // ---------------------------------------------------------
    public function jurusan($action = 'index', $id = null) {
        if ($action === 'index') {
            $search = isset($_GET['q']) ? $_GET['q'] : '';
            $data = [
                'title' => 'Data Jurusan',
                'jurusan' => $this->jurusanModel->getAllJurusan($search),
                'search' => $search
            ];
            $this->view('admin/jurusan/index', $data);
        } 
        elseif ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->jurusanModel->tambahJurusan($_POST)) {
                set_flash('Data jurusan berhasil ditambahkan.', 'success');
            } else {
                set_flash('Gagal menambahkan data jurusan.', 'danger');
            }
            redirect('admin/jurusan');
        }
        elseif ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->jurusanModel->updateJurusan($id, $_POST)) {
                set_flash('Data jurusan berhasil diubah.', 'success');
            } else {
                set_flash('Gagal mengubah data jurusan.', 'danger');
            }
            redirect('admin/jurusan');
        }
        elseif ($action === 'hapus' && $id) {
            if ($this->jurusanModel->hapusJurusan($id)) {
                set_flash('Data jurusan berhasil dihapus.', 'success');
            } else {
                set_flash('Gagal menghapus jurusan. Mungkin data ini masih digunakan di tabel kelas.', 'danger');
            }
            redirect('admin/jurusan');
        }
    }

    // ---------------------------------------------------------
    // TAHUN AJARAN
    // ---------------------------------------------------------
    public function tahun_ajaran($action = 'index', $id = null) {
        if ($action === 'index') {
            $data = [
                'title' => 'Data Tahun Ajaran',
                'tahun_ajaran' => $this->tahunAjaranModel->getAllTahunAjaran()
            ];
            $this->view('admin/tahun_ajaran/index', $data);
        }
        elseif ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->tahunAjaranModel->tambahTahunAjaran($_POST)) {
                set_flash('Tahun ajaran berhasil ditambahkan.', 'success');
            } else {
                set_flash('Gagal menambahkan tahun ajaran.', 'danger');
            }
            redirect('admin/tahun_ajaran');
        }
        elseif ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->tahunAjaranModel->updateTahunAjaran($id, $_POST)) {
                set_flash('Tahun ajaran berhasil diubah.', 'success');
            } else {
                set_flash('Gagal mengubah tahun ajaran.', 'danger');
            }
            redirect('admin/tahun_ajaran');
        }
        elseif ($action === 'hapus' && $id) {
            if ($this->tahunAjaranModel->hapusTahunAjaran($id)) {
                set_flash('Tahun ajaran berhasil dihapus.', 'success');
            } else {
                set_flash('Gagal menghapus tahun ajaran.', 'danger');
            }
            redirect('admin/tahun_ajaran');
        }
        elseif ($action === 'set_aktif' && $id) {
            if ($this->tahunAjaranModel->setAktif($id, 1)) {
                set_flash('Tahun ajaran berhasil diaktifkan.', 'success');
            } else {
                set_flash('Gagal mengaktifkan tahun ajaran.', 'danger');
            }
            redirect('admin/tahun_ajaran');
        }
        elseif ($action === 'set_nonaktif' && $id) {
            if ($this->tahunAjaranModel->setAktif($id, 0)) {
                set_flash('Tahun ajaran berhasil dinonaktifkan.', 'success');
            } else {
                set_flash('Gagal menonaktifkan tahun ajaran.', 'danger');
            }
            redirect('admin/tahun_ajaran');
        }
    }

    // ---------------------------------------------------------
    // MATA PELAJARAN
    // ---------------------------------------------------------
    public function mata_pelajaran($action = 'index', $id = null) {
        if ($action === 'index') {
            $data = [
                'title' => 'Data Mata Pelajaran',
                'mata_pelajaran' => $this->mataPelajaranModel->getAllMataPelajaran(),
                'guru' => $this->guruModel->getAllGuru(),
                'wali_kelas' => $this->waliKelasModel->getAllWaliKelas()
            ];
            $this->view('admin/mata_pelajaran/index', $data);
        }
        elseif ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->mataPelajaranModel->tambahMataPelajaran($_POST);
            if ($result === true) {
                set_flash('Mata pelajaran berhasil ditambahkan.', 'success');
            } elseif ($result === 'limit_exceeded') {
                set_flash('Gagal: Pengajar (Guru / Wali Kelas) sudah mengajar 2 mata pelajaran (maksimal).', 'danger');
            } else {
                set_flash('Gagal menambahkan mata pelajaran. Pastikan nama belum terdaftar.', 'danger');
            }
            redirect('admin/mata_pelajaran');
        }
        elseif ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->mataPelajaranModel->updateMataPelajaran($id, $_POST);
            if ($result === true) {
                set_flash('Mata pelajaran berhasil diubah.', 'success');
            } elseif ($result === 'limit_exceeded') {
                set_flash('Gagal: Pengajar (Guru / Wali Kelas) sudah mengajar 2 mata pelajaran (maksimal).', 'danger');
            } else {
                set_flash('Gagal mengubah mata pelajaran. Pastikan nama belum terdaftar.', 'danger');
            }
            redirect('admin/mata_pelajaran');
        }
        elseif ($action === 'hapus' && $id) {
            if ($this->mataPelajaranModel->hapusMataPelajaran($id)) {
                set_flash('Mata pelajaran berhasil dihapus.', 'success');
            } else {
                set_flash('Gagal menghapus mata pelajaran karena masih terhubung dengan jadwal.', 'danger');
            }
            redirect('admin/mata_pelajaran');
        }
    }

    // ---------------------------------------------------------
    // JADWAL PELAJARAN
    // ---------------------------------------------------------
    public function jadwal_pelajaran($action = 'index', $id = null) {
        if ($action === 'index') {
            $filters = [
                'hari' => $_GET['hari'] ?? '',
                'kelas_id' => $_GET['kelas_id'] ?? '',
                'pengajar' => $_GET['pengajar'] ?? '',
                'guru_id' => $_GET['guru_id'] ?? '',
                'mata_pelajaran_id' => $_GET['mata_pelajaran_id'] ?? '',
                'tahun_ajaran_id' => $_GET['tahun_ajaran_id'] ?? ''
            ];
            
            $data = [
                'title' => 'Data Jadwal Pelajaran',
                'jadwal' => $this->jadwalModel->getAllJadwal($filters),
                'guru' => $this->guruModel->getAllGuru(),
                'wali_kelas' => $this->waliKelasModel->getAllWaliKelas(),
                'kelas' => $this->kelasModel->getAllKelas(),
                'mata_pelajaran' => $this->mataPelajaranModel->getAllMataPelajaran(),
                'tahun_ajaran' => $this->tahunAjaranModel->getAllTahunAjaran(),
                'filters' => $filters
            ];
            $this->view('admin/jadwal/index', $data);
        }
        elseif ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->jadwalModel->tambahJadwal($_POST);
            if ($result === true) {
                set_flash('Jadwal berhasil ditambahkan.', 'success');
            } elseif (is_string($result)) {
                set_flash('Gagal menambahkan jadwal: ' . $result, 'danger');
            } else {
                set_flash('Gagal menambahkan jadwal.', 'danger');
            }
            redirect('admin/jadwal_pelajaran');
        }
        elseif ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->jadwalModel->updateJadwal($id, $_POST);
            if ($result === true) {
                set_flash('Jadwal berhasil diubah.', 'success');
            } elseif (is_string($result)) {
                set_flash('Gagal mengubah jadwal: ' . $result, 'danger');
            } else {
                set_flash('Gagal mengubah jadwal.', 'danger');
            }
            redirect('admin/jadwal_pelajaran');
        }
        elseif ($action === 'hapus' && $id) {
            if ($this->jadwalModel->hapusJadwal($id)) {
                set_flash('Jadwal berhasil dihapus.', 'success');
            } else {
                set_flash('Gagal menghapus jadwal.', 'danger');
            }
            redirect('admin/jadwal_pelajaran');
        }
    }

    // ---------------------------------------------------------
    // KELAS
    // ---------------------------------------------------------
    public function kelas($action = 'index', $id = null) {
        if ($action === 'index') {
            $search = isset($_GET['q']) ? $_GET['q'] : '';
            $data = [
                'title' => 'Data Kelas',
                'kelas' => $this->kelasModel->getAllKelas($search),
                'jurusan' => $this->jurusanModel->getAllJurusan(),
                'guru' => $this->waliKelasModel->getAllWaliKelas(),
                'search' => $search
            ];
            $this->view('admin/kelas/index', $data);
        }
        elseif ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->kelasModel->tambahKelas($_POST)) {
                set_flash('Data kelas berhasil ditambahkan.', 'success');
            } else {
                set_flash('Gagal menambahkan data kelas.', 'danger');
            }
            redirect('admin/kelas');
        }
        elseif ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->kelasModel->updateKelas($id, $_POST)) {
                set_flash('Data kelas berhasil diubah.', 'success');
            } else {
                set_flash('Gagal mengubah data kelas.', 'danger');
            }
            redirect('admin/kelas');
        }
        elseif ($action === 'hapus' && $id) {
            if ($this->kelasModel->hapusKelas($id)) {
                set_flash('Data kelas berhasil dihapus.', 'success');
            } else {
                set_flash('Gagal menghapus kelas. Mungkin data ini masih digunakan di tabel siswa.', 'danger');
            }
            redirect('admin/kelas');
        }
    }

    // ---------------------------------------------------------
    // GURU
    // ---------------------------------------------------------
    public function guru($action = 'index', $id = null) {
        if ($action === 'index') {
            $search = isset($_GET['q']) ? $_GET['q'] : '';
            $data = [
                'title' => 'Data Guru',
                'guru' => $this->guruModel->getAllGuru($search),
                'search' => $search
            ];
            $this->view('admin/guru/index', $data);
        }
        elseif ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            // Create user account first
            $userData = [
                'username' => $_POST['nip'],
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'role' => 'guru',
                'status' => 'active'
            ];
            $userId = $this->userModel->createUser($userData);

            if ($userId) {
                $_POST['user_id'] = $userId;
                $_POST['qr_identifier'] = 'G-' . uniqid();
                if ($this->guruModel->tambahGuru($_POST)) {
                    set_flash('Data guru dan akun berhasil ditambahkan.', 'success');
                } else {
                    set_flash('Gagal menambahkan data guru.', 'danger');
                }
            } else {
                set_flash('Gagal membuat akun untuk guru.', 'danger');
            }
            redirect('admin/guru');
        }
        elseif ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->guruModel->updateGuru($id, $_POST)) {
                set_flash('Data guru berhasil diubah.', 'success');
            } else {
                set_flash('Gagal mengubah data guru.', 'danger');
            }
            redirect('admin/guru');
        }
        elseif ($action === 'hapus' && $id) {
            // Hapus guru juga menghapus user berkat ON DELETE CASCADE (di setup PRD)
            $guru = $this->guruModel->getGuruById($id);
            if ($guru) {
                if ($this->userModel->deleteUser($guru->user_id)) {
                    set_flash('Data guru beserta akun berhasil dihapus.', 'success');
                } else {
                    set_flash('Gagal menghapus data guru.', 'danger');
                }
            }
            redirect('admin/guru');
        }
    }

    // ---------------------------------------------------------
    // SISWA
    // ---------------------------------------------------------
    public function siswa($action = 'index', $id = null) {
        if ($action === 'index') {
            $search = isset($_GET['q']) ? $_GET['q'] : '';
            $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
            $data = [
                'title' => 'Data Siswa',
                'siswa' => $this->siswaModel->getAllSiswa($search, $kelas_id),
                'kelas' => $this->kelasModel->getAllKelas(),
                'search' => $search,
                'kelas_id' => $kelas_id
            ];
            $this->view('admin/siswa/index', $data);
        }
        elseif ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            // Create user account first
            $userData = [
                'username' => $_POST['nis'], // Using NIS as username
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'role' => 'siswa',
                'status' => 'active'
            ];
            $userId = $this->userModel->createUser($userData);

            if ($userId) {
                $_POST['user_id'] = $userId;
                $_POST['qr_identifier'] = 'S-' . uniqid(); // Random for MVP
                if ($this->siswaModel->tambahSiswa($_POST)) {
                    set_flash('Data siswa dan akun berhasil ditambahkan.', 'success');
                } else {
                    set_flash('Gagal menambahkan data siswa.', 'danger');
                }
            } else {
                set_flash('Gagal membuat akun untuk siswa. NIS mungkin sudah digunakan.', 'danger');
            }
            redirect('admin/siswa');
        }
        elseif ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->siswaModel->updateSiswa($id, $_POST)) {
                set_flash('Data siswa berhasil diubah.', 'success');
            } else {
                set_flash('Gagal mengubah data siswa.', 'danger');
            }
            redirect('admin/siswa');
        }
        elseif ($action === 'hapus' && $id) {
            $siswa = $this->siswaModel->getSiswaById($id);
            if ($siswa) {
                if ($this->userModel->deleteUser($siswa->user_id)) {
                    set_flash('Data siswa beserta akun berhasil dihapus.', 'success');
                } else {
                    set_flash('Gagal menghapus data siswa.', 'danger');
                }
            }
            redirect('admin/siswa');
        }
    }

    // ---------------------------------------------------------
    // WALI KELAS
    // ---------------------------------------------------------
    public function wali_kelas($action = 'index', $id = null) {
        // Data Wali Kelas telah digabungkan ke dalam Data Guru
        set_flash('Data Wali Kelas telah digabungkan ke dalam Data Guru.', 'info');
        redirect('admin/guru');
    }

    // ---------------------------------------------------------
    // QR CODE / BARCODE PRINTING
    // ---------------------------------------------------------
    public function barcode_siswa($id = null) {
        $this->qrcode_siswa($id);
    }

    public function qrcode_siswa($id = null) {
        if (!$id) redirect('admin/siswa');
        
        $siswa = $this->siswaModel->getSiswaById($id);
        if (!$siswa) redirect('admin/siswa');

        $data = [
            'title' => 'QR Code Siswa - ' . $siswa->nama_lengkap,
            'siswa' => $siswa
        ];
        
        $this->view('admin/siswa/barcode', $data);
    }

    public function barcode_guru($id = null) {
        $this->qrcode_guru($id);
    }

    public function qrcode_guru($id = null) {
        if (!$id) redirect('admin/guru');
        
        $guru = $this->guruModel->getGuruById($id);
        if (!$guru) redirect('admin/guru');

        $data = [
            'title' => 'QR Code Guru - ' . ($guru->nama_lengkap ?? $guru->nama),
            'guru' => $guru
        ];
        
        $this->view('admin/guru/barcode', $data);
    }

    public function barcode_wali_kelas($id = null) {
        $this->qrcode_wali_kelas($id);
    }

    public function qrcode_wali_kelas($id = null) {
        if (!$id) redirect('admin/guru');
        
        $wali = $this->waliKelasModel->getWaliKelasById($id);
        if ($wali) {
            $guru = $this->guruModel->getGuruByUserId($wali->user_id);
            if ($guru) {
                redirect('admin/qrcode_guru/' . $guru->id);
            }
        }
        redirect('admin/guru');
    }

    // ---------------------------------------------------------
    // REKAP ABSENSI (ALL)
    // ---------------------------------------------------------
    public function rekap() {
        $absensiModel = $this->model('AbsensiModel');
        
        // Export logic
        if (isset($_GET['export'])) {
            if ($_GET['export'] == 'excel') {
                $this->export_excel();
                return;
            } elseif ($_GET['export'] == 'pdf') {
                $this->export_pdf();
                return;
            }
        }

        // Filter processing (Default dimulai dari 01/10/2026 sesuai instruksi)
        $filters = [
            'start_date' => !empty($_GET['start_date']) ? $_GET['start_date'] : '2026-10-01',
            'end_date'   => $_GET['end_date'] ?? '',
            'status'     => $_GET['status'] ?? '',
            'role'       => $_GET['role'] ?? '',
            'nama'       => $_GET['nama'] ?? '',
            'kelas_id'   => $_GET['kelas_id'] ?? ''
        ];
        
        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $totalData = $absensiModel->getRekapAdmin($filters, 0, 0, true);
        $totalPages = ceil($totalData / $limit);
        
        $riwayat = $absensiModel->getRekapAdmin($filters, $limit, $offset);
        $stats = $absensiModel->getStatsAdmin($filters);
        
        $data = [
            'title'        => 'Rekap & Riwayat Absensi',
            'riwayat'      => $riwayat,
            'stats'        => $stats,
            'filters'      => $filters,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalData,
            'kelas'        => $this->kelasModel->getAllKelas()
        ];
        
        $this->view('admin/rekap', $data);
    }

    private function get_export_filename($filters) {
        $roleStr = !empty($filters['role']) ? ucfirst($filters['role']) : "Keseluruhan";
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $dateStr = $filters['start_date'] . "_sampai_" . $filters['end_date'];
        } else if (!empty($filters['start_date'])) {
            $dateStr = "Mulai_" . $filters['start_date'];
        } else {
            $dateStr = date('Y-m-d');
        }
        return "Laporan_Absensi_{$roleStr}_{$dateStr}";
    }

    private function get_export_data() {
        $absensiModel = $this->model('AbsensiModel');
        $filters = [
            'start_date' => !empty($_GET['start_date']) ? $_GET['start_date'] : '2026-10-01',
            'end_date'   => $_GET['end_date'] ?? '',
            'status'     => $_GET['status'] ?? '',
            'role'       => $_GET['role'] ?? '',
            'nama'       => $_GET['nama'] ?? '',
            'kelas_id'   => $_GET['kelas_id'] ?? ''
        ];
        
        $data = $absensiModel->getRekapAdmin($filters, 0, 0, false);
        if (empty($data)) {
            set_flash('Tidak ada data absensi yang dapat diekspor berdasarkan filter yang dipilih.', 'warning');
            redirect('admin/rekap');
            return false;
        }

        $stats = $absensiModel->getStatsAdmin($filters);
        $filename = $this->get_export_filename($filters);
        
        $kelas_name = "Semua Kelas";
        if (!empty($filters['kelas_id'])) {
            $kls = $this->kelasModel->getKelasById($filters['kelas_id']);
            if ($kls) $kelas_name = $kls->nama_kelas;
        }

        return [
            'data' => $data,
            'stats' => $stats,
            'filters' => $filters,
            'filename' => $filename,
            'kelas_name' => $kelas_name
        ];
    }

    private function export_excel() {
        $exportData = $this->get_export_data();
        if (!$exportData) return;

        extract($exportData);
        
        // Include library
        require_once __DIR__ . '/../libraries/SimpleXLSXGen.php';

        $sheetData = [];
        
        $roleFilter = strtolower(trim($filters['role'] ?? ''));
        $isGuru = ($roleFilter === 'guru');
        $isSiswa = ($roleFilter === 'siswa');
        $isWaliKelas = ($roleFilter === 'wali_kelas');

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $tglIndo = function($dateStr, $withTime = false) use ($bulanIndo) {
            if (empty($dateStr)) return '-';
            $t = strtotime($dateStr);
            if (!$t) return $dateStr;
            $d = date('d', $t);
            $m = (int)date('m', $t);
            $y = date('Y', $t);
            $res = $d . ' ' . ($bulanIndo[$m] ?? date('F', $t)) . ' ' . $y;
            if ($withTime) {
                $res .= ' - ' . date('H:i:s', $t) . ' WIB';
            }
            return $res;
        };

        if ($isGuru) {
            $roleTitle = 'GURU';
            $kategoriPeranText = 'Guru';
        } elseif ($isSiswa) {
            $roleTitle = 'SISWA';
            $kategoriPeranText = 'Siswa';
        } elseif ($isWaliKelas) {
            $roleTitle = 'WALI KELAS';
            $kategoriPeranText = 'Wali Kelas';
        } else {
            $roleTitle = 'KEHADIRAN';
            $kategoriPeranText = 'Semua Peran (Siswa, Guru, Wali Kelas)';
        }

        $statusText = !empty($filters['status']) ? ucfirst($filters['status']) : 'Semua Status (Hadir, Izin, Sakit, Alpa)';
        $tglMulaiStr = !empty($filters['start_date']) ? $tglIndo($filters['start_date']) : '01 Oktober 2026';
        $tglAkhirStr = !empty($filters['end_date']) ? $tglIndo($filters['end_date']) : $tglIndo(date('Y-m-d'));
        $waktuRekapStr = $tglIndo(date('Y-m-d H:i:s'), true);

        // Title Rows
        $sheetData[] = ['<b>LAPORAN REKAPITULASI PRESENSI ' . $roleTitle . '</b>'];
        $sheetData[] = ['<b>SMK NU WARGABINANGUN</b>'];
        $sheetData[] = ['Data Periode Dimulai: ' . $tglMulaiStr . ' s/d ' . $tglAkhirStr];
        $sheetData[] = ['']; // Empty row
        
        // Filter Info Rows (Menyesuaikan dengan format sistem & kebutuhan peran)
        $sheetData[] = ['<b>INFORMASI FILTER:</b>'];
        $sheetData[] = ['Kategori Peran', ': ' . $kategoriPeranText];
        if ($isSiswa || (!$isGuru && !$isWaliKelas)) {
            $sheetData[] = ['Rombongan Belajar', ': ' . $kelas_name];
        }
        if (!empty($filters['nama'])) {
            $labelNama = $isGuru ? 'Nama Guru' : ($isSiswa ? 'Nama Siswa' : 'Penyaringan Nama');
            $sheetData[] = [$labelNama, ': ' . $filters['nama']];
        }
        $sheetData[] = ['Status Kehadiran', ': ' . $statusText];
        $sheetData[] = ['Waktu Merekap Absensi', ': ' . $waktuRekapStr];
        $sheetData[] = ['']; // Empty row
        
        // Summary Rows
        $sheetData[] = ['<b>REKAPITULASI STATISTIK:</b>'];
        $sheetData[] = ['Total Rekaman Absensi', ': ' . $stats['total'] . ' rekaman'];
        $sheetData[] = ['Total Hadir', ': ' . $stats['hadir']];
        $sheetData[] = ['Total Izin', ': ' . $stats['izin']];
        $sheetData[] = ['Total Sakit', ': ' . $stats['sakit']];
        $sheetData[] = ['Total Alpa', ': ' . $stats['alpa']];
        $sheetData[] = ['']; // Empty row

        // Helper function to prevent formula injection
        $sanitize = function($val) {
            if ($val === null || $val === '') return '';
            $val = (string)$val;
            if (in_array(substr($val, 0, 1), ['=', '+', '-', '@'])) {
                return "'" . $val;
            }
            return $val;
        };

        // Table Headers & Data based on Role
        if ($isGuru) {
            $sheetData[] = ['<b>No</b>', '<b>Nama Guru</b>', '<b>NIP</b>', '<b>Mata Pelajaran</b>', '<b>Tanggal</b>', '<b>Jam Masuk</b>', '<b>Jam Pulang</b>', '<b>Status</b>', '<b>Keterangan</b>'];
            $no = 1;
            foreach ($data as $r) {
                $mapelText = !empty($r->mapel) && $r->mapel !== '-' ? $r->mapel : 'Tenaga Pendidik';
                $sheetData[] = [
                    $no++,
                    $sanitize($r->nama),
                    $sanitize(!empty($r->id_nomor) && $r->id_nomor !== '-' ? $r->id_nomor : '-'),
                    $sanitize($mapelText),
                    $sanitize(date('d/m/Y', strtotime($r->tanggal))),
                    $sanitize(!empty($r->jam_masuk) ? substr($r->jam_masuk, 0, 5) . ' WIB' : '-'),
                    $sanitize(!empty($r->jam_pulang) ? substr($r->jam_pulang, 0, 5) . ' WIB' : '-'),
                    $sanitize(in_array(strtolower($r->status), ['hadir', 'terlambat']) ? 'HADIR' : strtoupper($r->status)),
                    $sanitize(!empty($r->keterangan) && $r->keterangan !== '-' ? $r->keterangan : '-')
                ];
            }
        } elseif ($isSiswa) {
            $sheetData[] = ['<b>No</b>', '<b>Nama Siswa</b>', '<b>NIS</b>', '<b>NISN</b>', '<b>Kelas / Jurusan</b>', '<b>Tanggal</b>', '<b>Jam Masuk</b>', '<b>Jam Pulang</b>', '<b>Status</b>', '<b>Keterangan</b>'];
            $no = 1;
            foreach ($data as $r) {
                $klsJur = !empty($r->kelas) && $r->kelas !== '-' ? $r->kelas . ' / ' . $r->jurusan : '-';
                $nisn = !empty($r->nisn) && $r->nisn !== '-' ? $r->nisn : '-';
                $sheetData[] = [
                    $no++,
                    $sanitize($r->nama),
                    $sanitize(!empty($r->id_nomor) && $r->id_nomor !== '-' ? $r->id_nomor : '-'),
                    $sanitize($nisn),
                    $sanitize($klsJur),
                    $sanitize(date('d/m/Y', strtotime($r->tanggal))),
                    $sanitize(!empty($r->jam_masuk) ? substr($r->jam_masuk, 0, 5) . ' WIB' : '-'),
                    $sanitize(!empty($r->jam_pulang) ? substr($r->jam_pulang, 0, 5) . ' WIB' : '-'),
                    $sanitize(in_array(strtolower($r->status), ['hadir', 'terlambat']) ? 'HADIR' : strtoupper($r->status)),
                    $sanitize(!empty($r->keterangan) && $r->keterangan !== '-' ? $r->keterangan : '-')
                ];
            }
        } else {
            $sheetData[] = ['<b>No</b>', '<b>Nama Lengkap</b>', '<b>Peran</b>', '<b>NIS / NIP</b>', '<b>NISN</b>', '<b>Kelas / Jurusan / Mapel</b>', '<b>Tanggal</b>', '<b>Jam Masuk</b>', '<b>Jam Pulang</b>', '<b>Status</b>', '<b>Keterangan</b>'];
            $no = 1;
            foreach ($data as $r) {
                $roleText = $r->role;
                if ($r->role === 'Guru' && !empty($r->wali_kelas_nama)) {
                    $roleText = 'Guru / Wali Kelas';
                }
                if ($r->role === 'Siswa') {
                    $klsJur = $r->kelas . ' / ' . $r->jurusan;
                } elseif ($r->role === 'Guru') {
                    $klsJur = !empty($r->mapel) && $r->mapel !== '-' ? $r->mapel : 'Tenaga Pendidik';
                } else {
                    $klsJur = !empty($r->kelas) && $r->kelas !== '-' ? 'Wali Kelas ' . $r->kelas : '-';
                }
                $nisn = !empty($r->nisn) && $r->nisn !== '-' ? $r->nisn : '-';
                $sheetData[] = [
                    $no++,
                    $sanitize($r->nama),
                    $sanitize($roleText),
                    $sanitize(!empty($r->id_nomor) && $r->id_nomor !== '-' ? $r->id_nomor : '-'),
                    $sanitize($nisn),
                    $sanitize($klsJur),
                    $sanitize(date('d/m/Y', strtotime($r->tanggal))),
                    $sanitize(!empty($r->jam_masuk) ? substr($r->jam_masuk, 0, 5) . ' WIB' : '-'),
                    $sanitize(!empty($r->jam_pulang) ? substr($r->jam_pulang, 0, 5) . ' WIB' : '-'),
                    $sanitize(in_array(strtolower($r->status), ['hadir', 'terlambat']) ? 'HADIR' : strtoupper($r->status)),
                    $sanitize(!empty($r->keterangan) && $r->keterangan !== '-' ? $r->keterangan : '-')
                ];
            }
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($sheetData, 'Rekap Absensi');
        // We output it to browser
        $xlsx->downloadAs($filename . '.xlsx');
        exit;
    }

    private function export_pdf() {
        $exportData = $this->get_export_data();
        if (!$exportData) return;

        $exportData['title'] = 'Cetak Laporan PDF';
        $this->viewStandalone('admin/export_pdf', $exportData);
    }

    // ---------------------------------------------------------
    // PENGAJUAN IZIN / SAKIT (ADMIN)
    // ---------------------------------------------------------
    public function pengajuan_izin($action = 'index', $id = null) {
        $pengajuanModel = $this->model('PengajuanModel');

        if ($action === 'index') {
            $filters = [
                'status'       => $_GET['status']       ?? '',
                'jenis'        => $_GET['jenis']        ?? '',
                'tipe_pemohon' => $_GET['tipe_pemohon'] ?? '',
                'kelas_id'     => $_GET['kelas_id']     ?? '',
                'nama'         => $_GET['nama']         ?? '',
            ];
            $page   = max(1, (int)($_GET['page'] ?? 1));
            $limit  = 15;
            $offset = ($page - 1) * $limit;

            $total      = $pengajuanModel->getAllPengajuan($filters, $limit, $offset, true);
            $totalPages = $total > 0 ? ceil($total / $limit) : 1;
            $list       = $pengajuanModel->getAllPengajuan($filters, $limit, $offset);

            $data = [
                'title'      => 'Kelola Pengajuan Izin / Sakit',
                'list'       => $list,
                'filters'    => $filters,
                'page'       => $page,
                'totalPages' => $totalPages,
                'kelasList'  => $this->kelasModel->getAllKelas(),
            ];
            $this->view('admin/pengajuan_izin/index', $data);

        } elseif ($action === 'proses' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $status  = trim($_POST['status']  ?? '');
            $catatan = trim($_POST['catatan'] ?? '');

            if (!in_array($status, ['Disetujui', 'Ditolak'])) {
                $this->flash('error', 'Status tidak valid.');
                $this->redirect('admin/pengajuan_izin');
                return;
            }

            $pengajuan = $pengajuanModel->getPengajuanById($id);
            if (!$pengajuan) {
                $this->flash('error', 'Pengajuan tidak ditemukan.');
                $this->redirect('admin/pengajuan_izin');
                return;
            }

            $pengajuanModel->updateStatus($id, $status, $catatan);

            // Jika disetujui, update / insert absensi (siswa atau guru)
            if ($status === 'Disetujui') {
                $absensiModel = $this->model('AbsensiModel');
                $jenisStatus = strtolower($pengajuan->jenis); // 'izin' atau 'sakit'
                $keterangan  = 'Pengajuan ' . $pengajuan->jenis . ' disetujui oleh admin';

                if ($pengajuan->tipe_pemohon === 'guru' && !empty($pengajuan->guru_id)) {
                    $existing = $absensiModel->cekAbsensiGuruHariIni($pengajuan->guru_id, $pengajuan->tanggal);
                    if ($existing) {
                        $db = new Database();
                        $db->query("UPDATE absensi_guru SET status = :status, keterangan = :ket, jam_masuk = NULL, jam_pulang = NULL WHERE id = :id");
                        $db->bind(':status', $jenisStatus);
                        $db->bind(':ket',    $keterangan);
                        $db->bind(':id',     $existing->id);
                        $db->execute();
                    } else {
                        $absensiModel->catatMasukGuru(
                            $pengajuan->guru_id,
                            $pengajuan->tanggal,
                            NULL,
                            $jenisStatus,
                            $keterangan
                        );
                    }
                } else if (!empty($pengajuan->siswa_id)) {
                    $existing = $absensiModel->cekAbsensiSiswaHariIni($pengajuan->siswa_id, $pengajuan->tanggal);
                    if ($existing) {
                        $db = new Database();
                        $db->query("UPDATE absensi_siswa SET status = :status, keterangan = :ket, jam_masuk = NULL, jam_pulang = NULL WHERE id = :id");
                        $db->bind(':status', $jenisStatus);
                        $db->bind(':ket',    $keterangan);
                        $db->bind(':id',     $existing->id);
                        $db->execute();
                    } else {
                        $absensiModel->catatMasukSiswa(
                            $pengajuan->siswa_id,
                            $pengajuan->tanggal,
                            NULL,
                            $jenisStatus,
                            $keterangan
                        );
                    }
                }
            }

            $this->flash('success', "Pengajuan berhasil di-{$status}.");
            $this->redirect('admin/pengajuan_izin');

        } else {
            $this->redirect('admin/pengajuan_izin');
        }
    }

}

