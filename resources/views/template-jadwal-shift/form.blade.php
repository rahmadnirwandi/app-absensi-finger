<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <?php
        $kode=!empty($model->id_template_jadwal_shift) ? $model->id_template_jadwal_shift : '';
    ?>  
    <input type="hidden" name="key_old" value="{{ $kode }}">

    <div class="row d-flex justify-content-between">
        <div class="col-md-12">
            <div class="row justify-content-start align-items-end mb-3">

                <div class="col-lg-4">
                    <div class='bagan_form'>
                        <label for="nm_shift" class="form-label">Nama Shift <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nm_shift" name='nm_shift' required value="{{ !empty($model->nm_shift) ? $model->nm_shift : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class='bagan_form'>
                        <div class='input-date-bagan'>
                            <label for="tgl_mulai" class="form-label">Tgl. Mulai <span class="text-danger">*</span></label>
                            <input type="text" class="form-control input-daterange input-date" id='tgl_mulai' autocomplete="off">
                            <input type="hidden" id="tgl" required name="tgl_mulai" value="{{ !empty($model->tgl_mulai) ? $model->tgl_mulai : date('Y-m-d') }}">
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

            </div>
        </div>

        
    </div>

    <div class="row justify-content-start align-items-end">
        <div class="col-lg-5">
            <button class="btn btn-primary validate_submit" type="submit">{{ !empty($kode) ? 'Ubah' : 'Simpan'  }}</button>
        </div>
    </div>
</form>
