<?php
/**
 * Profile Controller
 * Handles user profile page
 */
class ProfileController extends Controller {

    public function index() {
        require_login();
        
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        $userModel = $this->model('UserModel');
        $user = $userModel->getUserById($userId);
        
        if (!$user) {
            redirect('auth/logout');
        }

        $profileData = [
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'nama' => $user->nama_lengkap ?? '',
            'jenis_kelamin' => $user->jenis_kelamin ?? '',
            'tempat_lahir' => $user->tempat_lahir ?? '',
            'tanggal_lahir' => $user->tanggal_lahir ?? '',
            'alamat' => $user->alamat ?? '',
            'no_telp' => $user->no_telp ?? '',
            'email' => $user->email ?? '',
            'detail_role' => ''
        ];

        if ($role === 'guru') {
            $guruModel = $this->model('GuruModel');
            $guru = $guruModel->getGuruByUserId($userId);
            if ($guru) {
                if (empty($profileData['nama'])) $profileData['nama'] = $guru->nama_lengkap;
                if (empty($profileData['jenis_kelamin'])) $profileData['jenis_kelamin'] = $guru->jenis_kelamin;
                if (empty($profileData['tempat_lahir'])) $profileData['tempat_lahir'] = $guru->tempat_lahir;
                if (empty($profileData['tanggal_lahir'])) $profileData['tanggal_lahir'] = $guru->tanggal_lahir;
                if (empty($profileData['alamat'])) $profileData['alamat'] = $guru->alamat;
                if (empty($profileData['no_telp'])) $profileData['no_telp'] = $guru->no_telp;
                if (empty($profileData['email'])) $profileData['email'] = $guru->email;
                $profileData['detail_role'] = "NIP: " . htmlspecialchars($guru->nip);
            }
        } elseif ($role === 'wali_kelas') {
            $wkModel = $this->model('WaliKelasModel');
            $wk = $wkModel->getWaliKelasByUserId($userId);
            if ($wk) {
                if (empty($profileData['nama'])) $profileData['nama'] = $wk->nama_lengkap;
                if (empty($profileData['jenis_kelamin'])) $profileData['jenis_kelamin'] = $wk->jenis_kelamin;
                if (empty($profileData['tempat_lahir'])) $profileData['tempat_lahir'] = $wk->tempat_lahir;
                if (empty($profileData['tanggal_lahir'])) $profileData['tanggal_lahir'] = $wk->tanggal_lahir;
                if (empty($profileData['alamat'])) $profileData['alamat'] = $wk->alamat;
                if (empty($profileData['no_telp'])) $profileData['no_telp'] = $wk->no_telp;
                if (empty($profileData['email'])) $profileData['email'] = $wk->email;
                $profileData['detail_role'] = "NIP: " . htmlspecialchars($wk->nip);
            }
        } elseif ($role === 'siswa') {
            $siswaModel = $this->model('SiswaModel');
            $siswa = $siswaModel->getSiswaByUserId($userId);
            if ($siswa) {
                if (empty($profileData['nama'])) $profileData['nama'] = $siswa->nama_lengkap;
                if (empty($profileData['jenis_kelamin'])) $profileData['jenis_kelamin'] = $siswa->jenis_kelamin;
                if (empty($profileData['tempat_lahir'])) $profileData['tempat_lahir'] = $siswa->tempat_lahir;
                if (empty($profileData['tanggal_lahir'])) $profileData['tanggal_lahir'] = $siswa->tanggal_lahir;
                if (empty($profileData['alamat'])) $profileData['alamat'] = $siswa->alamat;
                if (empty($profileData['no_telp'])) $profileData['no_telp'] = $siswa->no_telp;
                if (empty($profileData['email'])) $profileData['email'] = $siswa->email;
                
                $siswaFull = $siswaModel->getSiswaById($siswa->id);
                $profileData['detail_role'] = "NIS/NISN: " . htmlspecialchars($siswa->nis) . " / " . htmlspecialchars($siswa->nisn);
                if ($siswaFull && !empty($siswaFull->nama_kelas)) {
                    $profileData['detail_role'] .= "<br>Kelas: " . htmlspecialchars($siswaFull->nama_kelas) . " - " . htmlspecialchars($siswaFull->nama_jurusan);
                }
            }
        } elseif ($role === 'kepala_sekolah') {
            if (empty($profileData['nama'])) $profileData['nama'] = "Drs. H. Ahmad Fauzi, M.Pd";
            $profileData['detail_role'] = "Jabatan: Kepala Sekolah SMK NU WARGABINANGUN";
        } elseif ($role === 'admin') {
            if (empty($profileData['nama'])) $profileData['nama'] = "Administrator Sistem";
            $profileData['detail_role'] = "Hak Akses: Administrator Utama";
        }

        $data = [
            'title' => 'Profil Saya',
            'profile' => $profileData
        ];
        
        $this->view('profile/index', $data);
    }
    
    public function update() {
        require_login();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'];
            $role = $_SESSION['user_role'];
            
            $username = trim($_POST['username'] ?? '');
            $nama = trim($_POST['nama'] ?? '');
            $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
            $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
            $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
            $alamat = trim($_POST['alamat'] ?? '');
            $no_telp = trim($_POST['no_telp'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            $password_lama = trim($_POST['password_lama'] ?? '');
            $password_baru = trim($_POST['password_baru'] ?? '');
            $konfirmasi_password = trim($_POST['konfirmasi_password'] ?? '');
            
            $userModel = $this->model('UserModel');
            $user = $userModel->getUserById($userId);
            
            if (empty($username)) {
                set_flash('error', 'Username tidak boleh kosong.');
                redirect('profile');
                return;
            }

            if (empty($nama)) {
                set_flash('error', 'Nama lengkap tidak boleh kosong.');
                redirect('profile');
                return;
            }
            
            // Cek apakah username sudah dipakai orang lain
            $existing = $userModel->getUserByUsername($username);
            if ($existing && $existing->id != $userId) {
                set_flash('error', 'Username sudah digunakan oleh pengguna lain.');
                redirect('profile');
                return;
            }
            
            // Validasi password jika diisi
            if (!empty($password_baru)) {
                if (empty($password_lama) || !password_verify($password_lama, $user->password)) {
                    set_flash('error', 'Password lama tidak sesuai.');
                    redirect('profile');
                    return;
                }
                
                if ($password_baru !== $konfirmasi_password) {
                    set_flash('error', 'Konfirmasi password baru tidak cocok.');
                    redirect('profile');
                    return;
                }

                if (strlen($password_baru) < 6) {
                    set_flash('error', 'Password baru minimal harus 6 karakter.');
                    redirect('profile');
                    return;
                }
                
                $hashedPassword = password_hash($password_baru, PASSWORD_DEFAULT);
                $userModel->updateUserPassword($userId, $hashedPassword);
            }
            
            if ($role === 'admin') {
                // Jangan timpa biodata lama admin jika form admin hanya mengelola nama, kontak & akun
                $jenis_kelamin = $user->jenis_kelamin ?? null;
                $tempat_lahir = $user->tempat_lahir ?? '';
                $tglVal = $user->tanggal_lahir ?? null;
                $alamat = $user->alamat ?? '';
            } else {
                // Sanitasi format jenis kelamin & tanggal untuk role selain admin
                if ($jenis_kelamin !== 'L' && $jenis_kelamin !== 'P') {
                    $jenis_kelamin = null;
                }
                $tglVal = (!empty($tanggal_lahir) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_lahir)) ? $tanggal_lahir : null;
            }

            // Update tabel users (berlaku untuk SEMUA role)
            $db = new Database();
            $db->query("UPDATE users SET 
                username = :username, 
                nama_lengkap = :nama, 
                jenis_kelamin = :jk, 
                tempat_lahir = :tempat, 
                tanggal_lahir = :tgl, 
                alamat = :alamat, 
                no_telp = :no_telp, 
                email = :email 
                WHERE id = :id");
            $db->bind(':username', $username);
            $db->bind(':nama', $nama);
            $db->bind(':jk', $jenis_kelamin);
            $db->bind(':tempat', $tempat_lahir ?: null);
            $db->bind(':tgl', $tglVal);
            $db->bind(':alamat', $alamat ?: null);
            $db->bind(':no_telp', $no_telp ?: null);
            $db->bind(':email', $email ?: null);
            $db->bind(':id', $userId);
            $db->execute();
            
            $_SESSION['user_username'] = $username;
            
            // Update tabel spesifik peran
            if ($role === 'guru') {
                $db->query("UPDATE guru SET 
                    nama_lengkap = :nama, 
                    jenis_kelamin = :jk, 
                    tempat_lahir = :tempat, 
                    tanggal_lahir = :tgl, 
                    alamat = :alamat, 
                    no_telp = :no_telp, 
                    email = :email 
                    WHERE user_id = :id");
                $db->bind(':nama', $nama);
                $db->bind(':jk', $jenis_kelamin);
                $db->bind(':tempat', $tempat_lahir ?: null);
                $db->bind(':tgl', $tglVal);
                $db->bind(':alamat', $alamat ?: null);
                $db->bind(':no_telp', $no_telp ?: null);
                $db->bind(':email', $email ?: null);
                $db->bind(':id', $userId);
                $db->execute();

                // Sinkronkan ke wali_kelas_data jika terdaftar
                $db->query("UPDATE wali_kelas_data SET 
                    nama_lengkap = :nama, 
                    jenis_kelamin = :jk, 
                    tempat_lahir = :tempat, 
                    tanggal_lahir = :tgl, 
                    alamat = :alamat, 
                    no_telp = :no_telp, 
                    email = :email 
                    WHERE user_id = :id");
                $db->bind(':nama', $nama);
                $db->bind(':jk', $jenis_kelamin);
                $db->bind(':tempat', $tempat_lahir ?: null);
                $db->bind(':tgl', $tglVal);
                $db->bind(':alamat', $alamat ?: null);
                $db->bind(':no_telp', $no_telp ?: null);
                $db->bind(':email', $email ?: null);
                $db->bind(':id', $userId);
                $db->execute();
            } elseif ($role === 'wali_kelas') {
                $db->query("UPDATE wali_kelas_data SET 
                    nama_lengkap = :nama, 
                    jenis_kelamin = :jk, 
                    tempat_lahir = :tempat, 
                    tanggal_lahir = :tgl, 
                    alamat = :alamat, 
                    no_telp = :no_telp, 
                    email = :email 
                    WHERE user_id = :id");
                $db->bind(':nama', $nama);
                $db->bind(':jk', $jenis_kelamin);
                $db->bind(':tempat', $tempat_lahir ?: null);
                $db->bind(':tgl', $tglVal);
                $db->bind(':alamat', $alamat ?: null);
                $db->bind(':no_telp', $no_telp ?: null);
                $db->bind(':email', $email ?: null);
                $db->bind(':id', $userId);
                $db->execute();

                // Sinkronkan ke guru jika ada
                $db->query("UPDATE guru SET 
                    nama_lengkap = :nama, 
                    jenis_kelamin = :jk, 
                    tempat_lahir = :tempat, 
                    tanggal_lahir = :tgl, 
                    alamat = :alamat, 
                    no_telp = :no_telp, 
                    email = :email 
                    WHERE user_id = :id");
                $db->bind(':nama', $nama);
                $db->bind(':jk', $jenis_kelamin);
                $db->bind(':tempat', $tempat_lahir ?: null);
                $db->bind(':tgl', $tglVal);
                $db->bind(':alamat', $alamat ?: null);
                $db->bind(':no_telp', $no_telp ?: null);
                $db->bind(':email', $email ?: null);
                $db->bind(':id', $userId);
                $db->execute();
            } elseif ($role === 'siswa') {
                $db->query("UPDATE siswa SET 
                    nama_lengkap = :nama, 
                    jenis_kelamin = :jk, 
                    tempat_lahir = :tempat, 
                    tanggal_lahir = :tgl, 
                    alamat = :alamat, 
                    no_telp = :no_telp, 
                    email = :email 
                    WHERE user_id = :id");
                $db->bind(':nama', $nama);
                $db->bind(':jk', $jenis_kelamin);
                $db->bind(':tempat', $tempat_lahir ?: null);
                $db->bind(':tgl', $tglVal);
                $db->bind(':alamat', $alamat ?: null);
                $db->bind(':no_telp', $no_telp ?: null);
                $db->bind(':email', $email ?: null);
                $db->bind(':id', $userId);
                $db->execute();
            }
            
            set_flash('success', 'Data profil identitas dan akun berhasil diperbarui.');
            redirect('profile');
        } else {
            redirect('profile');
        }
    }

    public function update_photo() {
        require_login();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'];
            
            if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['foto_profil']['tmp_name'];
                $fileName = $_FILES['foto_profil']['name'];
                $fileSize = $_FILES['foto_profil']['size'];
                
                // Backend Validation
                $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
                $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $fileTmp);
                finfo_close($finfo);
                
                if (!in_array($mimeType, $allowedMime) || !in_array($fileExt, $allowedExt)) {
                    set_flash('error', 'Format foto tidak didukung. Gunakan JPG, PNG, atau WEBP.');
                    redirect('profile');
                    return;
                }
                
                if ($fileSize > 2 * 1024 * 1024) {
                    set_flash('error', 'Ukuran foto terlalu besar. Maksimal 2 MB.');
                    redirect('profile');
                    return;
                }
                
                $uploadDir = 'public/uploads/profiles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Clean old profile pictures for this user
                foreach ($allowedExt as $ext) {
                    $oldFile = $uploadDir . 'profile_' . $userId . '.' . $ext;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                
                $newFileName = 'profile_' . $userId . '.' . $fileExt;
                $destPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmp, $destPath)) {
                    set_flash('success', 'Foto profil berhasil diperbarui.');
                } else {
                    set_flash('error', 'Gagal mengunggah foto profil.');
                }
            } else {
                set_flash('error', 'Tidak ada foto yang dipilih atau terjadi kesalahan.');
            }
            redirect('profile');
        } else {
            redirect('profile');
        }
    }

    public function delete_photo() {
        require_login();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'];
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            $uploadDir = 'public/uploads/profiles/';
            
            $deleted = false;
            foreach ($allowedExt as $ext) {
                $oldFile = $uploadDir . 'profile_' . $userId . '.' . $ext;
                if (file_exists($oldFile)) {
                    if (unlink($oldFile)) {
                        $deleted = true;
                    }
                }
            }
            
            if ($deleted) {
                set_flash('success', 'Foto profil berhasil dihapus.');
            } else {
                set_flash('error', 'Foto profil tidak ditemukan atau gagal dihapus.');
            }
            redirect('profile');
        } else {
            redirect('profile');
        }
    }
}
