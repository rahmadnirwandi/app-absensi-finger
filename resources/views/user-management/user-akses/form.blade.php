<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <?php
        $alias_group=!empty($model->alias_group) ? $model->alias_group : '';
        $password=!empty($model->password) ? $model->password : '';
    ?>
    <input type="hidden" name="id_karyawan" value="{{ !empty( $model->id_karyawan ) ? $model->id_karyawan : '' }}">
    <input type="hidden" name="id_uxui_users" value="{{ !empty( $model->id_uxui_users ) ? $model->id_uxui_users : '' }}">

    <div class="card card-body">
        <div class="row justify-content-start">
            <div class="col-md-6">
                <table class="table table-bordered table-responsive-tablet">
                    <tr>
                        <td style="width: 8%">NIP</td>
                        <td style="width: 92%">{{ !empty( $model->nip ) ? $model->nip : '' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 8%">Nama</td>
                        <td style="width: 92%">{{ !empty( $model->nm_karyawan ) ? $model->nm_karyawan : '' }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <table class="table table-bordered table-responsive-tablet">
                    <tr>
                        <td style="width: 8%">Jabatan</td>
                        <td style="width: 92%">{{ !empty( $model->nm_jabatan ) ? $model->nm_jabatan : '' }}</td>
                    </tr>
                    <tr>

                        <td style="width: 8%">Departemen</td>
                        <td style="width: 92%">{{ !empty( $model->nm_departemen ) ? $model->nm_departemen : '' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="card card-body">
        <div class="row justify-content-start align-items-start mb-3">
            <div class="col-lg-6">
                <div class="col-lg-12">
                    <div class='bagan_form'>
                        <label for="username" class="form-label">Username ( Minimal 3 karakter,Maksimal 100 karakter )</label>
                        <input type="text" class="form-control" id="username" name='username' required value="{{ !empty($model->username) ? $model->username : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class='bagan_form mt-3'>
                        <?php
                            $label_pass='Password';
                            $status_form_pass='required';
                            if(!empty($model->id_uxui_users)){
                                $label_pass='Password Baru <span style="color:RED">(Silahkan isi, jika ingin ganti password)</span>';
                                $status_form_pass='';
                            }
                        ?>
                        <label for="password" class="form-label">{!! $label_pass !!}</label>
                        <input type="text" class="form-control" id="password" name='password' {{ $status_form_pass }} value="">
                        <div class="message"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">

                <div class="col-lg-5">
                    <div class='bagan_form'>
                        <label for="level_akses" class="form-label">Level Akses</label>
                        <select id="level_akses" class="form-select" name='level_akses' value="{{ $alias_group }}">
                            <option value="">-</option>
                            @foreach ($level_akses_list as $key => $value)
                                <option value="{{ $value->alias }}" {{ $alias_group == $value->alias ? 'selected' : '' }}>{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class='bagan_form mt-3'>
                        <label for="status_user" class="form-label">Status Aktif</label>
                        <div class="form-check form-switch">
                            <?php 
                                $checked='checked';
                                if(!empty( $model->id_uxui_users )){
                                    $checked=!empty($model->status_user) ? ($model->status_user == 1 ? 'checked' : '') : '';
                                }
                            ?>
                            <input class="form-check-input" type="checkbox" name="status_user" value="1" {{ $checked }} id="status_user">
                            <div class="message"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-start align-items-end">
            <div class="col-lg-5">
                <button class="btn btn-primary" type="submit">{{ !empty($kode) ? 'Ubah' : 'Simpan' }}</button>
            </div>
        </div>
    </div>
</form>