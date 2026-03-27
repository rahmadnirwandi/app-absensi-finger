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
                            <th class="py-3 col-md-5">Nama Shift</th>
                            <th class="py-3 col-md-3">Tanggal Mulai</th>
                            <th class="py-3 col-md-2">Jml Priode</th>
                            <th class="py-3 col-md-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($list_data))
                            @foreach($list_data as $key => $item)
                            <?php
                                $paramater_url=[
                                    'data_sent'=>$item->id_template_jadwal_shift
                                ];
                            ?>
                            <tr>
                                <td>{{ !empty($item->nm_shift) ? $item->nm_shift : ''  }}</td>
                                <td>{{ !empty($item->tgl_mulai) ? $item->tgl_mulai : ''  }}</td>
                                <td>{{ !empty($item->jml_periode) ? $item->jml_periode : ''  }}</td>

                                <td class='text-right'>
                                    <?php $url_template_shift_waktu='template-jadwal-shift-waktu'; ?>
                                    @if( (new \App\Http\Traits\AuthFunction)->checkAkses($url_template_shift_waktu) )
                                        <?php
                                            $url_template_shift_waktu=(new \App\Http\Traits\GlobalFunction)->set_paramter_url($url_template_shift_waktu,$paramater_url);
                                        ?>
                                        <a href="{{ url($url_template_shift_waktu) }}" class="btn btn-kecil btn-info" style='color:#555'>Atur Waktu</a>
                                    @endif

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
