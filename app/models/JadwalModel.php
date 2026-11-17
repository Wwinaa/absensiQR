<?php
class JadwalModel extends BaseModel {
    public function getAllJadwal($filters = []) {
        $query = "SELECT j.*, 
                         g.nama_lengkap as nama_guru,
                         wkd.nama_lengkap as nama_wali_kelas,
                         COALESCE(g.nama_lengkap, wkd.nama_lengkap) as nama_pengajar,
                         CASE WHEN j.wali_kelas_id IS NOT NULL THEN 'Wali Kelas' ELSE 'Guru' END as tipe_pengajar,
                         k.nama_kelas, m.nama_mapel, t.tahun, t.semester 
                  FROM jadwal_mengajar j 
                  LEFT JOIN guru g ON j.guru_id = g.id 
                  LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id 
                  JOIN kelas k ON j.kelas_id = k.id 
                  JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                  JOIN tahun_ajaran t ON j.tahun_ajaran_id = t.id 
                  WHERE 1=1";
        
        if (!empty($filters['hari'])) {
            $query .= " AND j.hari = :hari";
        }
        if (!empty($filters['kelas_id'])) {
            $query .= " AND j.kelas_id = :kelas_id";
        }
        if (!empty($filters['pengajar'])) {
            $parts = explode('_', $filters['pengajar'], 2);
            if ($parts[0] === 'wali') {
                $query .= " AND j.wali_kelas_id = :wali_filter";
            } elseif ($parts[0] === 'guru') {
                $query .= " AND j.guru_id = :guru_filter";
            }
        } elseif (!empty($filters['guru_id'])) {
            $query .= " AND j.guru_id = :guru_id";
        } elseif (!empty($filters['wali_kelas_id'])) {
            $query .= " AND j.wali_kelas_id = :wali_kelas_id";
        }
        if (!empty($filters['mata_pelajaran_id'])) {
            $query .= " AND j.mata_pelajaran_id = :mata_pelajaran_id";
        }
        if (!empty($filters['tahun_ajaran_id'])) {
            $query .= " AND j.tahun_ajaran_id = :tahun_ajaran_id";
        }
        
        $query .= " ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat','sabtu'), j.jam_mulai ASC";
        
        $this->db->query($query);
        
        if (!empty($filters['hari'])) $this->db->bind(':hari', $filters['hari']);
        if (!empty($filters['kelas_id'])) $this->db->bind(':kelas_id', $filters['kelas_id']);
        if (!empty($filters['pengajar'])) {
            $parts = explode('_', $filters['pengajar'], 2);
            if ($parts[0] === 'wali') {
                $this->db->bind(':wali_filter', (int)$parts[1]);
            } elseif ($parts[0] === 'guru') {
                $this->db->bind(':guru_filter', (int)$parts[1]);
            }
        } elseif (!empty($filters['guru_id'])) {
            $this->db->bind(':guru_id', $filters['guru_id']);
        } elseif (!empty($filters['wali_kelas_id'])) {
            $this->db->bind(':wali_kelas_id', $filters['wali_kelas_id']);
        }
        if (!empty($filters['mata_pelajaran_id'])) $this->db->bind(':mata_pelajaran_id', $filters['mata_pelajaran_id']);
        if (!empty($filters['tahun_ajaran_id'])) $this->db->bind(':tahun_ajaran_id', $filters['tahun_ajaran_id']);
        
        return $this->db->resultSet();
    }

    public function getJadwalById($id) {
        $this->db->query("SELECT j.*,
                                 COALESCE(g.nama_lengkap, wkd.nama_lengkap) as nama_pengajar,
                                 CASE WHEN j.wali_kelas_id IS NOT NULL THEN 'Wali Kelas' ELSE 'Guru' END as tipe_pengajar
                          FROM jadwal_mengajar j
                          LEFT JOIN guru g ON j.guru_id = g.id
                          LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id
                          WHERE j.id = :id");
        $this->db->bind(':id', $id);
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

    /**
     * Memeriksa bentrok jadwal untuk pengajar dan kelas
     */
    public function cekBentrokJadwal($hari, $jam_mulai, $jam_selesai, $guru_id, $wali_kelas_id, $kelas_id, $tahun_ajaran_id, $exclude_id = null) {
        // Validasi format jam
        if (strtotime($jam_selesai) <= strtotime($jam_mulai)) {
            return 'Jam selesai harus lebih besar dari jam mulai.';
        }

        // 1. Cek bentrok pengajar pada hari dan jam yang sama di kelas mana pun
        if ($guru_id) {
            $sqlGuru = "SELECT j.*, k.nama_kelas, m.nama_mapel 
                        FROM jadwal_mengajar j 
                        JOIN kelas k ON j.kelas_id = k.id 
                        JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                        WHERE j.guru_id = :guru_id 
                          AND j.hari = :hari 
                          AND j.tahun_ajaran_id = :tahun_ajaran_id 
                          AND (:jam_mulai < j.jam_selesai AND :jam_selesai > j.jam_mulai)";
            if ($exclude_id) {
                $sqlGuru .= " AND j.id != :exclude_id";
            }
            $this->db->query($sqlGuru);
            $this->db->bind(':guru_id', $guru_id);
            $this->db->bind(':hari', $hari);
            $this->db->bind(':tahun_ajaran_id', $tahun_ajaran_id);
            $this->db->bind(':jam_mulai', $jam_mulai);
            $this->db->bind(':jam_selesai', $jam_selesai);
            if ($exclude_id) $this->db->bind(':exclude_id', $exclude_id);
            $bentrokGuru = $this->db->single();

            if ($bentrokGuru) {
                return "Jadwal bentrok! Guru sudah memiliki jadwal mengajar di kelas {$bentrokGuru->nama_kelas} ({$bentrokGuru->nama_mapel}) pada {$bentrokGuru->jam_mulai} - {$bentrokGuru->jam_selesai}.";
            }
        } elseif ($wali_kelas_id) {
            $sqlWali = "SELECT j.*, k.nama_kelas, m.nama_mapel 
                        FROM jadwal_mengajar j 
                        JOIN kelas k ON j.kelas_id = k.id 
                        JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                        WHERE j.wali_kelas_id = :wali_kelas_id 
                          AND j.hari = :hari 
                          AND j.tahun_ajaran_id = :tahun_ajaran_id 
                          AND (:jam_mulai < j.jam_selesai AND :jam_selesai > j.jam_mulai)";
            if ($exclude_id) {
                $sqlWali .= " AND j.id != :exclude_id";
            }
            $this->db->query($sqlWali);
            $this->db->bind(':wali_kelas_id', $wali_kelas_id);
            $this->db->bind(':hari', $hari);
            $this->db->bind(':tahun_ajaran_id', $tahun_ajaran_id);
            $this->db->bind(':jam_mulai', $jam_mulai);
            $this->db->bind(':jam_selesai', $jam_selesai);
            if ($exclude_id) $this->db->bind(':exclude_id', $exclude_id);
            $bentrokWali = $this->db->single();

            if ($bentrokWali) {
                return "Jadwal bentrok! Wali kelas sudah memiliki jadwal mengajar di kelas {$bentrokWali->nama_kelas} ({$bentrokWali->nama_mapel}) pada {$bentrokWali->jam_mulai} - {$bentrokWali->jam_selesai}.";
            }
        }

        // 2. Cek bentrok kelas pada hari dan jam yang sama
        $sqlKelas = "SELECT j.*, m.nama_mapel, COALESCE(g.nama_lengkap, wkd.nama_lengkap) as nama_pengajar 
                     FROM jadwal_mengajar j 
                     JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                     LEFT JOIN guru g ON j.guru_id = g.id 
                     LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id 
                     WHERE j.kelas_id = :kelas_id 
                       AND j.hari = :hari 
                       AND j.tahun_ajaran_id = :tahun_ajaran_id 
                       AND (:jam_mulai < j.jam_selesai AND :jam_selesai > j.jam_mulai)";
        if ($exclude_id) {
            $sqlKelas .= " AND j.id != :exclude_id";
        }
        $this->db->query($sqlKelas);
        $this->db->bind(':kelas_id', $kelas_id);
        $this->db->bind(':hari', $hari);
        $this->db->bind(':tahun_ajaran_id', $tahun_ajaran_id);
        $this->db->bind(':jam_mulai', $jam_mulai);
        $this->db->bind(':jam_selesai', $jam_selesai);
        if ($exclude_id) $this->db->bind(':exclude_id', $exclude_id);
        $bentrokKelas = $this->db->single();

        if ($bentrokKelas) {
            return "Jadwal bentrok! Kelas tersebut sudah memiliki jadwal {$bentrokKelas->nama_mapel} ({$bentrokKelas->nama_pengajar}) pada {$bentrokKelas->jam_mulai} - {$bentrokKelas->jam_selesai}.";
        }

        return null; // Tidak ada bentrok
    }

    public function tambahJadwal($data) {
        [$guru_id, $wali_kelas_id] = $this->parsePengajarInput($data);

        // Validasi bentrok
        $err = $this->cekBentrokJadwal(
            $data['hari'], 
            $data['jam_mulai'], 
            $data['jam_selesai'], 
            $guru_id, 
            $wali_kelas_id, 
            $data['kelas_id'], 
            $data['tahun_ajaran_id']
        );
        if ($err) {
            return $err; // return error message
        }

        $this->db->query("INSERT INTO jadwal_mengajar (guru_id, wali_kelas_id, kelas_id, mata_pelajaran_id, hari, jam_mulai, jam_selesai, ruang, tahun_ajaran_id) 
                          VALUES (:guru_id, :wali_kelas_id, :kelas_id, :mata_pelajaran_id, :hari, :jam_mulai, :jam_selesai, :ruang, :tahun_ajaran_id)");
        
        $this->db->bind(':guru_id', $guru_id);
        $this->db->bind(':wali_kelas_id', $wali_kelas_id);
        $this->db->bind(':kelas_id', $data['kelas_id']);
        $this->db->bind(':mata_pelajaran_id', $data['mata_pelajaran_id']);
        $this->db->bind(':hari', $data['hari']);
        $this->db->bind(':jam_mulai', $data['jam_mulai']);
        $this->db->bind(':jam_selesai', $data['jam_selesai']);
        $this->db->bind(':ruang', $data['ruang'] ?? null);
        $this->db->bind(':tahun_ajaran_id', $data['tahun_ajaran_id']);
        
        return $this->db->execute() ? true : false;
    }

    public function updateJadwal($id, $data) {
        [$guru_id, $wali_kelas_id] = $this->parsePengajarInput($data);

        // Validasi bentrok
        $err = $this->cekBentrokJadwal(
            $data['hari'], 
            $data['jam_mulai'], 
            $data['jam_selesai'], 
            $guru_id, 
            $wali_kelas_id, 
            $data['kelas_id'], 
            $data['tahun_ajaran_id'],
            $id
        );
        if ($err) {
            return $err; // return error message
        }

        $this->db->query("UPDATE jadwal_mengajar SET 
                          guru_id = :guru_id, 
                          wali_kelas_id = :wali_kelas_id, 
                          kelas_id = :kelas_id, 
                          mata_pelajaran_id = :mata_pelajaran_id, 
                          hari = :hari, 
                          jam_mulai = :jam_mulai, 
                          jam_selesai = :jam_selesai, 
                          ruang = :ruang,
                          tahun_ajaran_id = :tahun_ajaran_id
                          WHERE id = :id");
        
        $this->db->bind(':guru_id', $guru_id);
        $this->db->bind(':wali_kelas_id', $wali_kelas_id);
        $this->db->bind(':kelas_id', $data['kelas_id']);
        $this->db->bind(':mata_pelajaran_id', $data['mata_pelajaran_id']);
        $this->db->bind(':hari', $data['hari']);
        $this->db->bind(':jam_mulai', $data['jam_mulai']);
        $this->db->bind(':jam_selesai', $data['jam_selesai']);
        $this->db->bind(':ruang', $data['ruang'] ?? null);
        $this->db->bind(':tahun_ajaran_id', $data['tahun_ajaran_id']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute() ? true : false;
    }

    public function hapusJadwal($id) {
        $this->db->query("DELETE FROM jadwal_mengajar WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getJadwalGuru($guru_id, $hari = null) {
        $query = "SELECT j.*, k.nama_kelas, j2.nama_jurusan, m.nama_mapel, t.tahun, t.semester 
                  FROM jadwal_mengajar j 
                  JOIN kelas k ON j.kelas_id = k.id 
                  LEFT JOIN jurusan j2 ON k.jurusan_id = j2.id 
                  JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                  JOIN tahun_ajaran t ON j.tahun_ajaran_id = t.id 
                  WHERE (j.guru_id = :guru_id OR j.wali_kelas_id IN (
                      SELECT wkd.id FROM wali_kelas_data wkd 
                      JOIN guru g ON g.user_id = wkd.user_id 
                      WHERE g.id = :guru_id2
                  )) AND t.is_active = 1";
                  
        if ($hari) {
            $query .= " AND j.hari = :hari";
        }
        
        $query .= " ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat','sabtu'), j.jam_mulai ASC";
        
        $this->db->query($query);
        $this->db->bind(':guru_id', $guru_id);
        $this->db->bind(':guru_id2', $guru_id);
        if ($hari) {
            $this->db->bind(':hari', $hari);
        }
        
        return $this->db->resultSet();
    }

    public function getJadwalWaliKelas($wali_kelas_id, $hari = null) {
        $query = "SELECT j.*, k.nama_kelas, j2.nama_jurusan, m.nama_mapel, t.tahun, t.semester 
                  FROM jadwal_mengajar j 
                  JOIN kelas k ON j.kelas_id = k.id 
                  LEFT JOIN jurusan j2 ON k.jurusan_id = j2.id 
                  JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                  JOIN tahun_ajaran t ON j.tahun_ajaran_id = t.id 
                  WHERE (j.wali_kelas_id = :wali_kelas_id OR j.guru_id IN (
                      SELECT g.id FROM guru g 
                      JOIN wali_kelas_data wkd ON g.user_id = wkd.user_id 
                      WHERE wkd.id = :wali_kelas_id2
                  )) AND t.is_active = 1";
                  
        if ($hari) {
            $query .= " AND j.hari = :hari";
        }
        
        $query .= " ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat','sabtu'), j.jam_mulai ASC";
        
        $this->db->query($query);
        $this->db->bind(':wali_kelas_id', $wali_kelas_id);
        $this->db->bind(':wali_kelas_id2', $wali_kelas_id);
        if ($hari) {
            $this->db->bind(':hari', $hari);
        }
        
        return $this->db->resultSet();
    }

    public function getJadwalSiswa($kelas_id, $hari = null) {
        $query = "SELECT j.*, 
                         COALESCE(g.nama_lengkap, wkd.nama_lengkap) as nama_guru, 
                         m.nama_mapel, t.tahun, t.semester 
                  FROM jadwal_mengajar j 
                  LEFT JOIN guru g ON j.guru_id = g.id 
                  LEFT JOIN wali_kelas_data wkd ON j.wali_kelas_id = wkd.id 
                  JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
                  JOIN tahun_ajaran t ON j.tahun_ajaran_id = t.id 
                  WHERE j.kelas_id = :kelas_id AND t.is_active = 1";
                  
        if ($hari) {
            $query .= " AND j.hari = :hari";
        }
        
        $query .= " ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat','sabtu'), j.jam_mulai ASC";
        
        $this->db->query($query);
        $this->db->bind(':kelas_id', $kelas_id);
        if ($hari) {
            $this->db->bind(':hari', $hari);
        }
        
        return $this->db->resultSet();
    }
}
