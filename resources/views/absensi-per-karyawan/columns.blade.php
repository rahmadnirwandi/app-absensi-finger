<style>
    .label_status_ab_yes,.label_status_ab_no{
        padding:5px;
        color:444;
    }

    .label_status_ab_yes{
        background-color:#00ff71 ;
    }

    .label_status_ab_no{
        background-color:#ec051e ;
        color:#fff ;
    }

    .label_status_ab_unow{
        background-color:#716f6f17 ;
    }
</style>
<hr style="margin-top:0px">
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <input type="hidden" id="filter_id_mesin" name="filter_id_mesin" value="{{ Request::get('filter_id_mesin') }}" />
                
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-12 col-md-12">
                        <div class="row justify-content-start align-items-end mb-3">

                            <div class="col-lg-3 col-md-10">
                                <div class='input-date-range-bagan'>
                                    <label for="tanggal_data" class="form-label">Tanggal</label>
                                    <span class='icon-bagan-date'></span>
                                    <input type="text" class="form-control input-date-range" id="tanggal_data" placeholder="Tanggal">
                                    <input type="hidden" id="tgl_start" name="filter_date_start" value="{{ Request::get('filter_date_start') }}">
                                    <input type="hidden" id="tgl_end" name="filter_date_end" value="{{ Request::get('filter_date_end') }}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_status_absensi" class="form-label">Status Absensi : </label>
                                    <select class="form-select" id="filter_status_absensi" name="filter_status_absensi"  aria-label="Default select ">
                                        <option value=""  {{ (Request::get('filter_status_absensi')=='') ? 'selected' : '' }}>Semua</option>
                                        <option value="1" {{ (Request::get('filter_status_absensi')=='1') ? 'selected' : '' }}>Tepat Waktu</option>
                                        <option value="2" {{ (Request::get('filter_status_absensi')=='2') ? 'selected' : '' }}>Terlambat</option>
                                    </select>
                                    <div class="message"></div>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_cara_absensi" class="form-label">Cara Absen : </label>
                                    <select class="form-select" id="filter_cara_absensi" name="filter_cara_absensi"  aria-label="Default select ">
                                        <option value=""  {{ (Request::get('filter_cara_absensi')=='') ? 'selected' : '' }}>Semua</option>
                                        <option value="1" {{ (Request::get('filter_cara_absensi')=='1') ? 'selected' : '' }}>Finger</option>
                                        <option value="3" {{ (Request::get('filter_cara_absensi')=='3') ? 'selected' : '' }}>Password</option>
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
                    </div>

                </div>
            </form>


            <div style="overflow-x: auto; max-width: auto;">
                <table class="table border table-responsive-tablet">
                    <thead>
                        <tr>
                            <th class="py-3" style="width: 10%">Tanggal</th>
                            <th class="py-3" style="width: 5%">Jenis Jadwal</th>
                            <th class="py-3" style="width: 5%">Jadwal</th>
                            <th class="py-3" style="width: 5%">Waktu Absen</th>
                            <th class="py-3" style="width: 5%">Waktu Jadwal</th>
                            <th class="py-3" style="width: 15%">Status</th>
                            <th class="py-3" style="width: 10%">Data Mesin</th>
                            <th class="py-3" style="width: 15%">NIP/Nama</th>
                            <th class="py-3" style="width: 5%">Departemen</th>
                            <th class="py-3" style="width: 10%">Mesin</th>
                            <th class="py-3" style="width: 5%">Cara Absen</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($list_data))
                            @foreach($list_data as $key => $item)
                                <?php 

                                    if($item->hasil_status_absensi==1){
                                        $hasil_absensi='<span class="label_status_ab_yes">Tepat Waktu</span>';
                                    }elseif($item->hasil_status_absensi==2){
                                        $hasil_absensi='<span class="label_status_ab_no">Terlambat</span>';
                                    }else{
                                        $hasil_absensi='<span class="label_status_ab_no">""</span>';
                                    }

                                    $cara_absen='';
                                    if($item->verified_mesin==1){
                                        $cara_absen='Finger';
                                    }

                                    if($item->verified_mesin==3){
                                        $cara_absen='Password';
                                    }

                                    $selisih_waktu='';
                                    if($item->hasil_status_absensi==1){
                                        $selisih_waktu.='Lebih Cepat<br>';
                                    }elseif($item->hasil_status_absensi==2){
                                        // $selisih_waktu.='Terlambat<br>';
                                    }
                                    $selisih_waktu.=$item->jam.' jam, ';
                                    $selisih_waktu.=$item->menit.' menit, ';
                                    $selisih_waktu.=$item->detik.' detik';
                                ?>
                                <tr>
                                    <td>{{ !empty($item->tgl_absensi) ? $item->tgl_absensi : ''  }}</td>
                                    <td>{{ !empty($item->nm_jenis_jadwal) ? $item->nm_jenis_jadwal : ''  }}</td>
                                    
                                    <td>{{ !empty($item->nm_jadwal) ? $item->nm_jadwal : ''  }}</td>
                                    <td>{{ !empty($item->jam_absensi) ? $item->jam_absensi : ''  }}</td>
                                    <td>
                                        <div>{{ !empty($item->waktu_buka) ? $item->waktu_buka : ''  }}</div>
                                        <div>S/D</div>
                                        <div>{{ !empty($item->waktu_tutup) ? $item->waktu_tutup : ''  }}</div>
                                    </td>
                                    <td>
                                        <div>{!! $hasil_absensi !!}</div>
                                        <div>{!! $selisih_waktu !!}</div>
                                    </td>
                                    <td>
                                        <div>( {{ !empty($item->id_user) ? $item->id_user : ''  }} )</div>
                                        <div>{{ !empty($item->username) ? $item->username : ''  }}</div>
                                    </td>
                                    <td>
                                        <div>( {{ !empty($item->nip) ? $item->nip : ''  }} )</div>
                                        <div>{{ !empty($item->nm_karyawan) ? $item->nm_karyawan : ''  }}</div>
                                    </td>
                                    <td>
                                        <div>( {{ !empty($item->nm_jabatan) ? $item->nm_jabatan : ''  }} )</div>
                                        <div>{{ !empty($item->nm_departemen) ? $item->nm_departemen : ''  }}</div>
                                    </td>
                                    <td>
                                        <div>( {{ !empty($item->nm_mesin) ? $item->nm_mesin : ''  }} )</div>
                                        <div>{{ !empty($item->lokasi_mesin) ? $item->lokasi_mesin : ''  }}</div>
                                    </td>
                                    <td>{{ $cara_absen }}</td>
                                    
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