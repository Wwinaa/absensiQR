<?php
class JurusanModel extends BaseModel {
    public function getAllJurusan($search = '') {
        $query = "SELECT * FROM jurusan";
        if (!empty($search)) {
            $query .= " WHERE kode_jurusan LIKE :search OR nama_jurusan LIKE :search";
        }
        $query .= " ORDER BY nama_jurusan ASC";
        
        $this->db->query($query);
        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        return $this->db->resultSet();
    }

    public function getJurusanById($id) {
        $this->db->query("SELECT * FROM jurusan WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function tambahJurusan($data) {
        $this->db->query("INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES (:kode, :nama)");
        $this->db->bind(':kode', $data['kode_jurusan']);
        $this->db->bind(':nama', $data['nama_jurusan']);
        return $this->db->execute();
    }

    public function updateJurusan($id, $data) {
        $this->db->query("UPDATE jurusan SET kode_jurusan = :kode, nama_jurusan = :nama WHERE id = :id");
        $this->db->bind(':kode', $data['kode_jurusan']);
        $this->db->bind(':nama', $data['nama_jurusan']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function hapusJurusan($id) {
        // Validation constraint should be handled if foreign keys exist, or rely on InnoDB exception catching
        try {
            $this->db->query("DELETE FROM jurusan WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
