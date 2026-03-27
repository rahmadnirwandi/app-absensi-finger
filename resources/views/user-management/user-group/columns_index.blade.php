<div style="overflow-x: auto; max-width: auto;">
    <table class="table border data-table table-responsive-tablet">
        <thead>
            <tr>
                <th class="text-center ">No.</th>
                <th class="">Nama</th>
                <th class="">Keterangan</th>
                <th class="">Alias</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($dataList))
                @foreach ($dataList as $key => $value)
                    <?php
                        $kode = $value->id;
                        
                        $data_group_khusus=[
                            'group_super_admin',
                            'group_admin',
                            'group_karyawan'
                        ];
                        
                        $nm_group=str_replace(' ','',$value->alias);
                        $not_delete=0;
                        if(in_array($nm_group,$data_group_khusus)){
                            $not_delete=1;
                        }

                    ?>
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="">{{ $value->name }}</td>
                        <td class="">{{ $value->keterangan }}</td>
                        <td class="">{{ $value->alias }}</td>
                        <td class="text-center">
                            <a class="btn btn-primary" href="{{ url('permission-group-app?id=' . $value->id . '&alias=' . $value->alias) }}" role="button">Permission</a>

                            <a href="{{ url('user-group-app/form') }}" class='btn btn-warning modal-remote' data-modal-key='{{ $kode }}' data-modal-width='50%' data-modal-title='Ubah Group'>Edit</a>
                            @if(empty($not_delete))
                                <a href="{{ url('user-group-app/delete') }}" class='btn btn-danger modal-remote-delete' data-modal-key='{{ $kode }}' data-confirm-message="Apakah anda yakin menghapus data ini ?">Hapus</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
