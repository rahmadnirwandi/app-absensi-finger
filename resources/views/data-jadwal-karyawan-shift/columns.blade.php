@include($router_name->path_base.'.modal')
<?php 
    $header_tgl='';
    $header_hari='';
    foreach($list_tgl as $key_tgl => $item_tgl){
        $tgl_format_tmp = new \DateTime($item_tgl);
        $tgl_format=$tgl_format_tmp->format('d/m');
        $hari_format=$tgl_format_tmp->format('D');

        $hari_format_indo=(new \App\Http\Traits\GlobalFunction)->hari($hari_format);
        $data_tgl[$key_tgl]=$tgl_format;
        $data_hari_e[$key_tgl]=$hari_format;
        $data_hari_indo[$key_tgl]=(new \App\Http\Traits\GlobalFunction)->hari($hari_format,1);

        $nm_hari=!empty($data_hari_indo[$key_tgl]) ? $data_hari_indo[$key_tgl] : '';

        if(!empty($hari_kerja[$hari_format])){
            $jml_hari_kerja_bulan++;
        }else{
            $get_hari_minggu[$item_tgl]=1;
            $hari_minggu[(new \App\Http\Traits\GlobalFunction)->hari($hari_format)]=(new \App\Http\Traits\GlobalFunction)->hari($hari_format);
        }

        if(!empty($list_hari_libur[$item_tgl])){
            $jml_hari_libur++;
        }

        $header_tgl.='<th class="py-3" style="width: 1%">'.$tgl_format.'</th>';
        $header_hari.='<th class="py-3" style="width: 1%">'.$nm_hari.'</th>';
    }

    if(!empty($list_shift['item'])){
        $list_shift['item']=json_decode($list_shift['item'],true);
    }

    $template_list_shift=!empty($list_shift['item']) ? $list_shift['item'] : [];

?>
<style>
    .box_waktu{
        padding:10px;
    }

    .box_waktu_sendiri{
        position: relative;
        padding:10px;
        padding-right:30px;
    }
    .box_waktu_sendiri>.icon_tanda{
        position: absolute;
        top:-10px;
        right:-10px;
    }
    .box_waktu_sendiri>.icon_tanda>i{
        color:#fff;
        font-size: 14px;
        padding:8px;
        background: #f34e4e;
        border-radius:100px;
    }
</style>
<hr>
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <input type="hidden" name='data_sent' value='{{ !empty($data_sent) ? $data_sent : '' }}'>
                <input type="hidden" name='params' value='{{ !empty($params_json) ? $params_json : '' }}'>

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
                <table class="table border table-responsive-tablet" style="width:100%">
                    <thead>
                        <tr style="border-bottom:1px solid;">
                            <th style='width:10%'>*</th>
                            <th style='width:85%'>Uraian</th>
                            <th style='width:5%; border-left:1px solid;'>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($list_tgl))
                            @foreach($list_tgl as $key_date => $value_date)
                                <?php
                                    $list_shift_text_tmp=[];
                                    $list_shift_sistem=[];
                                    if(!empty($list_shift[$value_date])){
                                        $data_detail=$list_shift[$value_date];
                                        if(!empty($data_detail)){
                                            foreach($data_detail as $value){
                                                $value=(object)$value;
                                                $text='';
                                                $kode_jadwal=$value->type_jadwal.'@'.$value->id_jenis_jadwal;
                                                $list_shift_sistem[]=$kode_jadwal;

                                                if(!empty($value->type_jadwal)){
                                                    if($value->type_jadwal==2){
                                                        $text="
                                                            <span class='box_waktu' style='background:".$value->bg_color."; display:block; width:20%;'>".$value->nm_jenis_jadwal."</span>
                                                        ";
                                                    }else{
                                                        $text="
                                                            <span class='box_waktu' style='background:".$value->bg_color.";'>"
                                                                .$value->nm_jenis_jadwal." : ".$value->masuk_kerja.' s/d '.$value->pulang_kerja.
                                                            "</span>
                                                        ";

                                                        if(!empty($value->pulang_kerja_next_day)){
                                                            $text="
                                                                <span class='box_waktu' style='background:".$value->bg_color.";'>"
                                                                    .$value->nm_jenis_jadwal." : ".$value->masuk_kerja.' s/d '.$value->pulang_kerja.' Esok hari'.
                                                                "</span>
                                                            ";
                                                        }
                                                    }
                                                }

                                                if(!empty($text)){
                                                    $list_shift_text_tmp[]=$text;
                                                }
                                            }
                                        }
                                    }

                                    $list_shift_sistem_json=[
                                        'item'=>$list_shift_sistem
                                    ];
                                    $list_shift_sistem_json=!empty($list_shift_sistem_json) ? json_encode($list_shift_sistem_json) : '';
                                    
                                    $list_shift_text='';
                                    if(!empty($list_shift_text_tmp)){
                                        $list_shift_text=implode(',',$list_shift_text_tmp);
                                    }

                                    $list_shift_sendiri_json='';
                                    $ss_short=[];
                                    $list_shift_text_sendiri_tmp='';
                                    if(!empty($list_data_jadwal_sendiri[$data_karyawan->id_karyawan][$value_date])){
                                        $s_tmp=$list_data_jadwal_sendiri[$data_karyawan->id_karyawan][$value_date];
                                        $s_tmp_array=json_decode($s_tmp);
                                        $s_tmp_array=!empty($s_tmp_array->item) ? $s_tmp_array->item : [];

                                        $ss_short_tmp=[];
                                        $ss_long_tmp=[];
                                        foreach($s_tmp_array as $item_ss){
                                            if(!empty($list_data_jadwal_library[$item_ss])){
                                                $get_dd=$list_data_jadwal_library[$item_ss];
                                                $ss_short_tmp[]=$item_ss;
                                                
                                                $text='';
                                                if(!empty($get_dd->type_jadwal)){
                                                    if($get_dd->type_jadwal==2){
                                                        $text="
                                                            <span class='box_waktu box_waktu_sendiri' style='background:".$get_dd->bg_color."; display:block; width:20%; position:relative;'>".$get_dd->nm_jenis_jadwal."<span class='icon_tanda'><i class='fa-solid fa-pencil'></i></span></span>
                                                        ";
                                                    }else{
                                                        $text="
                                                            <span class='box_waktu box_waktu_sendiri' style='background:".$get_dd->bg_color."; position:relative;'>"
                                                                .$get_dd->nm_jenis_jadwal." : ".$get_dd->masuk_kerja.' s/d '.$get_dd->pulang_kerja.
                                                            "<span class='icon_tanda'><i class='fa-solid fa-pencil'></i></span></span>
                                                        ";

                                                        if(!empty($get_dd->pulang_kerja_next_day)){
                                                            $text="
                                                                <span class='box_waktu box_waktu_sendiri' style='background:".$get_dd->bg_color."; position:relative;'>"
                                                                    .$get_dd->nm_jenis_jadwal." : ".$get_dd->masuk_kerja.' s/d '.$get_dd->pulang_kerja.' Esok hari'.
                                                                "<span class='icon_tanda'><i class='fa-solid fa-pencil'></i></span></span>
                                                            ";
                                                        }
                                                    }
                                                }

                                                if($text){
                                                    $ss_long_tmp[]=$text;
                                                }
                                            }
                                        }
                                        $ss_short=$ss_short_tmp;

                                        $list_shift_text_sendiri_tmp='';
                                        if(!empty($ss_long_tmp)){
                                            $list_shift_text_sendiri_tmp=implode(',',$ss_long_tmp);
                                        }
                                    }

                                    $list_shift_sendiri_json=!empty($ss_short) ? json_encode(['item'=>$ss_short]) : '';
                                    $list_shift_text=!empty($list_shift_text_sendiri_tmp) ? $list_shift_text_sendiri_tmp : $list_shift_text ;
                                    
                                ?>
                                <tr style='border-bottom:1px solid #ccc;' id='$value_date'>
                                    <td>
                                        <div>{{ $value_date }}</div>
                                    </td>
                                    <td>
                                        <div>{!! $list_shift_text !!}</div>
                                        <textarea class='jadwal_sistem' style="display:none" >{{ !empty($list_shift_sistem_json) ? $list_shift_sistem_json : '' }}</textarea>
                                        <textarea class='jadwal_sendiri' style="display:none" >{{ !empty($list_shift_sendiri_json) ? $list_shift_sendiri_json : '' }}</textarea>
                                    </td>
                                    <td style="border-left:1px solid;">
                                        <?php 
                                            $parameter=[
                                                'tgl'=>$value_date,
                                                'data_sent'=>\Request::get('data_sent'),
                                                'params'=>\Request::get('params'),
                                                'list_sistem'=>$list_shift_sistem_json,
                                                'list_sendiri'=>$list_shift_sendiri_json,
                                            ];

                                            $parameter=json_encode($parameter);
                                            // dd($parameter);
                                            // $params = !empty($req['params']) ? $req['params'] : '';
                                        ?>
                                        <a href='#' 
                                            class="btn btn-kecil btn-warning btn_change"
                                            data-sent='{{ $parameter }}'
                                        ><i class="fa-solid fa-pencil"></i></a>
                                    </td>
                                </tr>
                            @endforeach        
                        @endif                  
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('script-end-2')
<script src="{{ asset('js/data-jadwal-karyawan-shift/form.js') }}"></script>
@endpush