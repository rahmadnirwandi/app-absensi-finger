<hr>
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-3 col-md-10">
                        <label for="filter_level_akses" class="form-label">Level Akses</label>
                        <select id="filter_level_akses" class="form-select" name='filter_level_akses' value="{{ Request::get('filter_level_akses') }}">
                            <option value="">Semua Data</option>
                            @foreach ($level_akses_list as $key => $value)
                                <option value="{{ $value->alias }}" {{ Request::get('filter_level_akses') == $value->alias ? 'selected' : '' }}>{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-10">
                        <label for="filter_status_akses" class="form-label">Status Akses</label>
                        <select id="filter_status_akses" class="form-select" name='filter_status_akses' value="{{ Request::get('filter_status_akses') }}">
                            <option value="">Semua Data</option>
                            <?php $status_akses=[1=>'Belum',2=>'Sudah']; ?>
                            @foreach ($status_akses as $key => $value)
                                <option value="{{ $key }}" {{ Request::get('filter_status_akses') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-10">
                        <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                        <input type="text" class="form-control" name='form_filter_text' value="{{ Request::get('form_filter_text') }}" id='filter_search_text' placeholder="Masukkan Kata">
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
                            <th class="py-3" style="width: 15%">Nama Karyawan</th>
                            <th class="py-3" style="width: 15%">Jabatan</th>
                            <th class="py-3" style="width: 15%">Departemen</th>
                            <th class="py-3" style="width: 15%">Username</th>
                            <th class="py-3" style="width: 15%">Level Akses</th>
                            <th class="py-3" style="width: 15%">Status Akun</th>
                            <th class="py-3" style="width: 15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($list_data))
                            @foreach($list_data as $key => $item)
                            <?php
                                $paramater_url=[
                                    'data_sent'=>$item->id_karyawan
                                ];
                                $paramater_url_delete=[
                                    'data_sent'=>$item->id_uxui_users
                                ];

                                $status_user=0;
                                $status_user_text='';
                                if(isset($item->status_user)){
                                    if($item->status_user==1){
                                        $status_user_text='Aktif';
                                        $status_user=1;
                                    }else{
                                        $status_user_text='Tidak Aktif';
                                        $status_user=2;
                                    }
                                }else{
                                    $status_user_text='TIdak ada Akun';
                                    $status_user=0;
                                }
                            ?>
                            <tr>
                                <td>
                                    <div>( {{ !empty($item->nip) ? $item->nip : ''  }} )</div>
                                    <div>{{ !empty($item->nm_karyawan) ? $item->nm_karyawan : ''  }}</div>
                                </td>
                                <td>{{ !empty($item->nm_jabatan) ? $item->nm_jabatan : ''  }}</td>
                                <td>{{ !empty($item->nm_departemen) ? $item->nm_departemen : ''  }}</td>
                                <td>{{ !empty($item->username) ? $item->username : ''  }}</td>
                                <td>{{ !empty($item->nama_group) ? $item->nama_group : ''  }}</td>
                                <td>{{ $status_user_text  }}</td>
                                <td class='text-right'>
                                    {!! (new \App\Http\Traits\AuthFunction)->setPermissionButton([$router_name->uri.'/update',$paramater_url,'update']) !!}
                                    @if(!empty($status_user))
                                        {!! (new \App\Http\Traits\AuthFunction)->setPermissionButton([$router_name->uri.'/delete',$paramater_url_delete,'delete'],['modal',['data-confirm-message'=>'Apakah anda yakin menghapus akun ini ?']]) !!}
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
