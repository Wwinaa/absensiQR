<?php
class GuruModel extends BaseModel {
    public function getAllGuru($search = '') {
        $query = "
            SELECT 
                g.*, 
                g.nama_lengkap AS nama,
                k.nama_kelas AS wali_kelas_nama,
                k.id AS kelas_id,
                (SELECT GROUP_CONCAT(mp.nama_mapel SEPARATOR ', ') 
                 FROM mata_pelajaran mp 
                 WHERE mp.guru_id = g.id OR (wkd.id IS NOT NULL AND mp.wali_kelas_id = wkd.id)) AS mapel_diajar
            FROM guru g
            LEFT JOIN wali_kelas_data wkd ON g.user_id = wkd.user_id
            LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id
            LEFT JOIN kelas k ON wk.kelas_id = k.id
        ";
        if (!empty($search)) {
            $query .= " WHERE g.nip LIKE :search OR g.nama_lengkap LIKE :search OR k.nama_kelas LIKE :search";
        }
        $query .= " ORDER BY g.nama_lengkap ASC";
        
        $this->db->query($query);
        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        return $this->db->resultSet();
    }

    public function getGuruById($id) {
        $this->db->query("
            SELECT g.*, 
                   g.nama_lengkap AS nama,
                   k.nama_kelas AS wali_kelas_nama,
                   k.id AS kelas_id
            FROM guru g
            LEFT JOIN wali_kelas_data wkd ON g.user_id = wkd.user_id
            LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id
            LEFT JOIN kelas k ON wk.kelas_id = k.id
            WHERE g.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getGuruByUserId($user_id) {
        $this->db->query("SELECT * FROM guru WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function tambahGuru($data) {
        $this->db->query("INSERT INTO guru (user_id, nip, barcode, nama_lengkap) VALUES (:user_id, :nip, :barcode, :nama)");
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':nip', $data['nip']);
        $this->db->bind(':barcode', $data['barcode'] ?? $data['nip']);
        $this->db->bind(':nama', $data['nama']);
        return $this->db->execute();
    }

    public function updateGuru($id, $data) {
        $this->db->query("UPDATE guru SET nip = :nip, nama_lengkap = :nama WHERE id = :id");
        $this->db->bind(':nip', $data['nip']);
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function hapusGuru($id) {
        $this->db->query("DELETE FROM guru WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getJadwalHariIni($guru_id) {
        $hari_ini = $this->getHariIndonesia(date('l'));
        
        $query = "SELECT j.jam_mulai, j.jam_selesai, m.nama_mapel as mata_pelajaran, k.nama_kelas as kelas 
                  FROM jadwal_mengajar j 
                  JOIN kelas k ON j.kelas_id = k.id 
                  JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
                  WHERE j.guru_id = :guru_id AND j.hari = :hari 
                  ORDER BY j.jam_mulai ASC";
                  
        $this->db->query($query);
        $this->db->bind(':guru_id', $guru_id);
        $this->db->bind(':hari', $hari_ini);
        
        return $this->db->resultSet();
    }
    
    private function getHariIndonesia($dayInEnglish) {
        $hari = [
            'Sunday' => 'minggu',
            'Monday' => 'senin',
            'Tuesday' => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday' => 'kamis',
            'Friday' => 'jumat',
            'Saturday' => 'sabtu'
        ];
        return $hari[$dayInEnglish] ?? strtolower($dayInEnglish);
    }
}
