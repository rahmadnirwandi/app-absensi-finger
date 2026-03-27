<style>
    .hari_default{
        background-color: transparent !important;
    }

    .hari_red{
        background-color: #f39791 !important;
    }

    .hari_yellow{
        background-color: #f7d44e !important;
    }

    .hari_cuti {
        background-color: #79F6EB;
    }

    .hari_blue_sky{
        background-color: #79f6eb !important;
    }

    .hari_green_sky{
        background-color: #9cec6d !important;
    }

    .hasil_positif{
        color: #358f00 !important;
        font-weight: 700;
    }

    .hasil_negatif{
        color: #8f1300 !important;
        font-weight: 700;
    }
</style>

<?php
    $hari_kerja_tmp=!empty($data_jadwal_rutin->hari_kerja) ? $data_jadwal_rutin->hari_kerja : '';
    $hari_kerja=[];
    $get_hari_minggu=[];
    $data_tgl=[];
    $data_hari_e=[];
    $data_hari_indo=[];
    $header_tgl='';
    $header_hari='';
    $jml_hari_kerja=0;
    $jml_hari_kerja_bulan=0;
    $jml_hari_libur=0;
    $hari_minggu=[];

    if(!empty($hari_kerja_tmp)){
        $hari_kerja_t=explode(',',$hari_kerja_tmp);
        if($hari_kerja_t){
            foreach($hari_kerja_t as $hk){
                $hari_kerja[$hk]=$hk;
            }
        }
    }

    $jml_hari_kerja=count($hari_kerja);
    $total_kerja_sec_sistem_sec=!empty($data_jadwal_rutin->total_kerja_sec) ? $data_jadwal_rutin->total_kerja_sec : 0;
    $total_kerja_sec_sistem=!empty($data_jadwal_rutin->total_kerja) ? $data_jadwal_rutin->total_kerja : '00::00:00';

    $data_libur_format=[];
    if(!empty($list_hari_libur)){
        foreach($list_hari_libur as $key_l => $val_l){
            if(empty($data_libur_format[$val_l->asal_tanggal])){
                $data_libur_format[$val_l->asal_tanggal]=[
                    'uraian'=>$val_l->uraian,
                    'tgl_mulai'=>$val_l->asal_tanggal,
                    'tgl_akhir'=>$val_l->asal_tanggal,
                ];
            }else{
                $data_libur_format[$val_l->asal_tanggal]['tgl_akhir']=$key_l;
            }
        }
    }

    foreach($list_tgl as $key_tgl => $item_tgl){
        $tgl_format_tmp = new \DateTime($item_tgl);
        $tgl_format=$tgl_format_tmp->format('d/m');
        $hari_format=$tgl_format_tmp->format('D');

        $hari_format_indo=(new \App\Http\Traits\GlobalFunction)->hari($hari_format);
        $data_tgl[$key_tgl]=$tgl_format;
        $data_hari_e[$key_tgl]=$hari_format;
        $data_hari_indo[$key_tgl]=(new \App\Http\Traits\GlobalFunction)->hari($hari_format,1);

        $nm_hari=!empty($data_hari_indo[$key_tgl]) ? $data_hari_indo[$key_tgl] : '';

//        if(!empty($hari_kerja[$hari_format])){
//            $jml_hari_kerja_bulan++;
//        }else{
//            $get_hari_minggu[$item_tgl]=1;
//            $hari_minggu[(new \App\Http\Traits\GlobalFunction)->hari($hari_format)]=(new \App\Http\Traits\GlobalFunction)->hari($hari_format);
//        }
        if(!empty($hari_kerja[$hari_format])){
            $jml_hari_kerja_bulan++;
        }

        if(!empty($list_hari_libur[$item_tgl])){
            $jml_hari_libur++;
        }

        $header_tgl.='<th class="py-3" style="width: 1%">'.$tgl_format.'</th>';
        $header_hari.='<th class="py-3" style="width: 1%">'.$nm_hari.'</th>';
    }

    $total_jml_hari_kerja_bulan=$jml_hari_kerja_bulan-$jml_hari_libur;

    $total_kerja_bulan_sec=$total_kerja_sec_sistem_sec * $total_jml_hari_kerja_bulan;

    $hari_minggu=!empty($hari_minggu) ? implode(',',$hari_minggu) : '';

    $data_jadwal_rutin=(new \App\Http\Traits\AbsensiFunction)->getJadwalRutin();
?>
<hr style="margin-top:0px">
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-12 col-md-12">
                        <div class="row justify-content-start align-items-end mb-3">

                            <div class="col-lg-3">
                                <div class='bagan_form'>
                                    <div class='input-month-year-bagan'>
                                        <label for="filter_tahun_bulan" class="form-label">Tahun & Bulan</label>
                                        <span class='icon-bagan-date'></span>
                                        <input type="text" class="form-control input-month" id="filter_tahun_bulan" name='filter_tahun_bulan' placeholder="tahun & bulan" value="{{ !empty(Request::get('filter_tahun_bulan')) ? Request::get('filter_tahun_bulan') : date('Y-m') }}">
                                    </div>
                                    <div class="message"></div>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class='bagan_form'>
                                    <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                                    <input type="text" class="form-control" name='form_filter_text' value="{{ Request::get('form_filter_text') }}" id='filter_search_text' placeholder="Masukkan Kata">
                                <div class="message"></div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="row justify-content-start align-items-end mb-3">

                            <div class="col-lg-3 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_nm_jabatan" class="form-label">Jabatan </label>
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
                                        <input type="text" class="input-text" id='filter_nm_departemen' name="filter_nm_departemen" value="{{ Request::get('filter_nm_departemen') }}" />
                                        <input type="hidden" id="filter_id_departemen" name='filter_id_departemen' value="{{ Request::get('filter_id_departemen') }}">
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
                                        <input type="text" class="input-text" id='filter_nm_ruangan' name="filter_nm_ruangan"  value="{{ Request::get('filter_nm_ruangan') }}" />
                                        <input type="hidden" id="filter_id_ruangan" name='filter_id_ruangan'  value="{{ Request::get('filter_id_ruangan') }}">
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

                    <div class="col-lg-1 col-md-1">
                        <div class="d-grid grap-2">
                            <button type="submit" class="btn btn-primary validasi_submit" name='cari_data' value=1>
                                <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <hr>
        <div>
            <div class="col-lg-12 col-md-12">
                <div class="row justify-content-start">
                    <div class="col-lg-8">
                        <table class="table table-bordered table-responsive-tablet">
                            <tbody>
                                <tr>
                                    <td rowspan="2" style='width: 20%; vertical-align: middle;'>Hari Libur</td>
                                    <td style='width: 1%; vertical-align: middle;'>:</td>
                                    <td style='width: 5%; vertical-align: middle;'>
                                        <div class='hari_red' style='width:40px; height:40px'></div>
                                    </td>
                                    <td style='width: 69%; vertical-align: middle;'>Libur</td>
                                </tr>
                                <tr>
                                    <td style='width: 1%; vertical-align: middle;'>:</td>
                                    <td style='width: 5%; vertical-align: middle;'>
                                        <div class='hari_yellow' style='width:40px; height:40px'></div>
                                    </td>
                                    <td style='width: 69%; vertical-align: middle;'>
                                        @if($data_libur_format)
                                            @foreach($data_libur_format as $val_dlf)
                                                <?php
                                                    $text_hasil='';
                                                    $val_dlf=(object)$val_dlf;
                                                    $text_hasil.=$val_dlf->uraian.' : ';
                                                    if($val_dlf->tgl_mulai!=$val_dlf->tgl_akhir){
                                                        $text_hasil.=$val_dlf->tgl_mulai.' s/d '.$val_dlf->tgl_akhir;
                                                    }else{
                                                        $text_hasil.=$val_dlf->tgl_mulai;
                                                    }

                                                ?>
                                                <div>{{ $text_hasil }}</div>
                                            @endforeach
                                        @endif

                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="1" style='width: 20%; vertical-align: middle;'>Cuti</td>
                                    <td style='width: 1%; vertical-align: middle;'>:</td>
                                    <td style='width: 5%; vertical-align: middle;'>
                                        <div class='hari_cuti' style='width:40px; height:40px'></div>
                                    </td>
                                    <td style='width: 69%; vertical-align: middle;'>
                                        Cuti Karyawan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="col-lg-12 col-md-12">
                <div class="row justify-content-start mb-3">
                    <div class="col-lg-6">
                        <table class="table table-bordered table-responsive-tablet">
                            <tbody>
                                <tr>
                                    <td style='width: 20%; vertical-align: middle;'>Hari kerja</td>
                                    <td style='width: 1%; vertical-align: middle;'>:</td>
                                    <td style='width: 79%; vertical-align: middle;'><span>{{ $jml_hari_kerja_bulan }} Hari</span></td>
                                </tr>
                                <tr>
                                    <td style='width: 20%; vertical-align: middle;'>Total hari libur</td>
                                    <td style='width: 1%; vertical-align: middle;'>:</td>
                                    <td style='width: 79%; vertical-align: middle;'><span>{{ $jml_hari_libur }} Hari</span></td>
                                </tr>
                                <tr>
                                    <td style='width: 20%; vertical-align: middle;'>Total Hari Kerja </td>
                                    <td style='width: 1%; vertical-align: middle;'>:</td>
                                    <td style='width: 79%; vertical-align: middle;'><span>{{ $total_jml_hari_kerja_bulan }} Hari</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-lg-6">
                        <table class="table table-bordered table-responsive-tablet">
                            <tbody>
                                <tr>
                                    <td style='width: 20%; vertical-align: middle;'>Jam Kerja Per Hari</td>
                                    <td style='width: 1%; vertical-align: middle;'>:</td>
                                    <td style='width: 79%; vertical-align: middle;'><span>{{ (new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo($total_kerja_sec_sistem_sec) }}</span></td>
                                </tr>
                                <tr>
                                    <td style='width: 20%; vertical-align: middle;'>Total Jam Kerja</td>
                                    <td style='width: 1%; vertical-align: middle;'>:</td>
                                    <td style='width: 79%; vertical-align: middle;'><span>{{ (new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo($total_kerja_bulan_sec) }}</span></td>
                                </tr>
                                <tr>
                                    <td style='width: 20%; vertical-align: middle;'>Ket. Simbol</td>
                                    <td style='width: 1%; vertical-align: middle;'>:</td>
                                    <td style='width: 79%; vertical-align: middle;'><span>{{ $list_simbol_text }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        @if( (new \App\Http\Traits\AuthFunction)->checkAkses($router_name->uri.'/cetak') )
            @if(!empty($list_data->total()))
                <?php
                    $params_filter=Request::all();
                    $parameter_sent=[
                        'data_sent'=>json_encode($params_filter)
                    ];
                    $url_cetak=(new \App\Http\Traits\GlobalFunction)->set_paramter_url($router_name->uri.'/cetak',$parameter_sent);
                ?>
                <div class="row">
                    <div class="col-md-12 mb-2 text-end">
                        <a href="{{ url($url_cetak) }}" class="btn" style='color:#fff;background-color:#7912e0;'><i class="fa-solid fa-file-excel"></i> Print</a>
                    </div>
                </div>
            @endif
        @endif
        <div style="overflow-x: auto; max-width: auto;">
            <table class="table table-bordered table-responsive-tablet">
                <thead>
                    <tr>
                        <th rowspan="2" class="py-3" style="width: 1%; vertical-align: middle;">No</th>
                        <th rowspan="2" class="py-3" style="width: 40%; vertical-align: middle;">Nama</th>
                        {!! $header_tgl !!}
                        <th rowspan="2" class="py-3" style="width: 30%; vertical-align: middle;">Total Kerja</th>
                        <th rowspan="2" class="py-3" style="width: 30%; vertical-align: middle;">Selisih</th>
                    </tr>
                    <tr>
                        {!! $header_hari !!}
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($list_data))
                        <?php
                            $list_departemen=[];
                            $list_ruangan=[];
                            $list_status_karyawan=[];
                        ?>
                        @foreach($list_data as $key => $item)
                            <?php

                                $data_presensi=!empty($item->presensi) ? (array)json_decode($item->presensi) : [];
                                
                                $list_cuti_karyawan=[];
                                $total_cuti=0;
                                if(!empty($list_cuti[$item->id_karyawan])){
                                    $data_cuti=(object)$list_cuti[$item->id_karyawan];
                                    if(!empty($data_cuti->waktu)){
                                        foreach($data_cuti->waktu as $wc){
                                            if(!empty($wc[0])){
                                                $parameter_get_cuti=[
                                                    'tgl_awal'=>!empty($wc[0]) ? $wc[0] : '',
                                                    'tgl_akhir'=>!empty($wc[1]) ? $wc[1] : '',
                                                    'data_sent'=>!empty($wc[3]) ? $wc[3] : '',
                                                    'list_libur_kerja'=>!empty($get_hari_minggu) ? $get_hari_minggu : '',
                                                    'list_libur_nasional'=>!empty($list_hari_libur) ? $list_hari_libur : '',
                                                ];
                                                $hasil_cuti_tmp=(new \App\Http\Traits\AbsensiFunction)->get_tgl_khusus_with_data($parameter_get_cuti);
                                                $hasil_cuti=!empty($hasil_cuti_tmp['hasil_data']) ? $hasil_cuti_tmp['hasil_data'] : [];
                                                $total_cuti+=!empty($hasil_cuti_tmp['jml_hari']) ? $hasil_cuti_tmp['jml_hari'] : 0;
                                                $list_cuti_karyawan=array_merge($list_cuti_karyawan,$hasil_cuti);
                                            }
                                        }
                                    }
                                }

                                $list_dinasluar_karyawan=[];
                                $total_dinasluar=0;
                                if(!empty($list_dinasluar[$item->id_karyawan])){
                                    $data_dinasluar=(object)$list_dinasluar[$item->id_karyawan];
                                    if(!empty($data_dinasluar->waktu)){
                                        foreach($data_dinasluar->waktu as $wc){
                                            if(!empty($wc[0])){
                                                $parameter_get_dinasluar=[
                                                    'tgl_awal'=>!empty($wc[0]) ? $wc[0] : '',
                                                    'tgl_akhir'=>!empty($wc[1]) ? $wc[1] : '',
                                                    'data_sent'=>!empty($wc[3]) ? $wc[3] : '',
                                                    'list_libur_kerja'=>!empty($get_hari_minggu) ? $get_hari_minggu : '',
                                                    'list_libur_nasional'=>!empty($list_hari_libur) ? $list_hari_libur : '',
                                                ];
                                                $hasil_dinasluar_tmp=(new \App\Http\Traits\AbsensiFunction)->get_tgl_khusus_with_data($parameter_get_dinasluar);
                                                $hasil_dinasluar=!empty($hasil_dinasluar_tmp['hasil_data']) ? $hasil_dinasluar_tmp['hasil_data'] : [];
                                                $total_dinasluar+=!empty($hasil_dinasluar_tmp['jml_hari']) ? $hasil_dinasluar_tmp['jml_hari'] : 0;
                                                $list_dinasluar_karyawan=array_merge($list_dinasluar_karyawan,$hasil_dinasluar);
                                            }
                                        }
                                    }
                                }
                            ?>
                            @if(empty($list_departemen[$item->id_departemen]))
                                <?php $list_departemen[$item->id_departemen]=1; ?>
                                <tr style='background: #a7a7a7;'>
                                    <td colspan="50" style='vertical-align: middle;'>{{ !empty($item->nm_departemen) ? $item->nm_departemen : '' }}</td>
                                </tr>
                            @endif

                            @if(empty($list_ruangan[$item->id_ruangan]))
                                <?php $list_ruangan[$item->id_ruangan]=1; ?>
                                <tr style='background: #c8c7c7;'>
                                    <td>-</td>
                                    <td colspan="50" style='vertical-align: middle;'>{{ !empty($item->nm_ruangan) ? $item->nm_ruangan : '' }}</td>
                                </tr>
                            @endif

                            @if(empty($list_status_karyawan[$item->id_ruangan][$item->id_status_karyawan]))
                                <?php $list_status_karyawan[$item->id_ruangan][$item->id_status_karyawan]=1; ?>
                                <tr style='background: #eaeaea;'>
                                    <td>--</td>
                                    <td colspan="50" style='vertical-align: middle;'>{{ !empty($item->nm_status_karyawan) ? $item->nm_status_karyawan : '' }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td style='vertical-align: middle;'>{{ $key+1 }}</td>
                                <td style='vertical-align: middle;'>
                                    <div>( {{ !empty($item->id_user) ? $item->id_user : '' }} )</div>
                                    <div>{{ !empty($item->nm_karyawan) ? $item->nm_karyawan : '' }}</div>
                                </td>
                                <?php
                                    $grand_twjk_user_sec=0;
                                    $grand_twjk_sistem_sec=0;
                                ?>
                                @foreach($list_tgl as $key_tgl => $item_tgl)
                                    <?php
                                    
                                        $presensi_user_text='';
                                        $status_kerja_alias='';
                                        $total_wjk_user_perhari_sec=0;

                                        $get_presensi_user = !empty($data_presensi[$item_tgl])
                                            ? $data_presensi[$item_tgl]
                                            : null;

                                        $jadwal_hari_ini = !empty($get_presensi_user->jadwal) 
                                            ? $get_presensi_user->jadwal 
                                            : null;

                                        if(!empty($jadwal_hari_ini)){

                                            $data_proses = [
                                                'list_presensi' => !empty($get_presensi_user->presensi) 
                                                    ? implode(',', $get_presensi_user->presensi) 
                                                    : '',

                                                'data_jadwal_kerja' => $jadwal_hari_ini
                                            ];

                                            $hasil_proses = (object)(new \App\Http\Traits\PresensiHitungRutinFunction)
                                                ->getHitungRutin($data_proses);

                                            $total_wjk_user_perhari_sec = !empty($hasil_proses->total_waktu_kerja_user_sec) 
                                                ? $hasil_proses->total_waktu_kerja_user_sec 
                                                : 0;

                                            $presensi_filter = !empty($hasil_proses->presensi_user) 
                                                ? $hasil_proses->presensi_user 
                                                : '';

                                            $presensi_filter_tmp = !empty($presensi_filter) 
                                                ? explode(',', $presensi_filter) 
                                                : [];

                                            if($presensi_filter_tmp){
                                                $presensi_user_text = implode('<br>', $presensi_filter_tmp);
                                            }

                                            $status_kerja_alias = !empty($hasil_proses->status_kerja) 
                                                ? $hasil_proses->status_kerja->alias 
                                                : '';

                                            if($status_kerja_alias=='A'){
                                                $presensi_user_text='';
                                            }
                                        }

                                        $total_wjk_sistem_perhari_sec=!empty($data_jadwal_rutin->total_waktu_kerja_sec) ? $data_jadwal_rutin->total_waktu_kerja_sec : 0;
                                        $total_wjk_user_perhari_sec=!empty($total_wjk_user_perhari_sec) ? $total_wjk_user_perhari_sec : 0;

                                        $total_wjku_perhari_sec=$total_wjk_user_perhari_sec;

                                        $grand_twjk_user_sec+=$total_wjku_perhari_sec;
                                        $grand_twjk_sistem_sec+=$total_wjk_sistem_perhari_sec;

                                        if(empty($status_kerja_alias)){
                                            $status_kerja_alias='A';
                                        }

                                        if(!empty($status_kerja_alias)){
                                            $status_kerja_alias="(".$status_kerja_alias.")";
                                        }

                                        $tgl_data = new \DateTime($item_tgl);
                                        $tgl_now = new \DateTime("now");
                                        $tgl_now = new \DateTime($tgl_now->format('Y-m-d'));

                                        $class_hari='hari_default';

                                        if(!empty($jadwal_hari_ini) && strtoupper($jadwal_hari_ini->nm_jenis_jadwal) == 'OFF'){
                                            $class_hari = 'hari_red';
                                            $status_kerja_alias = '';
                                            $presensi_user_text = '';
                                            $grand_twjk_sistem_sec -= $total_wjk_sistem_perhari_sec;
                                        }

////                                        if(!empty($list_cuti_karyawan[$item_tgl])){
////                                            $class_hari='hari_blue_sky';
////                                            $presensi_user_text=$list_cuti_karyawan[$item_tgl];
////                                            if(!empty($data_jadwal_rutin->total_waktu_kerja_sec)){
////                                                $grand_twjk_user_sec+=$data_jadwal_rutin->total_waktu_kerja_sec;
////                                                $status_kerja_alias='';
////                                                if(!empty($get_hari_minggu[$item_tgl])){
////                                                    $grand_twjk_user_sec-=$data_jadwal_rutin->total_waktu_kerja_sec;
////                                                    $total_wjku_perhari_sec='';
////                                                    $presensi_user_text='';
////                                                }
////
////                                                if(!empty($list_hari_libur[$item_tgl])){
////                                                    $grand_twjk_user_sec-=$data_jadwal_rutin->total_waktu_kerja_sec;
////                                                    $total_wjku_perhari_sec='';
////                                                    $presensi_user_text='';
////                                                }
////                                            }
////                                        }
//
//                                        if(!empty($list_dinasluar_karyawan[$item_tgl])){
//                                            $class_hari='hari_green_sky';
//                                            $presensi_user_text=$list_dinasluar_karyawan[$item_tgl];
//                                            if(!empty($data_jadwal_rutin->total_waktu_kerja_sec)){
//                                                $grand_twjk_user_sec+=$data_jadwal_rutin->total_waktu_kerja_sec;
//                                                $status_kerja_alias='';
//                                            }
//                                        }
//
//                                        if(!empty($get_hari_minggu[$item_tgl])){
//                                            $class_hari='hari_red';
//                                            if(empty($presensi_user_text)){
//                                                $status_kerja_alias='';
//                                            }
//                                            $grand_twjk_sistem_sec-=$total_wjk_sistem_perhari_sec;
//                                        }
//
//                                        if(!empty($list_hari_libur[$item_tgl])){
//                                            $class_hari='hari_yellow';
//                                            if(empty($presensi_user_text)){
//                                                $status_kerja_alias='';
//                                            }
//                                            $grand_twjk_sistem_sec-=$total_wjk_sistem_perhari_sec;
//                                        }

                                        if(isset($list_cuti_karyawan[$item_tgl])){

                                            $class_hari='hari_blue_sky';
                                            $presensi_user_text=$list_cuti_karyawan[$item_tgl];
                                            $status_kerja_alias='';

                                            if(!empty($data_jadwal_rutin->total_waktu_kerja_sec)){
                                                $grand_twjk_user_sec+=$data_jadwal_rutin->total_waktu_kerja_sec;
                                            }

                                        }elseif(!empty($list_dinasluar_karyawan[$item_tgl])){

                                            $class_hari='hari_green_sky';
                                            $presensi_user_text=$list_dinasluar_karyawan[$item_tgl];
                                            $status_kerja_alias='';

                                        }elseif(!empty($get_hari_minggu[$item_tgl])){

                                            $class_hari='hari_red';
                                            $status_kerja_alias='';
                                            $grand_twjk_sistem_sec-=$total_wjk_sistem_perhari_sec;

                                        }elseif(!empty($list_hari_libur[$item_tgl])){

                                            $class_hari='hari_yellow';
                                            $status_kerja_alias='';
                                            $grand_twjk_sistem_sec-=$total_wjk_sistem_perhari_sec;

                                        }

                                        if($tgl_data>$tgl_now){
                                            $status_kerja_alias='';
                                        }

                                        $total_wjku_perhari_sec_text=!empty($total_wjku_perhari_sec) ? (new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo($total_wjku_perhari_sec,':') : '';
                                    ?>
                                    <td class='{{ $class_hari }}' style='vertical-align: middle;'>
                                        @if(!empty($presensi_user_text))
                                            <div style='border-bottom:1px solid #ccc;'>{!! $presensi_user_text !!}</div>
                                        @endif
                                        <div class='text-center'>{{ $status_kerja_alias }}</div>
                                    </td>
                                @endforeach
                                <?php
                                    $grand_twjk_user_sec_text=(new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo($grand_twjk_user_sec,':');
                                    $grand_twjk_sistem_sec_text=(new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo($grand_twjk_sistem_sec,':');

                                    $selisih_grand_twjk=$grand_twjk_sistem_sec-$grand_twjk_user_sec;
                                    $tanda_selisih_grand_twjk=($selisih_grand_twjk<0) ? '+' : '-';
                                    $class_tanda_selisih_grand_twjk=($selisih_grand_twjk<0) ? 'hasil_positif' : 'hasil_negatif';
                                    if($selisih_grand_twjk==0){
                                        $tanda_selisih_grand_twjk='';
                                        $class_tanda_selisih_grand_twjk='hasil_positif';
                                    }

                                    $selisih_grand_twjk_text=$tanda_selisih_grand_twjk.' '.(new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo(abs($selisih_grand_twjk),':');
                                ?>
                                <td style='vertical-align: middle;font-weight:700;'>{{ $grand_twjk_user_sec_text }}</td>
                                <td style='vertical-align: middle;font-weight:700;' class="{{ $class_tanda_selisih_grand_twjk }}">{{ $selisih_grand_twjk_text }}</td>
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