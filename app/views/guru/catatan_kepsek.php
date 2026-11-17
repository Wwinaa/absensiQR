<!-- Header Catatan dari Kepala Sekolah untuk Guru -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold mb-1" style="color: var(--dark-blue);">
            <i class="bi bi-journal-check me-2" style="color: var(--medium-blue);"></i>Catatan dari Kepala Sekolah
        </h1>
        <p class="text-secondary small mb-0">Umpan balik, arahan supervisi akademik, evaluasi kerja, dan apresiasi pembinaan langsung dari Kepala Sekolah.</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 d-flex gap-2">
        <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>
</div>

<?php
    $totalCatatan = count($catatan);
    $totalApresiasi = 0;
    $totalEvaluasi = 0;
    $totalPenting = 0;

    foreach ($catatan as $c) {
        if ($c->urgensi === 'Apresiasi' || $c->kategori === 'Apresiasi') $totalApresiasi++;
        if ($c->urgensi === 'Peringatan' || $c->kategori === 'Kedisiplinan' || $c->kategori === 'Evaluasi Pembelajaran') $totalEvaluasi++;
        if ($c->urgensi === 'Penting') $totalPenting++;
    }
?>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid var(--medium-blue) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase">Total Pesan</span>
                    <h3 class="fw-bold my-1" style="color: var(--dark-blue);"><?= $totalCatatan ?></h3>
                    <div class="small text-muted">Dari Kepala Sekolah</div>
                </div>
                <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-envelope-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #10B981 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase">Apresiasi</span>
                    <h3 class="fw-bold my-1 text-success"><?= $totalApresiasi ?></h3>
                    <div class="small text-muted">Penghargaan Kinerja</div>
                </div>
                <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-star-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #F59E0B !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase">Perhatian / Penting</span>
                    <h3 class="fw-bold my-1 text-warning"><?= $totalPenting ?></h3>
                    <div class="small text-muted">Perlu Ditindaklanjuti</div>
                </div>
                <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-exclamation-circle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #EF4444 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase">Evaluasi</span>
                    <h3 class="fw-bold my-1 text-danger"><?= $totalEvaluasi ?></h3>
                    <div class="small text-muted">Kedisiplinan & Supervisi</div>
                </div>
                <div class="rounded-circle p-2 bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-clipboard-check-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Controls -->
<div class="card card-modern border-0 mb-4 shadow-sm p-3">
    <div class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-color: var(--border-blue);">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="cariCatatan" class="form-control border-start-0" placeholder="Cari judul atau isi pesan kepala sekolah..." style="border-color: var(--border-blue);">
            </div>
        </div>
        <div class="col-md-7 d-flex flex-wrap gap-2 justify-content-md-end">
            <button class="btn btn-sm btn-soft-blue filter-btn active" data-filter="all">Semua</button>
            <button class="btn btn-sm btn-outline-success filter-btn" data-filter="Apresiasi">Apresiasi</button>
            <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="Penting">Penting</button>
            <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="Peringatan">Peringatan</button>
            <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="Kedisiplinan">Kedisiplinan</button>
        </div>
    </div>
</div>

<!-- Daftar Catatan -->
<div class="row" id="daftarCatatanContainer">
    <?php if(!empty($catatan)): ?>
        <?php foreach($catatan as $index => $c): ?>
            <?php 
                $urgensi = $c->urgensi ?? 'Biasa';
                $kategori = $c->kategori ?? 'Umum';
                
                $badgeUrgensi = 'bg-secondary text-white';
                $borderAccent = 'var(--border-blue)';
                $iconHeader = 'bi-chat-left-text';
                
                if ($urgensi === 'Penting') {
                    $badgeUrgensi = 'bg-warning text-dark';
                    $borderAccent = '#F59E0B';
                    $iconHeader = 'bi-exclamation-triangle-fill text-warning';
                } elseif ($urgensi === 'Peringatan') {
                    $badgeUrgensi = 'bg-danger text-white';
                    $borderAccent = '#EF4444';
                    $iconHeader = 'bi-shield-exclamation text-danger';
                } elseif ($urgensi === 'Apresiasi') {
                    $badgeUrgensi = 'bg-success text-white';
                    $borderAccent = '#10B981';
                    $iconHeader = 'bi-award-fill text-success';
                }
                
                $judul = !empty($c->judul) ? $c->judul : 'Catatan Pembinaan Kepala Sekolah';
                $tglFormatted = date('d F Y', strtotime($c->tanggal));
                
                // Escape text for javascript pass
                $isiCatatanRaw = $c->isi_catatan;
            ?>
            <div class="col-md-6 col-lg-6 mb-3 card-item" data-kategori="<?= htmlspecialchars($kategori) ?>" data-urgensi="<?= htmlspecialchars($urgensi) ?>" data-text="<?= htmlspecialchars(strtolower($judul . ' ' . $isiCatatanRaw)) ?>">
                <div class="card card-modern border-0 h-100 shadow-sm p-3 note-card" 
                     style="border-left: 6px solid <?= $borderAccent ?> !important; cursor: pointer; transition: all 0.25s ease;"
                     onclick="bukaDetailCatatan(<?= $index ?>)"
                     title="Klik untuk membaca pesan ini dengan jelas">
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-soft-primary"><i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($kategori) ?></span>
                            <span class="badge <?= $badgeUrgensi ?>"><?= htmlspecialchars($urgensi) ?></span>
                        </div>
                        <small class="text-muted"><?= $tglFormatted ?></small>
                    </div>

                    <h5 class="fw-bold mb-2 text-truncate" style="color: var(--dark-blue); font-size: 1.08rem;">
                        <i class="bi <?= $iconHeader ?> me-1"></i> <?= htmlspecialchars($judul) ?>
                    </h5>

                    <p class="text-secondary small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.6;">
                        <?= htmlspecialchars($isiCatatanRaw) ?>
                    </p>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                        <span class="small text-muted"><i class="bi bi-person-fill text-primary me-1"></i>Oleh: <strong>Kepala Sekolah</strong></span>
                        <button type="button" class="btn btn-sm btn-soft-blue px-3 py-1" style="border-radius: 8px;">
                            <i class="bi bi-envelope-open me-1"></i> Baca Pesan
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card card-modern border-0 text-center py-5 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-inbox fs-1 d-block mb-3" style="color: var(--light-blue);"></i>
                    <h5 class="fw-bold" style="color: var(--dark-blue);">Belum Ada Catatan</h5>
                    <p class="text-secondary small mb-0">Saat ini belum ada catatan pembinaan, supervisi, atau evaluasi yang diberikan oleh Kepala Sekolah untuk Anda.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Baca Catatan Lengkap -->
<div class="modal fade" id="modalDetailCatatanKepsek" tabindex="-1" aria-labelledby="modalDetailCatatanKepsekLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header py-3 px-4 text-white" id="modalHeaderDetail" style="background: linear-gradient(135deg, var(--soft-blue) 0%, var(--medium-blue) 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white rounded-circle p-2 text-primary d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                        <i class="bi bi-award-fill fs-4" id="modalHeaderIcon"></i>
                    </div>
                    <div>
                        <span class="badge bg-white text-dark mb-1" id="modalHeaderBadge">CATATAN KEPALA SEKOLAH</span>
                        <h5 class="modal-title fw-bold mb-0" id="modalDetailCatatanKepsekLabel">Detail Pesan Pembinaan</h5>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 modal-body-detail">
                <!-- Header Info Pengirim & Penerima Memo Resmi -->
                <div class="card border-0 shadow-sm p-3 mb-3 memo-card" style="border-radius: 12px; border-left: 5px solid var(--medium-blue) !important;">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" id="modalKategoriText">Kategori</span>
                                <span class="badge" id="modalUrgensiText">Urgensi</span>
                            </div>
                            <h4 class="fw-bold mb-1" style="color: var(--dark-blue);" id="modalJudulPesan">Judul Catatan</h4>
                            <div class="text-muted small">
                                <span id="modalTanggalPesan">-</span>
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end border-start-md ps-md-3">
                            <div class="small text-secondary mb-0">Pengirim:</div>
                            <div class="fw-bold text-dark"><i class="bi bi-shield-check text-success me-1"></i> Kepala Sekolah</div>
                            <div class="small text-muted">SMK NU WARGABINANGUN</div>
                            <div class="small text-muted mt-1">Penerima: <strong><?= htmlspecialchars($guru->nama_lengkap ?? 'Bapak/Ibu Guru') ?></strong></div>
                        </div>
                    </div>
                </div>

                <!-- Kontainer Kertas Pesan Lengkap -->
                <div class="card border-0 shadow-sm p-4 memo-paper-card" style="border-radius: 16px;">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="bi bi-chat-quote-fill text-primary fs-5"></i>
                        <span class="fw-bold small text-uppercase tracking-wider text-secondary">Isi Arahan / Catatan Lengkap:</span>
                    </div>

                    <div class="p-3 rounded-3 memo-content-box" style="border: 1px solid var(--border-blue);">
                        <p class="mb-0 memo-text" id="modalIsiCatatan" style="font-size: 1.02rem; line-height: 1.8; white-space: pre-wrap; font-family: 'Inter', sans-serif;">
                            -
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center text-muted small">
                        <span><i class="bi bi-info-circle me-1"></i> Pesan resmi internal tenaga pendidik</span>
                        <span>Status: <strong class="text-success"><i class="bi bi-check2-circle me-1"></i>Telah Diterima & Dibaca</strong></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top py-2 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="cetakCatatanAktif()" style="border-radius: 8px;">
                    <i class="bi bi-printer-fill me-1"></i> Cetak Surat Resmi (PDF/Print)
                </button>
                <button type="button" class="btn btn-soft-blue btn-sm px-4" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="bi bi-check-lg me-1"></i> Tutup Pesan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- LEMBAR CETAK FORMAL DOKUMEN RESMI (Hanya dicetak saat tombol cetak ditekan) -->
<!-- ========================================================================= -->
<div id="printableCatatanSheet" style="display: none;">
    <!-- KOP SURAT FORMAL SEKOLAH -->
    <div style="display: flex; align-items: center; justify-content: center; gap: 20px; border-bottom: 3px double #000000; padding-bottom: 12px; margin-bottom: 15px;">
        <img src="<?= base_url('public/assets/images/logo_smk.png') ?>" alt="Logo SMK" style="width: 75px; height: 75px; object-fit: contain;">
        <div style="text-align: center;">
            <h4 style="margin: 0; font-size: 13px; font-weight: bold; text-transform: uppercase;">YAYASAN PENDIDIKAN DAN SOSIAL ISLAM NAHDLATUL ULAMA</h4>
            <h2 style="margin: 2px 0 0; font-size: 20px; font-weight: 800; letter-spacing: 1px;">SMK NU WARGABINANGUN</h2>
            <div style="margin: 2px 0 0; font-size: 11px; font-weight: 600;">NPSN: 69947514 &bull; NSS: 402021703058 &bull; STATUS: TERAKREDITASI</div>
            <div style="margin: 2px 0 0; font-size: 10px; color: #333;">Jl. Raya Wargabinangun No. 01 Kec. Kaliwedi Kab. Cirebon 45165 - Jawa Barat</div>
            <div style="margin: 2px 0 0; font-size: 10px; color: #333;">Email: smknuwargabinangun@gmail.com &bull; Website: smknuwargabinangun.sch.id</div>
        </div>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div style="text-align: center; margin-bottom: 18px;">
        <h3 style="margin: 0; font-size: 14px; font-weight: 800; text-decoration: underline; text-transform: uppercase;">SURAT LEMBAR CATATAN PEMBINAAN & SUPERVISI GURU</h3>
        <p style="margin: 3px 0 0; font-size: 11px; color: #444;">Nomor Dokumen Internal: 421.5 / Kepsek-Pemb / <?= date('m/Y') ?></p>
    </div>

    <!-- IDENTITAS PENERIMA DAN METADATA -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 11px;">
        <tr>
            <td style="width: 135px; padding: 4px 0; font-weight: bold;">Nama Tenaga Pendidik</td>
            <td style="width: 10px; padding: 4px 0;">:</td>
            <td style="padding: 4px 0;"><strong id="printNamaGuru"><?= htmlspecialchars($guru->nama_lengkap ?? '-') ?></strong></td>
            <td style="width: 110px; padding: 4px 0; font-weight: bold;">Tanggal Catatan</td>
            <td style="width: 10px; padding: 4px 0;">:</td>
            <td style="padding: 4px 0;" id="printTanggal">-</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-weight: bold;">NIP / ID Guru</td>
            <td style="padding: 4px 0;">:</td>
            <td style="padding: 4px 0;" id="printNip"><?= htmlspecialchars($guru->nip ?? '-') ?></td>
            <td style="padding: 4px 0; font-weight: bold;">Sifat / Urgensi</td>
            <td style="padding: 4px 0;">:</td>
            <td style="padding: 4px 0;" id="printUrgensi">-</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-weight: bold;">Kategori Arahan</td>
            <td style="padding: 4px 0;">:</td>
            <td style="padding: 4px 0;" id="printKategori">-</td>
            <td style="padding: 4px 0; font-weight: bold;">Pemberi Arahan</td>
            <td style="padding: 4px 0;">:</td>
            <td style="padding: 4px 0;">Kepala Sekolah</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-weight: bold;">Perihal / Topik</td>
            <td style="padding: 4px 0;">:</td>
            <td colspan="4" style="padding: 4px 0;"><strong id="printJudul">-</strong></td>
        </tr>
    </table>

    <!-- KOTAK ISI CATATAN RESMI -->
    <div style="border: 1px solid #000000; padding: 15px; margin-bottom: 25px; min-height: 220px; background-color: #fafafa;">
        <div style="font-weight: bold; font-size: 11.5px; border-bottom: 1px dashed #000000; padding-bottom: 6px; margin-bottom: 10px; text-transform: uppercase;">
            ISI ARAHAN / CATATAN EVALUASI RESMI KEPALA SEKOLAH:
        </div>
        <div id="printIsi" style="font-size: 11.5px; line-height: 1.8; white-space: pre-wrap; font-family: Arial, Helvetica, sans-serif;">
            -
        </div>
    </div>

    <!-- TANDA TANGAN DUA PIHAK -->
    <div style="width: 100%; margin-top: 30px; font-size: 11px;">
        <div style="float: left; width: 220px; text-align: center;">
            <div>Mengetahui / Menerima,</div>
            <div style="font-weight: bold;">Tenaga Pendidik / Guru,</div>
            <div style="height: 60px;"></div>
            <div style="font-weight: bold; text-decoration: underline;"><?= htmlspecialchars($guru->nama_lengkap ?? 'Guru') ?></div>
            <div>NIP. <?= htmlspecialchars($guru->nip ?? '-') ?></div>
        </div>
        <div style="float: right; width: 250px; text-align: center;">
            <div>Cirebon, <?= date('d F Y') ?></div>
            <div style="font-weight: bold;">Kepala SMK NU WARGABINANGUN,</div>
            <div style="height: 60px;"></div>
            <div style="font-weight: bold; text-decoration: underline;">Drs. H. Ahmad Fauzi, M.Pd</div>
            <div>NIP. 19750312 200003 1 004</div>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>

<style>
    .modal-body-detail {
        background-color: #F8FAFC;
    }
    .memo-card, .memo-paper-card {
        background-color: #FFFFFF;
    }
    .memo-content-box {
        background-color: #F8FAFC;
    }
    .memo-text {
        color: #1E293B;
    }
    .note-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(79, 143, 239, 0.15) !important;
        border-color: var(--soft-blue) !important;
    }

    /* Dark Mode Styling for Catatan Kepsek */
    [data-bs-theme="dark"] .modal-body-detail {
        background-color: #0F172A !important;
    }
    [data-bs-theme="dark"] .memo-card,
    [data-bs-theme="dark"] .memo-paper-card {
        background-color: #1E293B !important;
        border: 1px solid #334155 !important;
        color: #F1F5F9 !important;
    }
    [data-bs-theme="dark"] .memo-content-box {
        background-color: #0F172A !important;
        border: 1px solid #334155 !important;
    }
    [data-bs-theme="dark"] .memo-text {
        color: #F1F5F9 !important;
    }
    [data-bs-theme="dark"] #modalDetailCatatanKepsek .modal-footer {
        background-color: #1E293B !important;
        border-color: #334155 !important;
    }
    [data-bs-theme="dark"] #modalDetailCatatanKepsek .text-dark {
        color: #F1F5F9 !important;
    }
    [data-bs-theme="dark"] #modalDetailCatatanKepsek .text-muted,
    [data-bs-theme="dark"] #modalDetailCatatanKepsek .text-secondary {
        color: #94A3B8 !important;
    }
    [data-bs-theme="dark"] .input-group-text.bg-white {
        background-color: #0F172A !important;
        border-color: #334155 !important;
        color: #94A3B8 !important;
    }

    /* Print styling formal untuk lembar pembinaan */
    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        body * {
            visibility: hidden !important;
        }
        #printableCatatanSheet, #printableCatatanSheet * {
            visibility: visible !important;
        }
        #printableCatatanSheet {
            display: block !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .navbar, nav, .sidebar, .btn, .btn-toolbar, .no-print, header, footer, .modal, .modal-backdrop, .offcanvas, .chatbot-launcher, .chatbot-window {
            display: none !important;
        }
    }
</style>

<script>
    const dataCatatanList = <?= json_encode($catatan ?? []) ?>;
    let currentCatatanIndex = 0;

    function bukaDetailCatatan(index) {
        currentCatatanIndex = index;
        const item = dataCatatanList[index];
        if (!item) return;

        const judul = item.judul && item.judul.trim() !== '' ? item.judul : 'Catatan Pembinaan Kepala Sekolah';
        const kategori = item.kategori || 'Umum';
        const urgensi = item.urgensi || 'Biasa';
        const isi = item.isi_catatan || 'Tidak ada rincian pesan.';
        
        let tglStr = item.tanggal;
        try {
            const d = new Date(item.tanggal);
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            tglStr = d.toLocaleDateString('id-ID', options);
        } catch(e) {}

        document.getElementById('modalJudulPesan').innerText = judul;
        document.getElementById('modalTanggalPesan').innerText = tglStr;
        document.getElementById('modalKategoriText').innerText = kategori;
        document.getElementById('modalIsiCatatan').innerText = isi;

        const urgensiBadge = document.getElementById('modalUrgensiText');
        const headerEl = document.getElementById('modalHeaderDetail');
        const headerIcon = document.getElementById('modalHeaderIcon');

        urgensiBadge.className = 'badge';
        if (urgensi === 'Penting') {
            urgensiBadge.classList.add('bg-warning', 'text-dark');
            urgensiBadge.innerText = 'Penting';
            headerEl.style.background = 'linear-gradient(135deg, #D97706 0%, #F59E0B 100%)';
            headerIcon.className = 'bi bi-exclamation-triangle-fill fs-4 text-warning';
        } else if (urgensi === 'Peringatan') {
            urgensiBadge.classList.add('bg-danger', 'text-white');
            urgensiBadge.innerText = 'Peringatan';
            headerEl.style.background = 'linear-gradient(135deg, #DC2626 0%, #EF4444 100%)';
            headerIcon.className = 'bi bi-shield-exclamation fs-4 text-danger';
        } else if (urgensi === 'Apresiasi') {
            urgensiBadge.classList.add('bg-success', 'text-white');
            urgensiBadge.innerText = 'Apresiasi';
            headerEl.style.background = 'linear-gradient(135deg, #059669 0%, #10B981 100%)';
            headerIcon.className = 'bi bi-award-fill fs-4 text-success';
        } else {
            urgensiBadge.classList.add('bg-primary', 'text-white');
            urgensiBadge.innerText = 'Biasa';
            headerEl.style.background = 'linear-gradient(135deg, var(--soft-blue) 0%, var(--medium-blue) 100%)';
            headerIcon.className = 'bi bi-chat-left-text-fill fs-4 text-primary';
        }

        const modal = new bootstrap.Modal(document.getElementById('modalDetailCatatanKepsek'));
        modal.show();
    }

    function cetakCatatanAktif() {
        const item = dataCatatanList[currentCatatanIndex];
        if (!item) return;

        const judul = item.judul && item.judul.trim() !== '' ? item.judul : 'Catatan Pembinaan Kepala Sekolah';
        const kategori = item.kategori || 'Umum';
        const urgensi = item.urgensi || 'Biasa';
        const isi = item.isi_catatan || 'Tidak ada rincian pesan.';
        
        let tglStr = item.tanggal;
        try {
            const d = new Date(item.tanggal);
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            tglStr = d.toLocaleDateString('id-ID', options);
        } catch(e) {}

        document.getElementById('printJudul').innerText = judul;
        document.getElementById('printTanggal').innerText = tglStr;
        document.getElementById('printKategori').innerText = kategori;
        document.getElementById('printUrgensi').innerText = urgensi;
        document.getElementById('printIsi').innerText = isi;

        // Tutup modal sejenak agar background backdrop tidak mengganggu render print browser
        const modalEl = document.getElementById('modalDetailCatatanKepsek');
        const modalInst = bootstrap.Modal.getInstance(modalEl);
        if (modalInst) {
            modalInst.hide();
        }

        setTimeout(() => {
            window.print();
        }, 350);
    }

    // Filter and search live
    document.addEventListener('DOMContentLoaded', () => {
        const cariInput = document.getElementById('cariCatatan');
        const filterBtns = document.querySelectorAll('.filter-btn');
        const cardItems = document.querySelectorAll('.card-item');

        let activeFilter = 'all';

        function applyFilter() {
            const query = (cariInput ? cariInput.value : '').toLowerCase().trim();

            cardItems.forEach(item => {
                const text = item.getAttribute('data-text') || '';
                const kategori = item.getAttribute('data-kategori') || '';
                const urgensi = item.getAttribute('data-urgensi') || '';

                const matchesQuery = (query === '' || text.includes(query));
                let matchesFilter = false;

                if (activeFilter === 'all') {
                    matchesFilter = true;
                } else if (activeFilter === 'Apresiasi') {
                    matchesFilter = (urgensi === 'Apresiasi' || kategori === 'Apresiasi');
                } else if (activeFilter === 'Penting') {
                    matchesFilter = (urgensi === 'Penting');
                } else if (activeFilter === 'Peringatan') {
                    matchesFilter = (urgensi === 'Peringatan');
                } else if (activeFilter === 'Kedisiplinan') {
                    matchesFilter = (kategori === 'Kedisiplinan');
                }

                if (matchesQuery && matchesFilter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        if (cariInput) {
            cariInput.addEventListener('input', applyFilter);
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeFilter = btn.getAttribute('data-filter');
                applyFilter();
            });
        });
    });
</script>
