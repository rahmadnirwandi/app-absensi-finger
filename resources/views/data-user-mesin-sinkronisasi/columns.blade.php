<hr style="margin-top:0px">
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">

                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-3 col-md-10">
                        <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                        <input type="text" class="form-control" name='form_filter_text'
                            value="{{ Request::get('form_filter_text') }}" id='filter_search_text'
                            placeholder="Masukkan Kata">
                    </div>

                    <div class="col-lg-3 col-md-10">
                        <div class='bagan_form'>
                            <label for="filter_ip_mesin_asal" class="form-label">Pilih Asal Mesin </label>
                            <div class="button-icon-inside">
                                <input type="text" class="input-text" id='filter_ip_mesin_asal' value="{{ !empty($hasil_ip_asal) ? $hasil_ip_asal : '' }}" />
                                <input type="hidden" id="filter_id_mesin_asal" name="filter_id_mesin_asal" value="{{ Request::get('filter_id_mesin_asal') }}" />
                                <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_mesih_absensi') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Jenis' data-modal-width='40%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_mesin_asal|#filter_ip_mesin_asal|null|#filter_nama_mesin|#filter_lokasi_mesin@data-key-bagan=0@data-btn-close=#closeModalData">
                                    <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                </span>
                                <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>
                            </div>
                            <div class="message"></div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-10">
                        <div class='bagan_form'>
                            <label for="filter_simpan_db" class="form-label">Status Data pada Database : </label>
                            <select class="form-select" id="filter_simpan_db" name="filter_simpan_db"  aria-label="Default select ">
                                <option value=""  {{ (Request::get('filter_simpan_db')=='') ? 'selected' : '' }}>Semua</option>
                                <option value="1" {{ (Request::get('filter_simpan_db')=='1') ? 'selected' : '' }}>Sudah</option>
                                <option value="2" {{ (Request::get('filter_simpan_db')=='2') ? 'selected' : '' }}>Belum</option>
                            </select>
                            <div class="message"></div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-10">
                        <div class='bagan_form'>
                            <label for="filter_duplicate_data" class="form-label">Status Duplicate Data : </label>
                            <select class="form-select" id="filter_duplicate_data" name="filter_duplicate_data"  aria-label="Default select ">
                                <option value=""  {{ (Request::get('filter_duplicate_data')=='') ? 'selected' : '' }}>Semua</option>
                                <option value="1" {{ (Request::get('filter_duplicate_data')=='1') ? 'selected' : '' }}>Tidak Duplicate</option>
                                <option value="2" {{ (Request::get('filter_duplicate_data')=='2') ? 'selected' : '' }}>Duplicate</option>
                            </select>
                            <div class="message"></div>
                        </div>
                    </div>

                    <div class="col-lg-1 col-md-1">
                        <div class="d-grid grap-2">
                            <button type="submit" name='searchbydb' class="btn btn-primary" value=1>
                                <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between mb-2">
                <div>
                    Menampilkan {{ $list_data->firstItem() }} - {{ $list_data->lastItem() }}
                    dari {{ $list_data->total() }} data
                </div>
            </div>

            <div style="overflow-x: auto; max-width: auto;">
                <table class="table border table-responsive-tablet">
                    <thead>
                        <tr>
                            <th class="py-3" style="width: 1%">No.</th>
                            <th class="py-3" style="width: 15%">Nama Mesin</th>
                            <th class="py-3" style="width: 15%">Id User</th>
                            <th class="py-3" style="width: 15%">Username</th>
                            <th class="py-3" style="width: 2%">Group</th>
                            <th class="py-3" style="width: 15%">Privilege</th>
                            <th class="py-3" style="width: 15%">Database</th>
                            <th class="py-3" style="width: 15%">Keterangan</th>
                            <th class="py-3" style="width: 15%">Nama User di Database</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($list_data))
                            @foreach($list_data as $key => $item)
                            <?php
                                $check_database="<span style='color:RED'>Belum Ada</span>";
                                if(!empty($item->db)){
                                    $check_database="<span style='color:#128628'>Ada</span>";
                                }

                                $status_dup=0;
                                $data_duplicate_text="";
                                $data_duplicate_id='';
                                $data_duplicate_name='';
                                $color_red_id='#000';
                                $color_red_name='#000';
                                if(!empty($item->duplicate_id) || !empty($item->duplicate_name)){
                                    $status_dup=1;
                                    $data_duplicate_text="<span style='color:RED'>Duplicate ID USER Mesin</span>";
                                    if(!empty($item->duplicate_id)){
                                        $color_red_id='red';
                                        $data_duplicate_id=!empty($item->duplicate_id_id_user) ? $item->duplicate_id_id_user : '';
                                        $data_duplicate_name=!empty($item->duplicate_id_name) ? $item->duplicate_id_name : '';
                                    }elseif(!empty($item->duplicate_name)){
                                        $color_red_name='red';
                                        $data_duplicate_id=!empty($item->duplicate_name_id_user) ? $item->duplicate_name_id_user : '';
                                        $data_duplicate_name=!empty($item->duplicate_name_name) ? $item->duplicate_name_name : '';
                                    }
                                }

                                $get_privil=(new \App\Models\RefUserInfo())->get_privilege($item->privilege);

                                $id_mesin_absensi=!empty($item->id_mesin_absensi) ? $item->id_mesin_absensi : '';
                                $id_user_mesin=!empty($item->id_user) ? $item->id_user : '';
                                $username_mesin=!empty($item->name) ? $item->name : '';
                                $kode_change=$id_mesin_absensi.'@'.$id_user_mesin;
                            ?>
                            <tr>
                                <td>{{ $list_data->firstItem() + $loop->index }}</td>
                                <td>
                                    <div>{{ !empty($item->nm_mesin) ? $item->nm_mesin : ''  }}</div>
                                    <div>IP : {{ !empty($item->ip_address) ? $item->ip_address : ''  }}</div>
                                    <div>lokasi : {{ !empty($item->lokasi_mesin) ? $item->lokasi_mesin : ''  }}</div>
                                </td>
                                <td>
                                    <a style="color:{{ $color_red_id }}" href="#" data-url="{{ $router_name->uri }}" data-type="text" class="form_text_change" data-value="{{ $id_user_mesin }}"  data-pk="{{ $kode_change }}@id">
                                        <span >{{ $id_user_mesin }}</span>
                                    </a>
                                </td>
                                <td>
                                    <a style="color:{{ $color_red_name }}" href="#" data-url="{{ $router_name->uri }}" data-type="text" class="form_text_change" data-value="{{ $username_mesin }}"  data-pk="{{ $kode_change }}@name">
                                        <span>{{ $username_mesin }}</span>
                                    </a>
                                </td>
                                <td>{{ !empty($item->group) ? $item->group : ''  }}</td>
                                <td>{{ $get_privil  }}</td>
                                <td>{!! $check_database  !!}</td>
                                <td>
                                    @if(!empty($status_dup))
                                        <div>{!! $data_duplicate_text  !!}</div>
                                        <div>Id User  : {{ $data_duplicate_id  }}</div>
                                        <div>Username : {{ $data_duplicate_name  }}</div>
                                    @endif
                                </td>
                                <td>{{ !empty($item->db_name) ? $item->db_name : ''  }}</td>
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
<script src="{{ asset('js/data-user-mesin-sinkronisasi/form.js') }}"></script>
@endpush