import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Ellipse, PathPatch
import matplotlib.path as mpath
import numpy as np

def create_usecase_diagram(output_path):
    # Dimensions (inches) and DPI -> High Definition Canvas
    fig_w, fig_h = 28, 20
    dpi = 300
    fig, ax = plt.subplots(figsize=(fig_w, fig_h), dpi=dpi)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    # Color Palette - Professional Soft Blue & Slate
    BG_COLOR = "#F1F5F9"       # Slate 100
    PANEL_BG = "#FFFFFF"       # White
    PRIMARY_NAVY = "#0F172A"   # Slate 900
    ACCENT_BLUE = "#0284C7"    # Sky 600
    DARK_BLUE = "#1E3A8A"      # Blue 900
    BORDER_COLOR = "#94A3B8"   # Slate 400
    ACTOR_BG = "#E0F2FE"       # Sky 100
    ACTOR_BORDER = "#0369A1"   # Sky 700
    EXT_ACTOR_BG = "#FEF3C7"   # Amber 100
    EXT_ACTOR_BORDER = "#D97706"# Amber 600
    UC_BG = "#FFFFFF"
    UC_BORDER = "#2563EB"      # Blue 600
    INCLUDE_BORDER = "#0D9488" # Teal 600
    EXTEND_BORDER = "#D97706"  # Amber 600
    TAG_BG = "#F8FAFC"
    LINE_COLOR = "#64748B"     # Slate 500

    # Draw Background Canvas
    fig.patch.set_facecolor(BG_COLOR)
    ax.set_facecolor(BG_COLOR)

    # 1. HEADER BANNER
    banner = FancyBboxPatch((2, 91.5), 96, 7.2,
                            boxstyle="round,pad=0.2,rounding_size=0.8",
                            facecolor=DARK_BLUE, edgecolor=ACCENT_BLUE, linewidth=2.5, zorder=2)
    ax.add_patch(banner)

    ax.text(50, 96.2, "USE CASE DIAGRAM TERPADU SISTEM PRESENSI SMK NU WARGABINANGUN",
            fontsize=20, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 93.8, "Arsitektur Fungsional Menyeluruh Berdasarkan 20 Tabel Database `db_absensi_smk` & 5 Role Pengguna",
            fontsize=12, fontweight='medium', color='#BAE6FD', ha='center', va='center', zorder=3)
    ax.text(50, 92.4, "Oleh: Winarti  •  Framework: Native PHP, MySQL/MariaDB, Tailwind CSS, Telegram Bot API, NVIDIA NIM AI",
            fontsize=9.5, fontstyle='italic', color='#E2E8F0', ha='center', va='center', zorder=3)

    # 2. SYSTEM BOUNDARY (The System Box)
    sys_box = FancyBboxPatch((15, 7.5), 68, 82.5,
                             boxstyle="round,pad=0.3,rounding_size=1.0",
                             facecolor=PANEL_BG, edgecolor=BORDER_COLOR, linewidth=2.0, linestyle='-', zorder=1)
    ax.add_patch(sys_box)

    # System Boundary Label Header
    sys_header = FancyBboxPatch((15, 87), 68, 3,
                                boxstyle="round,pad=0.1,rounding_size=0.5",
                                facecolor="#F8FAFC", edgecolor=BORDER_COLOR, linewidth=1.5, zorder=2)
    ax.add_patch(sys_header)
    ax.text(49, 88.5, "BOUNDARY SISTEM: SISTEM PRESENSI DIGITAL & KESISWAAN SMK NU WARGABINANGUN",
            fontsize=12, fontweight='bold', color='#334155', ha='center', va='center', zorder=3)

    # Helper function to draw stickman actor
    def draw_actor(x, y, name, role_desc, is_external=False, color_bg=ACTOR_BG, color_border=ACTOR_BORDER):
        # Card container
        w, h = 9.5, 9.0
        card = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.2,rounding_size=0.6",
                              facecolor=color_bg, edgecolor=color_border, linewidth=2, zorder=4)
        ax.add_patch(card)

        # Stick figure inside
        head_y = y + 2.0
        # Head
        circle = plt.Circle((x, head_y), 0.75, facecolor='#FFFFFF', edgecolor=color_border, linewidth=2, zorder=5)
        ax.add_patch(circle)
        # Body
        ax.plot([x, x], [head_y - 0.75, head_y - 2.5], color=color_border, linewidth=2.2, zorder=5)
        # Arms
        ax.plot([x - 1.2, x + 1.2], [head_y - 1.4, head_y - 1.4], color=color_border, linewidth=2.2, zorder=5)
        # Legs
        ax.plot([x, x - 1.1], [head_y - 2.5, head_y - 3.8], color=color_border, linewidth=2.2, zorder=5)
        ax.plot([x, x + 1.1], [head_y - 2.5, head_y - 3.8], color=color_border, linewidth=2.2, zorder=5)

        # Label Text
        ax.text(x, y - 2.8, name, fontsize=9.5, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=5)
        ax.text(x, y - 3.7, role_desc, fontsize=7.2, color='#475569', ha='center', va='center', zorder=5)
        return (x, y)

    # Helper function to draw system external actor (Server / Bot)
    def draw_ext_actor(x, y, name, type_desc):
        w, h = 10.5, 8.5
        card = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.2,rounding_size=0.6",
                              facecolor=EXT_ACTOR_BG, edgecolor=EXT_ACTOR_BORDER, linewidth=2, zorder=4)
        ax.add_patch(card)

        # Icon server / api
        box1 = FancyBboxPatch((x - 1.8, y + 0.8), 3.6, 1.2, boxstyle="round,pad=0.1,rounding_size=0.2",
                              facecolor="#FFFFFF", edgecolor=EXT_ACTOR_BORDER, linewidth=1.5, zorder=5)
        box2 = FancyBboxPatch((x - 1.8, y - 0.8), 3.6, 1.2, boxstyle="round,pad=0.1,rounding_size=0.2",
                              facecolor="#FFFFFF", edgecolor=EXT_ACTOR_BORDER, linewidth=1.5, zorder=5)
        ax.add_patch(box1)
        ax.add_patch(box2)
        # Small LED dots
        ax.plot([x - 1.1], [y + 1.4], 'o', color='#10B981', markersize=4, zorder=6)
        ax.plot([x - 1.1], [y - 0.2], 'o', color='#10B981', markersize=4, zorder=6)

        ax.text(x, y - 2.3, name, fontsize=9.5, fontweight='bold', color='#92400E', ha='center', va='center', zorder=5)
        ax.text(x, y - 3.4, type_desc, fontsize=7.2, color='#B45309', ha='center', va='center', zorder=5)
        return (x, y)

    # POSITIONS OF PRIMARY ACTORS (Left column)
    actors = {
        'admin': draw_actor(7.5, 76, "ADMINISTRATOR", "Super Admin / Pengelola Sistem"),
        'kepsek': draw_actor(7.5, 60, "KEPALA SEKOLAH", "Pimpinan / Eksekutif"),
        'wali_kelas': draw_actor(7.5, 44, "WALI KELAS", "Pendidik & Pembina Kelas"),
        'guru': draw_actor(7.5, 28, "GURU PENGAJAR", "Pendidik Akademik"),
        'siswa': draw_actor(7.5, 12, "PESERTA DIDIK", "Siswa / Siswi Sekolah")
    }

    # POSITIONS OF SECONDARY / EXTERNAL ACTORS (Right column)
    ext_actors = {
        'telegram': draw_ext_actor(91.5, 52, "BOT TELEGRAM", "Notifikasi Gateway (@HadirioBot)"),
        'nvidia': draw_ext_actor(91.5, 25, "NVIDIA NIM LLM", "Engine AI Nemotron LLM")
    }

    # Helper function to draw Use Case Ellipse with Table Badges
    usecase_coords = {}
    def draw_usecase(uc_id, title, table_tag, cx, cy, rx=8.5, ry=2.2, border_color=UC_BORDER, bg_color=UC_BG, is_dashed=False):
        ellipse = Ellipse((cx, cy), rx*2, ry*2,
                          facecolor=bg_color, edgecolor=border_color,
                          linewidth=1.8, linestyle='--' if is_dashed else '-', zorder=4)
        ax.add_patch(ellipse)

        # Title text
        ax.text(cx, cy + 0.5, f"{uc_id}: {title}",
                fontsize=8.5, fontweight='bold', color='#1E293B', ha='center', va='center', zorder=5)
        # Table mapping badge
        badge_w, badge_h = rx * 1.5, 0.9
        badge = FancyBboxPatch((cx - badge_w/2, cy - 1.3), badge_w, badge_h,
                               boxstyle="round,pad=0.1,rounding_size=0.3",
                               facecolor="#F1F5F9", edgecolor="#CBD5E1", linewidth=0.8, zorder=5)
        ax.add_patch(badge)
        ax.text(cx, cy - 0.85, f"Tabel: {table_tag}",
                fontsize=7.0, fontweight='bold', color='#0369A1', ha='center', va='center', zorder=6)

        usecase_coords[uc_id] = (cx, cy, rx, ry)
        return (cx, cy)

    # 3. DRAW USE CASES (Organized into Logical Clusters inside Boundary)

    # --- CLUSTER A: AUTHENTICATION & PROFILE (Top Center) ---
    draw_usecase("UC01", "Login & Kelola Sesi Multi-Role", "users, roles", 31, 82, rx=8.2, ry=2.1)
    draw_usecase("UC02", "Kelola Profil & Foto Pribadi", "users", 54, 82, rx=8.0, ry=2.1)

    # --- CLUSTER B: MASTER DATA & KURIKULUM (Left Inner Column) ---
    draw_usecase("UC03", "Kelola Master Tahun Ajaran & Rombel", "tahun_ajaran, jurusan, kelas", 29, 71, rx=8.6, ry=2.1)
    draw_usecase("UC04", "Kelola Data Guru & Wali Kelas", "guru, wali_kelas_data, wali_kelas", 29, 61, rx=8.6, ry=2.1)
    draw_usecase("UC05", "Kelola Master Peserta Didik", "siswa", 29, 51, rx=8.6, ry=2.1)
    draw_usecase("UC06", "Kelola Mata Pelajaran & Jadwal KBM", "mata_pelajaran, jadwal_mengajar", 29, 41, rx=8.6, ry=2.1)
    draw_usecase("UC07", "Generate Barcode & Kartu RFID", "barcode_guru, rfid_cards, siswa", 29, 31, rx=8.6, ry=2.1)

    # --- CLUSTER C: PRESENSI & OPERASIONAL HARIAN (Center Core) ---
    draw_usecase("UC08", "Scan Presensi Masuk & Pulang", "absensi_siswa, absensi_guru, absensi_wali_kelas", 53, 67, rx=9.2, ry=2.2)
    # Sub-Use Cases included
    draw_usecase("UC08.1", "Validasi Waktu & Dual-Sync Role", "absensi_guru, absensi_wali_kelas", 72, 74, rx=7.8, ry=2.0, border_color=INCLUDE_BORDER, is_dashed=True)
    draw_usecase("UC08.2", "Push Notifikasi Presensi Instan", "absensi_siswa, pengaturan_sistem", 73, 63, rx=7.6, ry=2.0, border_color=INCLUDE_BORDER, is_dashed=True)

    # --- CLUSTER D: PENGAJUAN IZIN / SAKIT (Center Lower) ---
    draw_usecase("UC09", "Pengajuan & Approval Izin / Sakit", "pengajuan_izin", 53, 53, rx=8.8, ry=2.1)
    draw_usecase("UC09.1", "Sinkronisasi Rekapitulasi Izin", "absensi_siswa, absensi_guru", 73, 51, rx=7.6, ry=1.9, border_color=INCLUDE_BORDER, is_dashed=True)

    # --- CLUSTER E: SUPERVISI & PEMBINAAN SISWA ---
    draw_usecase("UC10", "Supervisi KBM & Jurnal Mengajar", "catatan_guru", 52, 40, rx=8.6, ry=2.1)
    draw_usecase("UC11", "Catatan Pembinaan & Disiplin", "catatan_pembinaan", 52, 29, rx=8.6, ry=2.1)

    # --- CLUSTER F: REKAPITULASI, CHATBOT & PENGATURAN (Bottom Row) ---
    draw_usecase("UC12", "Rekap Presensi & Ekspor Laporan", "absensi_siswa, absensi_guru, absensi_wali_kelas", 33, 19, rx=9.0, ry=2.1)
    draw_usecase("UC13", "Konsultasi Informasi via Chatbot", "pengaturan_sistem & 20 Tabel DB", 55, 17, rx=8.8, ry=2.1)
    draw_usecase("UC13.1", "NLP Contextual Query Engine", "Konektor NVIDIA NIM LLM", 73, 20, rx=7.4, ry=1.9, border_color=EXTEND_BORDER, is_dashed=True)
    draw_usecase("UC14", "Konfigurasi Sistem & Token API", "pengaturan_sistem", 33, 10, rx=8.6, ry=2.0)

    # 4. DRAW ACTOR-TO-USECASE CONNECTORS
    def connect_actor_uc(actor_key, uc_key, color=LINE_COLOR, lw=1.2, alpha=0.75):
        ax_pos = actors[actor_key]
        uc_pos = usecase_coords[uc_key]
        # Calculate line endpoints nicely
        x1 = ax_pos[0] + 4.75
        y1 = ax_pos[1]
        x2 = uc_pos[0] - uc_pos[2] * 0.95
        y2 = uc_pos[1]
        ax.plot([x1, x2], [y1, y2], color=color, linewidth=lw, alpha=alpha, zorder=3)

    # Connect Admin
    connect_actor_uc('admin', 'UC01')
    connect_actor_uc('admin', 'UC03')
    connect_actor_uc('admin', 'UC04')
    connect_actor_uc('admin', 'UC05')
    connect_actor_uc('admin', 'UC06')
    connect_actor_uc('admin', 'UC07')
    connect_actor_uc('admin', 'UC08')
    connect_actor_uc('admin', 'UC09')
    connect_actor_uc('admin', 'UC10')
    connect_actor_uc('admin', 'UC11')
    connect_actor_uc('admin', 'UC12')
    connect_actor_uc('admin', 'UC13')
    connect_actor_uc('admin', 'UC14')

    # Connect Kepala Sekolah
    connect_actor_uc('kepsek', 'UC01')
    connect_actor_uc('kepsek', 'UC02')
    connect_actor_uc('kepsek', 'UC10')
    connect_actor_uc('kepsek', 'UC11')
    connect_actor_uc('kepsek', 'UC12')
    connect_actor_uc('kepsek', 'UC13')

    # Connect Wali Kelas
    connect_actor_uc('wali_kelas', 'UC01')
    connect_actor_uc('wali_kelas', 'UC02')
    connect_actor_uc('wali_kelas', 'UC08')
    connect_actor_uc('wali_kelas', 'UC09')
    connect_actor_uc('wali_kelas', 'UC10')
    connect_actor_uc('wali_kelas', 'UC11')
    connect_actor_uc('wali_kelas', 'UC12')
    connect_actor_uc('wali_kelas', 'UC13')

    # Connect Guru
    connect_actor_uc('guru', 'UC01')
    connect_actor_uc('guru', 'UC02')
    connect_actor_uc('guru', 'UC08')
    connect_actor_uc('guru', 'UC09')
    connect_actor_uc('guru', 'UC10')
    connect_actor_uc('guru', 'UC11')
    connect_actor_uc('guru', 'UC12')
    connect_actor_uc('guru', 'UC13')

    # Connect Siswa
    connect_actor_uc('siswa', 'UC01')
    connect_actor_uc('siswa', 'UC02')
    connect_actor_uc('siswa', 'UC08')
    connect_actor_uc('siswa', 'UC09')
    connect_actor_uc('siswa', 'UC11')
    connect_actor_uc('siswa', 'UC12')
    connect_actor_uc('siswa', 'UC13')

    # 5. DRAW UML RELATIONS (<<include>> and <<extend>>)
    def draw_dependency(from_uc, to_uc, rel_text, color, is_arrow_rev=False):
        p1 = usecase_coords[from_uc]
        p2 = usecase_coords[to_uc]
        x1, y1 = p1[0] + p1[2]*0.8, p1[1]
        x2, y2 = p2[0] - p2[2]*0.8, p2[1]

        # Draw arrow with dashed style
        ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                    arrowprops=dict(arrowstyle="->", color=color, lw=1.5, linestyle="--", shrinkA=2, shrinkB=2),
                    zorder=6)
        # Text label above center
        mx, my = (x1 + x2)/2, (y1 + y2)/2 + 0.6
        ax.text(mx, my, rel_text, fontsize=7.2, fontweight='bold', color=color,
                ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none", alpha=0.9), zorder=7)

    draw_dependency("UC08", "UC08.1", "<<include>>", INCLUDE_BORDER)
    draw_dependency("UC08", "UC08.2", "<<include>>", INCLUDE_BORDER)
    draw_dependency("UC09", "UC09.1", "<<include>>", INCLUDE_BORDER)
    draw_dependency("UC13", "UC13.1", "<<extend>>", EXTEND_BORDER)

    # 6. CONNECT EXTERNAL ACTORS TO SUB-USECASES
    # Telegram Bot Actor
    tel_pos = ext_actors['telegram']
    uc08_2_pos = usecase_coords['UC08.2']
    ax.plot([tel_pos[0] - 5.25, uc08_2_pos[0] + uc08_2_pos[2]],
            [tel_pos[1] + 1.5, uc08_2_pos[1]],
            color=EXT_ACTOR_BORDER, linewidth=1.6, linestyle='-', zorder=5)
    ax.text((tel_pos[0] - 5.25 + uc08_2_pos[0] + uc08_2_pos[2])/2, (tel_pos[1] + 1.5 + uc08_2_pos[1])/2 + 0.6,
            "Webhook / Push", fontsize=7.0, color=EXT_ACTOR_BORDER, fontweight='bold',
            ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FEF3C7", ec="none"), zorder=6)

    # NVIDIA AI Actor
    nv_pos = ext_actors['nvidia']
    uc13_1_pos = usecase_coords['UC13.1']
    ax.plot([nv_pos[0] - 5.25, uc13_1_pos[0] + uc13_1_pos[2]],
            [nv_pos[1] - 0.5, uc13_1_pos[1]],
            color=EXT_ACTOR_BORDER, linewidth=1.6, linestyle='-', zorder=5)
    ax.text((nv_pos[0] - 5.25 + uc13_1_pos[0] + uc13_1_pos[2])/2, (nv_pos[1] - 0.5 + uc13_1_pos[1])/2 + 0.6,
            "REST API Inference", fontsize=7.0, color=EXT_ACTOR_BORDER, fontweight='bold',
            ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FEF3C7", ec="none"), zorder=6)

    # 7. BOTTOM DATABASE MATRIX & LEGEND BANNER
    legend_box = FancyBboxPatch((2, 0.8), 96, 5.5,
                                boxstyle="round,pad=0.2,rounding_size=0.6",
                                facecolor="#FFFFFF", edgecolor=BORDER_COLOR, linewidth=1.5, zorder=3)
    ax.add_patch(legend_box)

    ax.text(3.5, 5.2, "MATRIKS 20 TABEL DATABASE `db_absensi_smk` TERHUBUNG LENGKAP:",
            fontsize=9.0, fontweight='bold', color=PRIMARY_NAVY, va='center')

    db_groups = [
        ("1. Keamanan & Akun:", "users, roles", "#1E3A8A"),
        ("2. Master Akademik:", "tahun_ajaran, jurusan, kelas, guru, wali_kelas_data, wali_kelas, siswa, mata_pelajaran, jadwal_mengajar", "#0369A1"),
        ("3. Kartu & Barcode:", "barcode_guru, rfid_cards", "#0D9488"),
        ("4. Presensi & Izin:", "absensi_siswa, absensi_guru, absensi_wali_kelas, pengajuan_izin", "#B45309"),
        ("5. Bimbingan & Sistem:", "catatan_guru, catatan_pembinaan, pengaturan_sistem", "#6D28D9")
    ]

    y_off = 4.1
    for grp_title, grp_tables, col in db_groups:
        ax.text(4.0, y_off, grp_title, fontsize=8.0, fontweight='bold', color=col, va='center')
        ax.text(18.0, y_off, f"[{grp_tables}]", fontsize=7.5, color='#334155', va='center')
        y_off -= 0.8

    # Legend symbols right side
    ax.text(76, 5.2, "LEGENDA NOTASI UML:", fontsize=8.5, fontweight='bold', color=PRIMARY_NAVY, va='center')
    ax.plot([76, 79], [4.2, 4.2], color=LINE_COLOR, lw=1.5)
    ax.text(80, 4.2, "Asosiasi Aktor", fontsize=7.2, color='#334155', va='center')

    ax.plot([76, 79], [3.2, 3.2], color=INCLUDE_BORDER, lw=1.5, linestyle="--")
    ax.text(80, 3.2, "<<include>> Wajib Dijalankan", fontsize=7.2, color=INCLUDE_BORDER, va='center')

    ax.plot([76, 79], [2.2, 2.2], color=EXTEND_BORDER, lw=1.5, linestyle="--")
    ax.text(80, 2.2, "<<extend>> Operasi Opsional / AI", fontsize=7.2, color=EXTEND_BORDER, va='center')

    ax.plot([76, 79], [1.2, 1.2], color=EXT_ACTOR_BORDER, lw=1.5)
    ax.text(80, 1.2, "Integrasi Layanan Eksternal", fontsize=7.2, color=EXT_ACTOR_BORDER, va='center')

    # Save to high-res PNG
    plt.tight_layout()
    plt.savefig(output_path, dpi=dpi, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print(f"[SUCCESS] Use Case Diagram saved to: {output_path}")

if __name__ == "__main__":
    out_file = r"c:\xampp\htdocs\absen\Usecase_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    create_usecase_diagram(out_file)
