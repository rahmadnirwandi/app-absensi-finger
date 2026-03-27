<form action="{{ url($action_form) }}" method="POST">
    @csrf
    <div class="row justify-content-start align-items-end mb-3">
        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">

                <div class="col-lg-3">
                    <div class='bagan_form'>
                        <label for="nama_cuti" class="form-label">Nama Cuti<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_cuti" name='nama_cuti'  required>
                        <div class="message"></div>
                    </div>
                </div>

                 <div class="col-lg-2">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="jumlah_cuti" class="form-label">Jumlah Cuti<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah_cuti" name="jumlah_cuti" min='1' required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-start align-items-end">
        <div class="col-lg-5">
            <button class="btn btn-primary validate_submit" type="submit">Simpan</button>
        </div>
    </div>
</form>
