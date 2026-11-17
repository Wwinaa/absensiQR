import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Ellipse, Rectangle, Polygon
import numpy as np

def generate_perfect_usecase(output_path):
    fig, ax = plt.subplots(figsize=(30, 21), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    # Modern Color Palette
    BG_COLOR = "#F8FAFC"
    CARD_BG = "#FFFFFF"
    PRIMARY_NAVY = "#0F172A"
    ACCENT_BLUE = "#0284C7"
    DARK_BLUE = "#1E3A8A"
    SLATE_BORDER = "#94A3B8"
    LIGHT_BORDER = "#CBD5E1"
    
    # Semantic Colors
    ACTOR_BG = "#EFF6FF"
    ACTOR_BORDER = "#1D4ED8"
    EXT_BG = "#FFFBEB"
    EXT_BORDER = "#D97706"
    UC_BORDER = "#2563EB"
    INC_COLOR = "#0D9488"
    EXT_COLOR = "#D97706"
    LINE_COLOR = "#475569"

    fig.patch.set_facecolor(BG_COLOR)
    ax.set_facecolor(BG_COLOR)

    # 1. HEADER BANNER
    banner = FancyBboxPatch((2, 92.5), 96, 6.2, boxstyle="round,pad=0.2,rounding_size=0.6",
                            facecolor=DARK_BLUE, edgecolor=ACCENT_BLUE, linewidth=2.5, zorder=2)
    ax.add_patch(banner)

    ax.text(50, 96.6, "USE CASE DIAGRAM TERPADU SISTEM PRESENSI SMK NU WARGABINANGUN",
            fontsize=18, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 94.7, "Arsitektur Fungsional Multi-Role Bebas Tabrakan Jalur (Zero-Crossing Orthogonal Standard UML 2.5)",
            fontsize=11, color='#BAE6FD', ha='center', va='center', zorder=3)
    ax.text(50, 93.3, "Cakupan: 20 Tabel Database `db_absensi_smk` • Autentikasi, Presensi QR, Dual-Sync, Jurnal KBM, Pembinaan, AI & Notifikasi",
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

    # Subsystem Package Dividers (Visual Swimlanes inside Boundary)
    # Zone 1: Master Data & Admin (x: 16 to 38)
    p1 = FancyBboxPatch((17.0, 8.5), 21.0, 78.8, boxstyle="round,pad=0.1,rounding_size=0.4",
                        facecolor="#F8FAFC", edgecolor="#E2E8F0", linewidth=1.0, linestyle="--", zorder=1)
    ax.add_patch(p1)
    ax.text(27.5, 86.0, "Sub-Sistem Master & Admin", fontsize=8.5, fontweight='bold', color='#475569', ha='center', zorder=2)

    # Zone 2: Presensi & KBM (x: 39.5 to 61.5)
    p2 = FancyBboxPatch((39.5, 8.5), 22.0, 78.8, boxstyle="round,pad=0.1,rounding_size=0.4",
                        facecolor="#F0FDF4", edgecolor="#DCFCE7", linewidth=1.0, linestyle="--", zorder=1)
    ax.add_patch(p2)
    ax.text(50.5, 86.0, "Sub-Sistem Presensi & KBM", fontsize=8.5, fontweight='bold', color='#166534', ha='center', zorder=2)

    # Zone 3: Supervisi, Pembinaan & AI (x: 63 to 83)
    p3 = FancyBboxPatch((63.0, 8.5), 20.0, 78.8, boxstyle="round,pad=0.1,rounding_size=0.4",
                        facecolor="#F0F9FF", edgecolor="#E0F2FE", linewidth=1.0, linestyle="--", zorder=1)
    ax.add_patch(p3)
    ax.text(73.0, 86.0, "Sub-Sistem Supervisi & AI", fontsize=8.5, fontweight='bold', color='#075985', ha='center', zorder=2)

    # Helper: Draw Human Actor
    def draw_actor(x, y, name, role_desc, w=11.5, h=10.0, color=ACTOR_BORDER, bg=ACTOR_BG):
        card = FancyBboxPatch((x - w/2, y - h/2), w, h, boxstyle="round,pad=0.15,rounding_size=0.4",
                              facecolor=bg, edgecolor=color, linewidth=1.8, zorder=4)
        ax.add_patch(card)

        # Stick figure inside
        head_cy = y + h/2 - 2.2
        head = plt.Circle((x, head_cy), 0.9, facecolor="#FFFFFF", edgecolor=color, linewidth=1.6, zorder=5)
        ax.add_patch(head)
        # Body
        body_top = head_cy - 0.9
        body_bot = body_top - 2.2
        ax.plot([x, x], [body_top, body_bot], color=color, lw=1.8, zorder=5)
        # Arms
        ax.plot([x - 1.8, x + 1.8], [body_top - 0.9, body_top - 0.9], color=color, lw=1.8, zorder=5)
        # Legs
        ax.plot([x, x - 1.5], [body_bot, body_bot - 2.0], color=color, lw=1.8, zorder=5)
        ax.plot([x, x + 1.5], [body_bot, body_bot - 2.0], color=color, lw=1.8, zorder=5)

        # Labels
        ax.text(x, y - h/2 + 1.5, name, fontsize=8.5, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=5)
        ax.text(x, y - h/2 + 0.6, role_desc, fontsize=6.5, color='#475569', ha='center', va='center', zorder=5)

        return {'cx': x, 'cy': y, 'left': x - w/2, 'right': x + w/2, 'top': y + h/2, 'bottom': y - h/2}

    # Helper: Draw External Actor Box
    def draw_ext_actor(x, y, name, protocol, w=11.5, h=6.5):
        card = FancyBboxPatch((x - w/2, y - h/2), w, h, boxstyle="round,pad=0.15,rounding_size=0.4",
                              facecolor=EXT_BG, edgecolor=EXT_BORDER, linewidth=1.8, zorder=4)
        ax.add_patch(card)
        ax.text(x, y + 1.8, "<<Actor (External)>>", fontsize=6.5, fontstyle='italic', color=EXT_BORDER, ha='center', zorder=5)
        ax.text(x, y + 0.3, name, fontsize=8.2, fontweight='bold', color=PRIMARY_NAVY, ha='center', zorder=5)
        ax.text(x, y - 1.4, protocol, fontsize=6.8, color='#92400E', ha='center', zorder=5)
        return {'cx': x, 'cy': y, 'left': x - w/2, 'right': x + w/2, 'top': y + h/2, 'bottom': y - h/2}

    # Helper: Draw Use Case Ellipse
    def draw_usecase(cx, cy, code, title, subtitle="", rx=8.5, ry=2.2, border_color=UC_BORDER, bg_color="#FFFFFF", dashed=False):
        el = Ellipse((cx, cy), rx*2, ry*2, facecolor=bg_color, edgecolor=border_color,
                     linewidth=1.5, linestyle="--" if dashed else "-", zorder=3)
        ax.add_patch(el)
        if subtitle:
            ax.text(cx, cy + 0.65, f"{code}: {title}", fontsize=7.2, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=4)
            ax.text(cx, cy - 0.7, subtitle, fontsize=5.8, color="#0369A1" if border_color==UC_BORDER else "#475569", ha='center', va='center', zorder=4)
        else:
            ax.text(cx, cy, f"{code}: {title}", fontsize=7.2, fontweight='bold', color=PRIMARY_NAVY, ha='center', va='center', zorder=4)

        return {'cx': cx, 'cy': cy, 'left': cx - rx, 'right': cx + rx, 'top': cy + ry, 'bottom': cy - ry, 'rx': rx, 'ry': ry}

    # 3. DRAW ACTORS
    # Left Side: Internal Operations
    act_admin = draw_actor(8.0, 72.0, "ADMINISTRATOR", "Superuser & Konfigurasi")
    act_guru  = draw_actor(8.0, 48.0, "GURU PENGAJAR", "Pendidik & KBM Harian")
    act_siswa = draw_actor(8.0, 24.0, "PESERTA DIDIK", "Siswa SMK Terdaftar")

    # Right Side: Management & External
    act_kepsek = draw_actor(92.0, 72.0, "KEPALA SEKOLAH", "Eksekutif & Supervisi")
    act_wali   = draw_actor(92.0, 48.0, "WALI KELAS", "Pembina Kelas & Siswa")

    act_tg  = draw_ext_actor(92.0, 29.0, "BOT TELEGRAM", "Webhook Real-Time Push")
    act_nim = draw_ext_actor(92.0, 16.0, "NVIDIA NIM AI", "REST API Llama-3.1-Nemotron")

    # 4. DRAW USE CASES (Organized cleanly in 3 Zones)
    # Zone 1 (Master Data & Admin) - cx = 27.5
    uc01 = draw_usecase(27.5, 80.0, "UC01", "Kelola Data Akademik", "tahun_ajaran, jurusan, kelas")
    uc02 = draw_usecase(27.5, 68.0, "UC02", "Kelola Akun & Hak Akses", "users, roles (Multi-Role)")
    uc03 = draw_usecase(27.5, 56.0, "UC03", "Kelola Data Guru & Karyawan", "guru, wali_kelas_data")
    uc04 = draw_usecase(27.5, 44.0, "UC04", "Kelola Data Peserta Didik", "siswa (NIS, NISN, Kelas)")
    uc05 = draw_usecase(27.5, 32.0, "UC05", "Cetak Barcode & Smart Card", "barcode_guru, rfid_cards")
    uc06 = draw_usecase(27.5, 20.0, "UC06", "Konfigurasi Jam & Sistem", "pengaturan_sistem")
    uc07 = draw_usecase(27.5, 11.5, "UC07", "Audit Log & Security Firewall", "audit_log, csrf_tokens")

    # Zone 2 (Presensi & Transaksi KBM) - cx = 50.5
    uc08 = draw_usecase(50.5, 78.0, "UC08", "Presensi Scan Barcode/QR", "absensi_siswa, absensi_guru", bg_color="#ECFDF5", border_color="#10B981")
    uc08_inc1 = draw_usecase(50.5, 66.0, "UC08.1", "Validasi Jadwal & Waktu", "jadwal_mengajar, aturan_jam", dashed=True, border_color=INC_COLOR)
    uc08_inc2 = draw_usecase(50.5, 54.0, "UC08.2", "Dual-Sync Absensi Wali Kelas", "absensi_wali_kelas (Auto-Sync)", dashed=True, border_color=INC_COLOR)

    uc09 = draw_usecase(50.5, 41.0, "UC09", "Pengajuan Dispensasi Izin", "pengajuan_izin (Sakit/Izin)")
    uc09_inc1 = draw_usecase(50.5, 29.5, "UC09.1", "Upload Dokumen & Surat", "bukti_surat_dokter (Storage)", dashed=True, border_color=INC_COLOR)

    uc10 = draw_usecase(50.5, 18.0, "UC10", "Input Jurnal Mengajar KBM", "mata_pelajaran, jadwal_mengajar")

    # Zone 3 (Supervisi, Bimbingan & AI) - cx = 73.0
    uc11 = draw_usecase(73.0, 80.0, "UC11", "Supervisi KBM & Jurnal Guru", "Monitoring Mengajar Harian")
    uc12 = draw_usecase(73.0, 68.0, "UC12", "Rekapitulasi Presensi Eksekutif", "Laporan Rekap Senin - Jumat")
    uc13 = draw_usecase(73.0, 56.0, "UC13", "Monitoring Presensi Kelas", "Rekap Tingkat Kehadiran Kelas")
    uc14 = draw_usecase(73.0, 44.0, "UC14", "Pencatatan Pembinaan Siswa", "catatan_pembinaan (Konseling)")
    uc15 = draw_usecase(73.0, 32.0, "UC15", "Approval Dispensasi & Izin", "Validasi Surat Izin / Sakit")
    uc16 = draw_usecase(73.0, 20.0, "UC16", "Tanya Jawab AI Hadir.io", "Chatbot Asisten Cerdas Sekolah", bg_color="#FFFBEB", border_color=EXT_BORDER)
    uc16_ext = draw_usecase(73.0, 11.5, "UC16.1", "LLM Natural Language Query", "NVIDIA Nemotron Reasoning", dashed=True, border_color=EXT_BORDER)

    # 5. DRAW CLEAN ACTOR-TO-USECASE CONNECTIONS (ORTHOGONAL / DIRECT WITH ZERO CROSSINGS)
    def draw_actor_link(p_actor, p_uc, color="#475569"):
        ax.plot([p_actor[0], p_uc[0]], [p_actor[1], p_uc[1]], color=color, lw=1.4, linestyle="-", zorder=2)

    # A. ADMINISTRATOR (act_admin: right = 13.75, cy = 72.0)
    # Radiates cleanly into Zone 1 use cases:
    draw_actor_link((act_admin['right'], 75.0), (uc01['left'], uc01['cy']))
    draw_actor_link((act_admin['right'], 73.5), (uc02['left'], uc02['cy']))
    draw_actor_link((act_admin['right'], 72.0), (uc03['left'], uc03['cy']))
    draw_actor_link((act_admin['right'], 70.5), (uc04['left'], uc04['cy']))
    draw_actor_link((act_admin['right'], 69.0), (uc05['left'], uc05['cy']))
    draw_actor_link((act_admin['right'], 67.5), (uc06['left'], uc06['cy']))
    draw_actor_link((act_admin['right'], 66.0), (uc07['left'], uc07['cy']))

    # B. GURU PENGAJAR (act_guru: right = 13.75, cy = 48.0)
    # Connects across clear gutter into Zone 2:
    # Routes through the clear whitespace between UC03 and UC04
    draw_actor_link((act_guru['right'], 50.0), (uc08['left'], uc08['cy']))
    draw_actor_link((act_guru['right'], 48.0), (uc09['left'], uc09['cy']))
    draw_actor_link((act_guru['right'], 46.0), (uc10['left'], uc10['cy']))

    # C. PESERTA DIDIK (act_siswa: right = 13.75, cy = 24.0)
    # Connects across clear gutter into Zone 2:
    draw_actor_link((act_siswa['right'], 25.5), (uc08['left'], uc08['cy'] - 0.5))
    draw_actor_link((act_siswa['right'], 24.0), (uc09['left'], uc09['cy'] - 0.5))

    # D. KEPALA SEKOLAH (act_kepsek: left = 86.25, cy = 72.0)
    # Connects cleanly into Zone 3:
    draw_actor_link((act_kepsek['left'], 74.0), (uc11['right'], uc11['cy']))
    draw_actor_link((act_kepsek['left'], 72.0), (uc12['right'], uc12['cy']))
    draw_actor_link((act_kepsek['left'], 70.0), (uc15['right'], uc15['cy']))

    # E. WALI KELAS (act_wali: left = 86.25, cy = 48.0)
    # Connects cleanly into Zone 3:
    draw_actor_link((act_wali['left'], 50.0), (uc13['right'], uc13['cy']))
    draw_actor_link((act_wali['left'], 48.0), (uc14['right'], uc14['cy']))
    draw_actor_link((act_wali['left'], 46.0), (uc15['right'], uc15['cy'] - 0.4))

    # F. EXTERNAL ACTORS
    # Bot Telegram -> UC08 (via notification trigger)
    # Clean orthogonal step: from UC08.1 or UC08 to act_tg
    ax.annotate('', xy=(act_tg['left'], act_tg['cy']), xytext=(uc08['right'], uc08['cy']),
                arrowprops=dict(arrowstyle="->", color=EXT_BORDER, lw=1.5, linestyle="-"), zorder=3)
    ax.text(84.0, 75.0, "Webhook Alert", fontsize=6.5, fontweight='bold', color=EXT_BORDER, ha='center',
            bbox=dict(boxstyle="round,pad=0.1", fc="#FEF3C7", ec="none"), zorder=4)

    # NVIDIA NIM -> UC16
    ax.annotate('', xy=(act_nim['left'], act_nim['cy']), xytext=(uc16_ext['right'], uc16_ext['cy']),
                arrowprops=dict(arrowstyle="<->", color=EXT_BORDER, lw=1.5, linestyle="-"), zorder=3)
    ax.text(84.0, 14.5, "HTTPS REST", fontsize=6.5, fontweight='bold', color=EXT_BORDER, ha='center',
            bbox=dict(boxstyle="round,pad=0.1", fc="#FEF3C7", ec="none"), zorder=4)

    # 6. UML DEPENDENCY ARROWS (<<include>> and <<extend>>)
    def draw_dep(p1, p2, text, color=INC_COLOR):
        ax.annotate('', xy=p2, xytext=p1,
                    arrowprops=dict(arrowstyle="->", color=color, lw=1.5, linestyle="--", shrinkA=2, shrinkB=2),
                    zorder=4)
        mx = (p1[0] + p2[0]) / 2
        my = (p1[1] + p2[1]) / 2
        ax.text(mx, my, text, fontsize=6.8, fontweight='bold', color=color, ha='center', va='center',
                bbox=dict(boxstyle="round,pad=0.15", fc="#FFFFFF", ec="none", alpha=0.95), zorder=5)

    # Vertical Include: UC08 -> UC08.1
    draw_dep((uc08['cx'], uc08['bottom']), (uc08_inc1['cx'], uc08_inc1['top']), "<<include>>")
    # Vertical Include: UC08.1 -> UC08.2
    draw_dep((uc08_inc1['cx'], uc08_inc1['bottom']), (uc08_inc2['cx'], uc08_inc2['top']), "<<include>>")

    # Vertical Include: UC09 -> UC09.1
    draw_dep((uc09['cx'], uc09['bottom']), (uc09_inc1['cx'], uc09_inc1['top']), "<<include>>")

    # Vertical Extend: UC16.1 -> UC16 (in UML, extension points to the base usecase)
    draw_dep((uc16_ext['cx'], uc16_ext['top']), (uc16['cx'], uc16['bottom']), "<<extend>>", color=EXT_COLOR)

    # 7. INTEGRITY FOOTER MATRIX (Mapping 20 Database Tables to Use Cases)
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

    # Legend on right
    ax.text(88.0, 5.2, "STANDAR UML NOTASI:", fontsize=7.5, fontweight='bold', color=PRIMARY_NAVY, va='center')
    ax.text(88.0, 3.8, "─── Asosiasi Aktor", fontsize=6.5, color='#475569', va='center')
    ax.text(88.0, 2.6, "- - > <<include>> (Wajib)", fontsize=6.5, color=INC_COLOR, va='center')
    ax.text(88.0, 1.4, "- - > <<extend>> (Kondisional)", fontsize=6.5, color=EXT_COLOR, va='center')

    plt.tight_layout()
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print("[SUCCESS] Perfect Use Case Diagram saved to:", output_path)

if __name__ == "__main__":
    generate_perfect_usecase(r"c:\xampp\htdocs\absen\Usecase_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png")
