import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Rectangle
import numpy as np

def generate_class_diagram(output_path):
    fig, ax = plt.subplots(figsize=(24, 16), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    fig.patch.set_facecolor("#F8FAFC")
    ax.set_facecolor("#F8FAFC")

    # Header
    banner = FancyBboxPatch((2, 93), 96, 5.5, boxstyle="round,pad=0.2,rounding_size=0.5",
                            facecolor="#0F172A", edgecolor="#0284C7", linewidth=2.0)
    ax.add_patch(banner)
    ax.text(50, 96.2, "CLASS DIAGRAM BERORIENTASI OBJEK (SOLID ARCHITECTURE)",
            fontsize=16, fontweight='bold', color='#FFFFFF', ha='center', va='center')
    ax.text(50, 94.2, "Sistem Presensi Digital SMK NU WARGABINANGUN • Pemetaan MVC, Entitas Domain & Services",
            fontsize=10, color='#BAE6FD', ha='center', va='center')

    # Class Box Drawer
    def draw_class(x, y, name, stereotype, attrs, methods, w=15.0, h=10.0, header_color="#0284C7"):
        # Outer box
        box = FancyBboxPatch((x - w/2, y - h/2), w, h, boxstyle="round,pad=0.1,rounding_size=0.3",
                             facecolor="#FFFFFF", edgecolor="#94A3B8", linewidth=1.5, zorder=3)
        ax.add_patch(box)
        # Header box
        hdr_h = 2.2
        hdr = FancyBboxPatch((x - w/2, y + h/2 - hdr_h), w, hdr_h, boxstyle="round,pad=0.1,rounding_size=0.3",
                             facecolor=header_color, edgecolor="#94A3B8", linewidth=1.2, zorder=4)
        ax.add_patch(hdr)
        if stereotype:
            ax.text(x, y + h/2 - 0.7, f"<<{stereotype}>>", fontsize=6.8, fontstyle='italic', color='#E0F2FE', ha='center', zorder=5)
        ax.text(x, y + h/2 - 1.5, name, fontsize=8.5, fontweight='bold', color='#FFFFFF', ha='center', zorder=5)

        # Separator line
        sep_y = y + h/2 - hdr_h - (len(attrs)*0.45 + 0.5)
        # Attributes
        curr_y = y + h/2 - hdr_h - 0.5
        for a in attrs:
            ax.text(x - w/2 + 0.6, curr_y, a, fontsize=6.8, color='#1E293B', va='center', zorder=5)
            curr_y -= 0.42

        ax.plot([x - w/2, x + w/2], [sep_y, sep_y], color="#CBD5E1", lw=1.0, zorder=5)

        # Methods
        curr_y = sep_y - 0.5
        for m in methods:
            ax.text(x - w/2 + 0.6, curr_y, m, fontsize=6.8, color='#0369A1', va='center', zorder=5)
            curr_y -= 0.42

        return {'cx': x, 'cy': y, 'left': x - w/2, 'right': x + w/2, 'top': y + h/2, 'bottom': y - h/2}

    # Classes
    c_user = draw_class(12.0, 78.0, "User", "Model",
                        ["- id: int", "- username: string", "- password_hash: string", "- role: enum", "- status: enum"],
                        ["+ authenticate(): bool", "+ verifyRole(r): bool", "+ updateProfile(): bool"], w=14.0, h=9.0)

    c_siswa = draw_class(30.0, 78.0, "Siswa", "Model",
                         ["- id: int", "- nis: string", "- nisn: string", "- barcode: string", "- kelas_id: int"],
                         ["+ getPresensi(): List", "+ submitIzin(): bool", "+ getPembinaan(): List"], w=14.0, h=9.0)

    c_guru = draw_class(48.0, 78.0, "Guru", "Model",
                        ["- id: int", "- nip: string", "- barcode: string", "- is_wali_kelas: bool"],
                        ["+ getJadwal(): List", "+ inputJurnal(): bool", "+ submitIzin(): bool"], w=14.0, h=9.0)

    c_absensi = draw_class(66.0, 78.0, "Absensi", "Entity",
                           ["- id: int", "- user_id: int", "- tanggal: Date", "- jam_masuk: Time", "- jam_pulang: Time", "- status: enum"],
                           ["+ recordMasuk(): bool", "+ recordPulang(): bool", "+ executeDualSync(): void"], w=14.0, h=9.0)

    c_izin = draw_class(84.0, 78.0, "PengajuanIzin", "Entity",
                        ["- id: int", "- pemohon_id: int", "- tipe: enum", "- bukti_foto: string", "- status: enum"],
                        ["+ submit(): bool", "+ approve(): bool", "+ reject(): bool"], w=14.0, h=9.0)

    # Controller Layer
    c_auth_ctrl = draw_class(12.0, 56.0, "AuthController", "Controller",
                             ["- userModel: User", "- sessionGuard: Session"],
                             ["+ login(req): void", "+ logout(): void", "+ checkPermission(): bool"], w=14.0, h=8.5, header_color="#1E3A8A")

    c_scan_ctrl = draw_class(48.0, 56.0, "ScanController", "Controller",
                             ["- timeValidator: TimeService", "- absensiModel: Absensi", "- telegramService: BotService"],
                             ["+ handleScan(barcode): json", "+ validateTime(): Status", "+ triggerWebhook(): void"], w=16.0, h=8.5, header_color="#1E3A8A")

    c_rekap_ctrl = draw_class(84.0, 56.0, "LaporanController", "Controller",
                              ["- filterService: DateFilter", "- exporter: ExportService"],
                              ["+ getRekapSeninJumat(): array", "+ exportExcel(): xlsx", "+ exportPDF(): pdf"], w=15.0, h=8.5, header_color="#1E3A8A")

    # Service / External Integration Layer
    c_time_svc = draw_class(30.0, 36.0, "TimeValidatorService", "Service",
                            ["- aturanJam: array", "- activeDays: array"],
                            ["+ evaluateRule(time): string", "+ isWorkDay(date): bool", "+ isWeekendTest(): bool"], w=15.0, h=8.5, header_color="#0D9488")

    c_tg_svc = draw_class(66.0, 36.0, "TelegramBotService", "External Service",
                          ["- botToken: string", "- chatGroupId: string"],
                          ["+ formatPayload(data): string", "+ sendLiveAlert(msg): bool", "+ executeCurl(): void"], w=15.0, h=8.5, header_color="#0D9488")

    c_ai_svc = draw_class(48.0, 18.0, "HadirioAiService", "AI Service (NVIDIA NIM)",
                          ["- nimApiKey: string", "- nemotronModel: string", "- rbacFirewall: Guard"],
                          ["+ parseIntent(prompt): string", "+ extractSQLFacts(): array", "+ callNvidiaApi(): json"], w=17.0, h=9.0, header_color="#D97706")

    # Draw Relations / Connectors cleanly
    def draw_assoc(p1, p2, text=""):
        ax.plot([p1[0], p2[0]], [p1[1], p2[1]], color="#475569", lw=1.3, zorder=2)
        if text:
            mx, my = (p1[0]+p2[0])/2, (p1[1]+p2[1])/2 + 0.4
            ax.text(mx, my, text, fontsize=6.8, color="#475569", ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none"))

    draw_assoc((c_user['right'], c_user['cy']), (c_siswa['left'], c_siswa['cy']), "1..1")
    draw_assoc((c_siswa['right'], c_siswa['cy']), (c_guru['left'], c_guru['cy']), "KBM")
    draw_assoc((c_guru['right'], c_guru['cy']), (c_absensi['left'], c_absensi['cy']), "1..*")
    draw_assoc((c_absensi['right'], c_absensi['cy']), (c_izin['left'], c_izin['cy']), "sync")

    draw_assoc((c_auth_ctrl['cx'], c_auth_ctrl['top']), (c_user['cx'], c_user['bottom']), "uses")
    draw_assoc((c_scan_ctrl['cx'], c_scan_ctrl['top']), (c_guru['cx'], c_guru['bottom']), "processes")
    draw_assoc((c_rekap_ctrl['cx'], c_rekap_ctrl['top']), (c_izin['cx'], c_izin['bottom']), "aggregates")

    draw_assoc((c_scan_ctrl['cx'], c_scan_ctrl['bottom']), (c_time_svc['cx'], c_time_svc['top']), "validates")
    draw_assoc((c_scan_ctrl['cx'], c_scan_ctrl['bottom']), (c_tg_svc['cx'], c_tg_svc['top']), "triggers")
    draw_assoc((c_tg_svc['cx'], c_tg_svc['bottom']), (c_ai_svc['cx'], c_ai_svc['top']), "assists")

    plt.tight_layout()
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print("[SUCCESS] Class Diagram saved to:", output_path)

def generate_sequence_diagram(output_path):
    fig, ax = plt.subplots(figsize=(24, 16), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')

    fig.patch.set_facecolor("#F8FAFC")
    ax.set_facecolor("#F8FAFC")

    # Header
    banner = FancyBboxPatch((2, 93), 96, 5.5, boxstyle="round,pad=0.2,rounding_size=0.5",
                            facecolor="#0F172A", edgecolor="#0284C7", linewidth=2.0)
    ax.add_patch(banner)
    ax.text(50, 96.2, "SEQUENCE DIAGRAM SIKLUS PRESENSI & ASISTEN AI",
            fontsize=16, fontweight='bold', color='#FFFFFF', ha='center', va='center')
    ax.text(50, 94.2, "Alur Pertukaran Pesan: User -> Web UI -> Controller -> MariaDB -> Telegram API & NVIDIA LLM",
            fontsize=10, color='#BAE6FD', ha='center', va='center')

    # Lifelines
    lifelines = [
        ("User (Siswa/Guru)", 10.0),
        ("Web Presentation (UI)", 28.0),
        ("Presensi Controller", 46.0),
        ("Database (db_absensi_smk)", 64.0),
        ("Bot Telegram API", 82.0),
        ("NVIDIA NIM AI API", 94.0)
    ]

    for name, x in lifelines:
        box = FancyBboxPatch((x - 6.5, 87.5), 13.0, 3.5, boxstyle="round,pad=0.1,rounding_size=0.3",
                             facecolor="#0284C7", edgecolor="#0369A1", linewidth=1.2, zorder=3)
        ax.add_patch(box)
        ax.text(x, 89.25, name, fontsize=7.8, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=4)
        ax.plot([x, x], [87.5, 8.0], color="#94A3B8", linestyle="--", lw=1.2, zorder=1)

    # Messages
    def draw_msg(y, x1, x2, text, is_return=False, is_async=False):
        color = "#0284C7" if not is_return else "#64748B"
        linestyle = "--" if is_return else ("-." if is_async else "-")
        ax.annotate('', xy=(x2, y), xytext=(x1, y),
                    arrowprops=dict(arrowstyle="->", color=color, lw=1.3, linestyle=linestyle, shrinkA=0, shrinkB=0),
                    zorder=4)
        mx = (x1 + x2) / 2
        ax.text(mx, y + 0.45, text, fontsize=7.2, fontweight='bold' if not is_return else 'normal',
                color="#0F172A", ha='center', va='center', bbox=dict(boxstyle="round,pad=0.1", fc="#FFFFFF", ec="none", alpha=0.9), zorder=5)

    # Sequence Flow
    y = 84.0
    draw_msg(y, 10.0, 28.0, "1. Arahkan Kartu QR ke Kamera"); y -= 4.5
    draw_msg(y, 28.0, 46.0, "2. AJAX POST scan_handler(barcode, timestamp)"); y -= 4.5
    draw_msg(y, 46.0, 64.0, "3. SELECT user BY barcode & CEK aturan jam"); y -= 4.5
    draw_msg(y, 64.0, 46.0, "4. Return data siswa/guru & jadwal", is_return=True); y -= 4.5
    draw_msg(y, 46.0, 64.0, "5. INSERT absensi_siswa / DUAL-SYNC absensi_guru & wali"); y -= 4.5
    draw_msg(y, 64.0, 46.0, "6. Transaction Commit OK", is_return=True); y -= 4.5

    # Webhook Telegram
    draw_msg(y, 46.0, 82.0, "7. cURL POST sendNotification(Nama, Jam, Status)", is_async=True); y -= 4.5
    draw_msg(y, 82.0, 46.0, "8. Webhook 200 OK (Message Delivered)", is_return=True); y -= 4.5

    # Feedback to UI
    draw_msg(y, 46.0, 28.0, "9. JSON Response: {status: 'success', sound: 'beep'}"); y -= 4.5
    draw_msg(y, 28.0, 10.0, "10. Tampilkan Toast & Status Kehadiran Real-Time"); y -= 6.0

    # AI Chat Flow
    ax.plot([4.0, 96.0], [y, y], color="#E2E8F0", linestyle=":", lw=1.2)
    ax.text(6.0, y + 0.6, "• ALUR PERTANYAAN ASISTEN AI HADIR.IO", fontsize=7.8, fontweight='bold', color="#0369A1"); y -= 3.5

    draw_msg(y, 10.0, 28.0, "11. Ketik prompt 'ada berapa kelas?'"); y -= 4.0
    draw_msg(y, 28.0, 46.0, "12. POST chat_api.php (Sanitasi & RBAC check)"); y -= 4.0
    draw_msg(y, 46.0, 64.0, "13. SELECT COUNT(*) FROM kelas"); y -= 4.0
    draw_msg(y, 64.0, 46.0, "14. Return total: 9 kelas", is_return=True); y -= 4.0
    draw_msg(y, 46.0, 94.0, "15. REST API Prompt: Facts + Nemotron Model", is_async=True); y -= 4.0
    draw_msg(y, 94.0, 46.0, "16. AI Response: 'Jumlah kelas ada 9 kelas...'", is_return=True); y -= 4.0
    draw_msg(y, 46.0, 28.0, "17. Kirim balasan ringkas & to-the-point"); y -= 4.0
    draw_msg(y, 28.0, 10.0, "18. Render bubble chat pada widget Hadir.io")

    plt.tight_layout()
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print("[SUCCESS] Sequence Diagram saved to:", output_path)

if __name__ == "__main__":
    generate_class_diagram(r"c:\xampp\htdocs\absen\Class_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png")
    generate_sequence_diagram(r"c:\xampp\htdocs\absen\Sequence_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png")
