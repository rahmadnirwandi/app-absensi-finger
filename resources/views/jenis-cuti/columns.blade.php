

<div class="card border-2 mt-2">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Jenis Cuti</h6>
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

                        <th style="width: 5%">No</th>
                        <th style="width: 20%">Nama Cuti</th>
                        <th style="width: 10%">Jumlah Cuti</th>
                        <th style="width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($list_data) && count($list_data) > 0)
                        @foreach($list_data as $key => $item)
                            @php
                                $url_update = url($router_name->uri.'/update');
                                $url_delete = url($router_name->uri.'/delete');
                                $data_modal_key = [
                                    'id' => $item->id,
                                ];
                            @endphp
                        <tr>
                            <td class="text-center font-monospace">{{ $key + 1 }}</td>
                            <td class="fw-bold">{{ $item->nama ?? '-' }}</td>
                            <td class="text-nowrap text-center">
                                {{ $item->jumlah }}
                                
                            </td>
                            <td class="text-center">
                                <div class="btn-group gap-2" role="group">
                                    <a class="btn btn-sm btn-info modal-remote" style='color:#fff;' href="{{ $url_update }}"  data-modal-key='{{json_encode($data_modal_key)}}' data-modal-width='30%' data-modal-title='Edit Jenis Cuti'>
                                         <i class="fa-solid fa-pen-to-square"></i> Update
                                    </a>
                                </div>
                                <div class="btn-group gap-2" role="group">
                                    
                                    <a class="btn btn-sm btn-danger modal-remote-delete" style='color:#fff;' href="{{ $url_delete }}"  data-modal-key={{ $item->id }} data-modal-width='30%' data-modal-title='Informasi' data-confirm-message="Hapus Pengajuan <strong>{{ $item->nama }}</strong> ?">
                                         <i class="fa-solid fa-pen-to-square"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Data tidak ditemukan</td>
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