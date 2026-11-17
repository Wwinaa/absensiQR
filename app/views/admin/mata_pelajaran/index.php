<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
        <p class="text-muted small mb-0">Kelola mata pelajaran dan tentukan guru pengampu (baik Guru maupun Wali Kelas yang mengajar, maks 2 mapel/pengajar).</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn-soft-blue" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mata Pelajaran
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
                        <th>Kode Mapel</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Tingkat Kelas</th>
                        <th>Guru / Pengajar</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($mata_pelajaran)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data mata pelajaran.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($mata_pelajaran as $m): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge" style="background-color: var(--light-blue); color: var(--dark-blue);"><?= htmlspecialchars($m->kode_mapel) ?></span></td>
                            <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($m->nama_mapel) ?></strong></td>
                            <td>
                                <?php if(($m->tingkat ?? '') === 'Semua'): ?>
                                    <span class="badge" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-weight: 500;">Semua Tingkat</span>
                                <?php elseif(!empty($m->tingkat)): ?>
                                    <span class="fw-medium text-secondary">Kelas <?= htmlspecialchars($m->tingkat) ?></span>
                                <?php else: ?>
                                    <span class="badge" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-weight: 500;">Semua Tingkat</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($m->nama_pengajar)): ?>
                                    <span class="fw-semibold"><?= htmlspecialchars($m->nama_pengajar) ?></span>
                                    <?php if(($m->tipe_pengajar ?? '') === 'Wali Kelas'): ?>
                                        <span class="badge text-white ms-1" style="background-color: #7c3aed; font-size: 0.75rem;">Wali Kelas</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark ms-1" style="font-size: 0.75rem;">Guru</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">Belum ada pengajar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $m->id ?>" title="Edit Mapel"><i class="bi bi-pencil-square"></i></button>
                                <a href="<?= base_url('admin/mata_pelajaran/hapus/' . $m->id) ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus mata pelajaran ini?')" title="Hapus Mapel"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(!empty($mata_pelajaran)): ?>
    <?php foreach($mata_pelajaran as $m): ?>
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal<?= $m->id ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url('admin/mata_pelajaran/edit/' . $m->id) ?>" method="POST">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                        <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Edit Mata Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Kode Mapel</label>
                            <input type="text" name="kode_mapel" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($m->kode_mapel) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Nama Mata Pelajaran</label>
                            <input type="text" name="nama_mapel" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($m->nama_mapel) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Tingkat Kelas</label>
                            <select name="tingkat" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                                <option value="Semua" <?= $m->tingkat == 'Semua' ? 'selected' : '' ?>>Semua Tingkat (Lintas Kelas)</option>
                                <option value="X" <?= $m->tingkat == 'X' ? 'selected' : '' ?>>Kelas X</option>
                                <option value="XI" <?= $m->tingkat == 'XI' ? 'selected' : '' ?>>Kelas XI</option>
                                <option value="XII" <?= $m->tingkat == 'XII' ? 'selected' : '' ?>>Kelas XII</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Pengajar (Guru atau Wali Kelas)</label>
                            <select name="guru_id" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                                <option value="">-- Pilih Guru / Pengajar --</option>
                                <?php foreach($guru as $g): ?>
                                    <?php 
                                        $isSelected = (($m->guru_id ?? null) == $g->id) 
                                            || (($m->nama_pengajar ?? '') === ($g->nama_lengkap ?? $g->nama)); 
                                    ?>
                                    <option value="guru_<?= $g->id ?>" <?= $isSelected ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g->nama_lengkap ?? $g->nama) ?><?= !empty($g->wali_kelas_nama) ? ' (Wali Kelas ' . htmlspecialchars($g->wali_kelas_nama) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text small text-muted">Setiap pengajar dapat mengampu maksimal 2 mata pelajaran.</div>
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
        <form action="<?= base_url('admin/mata_pelajaran/tambah') ?>" method="POST">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Tambah Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Kode Mapel</label>
                        <input type="text" name="kode_mapel" class="form-control bg-light border-0" style="border-radius: 8px;" placeholder="Contoh: MP-01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" class="form-control bg-light border-0" style="border-radius: 8px;" placeholder="Nama mata pelajaran..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Tingkat Kelas</label>
                        <select name="tingkat" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="Semua">Semua Tingkat (Lintas Kelas)</option>
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Pengajar (Guru atau Wali Kelas)</label>
                        <select name="guru_id" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                            <option value="">-- Pilih Guru / Pengajar --</option>
                            <?php foreach($guru as $g): ?>
                                <option value="guru_<?= $g->id ?>">
                                    <?= htmlspecialchars($g->nama_lengkap ?? $g->nama) ?><?= !empty($g->wali_kelas_nama) ? ' (Wali Kelas ' . htmlspecialchars($g->wali_kelas_nama) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small text-muted">Setiap pengajar dapat mengampu maksimal 2 mata pelajaran.</div>
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
