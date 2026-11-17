<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn-soft-blue" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Tahun Ajaran
        </button>
    </div>
</div>

<div class="card card-modern border-0">
    <div class="card-body p-0">
        <div class="table-modern-container">
            <table class="table table-modern mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tahun</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($tahun_ajaran)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data tahun ajaran.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($tahun_ajaran as $ta): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($ta->tahun) ?></strong></td>
                            <td><?= ucfirst(htmlspecialchars($ta->semester)) ?></td>
                            <td>
                                <?php if($ta->status === 'aktif'): ?>
                                    <span class="badge badge-soft-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-soft-danger">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($ta->status !== 'aktif'): ?>
                                    <a href="<?= base_url('admin/tahun_ajaran/set_aktif/' . $ta->id) ?>" class="btn btn-sm btn-outline-success" style="border-radius: 6px;" onclick="return confirm('Aktifkan tahun ajaran ini?')"><i class="bi bi-check2-circle"></i> Set Aktif</a>
                                <?php else: ?>
                                    <a href="<?= base_url('admin/tahun_ajaran/set_nonaktif/' . $ta->id) ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="return confirm('Nonaktifkan tahun ajaran ini?')"><i class="bi bi-x-circle"></i> Set Nonaktif</a>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-warning" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $ta->id ?>"><i class="bi bi-pencil-square"></i></button>
                                <a href="<?= base_url('admin/tahun_ajaran/hapus/' . $ta->id) ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php if(!empty($tahun_ajaran)): ?>
    <?php foreach($tahun_ajaran as $ta): ?>
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal<?= $ta->id ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url('admin/tahun_ajaran/edit/' . $ta->id) ?>" method="POST">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                        <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Edit Tahun Ajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Tahun (Format: YYYY/YYYY)</label>
                            <input type="text" name="tahun" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($ta->tahun) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Semester</label>
                            <select name="semester" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                                <option value="ganjil" <?= $ta->semester === 'ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                <option value="genap" <?= $ta->semester === 'genap' ? 'selected' : '' ?>>Genap</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Status</label>
                            <select name="status" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                                <option value="aktif" <?= $ta->status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="tidak_aktif" <?= $ta->status === 'tidak_aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
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
        <form action="<?= base_url('admin/tahun_ajaran/tambah') ?>" method="POST">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Tambah Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Tahun (Format: YYYY/YYYY)</label>
                        <input type="text" name="tahun" class="form-control bg-light border-0" style="border-radius: 8px;" placeholder="2026/2027" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Semester</label>
                        <select name="semester" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Status</label>
                        <select name="status" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                            <option value="tidak_aktif">Tidak Aktif</option>
                            <option value="aktif">Aktif</option>
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
