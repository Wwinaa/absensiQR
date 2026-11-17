<?php
class WaliKelasModel extends BaseModel {
    public function getAllWaliKelas($search = '') {
        $query = "SELECT * FROM wali_kelas_data";
        if (!empty($search)) {
            $query .= " WHERE nama_lengkap LIKE :search OR nip LIKE :search";
        }
        $query .= " ORDER BY nama_lengkap ASC";
        $this->db->query($query);
        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        return $this->db->resultSet();
    }

    public function getAllWaliKelasWithDetails($search = '') {
        $query = "
            SELECT 
                wkd.*,
                k.id as kelas_id,
                k.nama_kelas,
                (SELECT GROUP_CONCAT(mp.nama_pelajaran SEPARATOR ', ') 
                 FROM mata_pelajaran mp 
                 WHERE mp.wali_kelas_id = wkd.id) as mapel_diajar,
                (SELECT COUNT(*) 
                 FROM jadwal_mengajar jm 
                 WHERE jm.wali_kelas_id = wkd.id) as total_jadwal
            FROM wali_kelas_data wkd
            LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id
            LEFT JOIN kelas k ON wk.kelas_id = k.id
        ";
        if (!empty($search)) {
            $query .= " WHERE wkd.nama_lengkap LIKE :search OR wkd.nip LIKE :search OR k.nama_kelas LIKE :search";
        }
        $query .= " ORDER BY wkd.nama_lengkap ASC";
        $this->db->query($query);
        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        return $this->db->resultSet();
    }

    public function getWaliKelasById($id) {
        $this->db->query("
            SELECT 
                wkd.*,
                k.id as kelas_id,
                k.nama_kelas
            FROM wali_kelas_data wkd
            LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id
            LEFT JOIN kelas k ON wk.kelas_id = k.id
            WHERE wkd.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getWaliKelasByUserId($user_id) {
        $this->db->query("
            SELECT 
                wkd.*,
                k.id as kelas_id,
                k.nama_kelas
            FROM wali_kelas_data wkd
            LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id
            LEFT JOIN kelas k ON wk.kelas_id = k.id
            WHERE wkd.user_id = :user_id
        ");
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function getWaliKelasByBarcode($barcode) {
        $this->db->query("
            SELECT 
                wkd.*,
                k.id as kelas_id,
                k.nama_kelas
            FROM wali_kelas_data wkd
            LEFT JOIN wali_kelas wk ON wkd.id = wk.wali_kelas_data_id
            LEFT JOIN kelas k ON wk.kelas_id = k.id
            WHERE wkd.barcode = :barcode
        ");
        $this->db->bind(':barcode', $barcode);
        return $this->db->single();
    }

    public function updateWaliKelas($id, $data) {
        $this->db->query("UPDATE wali_kelas_data SET nama_lengkap = :nama, nip = :nip, jenis_kelamin = :jk, email = :email, no_telp = :no_telp WHERE id = :id");
        $this->db->bind(':nama', $data['nama_lengkap']);
        $this->db->bind(':nip', $data['nip']);
        $this->db->bind(':jk', $data['jenis_kelamin']);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':no_telp', $data['no_telp'] ?? null);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
