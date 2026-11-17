USE db_absensi_smk;

-- Insert Users (Password for all: admin123)
-- 1. Admin
INSERT INTO users (username, password, role) VALUES ('admin', '$2y$10$LNwT8tBW66EpMzvUXAeCneG8rgtmp9M7Um8Bfq8bbqp15rsYSKMgi', 'admin');
-- 2. Kepsek
INSERT INTO users (username, password, role) VALUES ('kepsek', '$2y$10$LNwT8tBW66EpMzvUXAeCneG8rgtmp9M7Um8Bfq8bbqp15rsYSKMgi', 'kepsek');
-- 3. Guru (Wali Kelas)
INSERT INTO users (username, password, role) VALUES ('guru1', '$2y$10$LNwT8tBW66EpMzvUXAeCneG8rgtmp9M7Um8Bfq8bbqp15rsYSKMgi', 'walikelas');
-- 4. Siswa
INSERT INTO users (username, password, role) VALUES ('siswa1', '$2y$10$LNwT8tBW66EpMzvUXAeCneG8rgtmp9M7Um8Bfq8bbqp15rsYSKMgi', 'siswa');

-- Insert Jurusan
INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES ('RPL', 'Rekayasa Perangkat Lunak');
INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES ('TKJ', 'Teknik Komputer dan Jaringan');

-- Insert Tahun Ajaran
INSERT INTO tahun_ajaran (tahun, semester, status) VALUES ('2023/2024', 'ganjil', 'tidak_aktif');
INSERT INTO tahun_ajaran (tahun, semester, status) VALUES ('2023/2024', 'genap', 'aktif');

-- Insert Guru
-- user_id = 3 (guru1)
INSERT INTO guru (user_id, nip, nama, qr_identifier) VALUES (3, '198001012005011001', 'Budi Santoso, S.Kom', 'QR-GURU-001');

-- Insert Kelas
-- jurusan_id = 1 (RPL), wali_kelas_id = 1 (Budi)
INSERT INTO kelas (nama_kelas, tingkat, jurusan_id, wali_kelas_id) VALUES ('X RPL 1', '10', 1, 1);

-- Insert Siswa
-- user_id = 4 (siswa1), kelas_id = 1
INSERT INTO siswa (user_id, nis, nisn, nama, jenis_kelamin, kelas_id, qr_identifier) VALUES (4, '1001', '0051234567', 'Andi Pratama', 'L', 1, 'QR-SISWA-001');

-- Optional: Initial Jadwal Mengajar Data
INSERT INTO jadwal_mengajar (guru_id, kelas_id, mata_pelajaran, hari, jam_mulai, jam_selesai, ruang, tahun_ajaran_id) 
VALUES (1, 1, 'Pemrograman Web', 'senin', '08:00:00', '10:00:00', 'Lab Komputer 1', 2);
