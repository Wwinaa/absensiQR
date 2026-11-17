<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><i class="bi bi-calendar3 me-2" style="color: var(--medium-blue);"></i> <?= htmlspecialchars($title) ?></h1>
        <p class="text-muted small mb-0">Daftar jadwal dan jam mengajar resmi Anda di kelas.</p>
    </div>
</div>

<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('guru/jadwal') ?>">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="hari" class="form-select bg-light border-0" style="border-radius: 8px;">
                        <option value="">Semua Hari (Senin - Jumat)</option>
                        <option value="senin" <?= ($hari_aktif == 'senin') ? 'selected' : '' ?>>Senin</option>
                        <option value="selasa" <?= ($hari_aktif == 'selasa') ? 'selected' : '' ?>>Selasa</option>
                        <option value="rabu" <?= ($hari_aktif == 'rabu') ? 'selected' : '' ?>>Rabu</option>
                        <option value="kamis" <?= ($hari_aktif == 'kamis') ? 'selected' : '' ?>>Kamis</option>
                        <option value="jumat" <?= ($hari_aktif == 'jumat') ? 'selected' : '' ?>>Jumat</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary" type="submit" style="border-radius: 8px;"><i class="bi bi-filter me-1"></i> Filter</button>
                    <?php if(!empty($hari_aktif)): ?>
                        <a href="<?= base_url('guru/jadwal') ?>" class="btn btn-outline-danger" style="border-radius: 8px;"><i class="bi bi-x-circle me-1"></i> Reset</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-modern border-0">
    <div class="card-header bg-white py-3 border-0">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0" style="color: var(--dark-blue);"><i class="bi bi-person-video3 me-2" style="color: var(--primary-blue);"></i> Jadwal Pelajaran yang Anda Ampu</h5>
            <span class="badge bg-primary px-3 py-2" style="border-radius: 8px;"><?= count($jadwal) ?> Jadwal Mengajar</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-modern-container">
            <table class="table table-modern mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Hari</th>
                        <th>Jam Mengajar</th>
                        <th>Mata Pelajaran</th>
                        <th>Mengajar di Kelas</th>
                        <th>Jurusan</th>
                        <th>Ruang</th>
                        <th>Tahun Ajaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($jadwal)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2" style="color: var(--light-blue);"></i>
                                Tidak ada jadwal mengajar yang ditemukan <?= !empty($hari_aktif) ? 'pada hari ' . ucfirst($hari_aktif) : '' ?>.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($jadwal as $j): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge bg-secondary" style="border-radius: 6px;"><?= ucfirst($j->hari) ?></span></td>
                            <td><strong style="color: var(--dark-blue);"><i class="bi bi-clock me-1 text-primary"></i> <?= substr($j->jam_mulai, 0, 5) ?> - <?= substr($j->jam_selesai, 0, 5) ?></strong></td>
                            <td><strong style="color: var(--primary-blue);"><?= htmlspecialchars($j->nama_mapel) ?></strong></td>
                            <td><span class="badge bg-primary text-white fs-6 px-3 py-1" style="border-radius: 8px;"><i class="bi bi-door-open me-1"></i> <?= htmlspecialchars($j->nama_kelas) ?></span></td>
                            <td><?= htmlspecialchars($j->nama_jurusan ?? '-') ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($j->ruang ?? 'Ruang Kelas') ?></span></td>
                            <td><span class="small text-muted"><?= htmlspecialchars($j->tahun) ?> (<?= ucfirst($j->semester) ?>)</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
