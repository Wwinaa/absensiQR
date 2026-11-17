<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><i class="bi bi-clock-history me-2" style="color: var(--medium-blue);"></i> Riwayat Absensi Pribadi</h1>
        <p class="text-muted small mb-0">Catatan riwayat kehadiran presensi harian Anda sebagai Wali Kelas & Tenaga Pendidik.</p>
    </div>
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
        <form method="GET" action="<?= base_url('wali-kelas/riwayat') ?>" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-secondary small">Mulai Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['start_date']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['end_date']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">Status Kehadiran</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="hadir" <?= strtolower($filters['status']) == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                    <option value="izin" <?= strtolower($filters['status']) == 'izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="sakit" <?= strtolower($filters['status']) == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                    <option value="alpa" <?= strtolower($filters['status']) == 'alpa' ? 'selected' : '' ?>>Alpa</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-filter"></i> Filter</button>
                <a href="<?= base_url('wali-kelas/riwayat') ?>" class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Table -->
<div class="card card-modern border-0">
    <div class="card-body p-0">
        <div class="table-modern-container">
            <table class="table table-modern mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2" style="color: var(--light-blue);"></i>
                                Tidak ada data absensi yang sesuai filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = ($page - 1) * 10 + 1;
                        foreach ($riwayat as $r): 
                            $statusBadge = 'badge-soft-secondary';
                            $statusLower = strtolower($r->status);
                            if ($statusLower == 'hadir') $statusBadge = 'badge-soft-success';
                            elseif ($statusLower == 'izin') $statusBadge = 'badge-soft-info';
                            elseif ($statusLower == 'sakit') $statusBadge = 'badge-soft-primary';
                            elseif ($statusLower == 'alpa') $statusBadge = 'badge-soft-danger';
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= date('d M Y', strtotime($r->tanggal)) ?></strong></td>
                            <td><?= $r->jam_masuk ? date('H:i', strtotime($r->jam_masuk)) . ' WIB' : '-' ?></td>
                            <td><?= $r->jam_pulang ? date('H:i', strtotime($r->jam_pulang)) . ' WIB' : '-' ?></td>
                            <td><span class="badge <?= $statusBadge ?>"><?= ucfirst($r->status) ?></span></td>
                            <td><span class="badge bg-light text-secondary border"><i class="bi bi-qr-code me-1"></i> QR Code</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="<?= base_url('wali-kelas/riwayat?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
