<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
</div>

<?php if(!$kelas): ?>
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i> Anda belum ditugaskan sebagai wali kelas untuk kelas mana pun.</div>
<?php else: ?>
    <div class="card card-modern border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3" style="color: var(--dark-blue);">Kelas: <?= htmlspecialchars($kelas->nama_kelas) ?></h5>
            <div class="table-modern-container">
                <table class="table table-modern mb-0 align-middle">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Hadir</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alpa</th>
                            <th>Kehadiran (%)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($siswaStats)): ?>
                            <tr><td colspan="9" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data siswa.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($siswaStats as $s): 
                                $persentase = $s->total_hari > 0 ? round(($s->hadir / $s->total_hari) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($s->nis) ?></td>
                                <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($s->nama_lengkap) ?></strong></td>
                                <td class="text-success fw-bold"><?= $s->hadir ?></td>
                                <td class="text-primary fw-bold"><?= $s->izin ?></td>
                                <td class="text-warning fw-bold"><?= $s->sakit ?></td>
                                <td class="text-danger fw-bold"><?= $s->alpa ?></td>
                                <td>
                                    <?php if($s->total_hari == 0): ?>
                                        <span class="text-muted">Data Belum Cukup</span>
                                    <?php else: ?>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar <?= $persentase < 70 ? 'bg-danger' : 'bg-success' ?>" role="progressbar" style="width: <?= $persentase ?>%;" aria-valuenow="<?= $persentase ?>" aria-valuemin="0" aria-valuemax="100"><?= $persentase ?>%</div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($s->total_hari > 0 && $persentase < 70): ?>
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Perhatian</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Aman</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 small text-muted">
                * Persentase Kehadiran = (Hadir / Total Hari Efektif) × 100%. Total Hari Efektif adalah total absensi (Hadir, Izin, Sakit, Alpa) yang telah dicatat. Izin dan Sakit tidak dihitung sebagai hadir.
            </div>
        </div>
    </div>
<?php endif; ?>
