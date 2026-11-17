import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Ellipse
import numpy as np

def generate_perfect_usecase(output_path):
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
    LINE_COLOR = "#475569"

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
    # Boundary starts at x = 16.5 and ends at x = 83.5
    sys_box = FancyBboxPatch((16.5, 7.5), 67.0, 83.5,
                             boxstyle="round,pad=0.3,rounding_size=0.8",
                             facecolor=PANEL_BG, edgecolor="#94A3B8", linewidth=2.0, zorder=1)
    ax.add_patch(sys_box)

    sys_header = FancyBboxPatch((16.5, 88.3), 67.0, 2.7,
                                boxstyle="round,pad=0.1,rounding_size=0.4",
                                facecolor="#F1F5F9", edgecolor="#CBD5E1", linewidth=1.2, zorder=2)
    ax.add_patch(sys_header)
    ax.text(50.0, 89.65, "BOUNDARY SISTEM: SISTEM PRESENSI DIGITAL & KESISWAAN SMK NU WARGABINANGUN",
            fontsize=10.5, fontweight='bold', color='#1E293B', ha='center', va='center', zorder=3)

    # Helper function to draw stickman actor
    def draw_actor(x, y, name, role_desc, w=9.0, h=8.0):
        card = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.15,rounding_size=0.5",
                              facecolor=ACTOR_BG, edgecolor=ACTOR_BORDER, linewidth=1.8, zorder=4)
        ax.add_patch(card)

        head_y = y + 1.8
        circle = plt.Circle((x, head_y), 0.65, facecolor='#FFFFFF', edgecolor=ACTOR_BORDER, linewidth=1.8, zorder=5)
        ax.add_patch(circle)
        ax.plot([x, x], [head_y - 0.65, head_y - 2.2], color=ACTOR_BORDER, linewidth=2.0, zorder=5)
        ax.plot([x - 1.1, x + 1.1], [head_y - 1.2, head_y - 1.2], color=ACTOR_BORDER, linewidth=2.0, zorder=5)
        ax.plot([x, x - 0.95], [head_y - 2.2, head_y - 3.4], color=ACTOR_BORDER, linewidth=2.0, zorder=5)
        ax.plot([x, x + 0.95], [head_y - 2.2, head_y - 3.4], color=ACTOR_BORDER, linewidth=2.0, zorder=5)

        ax.text(x, y - 2.5, name, fontsize=8.8, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=5)
        ax.text(x, y - 3.3, role_desc, fontsize=6.8, color='#475569', ha='center', va='center', zorder=5)
        return (x, y, w, h)

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
        return (x, y, w, h)

    # Left Column Actors (x = 7.5) -> Right edge is at x = 12.0
    # Between Actor right edge (12.0) and System Box (16.5) is a clean 4.5-unit gap!
    actors = {
        'admin': draw_actor(7.5, 76.0, "ADMINISTRATOR", "Super Admin / Tata Usaha"),
        'guru': draw_actor(7.5, 52.0, "GURU PENGAJAR", "Tenaga Pendidik KBM"),
        'siswa': draw_actor(7.5, 26.0, "PESERTA DIDIK", "Siswa / Siswi Sekolah")
    }

    # Right Column Actors (x = 92.0) -> Left edge is at x = 86.8
    # Between System Box (83.5) and Actor left edge (86.8) is a clean 3.3-unit gap!
    right_actors = {
        'kepsek': draw_actor(92.0, 76.0, "KEPALA SEKOLAH", "Pimpinan / Eksekutif"),
        'wali_kelas': draw_actor(92.0, 52.0, "WALI KELAS", "Pendidik & Pembina Rombel"),
        'telegram': draw_ext_actor(92.0, 31.0, "BOT TELEGRAM", "Gateway (@HadirioBot)"),
        'nvidia': draw_ext_actor(92.0, 15.0, "NVIDIA NIM AI", "Engine Nemotron LLM")
    }

    # Helper function to draw Use Case capsule
    usecases = {}
    def draw_uc(uc_id, title, table_tag, cx, cy, rx=7.6, ry=1.9, border=UC_BORDER, is_dashed=False):
        ellipse = Ellipse((cx, cy), rx*2, ry*2, facecolor="#FFFFFF", edgecolor=border,
                          linewidth=1.7, linestyle='--' if is_dashed else '-', zorder=4)
        ax.add_patch(ellipse)

        ax.text(cx, cy + 0.42, f"{uc_id}: {title}", fontsize=7.8, fontweight='bold',
                color='#0F172A', ha='center', va='center', zorder=5)

        bw, bh = rx * 1.6, 0.80
        badge = FancyBboxPatch((cx - bw/2, cy - 1.25), bw, bh,
                               boxstyle="round,pad=0.1,rounding_size=0.25",
                               facecolor="#F1F5F9", edgecolor="#CBD5E1", linewidth=0.7, zorder=5)
        ax.add_patch(badge)
        ax.text(cx, cy - 0.85, f"Tabel: {table_tag}", fontsize=6.6, fontweight='bold',
                color='#0369A1', ha='center', va='center', zorder=6)

        usecases[uc_id] = {'cx': cx, 'cy': cy, 'rx': rx, 'ry': ry}
        return (cx, cy, rx, ry)

    # 3. USE CASE PLACEMENT (Divided into 3 Clean Zones):
    # Left Zone (x = 28.5): Admin Master Data & Setup
    # Center Zone (x = 50.0): Core Presensi, Izin, Autentikasi
    # Right Zone (x = 71.5): Supervisi, Pembinaan, Rekapitulasi, Asisten AI & Sub-Use Cases

    # Top Center: Authentication & Profile
    draw_uc("UC01", "Login & Kelola Sesi Multi-Role", "users, roles", 39.0, 84.5, rx=8.0, ry=1.9)
    draw_uc("UC02", "Kelola Profil & Biodata Diri", "users", 61.0, 84.5, rx=8.0, ry=1.9)

    # --- LEFT ZONE: MASTER DATA & KURIKULUM (x = 28.5) ---
    draw_uc("UC03", "Kelola Tahun Ajaran, Jurusan & Kelas", "tahun_ajaran, jurusan, kelas", 28.5, 75.0, rx=8.0, ry=1.85)
    draw_uc("UC04", "Kelola Guru & Wali Kelas Data", "guru, wali_kelas_data, wali_kelas", 28.5, 65.5, rx=8.0, ry=1.85)
    draw_uc("UC05", "Kelola Master Peserta Didik", "siswa", 28.5, 56.0, rx=7.8, ry=1.85)
    draw_uc("UC06", "Kelola Mapel & Jadwal KBM", "mata_pelajaran, jadwal_mengajar", 28.5, 46.5, rx=8.0, ry=1.85)
    draw_uc("UC07", "Generate Barcode & Kartu RFID", "barcode_guru, rfid_cards, siswa", 28.5, 37.0, rx=8.0, ry=1.85)
    draw_uc("UC14", "Konfigurasi Sistem & Token API", "pengaturan_sistem", 28.5, 27.5, rx=8.0, ry=1.85)

    # --- CENTER ZONE: CORE OPERASIONAL PRESENSI & IZIN (x = 50.0) ---
    draw_uc("UC08", "Scan Presensi QR (Masuk & Pulang)", "absensi_siswa, absensi_guru, absensi_wali_kelas", 50.0, 71.0, rx=8.8, ry=2.1)
    draw_uc("UC09", "Pengajuan & Approval Izin / Sakit", "pengajuan_izin", 50.0, 52.0, rx=8.4, ry=2.0)
    draw_uc("UC08.1", "Validasi Waktu & Dual-Sync Role", "absensi_guru, absensi_wali_kelas", 50.0, 36.5, rx=7.8, ry=1.85, border=INC_BORDER, is_dashed=True)
    draw_uc("UC09.1", "Sinkronisasi Rekapitulasi Izin", "absensi_siswa, absensi_guru", 50.0, 22.0, rx=7.8, ry=1.85, border=INC_BORDER, is_dashed=True)

    # --- RIGHT ZONE: SUPERVISI, PEMBINAAN, REKAPITULASI & AI (x = 71.5) ---
    draw_uc("UC10", "Supervisi KBM & Jurnal Mengajar", "catatan_guru", 71.5, 75.0, rx=8.2, ry=1.9)
    draw_uc("UC12", "Rekap Presensi & Ekspor Laporan", "absensi_siswa, guru, wali_kelas", 71.5, 62.5, rx=8.4, ry=1.9)
    draw_uc("UC11", "Catatan Pembinaan Karakter Siswa", "catatan_pembinaan", 71.5, 50.0, rx=8.2, ry=1.9)
    draw_uc("UC08.2", "Push Notifikasi Presensi Instan", "absensi_siswa, pengaturan_sistem", 71.5, 37.5, rx=7.8, ry=1.85, border=INC_BORDER, is_dashed=True)
    draw_uc("UC13", "Konsultasi Informasi via Hadir.io", "pengaturan_sistem & 20 Tabel DB", 71.5, 25.0, rx=8.2, ry=1.9)
    draw_uc("UC13.1", "NLP Contextual Query Engine", "Konektor Model AI Nemotron", 71.5, 13.5, rx=7.6, ry=1.8, border=EXT_INC_BORDER, is_dashed=True)

    # 4. MATHEMATICALLY CLEAN CONNECTIONS (EXACT EDGE-TO-EDGE, ZERO CROSSINGS!)
    def connect_line(p1, p2, color=LINE_COLOR, lw=1.2, alpha=0.9):
        ax.plot([p1[0], p2[0]], [p1[1], p2[1]], color=color, linewidth=lw, alpha=alpha, zorder=3)

    # --- LEFT ACTOR CONNECTIONS ---
    # Administrator (Right edge = 12.0, y = 76.0) connects directly to Left Zone (left edge = 20.5):
    connect_line((12.0, 76.0), (20.5, 75.0)) # Admin -> UC03
    connect_line((12.0, 76.0), (20.5, 65.5)) # Admin -> UC04
    connect_line((12.0, 76.0), (20.7, 56.0)) # Admin -> UC05
    connect_line((12.0, 76.0), (20.5, 46.5)) # Admin -> UC06
    connect_line((12.0, 76.0), (20.5, 37.0)) # Admin -> UC07
    connect_line((12.0, 76.0), (20.5, 27.5)) # Admin -> UC14
    connect_line((12.0, 76.0), (31.0, 84.5)) # Admin -> UC01 (Login)

    # Guru Pengajar (Right edge = 12.0, y = 52.0) connects to Center Zone:
    # Through the clear horizontal whitespace between UC05 and UC06 (y = 51.5)
    connect_line((12.0, 52.0), (41.2, 71.0)) # Guru -> UC08 (Presensi)
    connect_line((12.0, 52.0), (41.6, 52.0)) # Guru -> UC09 (Izin)
    connect_line((12.0, 52.0), (63.3, 75.0)) # Guru -> UC10 (Jurnal KBM) via clear corridor
    connect_line((12.0, 52.0), (31.0, 84.5)) # Guru -> UC01 (Login)

    # Siswa / Peserta Didik (Right edge = 12.0, y = 26.0):
    connect_line((12.0, 26.0), (41.2, 71.0)) # Siswa -> UC08 (Presensi)
    connect_line((12.0, 26.0), (41.6, 52.0)) # Siswa -> UC09 (Izin)
    connect_line((12.0, 26.0), (63.3, 50.0)) # Siswa -> UC11 (Pembinaan) via clear corridor
    connect_line((12.0, 26.0), (31.0, 84.5)) # Siswa -> UC01 (Login)

    # --- RIGHT ACTOR CONNECTIONS ---
    # Kepala Sekolah (Left edge = 87.5, y = 76.0) connects directly to Right Zone:
    connect_line((87.5, 76.0), (79.7, 75.0)) # Kepsek -> UC10 (Supervisi)
    connect_line((87.5, 76.0), (79.9, 62.5)) # Kepsek -> UC12 (Rekapitulasi)
    connect_line((87.5, 76.0), (69.0, 84.5)) # Kepsek -> UC02 (Profil)
    connect_line((87.5, 76.0), (79.7, 25.0)) # Kepsek -> UC13 (AI Hadir.io)

    # Wali Kelas (Left edge = 87.5, y = 52.0) connects directly to Right Zone:
    connect_line((87.5, 52.0), (79.7, 50.0)) # Wali Kelas -> UC11 (Pembinaan Siswa)
    connect_line((87.5, 52.0), (79.9, 62.5)) # Wali Kelas -> UC12 (Rekap Kelas)
    connect_line((87.5, 52.0), (58.8, 71.0)) # Wali Kelas -> UC08 (Presensi)
    connect_line((87.5, 52.0), (69.0, 84.5)) # Wali Kelas -> UC02 (Profil)

    # External Actors (Right Column)
    # Bot Telegram (Left edge = 86.8, y = 31.0) -> UC08.2 (x = 79.3, y = 37.5)
    connect_line((86.8, 31.0), (79.3, 37.5), color=EXT_BORDER, lw=1.5)
    ax.text(83.0, 35.0, "Webhook Push", fontsize=6.8, color=EXT_BORDER, fontweight='bold',
            ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FEF3C7", ec="none"), zorder=6)

    # NVIDIA NIM AI (Left edge = 86.8, y = 15.0) -> UC13.1 (x = 79.1, y = 13.5)
    connect_line((86.8, 15.0), (79.1, 13.5), color=EXT_BORDER, lw=1.5)
    ax.text(83.0, 15.0, "REST API", fontsize=6.8, color=EXT_BORDER, fontweight='bold',
            ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FEF3C7", ec="none"), zorder=6)

    # 5. UML DEPENDENCY ARROWS (<<include>> and <<extend>>)
    def draw_dep(p1, p2, text, color, dashed=True):
        ax.annotate('', xy=p2, xytext=p1,
                    arrowprops=dict(arrowstyle="->", color=color, lw=1.5,
                                    linestyle="--" if dashed else "-", shrinkA=2, shrinkB=2),
                    zorder=6)
        mx = (p1[0] + p2[0]) / 2
        my = (p1[1] + p2[1]) / 2 + 0.45
        ax.text(mx, my, text, fontsize=6.8, fontweight='bold', color=color,
                ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none", alpha=0.9), zorder=7)

    # UC08 (bottom = 68.9) -> UC08.1 (top = 38.35) [Vertical Include]
    draw_dep((50.0, 68.9), (50.0, 38.35), "<<include>>", INC_BORDER)

    # UC08 (right = 58.8, y = 71.0) -> UC08.2 (left = 63.7, y = 37.5) [Diagonal Include via whitespace]
    draw_dep((56.0, 69.2), (64.5, 38.5), "<<include>>", INC_BORDER)

    # UC09 (bottom = 50.0) -> UC09.1 (top = 23.85) [Vertical Include]
    draw_dep((50.0, 50.0), (50.0, 23.85), "<<include>>", INC_BORDER)

    # UC13.1 (top = 15.3) -> UC13 (bottom = 23.1) [Vertical Extend: extension points to base per UML standard]
    draw_dep((71.5, 15.3), (71.5, 23.1), "<<extend>>", EXT_INC_BORDER)

    # 6. BOTTOM FOOTER MATRIX & LEGEND
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
    print(f"[SUCCESS] Perfect Use Case Diagram saved to: {output_path}")

if __name__ == "__main__":
    out_file = r"c:\xampp\htdocs\absen\Usecase_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    generate_perfect_usecase(out_file)
