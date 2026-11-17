import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch
import numpy as np

def generate_erd_diagram(output_path):
    fig, ax = plt.subplots(figsize=(26, 18), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    fig.patch.set_facecolor("#F8FAFC")
    ax.set_facecolor("#F8FAFC")

    # Header
    banner = FancyBboxPatch((2, 93), 96, 5.5, boxstyle="round,pad=0.2,rounding_size=0.5",
                            facecolor="#0F172A", edgecolor="#0284C7", linewidth=2.0)
    ax.add_patch(banner)
    ax.text(50, 96.2, "ENTITY RELATIONSHIP DIAGRAM (ERD) BASIS DATA 20 TABEL",
            fontsize=16, fontweight='bold', color='#FFFFFF', ha='center', va='center')
    ax.text(50, 94.2, "Basis Data `db_absensi_smk` • Struktur Relasional Normalisasi 3NF Sistem Presensi SMK NU Wargabinangun",
            fontsize=10, color='#BAE6FD', ha='center', va='center')

    # Draw Table Entity
    def draw_entity(x, y, table_name, pk_list, fk_list, normal_list, w=14.5, header_color="#0284C7"):
        h = 2.0 + (len(pk_list) + len(fk_list) + len(normal_list)) * 0.42 + 0.4
        box = FancyBboxPatch((x - w/2, y - h/2), w, h, boxstyle="round,pad=0.1,rounding_size=0.25",
                             facecolor="#FFFFFF", edgecolor="#94A3B8", linewidth=1.2, zorder=3)
        ax.add_patch(box)

        # Header
        hdr_h = 1.8
        hdr = FancyBboxPatch((x - w/2, y + h/2 - hdr_h), w, hdr_h, boxstyle="round,pad=0.1,rounding_size=0.25",
                             facecolor=header_color, edgecolor="#94A3B8", linewidth=1.0, zorder=4)
        ax.add_patch(hdr)
        ax.text(x, y + h/2 - hdr_h/2, table_name, fontsize=8.0, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=5)

        curr_y = y + h/2 - hdr_h - 0.35
        # PKs
        for pk in pk_list:
            ax.text(x - w/2 + 0.5, curr_y, "🔑 " + pk, fontsize=6.8, fontweight='bold', color='#B45309', va='center', zorder=5)
            curr_y -= 0.42
        # FKs
        for fk in fk_list:
            ax.text(x - w/2 + 0.5, curr_y, "🔗 " + fk, fontsize=6.8, color='#0284C7', va='center', zorder=5)
            curr_y -= 0.42
        # Normal
        for col in normal_list:
            ax.text(x - w/2 + 0.5, curr_y, "• " + col, fontsize=6.5, color='#475569', va='center', zorder=5)
            curr_y -= 0.42

        return {'cx': x, 'cy': y, 'w': w, 'h': h,
                'left': x - w/2, 'right': x + w/2, 'top': y + h/2, 'bottom': y - h/2}

    # 4 Columns of 5 Tables = 20 Tables Total!
    # Col 1: Akun & Rombel (x = 12.0)
    t_users = draw_entity(12.0, 83.0, "users", ["id: INT"], [], ["username: VARCHAR", "password: VARCHAR", "role: ENUM", "status: ENUM"])
    t_roles = draw_entity(12.0, 68.0, "roles", ["id: INT"], [], ["nama_role: VARCHAR"])
    t_ta = draw_entity(12.0, 55.0, "tahun_ajaran", ["id: INT"], [], ["tahun: VARCHAR", "semester: ENUM", "is_active: TINYINT"])
    t_jurusan = draw_entity(12.0, 42.0, "jurusan", ["id: INT"], [], ["kode_jurusan: VARCHAR", "nama_jurusan: VARCHAR"])
    t_kelas = draw_entity(12.0, 27.0, "kelas", ["id: INT"], ["jurusan_id: INT"], ["tingkat: ENUM", "nama_kelas: VARCHAR"])

    # Col 2: Pendidik & Mapel (x = 37.0)
    t_guru = draw_entity(37.0, 81.0, "guru", ["id: INT"], ["user_id: INT"], ["nip: VARCHAR", "nama_lengkap: VARCHAR", "barcode: VARCHAR"])
    t_wkd = draw_entity(37.0, 64.0, "wali_kelas_data", ["id: INT"], ["user_id: INT"], ["nip: VARCHAR", "nama_lengkap: VARCHAR", "barcode: VARCHAR"])
    t_wk = draw_entity(37.0, 48.0, "wali_kelas", ["id: INT"], ["wali_kelas_data_id: INT", "kelas_id: INT", "tahun_ajaran_id: INT"], [])
    t_mapel = draw_entity(37.0, 32.0, "mata_pelajaran", ["id: INT"], ["guru_id: INT", "wali_kelas_id: INT"], ["kode_mapel: VARCHAR", "nama_mapel: VARCHAR"])
    t_jadwal = draw_entity(37.0, 15.0, "jadwal_mengajar", ["id: INT"], ["guru_id: INT", "kelas_id: INT", "mata_pelajaran_id: INT"], ["hari: ENUM", "jam_mulai: TIME", "jam_selesai: TIME"])

    # Col 3: Siswa & Kredensial (x = 63.0)
    t_siswa = draw_entity(63.0, 81.0, "siswa", ["id: INT"], ["kelas_id: INT", "user_id: INT"], ["nis: VARCHAR", "nisn: VARCHAR", "barcode: VARCHAR", "nama_lengkap: VARCHAR"])
    t_barcode_g = draw_entity(63.0, 65.0, "barcode_guru", ["id: INT"], ["guru_id: INT"], ["code: VARCHAR", "is_active: TINYINT"])
    t_rfid = draw_entity(63.0, 52.0, "rfid_cards", ["id: INT"], ["siswa_id: INT"], ["uid: VARCHAR", "is_active: TINYINT"])
    t_catatan_g = draw_entity(63.0, 37.0, "catatan_guru", ["id: INT"], ["guru_id: INT", "kepala_sekolah_id: INT"], ["tanggal: DATE", "judul: VARCHAR", "isi_catatan: TEXT", "status_baca: ENUM"])
    t_catatan_p = draw_entity(63.0, 18.0, "catatan_pembinaan", ["id: INT"], ["siswa_id: INT", "kelas_id: INT", "pembuat_user_id: INT"], ["kategori: ENUM", "isi_catatan: TEXT", "status_baca: TINYINT"])

    # Col 4: Transaksi Presensi & Sistem (x = 88.0)
    t_abs_s = draw_entity(88.0, 81.0, "absensi_siswa", ["id: INT"], ["siswa_id: INT"], ["tanggal: DATE", "jam_masuk: TIME", "jam_pulang: TIME", "status: ENUM"])
    t_abs_g = draw_entity(88.0, 65.0, "absensi_guru", ["id: INT"], ["guru_id: INT"], ["tanggal: DATE", "jam_masuk: TIME", "jam_pulang: TIME", "status: ENUM"])
    t_abs_w = draw_entity(88.0, 49.0, "absensi_wali_kelas", ["id: INT"], ["wali_kelas_id: INT"], ["tanggal: DATE", "jam_masuk: TIME", "jam_pulang: TIME", "status: ENUM"])
    t_izin = draw_entity(88.0, 32.0, "pengajuan_izin", ["id: INT"], ["siswa_id: INT", "guru_id: INT"], ["tipe_pemohon: ENUM", "jenis: ENUM", "bukti_foto: VARCHAR", "status: ENUM"])
    t_cfg = draw_entity(88.0, 16.0, "pengaturan_sistem", ["id: INT"], [], ["setting_key: VARCHAR", "setting_value: TEXT", "created_at: TIMESTAMP"])

    # Draw Foreign Key Relational Lines cleanly between columns
    def draw_fk(p1, p2, label=""):
        ax.plot([p1[0], p2[0]], [p1[1], p2[1]], color="#0284C7", lw=1.2, linestyle="-", zorder=2)
        if label:
            mx, my = (p1[0] + p2[0])/2, (p1[1] + p2[1])/2 + 0.35
            ax.text(mx, my, label, fontsize=6.2, color="#0369A1", ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none"))

    draw_fk((t_users['right'], t_users['cy']), (t_guru['left'], t_guru['cy']), "1:1")
    draw_fk((t_jurusan['bottom'], t_jurusan['cx']), (t_kelas['top'], t_kelas['cx']), "1:N")
    draw_fk((t_kelas['right'], t_kelas['cy']), (t_siswa['left'], t_siswa['cy']), "1:N")
    draw_fk((t_guru['right'], t_guru['cy']), (t_abs_g['left'], t_abs_g['cy']), "1:N")
    draw_fk((t_siswa['right'], t_siswa['cy']), (t_abs_s['left'], t_abs_s['cy']), "1:N")
    draw_fk((t_siswa['right'], t_siswa['bottom']), (t_izin['left'], t_izin['top']), "1:N")
    draw_fk((t_guru['right'], t_guru['bottom']), (t_catatan_g['left'], t_catatan_g['top']), "1:N")
    draw_fk((t_wk['right'], t_wk['cy']), (t_abs_w['left'], t_abs_w['cy']), "Dual-Sync")

    plt.tight_layout()
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print("[SUCCESS] ERD Diagram saved to:", output_path)

if __name__ == "__main__":
    generate_erd_diagram(r"c:\xampp\htdocs\absen\ERD_Database_Absensi_SMK.png")
