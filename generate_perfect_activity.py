import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Circle, Polygon
import numpy as np

def generate_perfect_activity(output_path):
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
    LINE_COLOR = "#334155" # Clear dark slate for arrows
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

    # Node Drawing Helpers
    def draw_action(x, y, text, w=15.0, h=2.1, bg=NODE_BG, border=NODE_BORDER, z=4, fontsize=7.2):
        node = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.15,rounding_size=0.4",
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
        return {'cx': x, 'cy': y, 'w': w, 'h': h,
                'left': x - w/2, 'right': x + w/2, 'top': y + h/2, 'bottom': y - h/2}

    def draw_db_store(x, y, table_name, action_text="", w=16.0, h=2.0):
        node = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.1,rounding_size=0.3",
                              facecolor=DB_NODE_BG, edgecolor=DB_BORDER, linewidth=1.4, zorder=4)
        ax.add_patch(node)
        ax.text(x - w/2 + 1.2, y, "[(DB)]", fontsize=6.8, fontweight='bold', color=DB_BORDER, ha='center', va='center', zorder=5)
        ax.text(x + 0.6, y + 0.32, f"Tabel: `{table_name}`", fontsize=7.2, fontweight='bold', color=DB_BORDER, ha='center', va='center', zorder=5)
        if action_text:
            ax.text(x + 0.6, y - 0.42, action_text, fontsize=6.3, color='#1E40AF', fontstyle='italic', ha='center', va='center', zorder=5)
        return {'cx': x, 'cy': y, 'w': w, 'h': h,
                'left': x - w/2, 'right': x + w/2, 'top': y + h/2, 'bottom': y - h/2}

    def draw_decision(x, y, text, w=5.0, h=2.3):
        pts = np.array([[x, y + h/2], [x + w/2, y], [x, y - h/2], [x - w/2, y]])
        poly = Polygon(pts, closed=True, facecolor=DECISION_BG, edgecolor=DECISION_BORDER, linewidth=1.4, zorder=4)
        ax.add_patch(poly)
        ax.text(x, y, text, fontsize=6.8, fontweight='bold', color='#92400E', ha='center', va='center', zorder=5)
        return {'cx': x, 'cy': y, 'w': w, 'h': h,
                'left': x - w/2, 'right': x + w/2, 'top': y + h/2, 'bottom': y - h/2}

    # Strict Arrow Function: EXACT start to EXACT end, shrinkA=0, shrinkB=1.0 for crisp contact
    def draw_arrow(p1, p2, text="", color=LINE_COLOR, dashed=False, lw=1.3):
        ax.annotate('', xy=p2, xytext=p1,
                    arrowprops=dict(arrowstyle="->", color=color, lw=lw,
                                    linestyle="--" if dashed else "-",
                                    shrinkA=0, shrinkB=1.2),
                    zorder=5)
        if text:
            mx = (p1[0] + p2[0]) / 2
            my = (p1[1] + p2[1]) / 2 + 0.35
            ax.text(mx, my, text, fontsize=6.5, fontweight='bold', color=color,
                    ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none", alpha=0.9), zorder=6)

    def draw_phase_separator(y, phase_title):
        ax.plot([2.5, 97.5], [y, y], color="#E2E8F0", linestyle=":", linewidth=1.2, zorder=2)
        ax.text(3.5, y + 0.5, phase_title, fontsize=8.0, fontweight='bold', color='#0284C7', zorder=3)

    # -------------------------------------------------------------
    # STRICT STEP-BY-STEP SEQUENTIAL FLOW
    # Every arrow touches the exact border of one box and terminates at the border of the next!
    # -------------------------------------------------------------

    # === FASE 1: INISIALISASI & LOGIN ===
    # Start Node in Swimlane 1
    start_c = Circle((11.5, 91.8), 0.75, facecolor='#10B981', edgecolor='#047857', linewidth=1.5, zorder=5)
    ax.add_patch(start_c)
    ax.text(11.5, 92.8, "START", fontsize=7.5, fontweight='bold', color='#047857', ha='center', zorder=6)

    b_log1 = draw_action(11.5, 89.2, "Input Kredensial Pengguna\n(Username & Password)")
    draw_arrow((11.5, 91.05), (11.5, b_log1['top']))

    b_log2 = draw_action(30.5, 89.2, "Kirim Payload Kredensial\n(POST ke login_process.php)")
    draw_arrow((b_log1['right'], 89.2), (b_log2['left'], 89.2))

    b_log3 = draw_action(51.0, 89.2, "Verifikasi Hash Bcrypt\n& Cek Otorisasi Role")
    draw_arrow((b_log2['right'], 89.2), (b_log3['left'], 89.2))

    db_log = draw_db_store(72.0, 89.2, "users, roles", "Validasi Kredensial & Hak Akses")
    draw_arrow((b_log3['right'], 89.2), (db_log['left'], 89.2))

    # Return flow from DB to Controller to UI to User (Row 2, y = 85.8)
    b_sess = draw_action(51.0, 85.8, "Inisialisasi Session PHP\n& Role-Based Access Control")
    # from DB bottom to Sess top via elbow
    draw_arrow((db_log['cx'], db_log['bottom']), (db_log['cx'], 85.8))
    draw_arrow((db_log['left'], 85.8), (b_sess['right'], 85.8))

    b_dash = draw_action(30.5, 85.8, "Render Dashboard Pengguna\nSesuai Role (Admin/Guru/Siswa)")
    draw_arrow((b_sess['left'], 85.8), (b_dash['right'], 85.8))

    b_user_dash = draw_action(11.5, 85.8, "Akses Dashboard & Fitur\n(Session Aktif Terverifikasi)")
    draw_arrow((b_dash['left'], 85.8), (b_user_dash['right'], 85.8))

    # === FASE 2: MANAJEMEN MASTER AKADEMIK & BARCODE ===
    draw_phase_separator(83.6, "• FASE 2: MANAJEMEN MASTER DATA AKADEMIK & GENERATE KARTU QR (ADMIN)")
    # Transition from Login to Master Data in Lane 1
    draw_arrow((b_user_dash['cx'], b_user_dash['bottom']), (11.5, 81.35))

    b_m1 = draw_action(11.5, 80.3, "Admin Input Data Rombel,\nPendidik, Siswa & Jadwal")
    b_m2 = draw_action(30.5, 80.3, "Formulir Validasi Client\n& Penanganan File Excel")
    draw_arrow((b_m1['right'], 80.3), (b_m2['left'], 80.3))

    b_m3 = draw_action(51.0, 80.3, "CRUD Controller Engine\n& Integritas Relasional")
    draw_arrow((b_m2['right'], 80.3), (b_m3['left'], 80.3))

    db_m1 = draw_db_store(72.0, 80.3, "tahun_ajaran, jurusan, kelas", "Master Rombel & Tingkat")
    draw_arrow((b_m3['right'], 80.3), (db_m1['left'], 80.3))

    db_m2 = draw_db_store(72.0, 77.2, "guru, wali_kelas_data, wali_kelas", "Master Pendidik & Penugasan")
    draw_arrow((db_m1['cx'], db_m1['bottom']), (db_m2['cx'], db_m2['top']))

    db_m3 = draw_db_store(72.0, 74.1, "siswa, mata_pelajaran, jadwal_mengajar", "Data Siswa & Jadwal KBM")
    draw_arrow((db_m2['cx'], db_m2['bottom']), (db_m3['cx'], db_m3['top']))

    b_gen_qr = draw_action(51.0, 74.1, "Generate Payload QR/Barcode\n& Enkripsi Identitas Siswa/Guru")
    draw_arrow((db_m3['left'], 74.1), (b_gen_qr['right'], 74.1))

    db_qr = draw_db_store(72.0, 70.8, "barcode_guru, rfid_cards", "Simpan String Barcode & Card UID")
    # from b_gen_qr to db_qr
    draw_arrow((b_gen_qr['cx'], b_gen_qr['bottom']), (b_gen_qr['cx'], 70.8))
    draw_arrow((b_gen_qr['cx'], 70.8), (db_qr['left'], 70.8))

    b_card_ui = draw_action(30.5, 70.8, "Render Kartu Presensi Digital\nSiap Cetak / Unduh PDF")
    draw_arrow((b_gen_qr['left'], 74.1), (30.5, 74.1))
    draw_arrow((30.5, 74.1), (b_card_ui['cx'], b_card_ui['top']))

    b_card_user = draw_action(11.5, 70.8, "Menerima Kartu QR / RFID\n(Siap Digunakan Presensi)")
    draw_arrow((b_card_ui['left'], 70.8), (b_card_user['right'], 70.8))

    # === FASE 3: SIKLUS PRESENSI HARIAN, ATURAN JAM & TELEGRAM ===
    draw_phase_separator(68.6, "• FASE 3: SIKLUS PRESENSI REAL-TIME, ATURAN JAM SERVER, DUAL-SYNC & TELEGRAM")
    # Transition in Lane 1
    draw_arrow((b_card_user['cx'], b_card_user['bottom']), (11.5, 66.75))

    b_scan1 = draw_action(11.5, 65.7, "Pindai Kartu Barcode / QR\npada Kamera Scanner")
    b_scan2 = draw_action(30.5, 65.7, "HTML5 Scanner Tangkap Payload\nKirim AJAX ke scan_handler.php")
    draw_arrow((b_scan1['right'], 65.7), (b_scan2['left'], 65.7))

    b_scan3 = draw_action(51.0, 65.7, "Mesin Pemroses Presensi\nValidasi Format & Jam Server")
    draw_arrow((b_scan2['right'], 65.7), (b_scan3['left'], 65.7))

    dec_time = draw_decision(51.0, 62.0, "Kondisi\nJam?", w=6.5, h=2.3)
    draw_arrow((b_scan3['cx'], b_scan3['bottom']), (dec_time['cx'], dec_time['top']))

    b_hadir = draw_action(43.8, 58.6, "05:00 - 07:00 WIB:\nStatus 'Hadir Tepat Waktu'", w=9.8, h=1.8, fontsize=6.8)
    b_late = draw_action(56.2, 58.6, "> 07:00 WIB:\nStatus 'Terlambat'", w=9.8, h=1.8, fontsize=6.8)
    draw_arrow((dec_time['left'], dec_time['cy']), (b_hadir['cx'], dec_time['cy']))
    draw_arrow((b_hadir['cx'], dec_time['cy']), (b_hadir['cx'], b_hadir['top']), text="Pagi")

    draw_arrow((dec_time['right'], dec_time['cy']), (b_late['cx'], dec_time['cy']))
    draw_arrow((b_late['cx'], dec_time['cy']), (b_late['cx'], b_late['top']), text="Siang")

    # DB Presensi: Siswa, Guru, Wali Kelas (Dual Sync)
    db_abs_s = draw_db_store(72.0, 61.2, "absensi_siswa", "Insert/Update Presensi Siswa", w=16.5, h=1.8)
    db_abs_g = draw_db_store(72.0, 58.6, "absensi_guru", "Insert/Update Presensi Guru", w=16.5, h=1.8)
    db_abs_w = draw_db_store(72.0, 56.0, "absensi_wali_kelas", "DUAL-SYNC: Otomatis Sinkron", w=16.5, h=1.8)
    draw_arrow((b_late['right'], 58.6), (db_abs_g['left'], 58.6))
    draw_arrow((db_abs_g['cx'], db_abs_g['top']), (db_abs_s['cx'], db_abs_s['bottom']))
    draw_arrow((db_abs_g['cx'], db_abs_g['bottom']), (db_abs_w['cx'], db_abs_w['top']))

    # Telegram Notification in Lane 5 (Lane 4 is empty at y = 53.6)
    b_notif = draw_action(51.0, 53.6, "Format Payload Kehadiran\n& Trigger cURL Telegram API")
    draw_arrow((db_abs_w['cx'], db_abs_w['bottom']), (db_abs_w['cx'], 53.6))
    draw_arrow((db_abs_w['cx'], 53.6), (b_notif['right'], 53.6))

    ext_tel = draw_action(90.0, 53.6, "Bot Telegram (@HadirioBot)\nKirim Pesan Live ke Chat", w=14.5, h=2.0, bg=EXT_BG, border=EXT_BORDER)
    draw_arrow((b_notif['right'], 53.6), (ext_tel['left'], 53.6), text="Webhook")

    b_scan_ui = draw_action(30.5, 53.6, "Tampilkan Toast Kehadiran\n& Audio Beep Konfirmasi")
    draw_arrow((b_notif['left'], 53.6), (b_scan_ui['right'], 53.6))

    b_scan_done = draw_action(11.5, 53.6, "Melihat Hasil Presensi\n(Status Tercatat & Terverifikasi)")
    draw_arrow((b_scan_ui['left'], 53.6), (b_scan_done['right'], 53.6))

    # === FASE 4: PENGAJUAN IZIN / SAKIT & AUTO-SYNC ===
    draw_phase_separator(51.4, "• FASE 4: PENGAJUAN DISPENSASI/IZIN/SAKIT ONLINE & AUTO-SYNC REKAP")
    draw_arrow((b_scan_done['cx'], b_scan_done['bottom']), (11.5, 49.65))

    b_iz1 = draw_action(11.5, 48.6, "Siswa/Guru Isi Form Izin\n& Unggah Foto Bukti Surat")
    b_iz2 = draw_action(30.5, 48.6, "Validasi File MIME & Ukuran\n(JPG/PNG/PDF Maksimal 2MB)")
    draw_arrow((b_iz1['right'], 48.6), (b_iz2['left'], 48.6))

    b_iz3 = draw_action(51.0, 48.6, "Catat Tiket Pengajuan Izin\nStatus Default: 'Menunggu'")
    draw_arrow((b_iz2['right'], 48.6), (b_iz3['left'], 48.6))

    db_iz = draw_db_store(72.0, 48.6, "pengajuan_izin", "Insert Rekord Surat Izin & Bukti")
    draw_arrow((b_iz3['right'], 48.6), (db_iz['left'], 48.6))

    # Admin Approval in Lane 1 -> UI Lane 2 -> Controller Lane 3 -> Auto-Sync DB Lane 4 (y = 45.2)
    b_iz_appr_user = draw_action(11.5, 45.2, "Admin / Walikelas Tinjau\n& Klik Tombol 'Setujui'")
    b_iz_appr_ui = draw_action(30.5, 45.2, "Kirim Payload Approval\n(AJAX ke admin/izin.php)")
    draw_arrow((b_iz_appr_user['right'], 45.2), (b_iz_appr_ui['left'], 45.2))

    b_iz_sync = draw_action(51.0, 45.2, "Update Status 'Disetujui' &\nSINKRONISASI OTOMATIS REKAP")
    draw_arrow((b_iz_appr_ui['right'], 45.2), (b_iz_sync['left'], 45.2))

    db_iz_sync = draw_db_store(72.0, 45.2, "absensi_siswa, absensi_guru", "Auto-Sync Status = Izin/Sakit")
    draw_arrow((b_iz_sync['right'], 45.2), (db_iz_sync['left'], 45.2))

    # Notification back to User (y = 42.0)
    b_iz_notif_ui = draw_action(30.5, 42.0, "Tampilkan Status 'Disetujui'\npada Riwayat Siswa/Guru")
    draw_arrow((db_iz_sync['cx'], db_iz_sync['bottom']), (db_iz_sync['cx'], 42.0))
    draw_arrow((db_iz_sync['cx'], 42.0), (b_iz_notif_ui['right'], 42.0))

    b_iz_done = draw_action(11.5, 42.0, "Siswa/Guru Terima Notifikasi\nStatus Izin Telah Disetujui")
    draw_arrow((b_iz_notif_ui['left'], 42.0), (b_iz_done['right'], 42.0))

    # === FASE 5: SUPERVISI KBM & PEMBINAAN KARAKTER ===
    draw_phase_separator(39.8, "• FASE 5: SUPERVISI KBM GURU & CATATAN PEMBINAAN KARAKTER SISWA")
    draw_arrow((b_iz_done['cx'], b_iz_done['bottom']), (11.5, 38.05))

    b_sp1 = draw_action(11.5, 37.0, "Guru Input Jurnal Mengajar /\nWali Kelas Catat Pembinaan")
    b_sp2 = draw_action(30.5, 37.0, "Sanitasi Input Teks & HTML\nProteksi Serangan XSS / CSRF")
    draw_arrow((b_sp1['right'], 37.0), (b_sp2['left'], 37.0))

    b_sp3 = draw_action(51.0, 37.0, "Routing Controller Jurnal\n& Disiplin Kesiswaan")
    draw_arrow((b_sp2['right'], 37.0), (b_sp3['left'], 37.0))

    db_sp1 = draw_db_store(72.0, 37.0, "catatan_guru", "Jurnal Mengajar & Supervisi Kepsek")
    draw_arrow((b_sp3['right'], 37.0), (db_sp1['left'], 37.0))

    db_sp2 = draw_db_store(72.0, 33.6, "catatan_pembinaan", "Pelanggaran/Prestasi & Bimbingan")
    draw_arrow((db_sp1['cx'], db_sp1['bottom']), (db_sp2['cx'], db_sp2['top']))

    b_sp_read_ui = draw_action(30.5, 33.6, "Tampilkan Catatan & Tombol\n'Tandai Sudah Dibaca'")
    draw_arrow((db_sp2['left'], 33.6), (b_sp_read_ui['right'], 33.6))

    b_sp_user = draw_action(11.5, 33.6, "Kepsek / Siswa Konfirmasi\n(Status Baca Otomatis Terarsip)")
    draw_arrow((b_sp_read_ui['left'], 33.6), (b_sp_user['right'], 33.6))

    # === FASE 6: ASISTEN AI HADIR.IO & REKAP SENIN-JUMAT ===
    draw_phase_separator(31.4, "• FASE 6: ASISTEN AI HADIR.IO (NVIDIA NIM) & REKAPITULASI SENIN-JUMAT")
    draw_arrow((b_sp_user['cx'], b_sp_user['bottom']), (11.5, 29.65))

    b_ai1 = draw_action(11.5, 28.6, "Ketik Pertanyaan pada\nWidget Chat Asisten AI")
    b_ai2 = draw_action(30.5, 28.6, "Chat UI Kirim Prompt AJAX\nke backend `chat_api.php`")
    draw_arrow((b_ai1['right'], 28.6), (b_ai2['left'], 28.6))

    b_ai3 = draw_action(51.0, 28.6, "Intent Parser & RBAC Retrieval\nQuery Data Faktual Sekolah")
    draw_arrow((b_ai2['right'], 28.6), (b_ai3['left'], 28.6))

    db_ai = draw_db_store(72.0, 28.6, "pengaturan_sistem & 20 Tabel", "Ekstraksi Metrik Agregat DB")
    draw_arrow((b_ai3['right'], 28.6), (db_ai['left'], 28.6))

    # AI Inference in Lane 5 (Lane 4 empty at y = 25.0)
    b_ai4 = draw_action(51.0, 25.0, "Sintesis Prompt Kontekstual\nKirim Request ke NVIDIA NIM")
    draw_arrow((db_ai['cx'], db_ai['bottom']), (db_ai['cx'], 25.0))
    draw_arrow((db_ai['cx'], 25.0), (b_ai4['right'], 25.0))

    ext_ai = draw_action(90.0, 25.0, "NVIDIA Nemotron LLM API\nInference Jawaban Faktual", w=14.5, h=2.0, bg=EXT_BG, border=EXT_BORDER)
    draw_arrow((b_ai4['right'], 25.0), (ext_ai['left'], 25.0), text="REST API")

    b_ai_res = draw_action(30.5, 25.0, "Tampilkan Jawaban Faktual,\nRingkas & Langsung to the Point")
    draw_arrow((b_ai4['left'], 25.0), (b_ai_res['right'], 25.0))

    b_ai_user = draw_action(11.5, 25.0, "Membaca Jawaban Asisten AI\n(Informasi Akurat & Terpercaya)")
    draw_arrow((b_ai_res['left'], 25.0), (b_ai_user['right'], 25.0))

    # Rekapitulasi Presensi Flow (y = 20.8)
    draw_arrow((b_ai_user['cx'], b_ai_user['bottom']), (11.5, 21.85))

    b_rk1 = draw_action(11.5, 20.8, "Admin/Kepsek Pilih Periode\n& Rombel Rekap Presensi")
    b_rk2 = draw_action(30.5, 20.8, "Filter Multi-Parameter\n(Kelas, Jurusan & Tanggal)")
    draw_arrow((b_rk1['right'], 20.8), (b_rk2['left'], 20.8))

    b_rk3 = draw_action(51.0, 20.8, "Hitung Total Hari Efektif\n(FILTER KETAT: SENIN - JUMAT)")
    draw_arrow((b_rk2['right'], 20.8), (b_rk3['left'], 20.8))

    db_rk = draw_db_store(72.0, 20.8, "absensi_siswa, guru, wali_kelas", "Agregasi Hadir, Izin, Sakit, Alpa")
    draw_arrow((b_rk3['right'], 20.8), (db_rk['left'], 20.8))

    b_rk_export = draw_action(30.5, 17.2, "Generate Laporan Resmi\nEkspor File Excel / Cetak PDF")
    draw_arrow((db_rk['cx'], db_rk['bottom']), (db_rk['cx'], 17.2))
    draw_arrow((db_rk['cx'], 17.2), (b_rk_export['right'], 17.2))

    b_rk_dl = draw_action(11.5, 17.2, "Unduh Dokumen Rekap Resmi\n(Arsip Kurikulum & Akreditasi)")
    draw_arrow((b_rk_export['left'], 17.2), (b_rk_dl['right'], 17.2))

    # === FASE FINAL: COMMIT TRANSAKSI & SELESAI ===
    draw_phase_separator(14.8, "• FASE 7: AUDIT LOGGING, INTEGRITAS TRANSAKSI & PENUTUPAN SIKLUS")
    draw_arrow((b_rk_dl['cx'], b_rk_dl['bottom']), (11.5, 12.35))

    b_fin_user = draw_action(11.5, 11.3, "Konfirmasi Akhir Siklus\n& Logout / Idle Dashboard")
    b_fin_log = draw_action(51.0, 11.3, "Commit Transaksi Database, Audit Logging & Penutupan Sesi", w=22.0, h=1.8)
    draw_arrow((b_fin_user['right'], 11.3), (b_fin_log['left'], 11.3))

    # Activity Final Node (Double circle / Bullseye)
    end_outer = Circle((51.0, 7.2), 0.85, facecolor='#FFFFFF', edgecolor='#DC2626', linewidth=2.0, zorder=5)
    end_inner = Circle((51.0, 7.2), 0.55, facecolor='#DC2626', edgecolor='none', zorder=6)
    ax.add_patch(end_outer)
    ax.add_patch(end_inner)
    draw_arrow((b_fin_log['cx'], b_fin_log['bottom']), (51.0, 8.05))
    ax.text(51.0, 5.5, "FINISH (STATE INTEGRITY PRESERVED)", fontsize=7.5, fontweight='bold', color='#991B1B', ha='center', va='center')

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
    print(f"[SUCCESS] Perfect Activity Diagram saved to: {output_path}")

if __name__ == "__main__":
    out_file = r"c:\xampp\htdocs\absen\Activity_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    generate_perfect_activity(out_file)
