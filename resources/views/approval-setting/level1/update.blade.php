<style>
     .btn-ubah:hover {
        background-color: #008BFF !important;
    }
    .btn-ubah {
        font-size: 20px
    }

</style>

<form action="{{ url($action_update) }}" method="POST" class="px-4">

    @csrf
    <input type="hidden" name="id_karyawan" value="{{ $data->id_karyawan ?? '' }}">
    <input type="hidden" name="id_ruangan" value="{{ $data->scope_id ?? '' }}">
    <input type="hidden" name="id_jabatan" value="{{ $data->approver_value ?? '' }}">
    <input type="hidden" name="id" value="{{ $data->id ?? '' }}">
    

    <div class="row g-3">

        <div class="col-lg-12 col-md-6 col-sm-12">
            <label class="form-label">Nama Karyawan</label>
            <input type="text" class="form-control" readonly 
                value="{{ $data->nm_karyawan ?? '' }}">
        </div>
        <div class="col-lg-12 col-md-6 col-sm-12">
            <label class="form-label">Bagian</label>
            <input type="text" class="form-control" readonly 
                value="{{ $data->nm_departemen ?? '' }}">
        </div>
        <div class="col-lg-12 col-md-6 col-sm-12">
            <label class="form-label">Unit</label>
            <input type="text" class="form-control" readonly 
                value="{{ $data->nm_ruangan ?? '' }}">
        </div>
        <div class="col-lg-12 col-md-6 col-sm-12">
            <label class="form-label">Jabatan</label>
            <input type="text" class="form-control" readonly 
                value="{{ $data->nm_jabatan ?? '' }}">
        </div>

        <div class="col-lg-12 col-md-6 col-sm-12">
            <label class="form-label">Jenis Pengajuan</label>
            <select name="jenis_pengajuan" class="form-control" required>
                <option value="">-- Pilih --</option>
                <option value="izin" {{ !empty($data->jenis_pengajuan == 'izin') ? 'selected' : '' }}>Izin</option>
                <option value="cuti" {{ !empty($data->jenis_pengajuan == 'cuti') ? 'selected' : '' }}>Cuti</option>
                <option value="lembur" {{ !empty($data->jenis_pengajuan == 'lembur') ? 'selected' : '' }}>Lembur</option>
            </select>
        </div>
        <div class="col-lg-12 col-md-6 col-sm-12">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="">-- Pilih --</option>
                <option value="izin" {{ !empty($data->status == 1) ? 'selected' : '' }}>Aktif</option>
                <option value="cuti" {{ !empty($data->status == 0) ? 'selected' : '' }}>Non Aktif</option>
            </select>
        </div>

        <hr>
        {{-- Submit --}}
        <div class="col-lg-12">
            <button class="btn btn-primary btn-ubah validate_submit w-100 p-2" type="submit">Ubah</button>
        </div>
    </div>

    </div>
</form>