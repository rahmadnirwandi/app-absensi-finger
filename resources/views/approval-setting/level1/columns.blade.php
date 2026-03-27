

<div class="card border-2 mt-2">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Approval</h6>
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
                        <th style="width: 18%">Bagian</th>
                        <th style="width: 18%">Unit</th>
                        <th style="width: 15%">Nama</th>
                        <th style="width: 12%">Jabatan</th>
                        <th style="width: 12%">Jenis Pengajuan</th>
                        <th style="width: 12%">Status</th>
                        <th style="width: 13%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($list_data) && count($list_data) > 0)
                        @foreach($list_data as $item)
                            @php
                                $url_update = url($router_name->uri.'/update');
                                $url_delete = url($router_name->uri.'/delete');
                                $data_modal_key = [
                                    'id_approval_mapping' => $item->id,
                                ];
                            @endphp
                        <tr>
                            <td class="text-center">{{ $item->nm_departemen ?? '-' }}</td>
                            <td>{{ $item->nm_ruangan ?? '-' }}</td>
                            <td class="fw-bold">{{ $item->nm_karyawan ?? '-' }}</td>
                            <td class="text-nowrap text-center">
                                {{ $item->nm_jabatan }}
                                
                            </td>
                            <td class="text-nowrap text-center">
                                {{ $item->jenis_pengajuan }}
                                
                            </td>
                            <td class="text-nowrap text-center">
                                @if($item->status == 1)
                                    Aktif
                                @else
                                    Non Aktif
                                @endif
                                
                            </td>

                            <td class="text-center">
                                <div class="d-flex flex-column gap-2 align-items-center">
                                    <a class="btn btn-sm btn-info modal-remote text-white"
                                    style="width: 80px;"
                                    href="{{ $url_update }}"
                                    data-modal-key='{{ json_encode($data_modal_key) }}'
                                    data-modal-width="30%"
                                    data-modal-title="Edit Approval Level 1">
                                        Update
                                    </a>

                                    <a class="btn btn-sm btn-danger modal-remote-delete text-white"
                                    style="width: 80px;"
                                    href="{{ $url_delete }}"
                                    data-modal-key="{{ $item->id }}"
                                    data-modal-width="30%"
                                    data-modal-title="Informasi"
                                    data-confirm-message="Hapus Approval Level 1 <strong>{{ $item->nm_karyawan }}</strong> ?">
                                        Hapus
                                    </a>
                                </div>
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