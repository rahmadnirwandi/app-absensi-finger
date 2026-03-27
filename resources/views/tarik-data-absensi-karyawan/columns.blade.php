<hr style="margin-top:0px">
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <input type="hidden" id="filter_id_mesin" name="filter_id_mesin" value="{{ Request::get('filter_id_mesin') }}" />
                
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-3 col-md-10">
                        <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                        <input type="text" class="form-control" name='form_filter_text'
                            value="{{ Request::get('form_filter_text') }}" id='filter_search_text'
                            placeholder="Masukkan Kata">
                    </div>

                    <div class="col-lg-1 col-md-1">
                        <div class="d-grid grap-2">
                            <button type="submit" name='searchbydb' class="btn btn-primary" value=1>
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
                            <th class="py-3" style="width: 15%">Id User</th>
                            <th class="py-3" style="width: 15%">Nama</th>
                            <th class="py-3" style="width: 15%">Group</th>
                            <th class="py-3" style="width: 15%">Privilege</th>
                            <th class="py-3" style="width: 15%">Database</th>
                            <th class="py-3" style="width: 15%">Nama User di Database</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($list_data))
                            @foreach($list_data as $key => $item)
                            <?php
                                $check_database="<span style='color:RED'>Belum Ada</span>";
                                if(!empty($item->ready)){
                                    $check_database="<span style='color:#128628'>Ada</span>";
                                }

                                $get_privil=(new \App\Models\RefUserInfo())->get_privilege($item->privilege);
                            ?>
                            <tr>
                                <td>{{ !empty($item->id_user) ? $item->id_user : ''  }}</td>
                                <td>{{ !empty($item->name) ? $item->name : ''  }}</td>
                                <td>{{ !empty($item->group) ? $item->group : ''  }}</td>
                                <td>{{ $get_privil  }}</td>
                                <td>{!! $check_database  !!}</td>
                                <td>{{ !empty($item->db_name) ? $item->db_name : ''  }}</td>
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
