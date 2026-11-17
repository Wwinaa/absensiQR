import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Ellipse
import numpy as np

def generate_clean_usecase(output_path):
    # Canvas Size: 28 x 20 inches, 300 DPI -> 8400 x 6000 px
    fig, ax = plt.subplots(figsize=(28, 20), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    # Color Palette - Professional Soft Blue & Slate
    BG_COLOR = "#F8FAFC"
    PANEL_BG = "#FFFFFF"
    PRIMARY_NAVY = "#0F172A"
    ACCENT_BLUE = "#0284C7"
    DARK_BLUE = "#1E3A8A"
    BORDER_COLOR = "#CBD5E1"
    ACTOR_BG = "#EFF6FF"
    ACTOR_BORDER = "#0284C7"
    EXT_BG = "#FEF3C7"
    EXT_BORDER = "#D97706"
    UC_BORDER = "#2563EB"
    INC_BORDER = "#0D9488"
    EXT_INC_BORDER = "#D97706"
    LINE_COLOR = "#64748B"

    fig.patch.set_facecolor(BG_COLOR)
    ax.set_facecolor(BG_COLOR)

    # 1. HEADER BANNER
    banner = FancyBboxPatch((2, 92.5), 96, 6.2,
                            boxstyle="round,pad=0.2,rounding_size=0.6",
                            facecolor=DARK_BLUE, edgecolor=ACCENT_BLUE, linewidth=2.5, zorder=2)
    ax.add_patch(banner)

    ax.text(50, 96.6, "USE CASE DIAGRAM TERPADU SISTEM PRESENSI SMK NU WARGABINANGUN",
            fontsize=18.5, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 94.6, "Pemetaan Lengkap Relasi Fungsional Multi-Role terhadap 20 Tabel Relasional Database `db_absensi_smk`",
            fontsize=11.2, fontweight='normal', color='#BAE6FD', ha='center', va='center', zorder=3)
    ax.text(50, 93.3, "Standar UML 2.5 • Otentikasi, Presensi QR, Dual-Sync, Dispensasi Izin, Supervisi, AI Hadir.io & Rekapitulasi",
            fontsize=9.0, fontstyle='italic', color='#E2E8F0', ha='center', va='center', zorder=3)

    # 2. SYSTEM BOUNDARY (The System Box)
    sys_box = FancyBboxPatch((15.0, 7.5), 69.0, 83.5,
                             boxstyle="round,pad=0.3,rounding_size=0.8",
                             facecolor=PANEL_BG, edgecolor="#94A3B8", linewidth=2.0, zorder=1)
    ax.add_patch(sys_box)

    sys_header = FancyBboxPatch((15.0, 88.3), 69.0, 2.7,
                                boxstyle="round,pad=0.1,rounding_size=0.4",
                                facecolor="#F1F5F9", edgecolor="#CBD5E1", linewidth=1.2, zorder=2)
    ax.add_patch(sys_header)
    ax.text(49.5, 89.65, "BOUNDARY SISTEM: SISTEM PRESENSI DIGITAL & KESISWAAN SMK NU WARGABINANGUN",
            fontsize=10.5, fontweight='bold', color='#1E293B', ha='center', va='center', zorder=3)

    # Helper function to draw stickman actor
    def draw_actor(x, y, name, role_desc, w=9.0, h=8.0):
        card = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.15,rounding_size=0.5",
                              facecolor=ACTOR_BG, edgecolor=ACTOR_BORDER, linewidth=1.8, zorder=4)
        ax.add_patch(card)

        # Stick figure inside
        head_y = y + 1.8
        circle = plt.Circle((x, head_y), 0.65, facecolor='#FFFFFF', edgecolor=ACTOR_BORDER, linewidth=1.8, zorder=5)
        ax.add_patch(circle)
        ax.plot([x, x], [head_y - 0.65, head_y - 2.2], color=ACTOR_BORDER, linewidth=2.0, zorder=5)
        ax.plot([x - 1.1, x + 1.1], [head_y - 1.2, head_y - 1.2], color=ACTOR_BORDER, linewidth=2.0, zorder=5)
        ax.plot([x, x - 0.95], [head_y - 2.2, head_y - 3.4], color=ACTOR_BORDER, linewidth=2.0, zorder=5)
        ax.plot([x, x + 0.95], [head_y - 2.2, head_y - 3.4], color=ACTOR_BORDER, linewidth=2.0, zorder=5)

        ax.text(x, y - 2.5, name, fontsize=8.8, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=5)
        ax.text(x, y - 3.3, role_desc, fontsize=6.8, color='#475569', ha='center', va='center', zorder=5)
        return (x, y)

    # Helper function to draw external actor
    def draw_ext_actor(x, y, name, type_desc, w=10.5, h=8.0):
        card = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.15,rounding_size=0.5",
                              facecolor=EXT_BG, edgecolor=EXT_BORDER, linewidth=1.8, zorder=4)
        ax.add_patch(card)

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

    # 3. DRAW ACTORS
    # Left Column Actors (Nicely distributed vertically)
    actors = {
        'admin': draw_actor(7.5, 78.0, "ADMINISTRATOR", "Super Admin / Tata Usaha"),
        'kepsek': draw_actor(7.5, 62.0, "KEPALA SEKOLAH", "Pimpinan / Eksekutif"),
        'wali_kelas': draw_actor(7.5, 46.0, "WALI KELAS", "Pendidik & Pembina Rombel"),
        'guru': draw_actor(7.5, 30.0, "GURU PENGAJAR", "Tenaga Pendidik KBM"),
        'siswa': draw_actor(7.5, 14.0, "PESERTA DIDIK", "Siswa / Siswi Sekolah")
    }

    # Right Column External Actors
    ext_actors = {
        'telegram': draw_ext_actor(91.8, 62.0, "BOT TELEGRAM", "Gateway (@HadirioBot)"),
        'nvidia': draw_ext_actor(91.8, 22.0, "NVIDIA NIM AI", "Engine Nemotron LLM")
    }

    # Helper function to draw Use Case capsule
    usecase_coords = {}
    def draw_uc(uc_id, title, table_tag, cx, cy, rx=7.8, ry=2.0, border=UC_BORDER, is_dashed=False):
        ellipse = Ellipse((cx, cy), rx*2, ry*2, facecolor="#FFFFFF", edgecolor=border,
                          linewidth=1.7, linestyle='--' if is_dashed else '-', zorder=4)
        ax.add_patch(ellipse)

        # Title
        ax.text(cx, cy + 0.45, f"{uc_id}: {title}", fontsize=7.8, fontweight='bold',
                color='#0F172A', ha='center', va='center', zorder=5)

        # Table badge
        bw, bh = rx * 1.6, 0.82
        badge = FancyBboxPatch((cx - bw/2, cy - 1.25), bw, bh,
                               boxstyle="round,pad=0.1,rounding_size=0.25",
                               facecolor="#F1F5F9", edgecolor="#CBD5E1", linewidth=0.7, zorder=5)
        ax.add_patch(badge)
        ax.text(cx, cy - 0.84, f"Tabel: {table_tag}", fontsize=6.6, fontweight='bold',
                color='#0369A1', ha='center', va='center', zorder=6)

        usecase_coords[uc_id] = (cx, cy, rx, ry)
        return (cx, cy)

    # 4. DRAW USE CASES IN CLEAN FUNCTIONAL CLUSTERS
    # We place Use Cases in 3 distinct columns:
    # Col 1 (x = 27.5): Master Data & Setup (Admin territory)
    # Col 2 (x = 49.5): Core Presensi, Izin, Supervisi, Pembinaan
    # Col 3 (x = 72.5): Included Extensions, Rekapitulasi & AI

    # --- TOP ROW: AUTHENTICATION (Shared) ---
    draw_uc("UC01", "Login & Kelola Sesi Multi-Role", "users, roles", 38.0, 84.5, rx=8.2, ry=1.9)
    draw_uc("UC02", "Kelola Profil & Biodata Pengguna", "users", 61.0, 84.5, rx=8.2, ry=1.9)

    # --- COLUMN 1: MASTER DATA & KURIKULUM (x = 27.5) ---
    draw_uc("UC03", "Kelola Tahun Ajaran, Jurusan & Kelas", "tahun_ajaran, jurusan, kelas", 27.5, 74.0, rx=8.2, ry=1.9)
    draw_uc("UC04", "Kelola Guru & Wali Kelas Data", "guru, wali_kelas_data, wali_kelas", 27.5, 64.0, rx=8.2, ry=1.9)
    draw_uc("UC05", "Kelola Master Peserta Didik", "siswa", 27.5, 54.0, rx=8.0, ry=1.9)
    draw_uc("UC06", "Kelola Mapel & Jadwal KBM", "mata_pelajaran, jadwal_mengajar", 27.5, 44.0, rx=8.2, ry=1.9)
    draw_uc("UC07", "Generate Barcode & Kartu RFID", "barcode_guru, rfid_cards, siswa", 27.5, 34.0, rx=8.2, ry=1.9)
    draw_uc("UC14", "Konfigurasi Sistem & Token API", "pengaturan_sistem", 27.5, 24.0, rx=8.2, ry=1.9)

    # --- COLUMN 2: CORE PRESENSI, IZIN & SUPERVISI (x = 49.5) ---
    draw_uc("UC08", "Scan Presensi QR (Masuk & Pulang)", "absensi_siswa, absensi_guru, absensi_wali_kelas", 49.5, 71.0, rx=8.8, ry=2.1)
    draw_uc("UC09", "Pengajuan & Approval Izin / Sakit", "pengajuan_izin", 49.5, 54.0, rx=8.5, ry=2.0)
    draw_uc("UC10", "Supervisi KBM & Jurnal Mengajar", "catatan_guru", 49.5, 39.0, rx=8.4, ry=2.0)
    draw_uc("UC11", "Catatan Pembinaan Karakter Siswa", "catatan_pembinaan", 49.5, 24.0, rx=8.5, ry=2.0)

    # --- COLUMN 3: DEPENDENCIES, REKAPITULASI & AI (x = 72.5) ---
    draw_uc("UC08.1", "Validasi Waktu & Dual-Sync Role", "absensi_guru, absensi_wali_kelas", 72.5, 77.0, rx=7.6, ry=1.8, border=INC_BORDER, is_dashed=True)
    draw_uc("UC08.2", "Push Notifikasi Presensi Instan", "absensi_siswa, pengaturan_sistem", 72.5, 65.0, rx=7.6, ry=1.8, border=INC_BORDER, is_dashed=True)
    draw_uc("UC09.1", "Sinkronisasi Rekapitulasi Izin", "absensi_siswa, absensi_guru", 72.5, 52.0, rx=7.6, ry=1.8, border=INC_BORDER, is_dashed=True)
    draw_uc("UC12", "Rekap Presensi & Ekspor Laporan", "absensi_siswa, guru, wali_kelas", 72.5, 38.0, rx=8.4, ry=2.0)
    draw_uc("UC13", "Konsultasi Informasi via Hadir.io", "pengaturan_sistem & 20 Tabel DB", 72.5, 24.0, rx=8.5, ry=2.0)
    draw_uc("UC13.1", "NLP Contextual Query Engine", "Konektor Model AI Nemotron", 72.5, 13.5, rx=7.6, ry=1.8, border=EXT_INC_BORDER, is_dashed=True)

    # 5. ORTHOGONAL & MANHATTAN ROUTING SYSTEM (ZERO INTERSECTIONS WITH ELLIPSES!)
    # Define channels/gutters in whitespace:
    # Gutter 1: x = 16.5 (between Left Actors and Column 1)
    # Gutter 2: x = 38.5 (between Column 1 and Column 2)
    # Horizontal Corridors: y = 80.0, y = 59.0, y = 49.0, y = 29.0, y = 18.0

    def draw_manhattan_line(x1, y1, x2, y2, waypoints=None, color=LINE_COLOR, lw=1.2, alpha=0.85):
        if waypoints is None:
            pts = [(x1, y1), (x2, y2)]
        else:
            pts = [(x1, y1)] + waypoints + [(x2, y2)]
        xs = [p[0] for p in pts]
        ys = [p[1] for p in pts]
        ax.plot(xs, ys, color=color, linewidth=lw, alpha=alpha, zorder=3)

    # Connect Admin (x=12.0, y=78.0) directly to Master Data (Column 1)
    # Admin is directly opposite UC03, UC04. Clean short lines!
    draw_manhattan_line(12.0, 78.0, 19.3, 74.0) # Admin -> UC03
    draw_manhattan_line(12.0, 78.0, 19.3, 64.0) # Admin -> UC04
    draw_manhattan_line(12.0, 78.0, 19.5, 54.0) # Admin -> UC05
    draw_manhattan_line(12.0, 78.0, 19.3, 44.0) # Admin -> UC06
    draw_manhattan_line(12.0, 78.0, 19.3, 34.0) # Admin -> UC07
    draw_manhattan_line(12.0, 78.0, 19.3, 24.0) # Admin -> UC14
    # Admin to UC01 (Login)
    draw_manhattan_line(12.0, 78.0, 29.8, 84.5, waypoints=[(16.0, 78.0), (16.0, 84.5)])

    # Connect Kepsek (x=12.0, y=62.0)
    # Kepsek to UC01 (Login)
    draw_manhattan_line(12.0, 62.0, 29.8, 84.5, waypoints=[(16.5, 62.0), (16.5, 84.5)])
    # Kepsek to UC10 (Supervisi): Route through horizontal corridor y=59.0 into Gutter 2 (x=38.5) -> UC10 (41.1, 39.0)
    draw_manhattan_line(12.0, 62.0, 41.1, 39.0, waypoints=[(17.0, 62.0), (17.0, 59.0), (38.5, 59.0), (38.5, 39.0)])
    # Kepsek to UC12 (Rekap): Route through Gutter 2 -> under UC10 -> to UC12 (64.1, 38.0)
    draw_manhattan_line(12.0, 62.0, 64.1, 38.0, waypoints=[(17.0, 62.0), (17.0, 59.0), (38.5, 59.0), (38.5, 33.0), (60.0, 33.0), (60.0, 38.0)])

    # Connect Wali Kelas (x=12.0, y=46.0)
    # Wali Kelas to UC01 (Login)
    draw_manhattan_line(12.0, 46.0, 29.8, 84.5, waypoints=[(16.0, 46.0), (16.0, 84.5)])
    # Wali Kelas to UC08 (Scan Presensi): Route through corridor y=49.0 -> Gutter 2 (x=38.5) -> to UC08 (40.7, 71.0)
    draw_manhattan_line(12.0, 46.0, 40.7, 71.0, waypoints=[(17.5, 46.0), (17.5, 49.0), (38.5, 49.0), (38.5, 71.0)])
    # Wali Kelas to UC09 (Izin): Route to UC09 (41.0, 54.0)
    draw_manhattan_line(12.0, 46.0, 41.0, 54.0, waypoints=[(17.5, 46.0), (17.5, 49.0), (38.5, 49.0), (38.5, 54.0)])
    # Wali Kelas to UC11 (Pembinaan): Route through corridor y=49.0 -> Gutter 2 -> UC11 (41.0, 24.0)
    draw_manhattan_line(12.0, 46.0, 41.0, 24.0, waypoints=[(17.5, 46.0), (17.5, 29.0), (38.5, 29.0), (38.5, 24.0)])

    # Connect Guru (x=12.0, y=30.0)
    # Guru to UC01 (Login)
    draw_manhattan_line(12.0, 30.0, 29.8, 84.5, waypoints=[(15.5, 30.0), (15.5, 84.5)])
    # Guru to UC08 (Presensi): Route through corridor y=29.0 -> Gutter 2 (x=39.0) -> to UC08 (40.7, 71.0)
    draw_manhattan_line(12.0, 30.0, 40.7, 71.0, waypoints=[(16.5, 30.0), (16.5, 29.0), (39.0, 29.0), (39.0, 71.0)])
    # Guru to UC09 (Izin): Route through Gutter 2 -> UC09 (41.0, 54.0)
    draw_manhattan_line(12.0, 30.0, 41.0, 54.0, waypoints=[(16.5, 30.0), (16.5, 29.0), (39.0, 29.0), (39.0, 54.0)])
    # Guru to UC10 (Jurnal KBM): Route through Gutter 2 -> UC10 (41.1, 39.0)
    draw_manhattan_line(12.0, 30.0, 41.1, 39.0, waypoints=[(16.5, 30.0), (16.5, 29.0), (39.0, 29.0), (39.0, 39.0)])

    # Connect Siswa (x=12.0, y=14.0)
    # Siswa to UC01 (Login)
    draw_manhattan_line(12.0, 14.0, 29.8, 84.5, waypoints=[(15.0, 14.0), (15.0, 84.5)])
    # Siswa to UC08 (Presensi): Route through bottom whitespace y=18.0 -> Gutter 2 (x=39.5) -> to UC08 (40.7, 71.0)
    draw_manhattan_line(12.0, 14.0, 40.7, 71.0, waypoints=[(16.0, 14.0), (16.0, 18.0), (39.5, 18.0), (39.5, 71.0)])
    # Siswa to UC09 (Izin): Route through Gutter 2 -> to UC09 (41.0, 54.0)
    draw_manhattan_line(12.0, 14.0, 41.0, 54.0, waypoints=[(16.0, 14.0), (16.0, 18.0), (39.5, 18.0), (39.5, 54.0)])
    # Siswa to UC11 (Pembinaan): Route directly to UC11 (41.0, 24.0)
    draw_manhattan_line(12.0, 14.0, 41.0, 24.0, waypoints=[(16.0, 14.0), (16.0, 18.0), (39.5, 18.0), (39.5, 24.0)])

    # 6. UML DEPENDENCIES (<<include>> and <<extend>>)
    def draw_dep(from_uc, to_uc, rel_text, color, is_extend=False):
        p1 = usecase_coords[from_uc]
        p2 = usecase_coords[to_uc]
        if not is_extend:
            x1, y1 = p1[0] + p1[2], p1[1]
            x2, y2 = p2[0] - p2[2], p2[1]
        else:
            x1, y1 = p1[0], p1[1] + p1[3]
            x2, y2 = p2[0], p2[1] - p2[3]

        ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                    arrowprops=dict(arrowstyle="->", color=color, lw=1.5, linestyle="--", shrinkA=2, shrinkB=2),
                    zorder=6)
        mx, my = (x1 + x2)/2, (y1 + y2)/2 + (0.5 if not is_extend else 0.0)
        ax.text(mx, my, rel_text, fontsize=6.8, fontweight='bold', color=color,
                ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none", alpha=0.9), zorder=7)

    draw_dep("UC08", "UC08.1", "<<include>>", INC_BORDER)
    draw_dep("UC08", "UC08.2", "<<include>>", INC_BORDER)
    draw_dep("UC09", "UC09.1", "<<include>>", INC_BORDER)
    draw_dep("UC13.1", "UC13", "<<extend>>", EXT_INC_BORDER, is_extend=True)

    # 7. CONNECT EXTERNAL ACTORS (Direct & Clean)
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

    # 8. BOTTOM FOOTER MATRIX & LEGEND
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
    print(f"[SUCCESS] Clean Use Case Diagram saved to: {output_path}")

if __name__ == "__main__":
    out_file = r"c:\xampp\htdocs\absen\Usecase_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    generate_clean_usecase(out_file)
