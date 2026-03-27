<hr>
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
                            <th class="py-3" style="width: 3%">Kel. Jadwal</th>
                            <th class="py-3" style="width: 5%">Jenis Jadwal</th>
                            <th class="py-3" style="width: 5%">Nama Uraian</th>
                            <th class="py-3" style="width: 5%">Toleransi <br>Presensi Awal <br> / Cepat Pulang</th>
                            <th class="py-3" style="width: 5%">Buka Presensi</th>
                            <th class="py-3" style="width: 5%">Tutup Presensi</th>
                            <th class="py-3" style="width: 5%">Toleransi Telat</th>
                            <th class="py-3" style="width: 5%">Status Jadwal</th>
                            <th class="py-3" style="width: 5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($list_data))
                            <?php $list_id_jenis=[]; ?>
                            @foreach($list_data as $key => $item)
                            <?php 
                                if(empty($list_id_jenis[$item->id_jenis_jadwal])){ 
                                    $list_id_jenis[$item->id_jenis_jadwal]=1;
                                    
                                    if($key>=1){ 
                            ?>  
                                <tr>
                                    <td colspan="20"><hr style="margin:1px"></td>
                                </tr>
                            <?php } } ?>
                            <?php
                                $paramater_url=[
                                    'data_sent'=>$item->id_jadwal
                                ];

                                $kode_status_text=['Tidak Aktif','Aktif'];

                                $status_jadwal_text=!empty($kode_status_text[$item->status_jadwal]) ? $kode_status_text[$item->status_jadwal] : '';
                                
                                $torensi_awal=$item->status_toren_jam_cepat;
                                $torensi_awal_text=!empty($kode_status_text[$torensi_awal]) ? $kode_status_text[$torensi_awal] : '';

                                $toren_jam_cepat_tmp=(new \App\Http\Traits\AbsensiFunction)->his_to_seconds($item->toren_jam_cepat);
                                $toren_jam_cepat=(new \App\Http\Traits\AbsensiFunction)->hitung_waktu_by_seccond($toren_jam_cepat_tmp);
                                $toren_jam_cepat_text=$toren_jam_cepat->jam.' jam'.', '.$toren_jam_cepat->menit.' Menit'.', '.$toren_jam_cepat->detik.' Detik';

                            
                                $jam_awal_tmp=(new \App\Http\Traits\AbsensiFunction)->his_to_seconds($item->jam_awal);
                                $toren_jam_cepat_masuk_tmp=$jam_awal_tmp-$toren_jam_cepat_tmp;
                                $toren_jam_cepat_masuk=gmdate("H:i:s", $toren_jam_cepat_masuk_tmp);
                                
                                if(!$torensi_awal){
                                    $toren_jam_cepat_text='';
                                    $toren_jam_cepat_masuk='';
                                }

                                $torensi_telat=$item->status_toren_jam_telat;
                                $torensi_telat_text=!empty($kode_status_text[$torensi_telat]) ? $kode_status_text[$torensi_telat] : '';

                                $toren_jam_telat_tmp=(new \App\Http\Traits\AbsensiFunction)->his_to_seconds($item->toren_jam_telat);
                                $toren_jam_telat=(new \App\Http\Traits\AbsensiFunction)->hitung_waktu_by_seccond($toren_jam_telat_tmp);
                                $toren_jam_telat_text=$toren_jam_telat->jam.' jam'.', '.$toren_jam_telat->menit.' Menit'.', '.$toren_jam_telat->detik.' Detik';

                                $jam_akhir_tmp=(new \App\Http\Traits\AbsensiFunction)->his_to_seconds($item->jam_akhir);
                                $toren_jam_telat_masuk_tmp=$jam_akhir_tmp+$toren_jam_telat_tmp;
                                $toren_jam_telat_masuk=gmdate("H:i:s", $toren_jam_telat_masuk_tmp);
                                
                                if(!$torensi_telat){
                                    $toren_jam_telat_text='';
                                    $toren_jam_telat_masuk='';
                                }

                                $nm_type_jenis = (new \App\Models\RefJenisJadwal())->type_jenis_jadwal($item->type_jenis);
                                $nm_type_jenis="";

                                $bgcolor_style='background:#e9e9e9';
                                if(!empty($item->bg_color)){
                                    $bgcolor_style='background:'.$item->bg_color;
                                }
                                
                            ?>
                            <tr style="{{ $bgcolor_style }}">
                                <td>{{ $nm_type_jenis }}</td>
                                <td>{{ !empty($item->nm_jenis_jadwal) ? $item->nm_jenis_jadwal : ''  }}</td>
                                <td>{{ !empty($item->uraian) ? $item->uraian : ''  }}</td>
                                <td>
                                    <div>{{ $toren_jam_cepat_masuk  }}</div>
                                    <div>{{ $toren_jam_cepat_text  }}</div>
                                </td>
                                <td>{{ !empty($item->jam_awal) ? $item->jam_awal : ''  }}</td>
                                <td>{{ !empty($item->jam_akhir) ? $item->jam_akhir : ''  }}</td>
                                <td>
                                    <div>{{ $toren_jam_telat_masuk  }}</div>
                                    <div>{{ $toren_jam_telat_text  }}</div>
                                </td>
                                <td>{{ $status_jadwal_text  }}</td>
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
