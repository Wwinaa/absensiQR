<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
    <?php if($kelas): ?>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn-soft-blue" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Catatan
        </button>
    </div>
    <?php endif; ?>
</div>

<?php if(!$kelas): ?>
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i> Anda belum ditugaskan sebagai wali kelas untuk kelas mana pun.</div>
<?php else: ?>
    <div class="card card-modern border-0">
        <div class="card-body p-0">
            <div class="table-modern-container">
                <table class="table table-modern mb-0 align-middle">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kategori</th>
                            <th>Isi Catatan</th>
                            <th>Pembuat</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($catatan)): ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data catatan pembinaan.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($catatan as $c): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($c->created_at)) ?></td>
                                <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($c->nama_siswa) ?></strong><br><small class="text-muted"><?= htmlspecialchars($c->nis) ?></small></td>
                                <td><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= htmlspecialchars($c->kategori) ?></span></td>
                                <td>
                                    <div style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($c->isi_catatan) ?>">
                                        <?= htmlspecialchars($c->isi_catatan) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($c->nama_pembuat) ?><br><small class="text-muted"><?= htmlspecialchars($c->pembuat_role) ?></small></td>
                                <td>
                                    <?php if($c->pembuat_user_id == $user_id || ($kelas && $c->kelas_id == $kelas->id)): ?>
                                        <button class="btn btn-sm btn-outline-warning" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $c->id ?>" title="Edit Catatan"><i class="bi bi-pencil-square"></i></button>
                                        <a href="<?= base_url('wali-kelas/catatan-pembinaan/hapus/' . $c->id) ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus catatan ini?')" title="Hapus Catatan"><i class="bi bi-trash"></i></a>
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

    <!-- Edit Modals (Di luar tabel agar tidak terpotong container overflow tabel) -->
    <?php if(!empty($catatan)): ?>
        <?php foreach($catatan as $c): ?>
            <?php if($c->pembuat_user_id == $user_id || ($kelas && $c->kelas_id == $kelas->id)): ?>
            <div class="modal fade" id="editModal<?= $c->id ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $c->id ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="<?= base_url('wali-kelas/catatan-pembinaan/edit') ?>" method="POST" class="w-100">
                        <input type="hidden" name="id" value="<?= $c->id ?>">
                        <input type="hidden" name="visibilitas" value="Siswa">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                            <div class="modal-header border-bottom-0 bg-light py-3 px-4" style="border-bottom: 1px solid var(--border-blue) !important;">
                                <div>
                                    <h5 class="modal-title fw-bold mb-0" id="editModalLabel<?= $c->id ?>" style="color: var(--dark-blue);">Edit Catatan</h5>
                                    <small class="text-muted">Siswa: <strong><?= htmlspecialchars($c->nama_siswa) ?></strong> (<?= htmlspecialchars($c->nis) ?>)</small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label text-secondary fw-bold small">Kategori</label>
                                    <select name="kategori" class="form-select bg-light" style="border-radius: 8px; border: 1px solid var(--border-blue);" required>
                                        <option value="Kehadiran" <?= $c->kategori == 'Kehadiran' ? 'selected' : '' ?>>Kehadiran</option>
                                        <option value="Kedisiplinan" <?= $c->kategori == 'Kedisiplinan' ? 'selected' : '' ?>>Kedisiplinan</option>
                                        <option value="Perilaku" <?= $c->kategori == 'Perilaku' ? 'selected' : '' ?>>Perilaku</option>
                                        <option value="Akademik" <?= $c->kategori == 'Akademik' ? 'selected' : '' ?>>Akademik</option>
                                        <option value="Apresiasi" <?= $c->kategori == 'Apresiasi' ? 'selected' : '' ?>>Apresiasi</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-secondary fw-bold small">Isi Catatan</label>
                                    <textarea name="isi_catatan" class="form-control bg-light" style="border-radius: 8px; border: 1px solid var(--border-blue);" rows="4" required><?= htmlspecialchars($c->isi_catatan) ?></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top bg-white px-4 py-3" style="border-color: var(--border-blue) !important;">
                                <button type="button" class="btn btn-outline-secondary px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-soft-blue px-4">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Tambah Modal -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="<?= base_url('wali-kelas/catatan-pembinaan/tambah') ?>" method="POST" class="w-100">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header border-bottom-0 bg-light py-3 px-4" style="border-bottom: 1px solid var(--border-blue) !important;">
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="tambahModalLabel" style="color: var(--dark-blue);">Tambah Catatan</h5>
                            <small class="text-muted">Kelas: <strong><?= htmlspecialchars($kelas->nama_kelas ?? '-') ?></strong></small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold small">Siswa</label>
                            <select name="siswa_id" class="form-select bg-light" style="border-radius: 8px; border: 1px solid var(--border-blue);" required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php foreach($siswa_list as $s): ?>
                                    <option value="<?= $s->id ?>"><?= htmlspecialchars($s->nama_lengkap) ?> (<?= htmlspecialchars($s->nis) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold small">Kategori</label>
                            <select name="kategori" class="form-select bg-light" style="border-radius: 8px; border: 1px solid var(--border-blue);" required>
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
                            <label class="form-label text-secondary fw-bold small">Isi Catatan</label>
                            <textarea name="isi_catatan" class="form-control bg-light" style="border-radius: 8px; border: 1px solid var(--border-blue);" rows="4" placeholder="Tuliskan catatan pembinaan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-white px-4 py-3" style="border-color: var(--border-blue) !important;">
                        <button type="button" class="btn btn-outline-secondary px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-soft-blue px-4">Simpan Catatan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<style>
/* Dark Mode Styling for Wali Kelas Catatan */
[data-bs-theme="dark"] .modal-header.bg-light,
[data-bs-theme="dark"] .modal-footer.bg-white {
    background-color: #0F172A !important;
    border-color: #334155 !important;
    color: #F1F5F9 !important;
}
[data-bs-theme="dark"] .form-control.bg-light,
[data-bs-theme="dark"] .form-select.bg-light {
    background-color: #0F172A !important;
    border-color: #334155 !important;
    color: #F1F5F9 !important;
}
</style>
