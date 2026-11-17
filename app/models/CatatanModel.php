<?php
class CatatanModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllCatatan($filters = []) {
        $query = "SELECT c.*, s.nama_lengkap as nama_siswa, s.nis, k.nama_kelas, 
                         COALESCE(g.nama_lengkap, u.username, 'Admin/Wali Kelas') as nama_pembuat 
                  FROM catatan_pembinaan c 
                  JOIN siswa s ON c.siswa_id = s.id 
                  JOIN kelas k ON c.kelas_id = k.id 
                  JOIN users u ON c.pembuat_user_id = u.id
                  LEFT JOIN guru g ON g.user_id = u.id
                  WHERE 1=1";
        
        if (!empty($filters['siswa_id'])) {
            $query .= " AND c.siswa_id = :siswa_id";
        }
        if (!empty($filters['kelas_id'])) {
            $query .= " AND c.kelas_id = :kelas_id";
        }
        if (!empty($filters['pembuat_user_id'])) {
            $query .= " AND c.pembuat_user_id = :pembuat_user_id";
        }
        if (!empty($filters['visibilitas'])) {
            $query .= " AND c.visibilitas = :visibilitas";
        }
        
        $query .= " ORDER BY c.created_at DESC";
        
        $this->db->query($query);
        
        if (!empty($filters['siswa_id'])) $this->db->bind(':siswa_id', $filters['siswa_id']);
        if (!empty($filters['kelas_id'])) $this->db->bind(':kelas_id', $filters['kelas_id']);
        if (!empty($filters['pembuat_user_id'])) $this->db->bind(':pembuat_user_id', $filters['pembuat_user_id']);
        if (!empty($filters['visibilitas'])) $this->db->bind(':visibilitas', $filters['visibilitas']);
        
        return $this->db->resultSet();
    }
    
    public function getCatatanById($id) {
        $this->db->query("SELECT * FROM catatan_pembinaan WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function tambahCatatan($data) {
        $this->db->query("INSERT INTO catatan_pembinaan (siswa_id, kelas_id, pembuat_user_id, pembuat_role, kategori, isi_catatan, visibilitas) 
                          VALUES (:siswa_id, :kelas_id, :pembuat_user_id, :pembuat_role, :kategori, :isi_catatan, :visibilitas)");
        $this->db->bind(':siswa_id', $data['siswa_id']);
        $this->db->bind(':kelas_id', $data['kelas_id']);
        $this->db->bind(':pembuat_user_id', $data['pembuat_user_id']);
        $this->db->bind(':pembuat_role', $data['pembuat_role']);
        $this->db->bind(':kategori', $data['kategori']);
        $this->db->bind(':isi_catatan', $data['isi_catatan']);
        $this->db->bind(':visibilitas', $data['visibilitas']);
        return $this->db->execute();
    }

    public function editCatatan($data) {
        $this->db->query("UPDATE catatan_pembinaan SET kategori = :kategori, isi_catatan = :isi_catatan, visibilitas = :visibilitas WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':kategori', $data['kategori']);
        $this->db->bind(':isi_catatan', $data['isi_catatan']);
        $this->db->bind(':visibilitas', $data['visibilitas']);
        return $this->db->execute();
    }

    public function hapusCatatan($id) {
        $this->db->query("DELETE FROM catatan_pembinaan WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function markAsRead($id) {
        $this->db->query("UPDATE catatan_pembinaan SET status_baca = 1 WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
