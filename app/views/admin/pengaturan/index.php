<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);"><?= htmlspecialchars($title) ?></h1>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card card-modern border-0">
            <div class="card-header bg-white fw-bold py-3 border-bottom" style="border-color: var(--border-blue) !important; color: var(--dark-blue);">
                <i class="bi bi-calendar-check me-2" style="color: var(--medium-blue);"></i> Pengaturan Tahun Ajaran
            </div>
            <div class="card-body">
                <p class="mb-3 text-secondary">
                    Tahun Ajaran Aktif Saat Ini: 
                    <?php if (!empty($active_tahun)): ?>
                        <?php foreach($active_tahun as $ta): ?>
                            <div class="mb-2">
                                <strong class="text-success"><?= htmlspecialchars($ta->tahun) ?> (<?= ucfirst(htmlspecialchars($ta->semester)) ?>)</strong>
                                <span class="badge bg-success ms-2">Aktif</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <strong class="text-danger">Belum ada yang aktif</strong>
                    <?php endif; ?>
                </p>
                <p class="text-muted small">
                    Mengelola daftar tahun ajaran untuk sistem absensi. Tahun ajaran yang aktif akan digunakan secara default oleh sistem untuk mencatat data absensi siswa dan guru.
                </p>
                <a href="<?= base_url('admin/tahun-ajaran') ?>" class="btn btn-primary" style="border-radius: 8px;">
                    Kelola Tahun Ajaran <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card card-modern border-0">
            <div class="card-header bg-white fw-bold py-3 border-bottom" style="border-color: var(--border-blue) !important; color: var(--dark-blue);">
                <i class="bi bi-info-circle me-2" style="color: var(--medium-blue);"></i> Informasi Sistem
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush text-secondary">
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        Versi Aplikasi
                        <span class="badge bg-secondary rounded-pill">v1.0.0</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        Waktu Server
                        <span class="text-dark fw-bold"><?= date('d M Y, H:i') ?></span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        Zona Waktu
                        <span class="text-dark fw-bold"><?= date_default_timezone_get() ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card card-modern border-0">
            <div class="card-header bg-white fw-bold py-3 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--border-blue) !important; color: var(--dark-blue);">
                <span><i class="bi bi-robot me-2" style="color: var(--medium-blue);"></i> Konfigurasi AI Chatbot</span>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.72rem;">Aktif</span>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <span class="text-secondary small d-block mb-1">Model AI Aktif:</span>
                    <strong class="text-primary font-monospace" style="font-size: 0.88rem;"><?= htmlspecialchars($aiConfig['model'] ?? 'meta/llama-3.2-11b-vision-instruct') ?></strong>
                </div>
                <div class="mb-3">
                    <span class="text-secondary small d-block mb-1">Status API Key:</span>
                    <?php if (!empty($aiConfig['api_key'])): ?>
                        <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-key-fill me-1"></i> Terkonfigurasi (<?= substr(htmlspecialchars($aiConfig['api_key']), 0, 12) ?>...)</span>
                    <?php else: ?>
                        <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Diisi</span>
                    <?php endif; ?>
                </div>
                <p class="text-muted small">
                    Kelola API Key, pilih dari puluhan model AI yang tersedia (Llama 3.2, Llama 3.1 70B, Mistral, Nemotron, DeepSeek), atau uji coba respon AI.
                </p>
                <a href="<?= base_url('admin/pengaturan_ai') ?>" class="btn btn-primary" style="border-radius: 8px;">
                    Kelola Konfigurasi AI <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
