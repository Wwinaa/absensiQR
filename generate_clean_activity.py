import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Circle, Polygon
import numpy as np

def generate_clean_activity(output_path):
    # Canvas Size: 26 x 38 inches at 300 DPI -> 7800 x 11400 px
    fig, ax = plt.subplots(figsize=(26, 38), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    # Color Palette
    BG_COLOR = "#F8FAFC"
    HEADER_BG = "#0F172A"
    SWIMLANE_LINE = "#CBD5E1"
    NODE_BG = "#FFFFFF"
    NODE_BORDER = "#2563EB"
    DB_NODE_BG = "#EFF6FF"
    DB_BORDER = "#1D4ED8"
    DECISION_BG = "#FEF3C7"
    DECISION_BORDER = "#D97706"
    LINE_COLOR = "#475569"
    EXT_BG = "#ECFDF5"
    EXT_BORDER = "#059669"

    fig.patch.set_facecolor(BG_COLOR)
    ax.set_facecolor(BG_COLOR)

    # 1. TOP HEADER BANNER
    banner = FancyBboxPatch((1.5, 96.0), 97, 3.4,
                            boxstyle="round,pad=0.2,rounding_size=0.5",
                            facecolor=HEADER_BG, edgecolor="#0284C7", linewidth=2.2, zorder=2)
    ax.add_patch(banner)
    ax.text(50, 97.9, "ACTIVITY DIAGRAM MENYELURUH SIKLUS SISTEM PRESENSI SMK NU WARGABINANGUN",
            fontsize=16.5, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 96.7, "Alur Fungsional 5 Swimlanes Memproses 20 Tabel Database `db_absensi_smk` dari Inisialisasi hingga Rekapitulasi",
            fontsize=10.5, color='#BAE6FD', ha='center', va='center', zorder=3)

    # 2. FIVE SWIMLANES (Partitions)
    lanes = [
        {"id": "user", "name": "1. AKTOR / PENGGUNA", "sub": "(Admin, Kepsek, Guru, Walikelas, Siswa)", "x1": 2.0, "x2": 21.0, "bg": "#F8FAFC"},
        {"id": "ui", "name": "2. PRESENTATION / UI LAYER", "sub": "(Kamera Scanner, Form, Dashboard, Chat)", "x1": 21.0, "x2": 40.0, "bg": "#F1F5F9"},
        {"id": "ctrl", "name": "3. CONTROLLER & BUSINESS LOGIC", "sub": "(Auth, Time Validator, Dual-Sync, RBAC)", "x1": 40.0, "x2": 62.0, "bg": "#F8FAFC"},
        {"id": "db", "name": "4. DATABASE LAYER (20 TABEL)", "sub": "(`db_absensi_smk` Relational Storage)", "x1": 62.0, "x2": 82.0, "bg": "#EFF6FF"},
        {"id": "ext", "name": "5. EXTERNAL SERVICES", "sub": "(Telegram API & NVIDIA NIM AI)", "x1": 82.0, "x2": 98.0, "bg": "#ECFDF5"},
    ]

    y_top = 95.4
    y_bot = 4.0
    for lane in lanes:
        w = lane["x2"] - lane["x1"]
        bg_patch = patches.Rectangle((lane["x1"], y_bot), w, y_top - y_bot,
                                     facecolor=lane["bg"], edgecolor=SWIMLANE_LINE, linewidth=1.2, zorder=1)
        ax.add_patch(bg_patch)

        hdr = FancyBboxPatch((lane["x1"] + 0.3, y_top - 2.6), w - 0.6, 2.3,
                             boxstyle="round,pad=0.1,rounding_size=0.3",
                             facecolor="#FFFFFF", edgecolor="#94A3B8", linewidth=1.4, zorder=2)
        ax.add_patch(hdr)
        cx = (lane["x1"] + lane["x2"]) / 2
        ax.text(cx, y_top - 1.1, lane["name"], fontsize=9.0, fontweight='bold', color='#1E293B', ha='center', va='center', zorder=3)
        ax.text(cx, y_top - 1.95, lane["sub"], fontsize=6.8, color='#64748B', ha='center', va='center', zorder=3)

    # Helper Functions for Clean Nodes
    def draw_action(x, y, text, w=15.0, h=2.1, bg=NODE_BG, border=NODE_BORDER, z=4, fontsize=7.2):
        node = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.15,rounding_size=0.5",
                              facecolor=bg, edgecolor=border, linewidth=1.4, zorder=z)
        ax.add_patch(node)
        lines = text.split("\n")
        if len(lines) == 1:
            ax.text(x, y, lines[0], fontsize=fontsize, fontweight='bold',
                    color='#0F172A', ha='center', va='center', zorder=z+1)
        elif len(lines) == 2:
            ax.text(x, y + 0.36, lines[0], fontsize=fontsize, fontweight='bold',
                    color='#0F172A', ha='center', va='center', zorder=z+1)
            ax.text(x, y - 0.38, lines[1], fontsize=fontsize-0.5, fontstyle='italic',
                    color='#475569', ha='center', va='center', zorder=z+1)
        else:
            spacing = 0.35
            start_y = y + (len(lines)-1)*spacing/2
            for i, l in enumerate(lines):
                ax.text(x, start_y - i*spacing, l, fontsize=fontsize-0.5,
                        fontweight='bold' if i==0 else 'normal',
                        color='#0F172A' if i==0 else '#475569', ha='center', va='center', zorder=z+1)
        return (x, y, w, h)

    def draw_db_store(x, y, table_name, action_text="", w=16.0, h=2.0):
        node = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.1,rounding_size=0.3",
                              facecolor=DB_NODE_BG, edgecolor=DB_BORDER, linewidth=1.4, zorder=4)
        ax.add_patch(node)
        ax.text(x - w/2 + 1.2, y, "[(DB)]", fontsize=6.8, fontweight='bold', color=DB_BORDER, ha='center', va='center', zorder=5)
        ax.text(x + 0.6, y + 0.32, f"Tabel: `{table_name}`", fontsize=7.2, fontweight='bold', color=DB_BORDER, ha='center', va='center', zorder=5)
        if action_text:
            ax.text(x + 0.6, y - 0.42, action_text, fontsize=6.3, color='#1E40AF', fontstyle='italic', ha='center', va='center', zorder=5)
        return (x, y, w, h)

    def draw_decision(x, y, text, w=4.5, h=2.2):
        pts = np.array([[x, y + h/2], [x + w/2, y], [x, y - h/2], [x - w/2, y]])
        poly = Polygon(pts, closed=True, facecolor=DECISION_BG, edgecolor=DECISION_BORDER, linewidth=1.4, zorder=4)
        ax.add_patch(poly)
        ax.text(x, y, text, fontsize=6.8, fontweight='bold', color='#92400E', ha='center', va='center', zorder=5)
        return (x, y, w, h)

    # Clean connecting arrow between two points with ZERO overshoot
    def draw_arrow(p1, p2, text="", color=LINE_COLOR, dashed=False, lw=1.3):
        ax.annotate('', xy=p2, xytext=p1,
                    arrowprops=dict(arrowstyle="->", color=color, lw=lw,
                                    linestyle="--" if dashed else "-",
                                    shrinkA=2, shrinkB=2),
                    zorder=5)
        if text:
            mx = (p1[0] + p2[0]) / 2
            my = (p1[1] + p2[1]) / 2 + 0.35
            ax.text(mx, my, text, fontsize=6.5, fontweight='bold', color=color,
                    ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none", alpha=0.9), zorder=6)

    # Phase dividing separator with title
    def draw_phase_separator(y, phase_title):
        ax.plot([2.5, 97.5], [y, y], color="#E2E8F0", linestyle=":", linewidth=1.2, zorder=2)
        ax.text(3.5, y + 0.5, phase_title, fontsize=8.0, fontweight='bold', color='#0284C7', zorder=3)

    # -------------------------------------------------------------
    # STEP-BY-STEP STRICT SEQUENTIAL FLOW (ZERO CUT-THROUGH LINES!)
    # -------------------------------------------------------------

    # === FASE 1: INISIALISASI & LOGIN (y: 92 -> 84) ===
    # Start Node in Swimlane 1
    start_c = Circle((11.5, 91.8), 0.75, facecolor='#10B981', edgecolor='#047857', linewidth=1.5, zorder=5)
    ax.add_patch(start_c)
    ax.text(11.5, 92.8, "START", fontsize=7.5, fontweight='bold', color='#047857', ha='center', zorder=6)

    act_login1 = draw_action(11.5, 89.2, "Input Kredensial Pengguna\n(Username & Password)")
    draw_arrow((11.5, 91.05), (11.5, 90.25))

    act_login2 = draw_action(30.5, 89.2, "Kirim Payload Kredensial\n(POST ke login_process.php)")
    draw_arrow((19.0, 89.2), (23.0, 89.2))

    act_login3 = draw_action(51.0, 89.2, "Verifikasi Hash Bcrypt\n& Cek Otorisasi Role")
    draw_arrow((38.0, 89.2), (42.5, 89.2))

    db_login = draw_db_store(72.0, 89.2, "users, roles", "Validasi Kredensial & Hak Akses")
    draw_arrow((59.5, 89.2), (64.0, 89.2))

    act_session = draw_action(51.0, 85.5, "Inisialisasi Session PHP\n& Role-Based Access Control")
    draw_arrow((72.0, 88.2), (72.0, 85.5))
    draw_arrow((64.0, 85.5), (59.5, 85.5))

    act_dash = draw_action(30.5, 85.5, "Render Dashboard Pengguna\nSesuai Role (Admin/Guru/Siswa)")
    draw_arrow((42.5, 85.5), (38.0, 85.5))

    act_user_dash = draw_action(11.5, 85.5, "Akses Dashboard & Fitur\n(Session Aktif Terverifikasi)")
    draw_arrow((23.0, 85.5), (19.0, 85.5))

    # === FASE 2: MANAJEMEN MASTER AKADEMIK & BARCODE (y: 83 -> 71) ===
    draw_phase_separator(83.2, "• FASE 2: MANAJEMEN MASTER DATA AKADEMIK & GENERATE KARTU QR (ADMIN)")
    draw_arrow((11.5, 84.45), (11.5, 81.35))

    act_m1 = draw_action(11.5, 80.3, "Admin Input Data Rombel,\nPendidik, Siswa & Jadwal")
    act_m2 = draw_action(30.5, 80.3, "Formulir Validasi Client\n& Penanganan File Excel")
    draw_arrow((19.0, 80.3), (23.0, 80.3))

    act_m3 = draw_action(51.0, 80.3, "CRUD Controller Engine\n& Integritas Relasional")
    draw_arrow((38.0, 80.3), (42.5, 80.3))

    db_m1 = draw_db_store(72.0, 80.3, "tahun_ajaran, jurusan, kelas", "Master Rombel & Tingkat")
    draw_arrow((59.5, 80.3), (64.0, 80.3))

    db_m2 = draw_db_store(72.0, 77.2, "guru, wali_kelas_data, wali_kelas", "Master Pendidik & Penugasan")
    draw_arrow((72.0, 79.3), (72.0, 78.2))

    db_m3 = draw_db_store(72.0, 74.1, "siswa, mata_pelajaran, jadwal_mengajar", "Data Siswa & Jadwal KBM")
    draw_arrow((72.0, 76.2), (72.0, 75.1))

    act_gen_qr = draw_action(51.0, 74.1, "Generate Payload QR/Barcode\n& Enkripsi Identitas Siswa/Guru")
    draw_arrow((64.0, 74.1), (59.5, 74.1))

    db_qr = draw_db_store(72.0, 71.0, "barcode_guru, rfid_cards", "Simpan String Barcode & Card UID")
    draw_arrow((51.0, 73.05), (51.0, 71.0))
    draw_arrow((59.5, 71.0), (64.0, 71.0))

    act_card_ui = draw_action(30.5, 71.0, "Render Kartu Presensi Digital\nSiap Cetak / Unduh PDF")
    draw_arrow((51.0, 71.0), (38.0, 71.0))

    act_card_user = draw_action(11.5, 71.0, "Menerima Kartu QR / RFID\n(Siap Digunakan Presensi)")
    draw_arrow((23.0, 71.0), (19.0, 71.0))

    # === FASE 3: SIKLUS PRESENSI HARIAN, ATURAN JAM & TELEGRAM (y: 69 -> 53) ===
    draw_phase_separator(69.2, "• FASE 3: SIKLUS PRESENSI REAL-TIME, ATURAN JAM SERVER, DUAL-SYNC & TELEGRAM")
    draw_arrow((11.5, 69.95), (11.5, 67.25))

    act_scan1 = draw_action(11.5, 66.2, "Pindai Kartu Barcode / QR\npada Kamera Scanner")
    act_scan2 = draw_action(30.5, 66.2, "HTML5 Scanner Tangkap Payload\nKirim AJAX ke scan_handler.php")
    draw_arrow((19.0, 66.2), (23.0, 66.2))

    act_scan3 = draw_action(51.0, 66.2, "Mesin Pemroses Presensi\nValidasi Format & Jam Server")
    draw_arrow((38.0, 66.2), (42.5, 66.2))

    dec_time = draw_decision(51.0, 62.4, "Kondisi\nJam?", w=7.2, h=2.4)
    draw_arrow((51.0, 65.15), (51.0, 63.6))

    act_hadir = draw_action(44.0, 58.8, "05:00 - 07:00 WIB:\nStatus 'Hadir Tepat Waktu'", w=10.0, h=1.8, fontsize=6.8)
    act_late = draw_action(56.0, 58.8, "> 07:00 WIB:\nStatus 'Terlambat'", w=10.0, h=1.8, fontsize=6.8)
    draw_arrow((47.4, 62.4), (44.0, 59.7), text="Pagi")
    draw_arrow((54.6, 62.4), (56.0, 59.7), text="Siang")

    # DB Presensi: Siswa, Guru, Wali Kelas (Dual Sync)
    db_abs_s = draw_db_store(72.0, 61.5, "absensi_siswa", "Insert/Update Presensi Siswa", w=16.5, h=1.8)
    db_abs_g = draw_db_store(72.0, 58.8, "absensi_guru", "Insert/Update Presensi Guru", w=16.5, h=1.8)
    db_abs_w = draw_db_store(72.0, 56.1, "absensi_wali_kelas", "DUAL-SYNC: Otomatis Sinkron", w=16.5, h=1.8)
    draw_arrow((61.0, 58.8), (63.75, 58.8))
    draw_arrow((72.0, 59.7), (72.0, 60.6))
    draw_arrow((72.0, 57.9), (72.0, 57.0))

    # Telegram Notification (Swimlane 5)
    act_notif_ctrl = draw_action(51.0, 54.0, "Format Payload Kehadiran\n& Trigger cURL Telegram API")
    draw_arrow((72.0, 55.2), (72.0, 54.0))
    draw_arrow((63.75, 54.0), (58.5, 54.0))

    ext_tel = draw_action(90.0, 54.0, "Bot Telegram (@HadirioBot)\nKirim Pesan Live ke Chat", w=15, h=2.0, bg=EXT_BG, border=EXT_BORDER)
    draw_arrow((58.5, 54.0), (82.5, 54.0), text="Webhook")

    act_scan_ui = draw_action(30.5, 54.0, "Tampilkan Toast Kehadiran\n& Audio Beep Konfirmasi")
    draw_arrow((43.5, 54.0), (38.0, 54.0))

    act_scan_done = draw_action(11.5, 54.0, "Melihat Hasil Presensi\n(Status Tercatat & Terverifikasi)")
    draw_arrow((23.0, 54.0), (19.0, 54.0))

    # === FASE 4: PENGAJUAN IZIN / SAKIT & AUTO-SYNC (y: 52 -> 41) ===
    draw_phase_separator(52.2, "• FASE 4: PENGAJUAN DISPENSASI/IZIN/SAKIT ONLINE & AUTO-SYNC REKAP")
    draw_arrow((11.5, 52.95), (11.5, 50.45))

    act_iz1 = draw_action(11.5, 49.4, "Siswa/Guru Isi Form Izin\n& Unggah Foto Bukti Surat")
    act_iz2 = draw_action(30.5, 49.4, "Validasi File MIME & Ukuran\n(JPG/PNG/PDF Maksimal 2MB)")
    draw_arrow((19.0, 49.4), (23.0, 49.4))

    act_iz3 = draw_action(51.0, 49.4, "Catat Tiket Pengajuan Izin\nStatus Default: 'Menunggu'")
    draw_arrow((38.0, 49.4), (42.5, 49.4))

    db_iz = draw_db_store(72.0, 49.4, "pengajuan_izin", "Insert Rekord Surat Izin & Bukti")
    draw_arrow((59.5, 49.4), (64.0, 49.4))

    act_iz_appr_ui = draw_action(30.5, 45.8, "Tampilkan Berkas di Dasbor\n(Admin Klik Tombol 'Setujui')")
    draw_arrow((72.0, 48.4), (72.0, 45.8))
    draw_arrow((64.0, 45.8), (38.0, 45.8))

    act_iz_sync = draw_action(51.0, 45.8, "Update Status 'Disetujui' &\nSINKRONISASI OTOMATIS REKAP")
    draw_arrow((38.0, 45.8), (42.5, 45.8))

    db_iz_sync = draw_db_store(72.0, 42.5, "absensi_siswa, absensi_guru", "Auto-Sync Status Presensi = Izin/Sakit")
    draw_arrow((51.0, 44.75), (51.0, 42.5))
    draw_arrow((59.5, 42.5), (64.0, 42.5))

    act_iz_user = draw_action(11.5, 42.5, "Siswa/Guru Terima Notifikasi\nStatus Izin Telah Disetujui")
    draw_arrow((64.0, 42.5), (19.0, 42.5))

    # === FASE 5: SUPERVISI KBM & PEMBINAAN KARAKTER (y: 40 -> 29) ===
    draw_phase_separator(40.8, "• FASE 5: SUPERVISI KBM GURU & CATATAN PEMBINAAN KARAKTER SISWA")
    draw_arrow((11.5, 41.45), (11.5, 38.65))

    act_sp1 = draw_action(11.5, 37.6, "Guru Input Jurnal Mengajar /\nWali Kelas Catat Pembinaan")
    act_sp2 = draw_action(30.5, 37.6, "Sanitasi Input Teks & HTML\nProteksi Serangan XSS / CSRF")
    draw_arrow((19.0, 37.6), (23.0, 37.6))

    act_sp3 = draw_action(51.0, 37.6, "Routing Controller Jurnal\n& Disiplin Kesiswaan")
    draw_arrow((38.0, 37.6), (42.5, 37.6))

    db_sp1 = draw_db_store(72.0, 37.6, "catatan_guru", "Jurnal Mengajar & Supervisi Kepsek")
    draw_arrow((59.5, 37.6), (64.0, 37.6))

    db_sp2 = draw_db_store(72.0, 34.0, "catatan_pembinaan", "Pelanggaran/Prestasi & Bimbingan")
    draw_arrow((72.0, 36.6), (72.0, 35.0))

    act_sp_read_ui = draw_action(30.5, 34.0, "Tampilkan Catatan & Tombol\n'Tandai Sudah Dibaca'")
    draw_arrow((64.0, 34.0), (38.0, 34.0))

    act_sp_user = draw_action(11.5, 34.0, "Kepsek / Siswa Konfirmasi\n(Status Baca Otomatis Terarsip)")
    draw_arrow((23.0, 34.0), (19.0, 34.0))

    # === FASE 6: ASISTEN AI HADIR.IO & REKAP SENIN-JUMAT (y: 32 -> 14) ===
    draw_phase_separator(32.4, "• FASE 6: ASISTEN AI HADIR.IO (NVIDIA NIM) & REKAPITULASI SENIN-JUMAT")
    draw_arrow((11.5, 32.95), (11.5, 30.25))

    act_ai1 = draw_action(11.5, 29.2, "Ketik Pertanyaan pada\nWidget Chat Asisten AI")
    act_ai2 = draw_action(30.5, 29.2, "Chat UI Kirim Prompt AJAX\nke backend `chat_api.php`")
    draw_arrow((19.0, 29.2), (23.0, 29.2))

    act_ai3 = draw_action(51.0, 29.2, "Intent Parser & RBAC Retrieval\nQuery Data Faktual Sekolah")
    draw_arrow((38.0, 29.2), (42.5, 29.2))

    db_ai = draw_db_store(72.0, 29.2, "pengaturan_sistem & 20 Tabel", "Ekstraksi Metrik Agregat DB")
    draw_arrow((59.5, 29.2), (64.0, 29.2))

    act_ai4 = draw_action(51.0, 25.4, "Sintesis Prompt Kontekstual\nKirim Request ke NVIDIA NIM")
    draw_arrow((72.0, 28.2), (72.0, 25.4))
    draw_arrow((64.0, 25.4), (59.5, 25.4))

    ext_ai = draw_action(90.0, 25.4, "NVIDIA Nemotron LLM API\nInference Jawaban Faktual", w=15, h=2.0, bg=EXT_BG, border=EXT_BORDER)
    draw_arrow((58.5, 25.4), (82.5, 25.4), text="REST API")

    act_ai_res = draw_action(30.5, 25.4, "Tampilkan Jawaban Faktual,\nRingkas & Langsung to the Point")
    draw_arrow((82.5, 25.4), (58.5, 25.4))
    draw_arrow((42.5, 25.4), (38.0, 25.4))

    act_ai_user = draw_action(11.5, 25.4, "Membaca Jawaban Asisten AI\n(Informasi Akurat & Terpercaya)")
    draw_arrow((23.0, 25.4), (19.0, 25.4))

    # Rekapitulasi Presensi
    draw_arrow((11.5, 24.35), (11.5, 20.95))
    act_rk1 = draw_action(11.5, 19.9, "Admin/Kepsek Pilih Periode\n& Rombel Rekap Presensi")
    act_rk2 = draw_action(51.0, 19.9, "Hitung Total Hari Efektif\n(FILTER KETAT: SENIN - JUMAT)")
    draw_arrow((19.0, 19.9), (42.5, 19.9))

    db_rk = draw_db_store(72.0, 19.9, "absensi_siswa, guru, wali_kelas", "Agregasi Hadir, Izin, Sakit, Alpa")
    draw_arrow((59.5, 19.9), (64.0, 19.9))

    act_rk_export = draw_action(30.5, 16.2, "Generate Laporan Resmi\nEkspor File Excel / Cetak PDF")
    draw_arrow((72.0, 18.9), (72.0, 16.2))
    draw_arrow((64.0, 16.2), (38.0, 16.2))

    act_rk_dl = draw_action(11.5, 16.2, "Unduh Dokumen Rekap Resmi\n(Arsip Kurikulum & Akreditasi)")
    draw_arrow((23.0, 16.2), (19.0, 16.2))

    # === FASE FINAL: PENUTUPAN & INTEGRITAS SISTEM (y: 13 -> 5) ===
    draw_phase_separator(14.2, "• FASE 7: AUDIT LOGGING, INTEGRITAS TRANSAKSI & PENUTUPAN SIKLUS")
    draw_arrow((11.5, 15.15), (11.5, 11.85))

    act_final_log = draw_action(51.0, 10.8, "Commit Transaksi Database, Audit Logging & Penutupan Sesi", w=22.0, h=1.8)
    draw_arrow((19.0, 10.8), (40.0, 10.8))

    # Activity Final Node (Double circle / Bullseye)
    end_outer = Circle((51.0, 6.8), 0.85, facecolor='#FFFFFF', edgecolor='#DC2626', linewidth=2.0, zorder=5)
    end_inner = Circle((51.0, 6.8), 0.55, facecolor='#DC2626', edgecolor='none', zorder=6)
    ax.add_patch(end_outer)
    ax.add_patch(end_inner)
    draw_arrow((51.0, 9.9), (51.0, 7.65))
    ax.text(51.0, 5.2, "FINISH (STATE INTEGRITY PRESERVED)", fontsize=7.5, fontweight='bold', color='#991B1B', ha='center', va='center')

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
    print(f"[SUCCESS] Clean Activity Diagram saved to: {output_path}")

if __name__ == "__main__":
    out_file = r"c:\xampp\htdocs\absen\Activity_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    generate_clean_activity(out_file)
