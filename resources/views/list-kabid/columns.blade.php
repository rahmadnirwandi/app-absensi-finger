<hr style="margin-top:0px">
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-12 col-md-12">
                        <div class="row justify-content-start align-items-end mb-3">

                            <div class="col-lg-4">
                                <div class='bagan_form'>
                                    <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                                    <input type="text" class="form-control" name='form_filter_text' value="{{ Request::get('form_filter_text') }}" id='filter_search_text' placeholder="Masukkan Kata">
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

                </div>
            </form>
        </div>

        <div style="overflow-x: auto; max-width: auto;">
            <table class="table border table-responsive-tablet">
                <thead>
                    <tr>
                        <th class="py-3">Nip</th>
                        <th class="py-3">Nama Kabid</th>
                        <th class="py-3">Jabatan</th>
                        <th class="py-3">Departemen</th>
                        <th class="py-3">Ruangan Pilihan</th>
                        <th class="py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($list_data))
                        @foreach($list_data as $key => $item)
                        <?php
                            $paramater_url=[
                                'data_sent'=>$item->id_karyawan
                            ];
                        ?>
                        <tr>
                            <td>{{ !empty($item->nip) ? $item->nip : ''  }}</td>
                            <td>{{ !empty($item->nm_karyawan) ? $item->nm_karyawan : ''  }}</td>
                            <td>{{ !empty($item->nm_jabatan) ? $item->nm_jabatan : ''  }}</td>
                            <td>{{ !empty($item->nm_departemen) ? $item->nm_departemen : ''  }}</td>
                            <td>{{ !empty($item->list_ruangan) ? $item->list_ruangan : ''  }}</td>
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
    </div>
</div>