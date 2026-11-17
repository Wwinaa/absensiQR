<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><i class="bi bi-clock-history me-2" style="color: var(--medium-blue);"></i> Riwayat Absensi Anda</h1>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-2 col-6 mb-3">
        <div class="card card-modern border-0 text-center h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total</h6>
                <h3 class="fw-bold mb-0" style="color: var(--dark-blue);"><?= $stats['total'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6 mb-3">
        <div class="card card-modern border-0 text-center h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Hadir</h6>
                <h3 class="fw-bold mb-0 text-success"><?= $stats['hadir'] ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2 col-6 mb-3">
        <div class="card card-modern border-0 text-center h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Izin</h6>
                <h3 class="fw-bold mb-0 text-info"><?= $stats['izin'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6 mb-3">
        <div class="card card-modern border-0 text-center h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Sakit</h6>
                <h3 class="fw-bold mb-0 text-primary"><?= $stats['sakit'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6 mb-3">
        <div class="card card-modern border-0 text-center h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Alpa</h6>
                <h3 class="fw-bold mb-0 text-danger"><?= $stats['alpa'] ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('siswa/riwayat') ?>" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-secondary small">Mulai Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['start_date']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['end_date']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="Hadir" <?= strtolower($filters['status']) == 'hadir' ? 'selected' : '' ?>>Hadir</option>

                    <option value="Izin" <?= strtolower($filters['status']) == 'izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="Sakit" <?= strtolower($filters['status']) == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                    <option value="Alpa" <?= strtolower($filters['status']) == 'alpa' ? 'selected' : '' ?>>Alpa</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1" style="background-color: var(--medium-blue); border-color: var(--medium-blue);">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="<?= base_url('siswa/riwayat') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card card-modern border-0 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive table-modern-container">
            <table class="table table-modern mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($riwayat)): ?>
                        <?php $no = ($page - 1) * 10 + 1; foreach ($riwayat as $r): ?>
                            <tr>
                                <td class="text-center"><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= $no++ ?></span></td>
                                <td class="fw-semibold" style="color: var(--dark-blue);"><?= date('d-m-Y', strtotime($r->tanggal)) ?></td>
                                <td><?= $r->jam_masuk ? $r->jam_masuk : '-' ?></td>
                                <td><?= $r->jam_pulang ? $r->jam_pulang : '-' ?></td>
                                <td>
                                    <?php if (in_array(strtolower($r->status), ['hadir', 'terlambat'])): ?>
                                        <span class="badge badge-soft-success">Hadir</span>
                                    <?php elseif (strtolower($r->status) == 'izin'): ?>
                                        <span class="badge badge-soft-info">Izin</span>
                                    <?php elseif (strtolower($r->status) == 'sakit'): ?>
                                        <span class="badge badge-soft-primary">Sakit</span>
                                    <?php else: ?>
                                        <span class="badge badge-soft-danger">Alpa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary">
                                    <?php
                                        $ketClean = trim($r->keterangan ?? '');
                                        $ketClean = preg_replace('/^Pengajuan (?:Izin|Sakit):\s*/i', '', $ketClean);
                                        if (strtolower($r->status) === 'alpa' || empty($ketClean) || stripos($ketClean, 'Tidak scan') !== false) {
                                            $ketClean = 'tidak ada keterangan';
                                        }
                                        echo htmlspecialchars($ketClean);
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>
                                Belum ada riwayat absensi.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="p-3 border-top" style="border-color: var(--border-blue) !important;">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0 custom-pagination">
                    <?php for($i=1; $i<=$totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="<?= base_url('siswa/riwayat') ?>?page=<?= $i ?>&start_date=<?= urlencode($filters['start_date']) ?>&end_date=<?= urlencode($filters['end_date']) ?>&status=<?= urlencode($filters['status']) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>
