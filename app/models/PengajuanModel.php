<?php
class PengajuanModel extends BaseModel {

    /**
     * Buat pengajuan baru untuk siswa
     */
    public function createPengajuan($data) {
        $this->db->query("INSERT INTO pengajuan_izin (siswa_id, tipe_pemohon, jenis, tanggal, alasan, bukti_foto, status)
                          VALUES (:siswa_id, 'siswa', :jenis, :tanggal, :alasan, :bukti_foto, 'Disetujui')");
        $this->db->bind(':siswa_id',   $data['siswa_id']);
        $this->db->bind(':jenis',      $data['jenis']);
        $this->db->bind(':tanggal',    $data['tanggal']);
        $this->db->bind(':alasan',     $data['alasan']);
        $this->db->bind(':bukti_foto', $data['bukti_foto']);
        return $this->db->execute();
    }

    /**
     * Buat pengajuan baru untuk guru
     */
    public function createPengajuanGuru($data) {
        $this->db->query("INSERT INTO pengajuan_izin (guru_id, tipe_pemohon, jenis, tanggal, alasan, bukti_foto, status)
                          VALUES (:guru_id, 'guru', :jenis, :tanggal, :alasan, :bukti_foto, 'Disetujui')");
        $this->db->bind(':guru_id',    $data['guru_id']);
        $this->db->bind(':jenis',      $data['jenis']);
        $this->db->bind(':tanggal',    $data['tanggal']);
        $this->db->bind(':alasan',     $data['alasan']);
        $this->db->bind(':bukti_foto', $data['bukti_foto']);
        return $this->db->execute();
    }

    /**
     * Cek apakah siswa sudah melakukan pengajuan izin/sakit pada tanggal ini atau pada hari ini
     */
    public function cekSudahPengajuanSiswa($siswa_id, $tanggal) {
        $this->db->query("SELECT id FROM pengajuan_izin 
                          WHERE siswa_id = :siswa_id 
                            AND tipe_pemohon = 'siswa' 
                            AND (tanggal = :tanggal OR DATE(created_at) = CURDATE())
                          LIMIT 1");
        $this->db->bind(':siswa_id', $siswa_id);
        $this->db->bind(':tanggal',  $tanggal);
        return $this->db->single();
    }

    /**
     * Cek apakah guru sudah melakukan pengajuan izin/sakit pada tanggal ini atau pada hari ini
     */
    public function cekSudahPengajuanGuru($guru_id, $tanggal) {
        $this->db->query("SELECT id FROM pengajuan_izin 
                          WHERE guru_id = :guru_id 
                            AND tipe_pemohon = 'guru' 
                            AND (tanggal = :tanggal OR DATE(created_at) = CURDATE())
                          LIMIT 1");
        $this->db->bind(':guru_id', $guru_id);
        $this->db->bind(':tanggal',  $tanggal);
        return $this->db->single();
    }

    /**
     * Ambil semua pengajuan milik satu siswa
     */
    public function getPengajuanBySiswa($siswa_id, $limit = 20, $offset = 0) {
        $this->db->query("SELECT * FROM pengajuan_izin WHERE siswa_id = :siswa_id AND tipe_pemohon = 'siswa'
                          ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $this->db->bind(':siswa_id', $siswa_id);
        $this->db->bind(':limit',  (int)$limit,  PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Hitung total pengajuan milik satu siswa
     */
    public function countPengajuanBySiswa($siswa_id) {
        $this->db->query("SELECT COUNT(*) as total FROM pengajuan_izin WHERE siswa_id = :siswa_id AND tipe_pemohon = 'siswa'");
        $this->db->bind(':siswa_id', $siswa_id);
        $res = $this->db->single();
        return $res ? $res->total : 0;
    }

    /**
     * Ambil semua pengajuan milik satu guru
     */
    public function getPengajuanByGuru($guru_id, $limit = 20, $offset = 0) {
        $this->db->query("SELECT * FROM pengajuan_izin WHERE guru_id = :guru_id AND tipe_pemohon = 'guru'
                          ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $this->db->bind(':guru_id', $guru_id);
        $this->db->bind(':limit',  (int)$limit,  PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Hitung total pengajuan milik satu guru
     */
    public function countPengajuanByGuru($guru_id) {
        $this->db->query("SELECT COUNT(*) as total FROM pengajuan_izin WHERE guru_id = :guru_id AND tipe_pemohon = 'guru'");
        $this->db->bind(':guru_id', $guru_id);
        $res = $this->db->single();
        return $res ? $res->total : 0;
    }

    /**
     * Ambil satu pengajuan berdasar ID (bisa siswa atau guru)
     */
    public function getPengajuanById($id) {
        $this->db->query("SELECT p.*,
                                 COALESCE(s.nama_lengkap, g.nama_lengkap) as nama_lengkap,
                                 COALESCE(s.nis, g.nip) as nomor_identitas,
                                 COALESCE(k.nama_kelas, 'Guru') as nama_kelas
                          FROM pengajuan_izin p
                          LEFT JOIN siswa s ON p.siswa_id = s.id
                          LEFT JOIN kelas k ON s.kelas_id = k.id
                          LEFT JOIN guru g ON p.guru_id = g.id
                          WHERE p.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // ============================================================
    // ADMIN / WALI KELAS: list semua pengajuan dengan filter
    // ============================================================
    public function getAllPengajuan($filters = [], $limit = 20, $offset = 0, $count = false) {
        $where  = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "p.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['jenis'])) {
            $where[] = "p.jenis = :jenis";
            $params[':jenis'] = $filters['jenis'];
        }
        if (!empty($filters['tipe_pemohon'])) {
            $where[] = "p.tipe_pemohon = :tipe_pemohon";
            $params[':tipe_pemohon'] = $filters['tipe_pemohon'];
        }
        if (!empty($filters['kelas_id'])) {
            $where[] = "s.kelas_id = :kelas_id";
            $params[':kelas_id'] = $filters['kelas_id'];
        }
        if (!empty($filters['nama'])) {
            $where[] = "(s.nama_lengkap LIKE :nama OR g.nama_lengkap LIKE :nama2 OR s.nis LIKE :nama3 OR s.nisn LIKE :nama4 OR g.nip LIKE :nama5)";
            $params[':nama']  = "%" . $filters['nama'] . "%";
            $params[':nama2'] = "%" . $filters['nama'] . "%";
            $params[':nama3'] = "%" . $filters['nama'] . "%";
            $params[':nama4'] = "%" . $filters['nama'] . "%";
            $params[':nama5'] = "%" . $filters['nama'] . "%";
        }

        $sql = "SELECT p.*,
                       COALESCE(s.nama_lengkap, g.nama_lengkap) as nama_lengkap,
                       COALESCE(s.nis, g.nip) as nomor_identitas,
                       s.nis, s.nisn, g.nip,
                       COALESCE(k.nama_kelas, 'Guru Pengajar') as nama_kelas
                FROM pengajuan_izin p
                LEFT JOIN siswa s ON p.siswa_id = s.id
                LEFT JOIN kelas k ON s.kelas_id = k.id
                LEFT JOIN guru g ON p.guru_id = g.id";

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        if ($count) {
            $this->db->query("SELECT COUNT(*) as total FROM ($sql) as t");
            foreach ($params as $k => $v) { $this->db->bind($k, $v); }
            $res = $this->db->single();
            return $res ? $res->total : 0;
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        foreach ($params as $k => $v) { $this->db->bind($k, $v); }
        $this->db->bind(':limit',  (int)$limit,  PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Update status pengajuan (admin/wali kelas)
     */
    public function updateStatus($id, $status, $catatan = '') {
        $this->db->query("UPDATE pengajuan_izin SET status = :status, catatan_admin = :catatan WHERE id = :id");
        $this->db->bind(':status',  $status);
        $this->db->bind(':catatan', $catatan);
        $this->db->bind(':id',      $id);
        return $this->db->execute();
    }

    /**
     * Hapus pengajuan (hanya boleh jika masih Menunggu)
     */
    public function deletePengajuan($id) {
        $this->db->query("DELETE FROM pengajuan_izin WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
