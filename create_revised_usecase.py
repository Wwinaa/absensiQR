import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Ellipse
import numpy as np

def create_revised_usecase(output_path):
    # Canvas Size: 28 x 20 inches at 300 DPI -> 8400 x 6000 px
    fig, ax = plt.subplots(figsize=(28, 20), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    # Color Palette - Professional Soft Blue & Slate
    BG_COLOR = "#F8FAFC"       # Clean Slate 50
    PANEL_BG = "#FFFFFF"       # White
    PRIMARY_NAVY = "#0F172A"   # Slate 900
    ACCENT_BLUE = "#0284C7"    # Sky 600
    DARK_BLUE = "#1E3A8A"      # Blue 900
    BORDER_COLOR = "#CBD5E1"   # Slate 300
    ACTOR_BG = "#EFF6FF"       # Sky 50
    ACTOR_BORDER = "#0284C7"   # Sky 600
    EXT_BG = "#FEF3C7"         # Amber 50
    EXT_BORDER = "#D97706"     # Amber 600
    UC_BORDER = "#2563EB"      # Blue 600
    INC_BORDER = "#0D9488"     # Teal 600
    EXT_INC_BORDER = "#D97706" # Amber 600
    LINE_COLOR = "#94A3B8"     # Slate 400

    fig.patch.set_facecolor(BG_COLOR)
    ax.set_facecolor(BG_COLOR)

    # 1. HEADER BANNER
    banner = FancyBboxPatch((2, 92.2), 96, 6.5,
                            boxstyle="round,pad=0.2,rounding_size=0.6",
                            facecolor=DARK_BLUE, edgecolor=ACCENT_BLUE, linewidth=2.5, zorder=2)
    ax.add_patch(banner)

    ax.text(50, 96.5, "USE CASE DIAGRAM TERPADU SISTEM PRESENSI SMK NU WARGABINANGUN",
            fontsize=19, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 94.3, "Pemetaan Lengkap Relasi Fungsional Multi-Role terhadap 20 Tabel Relasional Database `db_absensi_smk`",
            fontsize=11.5, fontweight='normal', color='#BAE6FD', ha='center', va='center', zorder=3)
    ax.text(50, 93.0, "Standar UML 2.5 • Otentikasi, Presensi QR, Dual-Sync, Dispensasi Izin, Supervisi, AI Hadir.io & Rekapitulasi",
            fontsize=9.2, fontstyle='italic', color='#E2E8F0', ha='center', va='center', zorder=3)

    # 2. SYSTEM BOUNDARY (The System Box)
    sys_box = FancyBboxPatch((14.5, 7.5), 69.5, 83.0,
                             boxstyle="round,pad=0.3,rounding_size=0.8",
                             facecolor=PANEL_BG, edgecolor="#94A3B8", linewidth=2.0, zorder=1)
    ax.add_patch(sys_box)

    # System Boundary Label Header
    sys_header = FancyBboxPatch((14.5, 87.8), 69.5, 2.7,
                                boxstyle="round,pad=0.1,rounding_size=0.4",
                                facecolor="#F1F5F9", edgecolor="#CBD5E1", linewidth=1.2, zorder=2)
    ax.add_patch(sys_header)
    ax.text(49.25, 89.15, "BOUNDARY SISTEM: SISTEM PRESENSI DIGITAL SMK NU WARGABINANGUN (db_absensi_smk)",
            fontsize=10.8, fontweight='bold', color='#1E293B', ha='center', va='center', zorder=3)

    # Helper function to draw stickman actor cleanly
    def draw_actor(x, y, name, role_desc, w=9.0, h=8.2):
        card = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.15,rounding_size=0.5",
                              facecolor=ACTOR_BG, edgecolor=ACTOR_BORDER, linewidth=1.8, zorder=4)
        ax.add_patch(card)

        # Stick figure inside
        head_y = y + 1.8
        # Head
        circle = plt.Circle((x, head_y), 0.65, facecolor='#FFFFFF', edgecolor=ACTOR_BORDER, linewidth=1.8, zorder=5)
        ax.add_patch(circle)
        # Body
        ax.plot([x, x], [head_y - 0.65, head_y - 2.2], color=ACTOR_BORDER, linewidth=2.0, zorder=5)
        # Arms
        ax.plot([x - 1.1, x + 1.1], [head_y - 1.2, head_y - 1.2], color=ACTOR_BORDER, linewidth=2.0, zorder=5)
        # Legs
        ax.plot([x, x - 0.95], [head_y - 2.2, head_y - 3.4], color=ACTOR_BORDER, linewidth=2.0, zorder=5)
        ax.plot([x, x + 0.95], [head_y - 2.2, head_y - 3.4], color=ACTOR_BORDER, linewidth=2.0, zorder=5)

        # Label Text
        ax.text(x, y - 2.5, name, fontsize=8.8, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=5)
        ax.text(x, y - 3.3, role_desc, fontsize=6.8, color='#475569', ha='center', va='center', zorder=5)
        return (x, y)

    # Helper function to draw external actor
    def draw_ext_actor(x, y, name, type_desc, w=10.5, h=8.0):
        card = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.15,rounding_size=0.5",
                              facecolor=EXT_BG, edgecolor=EXT_BORDER, linewidth=1.8, zorder=4)
        ax.add_patch(card)

        # Server/API icon
        b1 = FancyBboxPatch((x - 1.7, y + 0.7), 3.4, 1.1, boxstyle="round,pad=0.1,rounding_size=0.2",
                            facecolor="#FFFFFF", edgecolor=EXT_BORDER, linewidth=1.4, zorder=5)
        b2 = FancyBboxPatch((x - 1.7, y - 0.7), 3.4, 1.1, boxstyle="round,pad=0.1,rounding_size=0.2",
                            facecolor="#FFFFFF", edgecolor=EXT_BORDER, linewidth=1.4, zorder=5)
        ax.add_patch(b1)
        ax.add_patch(b2)
        ax.plot([x - 1.0], [y + 1.25], 'o', color='#10B981', markersize=3.5, zorder=6)
        ax.plot([x - 1.0], [y - 0.15], 'o', color='#10B981', markersize=3.5, zorder=6)

        ax.text(x, y - 2.1, name, fontsize=8.8, fontweight='bold', color='#92400E', ha='center', va='center', zorder=5)
        ax.text(x, y - 3.1, type_desc, fontsize=6.8, color='#B45309', ha='center', va='center', zorder=5)
        return (x, y)

    # 3. DRAW PRIMARY ACTORS (Left Column)
    actors = {
        'admin': draw_actor(7.2, 77.0, "ADMINISTRATOR", "Super Admin / Tata Usaha"),
        'kepsek': draw_actor(7.2, 62.0, "KEPALA SEKOLAH", "Pimpinan / Eksekutif"),
        'wali_kelas': draw_actor(7.2, 47.0, "WALI KELAS", "Pendidik & Pembina Rombel"),
        'guru': draw_actor(7.2, 32.0, "GURU PENGAJAR", "Tenaga Pendidik KBM"),
        'siswa': draw_actor(7.2, 17.0, "PESERTA DIDIK", "Siswa / Siswi Sekolah")
    }

    # 4. DRAW EXTERNAL ACTORS (Right Column)
    ext_actors = {
        'telegram': draw_ext_actor(91.8, 55.0, "BOT TELEGRAM", "Gateway (@HadirioBot)"),
        'nvidia': draw_ext_actor(91.8, 22.0, "NVIDIA NIM AI", "Engine Nemotron LLM")
    }

    # Helper function to draw Use Case capsule
    usecase_coords = {}
    def draw_uc(uc_id, title, table_tag, cx, cy, rx=8.4, ry=2.1, border=UC_BORDER, is_dashed=False):
        ellipse = Ellipse((cx, cy), rx*2, ry*2, facecolor="#FFFFFF", edgecolor=border,
                          linewidth=1.7, linestyle='--' if is_dashed else '-', zorder=4)
        ax.add_patch(ellipse)

        # Title
        ax.text(cx, cy + 0.45, f"{uc_id}: {title}", fontsize=8.0, fontweight='bold',
                color='#0F172A', ha='center', va='center', zorder=5)

        # Table badge
        bw, bh = rx * 1.55, 0.85
        badge = FancyBboxPatch((cx - bw/2, cy - 1.25), bw, bh,
                               boxstyle="round,pad=0.1,rounding_size=0.25",
                               facecolor="#F1F5F9", edgecolor="#CBD5E1", linewidth=0.7, zorder=5)
        ax.add_patch(badge)
        ax.text(cx, cy - 0.82, f"Tabel: {table_tag}", fontsize=6.8, fontweight='bold',
                color='#0369A1', ha='center', va='center', zorder=6)

        usecase_coords[uc_id] = (cx, cy, rx, ry)
        return (cx, cy)

    # 5. DRAW USE CASES IN 3 WELL-ORGANIZED COLUMNS
    # --- COLUMN 1: AKADEMIK & MASTER DATA (x=30.0) ---
    draw_uc("UC01", "Login & Kelola Sesi Multi-Role", "users, roles", 30.0, 81.5, rx=8.2, ry=2.0)
    draw_uc("UC02", "Kelola Profil & Biodata Pengguna", "users", 30.0, 71.5, rx=8.2, ry=2.0)
    draw_uc("UC03", "Kelola Tahun Ajaran, Jurusan & Kelas", "tahun_ajaran, jurusan, kelas", 30.0, 61.5, rx=8.5, ry=2.0)
    draw_uc("UC04", "Kelola Guru & Wali Kelas Data", "guru, wali_kelas_data, wali_kelas", 30.0, 51.5, rx=8.5, ry=2.0)
    draw_uc("UC05", "Kelola Master Peserta Didik", "siswa", 30.0, 41.5, rx=8.2, ry=2.0)
    draw_uc("UC06", "Kelola Mapel & Jadwal KBM", "mata_pelajaran, jadwal_mengajar", 30.0, 31.5, rx=8.5, ry=2.0)
    draw_uc("UC07", "Generate Barcode & Kartu RFID", "barcode_guru, rfid_cards, siswa", 30.0, 21.5, rx=8.5, ry=2.0)

    # --- COLUMN 2: CORE PRESENSI, IZIN & SUPERVISI (x=52.0) ---
    draw_uc("UC08", "Scan Presensi QR (Masuk & Pulang)", "absensi_siswa, absensi_guru, absensi_wali_kelas", 52.0, 72.0, rx=9.2, ry=2.2)
    draw_uc("UC09", "Pengajuan & Approval Izin/Sakit", "pengajuan_izin", 52.0, 55.0, rx=8.8, ry=2.1)
    draw_uc("UC10", "Supervisi KBM & Jurnal Mengajar", "catatan_guru", 52.0, 41.0, rx=8.6, ry=2.1)
    draw_uc("UC11", "Catatan Pembinaan & Disiplin Siswa", "catatan_pembinaan", 52.0, 28.0, rx=8.8, ry=2.1)
    draw_uc("UC14", "Konfigurasi Sistem & Token API", "pengaturan_sistem", 52.0, 15.0, rx=8.6, ry=2.0)

    # --- COLUMN 3: INCLUDED SUB-USECASES, AI & REKAP (x=73.5) ---
    draw_uc("UC08.1", "Validasi Waktu & Dual-Sync Role", "absensi_guru, absensi_wali_kelas", 73.5, 78.5, rx=7.8, ry=1.9, border=INC_BORDER, is_dashed=True)
    draw_uc("UC08.2", "Push Notifikasi Presensi Instan", "absensi_siswa, pengaturan_sistem", 73.5, 66.5, rx=7.8, ry=1.9, border=INC_BORDER, is_dashed=True)
    draw_uc("UC09.1", "Sinkronisasi Rekapitulasi Izin", "absensi_siswa, absensi_guru", 73.5, 53.0, rx=7.8, ry=1.9, border=INC_BORDER, is_dashed=True)
    draw_uc("UC12", "Rekap Presensi & Ekspor Laporan", "absensi_siswa, guru, wali_kelas", 73.5, 39.0, rx=8.5, ry=2.1)
    draw_uc("UC13", "Konsultasi Informasi via Hadir.io", "pengaturan_sistem & 20 Tabel DB", 73.5, 26.0, rx=8.6, ry=2.1)
    draw_uc("UC13.1", "NLP Contextual Query Engine", "Konektor Model AI Nemotron", 73.5, 14.5, rx=7.6, ry=1.9, border=EXT_INC_BORDER, is_dashed=True)

    # 6. DRAW ACTOR ASSOCIATIONS (CLEAN, ROUTED CONNECTIONS)
    def connect_actor(actor_key, uc_key, alpha=0.75):
        ap = actors[actor_key]
        up = usecase_coords[uc_key]
        x1 = ap[0] + 4.5
        y1 = ap[1]
        x2 = up[0] - up[2] * 0.96
        y2 = up[1]
        ax.plot([x1, x2], [y1, y2], color=LINE_COLOR, linewidth=1.15, alpha=alpha, zorder=3)

    # Admin connections
    for uc in ['UC01', 'UC03', 'UC04', 'UC05', 'UC06', 'UC07', 'UC08', 'UC09', 'UC10', 'UC11', 'UC12', 'UC13', 'UC14']:
        connect_actor('admin', uc)

    # Kepsek connections
    for uc in ['UC01', 'UC02', 'UC10', 'UC11', 'UC12', 'UC13']:
        connect_actor('kepsek', uc)

    # Wali Kelas connections
    for uc in ['UC01', 'UC02', 'UC08', 'UC09', 'UC10', 'UC11', 'UC12', 'UC13']:
        connect_actor('wali_kelas', uc)

    # Guru connections
    for uc in ['UC01', 'UC02', 'UC08', 'UC09', 'UC10', 'UC12', 'UC13']:
        connect_actor('guru', uc)

    # Siswa connections
    for uc in ['UC01', 'UC02', 'UC08', 'UC09', 'UC11', 'UC12', 'UC13']:
        connect_actor('siswa', uc)

    # 7. DRAW UML DEPENDENCIES (<<include>> and <<extend>>)
    def draw_dep(from_uc, to_uc, rel_text, color, is_extend=False):
        p1 = usecase_coords[from_uc]
        p2 = usecase_coords[to_uc]
        if not is_extend:
            # from base to included
            x1, y1 = p1[0] + p1[2]*0.8, p1[1]
            x2, y2 = p2[0] - p2[2]*0.85, p2[1]
        else:
            # from extension to base (UML standard: extension points to extended use case)
            x1, y1 = p1[0], p1[1] + p1[3]
            x2, y2 = p2[0], p2[1] - p2[3]

        ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                    arrowprops=dict(arrowstyle="->", color=color, lw=1.4, linestyle="--", shrinkA=2, shrinkB=2),
                    zorder=6)
        mx, my = (x1 + x2)/2, (y1 + y2)/2 + (0.5 if not is_extend else 0.0)
        ax.text(mx, my, rel_text, fontsize=6.8, fontweight='bold', color=color,
                ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none", alpha=0.9), zorder=7)

    draw_dep("UC08", "UC08.1", "<<include>>", INC_BORDER)
    draw_dep("UC08", "UC08.2", "<<include>>", INC_BORDER)
    draw_dep("UC09", "UC09.1", "<<include>>", INC_BORDER)
    draw_dep("UC13.1", "UC13", "<<extend>>", EXT_INC_BORDER, is_extend=True)

    # 8. CONNECT EXTERNAL ACTORS
    # Telegram Bot
    t_pos = ext_actors['telegram']
    uc08_2 = usecase_coords['UC08.2']
    ax.plot([t_pos[0] - 5.25, uc08_2[0] + uc08_2[2]], [t_pos[1], uc08_2[1]],
            color=EXT_BORDER, linewidth=1.5, zorder=5)
    ax.text((t_pos[0] - 5.25 + uc08_2[0] + uc08_2[2])/2, (t_pos[1] + uc08_2[1])/2 + 0.5,
            "Webhook Push", fontsize=6.8, color=EXT_BORDER, fontweight='bold',
            ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FEF3C7", ec="none"), zorder=6)

    # NVIDIA AI
    nv_pos = ext_actors['nvidia']
    uc13_1 = usecase_coords['UC13.1']
    ax.plot([nv_pos[0] - 5.25, uc13_1[0] + uc13_1[2]], [nv_pos[1], uc13_1[1]],
            color=EXT_BORDER, linewidth=1.5, zorder=5)
    ax.text((nv_pos[0] - 5.25 + uc13_1[0] + uc13_1[2])/2, (nv_pos[1] + uc13_1[1])/2 + 0.5,
            "REST API Inference", fontsize=6.8, color=EXT_BORDER, fontweight='bold',
            ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FEF3C7", ec="none"), zorder=6)

    # 9. BOTTOM FOOTER MATRIX & LEGEND
    footer = FancyBboxPatch((2, 0.8), 96, 5.6,
                            boxstyle="round,pad=0.2,rounding_size=0.5",
                            facecolor="#FFFFFF", edgecolor="#CBD5E1", linewidth=1.3, zorder=3)
    ax.add_patch(footer)

    ax.text(3.5, 5.2, "VERIFIKASI INTEGRITAS 20 TABEL DATABASE `db_absensi_smk` DALAM USE CASE DIAGRAM:",
            fontsize=8.5, fontweight='bold', color=PRIMARY_NAVY, va='center')

    db_rows = [
        ("1. Keamanan & Akun:", "users, roles", "#1E3A8A"),
        ("2. Master Data Akademik:", "tahun_ajaran, jurusan, kelas, guru, wali_kelas_data, wali_kelas, siswa, mata_pelajaran, jadwal_mengajar", "#0369A1"),
        ("3. Identifikasi & Barcode:", "barcode_guru, rfid_cards", "#0D9488"),
        ("4. Presensi & Izin:", "absensi_siswa, absensi_guru, absensi_wali_kelas, pengajuan_izin", "#B45309"),
        ("5. Bimbingan & Konfigurasi:", "catatan_guru, catatan_pembinaan, pengaturan_sistem", "#6D28D9")
    ]

    y_pos = 4.15
    for title, tbls, col in db_rows:
        ax.text(4.0, y_pos, title, fontsize=7.8, fontweight='bold', color=col, va='center')
        ax.text(19.0, y_pos, f"[{tbls}]", fontsize=7.2, color='#334155', va='center')
        y_pos -= 0.8

    # Legend symbols right side
    ax.text(78, 5.2, "LEGENDA NOTASI:", fontsize=8.0, fontweight='bold', color=PRIMARY_NAVY, va='center')
    ax.plot([78, 81], [4.2, 4.2], color=LINE_COLOR, lw=1.3)
    ax.text(82, 4.2, "Asosiasi Aktor", fontsize=7.0, color='#334155', va='center')

    ax.plot([78, 81], [3.2, 3.2], color=INC_BORDER, lw=1.3, linestyle="--")
    ax.text(82, 3.2, "<<include>> Wajib", fontsize=7.0, color=INC_BORDER, va='center')

    ax.plot([78, 81], [2.2, 2.2], color=EXT_INC_BORDER, lw=1.3, linestyle="--")
    ax.text(82, 2.2, "<<extend>> Opsional", fontsize=7.0, color=EXT_INC_BORDER, va='center')

    ax.plot([78, 81], [1.2, 1.2], color=EXT_BORDER, lw=1.3)
    ax.text(82, 1.2, "Layanan Eksternal", fontsize=7.0, color=EXT_BORDER, va='center')

    plt.tight_layout()
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print(f"[SUCCESS] Revised Use Case Diagram saved to: {output_path}")

if __name__ == "__main__":
    out_file = r"c:\xampp\htdocs\absen\Usecase_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    create_revised_usecase(out_file)
