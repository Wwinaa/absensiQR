import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Rectangle
import numpy as np

def generate_perfect_class_diagram(output_path):
    fig, ax = plt.subplots(figsize=(28, 20), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    BG_COLOR = "#F8FAFC"
    DARK_NAVY = "#0F172A"
    ACCENT_BLUE = "#0284C7"
    DARK_BLUE = "#1E3A8A"
    SLATE_BORDER = "#94A3B8"
    LIGHT_BORDER = "#CBD5E1"
    LINE_COLOR = "#334155"

    fig.patch.set_facecolor(BG_COLOR)
    ax.set_facecolor(BG_COLOR)

    # 1. HEADER BANNER
    banner = FancyBboxPatch((2, 92.5), 96, 6.2, boxstyle="round,pad=0.2,rounding_size=0.6",
                            facecolor=DARK_BLUE, edgecolor=ACCENT_BLUE, linewidth=2.5, zorder=2)
    ax.add_patch(banner)
    ax.text(50, 96.6, "CLASS DIAGRAM BERORIENTASI OBJEK (SOLID & 3-TIER MVC ARCHITECTURE)",
            fontsize=18, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 94.7, "Sistem Presensi Digital SMK NU Wargabinangun • Pemetaan Entitas Domain, Logika Kontroler, & Service Integrasi",
            fontsize=11, color='#BAE6FD', ha='center', va='center', zorder=3)
    ax.text(50, 93.3, "Standar UML 2.5 • Konektor Orthogonal Bebas Silang (Zero-Diagonal Manhattan Routing)",
            fontsize=9, fontstyle='italic', color='#E2E8F0', ha='center', va='center', zorder=3)

    # Architectural Layers Background Panels
    # Layer 1: Model & Domain Entity (y: 69 to 90)
    p_model = FancyBboxPatch((2.0, 68.0), 96.0, 22.5, boxstyle="round,pad=0.2,rounding_size=0.5",
                             facecolor="#F0F9FF", edgecolor="#BAE6FD", linewidth=1.2, linestyle="--", zorder=1)
    ax.add_patch(p_model)
    ax.text(3.5, 89.0, "TIER 1: DOMAIN ENTITIES & DATA MODELS (Active Record / ORM Layer)",
            fontsize=8.5, fontweight='bold', color='#0369A1', va='center', zorder=2)

    # Layer 2: Controller & Application Business Logic (y: 41 to 65)
    p_ctrl = FancyBboxPatch((2.0, 40.0), 96.0, 25.5, boxstyle="round,pad=0.2,rounding_size=0.5",
                            facecolor="#F8FAFC", edgecolor="#CBD5E1", linewidth=1.2, linestyle="--", zorder=1)
    ax.add_patch(p_ctrl)
    ax.text(3.5, 63.8, "TIER 2: APPLICATION CONTROLLERS (HTTP Handlers & Business Logic)",
            fontsize=8.5, fontweight='bold', color='#1E293B', va='center', zorder=2)

    # Layer 3: Services & External Infrastructure (y: 10 to 37)
    p_svc = FancyBboxPatch((2.0, 9.0), 96.0, 28.5, boxstyle="round,pad=0.2,rounding_size=0.5",
                           facecolor="#F0FDF4", edgecolor="#BBF7D0", linewidth=1.2, linestyle="--", zorder=1)
    ax.add_patch(p_svc)
    ax.text(3.5, 36.0, "TIER 3: SERVICES & INFRASTRUCTURE (External Adapters, Security Guard, AI & Webhook)",
            fontsize=8.5, fontweight='bold', color='#15803D', va='center', zorder=2)

    # Helper: Draw Professional UML Class Box
    def draw_class_box(cx, cy, name, stereotype, attrs, methods, w=15.0, header_color="#0284C7"):
        h = 2.4 + (len(attrs) + len(methods)) * 0.42 + 0.6
        # Outer Card
        card = FancyBboxPatch((cx - w/2, cy - h/2), w, h, boxstyle="round,pad=0.1,rounding_size=0.3",
                              facecolor="#FFFFFF", edgecolor="#94A3B8", linewidth=1.4, zorder=3)
        ax.add_patch(card)

        # Header
        hdr_h = 2.0
        hdr = FancyBboxPatch((cx - w/2, cy + h/2 - hdr_h), w, hdr_h, boxstyle="round,pad=0.1,rounding_size=0.3",
                             facecolor=header_color, edgecolor="#94A3B8", linewidth=1.0, zorder=4)
        ax.add_patch(hdr)
        if stereotype:
            ax.text(cx, cy + h/2 - 0.6, f"<<{stereotype}>>", fontsize=6.5, fontstyle='italic', color='#E0F2FE', ha='center', zorder=5)
            ax.text(cx, cy + h/2 - 1.4, name, fontsize=8.2, fontweight='bold', color='#FFFFFF', ha='center', zorder=5)
        else:
            ax.text(cx, cy + h/2 - 1.0, name, fontsize=8.5, fontweight='bold', color='#FFFFFF', ha='center', zorder=5)

        # Attributes
        curr_y = cy + h/2 - hdr_h - 0.45
        for a in attrs:
            ax.text(cx - w/2 + 0.5, curr_y, a, fontsize=6.8, color='#1E293B', va='center', zorder=5)
            curr_y -= 0.42

        # Separator Line
        ax.plot([cx - w/2, cx + w/2], [curr_y + 0.15, curr_y + 0.15], color="#CBD5E1", lw=1.0, zorder=5)
        curr_y -= 0.35

        # Methods
        for m in methods:
            ax.text(cx - w/2 + 0.5, curr_y, m, fontsize=6.8, color='#0369A1' if header_color!=DARK_BLUE else '#1E3A8A', va='center', zorder=5)
            curr_y -= 0.42

        return {'cx': cx, 'cy': cy, 'left': cx - w/2, 'right': cx + w/2, 'top': cy + h/2, 'bottom': cy - h/2, 'w': w, 'h': h}

    # 2. INSTANTIATE TIER 1: DOMAIN MODELS (Top Row)
    # y = 78.5
    c_user = draw_class_box(11.5, 78.5, "User", "Model",
                            ["- id: int", "- username: string", "- password_hash: string", "- role: enum", "- status: enum"],
                            ["+ authenticate(): bool", "+ verifyRole(r): bool", "+ getProfile(): array"],
                            w=14.0, header_color="#0284C7")

    c_siswa = draw_class_box(31.0, 78.5, "Siswa", "Entity",
                             ["- id: int", "- user_id: int", "- nis: string", "- barcode: string", "- kelas_id: int"],
                             ["+ getPresensi(): List", "+ submitIzin(): bool", "+ getPembinaan(): List"],
                             w=14.5, header_color="#0284C7")

    c_guru = draw_class_box(51.0, 78.5, "Guru", "Entity",
                            ["- id: int", "- user_id: int", "- nip: string", "- barcode: string", "- is_wali_kelas: bool"],
                            ["+ getJadwal(): List", "+ inputJurnal(): bool", "+ getRiwayatKBM(): List"],
                            w=14.5, header_color="#0284C7")

    c_absensi = draw_class_box(71.0, 78.5, "Absensi", "Entity",
                              ["- id: int", "- user_id: int", "- tanggal: Date", "- jam_masuk: Time", "- status: enum"],
                              ["+ recordMasuk(): bool", "+ recordPulang(): bool", "+ executeDualSync(): void"],
                              w=15.0, header_color="#0284C7")

    c_izin = draw_class_box(90.5, 78.5, "PengajuanIzin", "Entity",
                            ["- id: int", "- pemohon_id: int", "- tipe: enum", "- bukti_foto: string", "- status: enum"],
                            ["+ submitForm(): bool", "+ approve(id): bool", "+ reject(id): bool"],
                            w=14.5, header_color="#0284C7")

    # 3. INSTANTIATE TIER 2: CONTROLLERS (Middle Row)
    # y = 51.5
    c_auth_ctrl = draw_class_box(16.0, 51.5, "AuthController", "Controller",
                                 ["- userModel: User", "- sessionGuard: Session", "- csrfGuard: TokenGuard"],
                                 ["+ handleLogin(req): void", "+ handleLogout(): void", "+ checkPermission(): bool"],
                                 w=16.5, header_color=DARK_BLUE)

    c_scan_ctrl = draw_class_box(41.0, 51.5, "ScanController", "Controller",
                                 ["- timeService: TimeValidator", "- absensiModel: Absensi", "- tgService: BotAlert"],
                                 ["+ handleScan(barcode): json", "+ validateSchedule(): Status", "+ triggerDualSync(): void"],
                                 w=17.5, header_color=DARK_BLUE)

    c_super_ctrl = draw_class_box(65.5, 51.5, "SupervisiController", "Controller",
                                  ["- guruModel: Guru", "- absensiModel: Absensi", "- jurnalModel: Jurnal"],
                                  ["+ getKbmToday(): array", "+ reviewJurnal(id): bool", "+ trackReadStatus(): void"],
                                  w=17.5, header_color=DARK_BLUE)

    c_lap_ctrl = draw_class_box(89.5, 51.5, "LaporanController", "Controller",
                                ["- filterService: DateRange", "- pdfExporter: PDFService", "- excelExporter: XLS"],
                                ["+ getRekapSeninJumat(): array", "+ exportPDF(): file", "+ exportExcel(): file"],
                                w=16.5, header_color=DARK_BLUE)

    # 4. INSTANTIATE TIER 3: SERVICES & EXTERNAL INFRASTRUCTURE (Bottom Row)
    # y = 22.0
    c_time_svc = draw_class_box(16.0, 22.0, "TimeValidationService", "Domain Service",
                                ["- jadwalTable: array", "- waktuSekolah: config", "- toleransiMenit: int"],
                                ["+ evaluateRule(waktu): Status", "+ isWorkDay(tanggal): bool", "+ getLatenessMin(): int"],
                                w=17.0, header_color="#0D9488")

    c_db_svc = draw_class_box(41.0, 22.0, "DatabaseConnection", "Infrastructure Singleton",
                              ["- instance: PDO", "- host: string", "- dbname: string", "- isTransaction: bool"],
                              ["+ getInstance(): PDO", "+ beginTransaction(): void", "+ commit(): void", "+ rollback(): void"],
                              w=17.5, header_color="#0D9488")

    c_tg_svc = draw_class_box(65.5, 22.0, "TelegramNotificationService", "External Adapter",
                              ["- botToken: string", "- groupChatId: string", "- endpointUrl: string"],
                              ["+ formatPresensiMsg(data): text", "+ sendInstantAlert(msg): bool", "+ asyncCurlExecute(): void"],
                              w=18.0, header_color="#0D9488")

    c_ai_svc = draw_class_box(89.5, 22.0, "HadirioAiService", "AI Service (NVIDIA NIM)",
                              ["- nimApiKey: string", "- model: nemotron-4-340b", "- rbacFirewall: Guard"],
                              ["+ sanitizeQuery(prompt): string", "+ executeRAGQuery(): array", "+ generateExplanation(): text"],
                              w=17.5, header_color="#D97706")

    # 5. DRAW STRICT ORTHOGONAL CONNECTORS (ZERO DIAGONAL CROSSINGS)
    def draw_orthogonal_line(points, color=LINE_COLOR, lw=1.4, linestyle="-", arrow_end=False, label=""):
        # points is a list of (x, y) tuples
        xs = [p[0] for p in points]
        ys = [p[1] for p in points]
        ax.plot(xs, ys, color=color, lw=lw, linestyle=linestyle, zorder=2)
        if arrow_end:
            # Draw arrow at the last segment
            p_prev = points[-2]
            p_last = points[-1]
            ax.annotate('', xy=p_last, xytext=p_prev,
                        arrowprops=dict(arrowstyle="->", color=color, lw=lw, linestyle=linestyle), zorder=3)
        if label:
            mid_idx = len(points) // 2
            mx = (points[mid_idx-1][0] + points[mid_idx][0]) / 2
            my = (points[mid_idx-1][1] + points[mid_idx][1]) / 2 + 0.35
            ax.text(mx, my, label, fontsize=6.8, color=color, ha='center', va='center',
                    bbox=dict(boxstyle="round,pad=0.15", fc="#FFFFFF", ec="none", alpha=0.9), zorder=4)

    # A. Model Associations (Horizontal Straight Lines in Tier 1)
    # User -> Siswa (1..1)
    draw_orthogonal_line([(c_user['right'], c_user['cy']), (c_siswa['left'], c_siswa['cy'])], label="1..1")
    # Siswa -> Guru (KBM Schedule)
    draw_orthogonal_line([(c_siswa['right'], c_siswa['cy']), (c_guru['left'], c_guru['cy'])], label="KBM")
    # Guru -> Absensi (1..N)
    draw_orthogonal_line([(c_guru['right'], c_guru['cy']), (c_absensi['left'], c_absensi['cy'])], label="1..*")
    # Absensi -> PengajuanIzin (0..1)
    draw_orthogonal_line([(c_absensi['right'], c_absensi['cy']), (c_izin['left'], c_izin['cy'])], label="sync")

    # B. Tier 2 to Tier 1: Controllers depend on Models (Clean Vertical Upwards Lines)
    # AuthController -> User (Vertical straight line)
    draw_orthogonal_line([(c_auth_ctrl['cx'] - 4.5, c_auth_ctrl['top']), (c_user['cx'], c_user['bottom'])],
                         arrow_end=True, linestyle="--", label="authenticates")

    # ScanController -> Guru & Absensi (Clean Orthogonal T-Bus)
    # Exits top of ScanController, steps right to Absensi and left to Guru
    draw_orthogonal_line([(c_scan_ctrl['cx'], c_scan_ctrl['top']),
                          (c_scan_ctrl['cx'], c_scan_ctrl['top'] + 2.5),
                          (c_absensi['cx'] - 5.0, c_scan_ctrl['top'] + 2.5),
                          (c_absensi['cx'] - 5.0, c_absensi['bottom'])],
                         arrow_end=True, linestyle="--", label="records")

    # SupervisiController -> Absensi & Guru
    draw_orthogonal_line([(c_super_ctrl['cx'], c_super_ctrl['top']),
                          (c_super_ctrl['cx'], c_super_ctrl['top'] + 2.5),
                          (c_guru['cx'] + 4.0, c_super_ctrl['top'] + 2.5),
                          (c_guru['cx'] + 4.0, c_guru['bottom'])],
                         arrow_end=True, linestyle="--", label="supervises")

    # LaporanController -> PengajuanIzin (Vertical straight line)
    draw_orthogonal_line([(c_lap_ctrl['cx'], c_lap_ctrl['top']), (c_izin['cx'], c_izin['bottom'])],
                         arrow_end=True, linestyle="--", label="aggregates")

    # C. Tier 2 to Tier 3: Controllers call Services (Strict Orthogonal Downwards Lines)
    # ScanController -> TimeValidationService (Orthogonal Step Left)
    draw_orthogonal_line([(c_scan_ctrl['cx'] - 5.0, c_scan_ctrl['bottom']),
                          (c_scan_ctrl['cx'] - 5.0, c_scan_ctrl['bottom'] - 2.8),
                          (c_time_svc['cx'] + 3.0, c_scan_ctrl['bottom'] - 2.8),
                          (c_time_svc['cx'] + 3.0, c_time_svc['top'])],
                         arrow_end=True, linestyle="--", label="validates")

    # ScanController -> DatabaseConnection (Vertical Straight Down)
    draw_orthogonal_line([(c_scan_ctrl['cx'], c_scan_ctrl['bottom']), (c_db_svc['cx'], c_db_svc['top'])],
                         arrow_end=True, linestyle="--", label="persists")

    # ScanController -> TelegramNotificationService (Orthogonal Step Right)
    draw_orthogonal_line([(c_scan_ctrl['cx'] + 5.0, c_scan_ctrl['bottom']),
                          (c_scan_ctrl['cx'] + 5.0, c_scan_ctrl['bottom'] - 2.8),
                          (c_tg_svc['cx'] - 3.0, c_scan_ctrl['bottom'] - 2.8),
                          (c_tg_svc['cx'] - 3.0, c_tg_svc['top'])],
                         arrow_end=True, linestyle="--", label="triggers webhook")

    # LaporanController -> HadirioAiService (Vertical Straight Down)
    draw_orthogonal_line([(c_lap_ctrl['cx'], c_lap_ctrl['bottom']), (c_ai_svc['cx'], c_ai_svc['top'])],
                         arrow_end=True, linestyle="--", label="queries NLP")

    # 6. FOOTER LEGEND & SOLID PRINCIPLES BADGE
    ftr = FancyBboxPatch((2, 1.2), 96, 5.8, boxstyle="round,pad=0.2,rounding_size=0.5",
                         facecolor="#FFFFFF", edgecolor=LIGHT_BORDER, linewidth=1.3, zorder=3)
    ax.add_patch(ftr)

    ax.text(3.5, 5.5, "PENERAPAN PRINSIP SOLID PADA ARSITEKTUR KELAS:",
            fontsize=8.5, fontweight='bold', color=DARK_NAVY, va='center')

    solid_items = [
        ("S - Single Responsibility:", "Setiap kelas hanya memiliki 1 fokus tugas spesifik (contoh: TimeValidationService khusus aturan jam)."),
        ("O - Open/Closed:", "Desain model siap diekstensi dengan filter/hook baru tanpa memodifikasi core engine absensi."),
        ("L - Liskov Substitution:", "Entitas Siswa & Guru mengimplementasikan kontrak identitas turunan dari User secara konsisten."),
        ("I - Interface Segregation:", "Service integrasi (Telegram, AI NIM) terisolasi dari controller utama via endpoint bersih."),
        ("D - Dependency Inversion:", "Controller bergantung pada abstraksi service, koneksi database tersentralisasi via Singleton PDO.")
    ]

    sy = 4.2
    for title, desc in solid_items:
        ax.text(3.5, sy, title, fontsize=6.8, fontweight='bold', color=DARK_BLUE, va='center')
        ax.text(23.0, sy, desc, fontsize=6.4, color='#334155', va='center')
        sy -= 0.72

    plt.tight_layout()
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print("[SUCCESS] Perfect Class Diagram saved to:", output_path)

if __name__ == "__main__":
    generate_perfect_class_diagram(r"c:\xampp\htdocs\absen\Class_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png")
