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

def set_cell_margins(cell, top=120, bottom=120, left=160, right=160):
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
    set_cell_margins(cell, top=140, bottom=140, left=180, right=180)

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
    p.paragraph_format.space_after = Pt(3)

    r_hdr = p.add_run(f"SLIDE {slide_num}: {slide_title.upper()}\n")
    r_hdr.bold = True
    r_hdr.font.size = Pt(11.5)
    r_hdr.font.color.rgb = RGBColor(0x03, 0x69, 0xA1)

    if action_tip:
        r_tip = p.add_run(f"[Panduan Sikap & Demonstrasi: {action_tip}]\n\n")
        r_tip.italic = True
        r_tip.font.size = Pt(9.5)
        r_tip.font.color.rgb = RGBColor(0x64, 0x74, 0x8B)

    r_speech = p.add_run(speech_text)
    r_speech.font.size = Pt(10.5)
    r_speech.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_paragraph().paragraph_format.space_after = Pt(8)

def generate_expanded_docx(output_path):
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
    p_title.paragraph_format.space_after = Pt(3)
    r1 = p_title.add_run("NASKAH LENGKAP PRESENTASI & DEMONSTRASI (.PPTX)\nSISTEM PRESENSI DIGITAL SMK NU WARGABINANGUN")
    r1.bold = True
    r1.font.size = Pt(16.5)
    r1.font.color.rgb = RGBColor(0x1E, 0x3A, 0x8A)

    p_sub = doc.add_paragraph()
    p_sub.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_after = Pt(16)
    r2 = p_sub.add_run("Naskah Berbicara Komprehensif Per-Slide: Mendalam, Naratif, dan Terstruktur untuk Sidang / Presentasi\nPresenter: Winarti (41257168) • TI-1C-2025 • SMK NU WARGABINANGUN")
    r2.font.size = Pt(11)
    r2.font.color.rgb = RGBColor(0x02, 0x84, 0xC7)
    r2.bold = True

    slides_content = [
        (1, "Judul & Pengenalan Sistem Presensi Digital",
         "Assalamu’alaikum Warahmatullahi Wabarakatuh, Selamat pagi/siang kepada Bapak dan Ibu Dewan Penguji yang saya hormati, serta rekan-rekan sekalian yang berbahagia.\n\n"
         "Perkenalkan, nama saya Winarti, dengan NIM 41257168 dari kelas TI-1C-2025. Pada kesempatan yang sangat berharga ini, saya merasa bangga dapat memaparkan dan mendemonstrasikan hasil rancang bangun proyek perangkat lunak saya yang berjudul: 'Sistem Presensi Digital SMK NU WARGABINANGUN: Platform Absensi Modern Berbasis QR Code Scanner, Notifikasi Instan Telegram, Supervisi Akademik, dan Asisten AI Hadir.io'.\n\n"
         "Sistem ini dibangun secara khusus untuk menjawab kebutuhan nyata di lingkungan SMK NU WARGABINANGUN dalam menyongsong era transformasi digital sekolah vokasi. Dengan memadukan teknologi web modern PHP 8.2 MVC, basis data relasional MariaDB, gateway Bot Telegram (@HadirioBot), serta kecerdasan buatan berbasis model bahasa besar NVIDIA NIM, sistem ini dirancang bukan sekadar untuk mencatat kehadiran siswa dan guru, melainkan menjadi pusat ekosistem kedisiplinan, transparansi informasi, dan supervisi akademik yang akuntabel. Mari kita bedah bersama seluruh aspek sistem ini secara mendalam.",
         "Buka slide judul, tatap para penguji dengan senyum hangat dan sikap tubuh tegap."),

        (2, "Latar Belakang & Identifikasi Masalah",
         "Bapak dan Ibu Dewan Penguji, gagasan utama pengembangan sistem ini berakar dari tiga persoalan mendasar yang selama ini terjadi pada tata kelola absensi konvensional di sekolah:\n\n"
         "Pertama, Presensi Manual Berbasis Kertas yang Tidak Efisien. Selama ini, pencatatan absensi masih mengandalkan lembaran buku atau kertas absen manual. Metode ini memiliki banyak kelemahan: berkas rentan robek, basah, tercecer, hingga hilang. Saat jam masuk sekolah tiba, terjadi penumpukan antrean panjang siswa di pos gerbang piket hanya untuk menuliskan nama. Selain itu, staf tata usaha membutuhkan waktu berhari-hari setiap akhir bulan hanya untuk merekapitulasi ribuan baris data absen secara manual, belum lagi risiko terjadinya manipulasi data atau praktik titip absen yang sulit dicegah.\n\n"
         "Kedua, Ketiadaan Akses Informasi Real-Time. Orang tua dan wali murid kerap kali terlambat mengetahui kabar kehadiran putra-putrinya di sekolah. Banyak orang tua baru mengetahui anaknya membolos setelah menerima teguran berhari-hari kemudian. Di sisi manajemen, Kepala Sekolah tidak memiliki dashboard terpusat untuk memantau apakah guru-guru hadir tepat waktu, dan Wali Kelas kesulitan mengawasi tingkat kedisiplinan rombel binaannya secara harian karena data presensi tidak terhubung langsung dengan jadwal KBM.\n\n"
         "Ketiga, Tantangan Tata Kelola Perizinan dan Supervisi yang Belum Terdokumentasi. Alur pengajuan surat izin atau surat sakit masih dilakukan dengan surat fisik yang rawan hilang tanpa verifikasi dokter yang valid. Selain itu, belum ada wadah digital formal bagi Kepala Sekolah untuk mengevaluasi KBM guru, maupun bagi Wali Kelas untuk memberikan bimbingan konseling dan catatan kedisiplinan bagi peserta didik. Ketiga masalah inilah yang menjadi landasan kuat urgensi kehadiran sistem ini.",
         "Gunakan gestur tangan saat menyebutkan poin 1, 2, dan 3. Tekankan dampak negatif dari presensi manual."),

        (3, "Visi Produk & Tujuan Strategis Implementasi",
         "Berangkat dari permasalahan tersebut, kami menetapkan visi produk yang tegas: 'Mewujudkan tata kelola kehadiran yang modern, akuntabel, transparan, dan terintegrasi AI di lingkungan SMK NU WARGABINANGUN.'\n\n"
         "Target utama dari implementasi proyek ini mencakup empat parameter keberhasilan:\n"
         "1. Tercapainya 100% digitalisasi presensi untuk seluruh sivitas sekolah.\n"
         "2. Zero paper waste, yaitu penghapusan total pemborosan penggunaan kertas absensi dan surat fisik.\n"
         "3. Pengiriman notifikasi kehadiran instan ke aplikasi Telegram wali murid secara real-time.\n"
         "4. Pencegahan manipulasi data absensi secara menyeluruh melalui enkripsi identitas digital.\n\n"
         "Untuk merealisasikan visi besar tersebut, kami merancang lima pilar strategi utama: pertama, Otomasi Presensi Cepat di bawah satu detik tanpa sentuhan; kedua, Hak Akses Terisolasi berbasis Role-Based Access Control 5 tingkat; ketiga, Digitalisasi Pengajuan Izin dan Sakit mandiri dengan bukti foto; keempat, Layanan Supervisi KBM dan Pembinaan Karakter terpadu; serta kelima, Layanan Informasi Cerdas melalui Asisten AI Hadir.io yang mendukung empat bahasa.",
         "Tunjukkan poin visi pada layar, sampaikan dengan intonasi mantap dan penuh optimisme."),

        (4, "Target Pengguna: Role-Based Access Control (RBAC) 5 Tingkat",
         "Untuk menjamin integritas data dan keamanan sistem, kami merancang arsitektur Role-Based Access Control (RBAC) yang membagi hak akses ke dalam lima tingkatan pengguna secara terisolasi:\n\n"
         "1. Administrator: Memegang kendali penuh atas sistem. Admin bertanggung jawab mengelola master data rombel kelas, data guru, mata pelajaran, jadwal KBM, dan data siswa. Admin juga memiliki wewenang mencetak kartu presensi ber-barcode, menyetujui pengajuan izin, mengelola rekapitulasi sekolah, serta mengatur token API Telegram dan AI NVIDIA.\n\n"
         "2. Kepala Sekolah: Bertindak sebagai pengawas eksekutif tertinggi. Kepala Sekolah memiliki dashboard manajerial untuk memantau grafik kehadiran harian seluruh siswa dan guru secara makro, mencetak laporan PDF resmi berkop surat, mengelola catatan supervisi evaluasi KBM guru, serta memanfaatkan chatbot AI untuk membaca ringkasan statistik sekolah.\n\n"
         "3. Wali Kelas: Diberikan wewenang terfokus untuk memantau dan merekapitulasi kehadiran siswa di rombel binaannya saja. Wali kelas juga dapat menulis catatan pembinaan karakter bagi siswa yang bermasalah atau berprestasi, sekaligus memantau jadwal pelajaran kelas binaannya.\n\n"
         "4. Guru Pengajar: Memiliki portal untuk melakukan presensi mandiri saat datang dan pulang, memeriksa jadwal mengajar harian dan mingguan, mengajukan permohonan izin dinas secara online, serta membaca arahan supervisi dari Kepala Sekolah secara privat.\n\n"
         "5. Peserta Didik (Siswa): Memiliki akses mandiri untuk memindai presensi QR pribadi, memantau riwayat kehadirannya secara transparan, mengajukan surat izin atau sakit dengan mengunggah foto bukti, serta membaca catatan bimbingan dari Wali Kelas demi evaluasi kedisiplinan diri.",
         "Jelaskan prinsip 'Least Privilege' bahwa setiap peran hanya dapat melihat data yang menjadi haknya."),

        (5, "Modul Presensi & Otomasi: Pemindaian QR Code & Bot Telegram",
         "Bapak dan Ibu Penguji, inilah modul inti operasional harian yang menjadi tulang punggung sistem. Alur pemindaian kartu dirancang sangat cepat, ergonomis, dan higienis:\n\n"
         "1. Alur Pemindaian: Siswa atau Guru cukup membawa kartu identitas digital ber-QR code dan menghadapkannya ke arah kamera pemindai. Sistem menggunakan pustaka HTML5-QRCode yang langsung mengekstraksi kode identitas dan mengirimkannya secara asynchronous via AJAX ke berkas handler presensi.\n\n"
         "2. Validasi Instan di Bawah 0.5 Detik: Sistem langsung memvalidasi keabsahan kode, memeriksa jadwal, mengecek duplikasi scan, dan mencocokkan jam server secara tepat tanpa perlu me-refresh halaman peramban. Hasil presensi, nama, kelas, dan status kehadiran langsung terpampang seketika di panel antarmuka pemindai lengkap dengan notifikasi suara.\n\n"
         "3. Logika Dual-Mode Masuk dan Pulang: Sistem secara cerdas membedakan waktu scan: pemindaian pagi hari akan otomatis mencatat waktu kedatangan. Sedangkan pemindaian di sore hari mulai pukul 15.00 WIB ke atas akan mencatat waktu kepulangan dan menghitung durasi jam belajar di sekolah.\n\n"
         "4. Integrasi Bot Telegram Real-Time (@HadirioBot): Pada saat bersamaan ketika presensi tersimpan di database, server langsung memicu transmisi webhook ke Bot Telegram. Dalam hitungan detik, pesan notifikasi resmi terkirim ke kanal sekolah atau ponsel orang tua. Pesan memuat rincian lengkap: nama siswa, nomor induk, rombel kelas, jam presensi, tanggal, dan status apakah Hadir Tepat Waktu atau Terlambat. Transmisi ini juga telah kami optimalkan dengan opsi koneksi IPv4 untuk menjamin kelancaran pengiriman tanpa kendala timeout pada lingkungan jaringan sekolah.",
         "Tunjukkan simulasi kartu barcode di depan kamera atau gambarkan kecepatan respon sistem."),

        (6, "Manajemen Perizinan Mandiri & Rekapitulasi Pelaporan Otomatis",
         "Modul berikutnya adalah tata kelola perizinan dan rekapitulasi data, yang sepenuhnya menghapus birokrasi kertas konvensional:\n\n"
         "1. Digitalisasi Pengajuan Izin dan Sakit: Ketika siswa atau guru berhalangan hadir, mereka tidak perlu lagi mengirimkan surat fisik yang rawan hilang. Mereka cukup membuka dasbor akun masing-masing, memilih rentang tanggal, mengisi alasan, dan wajib mengunggah file foto surat dokter atau surat keterangan bermaterai dari orang tua.\n\n"
         "2. Mekanisme Approval dan Sinkronisasi Otomatis: Permohonan izin akan masuk ke dasbor Administrator dengan status 'Menunggu'. Admin dapat melihat berkas fisik foto surat secara langsung untuk memastikan validitasnya. Saat Admin menekan tombol 'Setujui', sistem menjalankan mekanisme 'Auto-Sync Rekap', yaitu secara otomatis mencatat status Izin atau Sakit ke tabel data presensi. Hal ini mencegah terjadinya kerja ganda dan menjamin tidak ada siswa izin yang salah terhitung sebagai alpa.\n\n"
         "3. Rekapitulasi Multi-Parameter & Ekspor Berkas: Laporan absensi dapat difilter secara fleksibel berdasarkan tanggal, rentang bulan, jurusan, maupun rombel kelas. Laporan ini dapat langsung diunduh dalam bentuk file Excel (.xlsx) rapi dengan pemformatan warna standar kurikulum, atau dicetak ke dalam format dokumen PDF resmi berlogo sekolah yang siap ditandatangani oleh Kepala Sekolah sebagai arsip akreditasi.",
         "Tekankan bahwa fitur Auto-Sync izin menghemat waktu tata usaha hingga berjam-jam setiap harinya."),

        (7, "Pilar Supervisi KBM & Pembinaan Karakter Terarah",
         "Sistem ini kami rancang melampaui fungsi absensi konvensional, yaitu menjadi jembatan komunikasi edukatif dan pembinaan karakter:\n\n"
         "Pertama, Modul Supervisi Kepala Sekolah terhadap Guru (Entitas `catatan_guru`). Kepala Sekolah dapat memantau keterlaksanaan KBM harian guru dan memberikan instruksi kerja, evaluasi mengajar, maupun motivasi tertulis. Setiap catatan bersifat rahasia dan hanya dapat diakses oleh guru yang bersangkutan. Hebatnya, sistem dilengkapi pelacakan status baca otomatis; ketika guru membuka catatan tersebut, status akan berubah menjadi 'Sudah Dibaca' lengkap dengan catatan waktunya, sehingga Kepala Sekolah dapat memastikan arahannya telah sampai.\n\n"
         "Kedua, Modul Pembinaan Wali Kelas terhadap Siswa (Entitas `catatan_pembinaan`). Wali Kelas dapat mendokumentasikan setiap catatan pelanggaran tata tertib maupun pencapaian prestasi siswa binaannya. Wali Kelas dapat mengatur visibilitas catatan tersebut agar dapat dibaca langsung oleh Siswa. Pada sisi portal siswa, tersedia tombol interaktif 'Tandai Sudah Dibaca', yang berfungsi sebagai bukti komitmen moral peserta didik untuk memperbaiki kedisiplinan dirinya. Data ini terarsip rapi menjadi rekam jejak bimbingan konseling komprehensif bagi sekolah.",
         "Soroti nilai tambah edukatif dari sistem yang tidak hanya fokus pada teknologi, tapi juga pembinaan karakter."),

        (8, "Asisten Cerdas Sekolah Hadir.io: Berbasis NVIDIA NIM LLM & RBAC",
         "Salah satu terobosan paling inovatif dalam sistem ini adalah hadirnya asisten kecerdasan buatan terintegrasi yang kami beri nama 'Hadir.io'. Hadir.io bukan sekadar chatbot biasa, melainkan asisten pintar berbasis model bahasa besar terkini dari NVIDIA NIM, yaitu model Nemotron-3.5.\n\n"
         "Arsitektur Hadir.io dibangun dengan konsep 'Dual-Engine':\n"
         "1. Engine Pertama (Fast-Path SQL): Untuk pertanyaan faktual sekolah—seperti jumlah kelas, total guru aktif, maupun jadwal pelajaran hari ini—sistem langsung mengekstrak metrik data dari basis data lokal sekolah. Hal ini menjamin akurasi informasi sebesar 100% dan bebas dari risiko halusinasi data.\n"
         "2. Engine Kedua (NVIDIA NIM AI): Data faktual tersebut kemudian dirangkum oleh model bahasa alami NVIDIA menjadi jawaban yang ringkas, to-the-point, santun, dan sangat mudah dipahami pengguna.\n\n"
         "Keamanan data tetap menjadi prioritas utama melalui penerapan 'RBAC Query Firewall'. Siswa dilarang keras menanyakan data pribadi guru, jadwal kelas lain, maupun data finansial sekolah; sistem akan menolak permintaan tersebut secara otomatis demi menjaga privasi. Hebatnya lagi, Hadir.io dilengkapi kemampuan multibahasa adaptif yang mendukung 4 bahasa sekaligus, yaitu Bahasa Indonesia, Bahasa Inggris, serta bahasa kearifan lokal daerah: Basa Sunda dan Basa Jawa.",
         "Beri penekanan pada fitur AI 4 bahasa dan ketepatan jawabannya yang langsung to the point tanpa bertele-tele."),

        (9, "Aturan Operasional Jam Presensi & Logika Sistem",
         "Bapak dan Ibu Penguji, agar operasional sekolah berjalan tertib, sistem ditanamkan serangkaian aturan bisnis yang ketat dan otomatis:\n\n"
         "1. Aturan Jam Masuk: Gerbang presensi dibuka mulai pukul 05.00 WIB. Siswa yang memindai kartu antara pukul 05.00 hingga pukul 07.00 WIB akan dicatat dengan status 'Hadir Tepat Waktu'. Namun, jika pemindaian dilakukan lewat dari pukul 07.00 WIB, sistem secara otomatis memberikan label 'Terlambat'. Siswa terlambat tetap diperbolehkan masuk agar tidak tertinggal pelajaran, namun status keterlambatannya tercatat transparan di rekap.\n\n"
         "2. Aturan Jam Pulang: Presensi kepulangan baru dibuka mulai pukul 15.00 WIB ke atas. Sistem akan menolak scan pulang jika dilakukan sebelum pukul 15.00 WIB demi mencegah siswa pulang sebelum jam KBM usai.\n\n"
         "3. Mekanisme 'Dual-Sync' Guru dan Wali Kelas: Di SMK NU WARGABINANGUN, seorang Wali Kelas pada hakikatnya juga merupakan Guru Pengajar. Sistem kami memiliki fitur pintar 'Dual-Sync', di mana ketika seorang wali kelas melakukan satu kali pemindaian kartu, sistem secara atomik dan simultan mencatat kehadirannya pada tabel data guru sekaligus tabel data wali kelas. Hal ini mencegah terjadinya inkonsistensi data antara laporan pendidik sekolah dan laporan rombel.\n\n"
         "4. Siklus Hari Efektif KBM: Hari operasional resmi pembelajaran di sekolah adalah hari Senin sampai dengan Jumat. Hari Sabtu sengaja tetap kami buka pemindainya untuk kebutuhan ekstrakurikuler, uji coba fungsional, dan pengujian bot Telegram, namun secara otomatis dikecualikan dari pembagi rekapitulasi resmi agar persentase kehadiran siswa tetap akurat dan adil. Sedangkan hari Minggu, sistem ditutup total.",
         "Jelaskan aturan jam dan pengecualian hari Sabtu secara tegas agar penguji memahami konsistensi data."),

        (10, "Spesifikasi Arsitektur Teknologi & Keamanan Perangkat Lunak",
         "Dari sudut pandang rekayasa perangkat lunak, sistem ini dirancang dengan standar performa tinggi, efisiensi sumber daya, dan keamanan berlapis:\n\n"
         "1. Arsitektur Backend: Menggunakan bahasa pemrograman modern PHP 8.2 Native dengan pola rancang Model-View-Controller (MVC) terstruktur. Seluruh transaksi data ke MariaDB dijalankan melalui antarmuka PHP Data Objects (PDO) dengan parameterized prepared statements. Hal ini menjamin sistem 100% kebal terhadap ancaman celah keamanan SQL Injection. Seluruh kata sandi pengguna dienkripsi menggunakan algoritma hashing satu arah berstandar industri, yaitu Bcrypt.\n\n"
         "2. Antarmuka Pengguna (Frontend): Menggunakan kombinasi HTML5, CSS3, JavaScript ES6+, dan kerangka kerja Bootstrap 5 yang adaptif. Antarmuka mendukung mode gelap dan terang, serta responsif secara sempurna saat dibuka melalui layar monitor desktop gerbang sekolah, tablet wali kelas, maupun ponsel pintar siswa.\n\n"
         "3. Integrasi Pihak Ketiga: Sistem memanfaatkan Telegram Bot API berbasis transmisi aman HTTPS/IPv4, pustaka SimpleXLSXGen untuk ekspor lembar sebar tanpa membebani memori server, serta NVIDIA NIM REST API untuk pemrosesan AI.\n\n"
         "4. Uji Kinerja: Pengujian performa membuktikan bahwa waktu eksekusi pemindaian barcode hingga data tersimpan di server rata-rata hanya membutuhkan waktu di bawah satu detik (kurang dari 0.5 detik pada jaringan lokal), siap menangani arus kehadiran ratusan siswa di pagi hari tanpa kendala lag.",
         "Tunjukkan bahwa stack teknologi yang dipilih sangat efisien, tanpa beban dependensi yang rumit."),

        (11, "Roadmap Pengembangan Proyek: Status Selesai 100%",
         "Dapat kami sampaikan dengan penuh rasa tanggung jawab bahwa pengembangan proyek Sistem Presensi Digital ini telah berhasil diselesaikan secara tuntas 100% melalui empat fase implementasi yang terencana:\n\n"
         "• Fase 1 (Inisiasi & Basis Data): Meliputi analisis kebutuhan lapangan, perancangan skema relasional 20 tabel database aktif `db_absensi_smk`, serta pembangunan fondasi autentikasi RBAC multi-role.\n\n"
         "• Fase 2 (Inti Presensi & QR Scanner): Meliputi integrasi mesin kamera HTML5 pemindai barcode, implementasi aturan jam masuk dan jam pulang, penanganan duplikasi presensi, serta integrasi webhook Bot Telegram.\n\n"
         "• Fase 3 (Perizinan, Supervisi & Pelaporan): Meliputi digitalisasi pengajuan izin mandiri dengan bukti foto surat dokter, mekanisme auto-sync status absensi, kanal supervisi `catatan_guru` dan `catatan_pembinaan`, serta modul ekspor rekapitulasi data ke format Excel dan PDF.\n\n"
         "• Fase 4 (Kecerdasan Buatan Hadir.io & Uji Coba Lapangan): Meliputi integrasi API model Nemotron NVIDIA NIM, penerapan RBAC Query Firewall 4 bahasa, validasi aturan rekapitulasi Senin-Jumat, serta pengujian fungsional menyeluruh yang memastikan seluruh fitur berjalan tanpa adanya galat (error-free).",
         "Sampaikan dengan rasa bangga dan percaya diri atas tuntasnya seluruh fase proyek."),

        (12, "Penutup & Kesiapan Demonstrasi Langsung",
         "Sebagai penutup dari pemaparan ini, Sistem Presensi Digital SMK NU WARGABINANGUN hadir bukan sekadar sebagai aplikasi absensi biasa, melainkan sebuah lompatan inovasi digital yang memberikan efisiensi operasional nyata, transparansi penuh bagi orang tua murid, kemudahan supervisi bagi pimpinan sekolah, serta membiasakan budaya disiplin berbasis teknologi bagi peserta didik.\n\n"
         "Saya mengucapkan terima kasih yang sebesar-besarnya kepada Bapak dan Ibu Dewan Penguji atas perhatian dan kesempatan yang diberikan. Untuk membuktikan seluruh keunggulan sistem yang telah saya sampaikan pada slide presentasi ini, sekarang dengan senang hati saya akan mendemonstrasikan cara kerja sistem secara langsung di layar monitor, mulai dari pemindaian presensi QR, penerimaan notifikasi live di Telegram, alur persetujuan surat izin, hingga interaksi langsung dengan Asisten AI Hadir.io.\n\n"
         "Wabillahi taufiq wal hidayah, Wassalamu’alaikum Warahmatullahi Wabarakatuh. Mari kita saksikan demonstrasi sistemnya bersama-sama.",
         "Tersenyum ramah, bungkukkan badan tanda hormat, lalu dengan tenang buka jendela web browser untuk memulai live demo.")
    ]

    for s_num, s_title, s_speech, s_tip in slides_content:
        add_slide_box(doc, s_num, s_title, s_speech, s_tip)

    doc.save(output_path)
    print(f"[SUCCESS] Expanded Presentation Script saved to: {output_path}")

if __name__ == "__main__":
    out_docx = r"c:\xampp\htdocs\absen\Naskah_Demonstrasi_PPTX_Sistem_Presensi.docx"
    generate_expanded_docx(out_docx)
