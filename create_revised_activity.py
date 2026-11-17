import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Circle, Polygon
import numpy as np

def create_revised_activity(output_path):
    # Canvas Size: 26 x 38 inches at 300 DPI -> 7800 x 11400 px
    fig, ax = plt.subplots(figsize=(26, 38), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    # Color Palette
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

    # 1. TOP HEADER BANNER
    banner = FancyBboxPatch((1.5, 95.8), 97, 3.6,
                            boxstyle="round,pad=0.2,rounding_size=0.5",
                            facecolor=HEADER_BG, edgecolor="#0284C7", linewidth=2.2, zorder=2)
    ax.add_patch(banner)
    ax.text(50, 97.8, "ACTIVITY DIAGRAM MENYELURUH SIKLUS SISTEM PRESENSI SMK NU WARGABINANGUN",
            fontsize=16.5, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 96.5, "Alur Fungsional Terintegrasi 5 Swimlanes Memproses 20 Tabel Database `db_absensi_smk` & 5 Role Pengguna",
            fontsize=10.5, color='#BAE6FD', ha='center', va='center', zorder=3)

    # 2. FIVE SWIMLANES (Partitions)
    lanes = [
        {"id": "user", "name": "1. AKTOR / PENGGUNA", "sub": "(Admin, Kepsek, Guru, Walikelas, Siswa)", "x1": 2.0, "x2": 21.0, "bg": "#F8FAFC"},
        {"id": "ui", "name": "2. PRESENTATION / UI LAYER", "sub": "(Kamera Scanner, Form, Dashboard, Chat)", "x1": 21.0, "x2": 40.0, "bg": "#F1F5F9"},
        {"id": "ctrl", "name": "3. CONTROLLER & BUSINESS LOGIC", "sub": "(Auth, Time Validator, Dual-Sync, RBAC)", "x1": 40.0, "x2": 62.0, "bg": "#F8FAFC"},
        {"id": "db", "name": "4. DATABASE LAYER (20 TABEL)", "sub": "(`db_absensi_smk` Relational Storage)", "x1": 62.0, "x2": 82.0, "bg": "#EFF6FF"},
        {"id": "ext", "name": "5. EXTERNAL SERVICES", "sub": "(Telegram API & NVIDIA NIM AI)", "x1": 82.0, "x2": 98.0, "bg": "#ECFDF5"},
    ]

    y_top = 95.2
    y_bot = 4.0
    for lane in lanes:
        w = lane["x2"] - lane["x1"]
        # Background
        bg_patch = patches.Rectangle((lane["x1"], y_bot), w, y_top - y_bot,
                                     facecolor=lane["bg"], edgecolor=SWIMLANE_LINE, linewidth=1.2, zorder=1)
        ax.add_patch(bg_patch)

        # Header box for each lane
        hdr = FancyBboxPatch((lane["x1"] + 0.3, y_top - 2.8), w - 0.6, 2.5,
                             boxstyle="round,pad=0.1,rounding_size=0.3",
                             facecolor="#FFFFFF", edgecolor="#94A3B8", linewidth=1.4, zorder=2)
        ax.add_patch(hdr)
        cx = (lane["x1"] + lane["x2"]) / 2
        ax.text(cx, y_top - 1.2, lane["name"], fontsize=9.0, fontweight='bold', color='#1E293B', ha='center', va='center', zorder=3)
        ax.text(cx, y_top - 2.1, lane["sub"], fontsize=6.8, color='#64748B', ha='center', va='center', zorder=3)

    # Helper Functions for UML Elements
    def draw_action(x, y, text, w=15.0, h=2.1, bg=NODE_BG, border=NODE_BORDER, z=4, fontsize=7.2, bold=False):
        node = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.15,rounding_size=0.5",
                              facecolor=bg, edgecolor=border, linewidth=1.4, zorder=z)
        ax.add_patch(node)
        lines = text.split("\n")
        if len(lines) == 1:
            ax.text(x, y, lines[0], fontsize=fontsize, fontweight='bold' if bold else 'normal',
                    color='#0F172A', ha='center', va='center', zorder=z+1)
        elif len(lines) == 2:
            ax.text(x, y + 0.38, lines[0], fontsize=fontsize, fontweight='bold' if bold else 'normal',
                    color='#0F172A', ha='center', va='center', zorder=z+1)
            ax.text(x, y - 0.38, lines[1], fontsize=fontsize-0.5, fontstyle='italic',
                    color='#475569', ha='center', va='center', zorder=z+1)
        else:
            spacing = 0.35
            start_y = y + (len(lines)-1)*spacing/2
            for i, l in enumerate(lines):
                ax.text(x, start_y - i*spacing, l, fontsize=fontsize-0.5,
                        color='#0F172A' if i==0 else '#475569', ha='center', va='center', zorder=z+1)
        return (x, y)

    def draw_db_store(x, y, table_name, action_text="", w=16.0, h=2.0):
        node = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.1,rounding_size=0.3",
                              facecolor=DB_NODE_BG, edgecolor=DB_BORDER, linewidth=1.4, zorder=4)
        ax.add_patch(node)
        ax.text(x - w/2 + 1.2, y, "[(DB)]", fontsize=6.8, fontweight='bold', color=DB_BORDER, ha='center', va='center', zorder=5)
        ax.text(x + 0.6, y + 0.32, f"Tabel: `{table_name}`", fontsize=7.2, fontweight='bold', color=DB_BORDER, ha='center', va='center', zorder=5)
        if action_text:
            ax.text(x + 0.6, y - 0.42, action_text, fontsize=6.3, color='#1E40AF', fontstyle='italic', ha='center', va='center', zorder=5)
        return (x, y)

    def draw_decision(x, y, text, w=4.5, h=2.2):
        pts = np.array([[x, y + h/2], [x + w/2, y], [x, y - h/2], [x - w/2, y]])
        poly = Polygon(pts, closed=True, facecolor=DECISION_BG, edgecolor=DECISION_BORDER, linewidth=1.4, zorder=4)
        ax.add_patch(poly)
        ax.text(x, y, text, fontsize=6.8, fontweight='bold', color='#92400E', ha='center', va='center', zorder=5)
        return (x, y)

    def draw_sync_bar(x1, x2, y, label=""):
        h = 0.55
        bar = patches.Rectangle((x1, y - h/2), x2 - x1, h, facecolor=SYNC_COLOR, edgecolor='none', zorder=4)
        ax.add_patch(bar)
        if label:
            ax.text((x1 + x2)/2, y + 0.62, label, fontsize=7.2, fontweight='bold', color=SYNC_COLOR, ha='center', va='center', zorder=5)

    def draw_arrow(p1, p2, text="", rad=0.0, color=LINE_COLOR, dashed=False, lw=1.2):
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
                    ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none", alpha=0.9), zorder=6)

    # -------------------------------------------------------------
    # 3. DRAW ACTIVITY FLOW
    # -------------------------------------------------------------

    # --- PHASE 1: INITIALIZATION & LOGIN ---
    # Start Node
    start_c = Circle((11.5, 91.2), 0.75, facecolor='#10B981', edgecolor='#047857', linewidth=1.5, zorder=5)
    ax.add_patch(start_c)
    ax.text(11.5, 92.25, "START", fontsize=7.5, fontweight='bold', color='#047857', ha='center', zorder=6)

    a1 = draw_action(11.5, 88.5, "Input Kredensial Pengguna\n(Username & Password)", w=16, h=2.1)
    draw_arrow((11.5, 90.4), (11.5, 89.6))

    a2 = draw_action(30.5, 88.5, "Kirim Payload Kredensial\n(POST ke login_process.php)", w=16, h=2.1)
    draw_arrow(a1, a2)

    a3 = draw_action(51.0, 88.5, "Verifikasi Hash Bcrypt\n& Cek Otorisasi Role", w=17, h=2.1)
    draw_arrow(a2, a3)

    db1 = draw_db_store(72.0, 88.5, "users, roles", "Validasi Kredensial & Hak Akses", w=17, h=2.1)
    draw_arrow(a3, db1)

    a4 = draw_action(51.0, 84.8, "Inisialisasi Session PHP\n& Role-Based Access Control", w=17, h=2.1)
    draw_arrow((72.0, 87.4), (51.0, 85.9), rad=0.08)

    a5 = draw_action(30.5, 84.8, "Render Dashboard Pengguna\nSesuai Role (Admin/Guru/Siswa)", w=16, h=2.1)
    draw_arrow(a4, a5)
    draw_arrow(a5, (11.5, 84.8), rad=-0.05)
    ax.text(11.5, 84.8, "Akses Dashboard", fontsize=7.2, fontweight='bold', color='#1E293B', ha='center', va='center')

    # FORK BAR (Split into 6 parallel lifecycle paths)
    draw_sync_bar(5.0, 95.0, 82.2, "[ FORK: SIKLUS OPERASIONAL SISTEM TERPADU ]")
    draw_arrow((11.5, 83.7), (11.5, 82.5))

    # =============================================================
    # THREAD A: MASTER DATA & BARCODE PROVISIONING (y: 81 -> 70)
    # =============================================================
    ax.text(3.5, 80.8, "• JALUR 1: MANAJEMEN MASTER AKADEMIK & GENERATE KARTU QR", fontsize=8.0, fontweight='bold', color='#0369A1')

    ta1 = draw_action(11.5, 78.2, "Admin Input Data Rombel,\nPendidik, Siswa & Jadwal", w=16, h=2.1)
    draw_arrow((11.5, 81.9), (11.5, 79.3))

    ta2 = draw_action(30.5, 78.2, "Formulir Validasi Client\n& Penanganan File Excel", w=16, h=2.1)
    draw_arrow(ta1, ta2)

    ta3 = draw_action(51.0, 78.2, "CRUD Engine Controller\n& Integritas Relasional", w=17, h=2.1)
    draw_arrow(ta2, ta3)

    db_m1 = draw_db_store(72.0, 78.2, "tahun_ajaran, jurusan, kelas", "Master Rombel & Tingkat Kelas", w=17, h=1.9)
    draw_arrow(ta3, db_m1)

    db_m2 = draw_db_store(72.0, 75.3, "guru, wali_kelas_data, wali_kelas", "Master Pendidik & Penugasan", w=17, h=1.9)
    draw_arrow((51.0, 77.1), (63.5, 75.3), rad=-0.05)

    db_m3 = draw_db_store(72.0, 72.4, "siswa, mata_pelajaran, jadwal_mengajar", "Data Siswa & Jadwal KBM Mingguan", w=17, h=1.9)
    draw_arrow((51.0, 77.1), (63.5, 72.4), rad=-0.1)

    ta4 = draw_action(51.0, 69.5, "Generate Payload QR/Barcode\n& Enkripsi Identitas Siswa/Guru", w=17, h=2.1)
    draw_arrow((63.5, 72.4), (51.0, 70.6), rad=0.06)

    db_m4 = draw_db_store(72.0, 69.5, "barcode_guru, rfid_cards", "Simpan String Barcode & Card UID", w=17, h=1.9)
    draw_arrow(ta4, db_m4)

    ta5 = draw_action(30.5, 69.5, "Render Kartu Presensi Digital\nSiap Cetak / Unduh PDF", w=16, h=2.1)
    draw_arrow(ta4, ta5)
    draw_arrow(ta5, (11.5, 69.5))
    ax.text(11.5, 69.5, "Cetak Kartu QR", fontsize=7.2, fontweight='bold', color='#1E293B', ha='center', va='center')

    # =============================================================
    # THREAD B: PRESENSI REAL-TIME, ATURAN WAKTU & DUAL-SYNC (y: 67 -> 49)
    # =============================================================
    ax.text(3.5, 66.8, "• JALUR 2: SIKLUS PRESENSI HARIAN, ATURAN JAM SERVER & DUAL-SYNC", fontsize=8.0, fontweight='bold', color='#0369A1')

    tb1 = draw_action(11.5, 64.2, "Pindai Kartu Barcode / QR\npada Kamera Scanner", w=16, h=2.1)
    draw_arrow((11.5, 81.9), (11.5, 65.3), rad=-0.1)

    tb2 = draw_action(30.5, 64.2, "HTML5 Scanner Tangkap Payload\nKirim AJAX ke scan_handler.php", w=16, h=2.1)
    draw_arrow(tb1, tb2)

    tb3 = draw_action(51.0, 64.2, "Mesin Pemroses Presensi\nValidasi Jam Server & Format", w=17, h=2.1)
    draw_arrow(tb2, tb3)

    # Decision Jam Server
    dec_time = draw_decision(51.0, 60.2, "Kondisi\nJam?", w=7.5, h=2.5)
    draw_arrow(tb3, (51.0, 61.5))

    tb_hadir = draw_action(43.5, 56.4, "05:00 - 07:00 WIB:\nStatus 'Hadir Tepat Waktu'", w=10.5, h=1.9)
    tb_late = draw_action(55.5, 56.4, "> 07:00 WIB:\nStatus 'Terlambat'", w=10.5, h=1.9)
    tb_pulang = draw_action(51.0, 53.4, ">= 15:00 WIB: Catat Jam Pulang", w=16, h=1.8)

    draw_arrow((48.0, 59.8), (43.5, 57.4), text="Pagi", rad=0.05)
    draw_arrow((54.0, 59.8), (55.5, 57.4), text="Siang", rad=-0.05)
    draw_arrow((51.0, 58.9), (51.0, 54.3), text="Sore")

    # DB Presensi: Multi-Table Write & Dual-Sync
    db_p1 = draw_db_store(72.0, 58.0, "absensi_siswa", "Insert/Update Presensi Siswa", w=17, h=1.9)
    db_p2 = draw_db_store(72.0, 55.2, "absensi_guru", "Insert/Update Presensi Guru", w=17, h=1.9)
    db_p3 = draw_db_store(72.0, 52.4, "absensi_wali_kelas", "DUAL-SYNC: Otomatis Sinkronisasi", w=17, h=1.9)

    draw_arrow((43.5, 55.4), (63.5, 58.0), rad=-0.05)
    draw_arrow((55.5, 55.4), (63.5, 55.2), rad=-0.05)
    draw_arrow((51.0, 52.5), (63.5, 52.4), rad=0.0)

    # Notifikasi Telegram
    tb_notif = draw_action(51.0, 48.6, "Format Payload Kehadiran\n& Trigger cURL Telegram API", w=17, h=2.1)
    draw_arrow((72.0, 51.4), (51.0, 49.7), rad=0.08)

    ext_tel = draw_action(90.0, 48.6, "Bot Telegram (@HadirioBot)\nKirim Notifikasi Live ke Chat", w=15, h=2.1, bg=EXT_BG, border=EXT_BORDER)
    draw_arrow(tb_notif, ext_tel, text="Webhook")

    tb_resp = draw_action(30.5, 48.6, "Tampilkan Toast Kehadiran\n& Suara Audio Konfirmasi", w=16, h=2.1)
    draw_arrow(tb_notif, tb_resp)
    draw_arrow(tb_resp, (11.5, 48.6))
    ax.text(11.5, 48.6, "Melihat Hasil Scan", fontsize=7.2, fontweight='bold', color='#1E293B', ha='center', va='center')

    # =============================================================
    # THREAD C: PENGAJUAN IZIN/SAKIT & AUTO-SYNC (y: 46 -> 36)
    # =============================================================
    ax.text(3.5, 45.8, "• JALUR 3: PENGAJUAN DISPENSASI/IZIN/SAKIT & AUTO-SYNC REKAP", fontsize=8.0, fontweight='bold', color='#0369A1')

    tc1 = draw_action(11.5, 43.2, "Siswa/Guru Isi Formulir Izin\n& Upload Surat Dokter / Bukti", w=16, h=2.1)
    draw_arrow((11.5, 81.9), (11.5, 44.3), rad=-0.15)

    tc2 = draw_action(30.5, 43.2, "Upload Handler & Validasi File\n(Format JPG/PDF Maks 2MB)", w=16, h=2.1)
    draw_arrow(tc1, tc2)

    tc3 = draw_action(51.0, 43.2, "Catat Tiket Izin Baru\nStatus Default: 'Menunggu'", w=17, h=2.1)
    draw_arrow(tc2, tc3)

    db_i1 = draw_db_store(72.0, 43.2, "pengajuan_izin", "Insert Surat Izin & Berkas Bukti", w=17, h=1.9)
    draw_arrow(tc3, db_i1)

    tc4 = draw_action(11.5, 39.2, "Admin / Walikelas Tinjau\n& Klik Tombol 'Setujui'", w=16, h=2.1)
    draw_arrow((72.0, 42.2), (11.5, 40.3), rad=0.15)

    tc5 = draw_action(51.0, 39.2, "Update Status 'Disetujui' &\nSINKRONISASI OTOMATIS REKAP", w=17, h=2.1)
    draw_arrow(tc4, (30.5, 39.2))
    draw_arrow((30.5, 39.2), tc5)

    db_i2 = draw_db_store(72.0, 39.2, "absensi_siswa, absensi_guru", "Update Status Presensi = Izin/Sakit", w=17, h=1.9)
    draw_arrow(tc5, db_i2)

    # =============================================================
    # THREAD D: SUPERVISI KBM & PEMBINAAN SISWA (y: 35 -> 25)
    # =============================================================
    ax.text(3.5, 35.5, "• JALUR 4: SUPERVISI KBM GURU & CATATAN PEMBINAAN KARAKTER", fontsize=8.0, fontweight='bold', color='#0369A1')

    td1 = draw_action(11.5, 32.8, "Guru Input Jurnal KBM /\nWalikelas Catat Pembinaan", w=16, h=2.1)
    draw_arrow((11.5, 81.9), (11.5, 33.9), rad=-0.2)

    td2 = draw_action(30.5, 32.8, "Sanitasi Input Teks & HTML\nProteksi Serangan XSS", w=16, h=2.1)
    draw_arrow(td1, td2)

    td3 = draw_action(51.0, 32.8, "Routing Controller Jurnal\n& Disiplin Kesiswaan", w=17, h=2.1)
    draw_arrow(td2, td3)

    db_s1 = draw_db_store(72.0, 33.8, "catatan_guru", "Jurnal Mengajar & Supervisi Kepsek", w=17, h=1.9)
    db_s2 = draw_db_store(72.0, 30.8, "catatan_pembinaan", "Pelanggaran/Prestasi & Bimbingan", w=17, h=1.9)
    draw_arrow(td3, db_s1)
    draw_arrow(td3, db_s2)

    td4 = draw_action(11.5, 28.0, "Kepala Sekolah / Siswa Baca\n& Konfirmasi Catatan Pembinaan", w=16, h=2.1)
    draw_arrow((72.0, 29.8), (11.5, 29.1), rad=0.1)

    # =============================================================
    # THREAD E: AI CHATBOT & REKAP SENIN-JUMAT (y: 24 -> 11)
    # =============================================================
    ax.text(3.5, 25.0, "• JALUR 5: ASISTEN AI HADIR.IO & REKAPITULASI SENIN-JUMAT", fontsize=8.0, fontweight='bold', color='#0369A1')

    # Chatbot flow
    te1 = draw_action(11.5, 22.0, "Ketik Pertanyaan pada\nWidget Chat Asisten AI", w=16, h=2.1)
    draw_arrow((11.5, 81.9), (11.5, 23.1), rad=-0.25)

    te2 = draw_action(30.5, 22.0, "Chat UI Kirim Prompt AJAX\nke backend `chat_api.php`", w=16, h=2.1)
    draw_arrow(te1, te2)

    te3 = draw_action(51.0, 22.0, "Intent Parser & RBAC Retrieval\nQuery Data Faktual Sekolah", w=17, h=2.1)
    draw_arrow(te2, te3)

    db_ai = draw_db_store(72.0, 22.0, "pengaturan_sistem & 20 Tabel", "Ekstraksi Metrik Agregat Database", w=17, h=1.9)
    draw_arrow(te3, db_ai)

    te4 = draw_action(51.0, 18.0, "Sintesis Prompt Kontekstual\nKirim Request ke NVIDIA NIM", w=17, h=2.1)
    draw_arrow(db_ai, (51.0, 19.1), rad=0.08)

    ext_ai = draw_action(90.0, 18.0, "NVIDIA Nemotron LLM API\nInference Jawaban Faktual", w=15, h=2.1, bg=EXT_BG, border=EXT_BORDER)
    draw_arrow(te4, ext_ai, text="REST POST")
    draw_arrow(ext_ai, (51.0, 17.1), text="JSON Result", rad=0.05)

    te5 = draw_action(30.5, 18.0, "Tampilkan Jawaban Faktual,\nRingkas & Langsung to the Point", w=16, h=2.1)
    draw_arrow((51.0, 17.1), te5)
    draw_arrow(te5, (11.5, 18.0))
    ax.text(11.5, 18.0, "Membaca Jawaban AI", fontsize=7.2, fontweight='bold', color='#1E293B', ha='center', va='center')

    # Rekapitulasi Presensi Flow
    tr1 = draw_action(11.5, 13.5, "Admin/Kepsek Pilih Periode\n& Rombel Rekap Presensi", w=16, h=2.1)
    tr2 = draw_action(51.0, 13.5, "Hitung Total Hari Efektif\n(FILTER KETAT: SENIN - JUMAT)", w=17, h=2.1)
    draw_arrow(tr1, tr2)

    db_rk = draw_db_store(72.0, 13.5, "absensi_siswa, guru, wali_kelas", "Agregasi Hadir, Izin, Sakit, Alpa", w=17, h=1.9)
    draw_arrow(tr2, db_rk)

    tr3 = draw_action(30.5, 13.5, "Generate Laporan Resmi\nEkspor File Excel / Cetak PDF", w=16, h=2.1)
    draw_arrow(db_rk, (51.0, 12.4), rad=0.05)
    draw_arrow((51.0, 12.4), tr3)
    draw_arrow(tr3, (11.5, 13.5))
    ax.text(11.5, 13.5, "Unduh Dokumen Rekap", fontsize=7.2, fontweight='bold', color='#1E293B', ha='center', va='center')

    # =============================================================
    # JOIN SYNCHRONIZATION BAR & END NODE (y: 9.5 -> 5.0)
    # =============================================================
    draw_sync_bar(5.0, 95.0, 9.5, "[ JOIN: SINKRONISASI KONSISTENSI & PENYIMPANAN LOG OPERASIONAL ]")

    draw_arrow((11.5, 68.4), (11.5, 9.8))
    draw_arrow((11.5, 47.5), (20.0, 9.8))
    draw_arrow((72.0, 38.2), (60.0, 9.8))
    draw_arrow((11.5, 26.9), (35.0, 9.8))
    draw_arrow((11.5, 16.9), (11.5, 9.8))
    draw_arrow((11.5, 12.4), (25.0, 9.8))

    fin_act = draw_action(50.0, 7.5, "Commit Transaksi Database, Audit Logging & Penutupan Sesi", w=22, h=1.8)
    draw_arrow((50.0, 9.2), (50.0, 8.4))

    # Activity Final Node
    end_outer = Circle((50.0, 5.2), 0.85, facecolor='#FFFFFF', edgecolor='#DC2626', linewidth=2.0, zorder=5)
    end_inner = Circle((50.0, 5.2), 0.55, facecolor='#DC2626', edgecolor='none', zorder=6)
    ax.add_patch(end_outer)
    ax.add_patch(end_inner)
    draw_arrow((50.0, 6.6), (50.0, 6.05))
    ax.text(50.0, 4.0, "FINISH (STATE INTEGRITY PRESERVED)", fontsize=7.5, fontweight='bold', color='#991B1B', ha='center', va='center')

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
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print(f"[SUCCESS] Revised Activity Diagram saved to: {output_path}")

if __name__ == "__main__":
    out_file = r"c:\xampp\htdocs\absen\Activity_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    create_revised_activity(out_file)
