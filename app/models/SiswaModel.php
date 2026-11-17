<?php
class SiswaModel extends BaseModel {
    public function getAllSiswa($search = '', $kelas_id = '') {
        $query = "
            SELECT s.*, s.nama_lengkap AS nama, k.nama_kelas, j.nama_jurusan 
            FROM siswa s
            LEFT JOIN kelas k ON s.kelas_id = k.id
            LEFT JOIN jurusan j ON k.jurusan_id = j.id
            WHERE 1=1
        ";
        
        if (!empty($search)) {
            $query .= " AND (s.nis LIKE :search OR s.nama_lengkap LIKE :search)";
        }
        if (!empty($kelas_id)) {
            $query .= " AND s.kelas_id = :kelas_id";
        }
        
        $query .= " ORDER BY s.nama_lengkap ASC";
        
        $this->db->query($query);
        
        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        if (!empty($kelas_id)) {
            $this->db->bind(':kelas_id', $kelas_id);
        }
        
        return $this->db->resultSet();
    }

    public function getSiswaById($id) {
        $this->db->query("
            SELECT s.*, k.nama_kelas, j.nama_jurusan 
            FROM siswa s 
            LEFT JOIN kelas k ON s.kelas_id = k.id 
            LEFT JOIN jurusan j ON k.jurusan_id = j.id 
            WHERE s.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getSiswaByUserId($user_id) {
        $this->db->query("SELECT * FROM siswa WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function tambahSiswa($data) {
        $this->db->query("INSERT INTO siswa (user_id, nis, nisn, barcode, nama_lengkap, jenis_kelamin, kelas_id) 
                         VALUES (:user_id, :nis, :nisn, :barcode, :nama, :jenis_kelamin, :kelas_id)");
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':nis', $data['nis']);
        $this->db->bind(':nisn', $data['nisn']);
        // Gunakan NIS sebagai barcode jika tidak ada
        $this->db->bind(':barcode', $data['barcode'] ?? $data['nis']);
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':jenis_kelamin', $data['jenis_kelamin']);
        $this->db->bind(':kelas_id', $data['kelas_id']);
        return $this->db->execute();
    }

    public function updateSiswa($id, $data) {
        $this->db->query("UPDATE siswa SET nis = :nis, nisn = :nisn, nama_lengkap = :nama, jenis_kelamin = :jenis_kelamin, kelas_id = :kelas_id WHERE id = :id");
        $this->db->bind(':nis', $data['nis']);
        $this->db->bind(':nisn', $data['nisn']);
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':jenis_kelamin', $data['jenis_kelamin']);
        $this->db->bind(':kelas_id', $data['kelas_id']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function hapusSiswa($id) {
        $this->db->query("DELETE FROM siswa WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
