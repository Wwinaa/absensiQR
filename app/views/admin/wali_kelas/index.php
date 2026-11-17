<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
        <p class="text-muted small mb-0">Kelola data wali kelas, pemantauan kelas, penugasan mata pelajaran, dan QR Code presensi.</p>
    </div>
</div>

<div class="card card-modern border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/wali_kelas') ?>">
            <div class="input-group w-100 w-md-50">
                <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari NIP, Nama Wali Kelas, atau Kelas..." value="<?= htmlspecialchars($search) ?>" style="border-radius: 8px 0 0 8px;">
                <button class="btn btn-outline-primary" type="submit" style="border-radius: 0 8px 8px 0;"><i class="bi bi-search me-1"></i> Cari</button>
                <?php if(!empty($search)): ?>
                    <a href="<?= base_url('admin/wali_kelas') ?>" class="btn btn-outline-danger ms-2" style="border-radius: 8px;"><i class="bi bi-x-circle me-1"></i> Reset</a>
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
                        <th>Nama Wali Kelas</th>
                        <th>Kelas Diampu</th>
                        <th>Mata Pelajaran Diajar</th>
                        <th>Jadwal Mengajar</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($wali_kelas)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data wali kelas.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($wali_kelas as $w): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge" style="background-color: var(--light-blue); color: var(--dark-blue);"><?= htmlspecialchars($w->nip ?? '-') ?></span></td>
                            <td>
                                <strong style="color: var(--dark-blue);"><?= htmlspecialchars($w->nama_lengkap) ?></strong>
                                <div class="small text-muted"><?= ($w->jenis_kelamin ?? 'L') === 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
                            </td>
                            <td>
                                <?php if(!empty($w->nama_kelas)): ?>
                                    <span class="badge bg-primary text-white" style="border-radius: 6px;"><?= htmlspecialchars($w->nama_kelas) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white" style="border-radius: 6px;">Belum Diplot</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($w->mapel_diajar)): ?>
                                    <span class="badge bg-info text-dark" style="border-radius: 6px;"><?= htmlspecialchars($w->mapel_diajar) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">Belum ada mapel</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(($w->total_jadwal ?? 0) > 0): ?>
                                    <span class="badge bg-success" style="border-radius: 6px;"><?= $w->total_jadwal ?> Jam / Sesi</span>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">0 sesi</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/qrcode_wali_kelas/' . $w->id) ?>" class="btn btn-sm btn-outline-info" style="border-radius: 6px;" target="_blank" title="Lihat & Cetak QR Code">
                                    <i class="bi bi-qr-code me-1"></i> QR Code
                                </a>
                                <button class="btn btn-sm btn-outline-warning" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $w->id ?>" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(!empty($wali_kelas)): ?>
    <?php foreach($wali_kelas as $w): ?>
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal<?= $w->id ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url('admin/wali_kelas/edit/' . $w->id) ?>" method="POST">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <div class="modal-header border-bottom-0 bg-light" style="border-radius: 12px 12px 0 0;">
                        <h5 class="modal-title fw-bold" style="color: var(--dark-blue);">Edit Data Wali Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">NIP</label>
                            <input type="text" name="nip" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($w->nip ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Nama Lengkap & Gelar</label>
                            <input type="text" name="nama_lengkap" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($w->nama_lengkap) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select bg-light border-0" style="border-radius: 8px;" required>
                                <option value="L" <?= ($w->jenis_kelamin ?? 'L') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= ($w->jenis_kelamin ?? 'L') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-bold">No. Telepon / WhatsApp</label>
                            <input type="text" name="no_telp" class="form-control bg-light border-0" style="border-radius: 8px;" value="<?= htmlspecialchars($w->no_telp ?? '') ?>">
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
