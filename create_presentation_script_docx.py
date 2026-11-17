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

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('w:top', top), ('w:bottom', bottom), ('w:left', left), ('w:right', right)]:
        node = OxmlElement(m)
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def add_slide_box(doc, slide_num, slide_title, speech_text, action_tip=""):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = tbl.cell(0, 0)
    set_cell_background(cell, "F0F9FF")
    set_cell_margins(cell, top=130, bottom=130, left=170, right=170)

    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>'
        f'<w:top w:val="none"/>'
        f'<w:left w:val="single" w:sz="36" w:space="0" w:color="0284C7"/>'
        f'<w:bottom w:val="none"/>'
        f'<w:right w:val="none"/>'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)

    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)

    r_hdr = p.add_run(f"SLIDE {slide_num}: {slide_title.upper()}\n")
    r_hdr.bold = True
    r_hdr.font.size = Pt(11)
    r_hdr.font.color.rgb = RGBColor(0x03, 0x69, 0xA1)

    if action_tip:
        r_tip = p.add_run(f"[Petunjuk Demonstrasi: {action_tip}]\n\n")
        r_tip.italic = True
        r_tip.font.size = Pt(9.5)
        r_tip.font.color.rgb = RGBColor(0x64, 0x74, 0x8B)

    r_speech = p.add_run(speech_text)
    r_speech.font.size = Pt(10.5)
    r_speech.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_paragraph().paragraph_format.space_after = Pt(6)

def generate_docx(output_path):
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

    # Title
    p_title = doc.add_paragraph()
    p_title.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_after = Pt(2)
    r1 = p_title.add_run("NASKAH DEMONSTRASI PRESENTASI (.PPTX)\nSISTEM PRESENSI DIGITAL SMK NU WARGABINANGUN")
    r1.bold = True
    r1.font.size = Pt(16)
    r1.font.color.rgb = RGBColor(0x1E, 0x3A, 0x8A)

    p_sub = doc.add_paragraph()
    p_sub.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_after = Pt(14)
    r2 = p_sub.add_run("Panduan Berbicara Per-Slide: Singkat, Padat, dan Langsung ke Intinya\nPresenter: Winarti (41257168) • TI-1C-2025")
    r2.font.size = Pt(11)
    r2.font.color.rgb = RGBColor(0x02, 0x84, 0xC7)
    r2.bold = True

    slides_content = [
        (1, "Judul & Pengenalan Sistem",
         "Assalamu’alaikum Warahmatullahi Wabarakatuh, dan selamat pagi/siang kepada Bapak/Ibu Dewan Penguji serta rekan-rekan sekalian.\n\n"
         "Nama saya Winarti. Pada kesempatan hari ini, saya akan mempresentasikan sekaligus mendemonstrasikan hasil rancang bangun Sistem Presensi Digital SMK NU WARGABINANGUN.\n\n"
         "Sistem ini adalah platform kehadiran modern berbasis pemindaian QR Code cepat, terhubung langsung dengan notifikasi real-time Telegram, modul supervisi akademik, serta asisten cerdas berbasis kecerdasan buatan, Hadir.io.",
         "Tampilkan slide pembuka dengan percaya diri dan kontak mata ke penguji."),

        (2, "Latar Belakang & Masalah",
         "Sistem ini dibangun untuk menyelesaikan tiga permasalahan utama di sekolah:\n\n"
         "Pertama, presensi manual menggunakan kertas sangat tidak efisien, rentan hilang, menimbulkan antrean panjang, dan rawan titip absen.\n\n"
         "Kedua, ketiadaan informasi real-time membuat orang tua dan pihak sekolah terlambat mengetahui siswa yang tidak hadir atau terlambat.\n\n"
         "Dan ketiga, alur izin sakit masih manual tanpa arsip bukti yang rapi, serta belum adanya catatan supervisi guru dan pembinaan karakter siswa yang terdokumentasi secara digital.",
         "Beri penekanan pada kata 'tidak efisien', 'ketiadaan informasi', dan 'rawan titip absen'."),

        (3, "Visi & Tujuan Strategis",
         "Visi utama produk ini adalah mewujudkan tata kelola presensi yang modern, akuntabel, transparan, dan bebas penggunaan kertas di SMK NU WARGABINANGUN.\n\n"
         "Untuk mencapai visi tersebut, kami menetapkan lima pilar fungsional:\n"
         "1. Pemindaian QR Code instan tanpa sentuhan fisik kurang dari satu detik.\n"
         "2. Pemisahan hak akses yang aman untuk lima tingkatan peran pengguna.\n"
         "3. Pengajuan izin dan sakit mandiri disertai unggah foto bukti.\n"
         "4. Layanan supervisi KBM dan pembinaan kedisiplinan terpadu.\n"
         "5. Serta integrasi asisten cerdas AI Hadir.io untuk layanan informasi cepat.",
         "Arahkan tangan ke 5 poin pilar pada slide."),

        (4, "Target Pengguna (RBAC 5 Tingkat)",
         "Sistem ini menerapkan Role-Based Access Control secara ketat untuk lima pengguna:\n\n"
         "• Administrator memegang kendali penuh atas master data sekolah, persetujuan izin, dan cetak kartu barcode.\n"
         "• Kepala Sekolah bertindak sebagai supervisor untuk memantau kehadiran harian dan memberi evaluasi KBM kepada guru.\n"
         "• Wali Kelas memiliki akses khusus untuk memantau presensi dan membina siswa di kelasnya saja.\n"
         "• Guru Pengajar mencatat presensi harian, melihat jadwal mengajar, dan membaca catatan supervisi.\n"
         "• Dan Siswa dapat melakukan presensi mandiri, melihat riwayat absensi, serta mengajukan izin sakit.",
         "Jelaskan bahwa setiap peran hanya bisa melihat data miliknya sendiri demi privasi."),

        (5, "Presensi & Otomasi (QR Code & Telegram)",
         "Inilah fitur inti operasional harian sekolah. Alurnya sangat praktis:\n\n"
         "Siswa atau guru cukup mengarahkan kartu QR ke kamera pemindai. Sistem akan memvalidasi aturan jam dan mencatat kehadiran kurang dari setengah detik tanpa perlu memuat ulang peramban.\n\n"
         "Scan pagi otomatis dicatat sebagai jam masuk, dan scan sore mulai jam 3 dicatat sebagai jam pulang.\n\n"
         "Istimewanya, setiap kali scan berhasil, sistem langsung mengirimkan notifikasi otomatis ke Bot Telegram secara real-time, lengkap dengan nama, kelas, jam, dan status kehadiran tepat waktu atau terlambat.",
         "Tunjukkan panel kamera dan contoh notifikasi Telegram yang masuk."),

        (6, "Perizinan & Rekapitulasi Otomatis",
         "Untuk perizinan, sistem menghilangkan penggunaan surat kertas. Siswa dan guru dapat mengajukan izin langsung dari akun masing-masing dengan mengunggah foto surat dokter.\n\n"
         "Admin cukup meninjau bukti berkas dan menentukan persetujuan. Ketika disetujui, sistem secara otomatis memperbarui status absensi menjadi 'Izin' atau 'Sakit' tanpa perlu input manual kedua kali.\n\n"
         "Laporan kehadiran juga dapat direkapitulasi secara otomatis dengan filter kelas maupun tanggal, serta langsung dapat diunduh dalam format Excel atau dicetak menjadi PDF resmi sekolah.",
         "Jelaskan keunggulan auto-sync izin yang mencegah kerja ganda."),

        (7, "Supervisi KBM & Pembinaan Karakter",
         "Sistem ini bukan sekadar alat pencatat absen, melainkan juga wadah komunikasi edukatif sekolah:\n\n"
         "Pertama, Supervisi Guru. Kepala Sekolah dapat menuliskan catatan evaluasi dan motivasi kerja kepada guru yang bersifat privat, lengkap dengan pelacakan kapan guru tersebut telah membacanya.\n\n"
         "Kedua, Pembinaan Siswa. Wali Kelas dapat mendokumentasikan catatan bimbingan atau pelanggaran kedisiplinan siswa. Siswa dapat melihat catatan tersebut dan menekan tombol 'Sudah Dibaca' sebagai bentuk komitmen kedisiplinan diri.",
         "Tekankan nilai edukatif dari sistem yang mendukung pembentukan karakter."),

        (8, "Asisten Cerdas Sekolah (Hadir.io)",
         "Kami juga melengkapi sistem ini dengan Asisten AI bernama Hadir.io yang ditenagai oleh model kecerdasan buatan NVIDIA NIM.\n\n"
         "Hadir.io bekerja dengan arsitektur Dual-Engine: Data angka dan jadwal diambil secara akurat langsung dari database sekolah, lalu disajikan oleh AI secara ringkas, to-the-point, dan santun.\n\n"
         "Chatbot ini mendukung empat bahasa—yaitu Indonesia, Inggris, Sunda, dan Jawa—serta dijaga oleh firewall privasi ketat agar siswa tidak bisa mengintip data guru atau kelas lain.",
         "Sebutkan fitur 4 bahasa dan ketepatan jawaban yang ringkas."),

        (9, "Aturan Operasional Jam Presensi",
         "Berikut aturan kerja yang tertanam di dalam mesin sistem:\n\n"
         "1. Jam Masuk dibuka mulai jam 05.00 WIB. Pukul 05.00 hingga 07.00 WIB dicatat Hadir Tepat Waktu, dan lewat jam 07.00 WIB otomatis dicatat Terlambat.\n"
         "2. Jam Pulang baru dapat diakses mulai pukul 15.00 WIB.\n"
         "3. Khusus Wali Kelas, kami menerapkan fitur 'Dual-Sync', di mana satu kali scan otomatis mencatat absensi guru sekaligus wali kelas secara simultan.\n"
         "4. Rekapitulasi kehadiran hanya menghitung hari efektif KBM, yaitu Senin sampai Jumat. Hari Sabtu tetap bisa melakukan scan untuk keperluan uji coba dan notifikasi, namun dikecualikan dari hitungan rekap resmi.",
         "Jelaskan bahwa aturan Senin-Jumat menjaga keadilan persentase kehadiran."),

        (10, "Spesifikasi Sistem & Keamanan",
         "Dari sisi arsitektur teknis, sistem ini dibangun menggunakan teknologi yang tangguh dan teruji:\n\n"
         "Backend menggunakan PHP 8.2 Native berkonsep MVC dan MariaDB dengan prepared statements untuk mencegah celah SQL Injection. Kata sandi diamankan dengan enkripsi Bcrypt.\n\n"
         "Frontend dirancang responsif menggunakan Bootstrap 5 dan engine HTML5-QRCode yang kompatibel di semua perangkat laptop maupun ponsel.\n\n"
         "Semua transaksi memiliki waktu respons sangat cepat di bawah satu detik dan terhubung aman ke Telegram Bot API serta NVIDIA NIM API.",
         "Tunjukkan kesiapan sistem dari segi performa dan keamanan data."),

        (11, "Progres Proyek (100% Selesai)",
         "Dapat kami laporkan bahwa pengembangan sistem ini telah rampung seratus persen melalui empat tahapan rilis:\n\n"
         "Fase 1: Perancangan database relasional dan otentikasi multi-peran.\n"
         "Fase 2: Pembangunan modul pemindai QR kamera dan integrasi Bot Telegram.\n"
         "Fase 3: Digitalisasi perizinan, kanal supervisi guru, dan ekspor laporan rekapitulasi.\n"
         "Serta Fase 4: Integrasi kecerdasan buatan Hadir.io, pengamanan firewall data, dan pengujian siklus presensi secara menyeluruh.",
         "Sampaikan dengan bangga bahwa seluruh fitur telah tuntas 100% dan teruji."),

        (12, "Penutup & Kesiapan Demonstrasi",
         "Sebagai kesimpulan, Sistem Presensi Digital SMK NU WARGABINANGUN telah siap dioperasikan sebagai solusi modern yang efisien, transparan, dan cerdas bagi kemajuan pendidikan vokasi.\n\n"
         "Sekian presentasi dari saya. Selanjutnya, saya sangat siap untuk mendemonstrasikan sistem ini secara langsung dan menjawab pertanyaan dari Bapak/Ibu Dewan Penguji.\n\n"
         "Terima kasih atas perhatiannya. Wabillahi taufiq wal hidayah, Wassalamu’alaikum Warahmatullahi Wabarakatuh.",
         "Tersenyum ramah, bungkukkan badan sedikit tanda hormat, lalu buka laptop/halaman browser untuk demo.")
    ]

    for s_num, s_title, s_speech, s_tip in slides_content:
        add_slide_box(doc, s_num, s_title, s_speech, s_tip)

    doc.save(output_path)
    print(f"[SUCCESS] File saved to: {output_path}")

if __name__ == "__main__":
    out_docx = r"c:\xampp\htdocs\absen\Naskah_Demonstrasi_PPTX_Sistem_Presensi.docx"
    generate_docx(out_docx)
