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

def set_cell_margins(cell, top=110, bottom=110, left=150, right=150):
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
        r_tip = p.add_run(f"[Panduan Demo: {action_tip}]\n\n")
        r_tip.italic = True
        r_tip.font.size = Pt(9.5)
        r_tip.font.color.rgb = RGBColor(0x64, 0x74, 0x8B)

    r_speech = p.add_run(speech_text)
    r_speech.font.size = Pt(10.5)
    r_speech.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_paragraph().paragraph_format.space_after = Pt(6)

def generate_balanced_docx(output_path):
    doc = docx.Document()

    for s in doc.sections:
        s.top_margin = Inches(1.0)
        s.bottom_margin = Inches(1.0)
        s.left_margin = Inches(1.0)
        s.right_margin = Inches(1.0)

    style_normal = doc.styles['Normal']
    style_normal.font.name = 'Calibri'
    style_normal.font.size = Pt(11)
    style_normal.font.color.rgb = RGBColor(0x1E, 0x29, 0x3B)
    style_normal.paragraph_format.line_spacing = 1.25

    # Title
    p_title = doc.add_paragraph()
    p_title.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_after = Pt(2)
    r1 = p_title.add_run("NASKAH PRESENTASI & DEMONSTRASI SISTEM (.PPTX)\nSISTEM PRESENSI DIGITAL SMK NU WARGABINANGUN")
    r1.bold = True
    r1.font.size = Pt(16)
    r1.font.color.rgb = RGBColor(0x1E, 0x3A, 0x8A)

    p_sub = doc.add_paragraph()
    p_sub.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_after = Pt(14)
    r2 = p_sub.add_run("Panduan Berbicara Terstruktur: Ringkas, Padat, dan Langsung ke Inti Materi\nPresenter: Winarti (41257168) • TI-1C-2025 • SMK NU WARGABINANGUN")
    r2.font.size = Pt(11)
    r2.font.color.rgb = RGBColor(0x02, 0x84, 0xC7)
    r2.bold = True

    slides_content = [
        (1, "Judul & Pengenalan Sistem",
         "Assalamu’alaikum Warahmatullahi Wabarakatuh, dan selamat pagi/siang kepada Bapak/Ibu Dewan Penguji yang saya hormati. "
         "Nama saya Winarti, NIM 41257168 dari kelas TI-1C-2025. Hari ini saya akan mempresentasikan dan mendemonstrasikan hasil rancang bangun "
         "'Sistem Presensi Digital SMK NU WARGABINANGUN'. Sistem ini adalah platform kehadiran modern berbasis pemindaian QR Code cepat, "
         "terintegrasi dengan notifikasi real-time Telegram, modul supervisi akademik, serta asisten cerdas AI Hadir.io, yang dirancang khusus "
         "untuk mewujudkan tata kelola kedisiplinan dan transparansi presensi di sekolah vokasi.",
         "Tatap dewan penguji dengan ramah, buka presentasi dengan percaya diri."),

        (2, "Latar Belakang & Masalah",
         "Sistem ini dibangun untuk menyelesaikan tiga masalah utama pada presensi konvensional di sekolah: "
         "Pertama, pencatatan manual berbasis kertas sangat tidak efisien, rentan rusak atau hilang, memicu antrean panjang di gerbang, dan rawan titip absen. "
         "Kedua, ketiadaan informasi real-time membuat orang tua dan pihak sekolah terlambat mengetahui kehadiran siswa, serta Kepala Sekolah kesulitan memantau kedisiplinan guru. "
         "Ketiga, alur izin sakit masih manual tanpa arsip bukti yang rapi, serta belum adanya wadah digital resmi untuk supervisi KBM guru dan pembinaan karakter siswa.",
         "Tekankan kerugian dari presensi kertas dan pentingnya data real-time."),

        (3, "Visi & Tujuan Strategis",
         "Visi produk ini adalah mewujudkan tata kelola presensi yang modern, akuntabel, transparan, dan zero paper waste di SMK NU WARGABINANGUN. "
         "Untuk mencapai target 100% digitalisasi dan bebas manipulasi, kami menetapkan lima pilar fungsional utama: "
         "Otomasi presensi cepat di bawah satu detik tanpa sentuhan; pemisahan hak akses aman untuk 5 tingkatan pengguna (RBAC); "
         "pengajuan izin dan sakit mandiri disertai unggah foto bukti; layanan supervisi KBM dan pembinaan kedisiplinan terpadu; "
         "serta integrasi layanan informasi cerdas melalui Asisten AI Hadir.io yang mendukung empat bahasa.",
         "Arahkan tangan ke poin visi dan 5 pilar implementasi."),

        (4, "Target Pengguna (RBAC 5 Tingkat)",
         "Sistem menerapkan Role-Based Access Control secara ketat pada lima tingkatan pengguna agar privasi dan integritas data terjamin: "
         "Administrator memegang kendali penuh atas master data, persetujuan izin, dan cetak kartu barcode. "
         "Kepala Sekolah bertindak sebagai supervisor untuk memantau grafik kehadiran seluruh sekolah dan memberi evaluasi KBM guru. "
         "Wali Kelas memantau dan membina siswa khusus di kelas binaannya saja. "
         "Guru Pengajar melakukan presensi mandiri, melihat jadwal KBM, dan membaca catatan supervisi. "
         "Serta Siswa dapat memindai presensi QR pribadi, memantau riwayat kehadirannya, dan mengajukan izin secara mandiri.",
         "Jelaskan bahwa setiap peran terisolasi sesuai wewenangnya."),

        (5, "Presensi & Otomasi (QR Code & Telegram)",
         "Inilah fitur inti harian sekolah. Siswa atau guru cukup mengarahkan kartu QR ke kamera pemindai; sistem langsung memvalidasi kode dan mencatat kehadiran dalam waktu kurang dari setengah detik tanpa perlu me-refresh browser. "
         "Sistem menerapkan logika dual-mode cerdas: scan pagi otomatis dicatat sebagai jam masuk, sedangkan scan sore mulai pukul 15.00 WIB dicatat sebagai jam pulang. "
         "Istimewanya, setiap kali scan berhasil, sistem seketika memicu Bot Telegram (@HadirioBot) untuk mengirimkan notifikasi resmi secara real-time ke ponsel orang tua, lengkap dengan nama, rombel, jam, dan status kehadiran tepat waktu atau terlambat.",
         "Tunjukkan alur scanner kamera dan contoh notifikasi Telegram."),

        (6, "Perizinan & Rekapitulasi Otomatis",
         "Modul perizinan berjalan sepenuhnya secara digital dan paperless. Siswa atau guru yang berhalangan hadir dapat mengajukan izin langsung dari akunnya dengan wajib melampirkan foto surat dokter atau bukti pendukung. "
         "Admin cukup meninjau bukti berkas tersebut dan menentukan persetujuan. Begitu disetujui, sistem secara otomatis memperbarui status absensi menjadi Izin atau Sakit tanpa perlu input manual kedua kali. "
         "Laporan kehadiran pun dapat direkapitulasi secara otomatis berdasarkan tanggal maupun rombel kelas, serta langsung bisa diekspor ke file Excel (.xlsx) rapi atau dicetak ke format dokumen PDF resmi berkop surat sekolah.",
         "Soroti fitur Auto-Sync izin yang mencegah kerja ganda."),

        (7, "Supervisi KBM & Pembinaan Karakter",
         "Aplikasi ini tidak sekadar mencatat kehadiran, tetapi juga menjadi sarana komunikasi edukatif sekolah: "
         "Pertama, Supervisi Guru; Kepala Sekolah dapat memberikan catatan evaluasi atau motivasi KBM secara tertulis kepada guru yang bersifat rahasia, lengkap dengan pelacakan otomatis kapan catatan tersebut telah dibaca oleh guru. "
         "Kedua, Pembinaan Siswa; Wali Kelas dapat mendokumentasikan pelanggaran tata tertib maupun prestasi siswa. Siswa dapat melihat catatan tersebut di akunnya dan menekan tombol 'Tandai Sudah Dibaca' sebagai bentuk komitmen kedisiplinan dan rekam jejak bimbingan konseling.",
         "Tekankan nilai edukatif aplikasi dalam membangun karakter siswa."),

        (8, "Asisten Cerdas Sekolah (Hadir.io)",
         "Kami melengkapi sistem ini dengan fitur kecerdasan buatan, yaitu Asisten AI Hadir.io yang ditenagai model bahasa besar NVIDIA NIM (Nemotron). "
         "Hadir.io beroperasi dengan konsep Dual-Engine: data angka dan jadwal diambil secara 100% akurat langsung dari basis data sekolah, lalu dirangkum oleh model AI menjadi jawaban yang ringkas, to-the-point, dan santun. "
         "Chatbot ini dilindungi oleh RBAC Firewall agar siswa tidak bisa mengakses data privat guru, serta memiliki kemampuan adaptif dalam 4 bahasa sekaligus, yaitu Bahasa Indonesia, Inggris, Sunda, dan Jawa.",
         "Sebutkan fitur 4 bahasa dan ketepatan jawaban AI yang ringkas."),

        (9, "Aturan Operasional Jam Presensi",
         "Berikut aturan kerja yang tertanam di dalam sistem untuk menjaga ketertiban sekolah: "
         "Jam Masuk dibuka mulai jam 05.00 WIB; pemindaian pukul 05.00 hingga 07.00 WIB dicatat Hadir Tepat Waktu, dan di atas jam 07.00 WIB otomatis dicatat Terlambat. Jam Pulang baru dapat discan mulai pukul 15.00 WIB. "
         "Khusus Wali Kelas, kami menerapkan fitur 'Dual-Sync', di mana satu kali scan otomatis mencatat kehadiran guru sekaligus wali kelas. "
         "Selain itu, rekapitulasi kehadiran resmi hanya menghitung hari efektif Senin sampai Jumat; hari Sabtu dibuka khusus untuk uji coba presensi dan Telegram, namun dikecualikan dari hitungan rekapitulasi agar persentase kehadiran tetap adil.",
         "Jelaskan aturan jam dan pengecualian hari Sabtu secara tegas."),

        (10, "Spesifikasi Sistem & Keamanan",
         "Dari segi arsitektur teknis, sistem ini dibangun menggunakan teknologi yang tangguh, efisien, dan aman: "
         "Backend menggunakan PHP 8.2 Native berkonsep MVC dan MariaDB dengan parameterized prepared statements (PDO), sehingga 100% kebal terhadap celah SQL Injection, serta kata sandi diamankan dengan hashing Bcrypt. "
         "Frontend dirancang responsif dengan Bootstrap 5 dan engine kamera HTML5-QRCode yang berjalan mulus di laptop maupun smartphone. "
         "Semua proses pemindaian memiliki respon sangat cepat di bawah satu detik dan terhubung aman ke Telegram Bot API serta NVIDIA NIM API.",
         "Tunjukkan bahwa arsitektur sistem sangat ringan dan aman."),

        (11, "Progres Proyek (100% Selesai)",
         "Dapat kami laporkan bahwa pengembangan proyek ini telah rampung seratus persen melalui empat tahapan rilis yang terstruktur: "
         "Fase 1 perancangan basis data relasional 20 tabel dan otentikasi RBAC; "
         "Fase 2 integrasi pemindai QR kamera dan webhook Bot Telegram; "
         "Fase 3 digitalisasi perizinan online, modul supervisi guru-siswa, dan ekspor laporan; "
         "serta Fase 4 integrasi kecerdasan buatan Hadir.io, keamanan firewall data, dan validasi siklus presensi secara menyeluruh tanpa adanya kendala.",
         "Sampaikan dengan percaya diri bahwa seluruh fitur telah tuntas 100%."),

        (12, "Penutup & Kesiapan Demonstrasi",
         "Sebagai kesimpulan, Sistem Presensi Digital SMK NU WARGABINANGUN siap dioperasikan sebagai solusi modern yang efisien, transparan, dan cerdas demi memajukan mutu pendidikan vokasi. "
         "Demikian pemaparan materi dari saya. Untuk membuktikan seluruh fungsi yang telah disampaikan, sekarang saya siap mendemonstrasikan sistem ini secara langsung di layar monitor dan menjawab pertanyaan dari Bapak/Ibu Dewan Penguji. "
         "Terima kasih atas perhatiannya. Wabillahi taufiq wal hidayah, Wassalamu’alaikum Warahmatullahi Wabarakatuh.",
         "Tersenyum ramah, bungkukkan badan tanda hormat, lalu buka browser untuk live demo.")
    ]

    for s_num, s_title, s_speech, s_tip in slides_content:
        add_slide_box(doc, s_num, s_title, s_speech, s_tip)

    doc.save(output_path)
    print(f"[SUCCESS] Balanced script saved to: {output_path}")

if __name__ == "__main__":
    out_docx = r"c:\xampp\htdocs\absen\Naskah_Demonstrasi_PPTX_Sistem_Presensi.docx"
    generate_balanced_docx(out_docx)
