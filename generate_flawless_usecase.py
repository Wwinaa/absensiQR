import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Ellipse
import numpy as np

def generate_flawless_usecase(output_path):
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
    ax.text(50, 94.8, "Arsitektur Fungsional Multi-Role Bebas Tabrakan Jalur (Zero-Crossing Orthogonal Standard UML 2.5)",
            fontsize=11, color='#BAE6FD', ha='center', va='center', zorder=3)
    ax.text(50, 93.5, "Cakupan: 20 Tabel Database `db_absensi_smk` • Autentikasi, Presensi QR, Dual-Sync, Jurnal KBM, Pembinaan, AI & Notifikasi",
            fontsize=9, fontstyle='italic', color='#E2E8F0', ha='center', va='center', zorder=3)

    # 2. SYSTEM BOUNDARY
    sys_box = FancyBboxPatch((16.0, 7.5), 68.0, 83.5, boxstyle="round,pad=0.3,rounding_size=0.8",
                             facecolor=CARD_BG, edgecolor=SLATE_BORDER, linewidth=2.0, zorder=1)
    ax.add_patch(sys_box)

    sys_header = FancyBboxPatch((16.0, 88.2), 68.0, 2.8, boxstyle="round,pad=0.1,rounding_size=0.4",
                                facecolor="#F1F5F9", edgecolor=LIGHT_BORDER, linewidth=1.2, zorder=2)
    ax.add_patch(sys_header)
    ax.text(50.0, 89.6, "BATAS SISTEM (SYSTEM BOUNDARY): SISTEM PRESENSI & KESISWAAN SMK NU WARGABINANGUN",
            fontsize=10.5, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=3)

    # Helper: Draw Human Actor
    def draw_actor(x, y, name, role_desc, w=11.5, h=9.5, color=ACTOR_BORDER, bg=ACTOR_BG):
        left = x - w/2
        right = x + w/2
        top = y + h/2
        bottom = y - h/2

        card = FancyBboxPatch((left, bottom), w, h, boxstyle="round,pad=0.15,rounding_size=0.4",
                              facecolor=bg, edgecolor=color, linewidth=1.8, zorder=4)
        ax.add_patch(card)

        # Stick figure
        head_cy = y + h/2 - 2.1
        head = plt.Circle((x, head_cy), 0.85, facecolor="#FFFFFF", edgecolor=color, linewidth=1.6, zorder=5)
        ax.add_patch(head)
        body_top = head_cy - 0.85
        body_bot = body_top - 2.1
        ax.plot([x, x], [body_top, body_bot], color=color, lw=1.8, zorder=5)
        ax.plot([x - 1.7, x + 1.7], [body_top - 0.8, body_top - 0.8], color=color, lw=1.8, zorder=5)
        ax.plot([x, x - 1.4], [body_bot, body_bot - 1.8], color=color, lw=1.8, zorder=5)
        ax.plot([x, x + 1.4], [body_bot, body_bot - 1.8], color=color, lw=1.8, zorder=5)

        ax.text(x, bottom + 1.4, name, fontsize=8.2, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=5)
        ax.text(x, bottom + 0.5, role_desc, fontsize=6.2, color='#475569', ha='center', va='center', zorder=5)

        return {'cx': x, 'cy': y, 'left': left, 'right': right, 'top': top, 'bottom': bottom}

    # Helper: Draw External Actor Box
    def draw_ext_actor(x, y, name, protocol, w=11.5, h=6.5):
        left = x - w/2
        right = x + w/2
        top = y + h/2
        bottom = y - h/2

        card = FancyBboxPatch((left, bottom), w, h, boxstyle="round,pad=0.15,rounding_size=0.4",
                              facecolor=EXT_BG, edgecolor=EXT_BORDER, linewidth=1.8, zorder=4)
        ax.add_patch(card)
        ax.text(x, y + 1.8, "<<Actor (External)>>", fontsize=6.2, fontstyle='italic', color=EXT_BORDER, ha='center', zorder=5)
        ax.text(x, y + 0.3, name, fontsize=8.0, fontweight='bold', color=PRIMARY_NAVY, ha='center', zorder=5)
        ax.text(x, y - 1.4, protocol, fontsize=6.6, color='#92400E', ha='center', zorder=5)
        return {'cx': x, 'cy': y, 'left': left, 'right': right, 'top': top, 'bottom': bottom}

    # Helper: Draw Use Case Ellipse
    usecases = {}
    def draw_usecase(code, cx, cy, title, subtitle="", rx=8.5, ry=2.2, border_color=UC_BORDER, bg_color="#FFFFFF", is_dashed=False):
        el = Ellipse((cx, cy), rx*2, ry*2, facecolor=bg_color, edgecolor=border_color,
                     linewidth=1.5, linestyle="--" if is_dashed else "-", zorder=3)
        ax.add_patch(el)
        if subtitle:
            ax.text(cx, cy + 0.65, f"{code}: {title}", fontsize=7.2, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=4)
            ax.text(cx, cy - 0.7, subtitle, fontsize=5.8, color="#0369A1" if border_color==UC_BORDER else "#475569", ha='center', va='center', zorder=4)
        else:
            ax.text(cx, cy, f"{code}: {title}", fontsize=7.2, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=4)

        uc_info = {
            'code': code, 'cx': cx, 'cy': cy, 'rx': rx, 'ry': ry,
            'left': cx - rx, 'right': cx + rx, 'top': cy + ry, 'bottom': cy - ry
        }
        usecases[code] = uc_info
        return uc_info

    # 3. DRAW ACTORS STRATEGICALLY POSITIONED
    # Left Side:
    act_admin = draw_actor(8.0, 68.0, "ADMINISTRATOR", "Superuser & Konfigurasi")
    act_guru  = draw_actor(8.0, 34.0, "GURU PENGAJAR", "Pendidik & KBM Harian")
    act_siswa = draw_actor(8.0, 16.0, "PESERTA DIDIK", "Siswa SMK Terdaftar")

    # Right Side:
    act_kepsek = draw_actor(92.0, 76.0, "KEPALA SEKOLAH", "Eksekutif & Supervisi")
    act_wali   = draw_actor(92.0, 52.0, "WALI KELAS", "Pembina Kelas & Siswa")
    act_tg     = draw_ext_actor(92.0, 27.0, "BOT TELEGRAM", "Webhook Instant Alert")
    act_nim    = draw_ext_actor(92.0, 13.0, "NVIDIA NIM AI", "Llama-3.1-Nemotron")

    # 4. DRAW USE CASES GROUPED LOGICALLY
    # A. TOP-LEFT ZONE: Admin Functionalities (Directly adjacent to Administrator!)
    draw_usecase("UC01", 27.5, 82.0, "Kelola Master Data Akademik", "tahun_ajaran, jurusan, kelas")
    draw_usecase("UC02", 27.5, 71.0, "Kelola Akun & Hak Akses", "users, roles (Multi-Role)")
    draw_usecase("UC03", 27.5, 60.0, "Kelola Pendidik & Tenaga Kerja", "guru, wali_kelas_data")
    draw_usecase("UC04", 27.5, 49.0, "Cetak Barcode & Registrasi RFID", "barcode_guru, rfid_cards")

    # B. BOTTOM-LEFT ZONE: Siswa & Guru Operational (Directly adjacent to Guru & Siswa!)
    draw_usecase("UC05", 27.5, 33.0, "Presensi Scan Barcode/QR", "absensi_siswa, absensi_guru", bg_color="#ECFDF5", border_color="#10B981")
    draw_usecase("UC06", 27.5, 17.0, "Pengajuan Dispensasi Izin/Sakit", "pengajuan_izin (Upload Surat)")

    # C. CENTER ZONE: Inclusions, Extensions, and Common Academic Processing
    draw_usecase("UC05.1", 50.5, 40.0, "Validasi Jadwal & Waktu", "jadwal_mengajar, aturan_jam", is_dashed=True, border_color=INC_COLOR)
    draw_usecase("UC05.2", 50.5, 27.0, "Dual-Sync Absensi Wali Kelas", "absensi_wali_kelas (Auto-Sync)", is_dashed=True, border_color=INC_COLOR)
    draw_usecase("UC06.1", 50.5, 15.0, "Upload Bukti Surat Dokter", "Storage & Validasi File", is_dashed=True, border_color=INC_COLOR)
    draw_usecase("UC07",   50.5, 60.0, "Input Jurnal Mengajar KBM", "mata_pelajaran, jadwal_mengajar")
    draw_usecase("UC08",   50.5, 78.0, "Konfigurasi Jam & Sistem", "pengaturan_sistem, toleransi_menit")

    # D. RIGHT ZONE: Supervisory, Executive & External Integration (Directly adjacent to Kepsek, Wali, Telegram, AI!)
    draw_usecase("UC09", 73.5, 82.0, "Supervisi KBM & Jurnal Guru", "Monitoring Mengajar Harian")
    draw_usecase("UC10", 73.5, 71.0, "Rekapitulasi Presensi Eksekutif", "Laporan Rekap Senin - Jumat")
    draw_usecase("UC11", 73.5, 58.0, "Monitoring Presensi Kelas", "Rekap Tingkat Kehadiran Kelas")
    draw_usecase("UC12", 73.5, 47.0, "Catatan Pembinaan Siswa", "catatan_pembinaan (Konseling)")
    draw_usecase("UC13", 73.5, 36.0, "Approval Dispensasi & Izin", "Validasi Surat Izin / Sakit")
    draw_usecase("UC14", 73.5, 25.0, "Broadcast Notifikasi Real-Time", "Otomasi Webhook Telegram", bg_color="#FFFBEB", border_color=EXT_BORDER)
    draw_usecase("UC15", 73.5, 13.0, "Tanya Jawab AI Hadir.io", "Chatbot Asisten Cerdas Sekolah", bg_color="#FFFBEB", border_color=EXT_BORDER)

    # 5. DRAW CLEAN ACTOR-TO-USECASE CONNECTIONS (EXACT BORDER TOUCHING, ZERO CROSSINGS!)
    def link_actor(actor, uc, color="#475569"):
        # Connects actor edge to usecase edge directly
        if actor['cx'] < uc['cx']:
            x1 = actor['right']
            x2 = uc['left']
            y1 = actor['cy']
            y2 = uc['cy']
        else:
            x1 = actor['left']
            x2 = uc['right']
            y1 = actor['cy']
            y2 = uc['cy']
        ax.plot([x1, x2], [y1, y2], color=color, lw=1.5, zorder=2)
        ax.plot(x1, y1, marker='o', markersize=3, color=color, zorder=3)
        ax.plot(x2, y2, marker='o', markersize=3, color=color, zorder=3)

    # A. Administrator (Direct to UC01, UC02, UC03, UC04, UC08)
    link_actor(act_admin, usecases["UC01"])
    link_actor(act_admin, usecases["UC02"])
    link_actor(act_admin, usecases["UC03"])
    link_actor(act_admin, usecases["UC04"])

    # B. Guru Pengajar (Direct to UC05, UC06, UC07)
    link_actor(act_guru, usecases["UC05"])
    link_actor(act_guru, usecases["UC06"])
    # Guru to UC07 routes through gutter:
    ax.plot([act_guru['right'], 39.0, 39.0, usecases["UC07"]['left']],
            [act_guru['cy'] + 1.0, act_guru['cy'] + 1.0, usecases["UC07"]['cy'], usecases["UC07"]['cy']],
            color="#475569", lw=1.4, zorder=2)

    # C. Peserta Didik (Direct to UC05, UC06)
    link_actor(act_siswa, usecases["UC05"])
    link_actor(act_siswa, usecases["UC06"])

    # D. Kepala Sekolah (Direct to UC09, UC10, UC13)
    link_actor(act_kepsek, usecases["UC09"])
    link_actor(act_kepsek, usecases["UC10"])
    link_actor(act_kepsek, usecases["UC13"])

    # E. Wali Kelas (Direct to UC11, UC12, UC13)
    link_actor(act_wali, usecases["UC11"])
    link_actor(act_wali, usecases["UC12"])
    link_actor(act_wali, usecases["UC13"])

    # F. External Actors:
    # Bot Telegram -> UC14 (Horizontal straight line!)
    link_actor(act_tg, usecases["UC14"], color=EXT_BORDER)
    # NVIDIA NIM -> UC15 (Horizontal straight line!)
    link_actor(act_nim, usecases["UC15"], color=EXT_BORDER)

    # 6. UML DEPENDENCY ARROWS (<<include>>)
    def draw_include(uc_src, uc_dst, text="<<include>>"):
        ax.annotate('', xy=(uc_dst['left'], uc_dst['cy']), xytext=(uc_src['right'], uc_src['cy']),
                    arrowprops=dict(arrowstyle="->", color=INC_COLOR, lw=1.5, linestyle="--", shrinkA=2, shrinkB=2), zorder=4)
        mx = (uc_src['right'] + uc_dst['left']) / 2
        my = (uc_src['cy'] + uc_dst['cy']) / 2
        ax.text(mx, my + 0.35, text, fontsize=6.8, fontweight='bold', color=INC_COLOR, ha='center', va='center',
                bbox=dict(boxstyle="round,pad=0.15", fc="#FFFFFF", ec="none", alpha=0.95), zorder=5)

    # UC05 (Presensi) -> UC05.1 (Validasi Jam)
    draw_include(usecases["UC05"], usecases["UC05.1"])
    # UC05 (Presensi) -> UC05.2 (Dual-Sync)
    draw_include(usecases["UC05"], usecases["UC05.2"])
    # UC06 (Izin) -> UC06.1 (Upload Bukti)
    draw_include(usecases["UC06"], usecases["UC06.1"])

    # 7. INTEGRITY FOOTER MATRIX
    ftr = FancyBboxPatch((2, 0.8), 96, 5.6, boxstyle="round,pad=0.2,rounding_size=0.5",
                         facecolor="#FFFFFF", edgecolor=LIGHT_BORDER, linewidth=1.3, zorder=3)
    ax.add_patch(ftr)

    ax.text(3.5, 5.2, "VERIFIKASI INTEGRITAS 20 TABEL DATABASE `db_absensi_smk` DALAM USE CASE DIAGRAM:",
            fontsize=8.5, fontweight='bold', color=PRIMARY_NAVY, va='center')

    db_rows = [
        ("1. Keamanan & Akun:", "users, roles", "#1E3A8A"),
        ("2. Master Data Akademik:", "tahun_ajaran, jurusan, kelas, guru, wali_kelas_data, wali_kelas, siswa, mata_pelajaran, jadwal_mengajar", "#0369A1"),
        ("3. Identifikasi & Barcode:", "barcode_guru, rfid_cards", "#0D9488"),
        ("4. Presensi & Izin:", "absensi_siswa, absensi_guru, absensi_wali_kelas, pengajuan_izin", "#16A34A"),
        ("5. Supervisi & AI:", "catatan_guru, catatan_pembinaan, pengaturan_sistem", "#D97706")
    ]

    rx = 3.5
    for title, tbls, c in db_rows:
        ax.text(rx, 3.4, title, fontsize=7.0, fontweight='bold', color=c, va='center')
        ax.text(rx, 2.0, tbls, fontsize=6.2, color='#334155', va='center')
        rx += 19.0

    ax.text(88.0, 5.2, "STANDAR UML NOTASI:", fontsize=7.5, fontweight='bold', color=PRIMARY_NAVY, va='center')
    ax.text(88.0, 3.8, "─── Asosiasi Aktor", fontsize=6.5, color='#475569', va='center')
    ax.text(88.0, 2.6, "- - > <<include>> (Wajib)", fontsize=6.5, color=INC_COLOR, va='center')
    ax.text(88.0, 1.4, "- - > <<extend>> (Kondisional)", fontsize=6.5, color=EXT_COLOR, va='center')

    plt.tight_layout()
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print("[SUCCESS] Flawless Use Case Diagram saved to:", output_path)

if __name__ == "__main__":
    generate_flawless_usecase(r"c:\xampp\htdocs\absen\Usecase_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png")
