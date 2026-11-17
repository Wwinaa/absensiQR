<?php
class MataPelajaranModel extends BaseModel {
    public function getAllMataPelajaran() {
        $this->db->query("
            SELECT m.*, 
                   g.nama_lengkap as nama_guru, 
                   wkd.nama_lengkap as nama_wali_kelas,
                   COALESCE(g.nama_lengkap, wkd.nama_lengkap) as nama_pengajar,
                   CASE WHEN m.wali_kelas_id IS NOT NULL THEN 'Wali Kelas' ELSE 'Guru' END as tipe_pengajar
            FROM mata_pelajaran m 
            LEFT JOIN guru g ON m.guru_id = g.id 
            LEFT JOIN wali_kelas_data wkd ON m.wali_kelas_id = wkd.id 
            ORDER BY m.tingkat ASC, m.nama_mapel ASC
        ");
        return $this->db->resultSet();
    }

    public function getMataPelajaranById($id) {
        $this->db->query("
            SELECT m.*, 
                   g.nama_lengkap as nama_guru, 
                   wkd.nama_lengkap as nama_wali_kelas,
                   COALESCE(g.nama_lengkap, wkd.nama_lengkap) as nama_pengajar,
                   CASE WHEN m.wali_kelas_id IS NOT NULL THEN 'Wali Kelas' ELSE 'Guru' END as tipe_pengajar
            FROM mata_pelajaran m 
            LEFT JOIN guru g ON m.guru_id = g.id 
            LEFT JOIN wali_kelas_data wkd ON m.wali_kelas_id = wkd.id 
            WHERE m.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getMataPelajaranByNamaTingkat($nama_mapel, $tingkat, $exclude_id = null) {
        $query = "SELECT * FROM mata_pelajaran WHERE nama_mapel = :nama_mapel AND tingkat = :tingkat";
        if ($exclude_id) {
            $query .= " AND id != :id";
        }
        $this->db->query($query);
        $this->db->bind(':nama_mapel', $nama_mapel);
        $this->db->bind(':tingkat', $tingkat);
        if ($exclude_id) {
            $this->db->bind(':id', $exclude_id);
        }
        return $this->db->single();
    }

    public function parsePengajarInput($data) {
        $guru_id = null;
        $wali_kelas_id = null;

        $input = $data['pengajar'] ?? $data['guru_id'] ?? $data['pengajar_id'] ?? null;

        if (!empty($input) && is_string($input) && strpos($input, '_') !== false) {
            $parts = explode('_', $input, 2);
            if ($parts[0] === 'wali') {
                $wali_kelas_id = (int)$parts[1];
            } elseif ($parts[0] === 'guru') {
                $guru_id = (int)$parts[1];
            }
        } else {
            $guru_id = (!empty($data['guru_id']) && is_numeric($data['guru_id'])) ? (int)$data['guru_id'] : null;
            $wali_kelas_id = (!empty($data['wali_kelas_id']) && is_numeric($data['wali_kelas_id'])) ? (int)$data['wali_kelas_id'] : null;
        }

        if ($guru_id !== null && $guru_id <= 0) $guru_id = null;
        if ($wali_kelas_id !== null && $wali_kelas_id <= 0) $wali_kelas_id = null;

        // Auto-link guru_id and wali_kelas_id if both exist for this teacher
        if ($guru_id && !$wali_kelas_id) {
            $this->db->query("SELECT wkd.id FROM wali_kelas_data wkd JOIN guru g ON wkd.user_id = g.user_id WHERE g.id = :gid LIMIT 1");
            $this->db->bind(':gid', $guru_id);
            $wkRow = $this->db->single();
            if ($wkRow) $wali_kelas_id = (int)$wkRow->id;
        } elseif ($wali_kelas_id && !$guru_id) {
            $this->db->query("SELECT g.id FROM guru g JOIN wali_kelas_data wkd ON g.user_id = wkd.user_id WHERE wkd.id = :wkid LIMIT 1");
            $this->db->bind(':wkid', $wali_kelas_id);
            $gRow = $this->db->single();
            if ($gRow) $guru_id = (int)$gRow->id;
        }

        return [$guru_id, $wali_kelas_id];
    }

    public function countMapelByPengajar($guru_id, $wali_kelas_id, $exclude_mapel_id = null) {
        $conditions = [];
        if ($guru_id) $conditions[] = "guru_id = :guru_id";
        if ($wali_kelas_id) $conditions[] = "wali_kelas_id = :wali_kelas_id";
        
        if (empty($conditions)) return 0;
        
        $query = "SELECT COUNT(DISTINCT id) as total FROM mata_pelajaran WHERE (" . implode(' OR ', $conditions) . ")";
        if ($exclude_mapel_id) $query .= " AND id != :id";
        
        $this->db->query($query);
        if ($guru_id) $this->db->bind(':guru_id', $guru_id);
        if ($wali_kelas_id) $this->db->bind(':wali_kelas_id', $wali_kelas_id);
        if ($exclude_mapel_id) $this->db->bind(':id', $exclude_mapel_id);
        
        return (int)($this->db->single()->total ?? 0);
    }

    public function tambahMataPelajaran($data) {
        if ($this->getMataPelajaranByNamaTingkat($data['nama_mapel'], $data['tingkat'])) {
            return false;
        }

        [$guru_id, $wali_kelas_id] = $this->parsePengajarInput($data);

        // Aturan: penugasan maksimal 2 mata pelajaran per pengajar
        if (($guru_id || $wali_kelas_id) && $this->countMapelByPengajar($guru_id, $wali_kelas_id) >= 2) {
            return 'limit_exceeded';
        }

        $this->db->query("INSERT INTO mata_pelajaran (kode_mapel, nama_mapel, tingkat, guru_id, wali_kelas_id) 
                          VALUES (:kode_mapel, :nama_mapel, :tingkat, :guru_id, :wali_kelas_id)");
        $this->db->bind(':kode_mapel', $data['kode_mapel']);
        $this->db->bind(':nama_mapel', $data['nama_mapel']);
        $this->db->bind(':tingkat', $data['tingkat']);
        $this->db->bind(':guru_id', $guru_id);
        $this->db->bind(':wali_kelas_id', $wali_kelas_id);
        return $this->db->execute();
    }

    public function updateMataPelajaran($id, $data) {
        if ($this->getMataPelajaranByNamaTingkat($data['nama_mapel'], $data['tingkat'], $id)) {
            return false;
        }

        [$guru_id, $wali_kelas_id] = $this->parsePengajarInput($data);

        // Aturan: penugasan maksimal 2 mata pelajaran per pengajar
        if (($guru_id || $wali_kelas_id) && $this->countMapelByPengajar($guru_id, $wali_kelas_id, $id) >= 2) {
            return 'limit_exceeded';
        }

        $this->db->query("UPDATE mata_pelajaran SET 
                          kode_mapel = :kode_mapel, 
                          nama_mapel = :nama_mapel, 
                          tingkat = :tingkat, 
                          guru_id = :guru_id, 
                          wali_kelas_id = :wali_kelas_id 
                          WHERE id = :id");
        $this->db->bind(':kode_mapel', $data['kode_mapel']);
        $this->db->bind(':nama_mapel', $data['nama_mapel']);
        $this->db->bind(':tingkat', $data['tingkat']);
        $this->db->bind(':guru_id', $guru_id);
        $this->db->bind(':wali_kelas_id', $wali_kelas_id);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function hapusMataPelajaran($id) {
        // Cek apakah dipakai di jadwal_mengajar
        $this->db->query("SELECT COUNT(*) as total FROM jadwal_mengajar WHERE mata_pelajaran_id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        if ($result->total > 0) {
            return false;
        }
        $this->db->query("DELETE FROM mata_pelajaran WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
