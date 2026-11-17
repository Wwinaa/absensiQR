<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <div class="card card-modern border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-primary fw-bold"><i class="bi bi-person-bounding-box me-2"></i> Identitas & Profil Akun</h4>
                <span class="badge badge-soft-primary px-3 py-2 text-capitalize fs-6">
                    <i class="bi bi-shield-check me-1"></i> <?= str_replace('_', ' ', $profile['role']) ?>
                </span>
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <?php 
                            $userId = $_SESSION['user_id'];
                            $profileExts = ['jpg', 'jpeg', 'png', 'webp'];
                            $avatarUrl = null;
                            foreach ($profileExts as $ext) {
                                if (file_exists("public/uploads/profiles/profile_{$userId}.{$ext}")) {
                                    $avatarUrl = base_url("public/uploads/profiles/profile_{$userId}.{$ext}?v=" . time());
                                    break;
                                }
                            }
                        ?>
                        <?php if ($avatarUrl): ?>
                            <img src="<?= $avatarUrl ?>" class="rounded-circle shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--very-light-blue); background-color: var(--bg-color);" alt="Profile">
                        <?php else: ?>
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm mb-3" style="width: 120px; height: 120px; font-size: 3.5rem; font-weight: 700; border: 4px solid var(--very-light-blue);">
                                <?= strtoupper(substr($profile['nama'] ?: $profile['username'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: var(--dark-blue);"><?= htmlspecialchars($profile['nama'] ?: $profile['username']) ?></h4>
                    <p class="text-secondary small mb-3">Kelola informasi data diri, kontak, dan keamanan kredensial akun Anda.</p>
                    
                    <div>
                        <button type="button" class="btn btn-sm btn-soft-blue-outline rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                            <i class="bi bi-camera me-1"></i> Ganti Foto
                        </button>
                        <?php if ($avatarUrl): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#deletePhotoModal">
                            <i class="bi bi-trash3 me-1"></i> Hapus Foto
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="text-muted opacity-25 mb-4">
                
                <form action="<?= base_url('profile/update') ?>" method="POST" id="profileForm">
                
                <div class="row g-4 mb-4">
                    <!-- KOLOM 1: IDENTITAS DIRI -->
                    <div class="col-lg-6">
                        <div class="p-3 rounded-4 border bg-light h-100" style="border-color: var(--border-blue) !important;">
                            <?php if ($profile['role'] === 'admin'): ?>
                                <h5 class="fw-bold mb-3" style="color: var(--dark-blue);">
                                    <i class="bi bi-person-gear text-primary me-2"></i>Data Administrator
                                </h5>

                                <div class="mb-3">
                                    <label for="nama" class="form-label fw-semibold text-secondary small">Nama Lengkap Administrator <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-white" id="nama" name="nama" value="<?= htmlspecialchars($profile['nama']) ?>" placeholder="Nama lengkap administrator..." required style="border-radius: 8px;">
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold text-secondary small">Email Administrator</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;"><i class="bi bi-envelope text-muted"></i></span>
                                        <input type="email" class="form-control bg-white border-start-0" id="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" placeholder="admin@domain.com" style="border-radius: 0 8px 8px 0;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="no_telp" class="form-label fw-semibold text-secondary small">No. HP / WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;"><i class="bi bi-whatsapp text-muted"></i></span>
                                        <input type="tel" class="form-control bg-white border-start-0" id="no_telp" name="no_telp" value="<?= htmlspecialchars($profile['no_telp'] ?? '') ?>" placeholder="08xxxxxxxxxx" style="border-radius: 0 8px 8px 0;">
                                    </div>
                                </div>

                                <div class="p-3 bg-white rounded-3 border mt-4" style="border-color: var(--border-blue) !important;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-shield-check text-primary fs-5"></i>
                                        <span class="text-secondary small fw-bold">Detail Peran & Hak Akses</span>
                                    </div>
                                    <div class="text-dark small fw-bold"><?= $profile['detail_role'] ?: 'Hak Akses: Administrator Utama' ?></div>
                                    <div class="text-muted small mt-1">Akun ini memiliki hak akses penuh dalam pengelolaan data sekolah, pengguna, presensi, jadwal, dan konfigurasi sistem SMK NU WARGABINANGUN.</div>
                                </div>
                            <?php else: ?>
                                <h5 class="fw-bold mb-3" style="color: var(--dark-blue);">
                                    <i class="bi bi-person-vcard text-primary me-2"></i>Data Identitas Diri
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="nama" class="form-label fw-semibold text-secondary small">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-white" id="nama" name="nama" value="<?= htmlspecialchars($profile['nama']) ?>" placeholder="Masukkan nama lengkap dengan gelar..." required style="border-radius: 8px;">
                                </div>

                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label fw-semibold text-secondary small">Jenis Kelamin</label>
                                    <select class="form-select bg-white" id="jenis_kelamin" name="jenis_kelamin" style="border-radius: 8px;">
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="L" <?= ($profile['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="P" <?= ($profile['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label for="tempat_lahir" class="form-label fw-semibold text-secondary small">Tempat Lahir</label>
                                        <input type="text" class="form-control bg-white" id="tempat_lahir" name="tempat_lahir" value="<?= htmlspecialchars($profile['tempat_lahir'] ?? '') ?>" placeholder="Kota / Kab. Lahir" style="border-radius: 8px;">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_lahir" class="form-label fw-semibold text-secondary small">Tanggal Lahir</label>
                                        <input type="date" class="form-control bg-white" id="tanggal_lahir" name="tanggal_lahir" value="<?= htmlspecialchars($profile['tanggal_lahir'] ?? '') ?>" style="border-radius: 8px;">
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label for="no_telp" class="form-label fw-semibold text-secondary small">No. HP / WhatsApp</label>
                                        <input type="tel" class="form-control bg-white" id="no_telp" name="no_telp" value="<?= htmlspecialchars($profile['no_telp'] ?? '') ?>" placeholder="08xxxxxxxxxx" style="border-radius: 8px;">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold text-secondary small">Email</label>
                                        <input type="email" class="form-control bg-white" id="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" placeholder="email@domain.com" style="border-radius: 8px;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="alamat" class="form-label fw-semibold text-secondary small">Alamat Lengkap</label>
                                    <textarea class="form-control bg-white" id="alamat" name="alamat" rows="2" placeholder="Alamat domisili / tempat tinggal..." style="border-radius: 8px;"><?= htmlspecialchars($profile['alamat'] ?? '') ?></textarea>
                                </div>

                                <?php if (!empty($profile['detail_role'])): ?>
                                <div class="p-3 bg-white rounded-3 border" style="border-color: var(--border-blue) !important;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-info-circle-fill text-primary"></i>
                                        <span class="text-secondary small fw-bold">Detail Peran & Hak Akses</span>
                                    </div>
                                    <div class="text-dark small fw-semibold"><?= $profile['detail_role'] ?></div>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- KOLOM 2: AKUN & KEAMANAN -->
                    <div class="col-lg-6" id="keamanan">
                        <div class="p-3 rounded-4 border bg-light h-100" style="border-color: var(--border-blue) !important;">
                            <h5 class="fw-bold mb-3" style="color: var(--dark-blue);">
                                <i class="bi bi-shield-lock text-primary me-2"></i>Akun & Keamanan
                            </h5>

                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold text-secondary small">Username Sistem <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;"><i class="bi bi-at text-muted"></i></span>
                                    <input type="text" class="form-control bg-white border-start-0" id="username" name="username" value="<?= htmlspecialchars($profile['username']) ?>" required style="border-radius: 0 8px 8px 0;">
                                </div>
                                <div class="form-text small text-muted">Digunakan saat melakukan login ke sistem absensi.</div>
                            </div>

                            <div class="alert alert-warning border-0 small mb-3 py-2 px-3 rounded-3" style="background-color: rgba(245, 158, 11, 0.15); color: #B45309;">
                                <i class="bi bi-key-fill me-1"></i> Biarkan kolom password kosong jika tidak ingin mengubah password akun Anda.
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_lama" class="form-label fw-semibold text-secondary small">Password Saat Ini</label>
                                <input type="password" class="form-control bg-white" id="password_lama" name="password_lama" placeholder="Ketik password saat ini..." style="border-radius: 8px;">
                            </div>

                            <div class="mb-3">
                                <label for="password_baru" class="form-label fw-semibold text-secondary small">Password Baru</label>
                                <input type="password" class="form-control bg-white" id="password_baru" name="password_baru" placeholder="Ketik password baru (min. 6 karakter)..." style="border-radius: 8px;">
                            </div>

                            <div class="mb-3">
                                <label for="konfirmasi_password" class="form-label fw-semibold text-secondary small">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control bg-white" id="konfirmasi_password" name="konfirmasi_password" placeholder="Ulangi password baru..." style="border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-4 flex-wrap gap-2">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 10px;">
                        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <button type="submit" class="btn btn-soft-blue px-4 py-2 text-uppercase fw-bold shadow-sm" style="border-radius: 10px;">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Profil
                    </button>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
let originalAvatarSrc = "<?= $avatarUrl ?? '' ?>";
let hasOriginalAvatar = <?= $avatarUrl ? 'true' : 'false' ?>;

function showError(msg) {
    const errDiv = document.getElementById('uploadErrorMsg');
    errDiv.textContent = msg;
    errDiv.classList.remove('d-none');
}

function hideError() {
    const errDiv = document.getElementById('uploadErrorMsg');
    errDiv.classList.add('d-none');
}

function previewImageModal(input) {
    hideError();
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Frontend validation
        if (file.size > 2 * 1024 * 1024) {
            showError('Ukuran foto terlalu besar. Maksimal 2 MB.');
            input.value = '';
            return;
        }
        
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showError('Format foto tidak didukung. Gunakan JPG, PNG, atau WEBP.');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('modalAvatarPreview').src = e.target.result;
            document.getElementById('modalAvatarPreview').classList.remove('d-none');
            document.getElementById('modalAvatarInitials').classList.add('d-none');
            document.getElementById('modalAvatarInitials').classList.remove('d-flex');
            
            // Swap button groups
            document.getElementById('btnGroupUploadDefault').classList.add('d-none');
            document.getElementById('btnGroupUploadPreview').classList.remove('d-none');
            document.getElementById('btnGroupUploadPreview').classList.add('d-flex');
        }
        reader.readAsDataURL(file);
    }
}

function cancelPreviewModal() {
    hideError();
    document.getElementById('foto_profil').value = '';
    
    // Revert image
    if (hasOriginalAvatar) {
        document.getElementById('modalAvatarPreview').src = originalAvatarSrc;
        document.getElementById('modalAvatarPreview').classList.remove('d-none');
        document.getElementById('modalAvatarInitials').classList.add('d-none');
        document.getElementById('modalAvatarInitials').classList.remove('d-flex');
    } else {
        document.getElementById('modalAvatarPreview').src = '';
        document.getElementById('modalAvatarPreview').classList.add('d-none');
        document.getElementById('modalAvatarInitials').classList.remove('d-none');
        document.getElementById('modalAvatarInitials').classList.add('d-flex');
    }
    
    // Swap button groups
    document.getElementById('btnGroupUploadDefault').classList.remove('d-none');
    document.getElementById('btnGroupUploadPreview').classList.add('d-none');
    document.getElementById('btnGroupUploadPreview').classList.remove('d-flex');
}

// Reset modal on close
document.getElementById('uploadPhotoModal')?.addEventListener('hidden.bs.modal', function () {
    cancelPreviewModal();
});
</script>

<!-- Upload Photo Modal -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background-color: rgba(255,255,255,0.98);">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="uploadPhotoModalLabel">Ubah Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <form action="<?= base_url('profile/update_photo') ?>" method="POST" enctype="multipart/form-data" id="photoForm">
                    <div class="mb-4">
                        <?php if ($avatarUrl): ?>
                            <img src="<?= $avatarUrl ?>" id="modalAvatarPreview" class="rounded-circle shadow-sm mx-auto" style="width: 160px; height: 160px; object-fit: cover; border: 4px solid var(--very-light-blue); background-color: var(--bg-color);" alt="Profile">
                            <div id="modalAvatarInitials" class="d-none align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm mx-auto" style="width: 160px; height: 160px; font-size: 4rem; font-weight: 700; border: 4px solid var(--very-light-blue);">
                                <?= strtoupper(substr($profile['nama'] ?: $profile['username'], 0, 1)) ?>
                            </div>
                        <?php else: ?>
                            <img src="" id="modalAvatarPreview" class="rounded-circle shadow-sm d-none mx-auto" style="width: 160px; height: 160px; object-fit: cover; border: 4px solid var(--very-light-blue); background-color: var(--bg-color);" alt="Profile">
                            <div id="modalAvatarInitials" class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm mx-auto" style="width: 160px; height: 160px; font-size: 4rem; font-weight: 700; border: 4px solid var(--very-light-blue);">
                                <?= strtoupper(substr($profile['nama'] ?: $profile['username'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div id="uploadErrorMsg" class="alert alert-danger d-none small py-2 mb-3"></div>

                    <input type="file" id="foto_profil" name="foto_profil" class="d-none" accept="image/jpeg, image/png, image/webp" onchange="previewImageModal(this)">
                    
                    <div id="btnGroupUploadDefault" class="px-4 pb-2">
                        <button type="button" class="btn btn-light w-100 py-3 rounded-4 d-flex align-items-center justify-content-center fw-semibold text-dark shadow-sm border" onclick="document.getElementById('foto_profil').click()" style="background-color: #f8faff; border-color: #dce7f9 !important;">
                            <span class="fs-5 me-2">📁</span> Upload dari Perangkat
                        </button>
                    </div>

                    <div id="btnGroupUploadPreview" class="d-none justify-content-center gap-2 mt-2 px-2 pb-2">
                        <button type="submit" class="btn btn-soft-blue px-4 py-2 rounded-pill fw-bold shadow-sm">Simpan Foto</button>
                        <button type="button" class="btn btn-secondary px-4 py-2 rounded-pill shadow-sm" onclick="cancelPreviewModal()">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Photo Modal -->
<div class="modal fade" id="deletePhotoModal" tabindex="-1" aria-labelledby="deletePhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-trash3" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Hapus Foto</h5>
                <p class="text-muted mb-4 small">Apakah Anda yakin ingin menghapus foto profil?</p>
                <form action="<?= base_url('profile/delete_photo') ?>" method="POST" class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 rounded-pill">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.location.hash === '#keamanan') {
            const sec = document.getElementById('keamanan');
            const pwdInput = document.getElementById('password_lama');
            if (sec) {
                sec.scrollIntoView({ behavior: 'smooth' });
                if (pwdInput) setTimeout(() => pwdInput.focus(), 300);
            }
        }
    });
</script>
