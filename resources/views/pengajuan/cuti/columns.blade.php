<style>
.h-auto {
    height: auto !important;
}


</style>

<div class="card border-2 mt-2 h-auto">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Cuti</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
            <i class="fa-solid fa-circle-info me-2"></i>
            <span>
                Pengajuan cuti <strong>hanya dapat dilakukan</strong> oleh karyawan yang telah
                memiliki masa kerja <strong>minimal 1 tahun</strong>.
            </span>
        </div>
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
                <thead class="table" style="background-color: #CEE5FF;">
                <tr>
                    <th style="width: 15%">Jenis Cuti</th>
                    <th style="width: 10%" class="text-center">Tgl Pengajuan</th>
                    <th style="width: 15%" class="text-center">Tgl Cuti</th>
                    <th style="width: 8%" class="text-center">Jumlah</th>
                    <th style="width: 8%" class="text-center">Sisa</th>
                    <th style="width: 19%" class="text-center">Keterangan</th>
                    <th style="width: 10%" class="text-center">Permohonan</th>
                    <th style="width: 8%" class="text-center">Status</th>
                    <th style="width: 7%" class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @if(!empty($list_data) && count($list_data) > 0)
                        @foreach($list_data as $item)
                            @php
                                $url_update = url($router_name->uri.'/update');
                                $url_delete = url($router_name->uri.'/delete');
                                $data_modal_key = [
                                    'id_pengajuan' => $item->id,
                                ];
                            @endphp
                        <tr>
                            <td class="fw-bold">{{ $item->nm_jenis_cuti ?? '-' }}</td>
                            <td class="text-center">{{ $item->tgl_pengajuan ?? '-' }}</td>
                            <td class="text-center">{{ $item->tgl_mulai ?? '-' }}</td>
                            <td class="text-nowrap text-center">
                                {{ $item->jumlah_hari }}
                                
                            </td>
                            <td class="text-nowrap text-center">
                                {{ $item->sisa_cuti }}
                                
                            </td>
                            <td class="text-nowrap text-center">
                                {{ $item->keterangan }}
                                
                            </td>
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
                                <div class="d-flex flex-column gap-2 align-items-center">
                                    <a class="btn btn-sm btn-info text-white modal-remote"
                                    style='width: 80px;'
                                    href="{{ $url_update }}"
                                    data-modal-key='{{ json_encode($data_modal_key) }}'
                                    data-modal-width="30%"
                                    data-modal-title="Edit Pengajuan Cuti">
                                         Update
                                    </a>

                                    <a class="btn btn-sm btn-danger text-white modal-remote-delete"
                                    style='width: 80px;'
                                    href="{{ $url_delete }}"
                                    data-modal-key="{{ $item->id }}"
                                    data-modal-width="30%"
                                    data-modal-title="Informasi"
                                    data-confirm-message="Hapus Pengajuan <strong>{{ $item->nm_jenis_cuti }}</strong> ?">
                                         Hapus
                                    </a>
                                </div>
                            </td>

                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">Data tidak ditemukan</td>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>