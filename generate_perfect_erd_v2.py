import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Rectangle
import numpy as np

def generate_perfect_erd(output_path):
    fig, ax = plt.subplots(figsize=(30, 22), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    BG_COLOR = "#F8FAFC"
    DARK_NAVY = "#0F172A"
    ACCENT_BLUE = "#0284C7"
    DARK_BLUE = "#1E3A8A"
    SLATE_BORDER = "#94A3B8"
    LIGHT_BORDER = "#CBD5E1"
    LINE_BLUE = "#0284C7"

    fig.patch.set_facecolor(BG_COLOR)
    ax.set_facecolor(BG_COLOR)

    # 1. HEADER BANNER
    banner = FancyBboxPatch((2, 92.8), 96, 5.8, boxstyle="round,pad=0.2,rounding_size=0.6",
                            facecolor=DARK_BLUE, edgecolor=ACCENT_BLUE, linewidth=2.5, zorder=2)
    ax.add_patch(banner)
    ax.text(50, 96.6, "ENTITY RELATIONSHIP DIAGRAM (ERD) TERPADU BASIS DATA 20 TABEL",
            fontsize=18, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 94.7, "Struktur Relasional 3NF Database `db_absensi_smk` • Sistem Presensi Digital SMK NU Wargabinangun",
            fontsize=11, color='#BAE6FD', ha='center', va='center', zorder=3)
    ax.text(50, 93.3, "Standar Relasi Crow's Foot • Perutean Jalur Orthogonal Murni (Bebas Silang Garis Diagonal)",
            fontsize=9, fontstyle='italic', color='#E2E8F0', ha='center', va='center', zorder=3)

    # Helper: Draw ERD Entity Table
    def draw_entity(cx, cy, table_name, pk_list, fk_list, attr_list, w=15.0, header_color="#0284C7"):
        row_h = 0.44
        total_rows = len(pk_list) + len(fk_list) + len(attr_list)
        hdr_h = 1.9
        total_h = hdr_h + (total_rows * row_h) + 0.5

        # Card outer border
        card = FancyBboxPatch((cx - w/2, cy - total_h/2), w, total_h, boxstyle="round,pad=0.1,rounding_size=0.25",
                              facecolor="#FFFFFF", edgecolor="#94A3B8", linewidth=1.3, zorder=3)
        ax.add_patch(card)

        # Header box
        hdr = FancyBboxPatch((cx - w/2, cy + total_h/2 - hdr_h), w, hdr_h, boxstyle="round,pad=0.1,rounding_size=0.25",
                             facecolor=header_color, edgecolor="#94A3B8", linewidth=1.0, zorder=4)
        ax.add_patch(hdr)
        ax.text(cx, cy + total_h/2 - hdr_h/2, table_name, fontsize=8.0, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=5)

        curr_y = cy + total_h/2 - hdr_h - 0.35

        # Primary Keys (PK)
        for pk in pk_list:
            ax.text(cx - w/2 + 0.4, curr_y, "PK", fontsize=5.8, fontweight='bold', color='#DC2626', va='center', zorder=5)
            ax.text(cx - w/2 + 1.2, curr_y, pk, fontsize=6.6, fontweight='bold', color='#0F172A', va='center', zorder=5)
            curr_y -= row_h

        # Foreign Keys (FK)
        for fk in fk_list:
            ax.text(cx - w/2 + 0.4, curr_y, "FK", fontsize=5.8, fontweight='bold', color='#2563EB', va='center', zorder=5)
            ax.text(cx - w/2 + 1.2, curr_y, fk, fontsize=6.5, color='#1E40AF', va='center', zorder=5)
            curr_y -= row_h

        # Normal Attributes
        for attr in attr_list:
            ax.text(cx - w/2 + 1.2, curr_y, attr, fontsize=6.4, color='#334155', va='center', zorder=5)
            curr_y -= row_h

        return {
            'cx': cx, 'cy': cy, 'w': w, 'h': total_h,
            'left': cx - w/2, 'right': cx + w/2,
            'top': cy + total_h/2, 'bottom': cy - total_h/2
        }

    # 2. INSTANTIATE 20 TABLES IN 4 BALANCED LOGICAL COLUMNS
    # COLUMN 1: Kredensial, Autentikasi & Struktur Lembaga (x = 12.0)
    t_users = draw_entity(12.0, 81.0, "users",
                          ["id: INT (AUTO)"],
                          ["role_id: INT"],
                          ["username: VARCHAR", "password: VARCHAR", "status: ENUM"],
                          w=14.5, header_color="#1E3A8A")

    t_roles = draw_entity(12.0, 64.5, "roles",
                          ["id: INT (AUTO)"],
                          [],
                          ["name: VARCHAR", "display_name: VARCHAR"],
                          w=14.5, header_color="#1E3A8A")

    t_ta = draw_entity(12.0, 49.5, "tahun_ajaran",
                       ["id: INT (AUTO)"],
                       [],
                       ["tahun: VARCHAR", "semester: ENUM", "is_active: TINYINT"],
                       w=14.5, header_color="#0369A1")

    t_jurusan = draw_entity(12.0, 33.5, "jurusan",
                            ["id: INT (AUTO)"],
                            [],
                            ["kode_jurusan: VARCHAR", "nama_jurusan: VARCHAR"],
                            w=14.5, header_color="#0369A1")

    t_kelas = draw_entity(12.0, 16.5, "kelas",
                          ["id: INT (AUTO)"],
                          ["jurusan_id: INT", "tahun_ajaran_id: INT"],
                          ["nama_kelas: VARCHAR", "tingkat: ENUM"],
                          w=14.5, header_color="#0369A1")

    # COLUMN 2: Tenaga Pendidik & Akademik (x = 37.0)
    t_guru = draw_entity(37.0, 80.0, "guru",
                         ["id: INT (AUTO)"],
                         ["user_id: INT"],
                         ["nip: VARCHAR", "nama_lengkap: VARCHAR", "barcode: VARCHAR"],
                         w=15.0, header_color="#0284C7")

    t_wkd = draw_entity(37.0, 64.0, "wali_kelas_data",
                        ["id: INT (AUTO)"],
                        ["guru_id: INT"],
                        ["tahun_ajaran_id: INT", "status: ENUM"],
                        w=15.0, header_color="#0284C7")

    t_wk = draw_entity(37.0, 48.0, "wali_kelas",
                       ["id: INT (AUTO)"],
                       ["wali_kelas_data_id: INT", "kelas_id: INT"],
                       ["tahun_ajaran_id: INT", "status_aktif: TINYINT"],
                       w=15.0, header_color="#0284C7")

    t_mapel = draw_entity(37.0, 31.5, "mata_pelajaran",
                          ["id: INT (AUTO)"],
                          ["guru_id: INT", "wali_kelas_id: INT"],
                          ["kode_mapel: VARCHAR", "nama_mapel: VARCHAR"],
                          w=15.0, header_color="#0284C7")

    t_jadwal = draw_entity(37.0, 14.5, "jadwal_mengajar",
                           ["id: INT (AUTO)"],
                           ["guru_id: INT", "kelas_id: INT", "mapel_id: INT"],
                           ["hari: ENUM", "jam_mulai: TIME", "jam_selesai: TIME"],
                           w=15.0, header_color="#0284C7")

    # COLUMN 3: Peserta Didik, Kredensial Barcode & Supervisi (x = 63.0)
    t_siswa = draw_entity(63.0, 80.0, "siswa",
                          ["id: INT (AUTO)"],
                          ["user_id: INT", "kelas_id: INT"],
                          ["nis: VARCHAR", "nisn: VARCHAR", "barcode: VARCHAR", "nama: VARCHAR"],
                          w=15.5, header_color="#0284C7")

    t_bar_g = draw_entity(63.0, 64.0, "barcode_guru",
                          ["id: INT (AUTO)"],
                          ["guru_id: INT"],
                          ["code: VARCHAR", "is_active: TINYINT"],
                          w=15.5, header_color="#D97706")

    t_rfid = draw_entity(63.0, 50.5, "rfid_cards",
                         ["id: INT (AUTO)"],
                         ["siswa_id: INT"],
                         ["uid: VARCHAR", "is_active: TINYINT"],
                         w=15.5, header_color="#D97706")

    t_cat_g = draw_entity(63.0, 34.0, "catatan_guru",
                          ["id: INT (AUTO)"],
                          ["guru_id: INT", "kepala_sekolah_id: INT"],
                          ["tanggal: DATE", "judul: VARCHAR", "isi: TEXT", "status_baca: ENUM"],
                          w=15.5, header_color="#64748B")

    t_cat_p = draw_entity(63.0, 15.5, "catatan_pembinaan",
                          ["id: INT (AUTO)"],
                          ["siswa_id: INT", "kelas_id: INT", "user_id: INT"],
                          ["kategori: ENUM", "isi_catatan: TEXT", "status_baca: TINYINT"],
                          w=15.5, header_color="#64748B")

    # COLUMN 4: Transaksi Presensi & Sistem (x = 88.0)
    t_abs_g = draw_entity(88.0, 80.0, "absensi_guru",
                          ["id: INT (AUTO)"],
                          ["guru_id: INT"],
                          ["tanggal: DATE", "jam_masuk: TIME", "jam_pulang: TIME", "status: ENUM"],
                          w=15.5, header_color="#10B981")

    t_abs_s = draw_entity(88.0, 63.5, "absensi_siswa",
                          ["id: INT (AUTO)"],
                          ["siswa_id: INT"],
                          ["tanggal: DATE", "jam_masuk: TIME", "jam_pulang: TIME", "status: ENUM"],
                          w=15.5, header_color="#10B981")

    t_abs_w = draw_entity(88.0, 47.0, "absensi_wali_kelas",
                          ["id: INT (AUTO)"],
                          ["wali_kelas_id: INT"],
                          ["tanggal: DATE", "jam_masuk: TIME", "jam_pulang: TIME", "status: ENUM"],
                          w=15.5, header_color="#10B981")

    t_izin = draw_entity(88.0, 29.5, "pengajuan_izin",
                         ["id: INT (AUTO)"],
                         ["siswa_id: INT", "guru_id: INT"],
                         ["tipe_pemohon: ENUM", "jenis: ENUM", "bukti_foto: VARCHAR", "status: ENUM"],
                         w=15.5, header_color="#D97706")

    t_cfg = draw_entity(88.0, 14.5, "pengaturan_sistem",
                        ["id: INT (AUTO)"],
                        [],
                        ["setting_key: VARCHAR", "setting_value: TEXT", "created_at: TIMESTAMP"],
                        w=15.5, header_color="#475569")

    # 3. DRAW CLEAN ORTHOGONAL (MANHATTAN) RELATION LINES
    # Zero diagonals! Lines travel horizontally and step vertically within clear gutters between columns.
    # Gutter 1-2: x in [19.5, 29.0]
    # Gutter 2-3: x in [44.8, 55.0]
    # Gutter 3-4: x in [71.0, 80.0]

    def draw_orthogonal_rel(points, label="", color="#0284C7", lw=1.3):
        xs = [p[0] for p in points]
        ys = [p[1] for p in points]
        ax.plot(xs, ys, color=color, lw=lw, linestyle="-", zorder=2)
        # Draw crow foot / arrow at destination (last point)
        p_prev = points[-2]
        p_last = points[-1]
        ax.plot(p_last[0], p_last[1], marker='o', markersize=3.5, color=color, zorder=3)
        if label:
            mid_idx = len(points) // 2
            mx = (points[mid_idx-1][0] + points[mid_idx][0]) / 2
            my = (points[mid_idx-1][1] + points[mid_idx][1]) / 2 + 0.35
            ax.text(mx, my, label, fontsize=6.2, fontweight='bold', color=color, ha='center', va='center',
                    bbox=dict(boxstyle="round,pad=0.15", fc="#FFFFFF", ec=color, lw=0.8, alpha=0.95), zorder=4)

    # 1. users -> roles (Straight Vertical in Col 1)
    draw_orthogonal_rel([(t_users['cx'], t_users['bottom']), (t_roles['cx'], t_roles['top'])], label="N:1")

    # 2. jurusan -> kelas (Straight Vertical in Col 1)
    draw_orthogonal_rel([(t_jurusan['cx'], t_jurusan['bottom']), (t_kelas['cx'], t_kelas['top'])], label="1:N")

    # 3. users -> guru (Horizontal in Row 1)
    draw_orthogonal_rel([(t_users['right'], t_users['cy']), (t_guru['left'], t_guru['cy'])], label="1:1")

    # 4. users -> siswa (Top Gutter Orthogonal Step)
    draw_orthogonal_rel([(t_users['right'], t_users['cy'] + 2.5),
                         (50.0, t_users['cy'] + 2.5),
                         (50.0, t_siswa['cy'] + 2.5),
                         (t_siswa['left'], t_siswa['cy'] + 2.5)], label="1:1 (Auth)")

    # 5. guru -> wali_kelas_data (Vertical in Col 2)
    draw_orthogonal_rel([(t_guru['cx'], t_guru['bottom']), (t_wkd['cx'], t_wkd['top'])], label="1:1")

    # 6. wali_kelas_data -> wali_kelas (Vertical in Col 2)
    draw_orthogonal_rel([(t_wkd['cx'], t_wkd['bottom']), (t_wk['cx'], t_wk['top'])], label="1:1")

    # 7. kelas -> siswa (Orthogonal Step via Gutter 1-2 & Gutter 2-3)
    # Exits kelas right, runs along lower corridor, enters siswa bottom
    draw_orthogonal_rel([(t_kelas['right'], t_kelas['cy']),
                         (24.0, t_kelas['cy']),
                         (24.0, 72.0),
                         (t_siswa['left'], 72.0)], label="1:N (Anggota)")

    # 8. guru -> barcode_guru (Horizontal in Row 1-2)
    draw_orthogonal_rel([(t_guru['right'], 65.0), (t_bar_g['left'], 65.0)], label="1:1")

    # 9. guru -> absensi_guru (Top Gutter straight across to Col 4)
    draw_orthogonal_rel([(t_guru['right'], t_guru['cy']),
                         (75.0, t_guru['cy']),
                         (t_abs_g['left'], t_abs_g['cy'])], label="1:N")

    # 10. siswa -> rfid_cards (Straight Vertical in Col 3)
    draw_orthogonal_rel([(t_siswa['cx'], t_siswa['bottom']), (t_rfid['cx'], t_rfid['top'])], label="1:1")

    # 11. siswa -> absensi_siswa (Horizontal to Col 4)
    draw_orthogonal_rel([(t_siswa['right'], 64.0), (t_abs_s['left'], 64.0)], label="1:N")

    # 12. wali_kelas -> absensi_wali_kelas (Horizontal Step via Gutter 2-3 & Gutter 3-4)
    draw_orthogonal_rel([(t_wk['right'], t_wk['cy']),
                         (50.0, t_wk['cy']),
                         (50.0, 47.0),
                         (76.0, 47.0),
                         (t_abs_w['left'], t_abs_w['cy'])], label="Dual-Sync")

    # 13. siswa -> pengajuan_izin (Orthogonal Step via Gutter 3-4)
    draw_orthogonal_rel([(t_siswa['right'], 78.0),
                         (76.0, 78.0),
                         (76.0, 31.0),
                         (t_izin['left'], 31.0)], label="1:N (Izin Siswa)")

    # 14. guru -> pengajuan_izin (Orthogonal Step via Gutter 2-3 & Gutter 3-4)
    draw_orthogonal_rel([(t_guru['right'], 76.0),
                         (74.0, 76.0),
                         (74.0, 28.0),
                         (t_izin['left'], 28.0)], label="1:N (Izin Guru)", color="#D97706")

    # 15. guru -> catatan_guru (Horizontal Step in Row 3)
    draw_orthogonal_rel([(t_guru['right'], 34.0), (t_cat_g['left'], 34.0)], label="1:N (Supervisi)")

    # 16. siswa -> catatan_pembinaan (Vertical Gutter Step in Col 3)
    draw_orthogonal_rel([(t_rfid['cx'], t_rfid['bottom']), (t_cat_p['cx'], t_cat_p['top'])], label="1:N (Konseling)")

    # 17. guru -> mata_pelajaran (Vertical in Col 2)
    draw_orthogonal_rel([(t_wk['cx'], t_wk['bottom']), (t_mapel['cx'], t_mapel['top'])], label="1:N")

    # 18. mapel -> jadwal_mengajar (Vertical in Col 2)
    draw_orthogonal_rel([(t_mapel['cx'], t_mapel['bottom']), (t_jadwal['cx'], t_jadwal['top'])], label="1:N")

    # 4. FOOTER VERIFIKASI RELASIONAL & KAMUS DATA
    ftr = FancyBboxPatch((2, 1.2), 96, 5.8, boxstyle="round,pad=0.2,rounding_size=0.5",
                         facecolor="#FFFFFF", edgecolor=LIGHT_BORDER, linewidth=1.3, zorder=3)
    ax.add_patch(ftr)

    ax.text(3.5, 5.5, "VERIFIKASI INTEGRITAS STRUKTUR DATABASE 20 TABEL (THIRD NORMAL FORM - 3NF):",
            fontsize=8.5, fontweight='bold', color=DARK_NAVY, va='center')

    db_groups = [
        ("Auth & Master Pengguna:", "users, roles (Enkripsi bcrypt, RBAC)"),
        ("Akademik & KBM:", "tahun_ajaran, jurusan, kelas, guru, wali_kelas_data, wali_kelas, mapel, jadwal"),
        ("Kesiswaan & Identifikasi:", "siswa, barcode_guru, rfid_cards (Dual-Token QR & RFID)"),
        ("Transaksi Presensi & Izin:", "absensi_siswa, absensi_guru, absensi_wali_kelas, pengajuan_izin (Dual-Sync)"),
        ("Supervisi & Sistem:", "catatan_guru, catatan_pembinaan, pengaturan_sistem (Tracking & Config)")
    ]

    gy = 4.2
    for title, desc in db_groups:
        ax.text(3.5, gy, title, fontsize=6.8, fontweight='bold', color=DARK_BLUE, va='center')
        ax.text(23.0, gy, desc, fontsize=6.4, color='#334155', va='center')
        gy -= 0.72

    plt.tight_layout()
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print("[SUCCESS] Perfect ERD Diagram saved to:", output_path)

if __name__ == "__main__":
    generate_perfect_erd(r"c:\xampp\htdocs\absen\ERD_Database_Absensi_SMK.png")
