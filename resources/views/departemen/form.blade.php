<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <?php 
        $kode=!empty($model->id_departemen) ? $model->id_departemen : '';
    ?>
    <input type="hidden" name="key_old" value="{{ $kode }}">

    <div class="row justify-content-start align-items-end mb-3">
        <div class="col-lg-4">
            <div class='bagan_form'>
                <label for="nm_departemen" class="form-label">Nama Departemen</label>
                <input type="text" class="form-control" id="nm_departemen" name='nm_departemen' required
                    value="{{ !empty($model->nm_departemen) ? $model->nm_departemen : '' }}">
                <div class="message"></div>
            </div>
        </div>
    </div>

    <div class="row justify-content-start align-items-end">
        <div class="col-lg-5">
            <button class="btn btn-primary" type="submit">{{ !empty($kode) ? 'Ubah' : 'Simpan'  }}</button>
        </div>
    </div>
</form>
