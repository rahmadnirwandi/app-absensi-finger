<hr>
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-3 col-md-10">
                        <div class='bagan_form'>
                            <label for="filter_nm_departemen" class="form-label">Departemen <span class="text-danger">*</span></label>
                            <div class="button-icon-inside">
                                <input type="text" class="input-text" id='filter_nm_departemen' name="filter_nm_departemen" readonly value="{{ Request::get('filter_nm_departemen') }}" />
                                <input type="hidden" id="filter_id_departemen" name='filter_id_departemen' readonly required value="{{ Request::get('filter_id_departemen') }}">
                                <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_departemen') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Departemen' data-modal-width='50%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_departemen|#filter_nm_departemen@data-key-bagan=0@data-btn-close=#closeModalData">
                                    <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                </span>
                                <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                            </div>
                            <div class="message"></div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-10">
                        <div class='bagan_form'>
                            <label for="filter_nm_ruangan" class="form-label">Ruangan <span class="text-danger">*</span></label>
                            <div class="button-icon-inside">
                                <input type="text" class="input-text" id='filter_nm_ruangan' name="filter_nm_ruangan" readonly value="{{ Request::get('filter_nm_ruangan') }}" />
                                <input type="hidden" id="filter_id_ruangan" name='filter_id_ruangan' readonly required value="{{ Request::get('filter_id_ruangan') }}">
                                <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_ruangan') }}" data-modal-key-with-form="#filter_id_departemen" data-modal-pencarian='true' data-modal-title='ruangan' data-modal-width='70%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_ruangan|#filter_nm_ruangan@data-key-bagan=0@data-btn-close=#closeModalData">
                                    <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                </span>
                                <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                            </div>
                            <div class="message"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-10">
                        <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                        <input type="text" class="form-control" name='form_filter_text'
                            value="{{ Request::get('form_filter_text') }}" id='filter_search_text'
                            placeholder="Masukkan Kata">
                    </div>

                    <div class="col-lg-1 col-md-1">
                        <div class="d-grid grap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>


            <div style="overflow-x: auto; max-width: auto;">
                <table class="table border table-responsive-tablet">
                    <thead>
                        <tr>
                            <th class="py-3">Departemen/Bidang</th>
                            <th class="py-3">Ruangan</th>
                            <th class="py-3">Kepala Ruangan</th>
                            <th class="py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($list_data))
                            @foreach($list_data as $key => $item)
                            <?php
                                $style_tr = !empty($item->id_karyawan) ? "style=background:#51a159;color:#fff;" : '';
                            ?>
                            <tr {{$style_tr}}>
                                <td>{{ !empty($item->nm_departemen) ? $item->nm_departemen : ''  }}</td>
                                <td>{{ !empty($item->nm_ruangan) ? $item->nm_ruangan : ''  }}</td>
                                <td>{{ !empty($item->nm_karyawan) ? $item->nm_karyawan : ''  }}</td>
                                <td class='text-right'>
                                    @php
                                        $url = url($router_name->uri.'/setting');
                                        $data_modal_key = [
                                            'nm_departemen' => $item->nm_departemen,
                                            'id_ruangan' => $item->id_ruangan,
                                            'nm_ruangan' => $item->nm_ruangan,
                                        ];
                                    @endphp
                                    @if( (new \App\Http\Traits\AuthFunction)->checkAkses($router_name->uri.'/setting') )
                                        @if(empty($item->id_karyawan))
                                            <a class="btn btn-info modal-remote" style='color:#fff;' href="{{ $url }}"  data-modal-key='{{json_encode($data_modal_key)}}' data-modal-width='30%' data-modal-title='Setting Kepala Ruangan'>
                                                <i class="fa-solid fa-gear"></i> Setting
                                            </a>
                                        @else
                                            <a href="{{ $url }}" class="btn btn-danger modal-remote-delete" data-modal-key={{ $item->id_karyawan }} data-modal-width="30%" data-modal-title="Informasi" data-confirm-message="Hapus Kepala Ruangan atas nama <strong>{{ $item->nm_karyawan }}</strong> ?">
                                                <i class="fa-solid fa-delete-left"></i> Hapus
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @if(!empty($list_data))
                <div class="d-flex justify-content-end">
                    {{ $list_data->withQueryString()->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
