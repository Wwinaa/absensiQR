<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
</div>

<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('siswa/jadwal') ?>">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="hari" class="form-select bg-light border-0">
                        <option value="">Semua Hari</option>
                        <option value="senin" <?= ($hari_aktif == 'senin') ? 'selected' : '' ?>>Senin</option>
                        <option value="selasa" <?= ($hari_aktif == 'selasa') ? 'selected' : '' ?>>Selasa</option>
                        <option value="rabu" <?= ($hari_aktif == 'rabu') ? 'selected' : '' ?>>Rabu</option>
                        <option value="kamis" <?= ($hari_aktif == 'kamis') ? 'selected' : '' ?>>Kamis</option>
                        <option value="jumat" <?= ($hari_aktif == 'jumat') ? 'selected' : '' ?>>Jumat</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button class="btn btn-primary w-100 me-1" type="submit"><i class="bi bi-filter"></i> Filter</button>
                    <a href="<?= base_url('siswa/jadwal') ?>" class="btn btn-outline-danger w-100"><i class="bi bi-x-circle"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-modern border-0">
    <div class="card-body p-0">
        <div class="table-modern-container">
            <table class="table table-modern mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Tahun Ajaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($jadwal)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada jadwal pelajaran.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($jadwal as $j): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge bg-secondary"><?= ucfirst($j->hari) ?></span></td>
                            <td><strong style="color: var(--dark-blue);"><?= substr($j->jam_mulai, 0, 5) ?> - <?= substr($j->jam_selesai, 0, 5) ?></strong></td>
                            <td><?= htmlspecialchars($j->nama_mapel) ?></td>
                            <td><?= htmlspecialchars($j->nama_guru) ?></td>
                            <td><?= htmlspecialchars($j->tahun) ?> (<?= ucfirst($j->semester) ?>)</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
