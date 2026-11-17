<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold mb-1" style="color: var(--dark-blue);"><i class="bi bi-file-earmark-spreadsheet me-2" style="color: var(--medium-blue);"></i> Rekap & Riwayat Absensi Keseluruhan</h1>
        <p class="text-muted small mb-0"><i class="bi bi-calendar-check me-1 text-primary"></i> Terjadwal aktif: <strong>Senin s.d. Jum'at</strong> (Sabtu & Minggu tidak direkap absensi)</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="<?= base_url('admin/rekap') ?>?export=excel&start_date=<?= urlencode($filters['start_date']) ?>&end_date=<?= urlencode($filters['end_date']) ?>&status=<?= urlencode($filters['status']) ?>&role=<?= urlencode($filters['role']) ?>&nama=<?= urlencode($filters['nama']) ?>&kelas_id=<?= urlencode($filters['kelas_id']) ?>" class="btn btn-success btn-sm me-2">
            <i class="bi bi-file-excel"></i> Export Excel
        </a>
        <a href="<?= base_url('admin/rekap') ?>?export=pdf&start_date=<?= urlencode($filters['start_date']) ?>&end_date=<?= urlencode($filters['end_date']) ?>&status=<?= urlencode($filters['status']) ?>&role=<?= urlencode($filters['role']) ?>&nama=<?= urlencode($filters['nama']) ?>&kelas_id=<?= urlencode($filters['kelas_id']) ?>" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf"></i> Export PDF
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-2 col-6 mb-3">
        <div class="card card-modern border-0 text-center h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Rekaman</h6>
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
<?php
    $roleFilter = $filters['role'] ?? '';
    $isFilterGuru = (strtolower(trim($roleFilter)) === 'guru');
    $isFilterSiswa = (strtolower(trim($roleFilter)) === 'siswa');
?>
<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/rekap') ?>" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label text-secondary small fw-semibold">Filter Peran (Role)</label>
                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Role (Guru & Siswa)</option>
                    <option value="Guru" <?= $filters['role'] == 'Guru' ? 'selected' : '' ?>>Tenaga Pendidik / Guru</option>
                    <option value="Siswa" <?= $filters['role'] == 'Siswa' ? 'selected' : '' ?>>Peserta Didik / Siswa</option>
                </select>
            </div>
            <div class="col-md-2" <?= $isFilterGuru ? 'style="opacity: 0.5;"' : '' ?>>
                <label class="form-label text-secondary small fw-semibold">Rombel / Kelas <?= $isFilterGuru ? '(Khusus Siswa)' : '' ?></label>
                <select name="kelas_id" class="form-select form-select-sm" <?= $isFilterGuru ? 'disabled' : '' ?>>
                    <option value="">Semua Kelas</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k->id ?>" <?= $filters['kelas_id'] == $k->id ? 'selected' : '' ?>><?= $k->nama_kelas ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small fw-semibold">Pencarian Nama / ID</label>
                <input type="text" name="nama" class="form-control form-control-sm" placeholder="<?= $isFilterGuru ? 'Cari Nama / NIP Guru...' : ($isFilterSiswa ? 'Cari Nama / NIS Siswa...' : 'Cari Nama / NIS / NIP...') ?>" value="<?= htmlspecialchars($filters['nama']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small fw-semibold">Tgl Mulai</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['start_date']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small fw-semibold">Tgl Akhir</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['end_date']) ?>">
            </div>
            <div class="col-md-1">
                <label class="form-label text-secondary small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="Hadir" <?= strtolower($filters['status']) == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                    <option value="Izin" <?= strtolower($filters['status']) == 'izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="Sakit" <?= strtolower($filters['status']) == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                    <option value="Alpa" <?= strtolower($filters['status']) == 'alpa' ? 'selected' : '' ?>>Alpa</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1" style="background-color: var(--medium-blue); border-color: var(--medium-blue);" title="Terapkan Filter">
                    <i class="bi bi-filter"></i>
                </button>
                <a href="<?= base_url('admin/rekap') ?>" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
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
                    <?php if ($isFilterGuru): ?>
                        <!-- Tampilan Khusus Rekap Guru (Tanpa NIS, NISN, atau Kelas/Jurusan) -->
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th>Mata Pelajaran</th>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th class="text-center">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    <?php elseif ($isFilterSiswa): ?>
                        <!-- Tampilan Khusus Rekap Siswa -->
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Kelas / Jurusan</th>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th class="text-center">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    <?php else: ?>
                        <!-- Tampilan Keseluruhan (Semua Role) yang Dinamis & Rapi -->
                        <tr>
                            <th width="4%" class="text-center">No</th>
                            <th width="16%">Nama Lengkap</th>
                            <th width="15%">Role</th>
                            <th width="11%">NIS / NIP</th>
                            <th width="16%">Rombel / Mata Pelajaran</th>
                            <th width="9%">Tanggal</th>
                            <th width="7%" class="text-center">Masuk</th>
                            <th width="7%" class="text-center">Pulang</th>
                            <th width="7%" class="text-center">Status</th>
                            <th width="8%">Keterangan</th>
                        </tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php if (!empty($riwayat)): ?>
                        <?php $no = ($page - 1) * 10 + 1; foreach ($riwayat as $r): ?>
                            <?php if ($isFilterGuru): ?>
                                <!-- Baris Data Khusus Guru -->
                                <tr>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= $no++ ?></span>
                                    </td>
                                    <td class="fw-semibold" style="color: var(--dark-blue);">
                                        <?= htmlspecialchars($r->nama) ?>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-semibold px-2 py-1 rounded" style="background:var(--very-light-blue); border:1px solid var(--border-blue); font-size:0.82rem; color:var(--dark-blue);">
                                            <?= htmlspecialchars(!empty($r->id_nomor) && $r->id_nomor !== '-' ? $r->id_nomor : '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                            <i class="bi bi-journal-bookmark me-1"></i><?= htmlspecialchars(!empty($r->mapel) && $r->mapel !== '-' ? $r->mapel : 'Guru Pengajar') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-medium text-secondary">
                                            <?= date('d/m/Y', strtotime($r->tanggal)) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($r->jam_masuk)): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-monospace">
                                                <i class="bi bi-box-arrow-in-right me-1"></i><?= substr($r->jam_masuk, 0, 5) ?> WIB
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($r->jam_pulang)): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-monospace">
                                                <i class="bi bi-box-arrow-right me-1"></i><?= substr($r->jam_pulang, 0, 5) ?> WIB
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (in_array(strtolower($r->status), ['hadir', 'terlambat'])): ?>
                                            <span class="badge badge-soft-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Hadir</span>
                                        <?php elseif (strtolower($r->status) == 'izin'): ?>
                                            <span class="badge badge-soft-info px-2 py-1"><i class="bi bi-info-circle me-1"></i>Izin</span>
                                        <?php elseif (strtolower($r->status) == 'sakit'): ?>
                                            <span class="badge badge-soft-primary px-2 py-1"><i class="bi bi-hospital me-1"></i>Sakit</span>
                                        <?php else: ?>
                                            <span class="badge badge-soft-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Alpa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-secondary"><?= !empty($r->keterangan) && $r->keterangan !== '-' ? htmlspecialchars($r->keterangan) : '-' ?></small>
                                    </td>
                                </tr>
                            <?php elseif ($isFilterSiswa): ?>
                                <!-- Baris Data Khusus Siswa -->
                                <tr>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= $no++ ?></span>
                                    </td>
                                    <td class="fw-semibold" style="color: var(--dark-blue);">
                                        <?= htmlspecialchars($r->nama) ?>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-semibold px-2 py-1 rounded" style="background:var(--very-light-blue); border:1px solid var(--border-blue); font-size:0.82rem; color:var(--dark-blue);">
                                            <?= htmlspecialchars(!empty($r->id_nomor) && $r->id_nomor !== '-' ? $r->id_nomor : '-') ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars(!empty($r->nisn) && $r->nisn !== '-' ? $r->nisn : '-') ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($r->kelas) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($r->jurusan) ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-medium text-secondary">
                                            <?= date('d/m/Y', strtotime($r->tanggal)) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($r->jam_masuk)): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-monospace">
                                                <?= substr($r->jam_masuk, 0, 5) ?> WIB
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($r->jam_pulang)): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-monospace">
                                                <?= substr($r->jam_pulang, 0, 5) ?> WIB
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (in_array(strtolower($r->status), ['hadir', 'terlambat'])): ?>
                                            <span class="badge badge-soft-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Hadir</span>
                                        <?php elseif (strtolower($r->status) == 'izin'): ?>
                                            <span class="badge badge-soft-info px-2 py-1"><i class="bi bi-info-circle me-1"></i>Izin</span>
                                        <?php elseif (strtolower($r->status) == 'sakit'): ?>
                                            <span class="badge badge-soft-primary px-2 py-1"><i class="bi bi-hospital me-1"></i>Sakit</span>
                                        <?php else: ?>
                                            <span class="badge badge-soft-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Alpa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-secondary"><?= !empty($r->keterangan) && $r->keterangan !== '-' ? htmlspecialchars($r->keterangan) : '-' ?></small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <!-- Baris Data Semua Role (Tampilan Cerdas) -->
                                <tr>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= $no++ ?></span>
                                    </td>
                                    <td class="fw-semibold" style="color: var(--dark-blue);"><?= htmlspecialchars($r->nama) ?></td>
                                    <td>
                                        <?php if ($r->role == 'Guru'): ?>
                                            <div class="d-flex align-items-center gap-1 flex-nowrap">
                                                <span class="badge badge-soft-primary text-nowrap"><i class="bi bi-person-workspace me-1"></i>Guru</span>
                                                <?php if (!empty($r->wali_kelas_nama)): ?>
                                                    <span class="badge badge-soft-warning text-nowrap" title="Wali Kelas <?= htmlspecialchars($r->wali_kelas_nama) ?>"><i class="bi bi-person-badge me-1"></i>Wali Kelas</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php elseif ($r->role == 'Wali Kelas'): ?>
                                            <span class="badge badge-soft-warning text-nowrap"><i class="bi bi-person-badge me-1"></i>Wali Kelas</span>
                                        <?php else: ?>
                                            <span class="badge badge-soft-info text-nowrap"><i class="bi bi-mortarboard me-1"></i>Siswa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($r->role == 'Guru' || $r->role == 'Wali Kelas'): ?>
                                            <span class="badge badge-soft-primary me-1">NIP</span>
                                            <span class="font-monospace fw-semibold"><?= htmlspecialchars(!empty($r->id_nomor) && $r->id_nomor !== '-' ? $r->id_nomor : '-') ?></span>
                                        <?php else: ?>
                                            <span class="font-monospace fw-semibold"><?= htmlspecialchars(!empty($r->id_nomor) && $r->id_nomor !== '-' ? $r->id_nomor : '-') ?></span>
                                            <?php if (!empty($r->nisn) && $r->nisn !== '-'): ?>
                                                <br><small class="text-muted">NISN: <?= htmlspecialchars($r->nisn) ?></small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($r->role == 'Guru'): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                                <i class="bi bi-journal-bookmark me-1"></i><?= htmlspecialchars(!empty($r->mapel) && $r->mapel !== '-' ? $r->mapel : 'Tenaga Pendidik') ?>
                                            </span>
                                        <?php elseif ($r->role == 'Wali Kelas'): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                                <i class="bi bi-door-open me-1"></i>Wali Kelas <?= htmlspecialchars($r->kelas) ?>
                                            </span>
                                        <?php else: ?>
                                            <strong><?= htmlspecialchars($r->kelas) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($r->jurusan) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-medium text-secondary">
                                            <?= date('d/m/Y', strtotime($r->tanggal)) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= !empty($r->jam_masuk) ? '<span class="font-monospace">' . substr($r->jam_masuk, 0, 5) . '</span>' : '-' ?>
                                    </td>
                                    <td>
                                        <?= !empty($r->jam_pulang) ? '<span class="font-monospace">' . substr($r->jam_pulang, 0, 5) . '</span>' : '-' ?>
                                    </td>
                                    <td class="text-center">
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
                                    <td>
                                        <small class="text-secondary"><?= !empty($r->keterangan) && $r->keterangan !== '-' ? htmlspecialchars($r->keterangan) : '-' ?></small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $isFilterGuru ? 9 : 10 ?>" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>
                                Tidak ada data absensi ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <?php
            $startPage = max(1, $page - 1);
            $endPage = min($totalPages, $startPage + 2);
            if ($endPage - $startPage < 2) {
                $startPage = max(1, $endPage - 2);
            }
            $buildPageUrl = function($p) use ($filters) {
                return base_url('admin/rekap') . '?page=' . $p 
                    . '&start_date=' . urlencode($filters['start_date'] ?? '') 
                    . '&end_date=' . urlencode($filters['end_date'] ?? '') 
                    . '&status=' . urlencode($filters['status'] ?? '') 
                    . '&role=' . urlencode($filters['role'] ?? '') 
                    . '&nama=' . urlencode($filters['nama'] ?? '') 
                    . '&kelas_id=' . urlencode($filters['kelas_id'] ?? '');
            };
        ?>
        <div class="p-3 border-top d-flex flex-column align-items-center justify-content-center text-center gap-2" style="border-color: var(--border-blue) !important;">
            <div class="small text-muted text-center">
                Halaman <strong><?= $page ?></strong> dari <strong><?= $totalPages ?></strong> (Total <?= htmlspecialchars($totalRecords ?? $totalData ?? 0) ?> data)
            </div>
            <nav aria-label="Navigasi Halaman" class="d-flex justify-content-center">
                <ul class="pagination pagination-sm justify-content-center mb-0 custom-pagination gap-1">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link px-3" href="<?= $buildPageUrl($page - 1) ?>" aria-label="Previous">&lt;</a>
                        </li>
                    <?php endif; ?>

                    <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link px-3" href="<?= $buildPageUrl($i) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link px-3" href="<?= $buildPageUrl($page + 1) ?>" aria-label="Next">&gt;</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>
