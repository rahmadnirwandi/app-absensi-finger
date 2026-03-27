<hr>
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <div class="col-lg-12 mt-3">
                    <div class="row justify-content-start align-items-end mb-3">
                        <div class="col-lg-3 col-md-10">
                            <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                            <input type="text" class="form-control" name='form_filter_text' value="{{ Request::get('form_filter_text') }}" id='filter_search_text' placeholder="Masukkan Kata">
                        </div>

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
                                    <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_ruangan') }}" data-modal-key-with-form="#filter_id_departemen" data-modal-pencarian='true' data-modal-title='Ruangan' data-modal-width='70%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_ruangan|#filter_nm_ruangan@data-key-bagan=0@data-btn-close=#closeModalData">
                                        <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                    </span>
                                    <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                                </div>
                                <div class="message"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row justify-content-start align-items-end mb-3">

                        <div class="col-lg-3 col-md-10">
                            <div class='bagan_form'>
                                <label for="form_jenis_jadwal" class="form-label">Pilih Jadwal : </label>
                                <select class="form-select" id="form_jenis_jadwal" name="form_jenis_jadwal"  aria-label="Default select ">
                                    <option value="" {{ (Request::get('form_jenis_jadwal')=='') ? 'selected' : '' }}>Semua</option>
                                    <option value="non" {{ (Request::get('form_jenis_jadwal')=='non') ? 'selected' : '' }}>Tidak ada jadwal</option>
                                    
                                    @if(!empty($data_jadwal))
                                        @foreach($data_jadwal as $key => $item)
                                            <?php
                                                $item=(object)$item;
                                                $kode=$item->key.'@'.$item->value;
                                                $selected=(Request::get('form_jenis_jadwal')==$kode) ? 'selected' : '';

                                                $title_jadwal=$item->text;
                                                if($item->key==2){
                                                    $title_jadwal='Shift:'.$item->text;
                                                }
                                            ?>
                                            <option value="{{ $kode }}" {{ $selected }} >{{ $title_jadwal }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="message"></div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-10">
                            <div class='bagan_form'>
                                <label for="form_connect_mesin" class="form-label">Connect dengan Mesin : </label>
                                <select class="form-select" id="form_connect_mesin" name="form_connect_mesin"  aria-label="Default select ">
                                    <option value=""  {{ (Request::get('form_connect_mesin')=='') ? 'selected' : '' }}>Semua</option>
                                    <option value="1" {{ (Request::get('form_connect_mesin')=='1') ? 'selected' : '' }}>Ada</option>
                                    <option value="2" {{ (Request::get('form_connect_mesin')=='2') ? 'selected' : '' }}>Tidak ada</option>
                                </select>
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
            </form>

            <div style="overflow-x: auto; max-width: auto;">
                <table class="table border table-responsive-tablet">
                    <thead>
                        <tr>
                            <th class="py-3" style="width: 5%">NIP</th>
                            <th class="py-3" style="width: 20%">Nama</th>
                            <th class="py-3" style="width: 10%">ALamat</th>
                            <th class="py-3" style="width: 10%">Jabatan</th>
                            <th class="py-3" style="width: 10%">Departemen</th>
                            <th class="py-3" style="width: 10%">Ruangan</th>
                            <th class="py-3" style="width: 5%">Id User Mesin</th>
                            <th class="py-3" style="width: 5%">Jenis Jadwal</th>
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
                                
                                $id_jenis_jadwal=!empty($item->id_jenis_jadwal) ? $item->id_jenis_jadwal : '';
                                $id_template_jadwal_shift=!empty($item->id_template_jadwal_shift) ? $item->id_template_jadwal_shift : '';
                                
                                $key_jadwal='';
                                if($id_jenis_jadwal){
                                    $key_jadwal='1@'.$id_jenis_jadwal;
                                }

                                if($id_template_jadwal_shift){
                                    $key_jadwal='2@'.$id_template_jadwal_shift;
                                }

                                $status_user_mesin=$item->status_akun_mesin;
                                $status_user_mesin_text="<span style='color:RED'>Tidak ada</span>";
                                if(!empty($status_user_mesin)){
                                    $status_user_mesin_text="<div style='color:GREEN'>".(!empty($item->id_user) ? $item->id_user : '')."</div>";
                                }
                            ?>
                            <tr>
                                <td>{{ !empty($item->nip) ? $item->nip : ''  }}</td>
                                <td>{{ !empty($item->nm_karyawan) ? $item->nm_karyawan : ''  }}</td>
                                <td>{{ !empty($item->alamat) ? $item->alamat : ''  }}</td>
                                <td>{{ !empty($item->nm_jabatan) ? $item->nm_jabatan : ''  }}</td>
                                <td>{{ !empty($item->nm_departemen) ? $item->nm_departemen : ''  }}</td>
                                <td>{{ !empty($item->nm_ruangan) ? $item->nm_ruangan : ''  }}</td>
                                
                                <td>{!! $status_user_mesin_text  !!}</td>
                                <td class='text-right'>
                                    <a href="#" class="pil_jadwal"
                                        data-source='{{ $data_jadwal_json }}' data-type="select" 
                                        data-value="{{ $key_jadwal  }}"
                                        data-pk="{{ !empty($item->id_karyawan) ? $item->id_karyawan : ''  }}" 
                                        data-url="{{ $router_name->uri }}" 
                                        data-title="Select status">
                                    </a>
                                </td>
                                <td>
                                    <?php $url_atur_waktu='/data-jadwal-karyawan-shift'; ?>
                                    @if( (new \App\Http\Traits\AuthFunction)->checkAkses($url_atur_waktu) )
                                        @if($id_template_jadwal_shift)
                                            <?php
                                                $paramater_url=[
                                                    'data_sent'=>$item->id_karyawan.'@'.$id_template_jadwal_shift,
                                                    'params'=>json_encode(Request::all())
                                                ];
                                                $url_atur_waktu=(new \App\Http\Traits\GlobalFunction)->set_paramter_url($url_atur_waktu,$paramater_url);
                                            ?>
                                            <a href="{{ url($url_atur_waktu) }}" class="btn btn-kecil btn-info" style='color:#555'>Atur Waktu</a>
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

@push('link-end-1')
    <link href="{{ asset('libs\editable\bootstrap5-editable\css\bootstrap-editable.css' )}}" rel="stylesheet" />
@endpush

@push('script-end-1')
<script type="text/javascript" src="{{ asset('bootstrap/js/bootstrap.bundle.min.js' )}}"></script>
<script type="text/javascript" src="{{ asset('libs\editable\bootstrap5-editable\js\bootstrap-editable.min.js' )}}"></script>
@endpush

@push('script-end-2')
<script src="{{ asset('js/data-jadwal-karyawan/form.js') }}"></script>
@endpush