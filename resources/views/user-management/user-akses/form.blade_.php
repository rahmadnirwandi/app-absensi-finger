<form id="formIsiResume" action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <input type="hidden" name="key_data" value="{{ !empty($model->id_user_) ? $model->id_user_ : '' }}">

    <table id="table_user_group" class="table table-striped mb-5">
        <tbody>
            <tr>
                <td>Nama User</td>
                <td>{{ !empty($model->nama) ? $model->nama : '' }}</td>
            </tr>
            <tr>
                <td>id User</td>
                <td>{{ !empty($model->id_user_) ? $model->id_user_ : '' }}</td>
            </tr>
            <tr>
                <td>Jenis Akun</td>
                <td>{{ !empty($model->status) ? $model->status : '' }}</td>
            </tr>
        </tbody>
    </table>

    <div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="w-25">Level Akses</th>
                    <th class="w-25">Keterangan</th>
                    <th class="">Aksi</th>
                </tr>
                @foreach ($level_akses_list as $value)
                    <tr>
                        <th class="w-50">{{ $value->name }}</th>
                        <th class="w-25">{{ $value->keterangam }}</th>
                        <th class="d-flex align-items-center justify-content-between">
                            <div class="form-check">
                                <input class="form-check-input" value="{{ $value->alias }}" {{ $model->alias_group == $value->alias ? 'checked' : '' }} type="radio" name="level_akses" required>
                            </div>
                        </th>
                    </tr>
                @endforeach
            </thead>
        </table>
    </div>

    <div class="col-lg-2 mb-3">
        <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">{{ !empty($cek) ? 'Ubah' : 'Simpan' }}</button>
        </div>
    </div>
</form>