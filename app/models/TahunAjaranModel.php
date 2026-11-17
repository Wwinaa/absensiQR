<?php
class TahunAjaranModel extends BaseModel {
    public function getAllTahunAjaran() {
        $this->db->query("SELECT *, IF(is_active = 1, 'aktif', 'tidak_aktif') AS status FROM tahun_ajaran ORDER BY tahun DESC, semester DESC");
        return $this->db->resultSet();
    }

    public function getTahunAjaranById($id) {
        $this->db->query("SELECT *, IF(is_active = 1, 'aktif', 'tidak_aktif') AS status FROM tahun_ajaran WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getActiveTahunAjaran() {
        $this->db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        return $this->db->single();
    }

    public function getAllActiveTahunAjaran() {
        $this->db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 ORDER BY tahun DESC, semester DESC");
        return $this->db->resultSet();
    }

    public function tambahTahunAjaran($data) {

        $is_active = ($data['status'] === 'aktif') ? 1 : 0;
        $this->db->query("INSERT INTO tahun_ajaran (tahun, semester, is_active) VALUES (:tahun, :semester, :is_active)");
        $this->db->bind(':tahun', $data['tahun']);
        $this->db->bind(':semester', $data['semester']);
        $this->db->bind(':is_active', $is_active);
        return $this->db->execute();
    }

    public function updateTahunAjaran($id, $data) {

        $is_active = ($data['status'] === 'aktif') ? 1 : 0;
        $this->db->query("UPDATE tahun_ajaran SET tahun = :tahun, semester = :semester, is_active = :is_active WHERE id = :id");
        $this->db->bind(':tahun', $data['tahun']);
        $this->db->bind(':semester', $data['semester']);
        $this->db->bind(':is_active', $is_active);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function hapusTahunAjaran($id) {
        $this->db->query("DELETE FROM tahun_ajaran WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function setAktif($id, $is_active = 1) {
        $this->db->query("UPDATE tahun_ajaran SET is_active = :is_active WHERE id = :id");
        $this->db->bind(':is_active', $is_active);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }


}
