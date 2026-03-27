<style>

.modal-top-rejected {
    align-items: flex-start !important;
    margin-top: 8vh;
}
.modal-top-approved {
    align-items: flex-start !important;
    margin-top: 8vh;
}

.btn-confirm {
    width: 90%;
}

@media (min-width: 992px) {
    .modal-top {
        margin-top: 10vh;
    }
}

@media (max-width: 576px) {
    .btn-persetujuan {
        width: 14vh;
    }

    .border-2 {
        border: 1px solid #dee2e6 !important;
    }
    .permohonan {
        font-size: 15px;
    }
}


</style>

<div class="card border-2 mt-2">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Persetujuan Cuti</h6>
    </div>
    <div class="card-body">
        <div class="row justify-content-end mb-3">
            <div class="col-lg-4 col-md-6">
                
                <form action="" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control py-2" name="form_filter_text"
                            value="{{ Request::get('form_filter_text') }}" id="filter_search_text"
                            placeholder="Masukkan kata kunci..." style="border-right: none;">
                        
                        <button type="submit" class="btn btn-primary px-3">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0" style="width:100%; border: 1px solid #dee2e6;">
                <thead class="table-primary">
                   <tr class="text-center align-middle">
                        <th style="width: 18%">Nama Karyawan</th>
                        <th style="width: 13%">Jenis Cuti</th>
                        <th style="width: 11%">Tgl Pengajuan</th>
                        <th style="width: 11%">Tgl Cuti</th>
                        <th style="width: 19%">Keterangan</th>
                        <th style="width: 10%">Permohonan</th>
                        <th style="width: 8%">Status</th>
                        <th style="width: 10%">Aksi</th>
                    </tr>


                </thead>
                <tbody>
                    @if(!empty($list_data) && count($list_data) > 0)
                        @foreach($list_data as $item)
                        <tr>
                            <td class="fw-bold">{{ $item->nm_karyawan ?? '-' }}</td>
                            <td>{{ $item->nm_jenis_cuti ?? '-' }}</td>
                            <td class="text-nowrap text-center">
                                {{ $item->tgl_pengajuan }}
                            </td>
                            <td class="text-nowrap text-center">
                                {{ $item->tgl_mulai }}
                            </td>
                            <td><small>{{ $item->keterangan ?? '-' }}</small></td>
                            <td class="text-start">
                                
                                <div class="permohonan">
                                    @if($item->status === 'rejected' && $item->current_level == 1)
                                        <i class="fa-solid fa-circle-xmark text-danger"></i>
                                    @elseif($item->current_level >= 2)
                                        <i class="fa-solid fa-circle-check text-success"></i>
                                    @elseif($item->current_level == 1 && $item->status === 'pending')
                                        <i class="fa-regular fa-clock text-warning"></i>
                                    @else
                                        <i class="fa-regular fa-circle text-muted"></i>
                                    @endif
                                    Kepala Unit
                                </div>

                                <div class="permohonan">
                                    @if($item->status === 'approved')
                                        <i class="fa-solid fa-circle-check text-success"></i>
                                    @elseif($item->status === 'rejected' && $item->current_level == 2)
                                        <i class="fa-solid fa-circle-xmark text-danger"></i>
                                    @elseif($item->current_level >= 2 && $item->status === 'pending')
                                        <i class="fa-regular fa-clock text-warning"></i>
                                    @else
                                        <i class="fa-regular fa-circle text-muted"></i>
                                    @endif
                                    SDI
                                </div>
                            </td>

                            <td class="text-center">
                                @if($item->status === 'approved')
                                    <span class="badge bg-success rounded-pill">Approved</span>
                                @elseif($item->status === 'rejected')
                                    <span class="badge bg-danger rounded-pill">Rejected</span>
                                @else
                                    <span class="badge bg-warning rounded-pill">Pending</span>
                                @endif
                            </td>
                        

                            <td class="text-center">
                                @if($item->status === 'approved')
                                    <span class="text-muted fst-italic">
                                        <i class="bi bi-check-circle text-success"></i> Selesai
                                    </span>
                                @elseif($item->status === 'rejected')
                                    <span class="text-muted fst-italic">
                                        <i class="bi bi-x-circle text-danger"></i> Ditolak
                                    </span>
                                @elseif($user_level == 1 && $item->current_level == 1)
                                    <div class="d-flex flex-column gap-2 align-items-center">
                                        <button type="button"
                                                class="btn btn-success btn-confirm btn-sm px-2 btn-persetujuan"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalApprove"
                                                data-id="{{ $item->id }}">
                                             Approve
                                        </button>

                                        <button type="button"
                                                class="btn btn-danger btn-confirm btn-sm px-2 btn-persetujuan"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalReject"
                                                data-id="{{ $item->id }}">
                                             Reject
                                        </button>
                                    </div>
                                 @elseif($user_level == 2)
                                    <div class="d-flex flex-column gap-2 align-items-center">
                                        <button type="button"
                                                class="btn btn-success btn-confirm btn-sm px-2 btn-persetujuan"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalApprove"
                                                data-id="{{ $item->id }}">
                                             Approve
                                        </button>

                                        <button type="button"
                                                class="btn btn-danger btn-confirm btn-sm px-2 btn-persetujuan"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalReject"
                                                data-id="{{ $item->id }}">
                                             Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">
                                        <i class="bi bi-hourglass-split text-warning"></i> Menunggu
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">Data tidak ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if(!empty($list_data))
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Showing {{ $list_data->count() }} entries</span>
            {{ $list_data->withQueryString()->links() }}
        </div>
        @endif
    </div>
    
</div>

<div class="modal fade" id="modalFile" tabindex="-1" aria-labelledby="modalFileLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFileLabel">Pratinjau File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="fileContent">
                </div>
            <div class="modal-footer">
                <a href="#" id="btnDownload" class="btn btn-success" download>Download</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@include('persetujuan.cuti.approved')
@include('persetujuan.cuti.rejected')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalApprove = document.getElementById('modalApprove');
        modalApprove.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const pengajuanId = button.getAttribute('data-id');

            console.log('Approve ID:', pengajuanId);
            document.getElementById('approve_pengajuan_id').value = pengajuanId;
        });

        const modalReject = document.getElementById('modalReject');
        modalReject.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const pengajuanId = button.getAttribute('data-id');

            console.log('Reject ID:', pengajuanId);
            document.getElementById('reject_pengajuan_id').value = pengajuanId;
        });

    });
</script>