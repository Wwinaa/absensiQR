<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><i class="bi bi-people me-2" style="color: var(--medium-blue);"></i> Absensi Kelas: <?= $kelas ? htmlspecialchars($kelas->nama_kelas) : 'Belum Ada Kelas' ?></h1>
</div>

<?php if (!$kelas): ?>
    <div class="alert alert-dismissible fade show text-start border-0 shadow-sm badge-soft-warning" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> Anda belum ditugaskan sebagai wali kelas untuk kelas manapun.
    </div>
<?php else: ?>
    <div class="card card-modern border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom" style="border-color: var(--border-blue) !important;">
            <form action="" method="GET" class="d-flex align-items-center">
                <label for="tanggal" class="me-2 fw-bold" style="color: var(--dark-blue);">Pilih Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control w-auto me-2" value="<?= htmlspecialchars($tanggal) ?>" max="<?= date('Y-m-d') ?>" style="border-color: var(--border-blue); color: var(--dark-blue);">
                <button type="submit" class="btn-soft-blue btn-sm px-3"><i class="bi bi-search me-1"></i> Tampilkan</button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-modern-container">
                <table class="table table-modern mb-0 align-middle">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($absensi)): ?>
                            <?php $no = 1; foreach ($absensi as $a): ?>
                                <tr>
                                    <td class="text-center"><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= $no++ ?></span></td>
                                    <td class="fw-semibold" style="color: var(--dark-blue);"><?= htmlspecialchars($a->nis) ?></td>
                                    <td class="fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($a->nama_lengkap) ?></td>
                                    <td><?= $a->jam_masuk ? $a->jam_masuk : '-' ?></td>
                                    <td><?= $a->jam_pulang ? $a->jam_pulang : '-' ?></td>
                                    <td>
                                        <?php if (in_array(strtolower($a->status), ['hadir', 'terlambat'])): ?>
                                            <span class="badge badge-soft-success">Hadir</span>

                                        <?php elseif ($a->status == 'izin'): ?>
                                            <span class="badge badge-soft-info">Izin</span>
                                        <?php elseif ($a->status == 'sakit'): ?>
                                            <span class="badge badge-soft-primary">Sakit</span>
                                        <?php elseif ($a->status == 'alpa'): ?>
                                            <span class="badge badge-soft-danger">Alpa</span>
                                        <?php else: ?>
                                            <span class="badge" style="background-color: #E5E7EB; color: #4B5563;">Belum Hadir</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>
                                    Belum ada data siswa di kelas ini pada tanggal terpilih.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
