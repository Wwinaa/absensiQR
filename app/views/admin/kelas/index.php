<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn-soft-blue" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
        </button>
    </div>
</div>

<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/kelas') ?>">
            <div class="input-group w-100 w-md-50">
                <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari nama kelas atau jurusan..." value="<?= htmlspecialchars($search) ?>" style="border-radius: 8px 0 0 8px;">
                <button class="btn btn-outline-primary" type="submit" style="border-radius: 0 8px 8px 0;"><i class="bi bi-search me-1"></i> Cari</button>
                <?php if(!empty($search)): ?>
                    <a href="<?= base_url('admin/kelas') ?>" class="btn btn-outline-danger ms-2" style="border-radius: 8px;"><i class="bi bi-x-circle me-1"></i> Reset</a>
                <?php endif; ?>
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
                        <th>Tingkat</th>
                        <th>Nama Kelas</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($kelas)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data kelas.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($kelas as $k): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <?php if (empty($k->tingkat)): ?>
                                    <span class="badge" style="background-color: #6c757d; color: #fff;">Tidak diset</span>
                                <?php else: ?>
                                    <span class="badge" style="background-color: var(--soft-blue); color: #fff;">Tingkat <?= htmlspecialchars($k->tingkat) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($k->nama_kelas) ?></strong></td>
                            <td><?= htmlspecialchars($k->nama_jurusan) ?></td>
                            <td><?= $k->nama_wali_kelas ? htmlspecialchars($k->nama_wali_kelas) : '<em class="text-muted">Belum diset</em>' ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $k->id ?>"><i class="bi bi-pencil-square"></i></button>
                                <a href="<?= base_url('admin/kelas/hapus/' . $k->id) ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus kelas ini?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(!empty($kelas)): ?>
    <?php foreach($kelas as $k): ?>
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal<?= $k->id ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url('admin/kelas/edit/' . $k->id) ?>" method="POST">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                        <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Edit Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Tingkat</label>
                            <select name="tingkat" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                                <option value="X" <?= ($k->tingkat === 'X' || $k->tingkat === '10') ? 'selected' : '' ?>>Tingkat X</option>
                                <option value="XI" <?= ($k->tingkat === 'XI' || $k->tingkat === '11') ? 'selected' : '' ?>>Tingkat XI</option>
                                <option value="XII" <?= ($k->tingkat === 'XII' || $k->tingkat === '12') ? 'selected' : '' ?>>Tingkat XII</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($k->nama_kelas) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Jurusan</label>
                            <select name="jurusan_id" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                                <?php foreach($jurusan as $j): ?>
                                    <option value="<?= $j->id ?>" <?= $k->jurusan_id == $j->id ? 'selected' : '' ?>><?= htmlspecialchars($j->nama_jurusan) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Wali Kelas</label>
                            <select name="wali_kelas_id" class="form-select bg-light border-0" style="border-radius: 8px;">
                                <option value="">-- Pilih Wali Kelas --</option>
                                <?php foreach($guru as $g): ?>
                                    <option value="<?= $g->id ?>" <?= $k->wali_kelas_id == $g->id ? 'selected' : '' ?>><?= htmlspecialchars($g->nama_lengkap) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px;" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-soft-blue">Simpan</button>
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
        <form action="<?= base_url('admin/kelas/tambah') ?>" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tingkat</label>
                        <select name="tingkat" class="form-select" required>
                            <option value="X">Tingkat X</option>
                            <option value="XI">Tingkat XI</option>
                            <option value="XII">Tingkat XII</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" required placeholder="Misal: X RPL 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan</label>
                        <select name="jurusan_id" class="form-select" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <?php foreach($jurusan as $j): ?>
                                <option value="<?= $j->id ?>"><?= htmlspecialchars($j->nama_jurusan) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Wali Kelas</label>
                        <select name="wali_kelas_id" class="form-select">
                            <option value="">-- Pilih Wali Kelas --</option>
                            <?php foreach($guru as $g): ?>
                                <option value="<?= $g->id ?>"><?= htmlspecialchars($g->nama_lengkap) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
