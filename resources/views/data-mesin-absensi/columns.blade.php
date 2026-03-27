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
                            <th class="py-3" style="width: 15%">Ip Address</th>
                            <th class="py-3" style="width: 15%">Comm Key Mesin</th>
                            <th class="py-3" style="width: 15%">Nama/Alias Mesin</th>
                            <th class="py-3" style="width: 15%">Lokasi Mesin</th>
                            <th class="py-3" style="width: 10%">Serial Number(SN)</th>
                            <th class="py-3" style="width: 15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($list_data))
                            @foreach($list_data as $key => $item)
                            <?php
                                $paramater_url=[
                                    'data_sent'=>$item->id_mesin_absensi
                                ];
                            ?>
                            <tr>
                                <td>{{ !empty($item->ip_address) ? $item->ip_address : ''  }}</td>
                                <td>{{ !empty($item->comm_key) ? $item->comm_key : ''  }}</td>
                                <td>{{ !empty($item->nm_mesin) ? $item->nm_mesin : ''  }}</td>
                                <td>{{ !empty($item->lokasi_mesin) ? $item->lokasi_mesin : ''  }}</td>
                                <td>{{ !empty($item->sn) ? $item->sn : ''  }}</td>
                                <td>{{ !empty($item->status_mesin) ? 'Aktif' : 'Tidak'  }}</td>
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
