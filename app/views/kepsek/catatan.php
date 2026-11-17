<!-- Header Halaman Catatan Guru -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold mb-1" style="color: var(--dark-blue);">
            <i class="bi bi-journal-text me-2" style="color: var(--medium-blue);"></i>Catatan untuk Guru
        </h1>
        <p class="text-secondary small mb-0">Kelola catatan pembinaan, supervisi akademik, dan evaluasi kinerja tenaga pendidik.</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-soft-blue px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahCatatan" style="border-radius: 8px;">
            <i class="bi bi-plus-lg me-1"></i> Tambah Catatan Guru
        </button>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid var(--medium-blue) !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Total Catatan</span>
                    <h3 class="fw-bold my-1" style="color: var(--dark-blue);"><?= $stats['total'] ?? 0 ?></h3>
                    <div class="small text-muted">Diberikan ke guru</div>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                    <i class="bi bi-journal-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #F59E0B !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Belum Dibaca</span>
                    <h3 class="fw-bold my-1 text-warning"><?= $stats['belum_dibaca'] ?? 0 ?></h3>
                    <div class="small text-muted">Menunggu dibaca guru</div>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                    <i class="bi bi-envelope-exclamation-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #10B981 !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Sudah Dibaca</span>
                    <h3 class="fw-bold my-1 text-success"><?= $stats['sudah_dibaca'] ?? 0 ?></h3>
                    <div class="small text-muted">Telah dikonfirmasi guru</div>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                    <i class="bi bi-check2-all fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-modern h-100 border-0 p-3" style="border-left: 5px solid #8B5CF6 !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Apresiasi & Pembinaan</span>
                    <h3 class="fw-bold my-1" style="color: #8B5CF6;"><?= ($stats['kategori']['Apresiasi'] ?? 0) + ($stats['kategori']['Evaluasi Pembelajaran'] ?? 0) ?></h3>
                    <div class="small text-muted">Apresiasi & Evaluasi</div>
                </div>
                <div class="rounded-3 p-2" style="background-color: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                    <i class="bi bi-star-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Box -->
<div class="card card-modern border-0 mb-4 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom" style="border-color: var(--border-blue) !important;">
        <span class="fw-bold" style="color: var(--dark-blue);"><i class="bi bi-funnel me-1"></i> Filter & Pencarian Catatan</span>
    </div>
    <div class="card-body p-3">
        <form method="GET" action="<?= base_url('kepala-sekolah/catatan') ?>" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Cari Guru / Isi Catatan:</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama guru, NIP, isi..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" style="border-color: var(--border-blue);">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-secondary mb-1">Pilih Guru:</label>
                <select name="guru_id" class="form-select form-select-sm" style="border-color: var(--border-blue);">
                    <option value="">Semua Guru</option>
                    <?php if(!empty($all_guru)): ?>
                        <?php foreach($all_guru as $g): ?>
                            <option value="<?= $g->id ?>" <?= ($filters['guru_id'] == $g->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g->nama) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-secondary mb-1">Kategori:</label>
                <select name="kategori" class="form-select form-select-sm" style="border-color: var(--border-blue);">
                    <option value="">Semua Kategori</option>
                    <option value="Kedisiplinan" <?= ($filters['kategori'] == 'Kedisiplinan') ? 'selected' : '' ?>>Kedisiplinan</option>
                    <option value="Kinerja Mengajar" <?= ($filters['kategori'] == 'Kinerja Mengajar') ? 'selected' : '' ?>>Kinerja Mengajar</option>
                    <option value="Kehadiran" <?= ($filters['kategori'] == 'Kehadiran') ? 'selected' : '' ?>>Kehadiran</option>
                    <option value="Apresiasi" <?= ($filters['kategori'] == 'Apresiasi') ? 'selected' : '' ?>>Apresiasi</option>
                    <option value="Evaluasi Pembelajaran" <?= ($filters['kategori'] == 'Evaluasi Pembelajaran') ? 'selected' : '' ?>>Evaluasi Pembelajaran</option>
                    <option value="Umum" <?= ($filters['kategori'] == 'Umum') ? 'selected' : '' ?>>Umum</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-secondary mb-1">Status Baca:</label>
                <select name="status_baca" class="form-select form-select-sm" style="border-color: var(--border-blue);">
                    <option value="">Semua Status</option>
                    <option value="belum" <?= ($filters['status_baca'] == 'belum') ? 'selected' : '' ?>>Belum Dibaca</option>
                    <option value="sudah" <?= ($filters['status_baca'] == 'sudah') ? 'selected' : '' ?>>Sudah Dibaca</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-soft-blue flex-grow-1"><i class="bi bi-filter me-1"></i> Terapkan</button>
                <a href="<?= base_url('kepala-sekolah/catatan') ?>" class="btn btn-sm btn-outline-secondary" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Daftar Catatan Guru -->
<div class="card card-modern border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--border-blue) !important;">
        <span class="fw-bold" style="color: var(--dark-blue);"><i class="bi bi-table me-1"></i> Riwayat Catatan Pembinaan Guru</span>
        <span class="badge bg-light text-secondary border">Total: <?= count($catatan) ?> data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-modern-container">
            <table class="table table-modern mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="22%">Guru / Pendidik</th>
                        <th width="15%">Kategori & Urgensi</th>
                        <th>Isi Catatan</th>
                        <th width="12%">Status Baca</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($catatan)): ?>
                        <?php $no = 1; foreach($catatan as $c): ?>
                            <?php 
                                $badgeUrgensi = 'badge bg-secondary';
                                if($c->urgensi == 'Penting') $badgeUrgensi = 'badge bg-warning text-dark';
                                elseif($c->urgensi == 'Peringatan') $badgeUrgensi = 'badge bg-danger';
                                elseif($c->urgensi == 'Apresiasi') $badgeUrgensi = 'badge bg-success';
                            ?>
                            <tr>
                                <td class="text-center"><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= $no++ ?></span></td>
                                <td>
                                    <span class="fw-semibold text-dark"><?= date('d/m/Y', strtotime($c->tanggal)) ?></span>
                                </td>
                                <td>
                                    <strong style="color: var(--dark-blue);"><?= htmlspecialchars($c->nama_guru) ?></strong>
                                    <div class="small text-muted">NIP: <?= htmlspecialchars($c->nip_guru ?? '-') ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary d-inline-block mb-1"><?= htmlspecialchars($c->kategori) ?></span>
                                    <div><span class="<?= $badgeUrgensi ?>"><?= htmlspecialchars($c->urgensi) ?></span></div>
                                </td>
                                <td>
                                    <?php if(!empty($c->judul)): ?>
                                        <div class="fw-bold text-dark small mb-1"><?= htmlspecialchars($c->judul) ?></div>
                                    <?php endif; ?>
                                    <div class="text-secondary small" style="max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($c->isi_catatan) ?>">
                                        <?= htmlspecialchars($c->isi_catatan) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($c->status_baca == 'sudah'): ?>
                                        <span class="badge badge-soft-success"><i class="bi bi-check2-all me-1"></i>Sudah Dibaca</span>
                                    <?php else: ?>
                                        <span class="badge badge-soft-warning text-warning"><i class="bi bi-clock me-1"></i>Belum Dibaca</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Tombol Detail -->
                                        <button type="button" class="btn btn-outline-info" style="border-radius: 6px 0 0 6px;" data-bs-toggle="modal" data-bs-target="#detailModal<?= $c->id ?>" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <!-- Tombol Edit -->
                                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $c->id ?>" title="Edit Catatan">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <!-- Tombol Hapus -->
                                        <a href="<?= base_url('kepala-sekolah/hapus-catatan/' . $c->id) ?>" class="btn btn-outline-danger" style="border-radius: 0 6px 6px 0;" onclick="return confirm('Apakah Anda yakin ingin menghapus catatan ini?')" title="Hapus Catatan">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>
                                Belum ada catatan pembinaan guru yang sesuai kriteria.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL CONTAINER (Diletakkan di luar tabel agar tidak      -->
<!-- terpotong overflow container dan tidak berkedip)         -->
<!-- ======================================================== -->

<!-- Modal Tambah Catatan Baru -->
<div class="modal fade" id="modalTambahCatatan" tabindex="-1" aria-labelledby="modalTambahCatatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <form action="<?= base_url('kepala-sekolah/tambah-catatan') ?>" method="POST">
                <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title fw-bold" id="modalTambahCatatanLabel" style="color: var(--dark-blue);">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Catatan untuk Guru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Pilih Guru yang Dituju <span class="text-danger">*</span></label>
                        <select name="guru_id" class="form-select" required style="border-radius: 8px; border-color: var(--border-blue);">
                            <option value="">-- Pilih Guru --</option>
                            <?php if(!empty($all_guru)): ?>
                                <?php foreach($all_guru as $g): ?>
                                    <option value="<?= $g->id ?>">
                                        <?= htmlspecialchars($g->nama) ?> (NIP: <?= htmlspecialchars($g->nip ?? '-') ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required style="border-radius: 8px; border-color: var(--border-blue);">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Kategori Catatan <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select" required style="border-radius: 8px; border-color: var(--border-blue);">
                                <option value="Kedisiplinan">Kedisiplinan</option>
                                <option value="Kinerja Mengajar">Kinerja Mengajar</option>
                                <option value="Kehadiran">Kehadiran</option>
                                <option value="Apresiasi">Apresiasi</option>
                                <option value="Evaluasi Pembelajaran">Evaluasi Pembelajaran</option>
                                <option value="Umum">Umum</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Tingkat Urgensi</label>
                        <select name="urgensi" class="form-select" style="border-radius: 8px; border-color: var(--border-blue);">
                            <option value="Biasa">Biasa</option>
                            <option value="Penting">Penting</option>
                            <option value="Peringatan">Peringatan</option>
                            <option value="Apresiasi">Apresiasi</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Perihal / Judul Catatan</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Evaluasi Perangkat Ajar Semester Ganjil" style="border-radius: 8px; border-color: var(--border-blue);">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Isi Catatan Pembinaan <span class="text-danger">*</span></label>
                        <textarea name="isi_catatan" class="form-control" rows="4" placeholder="Tuliskan arahan, evaluasi, apresiasi, atau masukan untuk guru..." required style="border-radius: 8px; border-color: var(--border-blue);"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-soft-blue px-4"><i class="bi bi-send-fill me-1"></i> Simpan & Kirim Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if(!empty($catatan)): ?>
    <?php foreach($catatan as $c): ?>
        <?php 
            $badgeUrgensi = 'badge bg-secondary';
            if($c->urgensi == 'Penting') $badgeUrgensi = 'badge bg-warning text-dark';
            elseif($c->urgensi == 'Peringatan') $badgeUrgensi = 'badge bg-danger';
            elseif($c->urgensi == 'Apresiasi') $badgeUrgensi = 'badge bg-success';
        ?>

        <!-- Modal Detail Catatan -->
        <div class="modal fade" id="detailModal<?= $c->id ?>" tabindex="-1" aria-labelledby="detailModalLabel<?= $c->id ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                        <h5 class="modal-title fw-bold" id="detailModalLabel<?= $c->id ?>" style="color: var(--dark-blue);">
                            <i class="bi bi-journal-text me-2"></i>Detail Catatan Guru
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <div class="text-muted small">Guru yang Dituju:</div>
                            <h5 class="fw-bold mb-0" style="color: var(--dark-blue);"><?= htmlspecialchars($c->nama_guru) ?></h5>
                            <div class="small text-secondary">NIP: <?= htmlspecialchars($c->nip_guru ?? '-') ?></div>
                        </div>
                        <hr class="my-2">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <span class="text-muted small">Tanggal:</span>
                                <div class="fw-semibold"><?= date('d F Y', strtotime($c->tanggal)) ?></div>
                            </div>
                            <div class="col-6">
                                <span class="text-muted small">Status Baca:</span>
                                <div>
                                    <?= $c->status_baca == 'sudah' ? '<span class="badge badge-soft-success">Sudah Dibaca</span>' : '<span class="badge badge-soft-warning text-warning">Belum Dibaca</span>' ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <span class="text-muted small">Kategori:</span>
                                <div><span class="badge badge-soft-primary"><?= htmlspecialchars($c->kategori) ?></span></div>
                            </div>
                            <div class="col-6">
                                <span class="text-muted small">Tingkat Urgensi:</span>
                                <div><span class="<?= $badgeUrgensi ?>"><?= htmlspecialchars($c->urgensi) ?></span></div>
                            </div>
                        </div>
                        <?php if(!empty($c->judul)): ?>
                            <div class="mb-2">
                                <span class="text-muted small">Perihal / Judul:</span>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($c->judul) ?></div>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <span class="text-muted small">Isi Catatan Pembinaan:</span>
                            <div class="p-3 bg-light rounded-3 mt-1" style="border: 1px solid var(--border-blue); font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap;"><?= htmlspecialchars($c->isi_catatan) ?></div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light" style="border-radius: 0 0 12px 12px;">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Catatan -->
        <div class="modal fade" id="editModal<?= $c->id ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $c->id ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <form action="<?= base_url('kepala-sekolah/edit-catatan') ?>" method="POST">
                        <input type="hidden" name="id" value="<?= $c->id ?>">
                        <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                            <h5 class="modal-title fw-bold" id="editModalLabel<?= $c->id ?>" style="color: var(--dark-blue);">
                                <i class="bi bi-pencil-square me-2"></i>Edit Catatan Guru
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Pilih Guru <span class="text-danger">*</span></label>
                                <select name="guru_id" class="form-select" required style="border-radius: 8px; border-color: var(--border-blue);">
                                    <?php foreach($all_guru as $g): ?>
                                        <option value="<?= $g->id ?>" <?= ($c->guru_id == $g->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($g->nama) ?> (NIP: <?= htmlspecialchars($g->nip ?? '-') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($c->tanggal) ?>" required style="border-radius: 8px; border-color: var(--border-blue);">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Kategori <span class="text-danger">*</span></label>
                                    <select name="kategori" class="form-select" required style="border-radius: 8px; border-color: var(--border-blue);">
                                        <option value="Kedisiplinan" <?= ($c->kategori == 'Kedisiplinan') ? 'selected' : '' ?>>Kedisiplinan</option>
                                        <option value="Kinerja Mengajar" <?= ($c->kategori == 'Kinerja Mengajar') ? 'selected' : '' ?>>Kinerja Mengajar</option>
                                        <option value="Kehadiran" <?= ($c->kategori == 'Kehadiran') ? 'selected' : '' ?>>Kehadiran</option>
                                        <option value="Apresiasi" <?= ($c->kategori == 'Apresiasi') ? 'selected' : '' ?>>Apresiasi</option>
                                        <option value="Evaluasi Pembelajaran" <?= ($c->kategori == 'Evaluasi Pembelajaran') ? 'selected' : '' ?>>Evaluasi Pembelajaran</option>
                                        <option value="Umum" <?= ($c->kategori == 'Umum') ? 'selected' : '' ?>>Umum</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Tingkat Urgensi</label>
                                <select name="urgensi" class="form-select" style="border-radius: 8px; border-color: var(--border-blue);">
                                    <option value="Biasa" <?= ($c->urgensi == 'Biasa') ? 'selected' : '' ?>>Biasa</option>
                                    <option value="Penting" <?= ($c->urgensi == 'Penting') ? 'selected' : '' ?>>Penting</option>
                                    <option value="Peringatan" <?= ($c->urgensi == 'Peringatan') ? 'selected' : '' ?>>Peringatan</option>
                                    <option value="Apresiasi" <?= ($c->urgensi == 'Apresiasi') ? 'selected' : '' ?>>Apresiasi</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Perihal / Judul</label>
                                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($c->judul ?? '') ?>" style="border-radius: 8px; border-color: var(--border-blue);">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Isi Catatan Pembinaan <span class="text-danger">*</span></label>
                                <textarea name="isi_catatan" class="form-control" rows="4" required style="border-radius: 8px; border-color: var(--border-blue);"><?= htmlspecialchars($c->isi_catatan) ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light" style="border-radius: 0 0 12px 12px;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-soft-blue px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<style>
/* Dark Mode Styling for Kepsek Catatan */
[data-bs-theme="dark"] .modal-content {
    background-color: #1E293B !important;
    border-color: #334155 !important;
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .modal-header.bg-light,
[data-bs-theme="dark"] .modal-footer.bg-light {
    background-color: #0F172A !important;
    border-color: #334155 !important;
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .modal-body {
    background-color: #1E293B !important;
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .modal-body .p-3.bg-light {
    background-color: #0F172A !important;
    border-color: #334155 !important;
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .modal-body .form-control,
[data-bs-theme="dark"] .modal-body .form-select {
    background-color: #0F172A !important;
    border-color: #334155 !important;
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .modal-body hr {
    border-color: #334155 !important;
    opacity: 0.5;
}
[data-bs-theme="dark"] .text-dark {
    color: #F1F5F9 !important;
}
</style>
