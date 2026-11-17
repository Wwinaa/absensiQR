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

def add_speech_box(doc, section_title, speech_text, tip=""):
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
    p.paragraph_format.space_after = Pt(2)

    r_hdr = p.add_run(f"🎙️ NASKAH BERBICARA: {section_title}\n")
    r_hdr.bold = True
    r_hdr.font.size = Pt(10.5)
    r_hdr.font.color.rgb = RGBColor(0x03, 0x69, 0xA1)

    if tip:
        r_tip = p.add_run(f"(Tips: {tip})\n\n")
        r_tip.italic = True
        r_tip.font.size = Pt(9.5)
        r_tip.font.color.rgb = RGBColor(0x64, 0x74, 0x8B)

    r_speech = p.add_run(speech_text)
    r_speech.font.size = Pt(11)
    r_speech.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    doc.add_paragraph().paragraph_format.space_after = Pt(6)

def generate_compact_naskah(output_path):
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
    r1 = p_title.add_run("NASKAH PRESENTASI SINGKAT & PADAT\nUSE CASE & ACTIVITY DIAGRAM")
    r1.bold = True
    r1.font.size = Pt(17)
    r1.font.color.rgb = RGBColor(0x1E, 0x3A, 0x8A)

    p_sub = doc.add_paragraph()
    p_sub.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_after = Pt(16)
    r2 = p_sub.add_run("Sistem Presensi Digital SMK NU WARGABINANGUN (Berbasis 20 Tabel Database)\nPresenter: Winarti")
    r2.font.size = Pt(11)
    r2.font.color.rgb = RGBColor(0x02, 0x84, 0xC7)
    r2.bold = True

    # Section 1: Pembukaan
    add_speech_box(
        doc,
        "1. PEMBUKAAN & KONSEP TERPADU",
        "Assalamu’alaikum Warahmatullahi Wabarakatuh, dan selamat pagi/siang kepada Bapak/Ibu Dewan Penguji.\n\n"
        "Pada presentasi kali ini, saya, Winarti, akan menjelaskan Use Case Diagram dan Activity Diagram dari Sistem Presensi Digital SMK NU WARGABINANGUN.\n\n"
        "Perlu saya sampaikan bahwa kedua diagram ini sengaja dibuat TERPADU dalam SATU KESATUAN SISTEM dan mencakup SELURUH 20 TABEL database, bukan dipecah-pecah per role. Alasannya, seluruh peran pengguna saling berinteraksi pada database yang sama, seperti sinkronisasi absensi guru dan wali kelas, alur persetujuan surat izin, hingga notifikasi otomatis.",
        "Sampaikan dengan suara tenang, lugas, dan percaya diri."
    )

    # Section 2: Use Case Diagram
    add_speech_box(
        doc,
        "2. PENJELASAN USE CASE DIAGRAM",
        "Bapak/Ibu Penguji, pada Use Case Diagram ini:\n\n"
        "1. Di sisi kiri terdapat 5 Aktor Pengguna: Administrator, Kepala Sekolah, Wali Kelas, Guru, dan Siswa.\n"
        "2. Di sisi kanan terdapat 2 Aktor Eksternal: Bot Telegram (@HadirioBot) untuk notifikasi instan, dan NVIDIA AI Engine untuk kecerdasan chatbot.\n"
        "3. Di dalam sistem, terdapat fungsi-fungsi utama yang langsung terhubung ke 20 tabel database:\n"
        "   • Login & Profil: mengelola akun pada tabel `users` dan `roles`.\n"
        "   • Master Data Akademik: mengelola data sekolah pada tabel `tahun_ajaran`, `jurusan`, `kelas`, `guru`, `wali_kelas`, `siswa`, `mata_pelajaran`, dan `jadwal_mengajar`.\n"
        "   • Kartu Identitas: menerbitkan QR code pada tabel `barcode_guru` dan `rfid_cards`.\n"
        "   • Presensi Harian: mencatat kehadiran pada tabel `absensi_siswa`, `absensi_guru`, dan `absensi_wali_kelas`.\n"
        "     - Di sini ada relasi <<include>> ke Validasi Waktu dan Push Notifikasi Telegram, yang artinya setiap kali scan dilakukan, sistem wajib mengecek jam dan wajib mengirim notifikasi.\n"
        "   • Pengajuan Izin/Sakit: melalui tabel `pengajuan_izin`, yang memiliki relasi <<include>> untuk otomatis memperbarui status absensi siswa/guru saat disetujui.\n"
        "   • Supervisi & Pembinaan: mencatat jurnal KBM pada `catatan_guru` dan bimbingan karakter pada `catatan_pembinaan`.\n"
        "   • Rekapitulasi: menyusun laporan kehadiran resmi untuk diekspor ke Excel dan PDF.\n"
        "   • Chatbot AI Hadir.io: fitur asisten cerdas yang memiliki relasi <<extend>> ke API NVIDIA untuk menjawab pertanyaan seputar data sekolah secara cepat dan ringkas.",
        "Arahkan pandangan ke diagram use case, tunjukkan kotak sistem dan garis relasinya."
    )

    # Section 3: Activity Diagram
    add_speech_box(
        doc,
        "3. PENJELASAN ACTIVITY DIAGRAM",
        "Selanjutnya, beralih ke Activity Diagram yang menggambarkan alur operasional sistem dari awal hingga akhir.\n\n"
        "Diagram ini disusun dalam 5 Swimlanes:\n"
        "1. Aktor: tindakan pengguna.\n"
        "2. UI: tampilan web scanner, dashboard, dan form.\n"
        "3. Controller: mesin logika, aturan jam, dan hak akses.\n"
        "4. Database Layer: pemrosesan ke-20 tabel database.\n"
        "5. External Services: layanan Telegram dan AI NVIDIA.\n\n"
        "Alur utamanya berjalan sangat sistematis:\n"
        "• Pertama, Login: Pengguna memasukkan username & password. Sistem memverifikasi kredensial di tabel `users` & `roles` lalu membuka dashboard sesuai perannya.\n"
        "• Kedua, Scan Presensi (Core Flow): Pengguna men-scan barcode ke kamera. Controller langsung mengevaluasi jam server:\n"
        "   - Jam 05.00 sampai 07.00 WIB dicatat Hadir Tepat Waktu.\n"
        "   - Di atas jam 07.00 WIB otomatis dicatat Terlambat.\n"
        "   - Jam 15.00 WIB ke atas dicatat sebagai Jam Pulang.\n"
        "   Khusus Wali Kelas, sistem menerapkan fitur 'Dual-Sync', yaitu sekali scan otomatis tercatat di tabel `absensi_wali_kelas` sekaligus `absensi_guru` agar data pendidik tetap lengkap. Bersamaan dengan itu, sistem langsung memicu webhook Telegram untuk mengirim notifikasi ke ponsel orang tua dan guru.\n"
        "• Ketiga, Pengajuan Izin/Sakit: Siswa/guru mengunggah surat dokter ke tabel `pengajuan_izin`. Saat Admin menyetujui, sistem otomatis menyinkronkan statusnya ke rekap absensi tanpa perlu input ulang manual.\n"
        "• Keempat, Chatbot Asisten AI Hadir.io: Pengguna bisa menanyakan data sekolah, seperti jumlah guru atau jadwal. Controller mengambil data faktual dari database sesuai hak akses, lalu memanggil AI NVIDIA untuk memberikan jawaban yang ringkas dan to-the-point.\n"
        "• Kelima, Rekapitulasi Presensi: Sistem menerapkan aturan ketat HANYA MENGHITUNG HARI SENIN SAMPAI JUMAT. Hari Sabtu dan Minggu tidak dimasukkan dalam hitungan rekapitulasi kehadiran, sehingga persentase kehadiran tetap akurat saat diekspor ke Excel atau PDF.",
        "Jelaskan bagian aturan jam dan filter Senin-Jumat dengan penekanan yang jelas."
    )

    # Section 4: Penutup
    add_speech_box(
        doc,
        "4. PENUTUP & KESIAPAN TANYA JAWAB",
        "Demikian penjelasan singkat namun menyeluruh mengenai Use Case dan Activity Diagram Sistem Presensi Digital SMK NU WARGABINANGUN.\n\n"
        "Seluruh 20 tabel database telah terintegrasi secara aman, efektif, dan saling mendukung dalam siklus operasional sekolah.\n\n"
        "Terima kasih atas perhatian Bapak/Ibu Dewan Penguji. Saya persilakan jika ada pertanyaan atau masukan. Wassalamu’alaikum Warahmatullahi Wabarakatuh.",
        "Tersenyum sopan dan siap mendengarkan pertanyaan dari penguji."
    )

    # Q&A Quick Reference
    doc.add_heading("Ringkasan Cepat Jawaban untuk Pertanyaan Penguji", level=2)
    qa_points = [
        ("Kenapa diagramnya disatukan, bukan per role?",
         "Karena semua role bekerja pada database yang sama (20 tabel). Diagram yang disatukan menunjukkan transparansi integrasi data, seperti Dual-Sync Wali Kelas dan Auto-Sync Izin."),
        ("Kenapa absen hari Sabtu tidak direkap?",
         "Karena KBM resmi di SMK NU WARGABINANGUN berlangsung Senin–Jumat. Hari Sabtu tetap bisa scan untuk uji coba sistem dan notifikasi Telegram, tetapi tidak dimasukkan ke pembagi total hari hadir agar rekap nilai kehadiran tetap adil."),
        ("Apa itu Dual-Sync pada Wali Kelas?",
         "Wali Kelas juga merupakan Guru. Saat Wali Kelas absen, sistem secara otomatis mencatat ke tabel `absensi_wali_kelas` dan `absensi_guru` secara bersamaan agar laporan kedua kategori tersebut selalu sinkron.")
    ]
    for q, a in qa_points:
        p = doc.add_paragraph()
        rq = p.add_run(f"• {q}\n  ")
        rq.bold = True
        rq.font.color.rgb = RGBColor(0x03, 0x69, 0xA1)
        ra = p.add_run(f"Jawaban: {a}")
        ra.font.size = Pt(10.5)

    doc.save(output_path)
    print(f"[SUCCESS] Compact Naskah saved to: {output_path}")

if __name__ == "__main__":
    out_docx = r"c:\xampp\htdocs\absen\Naskah_Penjelasan_Usecase_dan_Activity_Diagram.docx"
    generate_compact_naskah(out_docx)
