<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn-soft-blue" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
        </button>
    </div>
</div>

<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/siswa') ?>">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari NIS atau Nama Siswa..." value="<?= htmlspecialchars($search) ?>" style="border-radius: 8px;">
                </div>
                <div class="col-md-3">
                    <select name="kelas_id" class="form-select bg-light border-0" style="border-radius: 8px;">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach($kelas as $k): ?>
                            <option value="<?= $k->id ?>" <?= $kelas_id == $k->id ? 'selected' : '' ?>><?= htmlspecialchars($k->nama_kelas) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <button class="btn btn-outline-primary" type="submit" style="border-radius: 8px;"><i class="bi bi-search me-1"></i> Filter</button>
                    <?php if(!empty($search) || !empty($kelas_id)): ?>
                        <a href="<?= base_url('admin/siswa') ?>" class="btn btn-outline-danger" style="border-radius: 8px;"><i class="bi bi-x-circle me-1"></i> Reset</a>
                    <?php endif; ?>
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
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>Nama Lengkap</th>
                        <th>L/P</th>
                        <th>Kelas</th>
                        <th width="25%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($siswa)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data siswa.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($siswa as $s): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge" style="background-color: var(--light-blue); color: var(--dark-blue);"><?= htmlspecialchars($s->nis) ?></span></td>
                            <td><?= htmlspecialchars($s->nisn) ?></td>
                            <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($s->nama) ?></strong></td>
                            <td><?= $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td><?= htmlspecialchars($s->nama_kelas) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $s->id ?>"><i class="bi bi-pencil-square"></i></button>
                                <a href="<?= base_url('admin/barcode_siswa/' . $s->id) ?>" class="btn btn-sm btn-outline-info" style="border-radius: 6px;" target="_blank"><i class="bi bi-qr-code"></i></a>
                                <a href="<?= base_url('admin/siswa/hapus/' . $s->id) ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus data siswa ini? Data akun juga akan terhapus.')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(!empty($siswa)): ?>
    <?php foreach($siswa as $s): ?>
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal<?= $s->id ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url('admin/siswa/edit/' . $s->id) ?>" method="POST">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                        <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Edit Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">NIS</label>
                            <input type="text" name="nis" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($s->nis) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">NISN</label>
                            <input type="text" name="nisn" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($s->nisn) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($s->nama) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                                <option value="L" <?= $s->jenis_kelamin === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= $s->jenis_kelamin === 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Kelas</label>
                            <select name="kelas_id" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                                <?php foreach($kelas as $k): ?>
                                    <option value="<?= $k->id ?>" <?= $s->kelas_id == $k->id ? 'selected' : '' ?>><?= htmlspecialchars($k->nama_kelas) ?></option>
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
        <form action="<?= base_url('admin/siswa/tambah') ?>" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        Akun login akan otomatis dibuat.<br>
                        Username = <strong>NIS</strong><br>
                        Password Default = <strong>123456</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis" class="form-control" required placeholder="NIS Siswa">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn" class="form-control" required placeholder="NISN Siswa">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach($kelas as $k): ?>
                                <option value="<?= $k->id ?>"><?= htmlspecialchars($k->nama_kelas) ?></option>
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
