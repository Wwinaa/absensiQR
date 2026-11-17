import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Ellipse
import numpy as np

def generate_perfect_left_actors_usecase(output_path):
    fig, ax = plt.subplots(figsize=(32, 22), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    BG_COLOR = "#F8FAFC"
    CARD_BG = "#FFFFFF"
    PRIMARY_NAVY = "#0F172A"
    ACCENT_BLUE = "#0284C7"
    DARK_BLUE = "#1E3A8A"
    SLATE_BORDER = "#94A3B8"
    LIGHT_BORDER = "#CBD5E1"

    ACTOR_BG = "#EFF6FF"
    ACTOR_BORDER = "#1D4ED8"
    EXT_BG = "#FFFBEB"
    EXT_BORDER = "#D97706"
    UC_BORDER = "#2563EB"
    INC_COLOR = "#0D9488"
    EXT_COLOR = "#D97706"

    fig.patch.set_facecolor(BG_COLOR)
    ax.set_facecolor(BG_COLOR)

    # 1. HEADER BANNER
    banner = FancyBboxPatch((2, 93.0), 96, 5.5, boxstyle="round,pad=0.2,rounding_size=0.6",
                            facecolor=DARK_BLUE, edgecolor=ACCENT_BLUE, linewidth=2.5, zorder=2)
    ax.add_patch(banner)
    ax.text(50, 96.6, "USE CASE DIAGRAM TERPADU SISTEM PRESENSI SMK NU WARGABINANGUN",
            fontsize=18, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 94.8, "Arsitektur Fungsional Multi-Role 20 Tabel Database `db_absensi_smk` (5 Aktor Utama di Sisi Kiri)",
            fontsize=11, color='#BAE6FD', ha='center', va='center', zorder=3)
    ax.text(50, 93.5, "Standar UML 2.5 • Jalur Garis Terstruktur Bersih (100% Menempel Tepi Objek & Bebas Tabrakan Jalur)",
            fontsize=9, fontstyle='italic', color='#E2E8F0', ha='center', va='center', zorder=3)

    # 2. SYSTEM BOUNDARY (BOX)
    # Starts from x = 18.0 to x = 74.0 (width = 56.0)
    sys_box = FancyBboxPatch((18.0, 6.0), 56.0, 85.0, boxstyle="round,pad=0.3,rounding_size=0.8",
                             facecolor=CARD_BG, edgecolor=SLATE_BORDER, linewidth=2.0, zorder=1)
    ax.add_patch(sys_box)

    sys_header = FancyBboxPatch((18.0, 88.2), 56.0, 2.8, boxstyle="round,pad=0.1,rounding_size=0.4",
                                facecolor="#F1F5F9", edgecolor=LIGHT_BORDER, linewidth=1.2, zorder=2)
    ax.add_patch(sys_header)
    ax.text(46.0, 89.6, "BATAS SISTEM (SYSTEM BOUNDARY): SISTEM PRESENSI & KESISWAAN DIGITAL",
            fontsize=10.5, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=3)

    # Helper: Draw Human Actor on the Left
    actors = {}
    def draw_left_actor(key, name, role_desc, cy, cx=9.0, w=11.0, h=8.8):
        left = cx - w/2
        right = cx + w/2
        top = cy + h/2
        bottom = cy - h/2

        card = FancyBboxPatch((left, bottom), w, h, boxstyle="round,pad=0.12,rounding_size=0.35",
                              facecolor=ACTOR_BG, edgecolor=ACTOR_BORDER, linewidth=1.8, zorder=4)
        ax.add_patch(card)

        # Stick figure
        head_cy = cy + h/2 - 2.0
        head = plt.Circle((cx, head_cy), 0.8, facecolor="#FFFFFF", edgecolor=ACTOR_BORDER, linewidth=1.6, zorder=5)
        ax.add_patch(head)
        body_top = head_cy - 0.8
        body_bot = body_top - 1.9
        ax.plot([cx, cx], [body_top, body_bot], color=ACTOR_BORDER, lw=1.8, zorder=5)
        ax.plot([cx - 1.6, cx + 1.6], [body_top - 0.7, body_top - 0.7], color=ACTOR_BORDER, lw=1.8, zorder=5)
        ax.plot([cx, cx - 1.3], [body_bot, body_bot - 1.6], color=ACTOR_BORDER, lw=1.8, zorder=5)
        ax.plot([cx, cx + 1.3], [body_bot, body_bot - 1.6], color=ACTOR_BORDER, lw=1.8, zorder=5)

        ax.text(cx, bottom + 1.3, name, fontsize=8.0, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=5)
        ax.text(cx, bottom + 0.45, role_desc, fontsize=6.2, color='#475569', ha='center', va='center', zorder=5)

        act_info = {'key': key, 'cx': cx, 'cy': cy, 'left': left, 'right': right, 'top': top, 'bottom': bottom}
        actors[key] = act_info
        return act_info

    # Helper: Draw External Actor Box on the Right
    ext_actors = {}
    def draw_right_ext_actor(key, name, protocol, cy, cx=86.0, w=12.0, h=6.5):
        left = cx - w/2
        right = cx + w/2
        top = cy + h/2
        bottom = cy - h/2

        card = FancyBboxPatch((left, bottom), w, h, boxstyle="round,pad=0.15,rounding_size=0.4",
                              facecolor=EXT_BG, edgecolor=EXT_BORDER, linewidth=1.8, zorder=4)
        ax.add_patch(card)
        ax.text(cx, cy + 1.8, "<<Actor (External)>>", fontsize=6.2, fontstyle='italic', color=EXT_BORDER, ha='center', zorder=5)
        ax.text(cx, cy + 0.3, name, fontsize=8.0, fontweight='bold', color=PRIMARY_NAVY, ha='center', zorder=5)
        ax.text(cx, cy - 1.4, protocol, fontsize=6.6, color='#92400E', ha='center', zorder=5)

        ext_info = {'key': key, 'cx': cx, 'cy': cy, 'left': left, 'right': right, 'top': top, 'bottom': bottom}
        ext_actors[key] = ext_info
        return ext_info

    # 3. INSTANTIATE ALL 5 HUMAN ACTORS ON THE LEFT (As requested by User!)
    draw_left_actor("admin",      "ADMINISTRATOR",   "Superuser & Konfigurasi", cy=80.0)
    draw_left_actor("kepsek",     "KEPALA SEKOLAH",  "Eksekutif & Supervisi",    cy=63.5)
    draw_left_actor("wali_kelas", "WALI KELAS",      "Pembina Kelas & Siswa",    cy=48.0)
    draw_left_actor("guru",       "GURU PENGAJAR",   "Pendidik & KBM Harian",    cy=32.0)
    draw_left_actor("siswa",      "PESERTA DIDIK",   "Siswa SMK Terdaftar",      cy=15.5)

    # External Actors on the Right
    draw_right_ext_actor("telegram", "BOT TELEGRAM", "Webhook Instant Push", cy=22.0)
    draw_right_ext_actor("nim_ai",   "NVIDIA NIM AI", "Llama-3.1-Nemotron",   cy=8.5)

    # Helper: Draw Use Case Ellipse with exact attachment coordinates
    usecases = {}
    def draw_uc(code, title, subtitle, cx, cy, rx=8.5, ry=2.0, border_color=UC_BORDER, bg_color="#FFFFFF", is_dashed=False):
        el = Ellipse((cx, cy), rx*2, ry*2, facecolor=bg_color, edgecolor=border_color,
                     linewidth=1.5, linestyle="--" if is_dashed else "-", zorder=3)
        ax.add_patch(el)
        if subtitle:
            ax.text(cx, cy + 0.55, f"{code}: {title}", fontsize=7.2, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=4)
            ax.text(cx, cy - 0.65, subtitle, fontsize=5.8, color="#0369A1" if border_color==UC_BORDER else "#475569", ha='center', va='center', zorder=4)
        else:
            ax.text(cx, cy, f"{code}: {title}", fontsize=7.2, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=4)

        uc_info = {
            'code': code, 'cx': cx, 'cy': cy, 'rx': rx, 'ry': ry,
            'left': cx - rx, 'right': cx + rx, 'top': cy + ry, 'bottom': cy - ry
        }
        usecases[code] = uc_info
        return uc_info

    # 4. INSTANTIATE USE CASES IN COLUMN 1 (cx = 32.5) & COLUMN 2 (cx = 58.0)
    # Ordered Vertically to MATCH the Exact Roles of the 5 Actors on the Left!
    
    # --- BAND 1: ADMINISTRATOR ACTIONS (cy: 86 down to 68) ---
    draw_uc("UC01", "Kelola Data Akademik", "tahun_ajaran, jurusan, kelas", 32.5, 85.5)
    draw_uc("UC02", "Kelola Akun & Hak Akses", "users, roles (Multi-Role)", 32.5, 79.5)
    draw_uc("UC03", "Kelola Pendidik & Tenaga Kerja", "guru, wali_kelas_data", 32.5, 73.5)
    draw_uc("UC04", "Cetak Barcode & RFID Siswa/Guru", "barcode_guru, rfid_cards", 32.5, 67.5)

    # --- BAND 2: KEPALA SEKOLAH ACTIONS (cy: 61 down to 55) ---
    draw_uc("UC05", "Supervisi KBM & Jurnal Guru", "catatan_guru, jadwal_mengajar", 32.5, 61.5)
    draw_uc("UC06", "Rekapitulasi Presensi Eksekutif", "Laporan Rekap Senin - Jumat", 32.5, 55.5)

    # --- BAND 3: WALI KELAS ACTIONS (cy: 49 down to 37) ---
    draw_uc("UC07", "Monitoring Presensi Kelas", "Rekap Kehadiran Rombel Binaan", 32.5, 49.5)
    draw_uc("UC08", "Pencatatan Pembinaan Siswa", "catatan_pembinaan (Konseling)", 32.5, 43.5)
    draw_uc("UC09", "Approval Dispensasi & Izin", "Validasi Surat Izin / Sakit", 32.5, 37.5)

    # --- BAND 4: GURU PENGAJAR ACTIONS (cy: 31 down to 25) ---
    draw_uc("UC10", "Input Jurnal Mengajar KBM", "mata_pelajaran, jadwal_mengajar", 32.5, 31.5)
    draw_uc("UC11", "Presensi Scan Barcode/QR", "absensi_siswa, absensi_guru", 32.5, 25.5, bg_color="#ECFDF5", border_color="#10B981")

    # --- BAND 5: PESERTA DIDIK ACTIONS (cy: 19 down to 8) ---
    draw_uc("UC12", "Pengajuan Dispensasi Izin/Sakit", "pengajuan_izin (Upload Surat)", 32.5, 19.5)
    draw_uc("UC13", "Pantau Riwayat Kehadiran", "Kalender Presensi & Evaluasi Mandiri", 32.5, 14.0)
    draw_uc("UC14", "Tanya Jawab AI Hadir.io", "Chatbot Asisten Cerdas Sekolah", 32.5, 8.5, bg_color="#FFFBEB", border_color=EXT_BORDER)

    # --- COLUMN 2: CHILD INCLUSIONS & EXTENSIONS (cx = 58.0) ---
    draw_uc("UC11.1", "Validasi Jadwal & Waktu", "jadwal_mengajar, toleransi_jam", 58.0, 31.5, rx=8.0, ry=1.8, is_dashed=True, border_color=INC_COLOR)
    draw_uc("UC11.2", "Dual-Sync Absensi Otomatis", "absensi_wali_kelas (Auto-Sync)", 58.0, 26.5, rx=8.0, ry=1.8, is_dashed=True, border_color=INC_COLOR)
    draw_uc("UC11.3", "Broadcast Notifikasi Real-Time", "Otomasi Webhook Telegram", 58.0, 22.0, rx=8.0, ry=1.8, is_dashed=True, border_color=EXT_BORDER)
    draw_uc("UC12.1", "Upload Berkas Surat Dokter", "Storage & Validasi Lampiran", 58.0, 16.5, rx=8.0, ry=1.8, is_dashed=True, border_color=INC_COLOR)
    draw_uc("UC14.1", "Reasoning LLM 20 Tabel", "NVIDIA Nemotron Reasoning", 58.0, 8.5, rx=8.0, ry=1.8, is_dashed=True, border_color=EXT_BORDER)

    # 5. DRAW CLEAN ACTOR-TO-USECASE CONNECTIONS (ZERO CROSSING!)
    # All human actors are on the left (right border at x = 14.5).
    # All target use cases are in Column 1 (left border at x = 24.0).
    # Because they are ordered in the exact same vertical bands, lines NEVER CROSS!

    def connect_actor(actor_key, uc_key, y_offset=0.0):
        act = actors[actor_key]
        uc = usecases[uc_key]

        x1 = act['right']
        y1 = act['cy'] + y_offset
        x2 = uc['left']
        y2 = uc['cy']

        ax.plot([x1, x2], [y1, y2], color="#475569", lw=1.5, zorder=2)
        # End dots exactly on borders
        ax.plot(x1, y1, marker='o', markersize=3, color="#475569", zorder=3)
        ax.plot(x2, y2, marker='o', markersize=3, color="#475569", zorder=3)

    # A. ADMINISTRATOR (cy = 80.0) -> UC01, UC02, UC03, UC04
    connect_actor("admin", "UC01", y_offset=1.5)
    connect_actor("admin", "UC02", y_offset=0.5)
    connect_actor("admin", "UC03", y_offset=-0.5)
    connect_actor("admin", "UC04", y_offset=-1.5)

    # B. KEPALA SEKOLAH (cy = 63.5) -> UC05, UC06, UC09
    connect_actor("kepsek", "UC05", y_offset=0.8)
    connect_actor("kepsek", "UC06", y_offset=-0.8)

    # C. WALI KELAS (cy = 48.0) -> UC07, UC08, UC09
    connect_actor("wali_kelas", "UC07", y_offset=1.0)
    connect_actor("wali_kelas", "UC08", y_offset=0.0)
    connect_actor("wali_kelas", "UC09", y_offset=-1.0)

    # D. GURU PENGAJAR (cy = 32.0) -> UC10, UC11, UC12
    connect_actor("guru", "UC10", y_offset=1.0)
    connect_actor("guru", "UC11", y_offset=0.0)
    connect_actor("guru", "UC12", y_offset=-1.0)

    # E. PESERTA DIDIK (cy = 15.5) -> UC11, UC12, UC13, UC14
    connect_actor("siswa", "UC11", y_offset=1.5)
    connect_actor("siswa", "UC12", y_offset=0.5)
    connect_actor("siswa", "UC13", y_offset=-0.5)
    connect_actor("siswa", "UC14", y_offset=-1.5)

    # 6. DRAW UML DEPENDENCY ARROWS (<<include>> and <<extend>>)
    # From Column 1 to Column 2 (Horizontal & Clean)
    def draw_dependency(uc_src_code, uc_dst_code, text="<<include>>", color=INC_COLOR):
        src = usecases[uc_src_code]
        dst = usecases[uc_dst_code]

        x1 = src['right']
        y1 = src['cy']
        x2 = dst['left']
        y2 = dst['cy']

        ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                    arrowprops=dict(arrowstyle="->", color=color, lw=1.5, linestyle="--", shrinkA=2, shrinkB=2), zorder=4)
        mx = (x1 + x2) / 2
        my = (y1 + y2) / 2 + 0.35
        ax.text(mx, my, text, fontsize=6.8, fontweight='bold', color=color, ha='center', va='center',
                bbox=dict(boxstyle="round,pad=0.15", fc="#FFFFFF", ec="none", alpha=0.95), zorder=5)

    # UC11 (Presensi) -> UC11.1 (Validasi Jam)
    draw_dependency("UC11", "UC11.1", "<<include>>")
    # UC11 (Presensi) -> UC11.2 (Dual-Sync)
    draw_dependency("UC11", "UC11.2", "<<include>>")
    # UC11 (Presensi) -> UC11.3 (Push Telegram)
    draw_dependency("UC11", "UC11.3", "<<include>>", color=EXT_COLOR)
    # UC12 (Izin) -> UC12.1 (Upload Berkas)
    draw_dependency("UC12", "UC12.1", "<<include>>")
    # UC14.1 (AI LLM) -> UC14 (Tanya Jawab AI) [In UML extend arrow points to base usecase]
    draw_dependency("UC14", "UC14.1", "<<extend>>", color=EXT_COLOR)

    # 7. CONNECT EXTERNAL ACTORS ON THE RIGHT
    # Bot Telegram -> UC11.3 (Exact horizontal straight line!)
    tg = ext_actors["telegram"]
    uc_tg = usecases["UC11.3"]
    ax.plot([uc_tg['right'], tg['left']], [uc_tg['cy'], tg['cy']], color=EXT_BORDER, lw=1.6, zorder=3)
    ax.plot(uc_tg['right'], uc_tg['cy'], marker='o', markersize=3.5, color=EXT_BORDER, zorder=4)
    ax.plot(tg['left'], tg['cy'], marker='o', markersize=3.5, color=EXT_BORDER, zorder=4)
    ax.text((uc_tg['right'] + tg['left'])/2, tg['cy'] + 0.5, "Webhook Push", fontsize=6.5, fontweight='bold',
            color=EXT_BORDER, ha='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FEF3C7", ec="none"), zorder=5)

    # NVIDIA NIM -> UC14.1 (Exact horizontal straight line!)
    nim = ext_actors["nim_ai"]
    uc_nim = usecases["UC14.1"]
    ax.plot([uc_nim['right'], nim['left']], [uc_nim['cy'], nim['cy']], color=EXT_BORDER, lw=1.6, zorder=3)
    ax.plot(uc_nim['right'], uc_nim['cy'], marker='o', markersize=3.5, color=EXT_BORDER, zorder=4)
    ax.plot(nim['left'], nim['cy'], marker='o', markersize=3.5, color=EXT_BORDER, zorder=4)
    ax.text((uc_nim['right'] + nim['left'])/2, nim['cy'] + 0.5, "HTTPS REST", fontsize=6.5, fontweight='bold',
            color=EXT_BORDER, ha='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FEF3C7", ec="none"), zorder=5)

    # 8. INTEGRITY FOOTER MATRIX (20 Database Tables Verified)
    ftr = FancyBboxPatch((2, 0.8), 96, 4.8, boxstyle="round,pad=0.2,rounding_size=0.5",
                         facecolor="#FFFFFF", edgecolor=LIGHT_BORDER, linewidth=1.3, zorder=3)
    ax.add_patch(ftr)

    ax.text(3.5, 4.4, "VERIFIKASI INTEGRITAS 20 TABEL DATABASE `db_absensi_smk` DALAM USE CASE DIAGRAM:",
            fontsize=8.2, fontweight='bold', color=PRIMARY_NAVY, va='center')

    db_rows = [
        ("1. Keamanan & Akun:", "users, roles", "#1E3A8A"),
        ("2. Master Data Akademik:", "tahun_ajaran, jurusan, kelas, guru, wali_kelas_data, wali_kelas, siswa, mapel, jadwal", "#0369A1"),
        ("3. Identifikasi & Barcode:", "barcode_guru, rfid_cards", "#0D9488"),
        ("4. Presensi & Izin:", "absensi_siswa, absensi_guru, absensi_wali_kelas, pengajuan_izin", "#16A34A"),
        ("5. Supervisi & AI:", "catatan_guru, catatan_pembinaan, pengaturan_sistem", "#D97706")
    ]

    rx = 3.5
    for title, tbls, c in db_rows:
        ax.text(rx, 2.8, title, fontsize=6.8, fontweight='bold', color=c, va='center')
        ax.text(rx, 1.6, tbls, fontsize=6.0, color='#334155', va='center')
        rx += 19.0

    ax.text(88.0, 4.4, "STANDAR NOTASI UML 2.5:", fontsize=7.2, fontweight='bold', color=PRIMARY_NAVY, va='center')
    ax.text(88.0, 3.2, "─── Asosiasi Aktor", fontsize=6.2, color='#475569', va='center')
    ax.text(88.0, 2.2, "- - > <<include>> (Wajib)", fontsize=6.2, color=INC_COLOR, va='center')
    ax.text(88.0, 1.2, "- - > <<extend>> (Kondisional)", fontsize=6.2, color=EXT_COLOR, va='center')

    plt.tight_layout()
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print("[SUCCESS] Perfect Left-Actors Use Case Diagram saved to:", output_path)

if __name__ == "__main__":
    generate_perfect_left_actors_usecase(r"c:\xampp\htdocs\absen\Usecase_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png")
