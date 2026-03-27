<div class="modal fade"
     id="modalReject"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static">

    <div class="modal-dialog modal-dialog-centered modal-md modal-lg modal-top-rejected">
        <div class="modal-content rounded-4 shadow-lg">

            <div class="modal-header border-0 px-4 pt-4">
                <h4 class="modal-title">
                    Konfirmasi Penolakan
                </h4>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ url('/approver-lembur/rejected') }}">
                @csrf
                <input type="hidden" name="pengajuan_id" id="reject_pengajuan_id">

                <div class="modal-body px-4 py-3">
                    <p class="fs-6 text-secondary mb-2">
                        Silakan masukkan alasan
                    </p>

                    <textarea class="form-control form-control-lg"
                              name="reason"
                              rows="4"
                              placeholder="Contoh: Data tidak sesuai dokumen..."
                              required></textarea>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 d-flex gap-3">
                    <button type="button"
                            class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-danger px-4">
                        Reject
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
