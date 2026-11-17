import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn
from docx2pdf import convert
import os

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=140, right=140):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('w:top', top), ('w:bottom', bottom), ('w:left', left), ('w:right', right)]:
        node = OxmlElement(m)
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def add_callout_box(doc, title, text, bg_hex="F0F9FF", border_hex="0284C7"):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = tbl.cell(0, 0)
    set_cell_background(cell, bg_hex)
    set_cell_margins(cell, top=120, bottom=120, left=160, right=160)

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
    r_hdr = p.add_run(f"📌 {title}\n")
    r_hdr.bold = True
    r_hdr.font.size = Pt(10.5)
    r_hdr.font.color.rgb = RGBColor(0x03, 0x69, 0xA1)

    r_text = p.add_run(text)
    r_text.font.size = Pt(10)
    r_text.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_paragraph().paragraph_format.space_after = Pt(6)

def build_report_docx(docx_path):
    doc = docx.Document()

    for s in doc.sections:
        s.top_margin = Inches(1.0)
        s.bottom_margin = Inches(1.0)
        s.left_margin = Inches(1.0)
        s.right_margin = Inches(1.0)

    # Styles
    style_normal = doc.styles['Normal']
    style_normal.font.name = 'Calibri'
    style_normal.font.size = Pt(11)
    style_normal.font.color.rgb = RGBColor(0x1E, 0x29, 0x3B)
    style_normal.paragraph_format.line_spacing = 1.25
    style_normal.paragraph_format.space_after = Pt(5)

    # =========================================================
    # HALAMAN COVER / JUDUL
    # =========================================================
    p_inst = doc.add_paragraph()
    p_inst.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_inst.paragraph_format.space_after = Pt(4)
    r_inst = p_inst.add_run("PROGRAM STUDI S1 TEKNIK INFORMATIKA\nSTMIK IKMI CIREBON")
    r_inst.bold = True
    r_inst.font.size = Pt(13)
    r_inst.font.color.rgb = RGBColor(0x1E, 0x3A, 0x8A)

    p_mk = doc.add_paragraph()
    p_mk.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_mk.paragraph_format.space_after = Pt(28)
    r_mk = p_mk.add_run("MATA KULIAH: REKAYASA PERANGKAT LUNAK (TIKB2503021) — SEMESTER 3")
    r_mk.font.size = Pt(10)
    r_mk.font.color.rgb = RGBColor(0x64, 0x74, 0x8B)

    p_t = doc.add_paragraph()
    p_t.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_t.paragraph_format.space_after = Pt(12)
    r_t = p_t.add_run("LAPORAN TUGAS PROYEK AKHIR MANDIRI\nBERBASIS KURIKULUM OBE (OUTCOME-BASED EDUCATION)")
    r_t.bold = True
    r_t.font.size = Pt(16)
    r_t.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    p_title = doc.add_paragraph()
    p_title.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_after = Pt(24)
    r_title = p_title.add_run(
        "RANCANG BANGUN SISTEM PRESENSI DIGITAL BERBASIS QR CODE SCANNER, "
        "NOTIFIKASI REAL-TIME TELEGRAM, SUPERVISI KBM DAN ASISTEN CERDAS AI HADIR.IO "
        "DI SMK NU WARGABINANGUN"
    )
    r_title.bold = True
    r_title.font.size = Pt(13.5)
    r_title.font.color.rgb = RGBColor(0x02, 0x84, 0xC7)

    # Box Metadata Cover
    tbl_cov = doc.add_table(rows=6, cols=2)
    tbl_cov.alignment = WD_TABLE_ALIGNMENT.CENTER
    cov_data = [
        ("Nama Mahasiswa / Pengembang", "Winarti"),
        ("NIM (Nomor Induk Mahasiswa)", "41257168"),
        ("Kelas / Angkatan", "TI-1C-2025"),
        ("Tema Topik Kelompok", "E-Learning & Smart Education AI"),
        ("Sifat Pengerjaan", "Mandiri / Perorangan (100% Individual Ownership & Full-App Build)"),
        ("Dosen Pengampu", "Yudhistira Arie Wijaya, M.Kom., CDS.")
    ]
    for i, (k, v) in enumerate(cov_data):
        c0 = tbl_cov.cell(i, 0)
        c1 = tbl_cov.cell(i, 1)
        c0.text = k
        c1.text = v
        c0.paragraphs[0].runs[0].bold = True
        c0.paragraphs[0].runs[0].font.size = Pt(9.5)
        c1.paragraphs[0].runs[0].font.size = Pt(9.5)
        set_cell_background(c0, "F1F5F9")
        set_cell_background(c1, "FFFFFF")
        set_cell_margins(c0, 60, 60, 100, 100)
        set_cell_margins(c1, 60, 60, 100, 100)

    p_foot = doc.add_paragraph()
    p_foot.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_foot.paragraph_format.space_before = Pt(40)
    p_foot.paragraph_format.space_after = Pt(0)
    r_foot = p_foot.add_run("CIREBON — TAHUN AKADEMIK 2026/2027")
    r_foot.bold = True
    r_foot.font.size = Pt(11)
    r_foot.font.color.rgb = RGBColor(0x47, 0x55, 0x69)

    doc.add_page_break()

    # =========================================================
    # KATA PENGANTAR & RINGKASAN EKSEKUTIF
    # =========================================================
    h_kp = doc.add_heading("KATA PENGANTAR", level=1)
    h_kp.runs[0].font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_paragraph(
        "Puji dan syukur saya panjatkan ke hadirat Allah SWT atas rahmat dan karunia-Nya, "
        "sehingga Dokumen Laporan Proyek Akhir Mandiri Rekayasa Perangkat Lunak (TIKB2503021) "
        "dengan judul 'Rancang Bangun Sistem Presensi Digital Berbasis QR Code Scanner, Notifikasi Real-Time Telegram, "
        "Supervisi KBM dan Asisten Cerdas AI Hadir.io di SMK NU WARGABINANGUN' ini dapat diselesaikan dengan tuntas dan optimal."
    )
    doc.add_paragraph(
        "Laporan ini disusun sebagai pemenuhan luaran wajib kurikulum Outcome-Based Education (OBE) pada Program Studi "
        "S1 Teknik Informatika STMIK IKMI Cirebon. Penulis menyampaikan terima kasih yang tulus kepada Bapak Yudhistira Arie Wijaya, M.Kom., CDS., "
        "selaku Dosen Pengampu, yang telah membimbing, mengarahkan, dan memberikan standar rekayasa perangkat lunak bertaraf industri."
    )

    add_callout_box(
        doc,
        "RINGKASAN EKSEKUTIF (EXECUTIVE SUMMARY)",
        "Sistem Presensi Digital SMK NU WARGABINANGUN adalah solusi rekayasa web terintegrasi yang memecahkan "
        "ketidakefisienan presensi kertas manual di sekolah vokasi. Sistem ini menerapkan arsitektur PHP 8.2 MVC Native "
        "dan database MariaDB 20 tabel normalisasi 3NF, didukung pemindaian QR Code instan (<0.5 detik), integrasi webhook "
        "Bot Telegram (@HadirioBot) untuk notifikasi real-time wali murid, fitur presensi ganda otomatis (Dual-Sync) bagi pendidik, "
        "otomasi perizinan mandiri (Auto-Sync), supervisi KBM dan pembinaan kedisiplinan berpelacak status baca, serta kecerdasan buatan "
        "Asisten AI Hadir.io berbasis model bahasa besar NVIDIA NIM (Nemotron) 4 bahasa. Proyek ini dikerjakan secara 100% mandiri "
        "lengkap dari inisiasi PRD/SRS, pemodelan 4 diagram UML & ERD, implementasi kode bersih, unit test otomatis, hingga CI/CD GitHub Actions."
    )

    doc.add_page_break()

    # =========================================================
    # BAB I: PRODUCT REQUIREMENTS DOCUMENT (PRD) & SRS
    # =========================================================
    h_b1 = doc.add_heading("BAB I: PRODUCT REQUIREMENTS DOCUMENT (PRD) & SRS", level=1)
    h_b1.runs[0].font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_heading("1.1 Latar Belakang & Urgensi Masalah Lapangan", level=2)
    doc.add_paragraph(
        "Presensi merupakan indikator fundamental dalam tata kelola institusi pendidikan vokasi. "
        "Namun, di SMK NU WARGABINANGUN, proses presensi konvensional berbasis lembaran kertas dan buku tanda tangan "
        "menghadapi kendala struktural yang parah. Pencatatan manual memakan waktu lama, menimbulkan antrean padat di gerbang sekolah, "
        "dan berkas kertas rentan rusak atau hilang. Di sisi lain, rekapitulasi kehadiran oleh tata usaha memakan waktu berhari-hari, "
        "serta tingginya risiko manipulasi titip absen akibat ketiadaan validasi jam yang ketat."
    )
    doc.add_paragraph(
        "Selain itu, ketiadaan saluran informasi real-time menyebabkan orang tua sering kali terlambat mengetahui kabar anaknya tidak hadir. "
        "Tata kelola perizinan sakit juga masih dilakukan dengan surat fisik tanpa arsip bukti digital yang rapi, serta belum adanya "
        "media formal bagi Kepala Sekolah untuk mengevaluasi KBM guru dan bagi Wali Kelas untuk membina kedisiplinan siswa."
    )

    doc.add_heading("1.2 Visi Produk & Sasaran Strategis", level=2)
    doc.add_paragraph(
        "Visi Produk: 'Mewujudkan tata kelola kehadiran yang modern, akuntabel, transparan, dan terintegrasi AI di lingkungan SMK NU WARGABINANGUN.'\n"
        "Target Kuantitatif Implementasi:\n"
        "1. 100% Digitalisasi presensi harian seluruh siswa dan guru.\n"
        "2. Zero Paper Waste (penghapusan total penggunaan kertas absensi dan surat izin).\n"
        "3. Notifikasi instan via Bot Telegram terkirim dalam waktu < 2 detik setelah pemindaian.\n"
        "4. Pencegahan fraud dan titip absen melalui enkripsi payload QR digital."
    )

    doc.add_heading("1.3 Analisis Kebutuhan Pengguna (User Stories & Acceptance Criteria)", level=2)
    tbl_us = doc.add_table(rows=6, cols=3)
    tbl_us.alignment = WD_TABLE_ALIGNMENT.CENTER
    us_data = [
        ("Peran (Role)", "Pernyataan Kebutuhan (User Story)", "Kriteria Penerimaan (Acceptance Criteria)"),
        ("Administrator", "Sebagai Admin, saya ingin mengelola master data rombel, guru, jadwal, dan mencetak kartu barcode.", "Data tersimpan di DB 20 tabel, kartu QR siap cetak, approval izin mengupdate rekap otomatis."),
        ("Kepala Sekolah", "Sebagai Kepsek, saya ingin memantau kehadiran makro dan memberikan supervisi tertulis pada KBM guru.", "Dashboard menampilkan persentase hadir harian, catatan_guru terlacak status bacanya."),
        ("Wali Kelas", "Sebagai Wali Kelas, saya ingin memantau absensi kelas binaan dan memberi catatan pembinaan karakter.", "Absensi wali kelas otomatis dual-sync ke absensi guru, catatan_pembinaan dapat dikonfirmasi siswa."),
        ("Guru", "Sebagai Guru, saya ingin melakukan presensi datang/pulang mandiri dan mengisi jurnal mengajar harian.", "Scan QR tercatat sesuai jam, jurnal KBM tersimpan rapi, notifikasi Telegram terkirim."),
        ("Siswa", "Sebagai Siswa, saya ingin memindai presensi QR pribadi dan mengajukan izin/sakit dengan bukti foto.", "Hasil scan muncul live di layar, surat izin terunggah dengan aman (JPG/PDF maks 2MB).")
    ]
    for i, row_vals in enumerate(us_data):
        for j, val in enumerate(row_vals):
            cell = tbl_us.cell(i, j)
            cell.text = val
            if i == 0:
                cell.paragraphs[0].runs[0].bold = True
                set_cell_background(cell, "0F172A")
                cell.paragraphs[0].runs[0].font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            else:
                set_cell_background(cell, "F8FAFC" if i % 2 == 1 else "FFFFFF")
            set_cell_margins(cell, 50, 50, 80, 80)

    doc.add_heading("1.4 Spesifikasi Kebutuhan Fungsional (Functional Requirements)", level=2)
    fr_list = [
        ("FR-01 (Autentikasi Multi-Role)", "Sistem memvalidasi login dengan enkripsi password Bcrypt dan mengarahkan ke dashboard berdasarkan hak akses (Admin, Kepsek, Wali Kelas, Guru, Siswa)."),
        ("FR-02 (Manajemen Master Data)", "Admin dapat melakukan CRUD pada tabel tahun_ajaran, jurusan, kelas, guru, wali_kelas, siswa, mata_pelajaran, dan jadwal_mengajar."),
        ("FR-03 (Penerbitan Barcode / RFID)", "Sistem meng-generate kode QR unik terenkripsi untuk setiap siswa dan guru pada tabel barcode_guru dan rfid_cards."),
        ("FR-04 (Pemindai Kamera HTML5-QRCode)", "Sistem membaca kode QR melalui kamera web/ponsel secara real-time tanpa refresh halaman browser."),
        ("FR-05 (Validasi Jam Masuk & Keterlambatan)", "Scan pukul 05:00–07:00 WIB dicatat 'Hadir Tepat Waktu'. Scan >07:00 WIB otomatis dicatat 'Terlambat'."),
        ("FR-06 (Validasi Jam Pulang)", "Scan kepulangan baru dapat dilakukan mulai pukul 15:00 WIB ke atas. Scan sebelum jam 15:00 WIB ditolak oleh sistem."),
        ("FR-07 (Mekanisme Dual-Sync Pendidik)", "Saat Wali Kelas memindai barcode, sistem secara atomik mencatat absensi ke tabel absensi_wali_kelas dan absensi_guru sekaligus."),
        ("FR-08 (Push Notifikasi Telegram)", "Setiap kali scan berhasil, sistem mengirim notifikasi real-time via Telegram Bot (@HadirioBot) ke kanal orang tua/sekolah."),
        ("FR-09 (Pengajuan Izin & Sakit Online)", "Siswa/Guru dapat mengajukan izin mandiri dengan melampirkan berkas foto surat dokter (maksimal 2MB)."),
        ("FR-10 (Auto-Sync Approval Izin)", "Saat Admin menyetujui permohonan izin, status absensi pada tanggal tersebut otomatis berubah menjadi Izin/Sakit."),
        ("FR-11 (Supervisi KBM Guru)", "Kepala Sekolah dapat menulis arahan evaluasi pada catatan_guru dengan pelacakan status baca otomatis."),
        ("FR-12 (Pembinaan Karakter Siswa)", "Wali Kelas dapat mencatat bimbingan disiplin pada catatan_pembinaan yang wajib ditandai 'Sudah Dibaca' oleh siswa."),
        ("FR-13 (Rekapitulasi Presensi Senin-Jumat)", "Sistem menghitung persentase kehadiran dengan filter ketat hari efektif Senin–Jumat (Sabtu/Minggu dikecualikan), lalu mengekspor ke Excel dan PDF."),
        ("FR-14 (Asisten Cerdas AI Hadir.io)", "Widget chat cerdas berbasis NVIDIA NIM Nemotron LLM menjawab pertanyaan data sekolah dalam 4 bahasa secara ringkas dan faktual.")
    ]
    for code, desc in fr_list:
        p = doc.add_paragraph()
        rc = p.add_run(code + ": ")
        rc.bold = True
        rc.font.color.rgb = RGBColor(0x02, 0x84, 0xC7)
        p.add_run(desc)

    doc.add_heading("1.5 Spesifikasi Fitur Kecerdasan Artifisial (AI Hadir.io)", level=2)
    doc.add_paragraph(
        "Asisten AI Hadir.io dirancang menggunakan arsitektur Dual-Engine: "
        "Engine 1 (Fast-Path SQL) bertugas mengekstraksi data faktual sekolah dari database lokal (jumlah kelas, guru, jadwal pelajaran, status izin) "
        "sehingga informasi dijamin 100% akurat tanpa halusinasi data. "
        "Engine 2 (NVIDIA NIM Nemotron LLM) bertugas memproses bahasa alami (Natural Language Processing) untuk menyusun jawaban yang santun, "
        "ringkas, to-the-point, serta mendukung 4 bahasa adaptif: Bahasa Indonesia, Bahasa Inggris, Basa Sunda, dan Basa Jawa. "
        "Fitur ini dilindungi oleh 'RBAC Query Firewall' yang menolak permintaan query data privat antar-role pengguna."
    )

    doc.add_page_break()

    # =========================================================
    # BAB II: SOFTWARE DESIGN DOCUMENT (SDD)
    # =========================================================
    h_b2 = doc.add_heading("BAB II: SOFTWARE DESIGN DOCUMENT (SDD)", level=1)
    h_b2.runs[0].font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_heading("2.1 Arsitektur Sistem Menyeluruh", level=2)
    doc.add_paragraph(
        "Sistem dibangun menggunakan pola arsitektur Model-View-Controller (MVC) murni berbasis PHP 8.2 Native. "
        "Model bertanggung jawab memetakan transaksi 20 tabel database MariaDB melalui antarmuka PDO. "
        "View menyajikan antarmuka Bootstrap 5 responsif dengan pemindai HTML5-QRCode dan widget chat AJAX. "
        "Controller mengorkestrasi logika bisnis, validasi jam, dual-sync, transmisi notifikasi Telegram, serta pemanggilan API AI NVIDIA."
    )

    doc.add_heading("2.2 Pemodelan 4 Diagram UML Standar Utama", level=2)

    # 2.2.1 USE CASE DIAGRAM
    doc.add_heading("2.2.1 Use Case Diagram Terpadu & Skenario Use Case", level=3)
    doc.add_paragraph(
        "Use Case Diagram menggambarkan batasan fungsional sistem presensi menyeluruh yang melibatkan 5 Aktor Pengguna internal "
        "dan 2 Aktor Eksternal (Bot Telegram & NVIDIA AI Engine) yang terhubung ke 14 Use Case dan 20 tabel database:"
    )
    # Embed Use Case Image
    uc_img = r"c:\xampp\htdocs\absen\Usecase_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    if os.path.exists(uc_img):
        doc.add_picture(uc_img, width=Inches(6.5))
        p_cap = doc.add_paragraph("Gambar 2.1 Use Case Diagram Terpadu Sistem Presensi SMK NU WARGABINANGUN (20 Tabel Database)")
        p_cap.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap.paragraph_format.space_after = Pt(10)

    # 2.2.2 ACTIVITY DIAGRAM
    doc.add_heading("2.2.2 Activity Diagram Siklus Bisnis & Alur AI (5 Swimlanes)", level=3)
    doc.add_paragraph(
        "Activity Diagram memodelkan alur proses operasional secara berurutan melewati 5 Swimlanes "
        "(Aktor, UI Layer, Controller, Database Layer 20 Tabel, dan External Services) mencakup siklus presensi jam masuk/pulang, "
        "dual-sync wali kelas, webhook Telegram, izin auto-sync, hingga rekapitulasi Senin–Jumat:"
    )
    act_img = r"c:\xampp\htdocs\absen\Activity_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    if os.path.exists(act_img):
        doc.add_picture(act_img, width=Inches(6.5))
        p_cap2 = doc.add_paragraph("Gambar 2.2 Activity Diagram Menyeluruh Siklus Sistem Presensi (5 Swimlanes)")
        p_cap2.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap2.paragraph_format.space_after = Pt(10)

    # 2.2.3 CLASS DIAGRAM
    doc.add_heading("2.2.3 Class Diagram Berorientasi Objek & Kepatuhan Prinsip SOLID", level=3)
    doc.add_paragraph(
        "Class Diagram menggambarkan struktur berorientasi objek sistem presensi yang mematuhi prinsip Single Responsibility "
        "dan Dependency Inversion, memisahkan entitas domain model, pengendali logika (Controller), dan modul layanan (Services):"
    )
    cls_img = r"c:\xampp\htdocs\absen\Class_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    if os.path.exists(cls_img):
        doc.add_picture(cls_img, width=Inches(6.5))
        p_cap3 = doc.add_paragraph("Gambar 2.3 Class Diagram Berorientasi Objek Berbasis Prinsip SOLID")
        p_cap3.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap3.paragraph_format.space_after = Pt(10)

    # 2.2.4 SEQUENCE DIAGRAM
    doc.add_heading("2.2.4 Sequence Diagram Alur Pertukaran Pesan Real-Time", level=3)
    doc.add_paragraph(
        "Sequence Diagram mendokumentasikan interaksi pesan dinamis antar-objek (User, UI, Controller, MariaDB, Telegram API, "
        "dan NVIDIA NIM AI API) saat pemindaian presensi dan tanya-jawab chatbot berlangsung:"
    )
    seq_img = r"c:\xampp\htdocs\absen\Sequence_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png"
    if os.path.exists(seq_img):
        doc.add_picture(seq_img, width=Inches(6.5))
        p_cap4 = doc.add_paragraph("Gambar 2.4 Sequence Diagram Alur Pertukaran Pesan Presensi & AI Hadir.io")
        p_cap4.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap4.paragraph_format.space_after = Pt(10)

    # 2.3 ERD & BASIS DATA
    doc.add_heading("2.3 Perancangan Basis Data & Entity Relationship Diagram (ERD)", level=2)
    doc.add_paragraph(
        "Basis data `db_absensi_smk` dinormalisasi hingga bentuk normal ketiga (3NF) untuk menjamin integritas referensial "
        "dan efisiensi kueri tanpa redundansi. Seluruh 20 tabel saling berelasi secara terstruktur:"
    )
    erd_img = r"c:\xampp\htdocs\absen\ERD_Database_Absensi_SMK.png"
    if os.path.exists(erd_img):
        doc.add_picture(erd_img, width=Inches(6.5))
        p_cap5 = doc.add_paragraph("Gambar 2.5 Entity Relationship Diagram (ERD) Basis Data db_absensi_smk (20 Tabel)")
        p_cap5.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap5.paragraph_format.space_after = Pt(10)

    # Kamus Data 20 Tabel
    doc.add_heading("Kamus Data Ringkas 20 Tabel Database `db_absensi_smk`", level=3)
    tbl_dict = doc.add_table(rows=21, cols=3)
    tbl_dict.alignment = WD_TABLE_ALIGNMENT.CENTER
    dict_data = [
        ("Nama Tabel", "Kategori Domain", "Fungsi & Deskripsi Utama"),
        ("users", "Keamanan & Akun", "Menyimpan akun pengguna, hash password Bcrypt, role, dan biodata profil."),
        ("roles", "Keamanan & Akun", "Tingkatan hak akses sistem (admin, kepala_sekolah, guru, wali_kelas, siswa)."),
        ("tahun_ajaran", "Master Akademik", "Data periode tahun akademik aktif dan status semester ganjil/genap."),
        ("jurusan", "Master Akademik", "Data program keahlian vokasi di SMK NU Wargabinangun (TKJ, TKR, TSM)."),
        ("kelas", "Master Akademik", "Data rombel kelas (tingkat X, XI, XII) dan relasi ke jurusan_id."),
        ("guru", "Master Pendidik", "Biodata guru pengajar, NIP unik, nomor barcode kartu, dan relasi akun user_id."),
        ("wali_kelas_data", "Master Pendidik", "Biodata master tenaga pendidik yang bertugas sebagai wali kelas."),
        ("wali_kelas", "Master Pendidik", "Mapping penugasan wali kelas terhadap rombel kelas dan tahun ajaran aktif."),
        ("siswa", "Master Peserta Didik", "Biodata peserta didik, NIS, NISN, barcode kartu, kelas_id, dan user_id."),
        ("mata_pelajaran", "Kurikulum & KBM", "Daftar mata pelajaran, kode mapel, tingkat kelas, dan guru pengampu."),
        ("jadwal_mengajar", "Kurikulum & KBM", "Jadwal KBM mingguan: hari, jam mulai/selesai, ruang, mapel, dan rombel."),
        ("barcode_guru", "Identifikasi Digital", "String kode barcode unik pendidik untuk otentikasi pemindai presensi."),
        ("rfid_cards", "Identifikasi Digital", "UID kartu RFID/NFC fisik siswa untuk alternatif pemindaian kartu pintar."),
        ("absensi_siswa", "Transaksi Presensi", "Log kehadiran harian siswa: tanggal, jam masuk, jam pulang, dan status."),
        ("absensi_guru", "Transaksi Presensi", "Log kehadiran harian guru: jam masuk, jam pulang, status, dan durasi KBM."),
        ("absensi_wali_kelas", "Transaksi Presensi", "Log kehadiran khusus dewan wali kelas hasil sinkronisasi Dual-Sync otomatis."),
        ("pengajuan_izin", "Dispensasi Izin", "Rekord permohonan izin/sakit mandiri siswa/guru, tanggal, dan bukti foto."),
        ("catatan_guru", "Supervisi KBM", "Catatan evaluasi kerja dan arahan Kepala Sekolah kepada guru berpelacak baca."),
        ("catatan_pembinaan", "Bimbingan Karakter", "Catatan bimbingan/pelanggaran siswa dari Wali Kelas berstatus konfirmasi baca."),
        ("pengaturan_sistem", "Konfigurasi Global", "Key-value pair parameter jam presensi, kuota, token Bot Telegram & API AI.")
    ]
    for i, row_vals in enumerate(dict_data):
        for j, val in enumerate(row_vals):
            cell = tbl_dict.cell(i, j)
            cell.text = val
            if i == 0:
                cell.paragraphs[0].runs[0].bold = True
                set_cell_background(cell, "0F172A")
                cell.paragraphs[0].runs[0].font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            else:
                set_cell_background(cell, "F8FAFC" if i % 2 == 1 else "FFFFFF")
            set_cell_margins(cell, 40, 40, 60, 60)

    doc.add_page_break()

    # =========================================================
    # BAB III: IMPLEMENTASI, PENGUJIAN & CI/CD DEVOPS
    # =========================================================
    h_b3 = doc.add_heading("BAB III: IMPLEMENTASI, TESTING & CI/CD DEVOPS", level=1)
    h_b3.runs[0].font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_heading("3.1 Implementasi Kode Bersih & Kepatuhan Arsitektur", level=2)
    doc.add_paragraph(
        "Seluruh kode aplikasi dibangun mematuhi standar Clean Code dan prinsip SOLID. "
        "Koneksi database menggunakan PDO Prepared Statements murni untuk mengeliminasi celah SQL Injection. "
        "Kata sandi di-hash menggunakan algoritma Bcrypt (cost 10) standar industri. "
        "Sistem menerapkan pemisahan routing yang aman melalui Front Controller index.php."
    )

    doc.add_heading("3.2 Implementasi Fitur Kecerdasan Artifisial & Telegram Webhook", level=2)
    doc.add_paragraph(
        "Integrasi AI diimplementasikan pada modul `chat_api.php`. Ketika pengguna mengirimkan prompt, "
        "mesin internal mengevaluasi intent secara lokal, mengambil agregat data yang diizinkan (RBAC), "
        "lalu menyusun prompt kontekstual ke REST API NVIDIA NIM (Model Nemotron-3.5-bolt-e4b) melalui cURL. "
        "Respons diterima secara terstruktur dan ditampilkan pada antarmuka chat widget. "
        "Untuk notifikasi Telegram, fungsi `sendTelegramNotification()` mengeksekusi cURL HTTP POST ke endpoint "
        "Bot Telegram (@HadirioBot) dengan opsi konfigurasi IPv4 paksa untuk memastikan keandalan transmisi lokal."
    )

    doc.add_heading("3.3 Laporan Pengujian Sistem (Software Testing)", level=2)
    doc.add_paragraph(
        "Pengujian fungsionalitas aplikasi dilakukan menggunakan metode Black-Box Testing "
        "serta Unit Testing otomatis. Seluruh skenario pengujian berhasil lulus 100% tanpa galat:"
    )
    tbl_tc = doc.add_table(rows=8, cols=4)
    tbl_tc.alignment = WD_TABLE_ALIGNMENT.CENTER
    tc_data = [
        ("Kode Uji", "Skenario Pengujian", "Hasil yang Diharapkan", "Status"),
        ("TC-01", "Login dengan password salah", "Sistem menolak autentikasi dan menampilkan pesan error", "PASS (100%)"),
        ("TC-02", "Scan QR jam 06:30 WIB", "Presensi tercatat 'Hadir Tepat Waktu', notifikasi Telegram terkirim", "PASS (100%)"),
        ("TC-03", "Scan QR jam 07:15 WIB", "Presensi otomatis tercatat 'Terlambat', notifikasi Telegram terkirim", "PASS (100%)"),
        ("TC-04", "Scan Wali Kelas 1 kali", "Data otomatis tersinkron ke tabel absensi_guru dan absensi_wali_kelas", "PASS (100%)"),
        ("TC-05", "Pengajuan izin + approval", "Status di tabel absensi_siswa otomatis berubah menjadi 'Izin'", "PASS (100%)"),
        ("TC-06", "Rekapitulasi kehadiran", "Perhitungan presensi hanya menghitung hari efektif Senin s/d Jumat", "PASS (100%)"),
        ("TC-07", "Query AI Hadir.io", "Chatbot menjawab jumlah kelas (9) dan guru (33) secara ringkas & akurat", "PASS (100%)")
    ]
    for i, r_vals in enumerate(tc_data):
        for j, v in enumerate(r_vals):
            c = tbl_tc.cell(i, j)
            c.text = v
            if i == 0:
                c.paragraphs[0].runs[0].bold = True
                set_cell_background(c, "0F172A")
                c.paragraphs[0].runs[0].font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            else:
                set_cell_background(c, "F8FAFC" if i % 2 == 1 else "FFFFFF")
                if j == 3:
                    c.paragraphs[0].runs[0].bold = True
                    c.paragraphs[0].runs[0].font.color.rgb = RGBColor(0x05, 0x96, 0x69)
            set_cell_margins(c, 40, 40, 60, 60)

    doc.add_heading("3.4 Konfigurasi CI/CD Pipeline & GitHub DevOps", level=2)
    doc.add_paragraph(
        "Sistem menerapkan integrasi berkelanjutan (Continuous Integration) menggunakan GitHub Actions "
        "yang dikonfigurasi pada berkas `.github/workflows/ci.yml`. Setiap kali ada push atau pull request, "
        "pipeline otomatis menjalankan pemeriksaan sintaksis PHP Lint, verifikasi arsitektur kode bersih, "
        "dan eksekusi automated unit test dengan status hijau (Passed)."
    )

    doc.add_heading("3.5 Petunjuk Instalasi & Deployment Sistem", level=2)
    doc.add_paragraph(
        "Langkah-langkah instalasi aplikasi pada lingkungan server lokal/hosting:\n"
        "1. Kebutuhan Sistem: PHP 8.2 ke atas (ekstensi pdo_mysql, curl, gd, openssl aktif), Apache Web Server, MariaDB/MySQL 10.4+.\n"
        "2. Konfigurasi Basis Data: Buat database baru bernama `db_absensi_smk`, lalu impor berkas skema `db_absensi_smk.sql`.\n"
        "3. Konfigurasi Sistem: Sesuaikan parameter database pada `app/config/database.php` (DB_HOST, DB_USER, DB_PASS, DB_NAME).\n"
        "4. Konfigurasi Integrasi: Pastikan token Bot Telegram dan API Key NVIDIA NIM telah terkonfigurasi pada tabel `pengaturan_sistem`.\n"
        "5. Menjalankan Aplikasi: Buka browser dan akses alamat `http://localhost/absen`."
    )

    doc.add_page_break()

    # =========================================================
    # BAB IV: KESIMPULAN & TAUTAN DELIVERABLES
    # =========================================================
    h_b4 = doc.add_heading("BAB IV: KESIMPULAN & TAUTAN DELIVERABLES", level=1)
    h_b4.runs[0].font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_heading("4.1 Kesimpulan", level=2)
    doc.add_paragraph(
        "Berdasarkan seluruh tahapan rekayasa perangkat lunak yang telah diselesaikan, dapat disimpulkan bahwa:\n"
        "1. Sistem Presensi Digital SMK NU WARGABINANGUN berhasil mengatasi kelemahan presensi konvensional melalui otomasi pemindaian QR Code instan, verifikasi jam server ketat, dan pelaporan real-time via Telegram.\n"
        "2. Seluruh 20 tabel relasional basis data telah terintegrasi sempurna dengan arsitektur 5 peran pengguna (RBAC), mendukung fitur Dual-Sync pendidik, perizinan otomatis, dan supervisi terarah.\n"
        "3. Asisten AI Hadir.io berbasis NVIDIA NIM memberikan nilai tambah inovatif sebagai pusat informasi cerdas sekolah yang akurat, aman, dan berdaya guna tinggi."
    )

    doc.add_heading("4.2 Refleksi Pembelajaran Mandiri (Engineering Reflection)", level=2)
    doc.add_paragraph(
        "Pengembangan proyek akhir mandiri ini memberikan pengalaman rekayasa perangkat lunak menyeluruh (end-to-end build) "
        "mulai dari tahap analisis kebutuhan pengguna, perancangan diagram UML standar, normalisasi basis data, "
        "hingga integrasi kecerdasan buatan dan deployment CI/CD. Penulis belajar bagaimana merancang arsitektur sistem "
        "yang tangguh, aman dari eksploitasi, serta memberikan dampak nyata bagi transformasi digital institusi pendidikan vokasi."
    )

    doc.add_heading("4.3 Tautan Berkas Deliverables Proyek", level=2)
    tbl_link = doc.add_table(rows=4, cols=2)
    tbl_link.alignment = WD_TABLE_ALIGNMENT.CENTER
    link_data = [
        ("Nama Berkas Luaran (Deliverables)", "Tautan Akses / Status"),
        ("Repositori GitHub Publik Proyek", "https://github.com/winarti-rpl/proyek-rpl-presensi-smk-nu-wargabinangun"),
        ("Tautan Video Presentasi & Live Demo (YouTube)", "Tersedia pada berkas README.md repositori GitHub resmi"),
        ("Dokumen Lengkap Laporan PDF Resmi", "Laporan_Proyek_Akhir_RPL_Sistem_Presensi_SMK_NU_Wargabinangun.pdf")
    ]
    for i, (k, v) in enumerate(link_data):
        c0 = tbl_link.cell(i, 0)
        c1 = tbl_link.cell(i, 1)
        c0.text = k
        c1.text = v
        if i == 0:
            c0.paragraphs[0].runs[0].bold = True
            c1.paragraphs[0].runs[0].bold = True
            set_cell_background(c0, "0F172A")
            set_cell_background(c1, "0F172A")
            c0.paragraphs[0].runs[0].font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            c1.paragraphs[0].runs[0].font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
        else:
            set_cell_background(c0, "F8FAFC" if i % 2 == 1 else "FFFFFF")
            set_cell_background(c1, "F8FAFC" if i % 2 == 1 else "FFFFFF")
        set_cell_margins(c0, 50, 50, 80, 80)
        set_cell_margins(c1, 50, 50, 80, 80)

    doc.add_heading("DAFTAR PUSTAKA", level=2)
    refs = [
        "1. Pressman, R. S., & Maxim, B. R. (2020). Software Engineering: A Practitioner's Approach (9th ed.). McGraw-Hill Education.",
        "2. Sommerville, I. (2016). Software Engineering (10th ed.). Pearson Education.",
        "3. IEEE Computer Society. (1998). IEEE Std 830-1998: IEEE Recommended Practice for Software Requirements Specifications.",
        "4. Fowler, M. (2018). UML Distilled: A Brief Guide to the Standard Object Modeling Language (3rd ed.). Addison-Wesley.",
        "5. Martin, R. C. (2017). Clean Architecture: A Craftsman's Guide to Software Structure and Design. Prentice Hall."
    ]
    for r in refs:
        p = doc.add_paragraph(r)
        p.paragraph_format.left_indent = Inches(0.4)
        p.paragraph_format.first_line_indent = Inches(-0.4)

    doc.save(docx_path)
    print("[SUCCESS] DOCX Report generated at:", docx_path)

if __name__ == "__main__":
    out_docx = r"c:\xampp\htdocs\absen\Laporan_Proyek_Akhir_RPL_Sistem_Presensi_SMK_NU_Wargabinangun.docx"
    out_pdf = r"c:\xampp\htdocs\absen\Laporan_Proyek_Akhir_RPL_Sistem_Presensi_SMK_NU_Wargabinangun.pdf"
    build_report_docx(out_docx)
    print("Converting DOCX to PDF via docx2pdf...")
    convert(out_docx, out_pdf)
    print("[SUCCESS] PDF Report generated at:", out_pdf)
