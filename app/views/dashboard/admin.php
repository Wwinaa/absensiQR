<?php
// We will use the main layout for dashboards
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold mb-1" style="color: var(--dark-blue);">Dashboard <?= isset($role_name) ? htmlspecialchars($role_name) : 'Administrator' ?></h1>
        <p class="text-secondary small mb-0">Panel Kontrol & Manajemen Sistem Absensi Digital SMK NU WARGABINANGUN</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center gap-2">
        <div class="live-clock-widget d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 border shadow-sm" style="background: var(--very-light-blue); border-color: var(--border-blue) !important;">
            <div class="d-flex align-items-center gap-1">
                <i class="bi bi-calendar3 text-primary"></i>
                <span class="live-clock-date fw-semibold small text-secondary">Memuat tanggal...</span>
            </div>
            <span class="text-muted small">|</span>
            <div class="d-flex align-items-center gap-1">
                <i class="bi bi-clock-fill text-primary"></i>
                <span class="live-clock-time fw-bold small text-dark font-monospace">--:--:-- WIB</span>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-dismissible fade show text-start border-0 shadow-sm badge-soft-success mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> Selamat datang, <strong><?= htmlspecialchars($_SESSION['user_username']) ?></strong>.
</div>

<!-- Master Data Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-6 mb-3">
        <div class="card card-modern border-0 text-center h-100">
            <div class="card-body">
                <i class="bi bi-people fs-1" style="color: var(--medium-blue);"></i>
                <h6 class="text-muted mt-2 mb-1">Total Siswa</h6>
                <h3 class="fw-bold mb-0" style="color: var(--dark-blue);"><?= $stats['total_siswa'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card card-modern border-0 text-center h-100">
            <div class="card-body">
                <i class="bi bi-person-badge fs-1" style="color: var(--medium-blue);"></i>
                <h6 class="text-muted mt-2 mb-1">Total Guru</h6>
                <h3 class="fw-bold mb-0" style="color: var(--dark-blue);"><?= $stats['total_guru'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card card-modern border-0 text-center h-100">
            <div class="card-body">
                <i class="bi bi-building fs-1" style="color: var(--medium-blue);"></i>
                <h6 class="text-muted mt-2 mb-1">Total Kelas</h6>
                <h3 class="fw-bold mb-0" style="color: var(--dark-blue);"><?= $stats['total_kelas'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card card-modern border-0 text-center h-100">
            <div class="card-body">
                <i class="bi bi-book fs-1" style="color: var(--medium-blue);"></i>
                <h6 class="text-muted mt-2 mb-1">Total Jurusan</h6>
                <h3 class="fw-bold mb-0" style="color: var(--dark-blue);"><?= $stats['total_jurusan'] ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0" style="color: var(--dark-blue);">Kehadiran Hari Ini</h4>
    <small class="text-muted"><i class="bi bi-cursor-fill text-primary me-1"></i>Klik kartu status untuk melihat daftar nama, jam scan masuk & pulang, serta bukti foto</small>
</div>

<style>
.status-card-clickable {
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}
.status-card-clickable:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(79, 143, 239, 0.18) !important;
    border-color: var(--medium-blue) !important;
}
.click-hint {
    font-size: 0.68rem;
    opacity: 0.85;
}
</style>

<div class="row mb-4">
    <!-- Status Siswa -->
    <div class="col-md-6 mb-3">
        <div class="card card-modern border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <h5 class="card-title fw-bold mb-0" style="color: var(--dark-blue);">
                        <i class="bi bi-person-lines-fill me-2" style="color: var(--medium-blue);"></i>Status Siswa Hari Ini
                    </h5>
                    <span class="badge bg-light text-secondary border">Total: <?= $stats['total_siswa'] ?></span>
                </div>
                <div class="row text-center mt-3 g-2">
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100 status-card-clickable" onclick="bukaModalKehadiran('siswa', 'hadir')">
                            <div class="small text-muted fw-bold mb-1">Hadir</div>
                            <div class="fs-4 fw-bold text-success"><?= $stats['siswa_hadir'] ?></div>
                            <div class="click-hint text-success mt-1"><i class="bi bi-eye"></i> Lihat Data</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100 status-card-clickable" onclick="bukaModalKehadiran('siswa', 'izin')">
                            <div class="small text-muted fw-bold mb-1">Izin</div>
                            <div class="fs-4 fw-bold text-info"><?= $stats['siswa_izin'] ?></div>
                            <div class="click-hint text-info mt-1"><i class="bi bi-eye"></i> Lihat Data</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100 status-card-clickable" onclick="bukaModalKehadiran('siswa', 'sakit')">
                            <div class="small text-muted fw-bold mb-1">Sakit</div>
                            <div class="fs-4 fw-bold text-primary"><?= $stats['siswa_sakit'] ?></div>
                            <div class="click-hint text-primary mt-1"><i class="bi bi-eye"></i> Lihat Data</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100 status-card-clickable" onclick="bukaModalKehadiran('siswa', 'alpa')">
                            <div class="small text-muted fw-bold mb-1">Alpa</div>
                            <div class="fs-4 fw-bold text-danger"><?= $stats['siswa_alpa'] ?></div>
                            <div class="click-hint text-danger mt-1"><i class="bi bi-eye"></i> Lihat Data</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Guru -->
    <div class="col-md-6 mb-3">
        <div class="card card-modern border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <h5 class="card-title fw-bold mb-0" style="color: var(--dark-blue);">
                        <i class="bi bi-person-badge-fill me-2" style="color: var(--medium-blue);"></i>Status Guru Hari Ini
                    </h5>
                    <span class="badge bg-light text-secondary border">Total: <?= $stats['total_guru'] ?></span>
                </div>
                <div class="row text-center mt-3 g-2">
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100 status-card-clickable" onclick="bukaModalKehadiran('guru', 'hadir')">
                            <div class="small text-muted fw-bold mb-1">Hadir</div>
                            <div class="fs-4 fw-bold text-success"><?= $stats['guru_hadir'] ?></div>
                            <div class="click-hint text-success mt-1"><i class="bi bi-eye"></i> Lihat Data</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100 status-card-clickable" onclick="bukaModalKehadiran('guru', 'izin')">
                            <div class="small text-muted fw-bold mb-1">Izin</div>
                            <div class="fs-4 fw-bold text-info"><?= $stats['guru_izin'] ?></div>
                            <div class="click-hint text-info mt-1"><i class="bi bi-eye"></i> Lihat Data</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100 status-card-clickable" onclick="bukaModalKehadiran('guru', 'sakit')">
                            <div class="small text-muted fw-bold mb-1">Sakit</div>
                            <div class="fs-4 fw-bold text-primary"><?= $stats['guru_sakit'] ?></div>
                            <div class="click-hint text-primary mt-1"><i class="bi bi-eye"></i> Lihat Data</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light h-100 status-card-clickable" onclick="bukaModalKehadiran('guru', 'alpa')">
                            <div class="small text-muted fw-bold mb-1">Alpa</div>
                            <div class="fs-4 fw-bold text-danger"><?= $stats['guru_alpa'] ?></div>
                            <div class="click-hint text-danger mt-1"><i class="bi bi-eye"></i> Lihat Data</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Kehadiran -->
<div class="modal fade" id="modalDetailKehadiran" tabindex="-1" aria-labelledby="modalDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white" id="modalDetailHeader" style="background: var(--dark-blue); border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold" id="modalDetailTitle">
                    <i class="bi bi-card-checklist me-2"></i>Daftar Kehadiran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: var(--bg-color);">
                <div id="modalDetailContent">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
            <div class="modal-footer bg-white border-0 py-3 d-flex justify-content-between">
                <span class="text-muted small" id="modalDetailCount">Menampilkan 0 data</span>
                <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="bi bi-x-circle me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lihat Bukti Foto & Alasan -->
<div class="modal fade" id="modalBuktiDashboard" tabindex="-1" aria-labelledby="modalBuktiTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header" style="background-color: var(--very-light-blue); border-bottom: 1px solid var(--border-blue); border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold" id="modalBuktiTitle" style="color: var(--dark-blue);">
                    <i class="bi bi-file-earmark-image me-2 text-primary"></i>Bukti Pengajuan & Keterangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-start p-3 rounded" style="background: var(--very-light-blue); border: 1px solid var(--border-blue);">
                    <div class="fw-bold mb-1" id="buktiNamaPemohon" style="color: var(--dark-blue);">-</div>
                    <div class="text-secondary small mb-1" id="buktiInfoSub">-</div>
                    <div class="small mt-2 p-2 bg-white rounded border">
                        <strong>Alasan / Keterangan:</strong><br>
                        <span id="buktiAlasanText">-</span>
                    </div>
                </div>
                <div id="buktiFotoContainer" class="text-center">
                    <img id="buktiFotoImg" src="" alt="Bukti Foto" class="img-fluid rounded shadow-sm" style="max-height: 480px; object-fit: contain; width: 100%; border: 1px solid #CBD5E1;">
                </div>
            </div>
            <div class="modal-footer bg-white border-0 pt-0 pb-3 d-flex justify-content-end">
                <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="bi bi-x-circle me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-modern border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Tren Kehadiran (7 Hari Terakhir)</h5>
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const detailData = <?= json_encode([
        'siswa_hadir' => $stats['detail_siswa_hadir'] ?? [],
        'siswa_izin'  => $stats['detail_siswa_izin'] ?? [],
        'siswa_sakit' => $stats['detail_siswa_sakit'] ?? [],
        'siswa_alpa'  => $stats['detail_siswa_alpa'] ?? [],
        'guru_hadir'  => $stats['detail_guru_hadir'] ?? [],
        'guru_izin'   => $stats['detail_guru_izin'] ?? [],
        'guru_sakit'  => $stats['detail_guru_sakit'] ?? [],
        'guru_alpa'   => $stats['detail_guru_alpa'] ?? [],
    ]) ?>;

    const baseUrl = '<?= base_url() ?>';

    function bukaModalKehadiran(tipe, status) {
        const key = tipe + '_' + status;
        const list = detailData[key] || [];
        const isSiswa = (tipe === 'siswa');
        
        const headerEl = document.getElementById('modalDetailHeader');
        const titleEl = document.getElementById('modalDetailTitle');
        const countEl = document.getElementById('modalDetailCount');
        const contentEl = document.getElementById('modalDetailContent');

        let statusText = '';
        let headerBg = 'var(--dark-blue)';
        let statusBadge = '';

        if (status === 'hadir') {
            statusText = 'Hadir';
            headerBg = 'linear-gradient(135deg, #059669 0%, #10B981 100%)';
            statusBadge = '<span class="badge bg-success">Hadir</span>';
        } else if (status === 'izin') {
            statusText = 'Izin';
            headerBg = 'linear-gradient(135deg, #0284C7 0%, #38BDF8 100%)';
            statusBadge = '<span class="badge bg-info">Izin</span>';
        } else if (status === 'sakit') {
            statusText = 'Sakit';
            headerBg = 'linear-gradient(135deg, #2563EB 0%, #60A5FA 100%)';
            statusBadge = '<span class="badge bg-primary">Sakit</span>';
        } else if (status === 'alpa') {
            statusText = 'Alpa';
            headerBg = 'linear-gradient(135deg, #DC2626 0%, #EF4444 100%)';
            statusBadge = '<span class="badge bg-danger">Alpa</span>';
        }

        headerEl.style.background = headerBg;
        titleEl.innerHTML = `<i class="bi bi-people-fill me-2"></i>Daftar ${isSiswa ? 'Siswa' : 'Guru'} (${statusText}) — Hari Ini`;
        countEl.innerText = `Total: ${list.length} ${isSiswa ? 'Siswa' : 'Guru'}`;

        if (list.length === 0) {
            contentEl.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 d-block text-muted mb-2"></i>
                    <p class="text-muted fw-semibold">Tidak ada ${isSiswa ? 'siswa' : 'guru'} dengan status ${statusText} hari ini.</p>
                </div>
            `;
        } else {
            let tableHtml = `
                <div class="table-responsive bg-white rounded shadow-sm border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Nama Lengkap</th>
                                ${isSiswa ? '<th>Kelas</th><th>NIS / NISN</th>' : '<th>NIP</th>'}
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status / Keterangan</th>
                                ${(status === 'izin' || status === 'sakit') ? '<th class="text-center">Bukti & Alasan</th>' : ''}
                            </tr>
                        </thead>
                        <tbody>
            `;

            list.forEach((item, index) => {
                const nama = item.nama_lengkap || '-';
                const subInfo = isSiswa ? `Kelas: ${item.nama_kelas || '-'} | NIS: ${item.nis || '-'}` : `NIP: ${item.nip || '-'}`;
                
                let jamMasuk = '';
                if (status === 'alpa') {
                    jamMasuk = `<span class="text-muted fst-italic">-</span>`;
                } else if (item.jam_masuk && item.jam_masuk !== '00:00:00') {
                    jamMasuk = `<span class="badge bg-light text-dark border"><i class="bi bi-clock me-1 text-primary"></i>${item.jam_masuk.substring(0, 5)} WIB</span>`;
                } else {
                    jamMasuk = `<span class="text-muted fst-italic">-</span>`;
                }
                
                let jamPulang = '';
                if (status === 'alpa') {
                    jamPulang = `<span class="text-muted fst-italic">-</span>`;
                } else if (item.jam_pulang && item.jam_pulang !== '00:00:00') {
                    jamPulang = `<span class="badge bg-light text-success border"><i class="bi bi-box-arrow-right me-1"></i>${item.jam_pulang.substring(0, 5)} WIB</span>`;
                } else {
                    if (status === 'hadir') {
                        jamPulang = `<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Belum Scan Pulang</span>`;
                    } else {
                        jamPulang = `<span class="text-muted fst-italic">-</span>`;
                    }
                }

                let ketBadge = '';
                if (status === 'alpa') {
                    ketBadge = `<span class="badge bg-light text-secondary border">tidak ada keterangan</span>`;
                } else if (item.keterangan && item.keterangan.includes('Bolos')) {
                    ketBadge = `<span class="badge bg-light text-secondary border">tidak ada keterangan</span>`;
                } else if (item.keterangan) {
                    ketBadge = `<span class="badge bg-light text-secondary border">${item.keterangan}</span>`;
                } else {
                    ketBadge = statusBadge;
                }

                let aksiBukti = '';
                if (status === 'izin' || status === 'sakit') {
                    const alasanEsc = (item.alasan || item.keterangan || 'Tidak ada keterangan').replace(/"/g, '&quot;');
                    const buktiFile = item.bukti_foto || '';
                    aksiBukti = `
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;"
                                onclick="lihatBuktiDashboard('${nama.replace(/'/g, "\\'")}', '${subInfo.replace(/'/g, "\\'")}', '${statusText}', '${alasanEsc.replace(/'/g, "\\'")}', '${buktiFile}')">
                                <i class="bi bi-image me-1"></i>Lihat Bukti
                            </button>
                        </td>
                    `;
                }

                tableHtml += `
                    <tr>
                        <td class="text-center fw-bold text-muted">${index + 1}</td>
                        <td class="fw-semibold text-dark">${nama}</td>
                        ${isSiswa ? `<td><span class="badge bg-light text-primary border">${item.nama_kelas || '-'}</span></td><td><small class="text-secondary">${item.nis || '-'}${item.nisn ? ' / ' + item.nisn : ''}</small></td>` : `<td><small class="text-secondary">${item.nip || '-'}</small></td>`}
                        <td>${jamMasuk}</td>
                        <td>${jamPulang}</td>
                        <td>${ketBadge}</td>
                        ${aksiBukti}
                    </tr>
                `;
            });

            tableHtml += `</tbody></table></div>`;
            contentEl.innerHTML = tableHtml;
        }

        const modalEl = new bootstrap.Modal(document.getElementById('modalDetailKehadiran'));
        modalEl.show();
    }

    function lihatBuktiDashboard(nama, subInfo, jenis, alasan, fileFoto) {
        document.getElementById('buktiNamaPemohon').innerText = nama;
        document.getElementById('buktiInfoSub').innerText = subInfo;
        document.getElementById('buktiAlasanText').innerText = alasan || 'Tidak ada alasan tertulis.';
        
        const container = document.getElementById('buktiFotoContainer');
        if (fileFoto && fileFoto.trim() !== '') {
            const url = baseUrl + 'public/uploads/pengajuan/' + fileFoto;
            const ext = fileFoto.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
                container.innerHTML = `<img src="${url}" alt="Bukti Foto" class="img-fluid rounded shadow-sm" style="max-height: 480px; object-fit: contain; width: 100%; border: 1px solid #CBD5E1;">`;
            } else if (ext === 'pdf') {
                container.innerHTML = `<iframe src="${url}" style="width: 100%; height: 500px; border: 1px solid #CBD5E1; border-radius: 8px;"></iframe>`;
            } else {
                container.innerHTML = `
                    <div class="p-4 bg-light rounded text-center">
                        <i class="bi bi-file-earmark-text fs-1 text-primary d-block mb-2"></i>
                        <span class="fw-semibold d-block mb-2 text-dark">File: ${fileFoto}</span>
                        <a href="${url}" download class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download me-1"></i>Unduh File (${ext.toUpperCase()})
                        </a>
                    </div>
                `;
            }
        } else {
            container.innerHTML = `
                <div class="alert alert-secondary text-center small mb-0">
                    <i class="bi bi-info-circle me-1"></i>Tidak ada lampiran foto bukti.
                </div>
            `;
        }

        const modalBukti = new bootstrap.Modal(document.getElementById('modalBuktiDashboard'));
        modalBukti.show();
    }

    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('trendChart').getContext('2d');
        const trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($stats['trend_labels']) ?>,
                datasets: [
                    {
                        label: 'Siswa (Hadir)',
                        data: <?= json_encode($stats['trend_siswa']) ?>,
                        borderColor: '#2962ff',
                        backgroundColor: 'rgba(41, 98, 255, 0.1)',
                        tension: 0.3,
                        fill: true,
                        borderWidth: 2
                    },
                    {
                        label: 'Guru (Hadir)',
                        data: <?= json_encode($stats['trend_guru']) ?>,
                        borderColor: '#00c853',
                        backgroundColor: 'rgba(0, 200, 83, 0.1)',
                        tension: 0.3,
                        fill: true,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
