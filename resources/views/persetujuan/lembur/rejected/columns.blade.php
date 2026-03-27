<div class="card border-2 mt-2">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Izin Ditolak</h6>
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
                <thead class="table" style="background-color: #CEE5FF;">
                    <tr class="text-center">
                        <th style="width: 15%">Nama Karyawan</th>
                        <th style="width: 10%" class="text-center">Tgl Pengajuan</th>
                        <th style="width: 15%" class="text-center">Tgl Lembur</th>
                        <th style="width: 8%" class="text-center">Jam</th>
                        <th style="width: 19%" class="text-center">Keterangan</th>
                        <th style="width: 10%" class="text-center">Permohonan</th>
                        <th style="width: 8%" class="text-center">Status</th>
                        <th style="width: 8%" class="text-center">Disetujui</th>
                        <th style="width: 8%" class="text-center">SDI</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($list_data) && count($list_data) > 0)
                        @foreach($list_data as $item)
                           
                        <tr>
                            <td class="fw-bold">{{ $item->nama_pemohon ?? '-' }}</td>
                            <td class="text-center">{{ $item->tgl_pengajuan ?? '-' }}</td>
                            <td class="text-center">{{ $item->tgl_lembur ?? '-' }}</td>
                            <td class="text-nowrap text-center">
                                {{ $item->jam_mulai }} -- {{ $item->jam_selesai }}
                                
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
                            <td class="text-nowrap text-center">
                                {{ $item->nama_approver_level1 ?? '-' }}
                                
                            </td>
                            <td class="text-nowrap text-center">
                                {{ $item->nama_approver_level2 ?? '-' }}
                                
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $(document).on('click', '.btn-view-file', function(e) {
        e.preventDefault();
        
        const fileUrl = $(this).data('file');
        const fileName = $(this).data('name');
        const extension = fileName.split('.').pop().toLowerCase();
        let content = "";

        $('#fileContent').html('<div class="spinner-border text-primary" role="status"></div>');
        
        if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
            content = `<img src="${fileUrl}" class="img-fluid rounded border shadow-sm">`;
        } else if (extension === 'pdf') {
            content = `<iframe src="${fileUrl}" width="100%" height="500px" style="border:none;"></iframe>`;
        } else {
            content = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i><br>
                    Format file <b>${extension}</b> tidak didukung untuk pratinjau.<br>
                    Silakan unduh file untuk melihat kontennya.
                </div>`;
        }
        $('#fileContent').html(content);
        $('#btnDownload').attr('href', fileUrl);
        $('#modalFile').modal('show');
    });
});
</script>