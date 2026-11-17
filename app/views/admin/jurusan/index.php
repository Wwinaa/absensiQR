<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn-soft-blue" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Jurusan
        </button>
    </div>
</div>

<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/jurusan') ?>">
            <div class="input-group w-100 w-md-50">
                <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari kode atau nama jurusan..." value="<?= htmlspecialchars($search) ?>" style="border-radius: 8px 0 0 8px;">
                <button class="btn btn-outline-primary" type="submit" style="border-radius: 0 8px 8px 0;"><i class="bi bi-search me-1"></i> Cari</button>
                <?php if(!empty($search)): ?>
                    <a href="<?= base_url('admin/jurusan') ?>" class="btn btn-outline-danger ms-2" style="border-radius: 8px;"><i class="bi bi-x-circle me-1"></i> Reset</a>
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
                        <th>Kode Jurusan</th>
                        <th>Nama Jurusan</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($jurusan)): ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data jurusan.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($jurusan as $j): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge" style="background-color: var(--light-blue); color: var(--dark-blue);"><?= htmlspecialchars($j->kode_jurusan) ?></span></td>
                            <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($j->nama_jurusan) ?></strong></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $j->id ?>"><i class="bi bi-pencil-square"></i></button>
                                <a href="<?= base_url('admin/jurusan/hapus/' . $j->id) ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus jurusan ini?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php if(!empty($jurusan)): ?>
    <?php foreach($jurusan as $j): ?>
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal<?= $j->id ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url('admin/jurusan/edit/' . $j->id) ?>" method="POST">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                        <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Edit Jurusan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Kode Jurusan</label>
                            <input type="text" name="kode_jurusan" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($j->kode_jurusan) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Nama Jurusan</label>
                            <input type="text" name="nama_jurusan" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($j->nama_jurusan) ?>" required>
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
        <form action="<?= base_url('admin/jurusan/tambah') ?>" method="POST">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Tambah Jurusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Kode Jurusan</label>
                        <input type="text" name="kode_jurusan" class="form-control bg-light border-0" style="border-radius: 8px;" required placeholder="Misal: RPL">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" class="form-control bg-light border-0" style="border-radius: 8px;" required placeholder="Rekayasa Perangkat Lunak">
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
