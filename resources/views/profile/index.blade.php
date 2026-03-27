<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pb-4">
                <div class="user-icon-box mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h5 class="fw-bold" style="color: #094889;">{{ $user->nm_karyawan ?? 'adaabsensi' }}</h5>
                <p class="text-muted small">{{ $user->nama_role ?? 'Super Admin' }}</p>
                <hr>
                <div class="d-grid gap-2">
                    <a href="/edit-password" class="btn btn-outline-secondary btn-sm">Edit Password</a>
                    <a href="{{ url('/') }}/logout" class="btn btn-danger btn-sm">Keluar</a>
                </div>
            </div>
        </div>
    </div>
</div>