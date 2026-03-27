<?php
    $jml_periode=$get_template_shift_detail->jml_periode;
    $type_periode = (new \App\Models\RefTemplateJadwalShiftDetail())->list_type_periode_system($get_template_shift_detail->type_periode);

    $tgl_start=new \DateTime($get_template_shift_detail->tgl_mulai);
    $tgl_start_tmp=new \DateTime($get_template_shift_detail->tgl_mulai);

    $jml_periode=$jml_periode-1;
    $rumus_tmp="+".$jml_periode." ".$type_periode;
    $tgl_end = $tgl_start_tmp->modify($rumus_tmp);

    $tgl_start_text = $tgl_start->format('Y-m-d');
    $tgl_end_text = $tgl_end->format('Y-m-d');
    
    $looping_range_date = new DatePeriod($tgl_start, DateInterval::createFromDateString('1 day'), $tgl_end);

?>

<hr>
<style>
    .list_jadwal_style{
        display: table-cell;
    }
</style>
<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <input type="hidden" name="key_old" value="{{ !empty($get_template_shift_detail->id_template_jadwal_shift_detail) ? $get_template_shift_detail->id_template_jadwal_shift_detail : 0 }}">
    <input type="hidden" id='id_jenis_jadwal'>
    <input type="hidden" id='type_jadwal'>
    <textarea style="display:none" id="list_tgl_terpilih" name=list_tgl_terpilih>{{ !empty($list_data_json) ? $list_data_json : '{}' }}</textarea>

    <div class="row d-flex justify-content-between">
        <div class="col-lg-4 p-0">
            <div class="card">
                <div class="card-body" style="padding:7px;">
                    <h5>Daftar List Jadwal Shift</h5>
                    <hr style="margin:3px 0px;">
                    <div style="overflow-x: auto; max-width: auto;">
                        <table class="table border table-responsive-tablet">
                            <tbody>
                                <?php
                                    $item_jadwal=[
                                        'id_jenis_jadwal'=>0,
                                        'bg_color'=>"#e1dede",
                                        'nm_jenis_jadwal'=>'Libur',
                                        'masuk_kerja'=>0,
                                        'pulang_kerja'=>0,
                                    ];
                                    $item_jadwal=(object)$item_jadwal;

                                    $kode_uniq=$item_jadwal->id_jenis_jadwal;
                                    $nm_kode_uniq='pil_'.$kode_uniq;
                                    $bgcolor=!empty($item_jadwal->bg_color) ? $item_jadwal->bg_color : "#fff";
                                ?>
                                <tr style="border-bottom:1px solid; background:{{ $bgcolor }}">
                                    <td>
                                        <div class="custom-control custom-radio" style="display:table;">
                                            <input type="radio" id="{{ $nm_kode_uniq }}" name='pil_jadwal' class="custom-control-input list_jadwal_style radio_pil" data-type-jadwal='2' value='{{ $item_jadwal->id_jenis_jadwal }}'>
                                            <input type="hidden" class="radio_pil_nama" value='{{ !empty($item_jadwal->nm_jenis_jadwal) ? $item_jadwal->nm_jenis_jadwal : '' }}'>

                                            <label class="custom-control-label list_jadwal_style" for="{{ $nm_kode_uniq }}" style="width:100%">
                                                <div class="list_jadwal_style" style="width:43%;">{{ !empty($item_jadwal->nm_jenis_jadwal) ? $item_jadwal->nm_jenis_jadwal : '' }}</div>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($data_jadwal as $key_jadwal => $item_jadwal)
                                    <?php
                                        $kode_uniq=$item_jadwal->id_jenis_jadwal;
                                        $nm_kode_uniq='pil_'.$kode_uniq;
                                        $bgcolor=!empty($item_jadwal->bg_color) ? $item_jadwal->bg_color : "#fff";
                                    ?>
                                    <tr style="border-bottom:1px solid; background:{{ $bgcolor }}">
                                        <td>
                                            <div class="custom-control custom-radio" style="display:table;">
                                                <input type="radio" id="{{ $nm_kode_uniq }}" name='pil_jadwal' class="custom-control-input list_jadwal_style radio_pil" data-type-jadwal='1' value='{{ $item_jadwal->id_jenis_jadwal }}'>
                                                <input type="hidden" class="radio_pil_nama" value='{{ !empty($item_jadwal->nm_jenis_jadwal) ? $item_jadwal->nm_jenis_jadwal : '' }}'>

                                                <label class="custom-control-label list_jadwal_style" for="{{ $nm_kode_uniq }}" style="width:100%">
                                                    <div class="list_jadwal_style" style="width:43%;">{{ !empty($item_jadwal->nm_jenis_jadwal) ? $item_jadwal->nm_jenis_jadwal : '' }}</div>
                                                    <div class="list_jadwal_style" style="width:70%;">
                                                        {{ !empty($item_jadwal->masuk_kerja) ? $item_jadwal->masuk_kerja : '' }}
                                                        S/D
                                                        {{ !empty($item_jadwal->pulang_kerja) ? $item_jadwal->pulang_kerja : '' }}
                                                    </div>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 p-0">
            <div class="card">
                <div class="card-body" style="padding:7px;">
                    <label id='title_jadwal'></label>
                    <h5 style="border-top:1px solid;">Daftar hari</h5>
                    <hr style="margin:3px 0px;">
                    <div id='list_hari' style='display:none'>
                        <div style="overflow:auto; max-height: 1900px; padding:5px;">
                            <?php
                                $jml_periode=$get_template_shift_detail->jml_periode;
                                $check_looping=0;
                                $max_move=8;
                                
                                $hasil_pemilahan_data=[];
                                $jml_max_column=0;
                            ?>

                            @foreach($looping_range_date as $key_date => $valeu_date)
                                <?php
                                    $check_looping++;
                                    $single_date=$valeu_date->format('D');
                                    $number_date=$valeu_date->format('d');
                                    $month=$valeu_date->format('m');
                                    $number_date=(int)$number_date;
                                    $nm_hari=(new \App\Http\Traits\GlobalFunction)->hari($single_date);

                                    $kode=$month.'_'.$number_date;

                                    $nm_kode='pil_'.$kode;
                                    $value_hari=$number_date;
                                    
                                    $have_data=[];
                                    if(!empty($grafik_data[$number_date])){
                                        foreach($grafik_data[$number_date] as $val_grafik){
                                            $have_data[]="<div style='background-color:".$val_grafik['bgcolor']."; padding:5px;'>".$val_grafik['nm_shift']."</div>";
                                        }
                                    }
                                    $have_data=implode('',$have_data);
                                    if($check_looping>=$max_move){
                                        $check_looping=1;
                                    }
                                    $hasil_pemilahan_data[$check_looping][]=$valeu_date;
                                    if($jml_max_column<count($hasil_pemilahan_data[$check_looping])){
                                        $jml_max_column=count($hasil_pemilahan_data[$check_looping]);
                                    }
                                ?>
                            @endforeach
                            
                            <div class="col-sm p-0">
                                <table class="table border table-responsive-tablet">
                                    <tbody>
                                        @foreach($hasil_pemilahan_data as $key_parent => $valeu_parent)
                                            @if(!empty($valeu_parent))
                                                <tr style='border-bottom:1px solid #ccc;'>
                                                    @foreach($valeu_parent as $key_date => $valeu_date)
                                                        <?php 
                                                            $single_date=$valeu_date->format('D');
                                                            $number_date=$valeu_date->format('d');
                                                            $month=$valeu_date->format('m');
                                                            $number_date=(int)$number_date;
                                                            $nm_hari=(new \App\Http\Traits\GlobalFunction)->hari($single_date);
                                                            
                                                            $value_hari=$number_date;
                                                            $nm_kode='pil_'.$kode;

                                                            $have_data=[];
                                                            if(!empty($grafik_data[$number_date])){
                                                                foreach($grafik_data[$number_date] as $val_grafik){
                                                                    $have_data[]="<div style='background-color:".$val_grafik['bgcolor']."; padding:5px;'>".$val_grafik['nm_shift']."</div>";
                                                                }
                                                            }
                                                            $have_data=implode('',$have_data);
                                                        ?>

                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input checkbox_hari" type="checkbox" value="{{ $value_hari }}" id="{{ $nm_kode }}">
                                                                <label class="form-check-label" style='margin-top: 7px;margin-left: 5px;' for="{{ $nm_kode }}">
                                                                    <div>Hari Ke {{ $value_hari }}</div>
                                                                    <div style='font-size:13px'>{!! $have_data !!}</div>
                                                                </label>
                                                            </div>
                                                        </td>
                                                    @endforeach
                                                    @if(count($valeu_parent)<$jml_max_column)
                                                        <td></td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row justify-content-end align-items-end mt-1">
                            <div class="col-md-2 text-center">
                                <button class="btn btn-primary btn-block" id='btn_save' type="submit">Ubah</button>
                            </div>
                        </div>
                    </div>
                    <div id='list_hari_non_change'>
                        <h5 class="text-center">Silahkan Pilih jadwal shift terlebih dahulu</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('script-end-2')
    <script src="{{ asset('js/template-jadwal-shift-waktu/form.js') }}"></script>
@endpush