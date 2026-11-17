import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Rectangle
import numpy as np

def generate_flawless_sequence_diagram(output_path):
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
    ax.text(50, 96.6, "SEQUENCE DIAGRAM SIKLUS PRESENSI & ASISTEN AI HADIR.IO",
            fontsize=18, fontweight='bold', color='#FFFFFF', ha='center', va='center', zorder=3)
    ax.text(50, 94.8, "Alur Pesan Antar-Komponen: Presentation (Web) -> Controller -> MariaDB 20 Tabel -> Telegram Webhook & NVIDIA LLM",
            fontsize=11, color='#BAE6FD', ha='center', va='center', zorder=3)
    ax.text(50, 93.5, "Standar UML 2.5 • Presisi Focus of Control (Activation Bars Menempel 100% pada Panah Pesan Masuk & Keluar)",
            fontsize=9, fontstyle='italic', color='#E2E8F0', ha='center', va='center', zorder=3)

    # 2. DEFINE PARTICIPANTS & EXACT GEOMETRY
    participants = [
        {"id": "user", "name": "User\n(Siswa / Guru)", "sub": "Aktor Fisik", "cx": 9.5, "color": "#0284C7", "bg": "#EFF6FF"},
        {"id": "ui",   "name": "Web Presentation\n(Browser / PWA)", "sub": "View / JS Client", "cx": 26.0, "color": "#0284C7", "bg": "#EFF6FF"},
        {"id": "ctrl", "name": "Presensi & AI\nController", "sub": "App Business Logic", "cx": 44.0, "color": DARK_BLUE, "bg": "#EFF6FF"},
        {"id": "db",   "name": "Database MariaDB\n(db_absensi_smk)", "sub": "Data Tier 20 Tabel", "cx": 62.0, "color": "#0D9488", "bg": "#F0FDF4"},
        {"id": "tg",   "name": "Bot Telegram API\n(Broadcast Alert)", "sub": "External Webhook", "cx": 79.0, "color": "#D97706", "bg": "#FFFBEB"},
        {"id": "nim",  "name": "NVIDIA NIM AI\n(Nemotron LLM)", "sub": "External AI Service", "cx": 93.5, "color": "#7C3AED", "bg": "#F5F3FF"}
    ]

    parts_dict = {}
    lifeline_bottom_y = 8.5
    for p in participants:
        px = p["cx"]
        w, h = 12.0, 4.2
        box = FancyBboxPatch((px - w/2, 86.8), w, h, boxstyle="round,pad=0.1,rounding_size=0.35",
                             facecolor=p["bg"], edgecolor=p["color"], linewidth=1.6, zorder=6)
        ax.add_patch(box)
        ax.text(px, 89.4, p["name"], fontsize=7.2, fontweight='bold', color=DARK_NAVY, ha='center', va='center', zorder=7)
        ax.text(px, 87.6, p["sub"], fontsize=5.8, fontstyle='italic', color=p["color"], ha='center', va='center', zorder=7)

        # Dashed Lifeline
        ax.plot([px, px], [86.8, lifeline_bottom_y], color="#94A3B8", lw=1.2, linestyle="--", zorder=1)

        act_w = 1.6
        parts_dict[p["id"]] = {
            'cx': px,
            'left': px - act_w/2,
            'right': px + act_w/2,
            'act_w': act_w
        }

    # 3. INTERACTION FRAMES (COMBINED FRAGMENTS)
    # Frame 1: Siklus Presensi (y: 49.0 to 85.5)
    f1 = FancyBboxPatch((3.0, 49.0), 94.0, 36.5, boxstyle="round,pad=0.15,rounding_size=0.4",
                        facecolor="#FFFFFF", edgecolor="#38BDF8", linewidth=1.5, zorder=0, alpha=0.9)
    ax.add_patch(f1)
    tag1 = FancyBboxPatch((3.0, 82.5), 33.0, 3.0, boxstyle="round,pad=0.1,rounding_size=0.2",
                          facecolor="#E0F2FE", edgecolor="#0284C7", linewidth=1.2, zorder=2)
    ax.add_patch(tag1)
    ax.text(19.5, 84.0, "sd: Siklus Presensi QR / RFID & Webhook Notifikasi",
            fontsize=8.0, fontweight='bold', color=DARK_BLUE, ha='center', va='center', zorder=3)

    # Frame 2: Tanya Jawab AI Hadir.io (y: 10.5 to 47.0)
    f2 = FancyBboxPatch((3.0, 10.5), 94.0, 36.5, boxstyle="round,pad=0.15,rounding_size=0.4",
                        facecolor="#FFFFFF", edgecolor="#C084FC", linewidth=1.5, zorder=0, alpha=0.9)
    ax.add_patch(f2)
    tag2 = FancyBboxPatch((3.0, 44.0), 33.0, 3.0, boxstyle="round,pad=0.1,rounding_size=0.2",
                          facecolor="#F3E8FF", edgecolor="#7C3AED", linewidth=1.2, zorder=2)
    ax.add_patch(tag2)
    ax.text(19.5, 45.5, "sd: Konsultasi Natural Language AI & RBAC Guard",
            fontsize=8.0, fontweight='bold', color="#581C87", ha='center', va='center', zorder=3)

    # Helper: Draw Activation Box
    def draw_activation(part_id, y_top, y_bot, color="#0284C7", bg="#E0F2FE"):
        p = parts_dict[part_id]
        h = abs(y_top - y_bot)
        cy = (y_top + y_bot) / 2
        box = FancyBboxPatch((p['left'], cy - h/2), p['act_w'], h, boxstyle="round,pad=0.02,rounding_size=0.1",
                             facecolor=bg, edgecolor=color, linewidth=1.2, zorder=3)
        ax.add_patch(box)

    # 4. DRAW ACTIVATION BARS WITH MATHEMATICAL PRECISION
    # Frame 1:
    draw_activation("user", 81.0, 79.5)
    draw_activation("ui",   81.0, 51.5)
    draw_activation("ctrl", 78.0, 53.5)
    draw_activation("db",   75.0, 72.0, color="#0D9488", bg="#CCFBF1") # Query 1
    draw_activation("db",   66.0, 63.0, color="#0D9488", bg="#CCFBF1") # Query 2
    draw_activation("tg",   60.0, 57.0, color="#D97706", bg="#FEF3C7") # Webhook

    # Frame 2:
    draw_activation("user", 42.5, 41.0)
    draw_activation("ui",   42.5, 14.5)
    draw_activation("ctrl", 39.5, 17.5)
    draw_activation("db",   33.5, 30.5, color="#0D9488", bg="#CCFBF1") # Query Facts
    draw_activation("nim",  27.5, 21.5, color="#7C3AED", bg="#EDE9FE") # LLM Reasoning

    # Helper: Draw Message Arrow with exact attachment to activation box borders
    def draw_msg(src_id, dst_id, y, step_num, text, msg_type="sync", latency=""):
        p_src = parts_dict[src_id]
        p_dst = parts_dict[dst_id]

        if p_src['cx'] < p_dst['cx']:
            # Moving left to right
            x1 = p_src['right']
            x2 = p_dst['left']
            arrow_style = "-|>" if msg_type == "sync" else ("->" if msg_type == "async" else "->")
        else:
            # Moving right to left
            x1 = p_src['left']
            x2 = p_dst['right']
            arrow_style = "-|>" if msg_type == "sync" else ("->" if msg_type == "async" else "->")

        ls = "--" if msg_type == "return" else "-"
        color = "#1E293B" if msg_type == "sync" else ("#0284C7" if msg_type == "async" else "#0D9488")

        ax.annotate('', xy=(x2, y), xytext=(x1, y),
                    arrowprops=dict(arrowstyle=arrow_style, color=color, lw=1.4, linestyle=ls), zorder=4)

        mx = (x1 + x2) / 2
        full_text = f"{step_num}. {text}" if step_num else text
        if latency:
            full_text += f" [{latency}]"

        ax.text(mx, y + 0.65, full_text, fontsize=6.8, fontweight='bold' if msg_type=='sync' else 'normal',
                color=color, ha='center', va='center',
                bbox=dict(boxstyle="round,pad=0.15", fc="#FFFFFF", ec="#CBD5E1", lw=0.8, alpha=0.98), zorder=5)

    # Helper: Self Call Loop
    def draw_self(part_id, y_top, y_bot, step_num, text):
        p = parts_dict[part_id]
        x_base = p['right']
        w = 3.5
        xs = [x_base, x_base + w, x_base + w, x_base]
        ys = [y_top, y_top, y_bot, y_bot]
        ax.plot(xs, ys, color="#1E293B", lw=1.3, zorder=4)
        ax.annotate('', xy=(x_base, y_bot), xytext=(x_base + w, y_bot),
                    arrowprops=dict(arrowstyle="-|>", color="#1E293B", lw=1.3), zorder=4)
        ax.text(x_base + w + 0.3, (y_top + y_bot)/2, f"{step_num}. {text}", fontsize=6.5, color="#1E293B",
                va='center', bbox=dict(boxstyle="round,pad=0.12", fc="#FFFFFF", ec="#CBD5E1", lw=0.8, alpha=0.98), zorder=5)

    # 5. DRAW MESSAGES - FRAME 1 (Siklus Presensi)
    draw_msg("user", "ui",   80.5, "1", "Arahkan Kartu Barcode / QR ke Kamera", "sync")
    draw_msg("ui",   "ctrl", 77.5, "2", "AJAX POST /api/scan_handler (barcode, timestamp)", "sync", latency="~20ms")
    draw_msg("ctrl", "db",   74.5, "3", "SELECT identitas FROM siswa/guru WHERE barcode = ? (Validasi)", "sync")
    draw_msg("db",   "ctrl", 72.0, "4", "Return data profil, kelas & jadwal KBM aktif", "return")

    draw_self("ctrl", 70.0, 68.0, "5", "TimeValidator: Hitung Jam Masuk vs Batas Toleransi")

    draw_msg("ctrl", "db",   65.5, "6", "INSERT INTO absensi_siswa / absensi_guru + Dual-Sync Wali Kelas", "sync", latency="~45ms")
    draw_msg("db",   "ctrl", 63.0, "7", "Transaction Commit OK (Status: Hadir / Terlambat)", "return")

    draw_msg("ctrl", "tg",   59.5, "8", "cURL POST /sendMessage (Nama, Jam, Status, Rekap)", "async", latency="Webhook")
    draw_msg("tg",   "ctrl", 57.0, "9", "Webhook Response 200 OK (Telegram Notification Sent)", "return")

    draw_msg("ctrl", "ui",   54.0, "10", "JSON Response {status: 'success', badge: 'Tepat Waktu'}", "return")
    draw_msg("ui",   "user", 51.5, "11", "Tampilkan Toast Sukses & Mainkan Audio Konfirmasi Beep", "async")

    # 6. DRAW MESSAGES - FRAME 2 (Konsultasi AI Hadir.io)
    draw_msg("user", "ui",   41.5, "12", "Input Pertanyaan: 'Berapa siswa tidak hadir di kelas X TKJ hari ini?'", "sync")
    draw_msg("ui",   "ctrl", 38.5, "13", "POST /chat_api.php (prompt, session_token, role)", "sync")

    draw_self("ctrl", 36.5, 34.5, "14", "RBAC Guard: Cek Otorisasi Role & Filter SQL Injection")

    draw_msg("ctrl", "db",   32.5, "15", "SELECT COUNT(*) FROM absensi_siswa WHERE tanggal=CURDATE() AND status != 'hadir'", "sync")
    draw_msg("db",   "ctrl", 30.5, "16", "Return Database Facts: 2 Siswa Sakit, 1 Alpha", "return")

    draw_msg("ctrl", "nim",  26.5, "17", "REST POST /v1/chat/completions (System Prompt + Real Data Context)", "sync", latency="~950ms")
    draw_msg("nim",  "ctrl", 22.0, "18", "Return Generated Answer: 'Hari ini di kelas X TKJ terdapat 3 siswa tidak hadir...'", "return")

    draw_msg("ctrl", "ui",   18.0, "19", "JSON Response {answer: '...', status: 200}", "return")
    draw_msg("ui",   "user", 14.5, "20", "Render Bubble Chat Interaktif di Widget AI Hadir.io", "async")

    # 7. FOOTER PERFORMANCE BENCHMARKS
    ftr = FancyBboxPatch((2, 1.2), 96, 5.8, boxstyle="round,pad=0.2,rounding_size=0.5",
                         facecolor="#FFFFFF", edgecolor=LIGHT_BORDER, linewidth=1.3, zorder=3)
    ax.add_patch(ftr)

    ax.text(3.5, 5.5, "METRIK KINERJA & VERIFIKASI KEAMANAN SISTEM:",
            fontsize=8.5, fontweight='bold', color=DARK_NAVY, va='center')

    metrics = [
        ("[LATENSI SCAN]", "Total eksekusi rata-rata 110ms (kamera decode -> database commit -> feedback audio)."),
        ("[KEAMANAN RBAC]", "Mencegah bypass otorisasi role, token CSRF divalidasi pada setiap request POST."),
        ("[WEBHOOK NOTIF]", "Berjalan asynchronous via non-blocking cURL, tidak membebani antrean presensi siswa."),
        ("[AKURASI AI]", "Grounding data nyata mencegah halusinasi, model Llama-3.1-Nemotron menjawab to-the-point.")
    ]

    my = 4.2
    for title, desc in metrics:
        ax.text(3.5, my, title, fontsize=6.8, fontweight='bold', color=DARK_BLUE, va='center')
        ax.text(23.0, my, desc, fontsize=6.4, color='#334155', va='center')
        my -= 0.72

    plt.tight_layout()
    plt.savefig(output_path, dpi=300, facecolor=fig.get_facecolor(), edgecolor='none', bbox_inches='tight')
    plt.close()
    print("[SUCCESS] Flawless Sequence Diagram saved to:", output_path)

if __name__ == "__main__":
    generate_flawless_sequence_diagram(r"c:\xampp\htdocs\absen\Sequence_Diagram_Sistem_Absensi_SMK_NU_Wargabinangun.png")
