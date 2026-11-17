<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <div>
        <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
        <p class="text-secondary mb-0">Kelola API Key, pilih model AI yang tersedia, dan uji koneksi chatbot sistem absensi sekolah.</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/pengaturan') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Pengaturan
        </a>
    </div>
</div>

<div class="row">
    <!-- Kolom Kiri: Form Konfigurasi Utama -->
    <div class="col-lg-8 mb-4">
        <div class="card card-modern border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--border-blue) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 38px; height: 38px;">
                        <i class="bi bi-robot fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: var(--dark-blue);">Pengaturan API & Model AI</h5>
                        <small class="text-muted">Konfigurasi endpoint NVIDIA NIM / LLM Chatbot</small>
                    </div>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                    <i class="bi bi-check-circle me-1"></i> Sistem AI Aktif
                </span>
            </div>

            <div class="card-body p-4">
                <form action="<?= base_url('admin/simpan_pengaturan_ai') ?>" method="POST" id="aiConfigForm">
                    
                    <!-- 1. API KEY -->
                    <div class="mb-4">
                        <label for="apiKeyInput" class="form-label fw-bold text-dark d-flex align-items-center justify-content-between">
                            <span>
                                <i class="bi bi-key-fill text-warning me-1"></i> API Key AI Chatbot
                                <span class="text-danger">*</span>
                            </span>
                            <small class="text-muted fw-normal">NVIDIA NIM API Key</small>
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control" 
                                   id="apiKeyInput" 
                                   name="api_key" 
                                   value="<?= htmlspecialchars($config['api_key'] ?? '') ?>" 
                                   placeholder="Contoh: nvapi-WYB2K7LZLwRL..." 
                                   required 
                                   style="border-radius: 10px 0 0 10px; font-family: monospace;">
                            <button class="btn btn-outline-secondary" type="button" id="btnToggleKeyVisibility" title="Lihat/Sembunyikan API Key">
                                <i class="bi bi-eye" id="iconEye"></i>
                            </button>
                            <button class="btn btn-primary px-3" type="button" id="btnFetchModels" style="border-radius: 0 10px 10px 0;">
                                <i class="bi bi-arrow-repeat me-1" id="iconFetch"></i>
                                <span>Muat Model Tersedia</span>
                            </button>
                        </div>
                        <div class="form-text mt-1 d-flex justify-content-between align-items-center">
                            <span>Klik <strong>"Muat Model Tersedia"</strong> untuk mengambil seluruh daftar model yang diizinkan pada API Key ini.</span>
                            <a href="https://build.nvidia.com/" target="_blank" rel="noopener noreferrer" class="text-decoration-none small text-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Dapatkan API Key di NVIDIA NIM
                            </a>
                        </div>
                        <div id="fetchModelsAlert" class="mt-2" style="display: none;"></div>
                    </div>

                    <!-- 2. PILIHAN MODELING -->
                    <div class="mb-4">
                        <label for="modelSelect" class="form-label fw-bold text-dark d-flex align-items-center justify-content-between">
                            <span>
                                <i class="bi bi-cpu text-primary me-1"></i> Pilih Model AI (Modeling)
                                <span class="text-danger">*</span>
                            </span>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" id="activeModelBadge">
                                Model Aktif: <?= htmlspecialchars($config['model'] ?? 'nvidia/nemotron-3.5-bolt-e4b') ?>
                            </span>
                        </label>

                        <select class="form-select py-2" id="modelSelect" name="model" style="border-radius: 10px;" required>
                            <optgroup label="⭐ Model Populer & Direkomendasikan" id="optgroupPopular">
                                <?php foreach ($popularModels as $mId => $mName): ?>
                                    <option value="<?= htmlspecialchars($mId) ?>" <?= ($config['model'] === $mId) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($mName) ?> (<?= htmlspecialchars($mId) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                            
                            <optgroup label="📋 Semua Model Tersedia dari API Key" id="optgroupFetched" style="display: none;">
                                <!-- Diisi otomatis lewat AJAX -->
                            </optgroup>

                            <optgroup label="⚙️ Pilihan Kustom">
                                <option value="custom" <?= (!array_key_exists($config['model'], $popularModels)) ? 'selected' : '' ?>>
                                    ✏️ Gunakan Model Kustom Lainnya (Ketik Manual)
                                </option>
                            </optgroup>
                        </select>

                        <!-- Input custom model (jika memilih opsi custom) -->
                        <div class="mt-3" id="customModelWrapper" style="<?= (!array_key_exists($config['model'], $popularModels)) ? 'display:block;' : 'display:none;' ?>">
                            <label for="customModelInput" class="form-label small fw-bold text-secondary">
                                Masukkan Model ID Kustom:
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="customModelInput" 
                                   name="custom_model" 
                                   value="<?= htmlspecialchars($config['model'] ?? '') ?>" 
                                   placeholder="Contoh: nvidia/nemotron-3.5-bolt-e4b atau google/diffusiongemma-26b-a4b-it" 
                                   style="border-radius: 10px; font-family: monospace;">
                            <small class="text-muted">Pastikan format ID model sesuai dengan katalog NVIDIA NIM.</small>
                        </div>
                    </div>

                    <!-- 3. BOX UJI KONEKSI -->
                    <div class="p-3 mb-4 rounded-3 border" style="background: var(--very-light-blue); border-color: var(--border-blue) !important;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <div>
                                <strong style="color: var(--dark-blue);"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Uji Coba Respon AI</strong>
                                <p class="small text-secondary mb-0">Uji apakah API Key dan Model yang dipilih berfungsi normal sebelum disimpan.</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="btnTestConnection">
                                <i class="bi bi-play-fill me-1" id="iconTest"></i>
                                <span>Uji Koneksi Sekarang</span>
                            </button>
                        </div>
                        <div id="testResultBox" class="mt-3" style="display: none;"></div>
                    </div>

                    <!-- Tombol Aksi Simpan -->
                    <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-blue) !important;">
                        <a href="<?= base_url('admin/pengaturan') ?>" class="btn btn-light px-4" style="border-radius: 10px;">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold" style="border-radius: 10px;">
                            <i class="bi bi-save me-1"></i> Simpan Konfigurasi AI
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Panduan & Informasi -->
    <div class="col-lg-4 mb-4">
        <!-- Card Panduan API Key -->
        <div class="card card-modern border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-header bg-white py-3 border-bottom fw-bold" style="border-color: var(--border-blue) !important; color: var(--dark-blue);">
                <i class="bi bi-info-circle-fill text-primary me-2"></i> Panduan Pengaturan AI
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-2" style="color: var(--dark-blue); font-size: 0.9rem;">1. Cara Ganti API Key</h6>
                <p class="text-secondary small mb-3">
                    Tempelkan API Key baru pada kolom input (biasanya diawali dengan <code>nvapi-...</code>). Anda dapat menyembunyikan atau melihat key dengan menekan tombol mata.
                </p>

                <h6 class="fw-bold mb-2" style="color: var(--dark-blue); font-size: 0.9rem;">2. Memilih Model AI</h6>
                <p class="text-secondary small mb-3">
                    Klik tombol <strong>"Muat Model Tersedia"</strong> untuk melihat daftar 80+ model AI yang didukung oleh API key Anda, atau pilih salah satu dari rekomendasi model populer yang sudah kami siapkan.
                </p>

                <h6 class="fw-bold mb-2" style="color: var(--dark-blue); font-size: 0.9rem;">3. Model Rekomendasi</h6>
                <div class="p-2 mb-3 rounded-2 bg-light border small text-secondary">
                    <strong class="text-dark d-block">NVIDIA Nemotron 3.5 Bolt</strong>
                    Sangat fasih berbahasa Indonesia, memahami instruksi jadwal dan kehadiran secara instan, serta memiliki latensi respon yang sangat cepat.
                </div>

                <div class="d-grid">
                    <a href="https://build.nvidia.com/explore/discover" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill">
                        <i class="bi bi-collection me-1"></i> Jelajahi Katalog Model NVIDIA
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Status Terkoneksi -->
        <div class="card card-modern border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white py-3 border-bottom fw-bold" style="border-color: var(--border-blue) !important; color: var(--dark-blue);">
                <i class="bi bi-shield-check text-success me-2"></i> Keamanan & Integrasi
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small text-secondary">
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="bi bi-check2-circle text-success fs-6 mt-1"></i>
                        <span>Tersimpan aman di database sistem dan disinkronkan secara otomatis.</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="bi bi-check2-circle text-success fs-6 mt-1"></i>
                        <span>Chatbot langsung menggunakan model yang Anda pilih tanpa perlu restart server.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="bi bi-check2-circle text-success fs-6 mt-1"></i>
                        <span>Fitur chatbot terintegrasi dengan data absensi real-time SMK NU WARGABINANGUN.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Script Interaktif untuk Pengaturan AI -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiKeyInput = document.getElementById('apiKeyInput');
    const btnToggleVisibility = document.getElementById('btnToggleKeyVisibility');
    const iconEye = document.getElementById('iconEye');
    const btnFetchModels = document.getElementById('btnFetchModels');
    const iconFetch = document.getElementById('iconFetch');
    const modelSelect = document.getElementById('modelSelect');
    const optgroupFetched = document.getElementById('optgroupFetched');
    const customModelWrapper = document.getElementById('customModelWrapper');
    const customModelInput = document.getElementById('customModelInput');
    const fetchModelsAlert = document.getElementById('fetchModelsAlert');
    const btnTestConnection = document.getElementById('btnTestConnection');
    const iconTest = document.getElementById('iconTest');
    const testResultBox = document.getElementById('testResultBox');
    const activeModelBadge = document.getElementById('activeModelBadge');

    const currentSavedModel = "<?= addslashes($config['model'] ?? '') ?>";

    // 1. Toggle Show/Hide API Key
    btnToggleVisibility.addEventListener('click', function() {
        if (apiKeyInput.type === 'password') {
            apiKeyInput.type = 'text';
            iconEye.className = 'bi bi-eye-slash';
        } else {
            apiKeyInput.type = 'password';
            iconEye.className = 'bi bi-eye';
        }
    });

    // 2. Toggle custom model input if "custom" is selected
    modelSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customModelWrapper.style.display = 'block';
            customModelInput.focus();
        } else {
            customModelWrapper.style.display = 'none';
            if (activeModelBadge) {
                activeModelBadge.textContent = 'Pilihan: ' + this.value;
            }
        }
    });

    // 3. Fetch Models dari API Key via AJAX
    btnFetchModels.addEventListener('click', function() {
        const key = apiKeyInput.value.trim();
        if (!key) {
            alert('Silakan masukkan API Key terlebih dahulu.');
            apiKeyInput.focus();
            return;
        }

        // Tampilkan indikator loading
        btnFetchModels.disabled = true;
        iconFetch.className = 'spinner-border spinner-border-sm me-1';
        fetchModelsAlert.style.display = 'block';
        fetchModelsAlert.className = 'alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-0';
        fetchModelsAlert.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menghubungi NVIDIA API untuk memuat seluruh daftar model...';

        const formData = new FormData();
        formData.append('api_key', key);

        fetch("<?= base_url('admin/fetch_ai_models') ?>", {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btnFetchModels.disabled = false;
            iconFetch.className = 'bi bi-arrow-repeat me-1';

            if (data.success && data.models) {
                fetchModelsAlert.className = 'alert alert-success py-2 px-3 small mb-0';
                fetchModelsAlert.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> Berhasil menemukan <strong>${data.count} model</strong> yang siap digunakan untuk API Key ini!`;

                // Kosongkan dan isi optgroupFetched
                optgroupFetched.innerHTML = '';
                optgroupFetched.style.display = 'block';

                data.models.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.name;
                    if (m.id === currentSavedModel || m.id === modelSelect.value) {
                        opt.selected = true;
                    }
                    optgroupFetched.appendChild(opt);
                });

                // Jika model aktif saat ini ada di list, pilih itu
                if (currentSavedModel) {
                    modelSelect.value = currentSavedModel;
                }
            } else {
                fetchModelsAlert.className = 'alert alert-danger py-2 px-3 small mb-0';
                fetchModelsAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${data.message || 'Gagal memuat model.'}`;
            }
        })
        .catch(err => {
            btnFetchModels.disabled = false;
            iconFetch.className = 'bi bi-arrow-repeat me-1';
            fetchModelsAlert.className = 'alert alert-danger py-2 px-3 small mb-0';
            fetchModelsAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> Kesalahan koneksi: ${err.message}`;
        });
    });

    // 4. Uji Coba Koneksi AI Langsung
    btnTestConnection.addEventListener('click', function() {
        const key = apiKeyInput.value.trim();
        let model = modelSelect.value;
        const customModel = customModelInput.value.trim();

        if (model === 'custom') {
            model = customModel;
        }

        if (!key) {
            alert('Masukkan API Key terlebih dahulu untuk pengujian.');
            apiKeyInput.focus();
            return;
        }
        if (!model) {
            alert('Pilih model AI terlebih dahulu.');
            modelSelect.focus();
            return;
        }

        btnTestConnection.disabled = true;
        iconTest.className = 'spinner-border spinner-border-sm me-1';
        testResultBox.style.display = 'block';
        testResultBox.className = 'alert alert-light border p-3';
        testResultBox.innerHTML = `
            <div class="d-flex align-items-center gap-2 text-primary">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <span>Mengirim prompt uji coba ke model <strong>${model}</strong>... Mohon tunggu.</span>
            </div>
        `;

        const formData = new FormData();
        formData.append('api_key', key);
        formData.append('model', model);
        formData.append('custom_model', customModel);

        fetch("<?= base_url('admin/test_ai_connection') ?>", {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btnTestConnection.disabled = false;
            iconTest.className = 'bi bi-play-fill me-1';

            if (data.success) {
                testResultBox.className = 'alert alert-success border p-3';
                testResultBox.innerHTML = `
                    <div class="d-flex align-items-center gap-2 mb-2 text-success fw-bold">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <span>${data.message} (Model: <code>${data.model}</code>)</span>
                    </div>
                    <div class="bg-white p-2 rounded border small text-dark" style="font-family: inherit;">
                        <strong>Respon AI:</strong> "${data.reply}"
                    </div>
                `;
            } else {
                testResultBox.className = 'alert alert-danger border p-3';
                testResultBox.innerHTML = `
                    <div class="d-flex align-items-center gap-2 mb-1 text-danger fw-bold">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                        <span>Uji Koneksi Gagal!</span>
                    </div>
                    <div class="small text-danger">${data.message}</div>
                `;
            }
        })
        .catch(err => {
            btnTestConnection.disabled = false;
            iconTest.className = 'bi bi-play-fill me-1';
            testResultBox.className = 'alert alert-danger border p-3';
            testResultBox.innerHTML = `
                <div class="d-flex align-items-center gap-2 mb-1 text-danger fw-bold">
                    <i class="bi bi-x-circle-fill fs-5"></i>
                    <span>Terjadi Kesalahan!</span>
                </div>
                <div class="small text-danger">${err.message}</div>
            `;
        });
    });
});
</script>
