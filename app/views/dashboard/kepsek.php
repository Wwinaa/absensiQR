<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);">Dashboard <?= isset($role_name) ? htmlspecialchars($role_name) : '' ?></h1>

</div>

<div class="alert alert-dismissible fade show text-start border-0 shadow-sm badge-soft-success mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> Selamat datang, <strong><?= htmlspecialchars($_SESSION['user_username']) ?></strong>. Anda berhasil login sebagai <?= isset($role_name) ? htmlspecialchars($role_name) : '' ?>.
</div>

<div class="row mt-4">
    <div class="col-md-4 mb-3">
        <div class="card card-modern h-100 border-0 p-2">
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle" style="width: 50px; height: 50px;">
                        <i class="bi bi-bar-chart-fill text-primary fs-4"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold" style="color: var(--dark-blue);">Statistik Kehadiran</h5>
                <p class="card-text text-secondary">Pantau rekap kehadiran harian siswa dan guru.</p>
                <a href="#" class="btn-soft-blue w-100 mt-2">Lihat Laporan</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card card-modern h-100 border-0 p-2">
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle" style="width: 50px; height: 50px;">
                        <i class="bi bi-journal-text text-warning fs-4"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold" style="color: var(--dark-blue);">Catatan Guru</h5>
                <p class="card-text text-secondary">Berikan atau evaluasi catatan performa untuk guru/wali kelas.</p>
                <a href="#" class="btn btn-outline-primary w-100 mt-2" style="border-radius: 8px;">Kelola Catatan</a>
            </div>
        </div>
    </div>
</div>
