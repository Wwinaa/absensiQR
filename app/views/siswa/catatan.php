<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-modern border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-modern-container">
                    <table class="table table-modern mb-0 align-middle">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Tanggal</th>
                                <th width="15%">Kategori</th>
                                <th>Isi Catatan</th>
                                <th width="20%">Pembuat</th>
                                <th width="10%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($catatan)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2" style="color: var(--light-blue);"></i>Tidak ada data catatan pembinaan untuk Anda.</td></tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($catatan as $c): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($c->created_at)) ?></td>
                                    <td><span class="badge" style="background-color: var(--very-light-blue); color: var(--medium-blue);"><?= htmlspecialchars($c->kategori) ?></span></td>
                                    <td><?= htmlspecialchars($c->isi_catatan) ?></td>
                                    <td><?= htmlspecialchars($c->nama_pembuat) ?><br><small class="text-muted"><?= htmlspecialchars($c->pembuat_role) ?></small></td>
                                    <?php $isUnread = (empty($c->status_baca) || $c->status_baca === 0 || $c->status_baca === '0' || $c->status_baca === 'Belum Dibaca'); ?>
                                    <td>
                                        <?php if($isUnread): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-envelope me-1"></i> Belum Dibaca</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><i class="bi bi-envelope-open me-1"></i> Dibaca</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($isUnread): ?>
                                            <form action="<?= base_url('siswa/mark_read/' . $c->id) ?>" method="POST" style="display:inline;">
                                                <button type="submit" class="btn btn-sm btn-soft-blue" style="border-radius: 6px;" title="Tandai Sudah Dibaca"><i class="bi bi-check2-all"></i> Tandai Dibaca</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="bi bi-check2"></i> Selesai</span>
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
    </div>
</div>
