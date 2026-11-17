<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
        <p class="text-muted small mb-0">Kelola jadwal pelajaran untuk Guru dan Wali Kelas yang mengajar, dengan pencegahan jadwal bentrok.</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn-soft-blue" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal
        </button>
    </div>
</div>

<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/jadwal_pelajaran') ?>">
            <div class="row g-2">
                <div class="col-md-2">
                    <select name="hari" class="form-select bg-light border-0">
                        <option value="">Semua Hari</option>
                        <option value="senin" <?= ($filters['hari'] == 'senin') ? 'selected' : '' ?>>Senin</option>
                        <option value="selasa" <?= ($filters['hari'] == 'selasa') ? 'selected' : '' ?>>Selasa</option>
                        <option value="rabu" <?= ($filters['hari'] == 'rabu') ? 'selected' : '' ?>>Rabu</option>
                        <option value="kamis" <?= ($filters['hari'] == 'kamis') ? 'selected' : '' ?>>Kamis</option>
                        <option value="jumat" <?= ($filters['hari'] == 'jumat') ? 'selected' : '' ?>>Jumat</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="kelas_id" class="form-select bg-light border-0">
                        <option value="">Semua Kelas</option>
                        <?php foreach($kelas as $k): ?>
                            <option value="<?= $k->id ?>" <?= ($filters['kelas_id'] == $k->id) ? 'selected' : '' ?>><?= $k->nama_kelas ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="pengajar" class="form-select bg-light border-0">
                        <option value="">Semua Pengajar</option>
                        <?php foreach($guru as $g): ?>
                            <option value="guru_<?= $g->id ?>" <?= (($filters['pengajar'] ?? '') == "guru_{$g->id}" || ($filters['guru_id'] ?? '') == $g->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g->nama_lengkap ?? $g->nama) ?><?= !empty($g->wali_kelas_nama) ? ' (Wali Kelas ' . htmlspecialchars($g->wali_kelas_nama) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="mata_pelajaran_id" class="form-select bg-light border-0">
                        <option value="">Semua Mapel</option>
                        <?php foreach($mata_pelajaran as $m): ?>
                            <option value="<?= $m->id ?>" <?= ($filters['mata_pelajaran_id'] == $m->id) ? 'selected' : '' ?>><?= $m->nama_mapel ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex">
                    <button class="btn btn-primary w-100 me-1" type="submit"><i class="bi bi-filter"></i> Filter</button>
                    <a href="<?= base_url('admin/jadwal_pelajaran') ?>" class="btn btn-outline-danger w-100"><i class="bi bi-x-circle"></i> Reset</a>
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
                        <th>Jam Pelajaran</th>
                        <th>Mata Pelajaran</th>
                        <th>Pengajar</th>
                        <th>Kelas</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($jadwal)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data jadwal ditemukan.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($jadwal as $j): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge bg-secondary"><?= ucfirst($j->hari) ?></span></td>
                            <td><strong style="color: var(--dark-blue);"><?= substr($j->jam_mulai, 0, 5) ?> - <?= substr($j->jam_selesai, 0, 5) ?></strong></td>
                            <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($j->nama_mapel) ?></strong></td>
                            <td>
                                <span><?= htmlspecialchars($j->nama_guru) ?></span>
                                <?php if(($j->tipe_pengajar ?? '') === 'Wali Kelas'): ?>
                                    <span class="badge text-white ms-1" style="background-color: #7c3aed; font-size: 0.72rem;">Wali Kelas</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark ms-1" style="font-size: 0.72rem;">Guru</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-primary text-white" style="border-radius: 6px;"><?= htmlspecialchars($j->nama_kelas) ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $j->id ?>" title="Edit Jadwal"><i class="bi bi-pencil-square"></i></button>
                                <a href="<?= base_url('admin/jadwal_pelajaran/hapus/' . $j->id) ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus jadwal ini?')" title="Hapus Jadwal"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(!empty($jadwal)): ?>
    <?php foreach($jadwal as $j): ?>
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal<?= $j->id ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url('admin/jadwal_pelajaran/edit/' . $j->id) ?>" method="POST">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                        <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Edit Jadwal Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary fw-bold">Hari</label>
                                <select name="hari" class="form-select bg-light border-0" required>
                                    <option value="senin" <?= $j->hari == 'senin' ? 'selected' : '' ?>>Senin</option>
                                    <option value="selasa" <?= $j->hari == 'selasa' ? 'selected' : '' ?>>Selasa</option>
                                    <option value="rabu" <?= $j->hari == 'rabu' ? 'selected' : '' ?>>Rabu</option>
                                    <option value="kamis" <?= $j->hari == 'kamis' ? 'selected' : '' ?>>Kamis</option>
                                    <option value="jumat" <?= $j->hari == 'jumat' ? 'selected' : '' ?>>Jumat</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary fw-bold">Tahun Ajaran</label>
                                <select name="tahun_ajaran_id" class="form-select bg-light border-0" required>
                                    <?php foreach($tahun_ajaran as $t): ?>
                                        <option value="<?= $t->id ?>" <?= $j->tahun_ajaran_id == $t->id ? 'selected' : '' ?>><?= $t->tahun ?> (<?= ucfirst($t->semester) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary fw-bold">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control bg-light border-0" value="<?= $j->jam_mulai ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary fw-bold">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control bg-light border-0" value="<?= $j->jam_selesai ?>" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-secondary fw-bold">Mata Pelajaran</label>
                                <select name="mata_pelajaran_id" class="form-select bg-light border-0" required>
                                    <?php foreach($mata_pelajaran as $m): ?>
                                        <option value="<?= $m->id ?>" <?= $j->mata_pelajaran_id == $m->id ? 'selected' : '' ?>><?= htmlspecialchars($m->nama_mapel) ?> (Kelas <?= $m->tingkat ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-secondary fw-bold">Pengajar (Guru atau Wali Kelas)</label>
                                <select name="guru_id" class="form-select bg-light border-0" required>
                                    <option value="">-- Pilih Guru / Pengajar --</option>
                                    <?php foreach($guru as $g): ?>
                                        <?php 
                                            $isSelected = (($j->guru_id ?? null) == $g->id) 
                                                || (($j->nama_pengajar ?? '') === ($g->nama_lengkap ?? $g->nama)); 
                                        ?>
                                        <option value="guru_<?= $g->id ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($g->nama_lengkap ?? $g->nama) ?><?= !empty($g->wali_kelas_nama) ? ' (Wali Kelas ' . htmlspecialchars($g->wali_kelas_nama) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-secondary fw-bold">Kelas</label>
                                <select name="kelas_id" class="form-select bg-light border-0" required>
                                    <?php foreach($kelas as $k): ?>
                                        <option value="<?= $k->id ?>" <?= $j->kelas_id == $k->id ? 'selected' : '' ?>><?= $k->nama_kelas ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px;" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-soft-blue">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/jadwal_pelajaran/tambah') ?>" method="POST">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Tambah Jadwal Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary fw-bold">Hari</label>
                            <select name="hari" class="form-select bg-light border-0" required>
                                <option value="">Pilih Hari</option>
                                <option value="senin">Senin</option>
                                <option value="selasa">Selasa</option>
                                <option value="rabu">Rabu</option>
                                <option value="kamis">Kamis</option>
                                <option value="jumat">Jumat</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary fw-bold">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select bg-light border-0" required>
                                <?php foreach($tahun_ajaran as $t): ?>
                                    <option value="<?= $t->id ?>" <?= ($t->tahun === '2026/2027' || $t->id == 1) ? 'selected' : '' ?>><?= $t->tahun ?> (<?= ucfirst($t->semester) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary fw-bold">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary fw-bold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-secondary fw-bold">Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" class="form-select bg-light border-0" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                <?php foreach($mata_pelajaran as $m): ?>
                                    <option value="<?= $m->id ?>"><?= htmlspecialchars($m->nama_mapel) ?> (Kelas <?= $m->tingkat ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-secondary fw-bold">Pengajar (Guru atau Wali Kelas)</label>
                            <select name="guru_id" class="form-select bg-light border-0" required>
                                <option value="">-- Pilih Guru / Pengajar --</option>
                                <?php foreach($guru as $g): ?>
                                    <option value="guru_<?= $g->id ?>">
                                        <?= htmlspecialchars($g->nama_lengkap ?? $g->nama) ?><?= !empty($g->wali_kelas_nama) ? ' (Wali Kelas ' . htmlspecialchars($g->wali_kelas_nama) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-secondary fw-bold">Kelas</label>
                            <select name="kelas_id" class="form-select bg-light border-0" required>
                                <option value="">Pilih Kelas</option>
                                <?php foreach($kelas as $k): ?>
                                    <option value="<?= $k->id ?>"><?= $k->nama_kelas ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-text small text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i>Sistem otomatis memvalidasi agar tidak terjadi tabrakan jadwal pada pengajar maupun kelas.
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-soft-blue">Simpan Jadwal</button>
                </div>
            </div>
        </form>
    </div>
</div>
