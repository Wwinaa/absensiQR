<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn-soft-blue" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Guru
        </button>
    </div>
</div>

<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/guru') ?>">
            <div class="input-group w-100 w-md-50">
                <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari NIP atau Nama Guru..." value="<?= htmlspecialchars($search) ?>" style="border-radius: 8px 0 0 8px;">
                <button class="btn btn-outline-primary" type="submit" style="border-radius: 0 8px 8px 0;"><i class="bi bi-search me-1"></i> Cari</button>
                <?php if(!empty($search)): ?>
                    <a href="<?= base_url('admin/guru') ?>" class="btn btn-outline-danger ms-2" style="border-radius: 8px;"><i class="bi bi-x-circle me-1"></i> Reset</a>
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
                        <th>NIP</th>
                        <th>Nama Guru</th>
                        <th>Tugas / Peran</th>
                        <th>Mata Pelajaran Diajar</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($guru)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data guru.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($guru as $g): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge" style="background-color: var(--light-blue); color: var(--dark-blue);"><?= htmlspecialchars($g->nip) ?></span></td>
                            <td><strong style="color: var(--dark-blue);"><?= htmlspecialchars($g->nama) ?></strong></td>
                            <td>
                                <?php if(!empty($g->wali_kelas_nama)): ?>
                                    <span class="badge" style="background-color: #ede9fe; color: #6d28d9; border-radius: 6px; font-size: 0.82rem;">
                                        <i class="bi bi-person-workspace me-1"></i> Wali Kelas <?= htmlspecialchars($g->wali_kelas_nama) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary border" style="border-radius: 6px; font-size: 0.82rem;">Guru Pengajar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($g->mapel_diajar)): ?>
                                    <?php 
                                        $mapels = array_map('trim', explode(',', $g->mapel_diajar));
                                        foreach($mapels as $mpl):
                                            if(empty($mpl)) continue;
                                    ?>
                                        <span class="badge bg-info text-dark me-1 mb-1" style="border-radius: 6px; font-weight: 500; font-size: 0.8rem;"><?= htmlspecialchars($mpl) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">Belum ada mapel</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/barcode_guru/' . $g->id) ?>" class="btn btn-sm btn-outline-info" style="border-radius: 6px;" target="_blank" title="Lihat & Cetak QR Code">
                                    <i class="bi bi-qr-code me-1"></i> QR Code
                                </a>
                                <button class="btn btn-sm btn-outline-warning" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $g->id ?>" title="Edit Guru"><i class="bi bi-pencil-square"></i></button>
                                <a href="<?= base_url('admin/guru/hapus/' . $g->id) ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus data guru ini? Data akun login juga akan terhapus.')" title="Hapus Guru"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if(!empty($guru)): ?>
    <?php foreach($guru as $g): ?>
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal<?= $g->id ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url('admin/guru/edit/' . $g->id) ?>" method="POST">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                        <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Edit Guru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">NIP</label>
                            <input type="text" name="nip" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($g->nip) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($g->nama) ?>" required>
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
        <form action="<?= base_url('admin/guru/tambah') ?>" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        Akun login akan otomatis dibuat.<br>
                        Username = <strong>NIP</strong><br>
                        Password Default = <strong>123456</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" required placeholder="NIP Guru (Sebagai Username)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
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
