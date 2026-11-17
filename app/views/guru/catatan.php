<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn-soft-blue" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Catatan
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
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Kategori</th>
                        <th>Isi Catatan</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($catatan)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Anda belum membuat catatan pembinaan.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($catatan as $c): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($c->created_at)) ?></td>
                            <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($c->nama_siswa) ?></strong><br><small class="text-muted"><?= htmlspecialchars($c->nis) ?></small></td>
                            <td><?= htmlspecialchars($c->nama_kelas) ?></td>
                            <td><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= htmlspecialchars($c->kategori) ?></span></td>
                            <td>
                                <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($c->isi_catatan) ?>">
                                    <?= htmlspecialchars($c->isi_catatan) ?>
                                </div>
                            </td>
                            <td>
                                <?php if($c->pembuat_user_id == $user_id): ?>
                                    <button type="button" class="btn btn-sm btn-outline-warning" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $c->id ?>"><i class="bi bi-pencil-square"></i></button>
                                    <a href="<?= base_url('guru/catatan_pembinaan/hapus/' . $c->id) ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus catatan ini?')"><i class="bi bi-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modals (Di luar tabel) -->
<?php if(!empty($catatan)): ?>
    <?php foreach($catatan as $c): ?>
        <?php if($c->pembuat_user_id == $user_id): ?>
        <div class="modal fade" id="editModal<?= $c->id ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="<?= base_url('guru/catatan_pembinaan/edit') ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $c->id ?>">
                    <input type="hidden" name="visibilitas" value="Siswa">
                    <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                        <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                            <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Edit Catatan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label text-secondary fw-bold">Kategori</label>
                                <select name="kategori" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                                    <option value="Kehadiran" <?= $c->kategori == 'Kehadiran' ? 'selected' : '' ?>>Kehadiran</option>
                                    <option value="Kedisiplinan" <?= $c->kategori == 'Kedisiplinan' ? 'selected' : '' ?>>Kedisiplinan</option>
                                    <option value="Perilaku" <?= $c->kategori == 'Perilaku' ? 'selected' : '' ?>>Perilaku</option>
                                    <option value="Akademik" <?= $c->kategori == 'Akademik' ? 'selected' : '' ?>>Akademik</option>
                                    <option value="Apresiasi" <?= $c->kategori == 'Apresiasi' ? 'selected' : '' ?>>Apresiasi</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-secondary fw-bold">Isi Catatan</label>
                                <textarea name="isi_catatan" class="form-control bg-light border-0" style="border-radius: 8px;" rows="4" required><?= htmlspecialchars($c->isi_catatan) ?></textarea>
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
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Tambah Modal -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('guru/catatan_pembinaan/tambah') ?>" method="POST">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Tambah Catatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Siswa (Hanya siswa yang Anda ajar)</label>
                        <select name="siswa_id" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php foreach($siswa_list as $s): ?>
                                <option value="<?= $s->id . '_' . $s->kelas_id ?>"><?= htmlspecialchars($s->nama_lengkap) ?> - Kelas <?= htmlspecialchars($s->nama_kelas) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Kategori</label>
                        <select name="kategori" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Kehadiran">Kehadiran</option>
                            <option value="Kedisiplinan">Kedisiplinan</option>
                            <option value="Perilaku">Perilaku</option>
                            <option value="Akademik">Akademik</option>
                            <option value="Apresiasi">Apresiasi</option>
                        </select>
                    </div>
                    <input type="hidden" name="visibilitas" value="Siswa">
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Isi Catatan</label>
                        <textarea name="isi_catatan" class="form-control bg-light border-0" style="border-radius: 8px;" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-soft-blue">Simpan Catatan</button>
                </div>
            </div>
        </form>
    </div>
</div>
