<form id="formIsiResume" action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <input type="hidden" name="key_old" value="{{ !empty($model->id) ? $model->id : '' }}">

    <div class="row justify-content-start align-items-end mb-3">
        <div class='bagan_form'>
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name='name' required
                value="{{ !empty($model->name) ? $model->name : '' }}">
            <div class="message"></div>
        </div>
        <!-- <div class="col-lg-2 mb-3"> -->
        <div class='bagan_form'>
            <label for="ket" class="form-label">Keterangan</label>
            <input type="text" class="form-control" id="ket" name='keterangan'
                value="{{ !empty($model->keterangan) ? $model->keterangan : '' }}">
            <div class="message"></div>
        </div>
        <!-- </div> -->
    </div>

    <div class="col-lg-2 mb-3">
        <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">{{ !empty($model->id) ? 'Ubah' : 'Simpan' }}</button>
        </div>
    </div>
</form>
