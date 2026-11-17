<!-- ============================================================
     Pengajuan Izin / Sakit — Siswa View
     ============================================================ -->

<style>
.badge-menunggu  { background:#FEF3C7; color:#92400E; border:1px solid #FCD34D; }
.badge-disetujui { background:#D1FAE5; color:#065F46; border:1px solid #6EE7B7; }
.badge-ditolak   { background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; }

.upload-zone {
    border: 2px dashed var(--border-blue);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    background: var(--very-light-blue);
    cursor: pointer;
    transition: all .25s ease;
}
.upload-zone:hover, .upload-zone.dragover {
    background: #E0EEFF;
    border-color: var(--medium-blue);
}
.upload-zone .upload-icon { font-size: 2.5rem; color: var(--soft-blue); }
.upload-zone .upload-label { font-size:.9rem; color:var(--text-secondary); margin-top:.5rem; }
.preview-img { max-height:180px; border-radius:10px; border:2px solid var(--border-blue); object-fit:cover; }
</style>

<!-- Header -->
<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color:var(--border-blue)!important;">
    <h1 class="h2 fw-bold" style="color:var(--dark-blue);">
        <i class="bi bi-envelope-check me-2" style="color:var(--medium-blue);"></i>
        Pengajuan Izin / Sakit
    </h1>
    <button class="btn btn-soft-blue" data-bs-toggle="modal" data-bs-target="#modalBuatPengajuan">
        <i class="bi bi-plus-lg me-1"></i> Buat Pengajuan Baru
    </button>
</div>

<!-- Info box -->
<div class="alert alert-info d-flex gap-2 align-items-start mb-4" style="border-radius:12px; border-left:4px solid #3B82F6; background:#EFF6FF; color:#1E40AF; border-color:#BFDBFE;">
    <i class="bi bi-info-circle-fill mt-1"></i>
    <div>
        <strong>Cara Pengajuan:</strong> Isi form di bawah, lengkapi dengan <u>alasan</u> yang jelas dan <u>bukti foto</u> (surat dokter, surat orang tua, dll). 
        Pengajuan yang tidak menyertakan bukti foto akan <strong>otomatis ditolak</strong>.
    </div>
</div>

<!-- Tabel riwayat pengajuan -->
<div class="card card-modern border-0 mb-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom:1px solid var(--border-blue);">
        <span class="fw-bold" style="color:var(--dark-blue);"><i class="bi bi-list-ul me-2"></i>Riwayat Pengajuan Saya</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive table-modern-container">
            <table class="table table-modern mb-0">
                <thead                    <tr>
                        <th>No</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Dikirim</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($list)): ?>
                        <?php $no = ($page - 1) * 10 + 1; foreach ($list as $p): ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge" style="background:var(--very-light-blue); color:var(--medium-blue);"><?= $no++ ?></span>
                            </td>
                            <td>
                                <?php if ($p->jenis === 'Izin'): ?>
                                    <span class="badge" style="background:#E0F2FE; color:#0369A1; border:1px solid #7DD3FC; padding:.45em .8em; border-radius:8px;">
                                        <i class="bi bi-calendar-x me-1"></i>Izin
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background:#FDF4FF; color:#7E22CE; border:1px solid #D8B4FE; padding:.45em .8em; border-radius:8px;">
                                        <i class="bi bi-heart-pulse me-1"></i>Sakit
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold"><?= date('d-m-Y', strtotime($p->tanggal)) ?></td>
                            <td style="max-width:220px;">
                                <span class="text-truncate d-block" style="max-width:200px;" title="<?= htmlspecialchars($p->alasan) ?>">
                                    <?= htmlspecialchars($p->alasan) ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"
                                        onclick="lihatBuktiSiswa('<?= htmlspecialchars(addslashes($p->jenis)) ?>', '<?= date('d-m-Y', strtotime($p->tanggal)) ?>', '<?= htmlspecialchars(addslashes($p->alasan)) ?>', '<?= $p->bukti_foto ?>')">
                                    <i class="bi bi-image me-1"></i>Lihat
                                </button>
                            </td>
                            <td>
                                <span class="badge bg-success" style="padding:.45em .8em; border-radius:8px;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Tercatat
                                </span>
                            </td>
                            <td class="text-secondary small"><?= date('d/m/Y H:i', strtotime($p->created_at)) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2" style="color:var(--light-blue);"></i>
                                Belum ada pengajuan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="p-3 border-top" style="border-color:var(--border-blue)!important;">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="<?= base_url('siswa/pengajuan') ?>?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ======================================================
     MODAL: Buat Pengajuan Baru
     ====================================================== -->
<div class="modal fade" id="modalBuatPengajuan" tabindex="-1" aria-labelledby="modalPengajuanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:20px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--soft-blue),var(--medium-blue)); color:white;">
                <h5 class="modal-title fw-bold" id="modalPengajuanLabel">
                    <i class="bi bi-envelope-plus me-2"></i>Buat Pengajuan Izin / Sakit
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="<?= base_url('siswa/pengajuan_create') ?>" method="POST" enctype="multipart/form-data" id="formPengajuan" novalidate>
                <div class="modal-body p-4">

                    <!-- Jenis -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color:var(--dark-blue);">
                            Jenis Pengajuan <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis" id="jenisIzin" value="Izin" required>
                                <label class="form-check-label fw-semibold" for="jenisIzin">
                                    <i class="bi bi-calendar-x me-1 text-info"></i>Izin
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis" id="jenisSakit" value="Sakit">
                                <label class="form-check-label fw-semibold" for="jenisSakit">
                                    <i class="bi bi-heart-pulse me-1 text-purple"></i>Sakit
                                </label>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block" id="jenisError" style="display:none!important;"></div>
                    </div>

                    <!-- Tanggal -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color:var(--dark-blue);" for="inputTanggal">
                            Tanggal Tidak Hadir <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal" id="inputTanggal" class="form-control"
                               style="border-radius:10px; border-color:var(--border-blue);"
                               max="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Tanggal wajib diisi.</div>
                    </div>

                    <!-- Alasan -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color:var(--dark-blue);" for="inputAlasan">
                            Alasan <span class="text-danger">*</span>
                        </label>
                        <textarea name="alasan" id="inputAlasan" class="form-control" rows="3"
                                  placeholder="Jelaskan alasan ketidakhadiran Anda secara singkat dan jelas..."
                                  style="border-radius:10px; border-color:var(--border-blue);" required></textarea>
                        <div class="invalid-feedback">Alasan wajib diisi.</div>
                    </div>

                    <!-- Bukti Foto -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold" style="color:var(--dark-blue);">
                            Bukti Foto / Dokumen <span class="text-danger">*</span>
                            <small class="text-muted fw-normal ms-1">(JPG, PNG, WEBP, PDF · maks 5 MB)</small>
                        </label>

                        <div class="upload-zone text-center p-3" id="uploadZone" onclick="document.getElementById('inputBukti').click();">
                            <div id="uploadPrompt" class="py-2">
                                <div class="upload-icon mb-1"><i class="bi bi-cloud-arrow-up fs-2" style="color:var(--soft-blue);"></i></div>
                                <div class="upload-label small text-secondary">
                                    Klik atau seret file ke sini
                                </div>
                            </div>
                            <div id="previewWrap" class="p-2 px-3 bg-white border border-primary rounded-pill shadow-sm align-items-center justify-content-between" style="display:none; max-width: 380px; margin: 0 auto;">
                                <div class="d-flex align-items-center gap-2 text-truncate">
                                    <i class="bi bi-file-earmark-check-fill text-primary fs-5"></i>
                                    <span id="previewName" class="small fw-semibold text-dark text-truncate" style="max-width: 270px;"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" id="btnHapusFoto" aria-label="Hapus" onclick="event.stopPropagation(); hapusPilihanFoto();" title="Hapus foto">
                                    <i class="bi bi-x-circle-fill fs-5"></i>
                                </button>
                            </div>
                        </div>

                        <input type="file" name="bukti_foto" id="inputBukti"
                               accept="image/jpeg,image/png,image/webp,application/pdf"
                               style="display:none;" required>
                        <div class="invalid-feedback d-block" id="fotoError" style="display:none; color:#dc3545; font-size:.875em;"></div>
                    </div>

                </div>
                <div class="modal-footer border-top" style="border-color:var(--border-blue);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px;">
                        Batal
                    </button>
                    <button type="submit" id="btnKirim" class="btn btn-soft-blue">
                        <i class="bi bi-send-check me-1"></i> Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== SCRIPTS ==================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputFile  = document.getElementById('inputBukti');
    const uploadZone = document.getElementById('uploadZone');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const previewWrap = document.getElementById('previewWrap');
    const previewName = document.getElementById('previewName');
    const fotoError   = document.getElementById('fotoError');
    const form        = document.getElementById('formPengajuan');
    const jenisError  = document.getElementById('jenisError');

    // Drag & Drop
    uploadZone.addEventListener('dragover', e => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });
    uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
    uploadZone.addEventListener('drop', e => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            inputFile.files = e.dataTransfer.files;
            handleFilePreview(e.dataTransfer.files[0]);
        }
    });

    inputFile.addEventListener('change', function () {
        if (this.files.length > 0) handleFilePreview(this.files[0]);
    });

    function handleFilePreview(file) {
        fotoError.style.display = 'none';
        uploadZone.style.borderColor = '';
        if (uploadPrompt) uploadPrompt.style.display = 'none';
        previewName.textContent = file.name;
        previewWrap.style.setProperty('display', 'flex', 'important');
    }

    window.hapusPilihanFoto = function() {
        inputFile.value = '';
        if (uploadPrompt) uploadPrompt.style.display = 'block';
        previewWrap.style.setProperty('display', 'none', 'important');
        previewName.textContent = '';
        fotoError.style.display = 'none';
        uploadZone.style.borderColor = '';
    };

    // Form validation
    form.addEventListener('submit', function (e) {
        let valid = true;

        // Cek jenis
        const jenisChecked = document.querySelector('input[name="jenis"]:checked');
        if (!jenisChecked) {
            jenisError.textContent = 'Pilih jenis pengajuan (Izin / Sakit).';
            jenisError.style.display = 'block';
            valid = false;
        } else {
            jenisError.style.display = 'none';
        }

        // Cek tanggal
        const tanggal = document.getElementById('inputTanggal');
        if (!tanggal.value) {
            tanggal.classList.add('is-invalid');
            valid = false;
        } else { tanggal.classList.remove('is-invalid'); }

        // Cek alasan
        const alasan = document.getElementById('inputAlasan');
        if (!alasan.value.trim()) {
            alasan.classList.add('is-invalid');
            valid = false;
        } else { alasan.classList.remove('is-invalid'); }

        // Cek bukti foto (WAJIB)
        if (!inputFile.files || inputFile.files.length === 0) {
            fotoError.textContent = 'Bukti foto wajib disertakan.';
            fotoError.style.display = 'block';
            uploadZone.style.borderColor = '#dc3545';
            valid = false;
        } else {
            fotoError.style.display = 'none';
            uploadZone.style.borderColor = '';
        }

        if (!valid) e.preventDefault();
    });
});

const baseUrlSiswa = '<?= base_url() ?>';

function lihatBuktiSiswa(jenis, tgl, alasan, foto) {
    document.getElementById('buktiSiswaJenis').innerText = jenis;
    document.getElementById('buktiSiswaTanggal').innerText = tgl;
    document.getElementById('buktiSiswaAlasan').innerText = alasan || '-';

    const container = document.getElementById('buktiSiswaImgContainer');
    if (foto && foto.trim() !== '') {
        const url = baseUrlSiswa + 'public/uploads/pengajuan/' + foto;
        const ext = foto.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
            container.innerHTML = `<img src="${url}" alt="Bukti Foto" class="img-fluid rounded shadow-sm" style="max-height: 450px; object-fit: contain; width: 100%; border: 1px solid #CBD5E1;">`;
        } else {
            container.innerHTML = `<iframe src="${url}" style="width: 100%; height: 450px; border-radius: 8px; border: 1px solid #CBD5E1;"></iframe>`;
        }
    } else {
        container.innerHTML = `
            <div class="alert alert-secondary text-center small mb-0">
                <i class="bi bi-info-circle me-1"></i>Tidak ada lampiran foto bukti.
            </div>
        `;
    }

    const modal = new bootstrap.Modal(document.getElementById('modalLihatBuktiSiswa'));
    modal.show();
}
</script>

<!-- Modal Lihat Bukti Foto & Alasan (Siswa) -->
<div class="modal fade" id="modalLihatBuktiSiswa" tabindex="-1" aria-labelledby="modalLihatBuktiSiswaTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header" style="background-color: var(--very-light-blue); border-bottom: 1px solid var(--border-blue); border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold" id="modalLihatBuktiSiswaTitle" style="color: var(--dark-blue);">
                    <i class="bi bi-file-earmark-image me-2 text-primary"></i>Bukti Foto Surat Pengajuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-start p-3 rounded" style="background: var(--very-light-blue); border: 1px solid var(--border-blue);">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-primary fs-6" id="buktiSiswaJenis">-</span>
                        <span class="text-secondary small">Tanggal: <strong id="buktiSiswaTanggal">-</strong></span>
                    </div>
                    <div class="small p-2 bg-white rounded border mt-2">
                        <strong class="text-dark">Alasan Pengajuan:</strong><br>
                        <span id="buktiSiswaAlasan" class="text-secondary">-</span>
                    </div>
                </div>
                <div id="buktiSiswaImgContainer" class="text-center">
                    <img id="buktiSiswaImg" src="" alt="Bukti Foto" class="img-fluid rounded shadow-sm" style="max-height: 450px; object-fit: contain; width: 100%; border: 1px solid #CBD5E1;">
                </div>
            </div>
            <div class="modal-footer bg-white border-0 pt-0 pb-3 d-flex justify-content-end">
                <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="bi bi-x-circle me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
