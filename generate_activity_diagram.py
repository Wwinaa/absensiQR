import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Circle, Polygon
import numpy as np

def create_activity_diagram(output_path):
    # Dimensions: 26 inches wide x 36 inches tall for crystal clear resolution
    fig_w, fig_h = 26, 36
    dpi = 300
    fig, ax = plt.subplots(figsize=(fig_w, fig_h), dpi=dpi)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    # Palette
    BG_COLOR = "#F8FAFC"        # Slate 50
    HEADER_BG = "#0F172A"       # Slate 900
    SWIMLANE_LINE = "#CBD5E1"   # Slate 300
    NODE_BG = "#FFFFFF"         # White
    NODE_BORDER = "#2563EB"     # Blue 600
    DB_NODE_BG = "#EFF6FF"      # Blue 50
    DB_BORDER = "#1D4ED8"       # Blue 700
    DECISION_BG = "#FEF3C7"     # Amber 100
    DECISION_BORDER = "#D97706" # Amber 600
    SYNC_COLOR = "#1E293B"      # Dark Slate for Fork/Join bars
    LINE_COLOR = "#475569"      # Slate 600
    EXT_BG = "#ECFDF5"          # Emerald 50
    EXT_BORDER = "#059669"      # Emerald 600

    fig.patch.set_facecolor(BG_COLOR)
    ax.set_facecolor(BG_COLOR)

    # 1. HEADER BANNER (Top)
    banner = FancyBboxPatch((1.5, 95.2), 97, 4.0,
                            boxstyle="round,pad=0.2,rounding_size=0.5",
                            facecolor=HEADER_BG, edgecolor="#0284C7", linewidth=2, zorder=2)
    ax.add_patch(banner)
    ax.text(50, 97.5, "ACTIVITY DIAGRAM MENYELURUH SIKLUS SISTEM PRESENSI SMK NU WARGABINANGUN",
            fontsize=17, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 96.0, "Alur Terintegrasi 5 Swimlanes Memproses Seluruh Siklus Operasional & 20 Tabel Database `db_absensi_smk`",
            fontsize=10.5, color='#BAE6FD', ha='center', va='center', zorder=3)

    # 2. FIVE SWIMLANES (Partitions)
    lanes = [
        {"id": "user", "name": "1. AKTOR / PENGGUNA", "sub": "(Admin, Kepsek, Guru, Walikelas, Siswa)", "x1": 2.0, "x2": 21.0, "bg": "#F8FAFC"},
        {"id": "ui", "name": "2. PRESENTATION / UI LAYER", "sub": "(Web Scanner, Dashboard, Forms, Chat)", "x1": 21.0, "x2": 40.0, "bg": "#F1F5F9"},
        {"id": "ctrl", "name": "3. CONTROLLER & BUSINESS LOGIC", "sub": "(Auth, Time Validator, Dual-Sync, RBAC)", "x1": 40.0, "x2": 62.0, "bg": "#F8FAFC"},
        {"id": "db", "name": "4. DATABASE LAYER (20 TABEL)", "sub": "(`db_absensi_smk` Relational Storage)", "x1": 62.0, "x2": 82.0, "bg": "#EFF6FF"},
        {"id": "ext", "name": "5. EXTERNAL SERVICES", "sub": "(Telegram API & NVIDIA NIM AI)", "x1": 82.0, "x2": 98.0, "bg": "#ECFDF5"},
    ]

    # Draw swimlane backgrounds and borders
    y_top = 94.5
    y_bot = 4.5
    for lane in lanes:
        w = lane["x2"] - lane["x1"]
        # Background
        bg_patch = patches.Rectangle((lane["x1"], y_bot), w, y_top - y_bot,
                                     facecolor=lane["bg"], edgecolor=SWIMLANE_LINE, linewidth=1.2, zorder=1)
        ax.add_patch(bg_patch)

        # Header box for each lane
        hdr = FancyBboxPatch((lane["x1"] + 0.3, y_top - 2.8), w - 0.6, 2.5,
                             boxstyle="round,pad=0.1,rounding_size=0.3",
                             facecolor="#FFFFFF", edgecolor="#94A3B8", linewidth=1.5, zorder=2)
        ax.add_patch(hdr)
        cx = (lane["x1"] + lane["x2"]) / 2
        ax.text(cx, y_top - 1.2, lane["name"], fontsize=9.2, fontweight='bold', color='#1E293B', ha='center', va='center', zorder=3)
        ax.text(cx, y_top - 2.1, lane["sub"], fontsize=6.8, color='#64748B', ha='center', va='center', zorder=3)

    # Helper Functions for UML Symbols
    def draw_action(x, y, text, w=15, h=2.2, bg=NODE_BG, border=NODE_BORDER, z=4, fontsize=7.2, bold=False):
        node = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.15,rounding_size=0.5",
                              facecolor=bg, edgecolor=border, linewidth=1.4, zorder=z)
        ax.add_patch(node)
        lines = text.split("\n")
        if len(lines) == 1:
            ax.text(x, y, lines[0], fontsize=fontsize, fontweight='bold' if bold else 'normal',
                    color='#0F172A', ha='center', va='center', zorder=z+1)
        elif len(lines) == 2:
            ax.text(x, y + 0.4, lines[0], fontsize=fontsize, fontweight='bold' if bold else 'normal',
                    color='#0F172A', ha='center', va='center', zorder=z+1)
            ax.text(x, y - 0.4, lines[1], fontsize=fontsize-0.6, fontstyle='italic',
                    color='#475569', ha='center', va='center', zorder=z+1)
        else:
            spacing = 0.35
            start_y = y + (len(lines)-1)*spacing/2
            for i, l in enumerate(lines):
                ax.text(x, start_y - i*spacing, l, fontsize=fontsize-0.5,
                        color='#0F172A' if i==0 else '#475569', ha='center', va='center', zorder=z+1)
        return (x, y)

    def draw_db_store(x, y, table_name, action_text="", w=16, h=2.1):
        # Database cylinder/store representation
        node = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.1,rounding_size=0.3",
                              facecolor=DB_NODE_BG, edgecolor=DB_BORDER, linewidth=1.5, zorder=4)
        ax.add_patch(node)
        # Cylinder icon left side
        ax.text(x - w/2 + 1.2, y, "[(DB)]", fontsize=7.0, fontweight='bold', color=DB_BORDER, ha='center', va='center', zorder=5)
        ax.text(x + 0.6, y + 0.35, f"Tabel: `{table_name}`", fontsize=7.5, fontweight='bold', color=DB_BORDER, ha='center', va='center', zorder=5)
        if action_text:
            ax.text(x + 0.6, y - 0.45, action_text, fontsize=6.5, color='#1E40AF', fontstyle='italic', ha='center', va='center', zorder=5)
        return (x, y)

    def draw_decision(x, y, text, w=4.5, h=2.2):
        # Diamond
        pts = np.array([[x, y + h/2], [x + w/2, y], [x, y - h/2], [x - w/2, y]])
        poly = Polygon(pts, closed=True, facecolor=DECISION_BG, edgecolor=DECISION_BORDER, linewidth=1.5, zorder=4)
        ax.add_patch(poly)
        ax.text(x, y, text, fontsize=6.8, fontweight='bold', color='#92400E', ha='center', va='center', zorder=5)
        return (x, y)

    def draw_sync_bar(x1, x2, y, label=""):
        # Thick horizontal fork/join synchronization bar
        h = 0.55
        bar = patches.Rectangle((x1, y - h/2), x2 - x1, h, facecolor=SYNC_COLOR, edgecolor='none', zorder=4)
        ax.add_patch(bar)
        if label:
            ax.text((x1 + x2)/2, y + 0.65, label, fontsize=7.2, fontweight='bold', color=SYNC_COLOR, ha='center', va='center', zorder=5)

    def draw_arrow(p1, p2, text="", rad=0.0, color=LINE_COLOR, dashed=False, lw=1.2, text_pos='mid'):
        # Clean connecting arrow
        connectionstyle = f"arc3,rad={rad}"
        ax.annotate('', xy=p2, xytext=p1,
                    arrowprops=dict(arrowstyle="->", color=color, lw=lw,
                                    linestyle="--" if dashed else "-",
                                    shrinkA=3, shrinkB=3, connectionstyle=connectionstyle),
                    zorder=5)
        if text:
            mx = (p1[0] + p2[0]) / 2
            my = (p1[1] + p2[1]) / 2 + 0.4
            ax.text(mx, my, text, fontsize=6.5, fontweight='bold', color=color,
                    ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none", alpha=0.85), zorder=6)

    # -------------------------------------------------------------
    # 3. CONSTRUCTING THE LIFECYCLE ACTIVITY FLOW
    # -------------------------------------------------------------

    # --- PHASE 1: INITIALIZATION & MULTI-ROLE LOGIN ---
    # Start Node
    start_circle = Circle((11.5, 90.5), 0.75, facecolor='#10B981', edgecolor='#047857', linewidth=1.5, zorder=5)
    ax.add_patch(start_circle)
    ax.text(11.5, 91.6, "START", fontsize=7.5, fontweight='bold', color='#047857', ha='center', zorder=6)

    # Action 1: User input credentials
    a1 = draw_action(11.5, 87.5, "Input Username & Password\n(Pilih Role / Akses Login)", w=16, h=2.2)
    draw_arrow((11.5, 89.7), (11.5, 88.6))

    # Action 2: Presentation submits form
    a2 = draw_action(30.5, 87.5, "Kirim Payload Kredensial\n(POST ke login_process.php)", w=16, h=2.2)
    draw_arrow(a1, a2)

    # Action 3: Controller verifies
    a3 = draw_action(51.0, 87.5, "Verifikasi Hash Password\n& Sanitasi Input SQL", w=17, h=2.2)
    draw_arrow(a2, a3)

    # DB 1: Query users & roles
    db1 = draw_db_store(72.0, 87.5, "users, roles", "Validasi Kredensial & Hak Akses", w=17, h=2.2)
    draw_arrow(a3, db1)

    # Action 4: Return Session & Load Dashboard
    a4 = draw_action(51.0, 83.8, "Inisialisasi Session PHP\n& Role-Based Access Control", w=17, h=2.2)
    draw_arrow((72.0, 86.4), (51.0, 84.9), rad=0.1)

    a5 = draw_action(30.5, 83.8, "Render Dashboard Pengguna\nSesuai Role (Admin/Guru/Siswa)", w=16, h=2.2)
    draw_arrow(a4, a5)
    draw_arrow(a5, (11.5, 83.8), rad=-0.05)
    ax.text(11.5, 83.8, "Akses Dashboard", fontsize=7.2, fontweight='bold', color='#1E293B', ha='center', va='center')

    # FORK BAR (Split into 6 Core Lifecycle Threads)
    draw_sync_bar(5.0, 95.0, 81.0, "[ FORK: SIKLUS OPERASIONAL SISTEM TERINTEGRASI ]")
    draw_arrow((11.5, 82.7), (11.5, 81.3))

    # =============================================================
    # THREAD A: MASTER DATA & CREDENTIAL PROVISIONING (y: 80 -> 70)
    # =============================================================
    # Section Header Note
    ax.text(3.5, 79.5, "• JALUR 1: PENGELOLAAN MASTER AKADEMIK & KARTU PRESENSI", fontsize=8.0, fontweight='bold', color='#0369A1')

    ta1 = draw_action(11.5, 77.0, "Admin Input Data Sekolah,\nSiswa, Guru & Jadwal KBM", w=16, h=2.2)
    draw_arrow((11.5, 80.7), (11.5, 78.1))

    ta2 = draw_action(30.5, 77.0, "Form Validasi Client-Side\n& Upload File Excel / CSV", w=16, h=2.2)
    draw_arrow(ta1, ta2)

    ta3 = draw_action(51.0, 77.0, "CRUD Controller Engine\n& Relational Constraint Check", w=17, h=2.2)
    draw_arrow(ta2, ta3)

    db_m1 = draw_db_store(72.0, 77.0, "tahun_ajaran, jurusan, kelas", "Insert / Update Master Rombel", w=17, h=2.0)
    draw_arrow(ta3, db_m1)

    db_m2 = draw_db_store(72.0, 74.0, "guru, wali_kelas_data, wali_kelas", "Mapping Pendidik & Penugasan", w=17, h=2.0)
    draw_arrow((51.0, 75.9), (63.5, 74.0), rad=-0.05)

    db_m3 = draw_db_store(72.0, 71.0, "siswa, mata_pelajaran, jadwal_mengajar", "Data Siswa & Jadwal KBM Mingguan", w=17, h=2.0)
    draw_arrow((51.0, 75.9), (63.5, 71.0), rad=-0.1)

    # Sub-flow: Barcode generation
    ta4 = draw_action(51.0, 68.0, "Generate Payload QR/Barcode\n& Enkripsi Identitas Siswa/Guru", w=17, h=2.2)
    draw_arrow((63.5, 71.0), (51.0, 69.1), rad=0.05)

    db_m4 = draw_db_store(72.0, 68.0, "barcode_guru, rfid_cards", "Simpan String Barcode & Card ID", w=17, h=2.0)
    draw_arrow(ta4, db_m4)

    ta5 = draw_action(30.5, 68.0, "Render Kartu Presensi Digital\nSiap Cetak / Unduh PDF", w=16, h=2.2)
    draw_arrow(ta4, ta5)
    draw_arrow(ta5, (11.5, 68.0))
    ax.text(11.5, 68.0, "Menerima Kartu QR", fontsize=7.2, fontweight='bold', color='#1E293B', ha='center', va='center')

    # =============================================================
    # THREAD B: SIKLUS PRESENSI REAL-TIME & TELEGRAM (y: 65 -> 48)
    # =============================================================
    ax.text(3.5, 65.0, "• JALUR 2: SIKLUS PRESENSI HARIAN, ATURAN WAKTU & DUAL-SYNC", fontsize=8.0, fontweight='bold', color='#0369A1')

    tb1 = draw_action(11.5, 62.5, "Pindai Barcode / QR Card\npada Kamera Pemindai", w=16, h=2.2)
    draw_arrow((11.5, 80.7), (11.5, 63.6), rad=-0.1)

    tb2 = draw_action(30.5, 62.5, "HTML5 Scanner Tangkap Payload\nKirim AJAX ke scan_handler.php", w=16, h=2.2)
    draw_arrow(tb1, tb2)

    tb3 = draw_action(51.0, 62.5, "Presensi Processing Engine\nValidasi Format & Jam Server", w=17, h=2.2)
    draw_arrow(tb2, tb3)

    # Decision 1: Aturan Jam Masuk vs Pulang
    dec_time = draw_decision(51.0, 58.5, "Kondisi\nJam?", w=7.5, h=2.6)
    draw_arrow(tb3, (51.0, 59.8))

    # Branches of Decision
    tb_hadir = draw_action(43.5, 54.5, "05:00 - 07:00 WIB:\nStatus 'Hadir'", w=10.5, h=2.0)
    tb_late = draw_action(55.5, 54.5, "> 07:00 WIB:\nStatus 'Terlambat'", w=10.5, h=2.0)
    tb_pulang = draw_action(51.0, 51.5, ">= 15:00 WIB: Catat Jam Pulang", w=16, h=1.8)

    draw_arrow((48.0, 58.0), (43.5, 55.5), text="Pagi", rad=0.05)
    draw_arrow((54.0, 58.0), (55.5, 55.5), text="Siang", rad=-0.05)
    draw_arrow((51.0, 57.2), (51.0, 52.4), text="Sore")

    # DB Presensi: Multi-Table Write & Dual-Sync
    db_p1 = draw_db_store(72.0, 56.5, "absensi_siswa", "Insert/Update Presensi Siswa", w=17, h=2.0)
    db_p2 = draw_db_store(72.0, 53.5, "absensi_guru", "Insert/Update Presensi Guru", w=17, h=2.0)
    db_p3 = draw_db_store(72.0, 50.5, "absensi_wali_kelas", "DUAL-SYNC: Otomatis Sinkronisasi", w=17, h=2.0)

    draw_arrow((43.5, 53.5), (63.5, 56.5), rad=-0.05)
    draw_arrow((55.5, 53.5), (63.5, 53.5), rad=-0.05)
    draw_arrow((51.0, 50.6), (63.5, 50.5), rad=0.0)

    # Trigger Notifikasi Telegram
    tb_notif = draw_action(51.0, 46.5, "Format Pesan Kehadiran\n& Trigger cURL Telegram API", w=17, h=2.2)
    draw_arrow((72.0, 49.5), (51.0, 47.6), rad=0.08)

    ext_tel = draw_action(90.0, 46.5, "Bot Telegram (@HadirioBot)\nKirim Notifikasi Live ke Chat", w=15, h=2.2, bg=EXT_BG, border=EXT_BORDER)
    draw_arrow(tb_notif, ext_tel, text="Webhook")

    tb_resp = draw_action(30.5, 46.5, "Tampilkan Toast Kehadiran\n& Suara Audio Konfirmasi", w=16, h=2.2)
    draw_arrow(tb_notif, tb_resp)
    draw_arrow(tb_resp, (11.5, 46.5))
    ax.text(11.5, 46.5, "Melihat Status Presensi", fontsize=7.2, fontweight='bold', color='#1E293B', ha='center', va='center')

    # =============================================================
    # THREAD C: PENGAJUAN IZIN/SAKIT & AUTO-SYNC (y: 44 -> 34)
    # =============================================================
    ax.text(3.5, 43.5, "• JALUR 3: LAYANAN PENGAJUAN DISPENSASI/IZIN/SAKIT & SINKRONISASI", fontsize=8.0, fontweight='bold', color='#0369A1')

    tc1 = draw_action(11.5, 40.5, "Siswa/Guru Isi Formulir Izin\n& Upload Surat Dokter / Bukti", w=16, h=2.2)
    draw_arrow((11.5, 80.7), (11.5, 41.6), rad=-0.15)

    tc2 = draw_action(30.5, 40.5, "Upload Handler & Validasi File\n(PDF/JPG, Maksimal 2MB)", w=16, h=2.2)
    draw_arrow(tc1, tc2)

    tc3 = draw_action(51.0, 40.5, "Catat Tiket Pengajuan\nStatus Default: 'Menunggu'", w=17, h=2.2)
    draw_arrow(tc2, tc3)

    db_i1 = draw_db_store(72.0, 40.5, "pengajuan_izin", "Insert Rekord Surat Izin & Bukti", w=17, h=2.0)
    draw_arrow(tc3, db_i1)

    tc4 = draw_action(11.5, 36.5, "Admin/Walikelas Verifikasi\n& Klik Tombol 'Setujui'", w=16, h=2.2)
    draw_arrow((72.0, 39.5), (11.5, 37.6), rad=0.15)

    tc5 = draw_action(51.0, 36.5, "Update Status 'Disetujui' &\nSINKRONISASI OTOMATIS REKAP", w=17, h=2.2)
    draw_arrow(tc4, (30.5, 36.5))
    draw_arrow((30.5, 36.5), tc5)

    db_i2 = draw_db_store(72.0, 36.5, "absensi_siswa, absensi_guru", "Update Status Presensi = Izin/Sakit", w=17, h=2.0)
    draw_arrow(tc5, db_i2)

    # =============================================================
    # THREAD D: SUPERVISI KBM & PEMBINAAN SISWA (y: 33 -> 23)
    # =============================================================
    ax.text(3.5, 33.5, "• JALUR 4: SUPERVISI KBM GURU & CATATAN PEMBINAAN KARAKTER", fontsize=8.0, fontweight='bold', color='#0369A1')

    td1 = draw_action(11.5, 30.5, "Guru Input Jurnal KBM /\nWalikelas Catat Pembinaan", w=16, h=2.2)
    draw_arrow((11.5, 80.7), (11.5, 31.6), rad=-0.2)

    td2 = draw_action(30.5, 30.5, "Sanitasi Input Teks\n& Proteksi XSS / CSRF", w=16, h=2.2)
    draw_arrow(td1, td2)

    td3 = draw_action(51.0, 30.5, "Routing Handler Jurnal\n& Disiplin Kesiswaan", w=17, h=2.2)
    draw_arrow(td2, td3)

    db_s1 = draw_db_store(72.0, 31.5, "catatan_guru", "Jurnal Mengajar & Supervisi Kepsek", w=17, h=2.0)
    db_s2 = draw_db_store(72.0, 28.5, "catatan_pembinaan", "Pelanggaran/Prestasi & Bimbingan", w=17, h=2.0)

    draw_arrow(td3, db_s1)
    draw_arrow(td3, db_s2)

    td4 = draw_action(11.5, 26.0, "Kepala Sekolah / Siswa Baca\n& Konfirmasi Catatan Pembinaan", w=16, h=2.2)
    draw_arrow((72.0, 27.5), (11.5, 27.1), rad=0.1)

    # =============================================================
    # THREAD E: AI CHATBOT & REKAP SENIN-JUMAT (y: 23 -> 10)
    # =============================================================
    ax.text(3.5, 23.5, "• JALUR 5: ASISTEN AI HADIR.IO & REKAPITULASI SENIN-JUMAT", fontsize=8.0, fontweight='bold', color='#0369A1')

    # Chatbot flow
    te1 = draw_action(11.5, 20.5, "Ketik Pertanyaan pada\nWidget Chat Asisten AI", w=16, h=2.2)
    draw_arrow((11.5, 80.7), (11.5, 21.6), rad=-0.25)

    te2 = draw_action(30.5, 20.5, "Chat UI kirim Prompt AJAX\nke backend `chat_api.php`", w=16, h=2.2)
    draw_arrow(te1, te2)

    te3 = draw_action(51.0, 20.5, "Intent Parser & RBAC Retrieval\nQuery Data Faktual Sekolah", w=17, h=2.2)
    draw_arrow(te2, te3)

    db_ai = draw_db_store(72.0, 20.5, "pengaturan_sistem & 20 Tabel", "Ekstraksi Metrik Agregat Database", w=17, h=2.0)
    draw_arrow(te3, db_ai)

    te4 = draw_action(51.0, 16.5, "Sintesis Prompt Kontekstual\nKirim Request ke NVIDIA NIM", w=17, h=2.2)
    draw_arrow(db_ai, (51.0, 17.6), rad=0.08)

    ext_ai = draw_action(90.0, 16.5, "NVIDIA Nemotron LLM API\nInference Jawaban Faktual", w=15, h=2.2, bg=EXT_BG, border=EXT_BORDER)
    draw_arrow(te4, ext_ai, text="REST POST")
    draw_arrow(ext_ai, (51.0, 15.6), text="JSON Result", rad=0.05)

    te5 = draw_action(30.5, 16.5, "Tampilkan Jawaban Faktual,\nRingkas & Langsung to the Point", w=16, h=2.2)
    draw_arrow((51.0, 15.6), te5)
    draw_arrow(te5, (11.5, 16.5))
    ax.text(11.5, 16.5, "Membaca Jawaban AI", fontsize=7.2, fontweight='bold', color='#1E293B', ha='center', va='center')

    # Rekapitulasi Presensi Flow
    tr1 = draw_action(11.5, 12.0, "Admin/Kepsek Pilih Periode\n& Rombel Rekap Presensi", w=16, h=2.2)
    tr2 = draw_action(51.0, 12.0, "Hitung Total Hari Efektif\n(FILTER KETAT: SENIN - JUMAT)", w=17, h=2.2)
    draw_arrow(tr1, tr2)

    db_rk = draw_db_store(72.0, 12.0, "absensi_siswa, guru, wali_kelas", "Agregasi Hadir, Izin, Sakit, Alpa", w=17, h=2.0)
    draw_arrow(tr2, db_rk)

    tr3 = draw_action(30.5, 12.0, "Generate Laporan Resmi\nEkspor File Excel / Cetak PDF", w=16, h=2.2)
    draw_arrow(db_rk, (51.0, 10.9), rad=0.05)
    draw_arrow((51.0, 10.9), tr3)
    draw_arrow(tr3, (11.5, 12.0))
    ax.text(11.5, 12.0, "Unduh Dokumen Rekap", fontsize=7.2, fontweight='bold', color='#1E293B', ha='center', va='center')

    # =============================================================
    # JOIN SYNCHRONIZATION BAR & END NODE (y: 9 -> 5)
    # =============================================================
    draw_sync_bar(5.0, 95.0, 8.5, "[ JOIN: SINKRONISASI KONSISTENSI & PENYIMPANAN LOG OPERASIONAL ]")

    # Connect completed operational lines to Join Bar
    draw_arrow((11.5, 66.9), (11.5, 8.8))
    draw_arrow((11.5, 45.4), (20.0, 8.8))
    draw_arrow((72.0, 35.5), (60.0, 8.8))
    draw_arrow((11.5, 24.9), (35.0, 8.8))
    draw_arrow((11.5, 15.4), (11.5, 8.8))
    draw_arrow((11.5, 10.9), (25.0, 8.8))

    # Action after Join: Audit Trail Logging
    fin_act = draw_action(50.0, 6.8, "Commit Transaksi Database, Audit Logging & Penutupan Sesi", w=22, h=1.8)
    draw_arrow((50.0, 8.2), (50.0, 7.7))

    # End Node
    end_outer = Circle((50.0, 4.6), 0.85, facecolor='#FFFFFF', edgecolor='#DC2626', linewidth=2.0, zorder=5)
    end_inner = Circle((50.0, 4.6), 0.55, facecolor='#DC2626', edgecolor='none', zorder=6)
    ax.add_patch(end_outer)
    ax.add_patch(end_inner)
    draw_arrow((50.0, 5.9), (50.0, 5.45))
    ax.text(50.0, 3.4, "FINISH (STATE INTEGRITY PRESERVED)", fontsize=7.5, fontweight='bold', color='#991B1B', ha='center', va='center')

    # 4. BOTTOM DATABASE SUMMARY FOOTER
    footer_box = FancyBboxPatch((1.5, 0.8), 97, 2.3,
                                boxstyle="round,pad=0.1,rounding_size=0.3",
                                facecolor="#FFFFFF", edgecolor="#CBD5E1", linewidth=1.2, zorder=3)
    ax.add_patch(footer_box)
    ax.text(50, 2.2, "VERIFIKASI INTEGRITAS DATABASE `db_absensi_smk` (20 TABEL LENGKAP DIPROSES):",
            fontsize=8.2, fontweight='bold', color='#0F172A', ha='center')
    ax.text(50, 1.3, "users • roles • tahun_ajaran • jurusan • kelas • guru • wali_kelas_data • wali_kelas • siswa • mata_pelajaran • "
                     "jadwal_mengajar • barcode_guru • rfid_cards • absensi_siswa • absensi_guru • absensi_wali_kelas • pengajuan_izin • "
                     "catatan_guru • catatan_pembinaan • pengaturan_sistem",
            fontsize=7.2, color='#0284C7', fontweight='medium', ha='center')

    plt.tight_layout()
    plt.savefig(output_path, dpi=dpi, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print(f"[SUCCESS] Activity Diagram saved to: {output_path}")

if __name__ == "__main__":
    out_file = r"c:\xampp\htdocs\absen\Activity_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    create_activity_diagram(out_file)
