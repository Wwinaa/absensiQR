<!-- ============================================================
     Admin — Kelola Pengajuan Izin / Sakit
     ============================================================ -->

<style>
.badge-menunggu  { background:#FEF3C7; color:#92400E; border:1px solid #FCD34D; }
.badge-disetujui { background:#D1FAE5; color:#065F46; border:1px solid #6EE7B7; }
.badge-ditolak   { background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; }
</style>

<!-- Header -->
<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color:var(--border-blue)!important;">
    <h1 class="h2 fw-bold" style="color:var(--dark-blue);">
        <i class="bi bi-envelope-check me-2" style="color:var(--medium-blue);"></i>
        Kelola Pengajuan Izin / Sakit
    </h1>
</div>

<!-- Filter -->
<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/pengajuan_izin') ?>" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-secondary">Pemohon</label>
                <select name="tipe_pemohon" class="form-select form-select-sm">
                    <option value="">Semua (Guru & Siswa)</option>
                    <option value="siswa" <?= ($filters['tipe_pemohon'] ?? '') === 'siswa' ? 'selected' : '' ?>>Hanya Siswa</option>
                    <option value="guru"  <?= ($filters['tipe_pemohon'] ?? '') === 'guru'  ? 'selected' : '' ?>>Hanya Guru</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary">Jenis</label>
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="Izin"  <?= $filters['jenis'] === 'Izin'  ? 'selected' : '' ?>>Izin</option>
                    <option value="Sakit" <?= $filters['jenis'] === 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary">Kelas (Siswa)</label>
                <select name="kelas_id" class="form-select form-select-sm">
                    <option value="">Semua Kelas</option>
                    <?php foreach ($kelasList as $k): ?>
                        <option value="<?= $k->id ?>" <?= $filters['kelas_id'] == $k->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k->nama_kelas) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary">Nama / NIS / NIP</label>
                <input type="text" name="nama" class="form-control form-control-sm"
                       placeholder="Cari pemohon..." value="<?= htmlspecialchars($filters['nama']) ?>">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1" style="background:var(--medium-blue); border-color:var(--medium-blue);">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="<?= base_url('admin/pengajuan_izin') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel -->
<div class="card card-modern border-0">
    <div class="card-body p-0">
        <div class="table-responsive table-modern-container">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Pemohon</th>
                        <th>Peran / Info</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th class="text-center">Bukti Foto</th>
                        <th>Status</th>
                        <th>Dikirim</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($list)): ?>
                        <?php $no = ($page - 1) * 15 + 1; foreach ($list as $p): ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge" style="background:var(--very-light-blue); color:var(--medium-blue);"><?= $no++ ?></span>
                            </td>
                            <td>
                                <div class="fw-semibold" style="color:var(--dark-blue);"><?= htmlspecialchars($p->nama_lengkap) ?></div>
                                <?php if (($p->tipe_pemohon ?? 'siswa') === 'guru'): ?>
                                    <small class="text-muted"><i class="bi bi-person-vcard me-1"></i>NIP: <strong><?= htmlspecialchars($p->nip ?? '-') ?></strong></small>
                                <?php else: ?>
                                    <small class="text-muted">
                                        <i class="bi bi-person-vcard me-1"></i>NIS: <strong><?= htmlspecialchars($p->nis ?? '-') ?></strong>
                                        <?php if (!empty($p->nisn)): ?>
                                            &bull; NISN: <strong><?= htmlspecialchars($p->nisn) ?></strong>
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($p->tipe_pemohon ?? 'siswa') === 'guru'): ?>
                                    <span class="badge" style="background:#EDE9FE; color:#5B21B6; border:1px solid #DDD6FE; padding:.35em .7em; border-radius:8px;">
                                        <i class="bi bi-person-badge me-1"></i>Guru
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background:#E0F2FE; color:#0369A1; border:1px solid #BAE6FD; padding:.35em .7em; border-radius:8px;">
                                        <i class="bi bi-person me-1"></i><?= htmlspecialchars($p->nama_kelas ?? 'Siswa') ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p->jenis === 'Izin'): ?>
                                    <span class="badge" style="background:#E0F2FE; color:#0369A1; border:1px solid #7DD3FC; padding:.4em .7em; border-radius:8px;">
                                        <i class="bi bi-calendar-x me-1"></i>Izin
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background:#FDF4FF; color:#7E22CE; border:1px solid #D8B4FE; padding:.4em .7em; border-radius:8px;">
                                        <i class="bi bi-heart-pulse me-1"></i>Sakit
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold"><?= date('d-m-Y', strtotime($p->tanggal)) ?></td>
                            <td style="max-width:180px;">
                                <span class="text-truncate d-block" style="max-width:160px;" title="<?= htmlspecialchars($p->alasan) ?>">
                                    <?= htmlspecialchars($p->alasan) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php
                                    $subInfo = (($p->tipe_pemohon ?? 'siswa') === 'guru') ? 'Guru (NIP: ' . ($p->nip ?? '-') . ')' : 'Siswa (Kelas: ' . ($p->nama_kelas ?? '-') . ')';
                                    $namaEsc = htmlspecialchars(addslashes($p->nama_lengkap));
                                    $subEsc  = htmlspecialchars(addslashes($subInfo));
                                    $alasanEsc = htmlspecialchars(addslashes($p->alasan));
                                ?>
                                <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius:8px;"
                                        onclick="lihatBuktiAdmin('<?= $namaEsc ?>', '<?= $subEsc ?>', '<?= $p->jenis ?>', '<?= date('d-m-Y', strtotime($p->tanggal)) ?>', '<?= $alasanEsc ?>', '<?= $p->bukti_foto ?>')">
                                    <i class="bi bi-image me-1"></i>Lihat Bukti
                                </button>
                            </td>
                            <td>
                                <span class="badge bg-success" style="padding:.4em .7em; border-radius:8px;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Tercatat
                                </span>
                            </td>
                            <td class="text-secondary small"><?= date('d/m H:i', strtotime($p->created_at)) ?></td>
                            <td>
                                <span class="badge bg-light text-success border px-2 py-1"><i class="bi bi-check2-circle me-1"></i>Otomatis Sah</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2" style="color:var(--light-blue);"></i>
                                Tidak ada pengajuan izin atau sakit yang ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="p-3 border-top" style="border-color:var(--border-blue)!important;">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="<?= base_url('admin/pengajuan_izin') ?>?page=<?= $i ?>&jenis=<?= urlencode($filters['jenis']) ?>&tipe_pemohon=<?= urlencode($filters['tipe_pemohon'] ?? '') ?>&kelas_id=<?= urlencode($filters['kelas_id'] ?? '') ?>&nama=<?= urlencode($filters['nama'] ?? '') ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Lihat Bukti Foto & Alasan (Admin) -->
<div class="modal fade" id="modalLihatBuktiAdmin" tabindex="-1" aria-labelledby="modalLihatBuktiTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header" style="background-color: var(--very-light-blue); border-bottom: 1px solid var(--border-blue); border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold" id="modalLihatBuktiTitle" style="color: var(--dark-blue);">
                    <i class="bi bi-file-earmark-image me-2 text-primary"></i>Bukti Foto Surat Pengajuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-start p-3 rounded" style="background: var(--very-light-blue); border: 1px solid var(--border-blue);">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold fs-6" id="buktiAdminNama" style="color: var(--dark-blue);">-</span>
                        <span class="badge bg-primary" id="buktiAdminJenis">-</span>
                    </div>
                    <div class="text-secondary small mb-2" id="buktiAdminSub">-</div>
                    <div class="small p-2 bg-white rounded border">
                        <strong class="text-dark">Alasan Pengajuan:</strong><br>
                        <span id="buktiAdminAlasan" class="text-secondary">-</span>
                    </div>
                </div>
                <div id="buktiAdminImgContainer" class="text-center">
                    <img id="buktiAdminImg" src="" alt="Bukti Foto" class="img-fluid rounded shadow-sm" style="max-height: 480px; object-fit: contain; width: 100%; border: 1px solid #CBD5E1;">
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

<script>
const baseUrlAdmin = '<?= base_url() ?>';

function lihatBuktiAdmin(nama, subInfo, jenis, tgl, alasan, foto) {
    document.getElementById('buktiAdminNama').innerText = nama;
    document.getElementById('buktiAdminSub').innerText = subInfo + ' | Tanggal: ' + tgl;
    document.getElementById('buktiAdminJenis').innerText = jenis;
    document.getElementById('buktiAdminAlasan').innerText = alasan || '-';

    const container = document.getElementById('buktiAdminImgContainer');
    if (foto && foto.trim() !== '') {
        const url = baseUrlAdmin + 'public/uploads/pengajuan/' + foto;
        const ext = foto.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
            container.innerHTML = `<img src="${url}" alt="Bukti Foto" class="img-fluid rounded shadow-sm" style="max-height: 500px; object-fit: contain; width: 100%; border: 1px solid #CBD5E1;">`;
        } else if (ext === 'pdf') {
            container.innerHTML = `<iframe src="${url}" style="width: 100%; height: 500px; border: 1px solid #CBD5E1; border-radius: 8px;"></iframe>`;
        } else {
            container.innerHTML = `
                <div class="p-4 bg-light rounded text-center">
                    <i class="bi bi-file-earmark-text fs-1 text-primary d-block mb-2"></i>
                    <span class="fw-semibold d-block mb-2 text-dark">File: ${foto}</span>
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

    const modal = new bootstrap.Modal(document.getElementById('modalLihatBuktiAdmin'));
    modal.show();
}
</script>
