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

    .penanda_jadwal{
        margin-top:5px;
        height: 20px;
    }
</style>

<?php
    $hari_kerja=[];
    $data_tgl=[];
    $data_hari_e=[];
    $data_hari_indo=[];
    $header_tgl='';
    $header_hari='';

    if(!empty($hari_kerja_tmp)){
        $hari_kerja_t=explode(',',$hari_kerja_tmp);
        if($hari_kerja_t){
            foreach($hari_kerja_t as $hk){
                $hari_kerja[$hk]=$hk;
            }
        }
    }

    $jml_hari_kerja=count($hari_kerja);


    foreach($list_tgl as $key_tgl => $item_tgl){
        $tgl_format_tmp = new \DateTime($item_tgl);
        $tgl_format=$tgl_format_tmp->format('d/m');
        $hari_format=$tgl_format_tmp->format('D');

        $hari_format_indo=(new \App\Http\Traits\GlobalFunction)->hari($hari_format);
        $data_tgl[$key_tgl]=$tgl_format;
        $data_hari_e[$key_tgl]=$hari_format;
        $data_hari_indo[$key_tgl]=(new \App\Http\Traits\GlobalFunction)->hari($hari_format,1);

        $nm_hari=!empty($data_hari_indo[$key_tgl]) ? $data_hari_indo[$key_tgl] : '';

        $header_tgl.='<th class="py-3" style="width: 1%">'.$tgl_format.'</th>';
        $header_hari.='<th class="py-3" style="width: 1%">'.$nm_hari.'</th>';
    }
?>
<hr style="margin-top:0px">
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <input type='hidden' name='type_link' value='2'>
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
                <div class="row justify-content-start mb-3">
                    <div class="col-lg-6">
                        <table class="table table-bordered table-responsive-tablet">
                            <tbody>
                                <tr>
                                    <td style='width: 20%; vertical-align: middle;'>Total Jam Kerja 1 Bulan</td>
                                    <td style='width: 1%; vertical-align: middle;'>:</td>
                                    <td style='width: 79%; vertical-align: middle;'><span> Hari</span></td>
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
                    <div class="col-md-12 text-end">
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
                                $get_tamplate_user=!empty($list_tamplate_user[$item->id_karyawan]) ? $list_tamplate_user[$item->id_karyawan] : '';
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
                                    @if(!empty($item->nm_shift))
                                        <div style='border-top:1px solid'>{{ $item->nm_shift }}</div>
                                    @endif
                                </td>
                                @foreach($list_tgl as $key_tgl => $item_tgl)
                                    <?php 
                                        $get_presensi_user=!empty($data_presensi[$item_tgl]) ? $data_presensi[$item_tgl] : '';
                                        $list_nm_shift_tmp=[];
                                        $list_color_shift_tmp=[];
                                        $list_libur_tmp_style=[];
                                        $id_template_jadwal_shift=$item->id_template_jadwal_shift;
                                        
                                        if(!empty($get_tamplate_default[$id_template_jadwal_shift])){
                                            $tamplate_jadwal=$get_tamplate_default[$id_template_jadwal_shift];
                                            if(!empty($get_tamplate_user[$id_template_jadwal_shift][$item_tgl])){
                                                $tamplate_jadwal=$get_tamplate_user[$id_template_jadwal_shift];
                                            }
                                            // dd($tamplate_jadwal,$get_tamplate_user);

                                            $data_proses=[
                                                'tgl'=>$item_tgl,
                                                'tamplate_jadwal'=>$get_tamplate_default[$id_template_jadwal_shift],
                                                'tamplate_jadwal_user'=>$get_tamplate_default[$id_template_jadwal_shift],
                                                'data_jadwal_shift_by_sistem'=>!empty($get_tamplate_user[$id_template_jadwal_shift]) ? $get_tamplate_user[$id_template_jadwal_shift] : [],
                                                'data_presensi_user'=>$data_presensi,
                                            ];

                                            $hasil_proses=(new \App\Http\Traits\PresensiHitungShiftFunction)->getHitung($data_proses);

                                            if(!empty($tamplate_jadwal[$item_tgl])){
                                                foreach($tamplate_jadwal[$item_tgl] as $item_jadwal){
                                                    $item_jadwal=(object)$item_jadwal;
                                                    
                                                    $list_nm_shift_tmp[]=$item_jadwal->nm_jenis_jadwal;
                                                    if($item_jadwal->type_jadwal==1){
                                                        $data_tmp="<div class='penanda_jadwal' style='background:".$item_jadwal->bg_color.";'></div>";
                                                        if(!empty($item_jadwal->pulang_kerja_next_day)){
                                                            $data_tmp="";
                                                        }
                                                        $list_color_shift_tmp[]=$data_tmp;
                                                    }
                                                    if($item_jadwal->type_jadwal==2){
                                                        $list_libur_tmp_style[$item_tgl]=[
                                                            'bg_color'=>$item_jadwal->bg_color,
                                                            'title'=>$item_jadwal->nm_jenis_jadwal
                                                        ];
                                                    }
                                                }
                                            }
                                            
                                            $log_user=!empty($get_presensi_user->presensi) ? implode('<br>',$get_presensi_user->presensi) : '';

                                            // dd($data,$data_jadwal_shift_by_sistem);


                                            // if(!empty($item_jadwal->pulang_kerja_next_day)){
                                            //     dd($item_jadwal->pulang_kerja_next_day,$item_tgl);
                                                
                                            //     $tgl_awal=new \DateTime($tgl_awal_tmp);
                                            //     $tgl_awal=$tgl_awal->modify('-1 day');
                                            //     $tgl_awal=$tgl_awal->format('Y-m-d');
                                            // }

                                            // if(!empty($tamplate_default[$item_tgl])){
                                            //     foreach($tamplate_default[$item_tgl] as $item_jadwal){
                                            //         $item_jadwal=(object)$item_jadwal;
                                            //         // dd($item_jadwal,$item_jadwal->pulang_kerja_next_day);
                                                    
                                            //         if(!empty($data_jadwal_shift_by_sistem[$item_jadwal->id_jenis_jadwal])){
                                                        
                                            //             $data_proses=[
                                            //                 'nm_jenis_jadwal'=>$item_jadwal->nm_jenis_jadwal,
                                            //                 'pulang_next_day'=>$item_jadwal->pulang_kerja_next_day,
                                            //                 'list_presensi'=>!empty($get_presensi_user->presensi) ? $get_presensi_user->presensi : '',
                                            //                 'data_jadwal_kerja'=>!empty($data_jadwal_shift_by_sistem[$item_jadwal->id_jenis_jadwal]) ? $data_jadwal_shift_by_sistem[$item_jadwal->id_jenis_jadwal]  : ''
                                            //             ];
                                            //             // dd($data_proses);
                                            //             // $hasil_proses=(new \App\Http\Traits\PresensiHitungShiftFunction)->getHitung($data_proses);
                                            //         }

                                            //         $list_nm_shift_tmp[]=$item_jadwal->nm_jenis_jadwal;
                                            //         if($item_jadwal->type_jadwal==1){
                                            //             $list_color_shift_tmp[]="<div class='penanda_jadwal' style='background:".$item_jadwal->bg_color.";'></div>";
                                            //         }
                                            //         if($item_jadwal->type_jadwal==2){
                                            //             $list_libur_tmp_style[$item_tgl]=[
                                            //                 'bg_color'=>$item_jadwal->bg_color,
                                            //                 'title'=>$item_jadwal->nm_jenis_jadwal
                                            //             ];
                                            //         }

                                            //         if(!empty($item_jadwal->pulang_kerja_next_day)){
                                            //             // dd($item_jadwal);
                                            //         }
                                                    
                                            //     }
                                            // }
                                        }

                                        $list_nm_shift=!empty($list_nm_shift_tmp) ? implode(',',$list_nm_shift_tmp) : '';
                                        $list_color_shift=!empty($list_color_shift_tmp) ? implode('',$list_color_shift_tmp) : '';
                                    ?>
                                    <?php
                                        $td_color='transparent';
                                        if(!empty($list_libur_tmp_style[$item_tgl])){
                                            $data_libur_jadwal=(object)$list_libur_tmp_style[$item_tgl];
                                            $td_color=$data_libur_jadwal->bg_color;
                                            $list_color_shift=!empty($list_color_shift) ? $list_color_shift : $data_libur_jadwal->title;
                                        }
                                    ?>
                                    @if(!empty($list_libur_tmp_style[$item_tgl]))
                                        <td style='vertical-align: middle; background:{{ $td_color }}; '>
                                            <div>{!! $list_color_shift !!}</div>
                                            <div>{!! $log_user !!}</div>
                                        </td>
                                    @else
                                        <td style='background:{{ $td_color }}; '>
                                            <div>{!! $list_color_shift !!}</div>
                                            <div>{!! $log_user !!}</div>
                                        </td>
                                    @endif
                                    
                                @endforeach
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