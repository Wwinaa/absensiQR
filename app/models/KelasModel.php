<?php
class KelasModel extends BaseModel {
    public function getAllKelas($search = '') {
        $query = "
            SELECT k.*, j.nama_jurusan, wkd.nama_lengkap as nama_wali_kelas, wk.wali_kelas_data_id as wali_kelas_id
            FROM kelas k
            LEFT JOIN jurusan j ON k.jurusan_id = j.id
            LEFT JOIN wali_kelas wk ON k.id = wk.kelas_id
            LEFT JOIN wali_kelas_data wkd ON wk.wali_kelas_data_id = wkd.id
        ";
        if (!empty($search)) {
            $query .= " WHERE k.nama_kelas LIKE :search OR j.nama_jurusan LIKE :search";
        }
        $query .= " ORDER BY k.tingkat ASC, k.nama_kelas ASC";
        
        $this->db->query($query);
        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        return $this->db->resultSet();
    }

    public function getKelasById($id) {
        $this->db->query("
            SELECT k.*, wk.wali_kelas_data_id as wali_kelas_id 
            FROM kelas k 
            LEFT JOIN wali_kelas wk ON k.id = wk.kelas_id 
            WHERE k.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    private function normalizeTingkat($tingkat, $namaKelas = '') {
        $tingkat = strtoupper(trim($tingkat ?? ''));
        if ($tingkat === '10') $tingkat = 'X';
        elseif ($tingkat === '11') $tingkat = 'XI';
        elseif ($tingkat === '12') $tingkat = 'XII';

        if (!in_array($tingkat, ['X', 'XI', 'XII'])) {
            if (preg_match('/^XII\b/i', $namaKelas)) $tingkat = 'XII';
            elseif (preg_match('/^XI\b/i', $namaKelas)) $tingkat = 'XI';
            elseif (preg_match('/^X\b/i', $namaKelas)) $tingkat = 'X';
            else $tingkat = 'X';
        }
        return $tingkat;
    }

    public function tambahKelas($data) {
        $tingkat = $this->normalizeTingkat($data['tingkat'] ?? '', $data['nama_kelas'] ?? '');
        $this->db->query("INSERT INTO kelas (nama_kelas, tingkat, jurusan_id) VALUES (:nama, :tingkat, :jurusan_id)");
        $this->db->bind(':nama', $data['nama_kelas']);
        $this->db->bind(':tingkat', $tingkat);
        $this->db->bind(':jurusan_id', $data['jurusan_id']);
        
        if ($this->db->execute()) {
            $kelas_id = $this->db->lastInsertId();
            $wali_kelas_id = !empty($data['wali_kelas_id']) ? $data['wali_kelas_id'] : null;
            
            if ($wali_kelas_id) {
                // Determine active tahun ajaran
                $this->db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
                $tahun = $this->db->single();
                $tahun_id = $tahun ? $tahun->id : 1; // fallback to 1 if none active
                
                $this->db->query("INSERT INTO wali_kelas (wali_kelas_data_id, kelas_id, tahun_ajaran_id) VALUES (:guru_id, :kelas_id, :tahun_id)");
                $this->db->bind(':guru_id', $wali_kelas_id);
                $this->db->bind(':kelas_id', $kelas_id);
                $this->db->bind(':tahun_id', $tahun_id);
                $this->db->execute();
            }
            return true;
        }
        return false;
    }

    public function updateKelas($id, $data) {
        $tingkat = $this->normalizeTingkat($data['tingkat'] ?? '', $data['nama_kelas'] ?? '');
        $this->db->query("UPDATE kelas SET nama_kelas = :nama, tingkat = :tingkat, jurusan_id = :jurusan_id WHERE id = :id");
        $this->db->bind(':nama', $data['nama_kelas']);
        $this->db->bind(':tingkat', $tingkat);
        $this->db->bind(':jurusan_id', $data['jurusan_id']);
        $this->db->bind(':id', $id);
        
        if ($this->db->execute()) {
            $wali_kelas_id = !empty($data['wali_kelas_id']) ? $data['wali_kelas_id'] : null;
            
            // Delete existing mapping
            $this->db->query("DELETE FROM wali_kelas WHERE kelas_id = :kelas_id");
            $this->db->bind(':kelas_id', $id);
            $this->db->execute();
            
            if ($wali_kelas_id) {
                // Determine active tahun ajaran
                $this->db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
                $tahun = $this->db->single();
                $tahun_id = $tahun ? $tahun->id : 1;
                
                $this->db->query("INSERT INTO wali_kelas (wali_kelas_data_id, kelas_id, tahun_ajaran_id) VALUES (:guru_id, :kelas_id, :tahun_id)");
                $this->db->bind(':guru_id', $wali_kelas_id);
                $this->db->bind(':kelas_id', $id);
                $this->db->bind(':tahun_id', $tahun_id);
                $this->db->execute();
            }
            return true;
        }
        return false;
    }

    public function hapusKelas($id) {
        try {
            $this->db->query("DELETE FROM kelas WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getKelasByWaliKelas($guru_id) {
        $this->db->query("
            SELECT k.*, wk.wali_kelas_data_id as wali_kelas_id 
            FROM kelas k 
            JOIN wali_kelas wk ON k.id = wk.kelas_id 
            WHERE wk.wali_kelas_data_id = :guru_id
            LIMIT 1
        ");
        $this->db->bind(':guru_id', $guru_id);
        return $this->db->single();
    }
}
