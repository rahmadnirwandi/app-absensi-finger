<hr>

<style>

.table-wrapper {
    overflow-x: auto;
}
.bg-cuti {
    background-color: #0dcaf0 !important;
    color: white;
    font-weight: 600;
}
.table-jadwal {
    border-collapse: collapse;
    min-width: max-content;
}

.table-jadwal th,
.table-jadwal td {
    min-width: 120px;
    padding: 6px;
    vertical-align: middle;
}

.table-jadwal th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 16px;
}

.table-jadwal th:first-child,
.table-jadwal td:first-child {
    position: sticky;
    left: 0;
    background: #ffffff;
    z-index: 2;
    min-width: 220px;
    text-align: left;
    font-weight: 500;
}

.table-jadwal select {
    width: 100%;
    height: 36px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
}

.shift-selected {
    background-color: #e7f1ff !important;
}
</style>

<form method="POST" action="{{ url($action_form) }}">
@csrf

<input type="hidden" name="id_ruangan" value="{{ $id_ruangan }}">

<div class="table-wrapper">
<table class="table table-bordered text-center table-jadwal">

    <thead>
        <tr>
            <th class="text-center">Nama Karyawan</th>

            @foreach($list_tgl as $tgl)
                <th>
                    {{ \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F') }}
                </th>
            @endforeach
        </tr>
    </thead>
    
    <tbody>
        @foreach($karyawan as $kar)
        <tr>
            <td>
                {{ $kar->nm_karyawan }}
            </td>

            @foreach($list_tgl as $tgl)

                @php
                    $isCuti = $list_cuti[$kar->id_karyawan][$tgl] ?? null;
                @endphp

                <td class="{{ $isCuti ? 'bg-cuti' : '' }}">

                    @if($isCuti)

                        <div>
                            <strong>CUTI</strong>
                        </div>
                        <div style="font-size:12px">
                            {{ $isCuti }}
                        </div>

                    @else

                        <select
                                name="jadwal[{{ $kar->id_karyawan }}][{{ $tgl }}]"
                                class="form-control shift-select"
                        >
                            <option value="">-</option>

                            @foreach($list_shift as $shift)
                                <option value="{{ $shift->id_jenis_jadwal }}">
                                    {{ $shift->nm_jenis_jadwal }}
                                    ({{ $shift->jam_masuk }} - {{ $shift->jam_pulang }})
                                </option>
                            @endforeach

                        </select>

                    @endif

                </td>
            @endforeach

        </tr>
        @endforeach
    </tbody>

</table>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">
        Simpan Jadwal
    </button>
</div>

</form>


<script>
document.querySelectorAll('.shift-select').forEach(function(select){

    if(select.value !== ''){
        select.classList.add('shift-selected');
    }

    select.addEventListener('change', function(){

        this.classList.remove('shift-selected');

        if(this.value !== ''){
            this.classList.add('shift-selected');
        }

    });

});
</script>