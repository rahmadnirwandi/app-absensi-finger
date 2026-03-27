<hr style="margin-top:0px">
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-12 col-md-12">
                        <div class="row justify-content-start align-items-end mb-3">

                            <div class="col-lg-2">
                                <div class='bagan_form'>
                                    <div class='input-month-year-bagan'>
                                        <label for="filter_tahun_bulan" class="form-label">Tahun & Bulan</label>
                                        <span class='icon-bagan-date'></span>
                                        <input type="text" class="form-control input-month" id="filter_tahun_bulan" name='filter_tahun_bulan' placeholder="tahun & bulan" value="{{ !empty(Request::get('filter_tahun_bulan')) ? Request::get('filter_tahun_bulan') : date('Y-m') }}">
                                    </div>
                                    <div class="message"></div>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-10">
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
        
                            <div class="col-lg-2 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_nm_departemen" class="form-label">Departemen <span class="text-danger">*</span></label>
                                    <div class="button-icon-inside">
                                        <input type="text" class="input-text" id='filter_nm_departemen' name="filter_nm_departemen" readonly value="{{ Request::get('filter_nm_departemen') }}" />
                                        <input type="hidden" id="filter_id_departemen" name='filter_id_departemen' readonly required value="{{ Request::get('filter_id_departemen') }}">
                                        <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_departemen') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Departemen' data-modal-width='30%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_departemen|#filter_nm_departemen@data-key-bagan=0@data-btn-close=#closeModalData">
                                            <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                        </span>
                                        <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                                    </div>
                                    <div class="message"></div>
                                </div>
                            </div>
        
                            <div class="col-lg-2 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_nm_ruangan" class="form-label">Ruangan <span class="text-danger">*</span></label>
                                    <div class="button-icon-inside">
                                        <input type="text" class="input-text" id='filter_nm_ruangan' name="filter_nm_ruangan" readonly value="{{ Request::get('filter_nm_ruangan') }}" />
                                        <input type="hidden" id="filter_id_ruangan" name='filter_id_ruangan' readonly required value="{{ Request::get('filter_id_ruangan') }}">
                                        <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_ruangan') }}" data-modal-key-with-form="#filter_id_departemen" data-modal-pencarian='true' data-modal-title='ruangan' data-modal-width='30%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_ruangan|#filter_nm_ruangan@data-key-bagan=0@data-btn-close=#closeModalData">
                                            <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                        </span>
                                        <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                                    </div>
                                    <div class="message"></div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class='bagan_form'>
                                    <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                                    <input type="text" class="form-control" name='form_filter_text' value="{{ Request::get('form_filter_text') }}" id='filter_search_text' placeholder="Masukkan Kata">
                                <div class="message"></div>
                            </div>

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
            </form>
        </div>

        <div style="overflow-x: auto; max-width: auto;">
            <table class="table border table-responsive-tablet">
                <thead>
                    <tr>
                        <th class="py-3" style="width: 5%">Nip</th>
                        <th class="py-3" style="width: 5%">Nama Karyawan</th>
                        <th class="py-3" style="width: 5%">Jabatan</th>
                        <th class="py-3" style="width: 5%">Departemen</th>
                        <th class="py-3" style="width: 5%">Ruangan</th>
                        <th class="py-3" style="width: 10%">Jenis Dinas</th>
                        <th class="py-3" style="width: 10%">Tanggal Pelaksanaan</th>
                        <th class="py-3" style="width: 5%">Lama Dinas</th>
                        <th class="py-3" style="width: 10%">Keterangan</th>
                        <th class="py-3" style="width: 5%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($list_dinas))
                        @foreach($list_dinas as $key => $item)
                        <?php
                            $paramater_url=[
                                'data_sent'=>$item->id_spd
                            ];
                            $jenisDinas="<span style='color:RED'>Dinas Luar Kota</span>";
                            if($item->jenis_dinas == '1'){
                                $jenisDinas="<span style='color:GREEN'>Dinas Dalam Kota</span>";
                            }
                        ?>
                        <tr>
                            <td>{{ !empty($item->nip) ? $item->nip : ''  }}</td>
                            <td>{{ !empty($item->nm_karyawan) ? $item->nm_karyawan : ''  }}</td>
                            <td>{{ !empty($item->nm_jabatan) ? $item->nm_jabatan : ''  }}</td>
                            <td>{{ !empty($item->nm_departemen) ? $item->nm_departemen : ''  }}</td>
                            <td>{{ !empty($item->nm_ruangan) ? $item->nm_ruangan : ''  }}</td>
                            <td>{!! $jenisDinas  !!}</td>
                            <td>{{ !empty($item->tgl_mulai) ? $item->tgl_mulai : ''  }} S/D {{ !empty($item->tgl_selesai) ? $item->tgl_selesai : ''  }}</td>
                            <td>{{ !empty($item->jumlah) ? $item->jumlah : ''  }} Hari</td>
                            <td>{{ !empty($item->uraian) ? $item->uraian : ''  }}</td>
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
    </div>
</div>