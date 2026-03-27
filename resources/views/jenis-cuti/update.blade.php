<style>
    .btn-ubah:hover {
        background-color: #008BFF !important;
    }
    .btn-ubah {
        font-size: 20px
    }
</style>

<form action="{{ url('/jenis-cuti/update') }}" method="POST" class="px-4">

    @csrf
    <input type="hidden" name="id" value="{{ $data->id ?? '' }}">
    

    <div class="row g-3 mb-3">
        <div class="col-lg-12 col-md-6 col-sm-12">
                <label for="nama_cuti" class="form-label">Nama Cuti<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_cuti" name='nama_cuti' value="{{ $data->nama ?? '' }}" required>
           
        </div>

            <div class="col-lg-12 col-md-6 col-sm-12">
                <div class="bagan_form">
                    <label for="jumlah_cuti" class="form-label">Jumlah Cuti<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="jumlah_cuti" name="jumlah_cuti" min='1' value="{{ $data->jumlah ?? '' }}" required >
                </div>
        </div>
    </div>

    <div class="row justify-content-start align-items-end">
        <div class="col-lg-12">
            <button class="btn btn-primary btn-ubah validate_submit w-100 p-2" type="submit">Ubah</button>
        </div>
    </div>

    </div>
</form>
