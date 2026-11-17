<?php
/**
 * Catatan Guru Model
 * Mengelola catatan pembinaan, supervisi, dan evaluasi guru dari Kepala Sekolah
 */
class CatatanGuruModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Mengambil semua catatan guru dengan filter opsional
     */
    public function getAllCatatan($filters = []) {
        $query = "SELECT cg.*, g.nama_lengkap as nama_guru, g.nip as nip_guru, g.email as email_guru,
                         u.username as username_kepsek
                  FROM catatan_guru cg
                  JOIN guru g ON cg.guru_id = g.id
                  JOIN users u ON cg.kepala_sekolah_id = u.id
                  WHERE 1=1";

        if (!empty($filters['guru_id'])) {
            $query .= " AND cg.guru_id = :guru_id";
        }
        if (!empty($filters['kategori'])) {
            $query .= " AND cg.kategori = :kategori";
        }
        if (!empty($filters['urgensi'])) {
            $query .= " AND cg.urgensi = :urgensi";
        }
        if (!empty($filters['status_baca'])) {
            $query .= " AND cg.status_baca = :status_baca";
        }
        if (!empty($filters['tanggal'])) {
            $query .= " AND cg.tanggal = :tanggal";
        }
        if (!empty($filters['search'])) {
            $query .= " AND (g.nama_lengkap LIKE :search OR g.nip LIKE :search OR cg.isi_catatan LIKE :search OR cg.judul LIKE :search)";
        }

        $query .= " ORDER BY cg.tanggal DESC, cg.id DESC";

        $this->db->query($query);

        if (!empty($filters['guru_id'])) $this->db->bind(':guru_id', $filters['guru_id']);
        if (!empty($filters['kategori'])) $this->db->bind(':kategori', $filters['kategori']);
        if (!empty($filters['urgensi'])) $this->db->bind(':urgensi', $filters['urgensi']);
        if (!empty($filters['status_baca'])) $this->db->bind(':status_baca', $filters['status_baca']);
        if (!empty($filters['tanggal'])) $this->db->bind(':tanggal', $filters['tanggal']);
        if (!empty($filters['search'])) $this->db->bind(':search', '%' . $filters['search'] . '%');

        return $this->db->resultSet();
    }

    /**
     * Mengambil satu catatan berdasarkan ID
     */
    public function getCatatanById($id) {
        $this->db->query("SELECT cg.*, g.nama_lengkap as nama_guru, g.nip as nip_guru, u.username as username_kepsek
                          FROM catatan_guru cg
                          JOIN guru g ON cg.guru_id = g.id
                          JOIN users u ON cg.kepala_sekolah_id = u.id
                          WHERE cg.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Mengambil catatan khusus untuk guru tertentu (berdasarkan guru_id)
     */
    public function getCatatanByGuruId($guru_id) {
        $this->db->query("SELECT cg.*, u.username as username_kepsek
                          FROM catatan_guru cg
                          JOIN users u ON cg.kepala_sekolah_id = u.id
                          WHERE cg.guru_id = :guru_id
                          ORDER BY cg.tanggal DESC, cg.id DESC");
        $this->db->bind(':guru_id', $guru_id);
        return $this->db->resultSet();
    }

    /**
     * Menambah catatan baru
     */
    public function tambahCatatan($data) {
        $this->db->query("INSERT INTO catatan_guru (guru_id, kepala_sekolah_id, tanggal, judul, kategori, urgensi, isi_catatan, status_baca)
                          VALUES (:guru_id, :kepala_sekolah_id, :tanggal, :judul, :kategori, :urgensi, :isi_catatan, :status_baca)");
        $this->db->bind(':guru_id', $data['guru_id']);
        $this->db->bind(':kepala_sekolah_id', $data['kepala_sekolah_id']);
        $this->db->bind(':tanggal', $data['tanggal']);
        $this->db->bind(':judul', $data['judul'] ?? null);
        $this->db->bind(':kategori', $data['kategori']);
        $this->db->bind(':urgensi', $data['urgensi'] ?? 'Biasa');
        $this->db->bind(':isi_catatan', $data['isi_catatan']);
        $this->db->bind(':status_baca', $data['status_baca'] ?? 'belum');
        return $this->db->execute();
    }

    /**
     * Memperbarui catatan
     */
    public function updateCatatan($id, $data) {
        $this->db->query("UPDATE catatan_guru 
                          SET guru_id = :guru_id, 
                              tanggal = :tanggal, 
                              judul = :judul, 
                              kategori = :kategori, 
                              urgensi = :urgensi, 
                              isi_catatan = :isi_catatan
                          WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':guru_id', $data['guru_id']);
        $this->db->bind(':tanggal', $data['tanggal']);
        $this->db->bind(':judul', $data['judul'] ?? null);
        $this->db->bind(':kategori', $data['kategori']);
        $this->db->bind(':urgensi', $data['urgensi'] ?? 'Biasa');
        $this->db->bind(':isi_catatan', $data['isi_catatan']);
        return $this->db->execute();
    }

    /**
     * Menghapus catatan
     */
    public function hapusCatatan($id) {
        $this->db->query("DELETE FROM catatan_guru WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Menandai catatan sebagai sudah dibaca oleh guru
     */
    public function markAsRead($id, $guru_id) {
        $this->db->query("UPDATE catatan_guru SET status_baca = 'sudah' WHERE id = :id AND guru_id = :guru_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':guru_id', $guru_id);
        return $this->db->execute();
    }

    /**
     * Menghitung catatan yang belum dibaca untuk guru tertentu
     */
    public function countUnreadByGuruId($guru_id) {
        $this->db->query("SELECT COUNT(*) as total FROM catatan_guru WHERE guru_id = :guru_id AND status_baca = 'belum'");
        $this->db->bind(':guru_id', $guru_id);
        $res = $this->db->single();
        return $res ? (int)$res->total : 0;
    }

    /**
     * Mengambil statistik ringkasan catatan untuk Kepala Sekolah
     */
    public function getStats() {
        $stats = [
            'total' => 0,
            'sudah_dibaca' => 0,
            'belum_dibaca' => 0,
            'kategori' => [
                'Kedisiplinan' => 0,
                'Kinerja Mengajar' => 0,
                'Kehadiran' => 0,
                'Apresiasi' => 0,
                'Evaluasi Pembelajaran' => 0,
                'Umum' => 0
            ]
        ];

        $this->db->query("SELECT COUNT(*) as total, 
                                 SUM(CASE WHEN status_baca = 'sudah' THEN 1 ELSE 0 END) as sudah,
                                 SUM(CASE WHEN status_baca = 'belum' THEN 1 ELSE 0 END) as belum
                          FROM catatan_guru");
        $res = $this->db->single();
        if ($res) {
            $stats['total'] = (int)$res->total;
            $stats['sudah_dibaca'] = (int)$res->sudah;
            $stats['belum_dibaca'] = (int)$res->belum;
        }

        $this->db->query("SELECT kategori, COUNT(*) as jml FROM catatan_guru GROUP BY kategori");
        $cats = $this->db->resultSet();
        foreach ($cats as $c) {
            if (isset($stats['kategori'][$c->kategori])) {
                $stats['kategori'][$c->kategori] = (int)$c->jml;
            }
        }

        return $stats;
    }
}
