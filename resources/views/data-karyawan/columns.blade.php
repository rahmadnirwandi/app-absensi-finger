<hr>
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-12">
                        <div class="row justify-content-start align-items-end mb-3">
                            <div class="col-lg-3 col-md-10">
                                <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                                <input type="text" class="form-control" name='form_filter_text'
                                    value="{{ Request::get('form_filter_text') }}" id='filter_search_text'
                                    placeholder="Masukkan Kata">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="row justify-content-end align-items-end mb-3">
                            <div class="col-lg-3 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_nm_jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                    <div class="button-icon-inside">
                                        <input type="text" class="input-text" id='filter_nm_jabatan' name="filter_nm_jabatan" readonly value="{{ Request::get('filter_nm_jabatan') }}" />
                                        <input type="hidden" id="filter_id_jabatan" name='filter_id_jabatan' readonly required value="{{ Request::get('filter_id_jabatan') }}">
                                        <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_jabatan') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Jabatan' data-modal-width='30%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_jabatan|#filter_nm_jabatan@data-key-bagan=0@data-btn-close=#closeModalData">
                                            <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                        </span>
                                        <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>
                                    </div>
                                    <div class="message"></div>
                                </div>
                            </div>

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
                                <div class='bagan_form'>
                                    <label for="filter_nm_status_karyawan" class="form-label">Status Karyawan <span class="text-danger">*</span></label>
                                    <div class="button-icon-inside">
                                        <input type="text" class="input-text" id='filter_nm_status_karyawan' name="filter_nm_status_karyawan" readonly value="{{ Request::get('filter_nm_status_karyawan') }}" />
                                        <input type="hidden" id="filter_id_status_karyawan" name='filter_id_status_karyawan' readonly required value="{{ Request::get('filter_id_status_karyawan') }}">
                                        <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_status_karyawan') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Departemen' data-modal-width='40%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_status_karyawan|#filter_nm_status_karyawan@data-key-bagan=0@data-btn-close=#closeModalData">
                                            <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                        </span>
                                        <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                                    </div>
                                    <div class="message"></div>
                                </div>
                            </div>

                            <div class="col-lg-1 col-md-1">
                                <div class="d-grid grap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div style="overflow-x: auto; max-width: auto;">
                <table class="table border table-responsive-tablet">
                    <thead>
                        <tr>
                            <th class="py-3" style="width: 5%">NIP</th>
                            <th class="py-3" style="width: 30%">Nama</th>
{{--                            <th class="py-3" style="width: 13%">Alamat</th>--}}
                            <th class="py-3" style="width: 10%">Tgl Masuk</th>
                            <th class="py-3" style="width: 10%">Jabatan</th>
                            <th class="py-3" style="width: 10%">Departemen</th>
                            <th class="py-3" style="width: 10%">Ruangan</th>
                            <th class="py-3" style="width: 10%">Status Karyawan</th>
                            <th class="py-3" style="width: 5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($list_data))
                            @foreach($list_data as $key => $item)
                            <?php
                                $paramater_url=[
                                    'data_sent'=>$item->id_karyawan
                                ];
                            ?>
                            <tr>
                                <td>{{ !empty($item->nip) ? $item->nip : ''  }}</td>
                                <td>{{ !empty($item->nm_karyawan) ? $item->nm_karyawan : ''  }}</td>
{{--                                <td>{{ !empty($item->alamat) ? $item->alamat : ''  }}</td>--}}
                                <td>{{ !empty($item->tgl_masuk) ? $item->tgl_masuk : ''  }}</td>
                                <td>{{ !empty($item->nm_jabatan) ? $item->nm_jabatan : ''  }}</td>
                                <td>{{ !empty($item->nm_departemen) ? $item->nm_departemen : ''  }}</td>
                                <td>{{ !empty($item->nm_ruangan) ? $item->nm_ruangan : ''  }}</td>
                                <td>{{ !empty($item->nm_status_karyawan) ? $item->nm_status_karyawan : ''  }}</td>
                                <td class='text-right'>
                                    {!! (new
                                    \App\Http\Traits\AuthFunction)->setPermissionButton([$router_name->uri.'/update',$paramater_url,'update'])
                                    !!}
                                    {!! (new
                                    \App\Http\Traits\AuthFunction)->setPermissionButton([$router_name->uri.'/delete',$paramater_url,'delete'],['modal'])
                                    !!}
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
