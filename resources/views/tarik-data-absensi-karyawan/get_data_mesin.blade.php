<table class="table table-bordered">
    <thead>
        <tr>
            <th>Ip Mesin</th>
            <th>Nama Mesin</th>
            <th>Lokasi Mesin</th>
            <th>Status</th>
            <th>Progress</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($list_data))
            @foreach($list_data as $item)
                <?php 
                    $id_mesin=!empty($item->id_mesin_absensi) ? $item->id_mesin_absensi : ''
                ?>
                <tr id='{{ "item_".$id_mesin }}' data-status='0' data-key='{{ $id_mesin }}'>
                    <td>{{ !empty($item->ip_address) ? $item->ip_address : '' }}</td>
                    <td>{{ !empty($item->nm_mesin) ? $item->nm_mesin : '' }}</td>
                    <td>{{ !empty($item->lokasi_mesin) ? $item->lokasi_mesin : '' }}</td>
                    <td class='status_mesin'></td>
                    <td>
                        <div class="progress progress-striped">
                            <div class="data">
                                <input type="hidden" class='id_mesin' value="{{ $id_mesin }}">
                            </div>
                            <div style="width: 0%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" role="progressbar" class="progress-bar progress-bar-success" id="">
                                <span id="bar-progress-label">0%</span>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>