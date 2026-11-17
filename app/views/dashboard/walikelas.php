<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-blue) !important;">
    <h1 class="h2 fw-bold" style="color: var(--dark-blue);">Dashboard <?= isset($role_name) ? htmlspecialchars($role_name) : '' ?></h1>

</div>

<div class="alert alert-dismissible fade show text-start border-0 shadow-sm badge-soft-success mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> Selamat datang, <strong><?= htmlspecialchars($_SESSION['user_username']) ?></strong>. Anda berhasil login sebagai <?= isset($role_name) ? htmlspecialchars($role_name) : '' ?>.
</div>

<div class="row mt-4">
    <div class="col-md-4 mb-3">
        <div class="card card-modern h-100 border-0 p-2" style="border-left: 5px solid var(--soft-blue) !important;">
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: var(--light-blue);">
                        <i class="bi bi-people text-primary fs-4" style="color: var(--medium-blue) !important;"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold" style="color: var(--dark-blue);">Monitoring Kelas Saya</h5>
                <p class="card-text text-secondary">Pantau rekap kehadiran khusus siswa di kelas Anda.</p>
                <a href="#" class="btn-soft-blue w-100 mt-2">Buka Modul Kelas</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card card-modern h-100 border-0 p-2">
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: var(--very-light-blue);">
                        <i class="bi bi-calendar-event fs-4" style="color: var(--medium-blue);"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold" style="color: var(--dark-blue);">Jadwal Mengajar</h5>
                <p class="card-text text-secondary">Akses jadwal mengajar Anda sebagai Guru.</p>
                <a href="#" class="btn btn-outline-primary w-100 mt-2" style="border-radius: 8px;">Lihat Jadwal</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card card-modern h-100 border-0 p-2">
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: var(--very-light-blue);">
                        <i class="bi bi-clock-history fs-4" style="color: var(--medium-blue);"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold" style="color: var(--dark-blue);">Riwayat Absensi Saya</h5>
                <p class="card-text text-secondary">Lihat data kehadiran Anda sendiri.</p>
                <a href="#" class="btn btn-outline-primary w-100 mt-2" style="border-radius: 8px;">Cek Kehadiran</a>
            </div>
        </div>
    </div>
</div>
