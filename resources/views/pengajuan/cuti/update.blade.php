<style>
    .btn-ubah:hover {
        background-color: #008BFF !important;
    }
    .btn-ubah {
        font-size: 20px
    }

    .jenis_cuti {
        background-color: #fff !important;
    }
    
</style>

<form action="{{ url($action_update) }}" method="POST" class="px-4">
    @csrf
    <div class="row justify-content-start align-items-end mb-3">
        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">

                <input type="text" name="id_karyawan" hidden value="{{ $data->id_karyawan }}">
                <input type="text" name="id" hidden value="{{ $data->id }}">
                <input type="text" name="id_jenis_cuti" hidden value="{{ $data->id_jenis_cuti }}">

                <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="jenis_cuti" class="form-label">Jenis Cuti</label>
                            <input type="text" readonly class="form-control jenis_cuti" id="jenis_cuti" value="{{ $data->nm_jenis_cuti }}" required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-6 col-sm-12 mb-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="tgl_mulai" value="{{ $data->tgl_mulai }}" required>
                </div>

                <div class="col-lg-12 col-md-6 col-sm-12 mb-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" name="tgl_selesai" value="{{ $data->tgl_selesai }}" required>
                </div>
                <div class="col-12  col-sm-12 mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" name="keterangan" rows="4">{{ !empty($data->keterangan) ? $data->keterangan : '' }}</textarea>
                </div>

                
            </div>
        </div>
    </div>

    <div class="row justify-content-start align-items-end">
        <div class="col-lg-12">
            <button class="btn btn-primary btn-ubah validate_submit w-100 p-2" type="submit">Ubah</button>
        </div>
    </div>
</form>