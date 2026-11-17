import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=120, bottom=120, left=180, right=180):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('w:top', top), ('w:bottom', bottom), ('w:left', left), ('w:right', right)]:
        node = OxmlElement(m)
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def add_callout(doc, title, text, bg_hex="F0F9FF", border_hex="0284C7"):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = tbl.cell(0, 0)
    set_cell_background(cell, bg_hex)
    set_cell_margins(cell, top=140, bottom=140, left=200, right=200)

    # Set left border thick, others none
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>'
        f'<w:top w:val="none"/>'
        f'<w:left w:val="single" w:sz="36" w:space="0" w:color="{border_hex}"/>'
        f'<w:bottom w:val="none"/>'
        f'<w:right w:val="none"/>'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)

    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)
    r_title = p.add_run(f"PETUNJUK BERBICARA ({title}):\n")
    r_title.bold = True
    r_title.font.size = Pt(10)
    r_title.font.color.rgb = RGBColor(0x03, 0x69, 0xA1)

    r_text = p.add_run(f'"{text}"')
    r_text.italic = True
    r_text.font.size = Pt(10.5)
    r_text.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_paragraph().paragraph_format.space_after = Pt(4)

def generate_script_docx(output_path):
    doc = docx.Document()

    # Set standard margins (1 inch all around)
    for section in doc.sections:
        section.top_margin = Inches(1.0)
        section.bottom_margin = Inches(1.0)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(1.0)

    # Styles setup
    style_normal = doc.styles['Normal']
    style_normal.font.name = 'Calibri'
    style_normal.font.size = Pt(11)
    style_normal.font.color.rgb = RGBColor(0x1E, 0x29, 0x3B)
    style_normal.paragraph_format.line_spacing = 1.2
    style_normal.paragraph_format.space_after = Pt(6)

    # TITLE HEADER
    p_title = doc.add_paragraph()
    p_title.paragraph_format.space_before = Pt(0)
    p_title.paragraph_format.space_after = Pt(4)
    p_title.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_main_title = p_title.add_run("NASKAH PRESENTASI & PENJELASAN TEKNIS LENGKAP\nUSE CASE DIAGRAM & ACTIVITY DIAGRAM")
    r_main_title.bold = True
    r_main_title.font.size = Pt(18)
    r_main_title.font.color.rgb = RGBColor(0x1E, 0x3A, 0x8A)

    p_sub = doc.add_paragraph()
    p_sub.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_after = Pt(16)
    r_sub = p_sub.add_run("Arsitektur Fungsional dan Alur Terpadu 20 Tabel Database `db_absensi_smk`\nSistem Presensi Digital SMK NU WARGABINANGUN")
    r_sub.font.size = Pt(12)
    r_sub.font.color.rgb = RGBColor(0x02, 0x84, 0xC7)
    r_sub.bold = True

    # METADATA TABLE
    meta_tbl = doc.add_table(rows=4, cols=2)
    meta_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    meta_data = [
        ("Nama Sistem", "Sistem Presensi Digital SMK NU WARGABINANGUN (by Winarti)"),
        ("Cakupan Database", "Database `db_absensi_smk` (20 Tabel Relasional Utuh)"),
        ("Format Diagram", "High-Resolution PNG (300 DPI, Matriks 20 Tabel Terintegrasi)"),
        ("Tujuan Naskah", "Panduan Berbicara & Menjelaskan Diagram secara Profesional kepada Penguji / Stakeholder")
    ]
    for i, (k, v) in enumerate(meta_data):
        c0 = meta_tbl.cell(i, 0)
        c1 = meta_tbl.cell(i, 1)
        c0.text = k
        c1.text = v
        c0.paragraphs[0].runs[0].bold = True
        c0.paragraphs[0].runs[0].font.size = Pt(9.5)
        c1.paragraphs[0].runs[0].font.size = Pt(9.5)
        set_cell_background(c0, "F1F5F9")
        set_cell_background(c1, "FFFFFF")
        set_cell_margins(c0, top=80, bottom=80, left=120, right=120)
        set_cell_margins(c1, top=80, bottom=80, left=120, right=120)

    doc.add_paragraph().paragraph_format.space_after = Pt(12)

    # -------------------------------------------------------------
    # BAGIAN 1: PANDUAN UMUM
    # -------------------------------------------------------------
    h1 = doc.add_heading("BAGIAN 1: PANDUAN UMUM & FILOSOFI DIAGRAM TERPADU", level=1)
    h1.runs[0].font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    p_intro = doc.add_paragraph(
        "Naskah ini disusun khusus agar Anda dapat menjelaskan Use Case Diagram dan Activity Diagram "
        "dengan sangat percaya diri, terstruktur, sistematis, dan bernilai akademis tinggi. "
        "Sesuai dengan instruksi arsitektural yang ketat, kedua diagram ini TIDAK DIPISAH per role, "
        "melainkan digabungkan menjadi SATU DIAGRAM KESATUAN UTUH yang merangkul SELURUH 20 TABEL dalam database db_absensi_smk."
    )

    doc.add_paragraph(
        "Filosofi di balik pendekatan ini adalah 'Single Source of Truth' dan 'Enterprise Lifecycle Integration'. "
        "Dalam aplikasi presensi sekolah modern, peran pengguna (Admin, Kepala Sekolah, Wali Kelas, Guru, dan Siswa) "
        "serta entitas eksternal (Bot Telegram dan Engine AI NVIDIA) saling berinteraksi pada tabel-tabel data yang sama. "
        "Oleh karena itu, memisahkan diagram per role hanya akan mengaburkan integrasi data, sedangkan diagram terpadu "
        "menampilkan orkestrasi sistem secara menyeluruh dan transparan."
    )

    add_callout(
        doc,
        "Pembukaan Presentasi",
        "Assalamu’alaikum Warahmatullahi Wabarakatuh, Selamat pagi/siang kepada Bapak/Ibu Dewan Penguji dan rekan-rekan sekalian. "
        "Pada kesempatan yang berharga ini, saya, Winarti, akan memaparkan rancang bangun arsitektur sistem dari Sistem Presensi Digital "
        "SMK NU WARGABINANGUN melalui dua model UML utama, yaitu Use Case Diagram Terpadu dan Activity Diagram Siklus Menyeluruh. "
        "Kedua diagram ini dirancang berbasiskan 20 tabel database aktif db_absensi_smk dan memadukan interaksi seluruh aktor internal maupun layanan eksternal secara komprehensif."
    )

    # -------------------------------------------------------------
    # BAGIAN 2: USE CASE DIAGRAM
    # -------------------------------------------------------------
    h2 = doc.add_heading("BAGIAN 2: NASKAH DETAIL PENJELASAN USE CASE DIAGRAM", level=1)
    h2.runs[0].font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_paragraph(
        "Use Case Diagram menggambarkan batasan fungsional sistem (System Boundary), aktor-aktor yang terlibat, "
        "serta fungsi-fungsi inti (Use Case) yang disediakan sistem beserta relasi dependensi (<<include>> dan <<extend>>) "
        "dan pemetaan tabel databasenya."
    )

    add_callout(
        doc,
        "Menjelaskan System Boundary & Aktor",
        "Bapak/Ibu Penguji, mari kita perhatikan sisi visual Use Case Diagram ini. "
        "Di dalam kotak persegi panjang besar berlabel 'Boundary Sistem: Sistem Presensi Digital SMK NU WARGABINANGUN', "
        "terdapat 14 Use Case utama dan 4 Sub-Use Case khusus. "
        "Di sebelah kiri, kita memiliki 5 Aktor Utama (Human Actors):\n"
        "1. Administrator: Penanggung jawab kontrol penuh tata kelola master akademik, kredensial, dan konfigurasi server.\n"
        "2. Kepala Sekolah: Pimpinan eksekutif yang melakukan supervisi manajerial terhadap KBM guru, pembinaan siswa, dan rekapitulasi kehadiran.\n"
        "3. Wali Kelas: Pendidik dengan peran ganda pembina rombel yang memantau presensi kelasnya dan catatan pembinaan karakter.\n"
        "4. Guru Pengajar: Tenaga pendidik yang mencatat presensi harian, jurnal KBM mengajar, dan pengajuan izin dinas.\n"
        "5. Peserta Didik (Siswa): Pengguna utama yang melakukan presensi barcode, mengajukan surat sakit/izin, serta menerima pembinaan.\n\n"
        "Sedangkan di sebelah kanan, terdapat 2 Aktor Eksternal (Secondary System Actors):\n"
        "1. Bot Telegram Gateway (@HadirioBot): Layanan eksternal yang mengeksekusi push notification kehadiran secara instan ke ponsel orang tua dan guru.\n"
        "2. NVIDIA NIM LLM AI Engine: Layanan kecerdasan buatan berbasis model bahasa besar Nemotron yang memproses penalaran bahasa alami pada fitur Chatbot Asisten AI Sekolah."
    )

    # Tabel Penjelasan 14 Use Case & 20 Tabel
    doc.add_heading("Pemetaan 14 Use Case terhadap 20 Tabel Database db_absensi_smk", level=2)
    uc_tbl = doc.add_table(rows=15, cols=4)
    uc_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    headers = ["No & Kode", "Nama Use Case", "Tabel Database Terkait", "Aktor yang Berinteraksi"]
    for j, h in enumerate(headers):
        cell = uc_tbl.cell(0, j)
        cell.text = h
        cell.paragraphs[0].runs[0].bold = True
        cell.paragraphs[0].runs[0].font.size = Pt(9.5)
        set_cell_background(cell, "0F172A")
        cell.paragraphs[0].runs[0].font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
        set_cell_margins(cell, 80, 80, 100, 100)

    uc_rows = [
        ("UC01", "Login & Kelola Sesi Multi-Role", "users, roles", "Semua Role (Admin, Kepsek, Wali Kelas, Guru, Siswa)"),
        ("UC02", "Kelola Profil & Foto Pribadi", "users", "Semua Role"),
        ("UC03", "Kelola Master Tahun Ajaran & Rombel", "tahun_ajaran, jurusan, kelas", "Administrator"),
        ("UC04", "Kelola Data Guru & Wali Kelas", "guru, wali_kelas_data, wali_kelas", "Administrator"),
        ("UC05", "Kelola Master Peserta Didik", "siswa", "Administrator"),
        ("UC06", "Kelola Mapel & Jadwal KBM Mingguan", "mata_pelajaran, jadwal_mengajar", "Administrator"),
        ("UC07", "Generate Barcode & Kartu RFID", "barcode_guru, rfid_cards, siswa", "Administrator"),
        ("UC08", "Scan Presensi Masuk & Pulang", "absensi_siswa, absensi_guru, absensi_wali_kelas", "Siswa, Guru, Wali Kelas, Admin"),
        ("UC08.1", "<<include>> Validasi Waktu & Dual-Sync", "absensi_guru, absensi_wali_kelas", "Sistem Internal Controller"),
        ("UC08.2", "<<include>> Push Notifikasi Presensi", "absensi_siswa, pengaturan_sistem", "Bot Telegram (@HadirioBot)"),
        ("UC09", "Pengajuan & Approval Izin / Sakit", "pengajuan_izin", "Siswa, Guru (Pengaju), Admin (Approval)"),
        ("UC09.1", "<<include>> Sinkronisasi Rekap Izin", "absensi_siswa, absensi_guru", "Sistem Internal Controller"),
        ("UC10", "Supervisi KBM & Jurnal Mengajar", "catatan_guru", "Guru Pengajar (Input), Kepala Sekolah (Supervisi)"),
        ("UC11", "Catatan Pembinaan & Disiplin Siswa", "catatan_pembinaan", "Wali Kelas, Guru (Input), Siswa (Baca)"),
    ]

    for i, r_data in enumerate(uc_rows, start=1):
        for j in range(4):
            cell = uc_tbl.cell(i, j)
            cell.text = r_data[j]
            cell.paragraphs[0].runs[0].font.size = Pt(8.5)
            if i % 2 == 1:
                set_cell_background(cell, "F8FAFC")
            else:
                set_cell_background(cell, "FFFFFF")
            set_cell_margins(cell, 60, 60, 80, 80)

    # Tambahan baris UC12, UC13, UC14
    extra_rows = [
        ("UC12", "Rekap Presensi & Ekspor Laporan (Excel/PDF)", "absensi_siswa, absensi_guru, absensi_wali_kelas", "Admin, Kepala Sekolah, Wali Kelas"),
        ("UC13", "Konsultasi Informasi via Chatbot Hadir.io", "pengaturan_sistem & Seluruh 20 Tabel DB", "Semua Role"),
        ("UC13.1", "<<extend>> NLP Contextual Query Engine", "Konektor Model AI Nemotron", "NVIDIA NIM LLM API"),
        ("UC14", "Konfigurasi Sistem & Token API", "pengaturan_sistem", "Administrator")
    ]
    for r_data in extra_rows:
        row = uc_tbl.add_row()
        for j in range(4):
            cell = row.cells[j]
            cell.text = r_data[j]
            cell.paragraphs[0].runs[0].font.size = Pt(8.5)
            set_cell_background(cell, "F1F5F9")
            set_cell_margins(cell, 60, 60, 80, 80)

    add_callout(
        doc,
        "Menjelaskan Relasi <<include>> dan <<extend>>",
        "Dewan Penguji yang saya hormati, pada diagram ini terdapat dua jenis hubungan relasi UML stereotipe yang sangat penting:\n"
        "Pertama, relasi <<include>> pada UC08 (Scan Presensi) menuju UC08.1 (Validasi Waktu & Dual-Sync) dan UC08.2 (Push Notifikasi). "
        "Arti dari <<include>> adalah bahwa aksi scan presensi TIDAK BISA DIPISAHKAN dari pengecekan jam server dan pengiriman notifikasi ke Telegram. Setiap kali barcode discan, validasi aturan jam dan transmisi pesan Telegram pasti dieksekusi secara otomatis.\n\n"
        "Kedua, relasi <<extend>> pada UC13 (Chatbot Hadir.io) menuju UC13.1 (NLP Contextual Query Engine via NVIDIA). "
        "Arti dari <<extend>> adalah perluasan kondisional. Jika pertanyaan pengguna berupa sapaan sederhana atau data lokal, sistem dapat menjawab secara langsung. Namun jika pengguna mengajukan pertanyaan analitis kompleks (seperti rekapitulasi siswa alpa atau jadwal mengajar), use case akan memperluas fungsionalitasnya dengan memanggil NVIDIA NIM LLM API untuk menyusun jawaban alami yang cerdas dan ringkas."
    )

    # -------------------------------------------------------------
    # BAGIAN 3: ACTIVITY DIAGRAM
    # -------------------------------------------------------------
    h3 = doc.add_heading("BAGIAN 3: NASKAH DETAIL PENJELASAN ACTIVITY DIAGRAM", level=1)
    h3.runs[0].font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_paragraph(
        "Activity Diagram menggambarkan alur kerja (workflow) operasional sistem dari status awal (Initial Node) "
        "hingga status selesai (Final Node), dengan pembagian tanggung jawab menggunakan 5 Swimlanes (Partisi Horizontal) "
        "serta integrasi siklus data pada 20 tabel database."
    )

    add_callout(
        doc,
        "Menjelaskan 5 Swimlanes",
        "Beralih ke Activity Diagram, di sini saya membagi alur kerja ke dalam 5 Swimlanes terstruktur:\n"
        "1. Swimlane Aktor / Pengguna: Menggambarkan tindakan fisik pengguna manusia, seperti menginput data, memindai barcode, dan mengunggah dokumen.\n"
        "2. Swimlane UI / Web Presentation: Menangani respons antarmuka, kamera HTML5 scanner, validasi form di sisi browser, dan penayangan hasil interaktif.\n"
        "3. Swimlane Controller & Business Logic: Inti mesin aplikasi di sisi server yang bertugas memverifikasi keamanan, menghitung aturan jam presensi, menjalankan dual-sync, dan mengatur hak akses (RBAC).\n"
        "4. Swimlane Database Layer: Menunjukkan secara eksplisit tabel mana dari 20 tabel database db_absensi_smk yang sedang dibaca (SELECT), ditulis (INSERT), atau diperbarui (UPDATE).\n"
        "5. Swimlane External Services: Menunjukkan komunikasi data asynchronous dengan server Bot Telegram dan NVIDIA NIM AI Engine."
    )

    doc.add_heading("Alur Siklus Hidup Operasional (Lifecycle Flow)", level=2)
    doc.add_paragraph(
        "Berikut adalah 6 tahapan siklus hidup sistem yang dialirkan melalui Fork Bar dan disatukan kembali pada Join Bar:"
    )

    steps = [
        ("1. Fase Inisialisasi & Otentikasi:",
         "Dimulai dari Start Node pada Swimlane Aktor yang memasukkan username dan password. UI mengirim payload via POST, Controller memverifikasi hash Bcrypt/MD5 dan mencocokkan hak akses pada tabel `users` dan `roles`. Setelah valid, sesi PHP dibuka dan dashboard yang sesuai dirender."),

        ("2. Fork Bar (Percabangan Paralel):",
         "Garis tebal horizontal (Fork Synchronization Bar) menunjukkan bahwa setelah sistem terotentikasi, sistem siap melayani 6 rangkaian proses operasional independen secara paralel."),

        ("3. Jalur 1 - Master Akademik & Barcode:",
         "Admin mengisi data sekolah dan rombel. Controller memeriksa integritas referensial dan menyimpan data ke tabel `tahun_ajaran`, `jurusan`, `kelas`, `guru`, `wali_kelas_data`, `wali_kelas`, `siswa`, `mata_pelajaran`, dan `jadwal_mengajar`. Kemudian mesin barcode mengenkripsi nomor identitas menjadi representasi QR code dan menyimpannya pada tabel `barcode_guru` dan `rfid_cards`."),

        ("4. Jalur 2 - Presensi Real-Time, Aturan Waktu & Dual-Sync (Core Flow):",
         "Pengguna mengarahkan kartu barcode ke kamera. Scanner membaca payload dan mengirim request AJAX ke scan_handler.php. Decision Diamond mengevaluasi jam server:\n"
         "• Jam 05:00 - 07:00 WIB: Status dicatat 'Hadir' (Tepat Waktu).\n"
         "• Lebih dari 07:00 WIB: Status dicatat 'Terlambat'.\n"
         "• Pukul 15:00 WIB ke atas: Dicatat sebagai scan pulang.\n"
         "Data presensi disimpan ke `absensi_siswa` untuk siswa, atau `absensi_guru` untuk guru. Khusus Wali Kelas, Controller mengeksekusi fitur DUAL-SYNC, yaitu mencatat transaksi secara simultan ke tabel `absensi_wali_kelas` dan `absensi_guru` agar tidak terjadi anomali data pendidik. Seketika itu pula, Controller mentrigger cURL ke Swimlane Layanan Eksternal (Bot Telegram) untuk mengirim notifikasi instan."),

        ("5. Jalur 3 - Dispensasi Izin / Sakit & Auto-Sync:",
         "Siswa atau Guru mengunggah surat dokter atau keterangan izin. File divalidasi MIME type dan ukurannya, lalu disimpan ke tabel `pengajuan_izin` dengan status 'Menunggu'. Saat Admin memberikan approval 'Disetujui', Controller secara otomatis mengeksekusi SINKRONISASI REKAPITULASI ke tabel `absensi_siswa` atau `absensi_guru` dengan status Izin/Sakit tanpa perlu input manual kedua kali."),

        ("6. Jalur 4 - Supervisi KBM & Pembinaan Karakter:",
         "Guru mengisi jurnal harian mengajar yang tersimpan di tabel `catatan_guru`. Kepala Sekolah dapat memantau keterlaksanaan KBM dan status baca supervisi. Pada saat yang sama, pelanggaran disiplin atau prestasi siswa dicatat oleh Wali Kelas ke tabel `catatan_pembinaan`, yang dapat dilihat langsung oleh Siswa dan Wali Murid."),

        ("7. Jalur 5 - Asisten AI Hadir.io & Rekapitulasi Senin-Jumat:",
         "Pengguna mengetikkan pertanyaan pada widget chatbot Hadir.io. Controller chat_api.php menerapkan filter RBAC ketat, mengekstraksi metrik faktual dari 20 tabel database, dan mengirimkan prompt terstruktur ke NVIDIA NIM LLM API. AI mengembalikan respons yang padat, akurat, dan to-the-point.\n"
         "Untuk laporan rekapitulasi, Controller menerapkan FILTER KETAT SENIN-JUMAT (mengabaikan hari Sabtu dan Minggu), menghitung persentase kehadiran dari tabel absensi, dan mengekspornya ke dalam format Excel (.xlsx) atau PDF."),

        ("8. Join Bar & Penutupan Status (Finish Node):",
         "Seluruh jalur operasional disatukan kembali pada Join Synchronization Bar. Sistem melakukan pencatatan audit log, menjaga konsistensi state transaksi, dan mengakhiri siklus operasional pada Final Node yang berstatus aman dan terintegrasi.")
    ]

    for title, desc in steps:
        p = doc.add_paragraph()
        r_t = p.add_run(title + " ")
        r_t.bold = True
        r_t.font.color.rgb = RGBColor(0x02, 0x84, 0xC7)
        p.add_run(desc)

    add_callout(
        doc,
        "Menjelaskan Keputusan Jam Server & Filter Senin-Jumat",
        "Dewan Penguji yang terhormat, jika diperhatikan pada Decision Node di Swimlane Controller, sistem menerapkan aturan bisnis yang sangat ketat: "
        "Pertama, jam presensi masuk dibatasi maksimal pukul 07:00 WIB, lewat dari itu otomatis berstatus Terlambat. "
        "Kedua, jam pulang baru dapat diakses mulai pukul 15:00 WIB. "
        "Ketiga, dan ini sangat krusial, perhitungan rekapitulasi kehadiran hanya menghitung hari efektif Senin hingga Jumat. Hari Sabtu dan Minggu tidak dimasukkan ke dalam pembagi total hari sekolah, sehingga statistik kehadiran siswa dan guru tetap 100% presisi dan adil."
    )

    # -------------------------------------------------------------
    # BAGIAN 4: SIMULASI TANYA JAWAB (Q&A) DENGAN DEWAN PENGUJI
    # -------------------------------------------------------------
    h4 = doc.add_heading("BAGIAN 4: SIMULASI TANYA JAWAB (Q&A) & KUNCI JAWABAN PENGUJI", level=1)
    h4.runs[0].font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    qa_list = [
        ("Pertanyaan 1: Mengapa Use Case dan Activity Diagram ini digabungkan secara utuh dan tidak dibuat terpisah per role?",
         "Jawaban: Karena dalam rekayasa perangkat lunak modern berorientasi relasional, aplikasi berbasis database bersama (db_absensi_smk) memiliki alur data yang saling bertransaksi antar-role. Jika diagram dipisah per role, keterhubungan data seperti Dual-Sync Wali Kelas, persetujuan Izin oleh Admin, dan pemantauan KBM oleh Kepala Sekolah akan tampak terfragmentasi dan terputus. Dengan diagram terpadu ini, seluruh pemangku kepentingan dapat melihat transparansi arsitektur end-to-end dalam satu peta sistem yang utuh."),

        ("Pertanyaan 2: Di mana letak keterlibatan ke-20 tabel database pada diagram Use Case dan Activity Diagram yang Anda buat?",
         "Jawaban: Ke-20 tabel terpetakan secara lengkap dan presisi di kedua diagram:\n"
         "• Keamanan & Akun: `users`, `roles` pada autentikasi sesi.\n"
         "• Master Akademik: `tahun_ajaran`, `jurusan`, `kelas`, `guru`, `wali_kelas_data`, `wali_kelas`, `siswa`, `mata_pelajaran`, `jadwal_mengajar` pada manajemen master data.\n"
         "• Kredensial Barcode: `barcode_guru`, `rfid_cards` pada generator kartu QR.\n"
         "• Transaksi Presensi: `absensi_siswa`, `absensi_guru`, `absensi_wali_kelas`, `pengajuan_izin` pada siklus presensi dan dispensasi.\n"
         "• Catatan & Konfigurasi: `catatan_guru`, `catatan_pembinaan`, `pengaturan_sistem` pada jurnal supervisi, pembinaan karakter, dan token integrasi API."),

        ("Pertanyaan 3: Mengapa ada dua use case bertipe <<include>> pada fungsi Scan Presensi (UC08)?",
         "Jawaban: Relasi <<include>> menandakan keharusan eksekusi mutlak (mandatory). Saat seorang siswa atau guru memindai kartu barcode, sistem secara inheren dan mutlak WAJIB menjalankan validasi jam server (UC08.1) untuk menentukan status 'Tepat Waktu' atau 'Terlambat', serta WAJIB mentrigger push notifikasi Telegram (UC08.2) untuk memberitahukan kehadiran secara real-time. Tanpa kedua proses itu, use case scan presensi dianggap belum lengkap."),

        ("Pertanyaan 4: Apa yang dimaksud dengan mekanisme 'Dual-Sync' pada presensi Wali Kelas?",
         "Jawaban: Wali Kelas di SMK NU WARGABINANGUN pada hakikatnya adalah seorang Guru Pengajar yang mendapatkan tugas tambahan manajerial. Oleh karena itu, ketika Wali Kelas melakukan absensi, Controller secara otomatis melakukan sinkronisasi ganda (Dual-Sync) ke tabel `absensi_wali_kelas` dan tabel `absensi_guru`. Hal ini mencegah kekosongan data pada rekapitulasi kehadiran guru secara umum maupun laporan khusus dewan wali kelas."),

        ("Pertanyaan 5: Bagaimana Chatbot AI Hadir.io mengamankan data rahasia sekolah?",
         "Jawaban: Seperti terlihat pada Swimlane Controller di Activity Diagram, chatbot Hadir.io tidak langsung mengarahkan teks mentah pengguna ke database atau ke model LLM. Controller menerapkan lapisan Role-Based Access Control (RBAC). Jika siswa bertanya tentang gaji guru atau data di luar hak aksesnya, controller memblokir kueri tersebut di tingkat database lokal, sehingga prompt yang dikirim ke NVIDIA NIM LLM API hanya memuat data publik yang diizinkan untuk role tersebut.")
    ]

    for q, a in qa_list:
        p_q = doc.add_paragraph()
        r_q = p_q.add_run(q)
        r_q.bold = True
        r_q.font.color.rgb = RGBColor(0x1E, 0x3A, 0x8A)

        p_a = doc.add_paragraph()
        p_a.add_run(a)
        p_a.paragraph_format.space_after = Pt(8)

    # PENUTUP
    add_callout(
        doc,
        "Kalimat Penutup Presentasi",
        "Demikianlah pemaparan komprehensif mengenai Use Case Diagram dan Activity Diagram dari Sistem Presensi Digital SMK NU WARGABINANGUN. "
        "Dengan arsitektur yang teruji memadukan seluruh 20 tabel database db_absensi_smk, sistem ini siap memberikan efisiensi, akurasi, dan transparansi tinggi bagi kemajuan digitalisasi sekolah. "
        "Terima kasih atas perhatian Bapak/Ibu Dewan Penguji. Saya siap menerima masukan, saran, dan pertanyaan lebih lanjut. "
        "Wabillahi taufiq wal hidayah, Wassalamu’alaikum Warahmatullahi Wabarakatuh."
    )

    doc.save(output_path)
    print(f"[SUCCESS] Naskah Docx saved to: {output_path}")

if __name__ == "__main__":
    out_docx = r"c:\xampp\htdocs\absen\Naskah_Penjelasan_Usecase_dan_Activity_Diagram.docx"
    generate_script_docx(out_docx)
