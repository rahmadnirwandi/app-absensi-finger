<div class="modal fade"
     id="modalApprove"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static">

    <div class="modal-dialog modal-dialog-centered modal-md modal-lg modal-top-approved">
        <div class="modal-content rounded-4 shadow-lg">

            <div class="modal-header border-0 px-4 pt-4">
                <h4 class="modal-title ">
                    Konfirmasi Persetujuan
                </h4>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ url('/approver-cuti/approved') }}">
                @csrf
                <input type="hidden" name="pengajuan_id" id="approve_pengajuan_id">

                <div class="modal-body px-4 py-3">
                    <p class="fs-6 text-secondary">
                        Anda akan Menyutujui Pengajuan Cuti Ini.
                        Pastikan informasi sudah benar sebelum melanjutkan.
                    </p>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 d-flex gap-3">
                    <button type="button"
                            class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-success px-4">
                        Ya, Approve
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
