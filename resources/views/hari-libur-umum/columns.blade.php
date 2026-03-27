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

    .bagan_action{
        background-color: #fff;
        padding: 10px;
        margin-top:5px;
        text-align: center;
    }
</style>
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
            <table class="table table-bordered table-responsive-tablet">
                <thead>
                    <tr>
                        @if(!empty($list_tgl))
                            <?php
                                $data_tgl=[];
                                $data_hari_e=[];
                                $data_hari_indo=[];
                                $hari_libur_minggu=[];
                            ?>
                            @foreach($list_tgl as $key_tgl => $item_tgl)
                                <?php
                                    $tgl_format_tmp = new \DateTime($item_tgl);
                                    $tgl_format=$tgl_format_tmp->format('d/m');
                                    $hari_format=$tgl_format_tmp->format('D');
                                    $hari_format_indo=(new \App\Http\Traits\GlobalFunction)->hari($hari_format,1);
                                    $data_tgl[$key_tgl]=$tgl_format;
                                    $data_hari_e[$key_tgl]=$hari_format;
                                    $data_hari_indo[$key_tgl]=$hari_format_indo;
                                    if($hari_format=='Sat' or $hari_format=='Sun'){
                                        $hari_libur_minggu[$item_tgl]=$hari_format;
                                    }
                                ?>
                                <th class="py-3" style="width: 1%">{{ $tgl_format }}</th>
                            @endforeach
                        @endif
                    </tr>
                    <tr>
                        @foreach($list_tgl as $key_tgl => $item_tgl)
                            <th class="py-3" style="width: 1%">{{ !empty($data_hari_indo[$key_tgl]) ? $data_hari_indo[$key_tgl] : '' }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($list_hari_libur))
                        @foreach($list_tgl as $key_tgl => $item_tgl)
                            <?php
                                $get_hari_libur_tmp=!empty($list_hari_libur[$item_tgl]) ? $list_hari_libur[$item_tgl] : [];
                                $get_hari_libur='';
                                $get_tgl_kode='';
                                $paramater_url=[];
                                if($get_hari_libur_tmp){
                                    $get_hari_libur=!empty($get_hari_libur_tmp->uraian) ? $get_hari_libur_tmp->uraian : '';
                                    $get_tgl_kode=!empty($get_hari_libur_tmp->asal_tanggal) ? $get_hari_libur_tmp->asal_tanggal : '';
                                }

                                if(!empty($get_tgl_kode)){
                                    $paramater_url=[
                                        'data_sent'=>$get_tgl_kode,
                                    ];
                                }

                                $class_hari='hari_default';
                                if($get_hari_libur){
                                    $class_hari='hari_yellow';
                                }
                                if(!empty($hari_libur_minggu[$item_tgl])){
                                    $class_hari='hari_red';
                                }
                            ?>
                            <td class='{{ $class_hari }}' style='vertical-align: middle;'>
                                <div>{{ $get_hari_libur }}</div>
                                @if(!empty($paramater_url))
                                    <hr style='margin:0px;'>
                                    <div class='bagan_action'>
                                        <div>
                                            {!! (new \App\Http\Traits\AuthFunction)->setPermissionButton([$router_name->uri.'/update',$paramater_url,'update']) !!}
                                        </div>
                                        <div class='mt-2'>
                                        {!! (new \App\Http\Traits\AuthFunction)->setPermissionButton([$router_name->uri.'/delete',$paramater_url,'delete'],['modal'])!!}
                                        </div>
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>