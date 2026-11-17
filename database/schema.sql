CREATE DATABASE IF NOT EXISTS db_absensi_smk;
USE db_absensi_smk;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kepsek', 'guru', 'walikelas', 'siswa') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE jurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_jurusan VARCHAR(20) NOT NULL UNIQUE,
    nama_jurusan VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tahun_ajaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tahun VARCHAR(20) NOT NULL,
    semester ENUM('ganjil', 'genap') NOT NULL,
    status ENUM('aktif', 'tidak_aktif') DEFAULT 'tidak_aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (tahun, semester)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE guru (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nip VARCHAR(30) UNIQUE,
    nama VARCHAR(100) NOT NULL,
    qr_identifier VARCHAR(100) UNIQUE,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(50) NOT NULL,
    tingkat ENUM('10', '11', '12') NOT NULL,
    jurusan_id INT NOT NULL,
    wali_kelas_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE CASCADE,
    FOREIGN KEY (wali_kelas_id) REFERENCES guru(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nisn VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    kelas_id INT NOT NULL,
    qr_identifier VARCHAR(100) UNIQUE,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE jadwal_mengajar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guru_id INT NOT NULL,
    kelas_id INT NOT NULL,
    mata_pelajaran VARCHAR(100) NOT NULL,
    hari ENUM('senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu') NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    ruang VARCHAR(50),
    tahun_ajaran_id INT NOT NULL,
    FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajaran(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME,
    jam_pulang TIME,
    status ENUM('hadir', 'terlambat', 'izin', 'sakit', 'alpha') NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE catatan_guru (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guru_id INT NOT NULL,
    kepala_sekolah_id INT NOT NULL,
    tanggal DATE NOT NULL,
    isi_catatan TEXT NOT NULL,
    status_baca ENUM('belum', 'sudah') DEFAULT 'belum',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE,
    FOREIGN KEY (kepala_sekolah_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
