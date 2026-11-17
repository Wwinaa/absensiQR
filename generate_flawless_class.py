import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Rectangle
import numpy as np

def generate_flawless_class_diagram(output_path):
    fig, ax = plt.subplots(figsize=(30, 21), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    BG_COLOR = "#F8FAFC"
    DARK_NAVY = "#0F172A"
    ACCENT_BLUE = "#0284C7"
    DARK_BLUE = "#1E3A8A"
    LIGHT_BORDER = "#CBD5E1"

    fig.patch.set_facecolor(BG_COLOR)
    ax.set_facecolor(BG_COLOR)

    # 1. HEADER BANNER
    banner = FancyBboxPatch((2, 93.0), 96, 5.5, boxstyle="round,pad=0.2,rounding_size=0.6",
                            facecolor=DARK_BLUE, edgecolor=ACCENT_BLUE, linewidth=2.5, zorder=2)
    ax.add_patch(banner)
    ax.text(50, 96.6, "CLASS DIAGRAM BERORIENTASI OBJEK (SOLID & 3-TIER MVC ARCHITECTURE)",
            fontsize=18, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 94.8, "Sistem Presensi Digital SMK NU Wargabinangun • Pemetaan Entitas Domain, Logika Kontroler & Service Integrasi",
            fontsize=11, color='#BAE6FD', ha='center', va='center', zorder=3)
    ax.text(50, 93.5, "Perutean Orthogonal Presisi Tinggi (100% Menempel Border Box & Siku Sempurna 90°)",
            fontsize=9, fontstyle='italic', color='#E2E8F0', ha='center', va='center', zorder=3)

    # Architectural Layers Background Panels
    p1 = FancyBboxPatch((2.0, 68.0), 96.0, 22.5, boxstyle="round,pad=0.2,rounding_size=0.5",
                        facecolor="#F0F9FF", edgecolor="#BAE6FD", linewidth=1.2, linestyle="--", zorder=1)
    ax.add_patch(p1)
    ax.text(3.5, 89.0, "TIER 1: DOMAIN ENTITIES & DATA MODELS (Active Record / ORM Layer)",
            fontsize=8.5, fontweight='bold', color='#0369A1', va='center', zorder=2)

    p2 = FancyBboxPatch((2.0, 40.0), 96.0, 25.5, boxstyle="round,pad=0.2,rounding_size=0.5",
                        facecolor="#F8FAFC", edgecolor="#CBD5E1", linewidth=1.2, linestyle="--", zorder=1)
    ax.add_patch(p2)
    ax.text(3.5, 63.8, "TIER 2: APPLICATION CONTROLLERS (HTTP Handlers & Business Logic)",
            fontsize=8.5, fontweight='bold', color='#1E293B', va='center', zorder=2)

    p3 = FancyBboxPatch((2.0, 9.0), 96.0, 28.5, boxstyle="round,pad=0.2,rounding_size=0.5",
                        facecolor="#F0FDF4", edgecolor="#BBF7D0", linewidth=1.2, linestyle="--", zorder=1)
    ax.add_patch(p3)
    ax.text(3.5, 36.0, "TIER 3: SERVICES & INFRASTRUCTURE (External Adapters, Security Guard, AI & Webhook)",
            fontsize=8.5, fontweight='bold', color='#15803D', va='center', zorder=2)

    # Helper: Draw Class Box with exact boundaries
    classes = {}
    def draw_class(name, cx, cy, stereotype, attrs, methods, w=15.0, header_color="#0284C7"):
        h = 2.2 + (len(attrs) + len(methods)) * 0.40 + 0.5
        left = cx - w/2
        right = cx + w/2
        top = cy + h/2
        bottom = cy - h/2

        card = FancyBboxPatch((left, bottom), w, h, boxstyle="round,pad=0.05,rounding_size=0.2",
                              facecolor="#FFFFFF", edgecolor="#94A3B8", linewidth=1.4, zorder=3)
        ax.add_patch(card)

        hdr_h = 1.9
        hdr = FancyBboxPatch((left, top - hdr_h), w, hdr_h, boxstyle="round,pad=0.05,rounding_size=0.2",
                             facecolor=header_color, edgecolor="#94A3B8", linewidth=1.0, zorder=4)
        ax.add_patch(hdr)
        if stereotype:
            ax.text(cx, top - 0.55, f"<<{stereotype}>>", fontsize=6.2, fontstyle='italic', color='#E0F2FE', ha='center', zorder=5)
            ax.text(cx, top - 1.35, name, fontsize=8.0, fontweight='bold', color='#FFFFFF', ha='center', zorder=5)
        else:
            ax.text(cx, top - 0.95, name, fontsize=8.2, fontweight='bold', color='#FFFFFF', ha='center', zorder=5)

        curr_y = top - hdr_h - 0.35
        for a in attrs:
            ax.text(left + 0.45, curr_y, a, fontsize=6.6, color='#1E293B', va='center', zorder=5)
            curr_y -= 0.40

        ax.plot([left, right], [curr_y + 0.15, curr_y + 0.15], color="#CBD5E1", lw=1.0, zorder=5)
        curr_y -= 0.30

        for m in methods:
            ax.text(left + 0.45, curr_y, m, fontsize=6.6, color='#0369A1' if header_color!=DARK_BLUE else '#1E3A8A', va='center', zorder=5)
            curr_y -= 0.40

        c_info = {'name': name, 'cx': cx, 'cy': cy, 'w': w, 'h': h, 'left': left, 'right': right, 'top': top, 'bottom': bottom}
        classes[name] = c_info
        return c_info

    # 2. DRAW CLASSES ACCURATELY ALIGNED
    # TIER 1: MODELS (cy = 78.5)
    draw_class("User", 11.5, 78.5, "Model",
               ["- id: int", "- username: string", "- password_hash: string", "- role: enum", "- status: enum"],
               ["+ authenticate(): bool", "+ verifyRole(r): bool", "+ getProfile(): array"],
               w=14.0, header_color="#0284C7")

    draw_class("Siswa", 30.5, 78.5, "Entity",
               ["- id: int", "- user_id: int", "- nis: string", "- barcode: string", "- kelas_id: int"],
               ["+ getPresensi(): List", "+ submitIzin(): bool", "+ getPembinaan(): List"],
               w=14.5, header_color="#0284C7")

    draw_class("Guru", 49.5, 78.5, "Entity",
               ["- id: int", "- user_id: int", "- nip: string", "- barcode: string", "- is_wali_kelas: bool"],
               ["+ getJadwal(): List", "+ inputJurnal(): bool", "+ getRiwayatKBM(): List"],
               w=14.5, header_color="#0284C7")

    draw_class("Absensi", 69.5, 78.5, "Entity",
               ["- id: int", "- user_id: int", "- tanggal: Date", "- jam_masuk: Time", "- status: enum"],
               ["+ recordMasuk(): bool", "+ recordPulang(): bool", "+ executeDualSync(): void"],
               w=15.0, header_color="#0284C7")

    draw_class("PengajuanIzin", 89.5, 78.5, "Entity",
               ["- id: int", "- pemohon_id: int", "- tipe: enum", "- bukti_foto: string", "- status: enum"],
               ["+ submitForm(): bool", "+ approve(id): bool", "+ reject(id): bool"],
               w=14.5, header_color="#0284C7")

    # TIER 2: CONTROLLERS (cy = 51.5)
    # Aligned directly beneath models for vertical perfection!
    draw_class("AuthController", 11.5, 51.5, "Controller",
               ["- userModel: User", "- sessionGuard: Session", "- csrfGuard: TokenGuard"],
               ["+ handleLogin(req): void", "+ handleLogout(): void", "+ checkPermission(): bool"],
               w=14.0, header_color=DARK_BLUE)

    draw_class("ScanController", 40.0, 51.5, "Controller",
               ["- timeService: TimeValidator", "- absensiModel: Absensi", "- tgService: BotAlert"],
               ["+ handleScan(barcode): json", "+ validateSchedule(): Status", "+ triggerDualSync(): void"],
               w=17.0, header_color=DARK_BLUE)

    draw_class("SupervisiController", 65.0, 51.5, "Controller",
               ["- guruModel: Guru", "- absensiModel: Absensi", "- jurnalModel: Jurnal"],
               ["+ getKbmToday(): array", "+ reviewJurnal(id): bool", "+ trackReadStatus(): void"],
               w=17.0, header_color=DARK_BLUE)

    draw_class("LaporanController", 89.5, 51.5, "Controller",
               ["- filterService: DateRange", "- pdfExporter: PDFService", "- excelExporter: XLS"],
               ["+ getRekapSeninJumat(): array", "+ exportPDF(): file", "+ exportExcel(): file"],
               w=14.5, header_color=DARK_BLUE)

    # TIER 3: SERVICES & INFRASTRUCTURE (cy = 22.0)
    draw_class("TimeValidationService", 16.0, 22.0, "Domain Service",
               ["- jadwalTable: array", "- waktuSekolah: config", "- toleransiMenit: int"],
               ["+ evaluateRule(waktu): Status", "+ isWorkDay(tanggal): bool", "+ getLatenessMin(): int"],
               w=16.5, header_color="#0D9488")

    draw_class("DatabaseConnection", 40.0, 22.0, "Infrastructure Singleton",
               ["- instance: PDO", "- host: string", "- dbname: string", "- isTransaction: bool"],
               ["+ getInstance(): PDO", "+ beginTransaction(): void", "+ commit(): void", "+ rollback(): void"],
               w=17.0, header_color="#0D9488")

    draw_class("TelegramNotificationService", 65.0, 22.0, "External Adapter",
               ["- botToken: string", "- groupChatId: string", "- endpointUrl: string"],
               ["+ formatPresensiMsg(data): text", "+ sendInstantAlert(msg): bool", "+ asyncCurlExecute(): void"],
               w=17.0, header_color="#0D9488")

    draw_class("HadirioAiService", 89.5, 22.0, "AI Service (NVIDIA NIM)",
               ["- nimApiKey: string", "- model: nemotron-4-340b", "- rbacFirewall: Guard"],
               ["+ sanitizeQuery(prompt): string", "+ executeRAGQuery(): array", "+ generateExplanation(): text"],
               w=14.5, header_color="#D97706")

    # 3. DRAW EXACT ORTHOGONAL CONNECTORS
    def draw_link(pts, label="", arrow_end=False, is_dashed=False, color="#334155"):
        xs = [p[0] for p in pts]
        ys = [p[1] for p in pts]
        ax.plot(xs, ys, color=color, lw=1.4, linestyle="--" if is_dashed else "-", zorder=2)
        if arrow_end:
            p_prev = pts[-2]
            p_last = pts[-1]
            ax.annotate('', xy=p_last, xytext=p_prev,
                        arrowprops=dict(arrowstyle="->", color=color, lw=1.4, linestyle="--" if is_dashed else "-"), zorder=3)
        if label:
            mid_idx = len(pts) // 2
            mx = (pts[mid_idx-1][0] + pts[mid_idx][0]) / 2
            my = (pts[mid_idx-1][1] + pts[mid_idx][1]) / 2
            ax.text(mx, my, label, fontsize=6.6, color=color, ha='center', va='center',
                    bbox=dict(boxstyle="round,pad=0.15", fc="#FFFFFF", ec=color, lw=0.7, alpha=0.95), zorder=4)

    u = classes["User"]
    si = classes["Siswa"]
    gu = classes["Guru"]
    ab = classes["Absensi"]
    iz = classes["PengajuanIzin"]

    ac = classes["AuthController"]
    sc = classes["ScanController"]
    su = classes["SupervisiController"]
    lc = classes["LaporanController"]

    ts = classes["TimeValidationService"]
    db = classes["DatabaseConnection"]
    tg = classes["TelegramNotificationService"]
    ai = classes["HadirioAiService"]

    # A. Model Associations (Horizontal Straight Lines in Tier 1 at y = 78.5)
    draw_link([(u['right'], 78.5), (si['left'], 78.5)], "1..1")
    draw_link([(si['right'], 78.5), (gu['left'], 78.5)], "KBM")
    draw_link([(gu['right'], 78.5), (ab['left'], 78.5)], "1..*")
    draw_link([(ab['right'], 78.5), (iz['left'], 78.5)], "sync")

    # B. Tier 2 to Tier 1: Controllers depend on Models
    # 1. AuthController -> User (100% Vertical Straight Up)
    draw_link([(ac['cx'], ac['top']), (u['cx'], u['bottom'])], "authenticates", arrow_end=True, is_dashed=True)

    # 2. LaporanController -> PengajuanIzin (100% Vertical Straight Up)
    draw_link([(lc['cx'], lc['top']), (iz['cx'], iz['bottom'])], "aggregates", arrow_end=True, is_dashed=True)

    # 3. ScanController -> Absensi & Guru (Clean T-Bus in Corridor y = 69.5)
    draw_link([(sc['cx'], sc['top']), (sc['cx'], 69.5), (ab['cx'] - 4.0, 69.5), (ab['cx'] - 4.0, ab['bottom'])],
              "records", arrow_end=True, is_dashed=True)

    # 4. SupervisiController -> Guru (Clean Step in Corridor y = 71.5)
    draw_link([(su['cx'], su['top']), (su['cx'], 71.5), (gu['cx'] + 3.0, 71.5), (gu['cx'] + 3.0, gu['bottom'])],
              "supervises", arrow_end=True, is_dashed=True)

    # C. Tier 2 to Tier 3: Controllers call Services
    # 1. ScanController -> DatabaseConnection (100% Vertical Straight Down)
    draw_link([(sc['cx'], sc['bottom']), (db['cx'], db['top'])], "persists", arrow_end=True, is_dashed=True)

    # 2. LaporanController -> HadirioAiService (100% Vertical Straight Down)
    draw_link([(lc['cx'], lc['bottom']), (ai['cx'], ai['top'])], "queries NLP", arrow_end=True, is_dashed=True)

    # 3. ScanController -> TimeValidationService (Clean Step in Corridor y = 37.5)
    draw_link([(sc['cx'] - 4.0, sc['bottom']), (sc['cx'] - 4.0, 37.5), (ts['cx'], 37.5), (ts['cx'], ts['top'])],
              "validates", arrow_end=True, is_dashed=True)

    # 4. ScanController -> TelegramNotificationService (Clean Step in Corridor y = 37.5)
    draw_link([(sc['cx'] + 4.0, sc['bottom']), (sc['cx'] + 4.0, 37.5), (tg['cx'], 37.5), (tg['cx'], tg['top'])],
              "triggers webhook", arrow_end=True, is_dashed=True)

    # 4. FOOTER SOLID PRINCIPLES
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
    print("[SUCCESS] Flawless Class Diagram saved to:", output_path)

if __name__ == "__main__":
    generate_flawless_class_diagram(r"c:\xampp\htdocs\absen\Class_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png")
