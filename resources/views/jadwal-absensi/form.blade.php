<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <?php
        $kode=!empty($model->id_jadwal) ? $model->id_jadwal : '';
    ?>  
    <input type="hidden" name="key_old" value="{{ $kode }}">

    <div class="row d-flex justify-content-between">
        <div class="col-lg-6">
            <table class="table table-bordered table-responsive-tablet">
                <tbody>
                    <tr>
                        <td rowspan="2" style='width: 20%; vertical-align: middle;'>Jenis Jadwal</td>
                        <td style='width: 1%; vertical-align: middle;'>:</td>
                        <td style='width: 69%; vertical-align: middle;'>{{ !empty($model->nm_jenis_jadwal) ? $model->nm_jenis_jadwal : '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row d-flex justify-content-between">
        <div class="col-md-8">
            <div class="row justify-content-start align-items-end mb-3">

                <div class="col-lg-6">
                    <div class='bagan_form'>
                        <label for="uraian" class="form-label">Nama Jadwal</label>
                        <input type="text" class="form-control" id="uraian" name='uraian' required value="{{ !empty($model->uraian) ? $model->uraian : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-12">
            <div class="row justify-content-start align-items-end mb-3">
                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="toren_jam_cepat" class="form-label">Toleransi Presensi Awal / Cepat Pulang</label>
                        <input type="time" class="form-control input-daterange" id="toren_jam_cepat" name='toren_jam_cepat' required value="{{ !empty($model->toren_jam_cepat) ? $model->toren_jam_cepat : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="jam_awal" class="form-label">Jam Mulai</label>
                        <input type="time" class="form-control input-daterange" id="jam_awal" name='jam_awal' required value="{{ !empty($model->jam_awal) ? $model->jam_awal : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="jam_akhir" class="form-label">s/d Jam</label>
                        <input type="time" class="form-control input-daterange" id="jam_akhir" name='jam_akhir' required value="{{ !empty($model->jam_akhir) ? $model->jam_akhir : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="toren_jam_telat" class="form-label">Toleransi Telat</label>
                        <input type="time" class="form-control input-daterange" id="toren_jam_telat" name='toren_jam_telat' required value="{{ !empty($model->toren_jam_telat) ? $model->toren_jam_telat : '' }}">
                        <div class="message"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="row justify-content-start align-items-end mb-3">
                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="status_jadwal" class="form-label">Status Jadwal</label>
                        <div class="form-check form-switch">
                            <?php 
                                $checked=!empty($model->status_jadwal) ? ($model->status_jadwal == 1 ? 'checked' : '') : '';
                            ?>
                            <input class="form-check-input" type="checkbox" name="status_jadwal" value="1" {{ $checked }} id="status_user">
                            <div class="message"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-start align-items-end">
        <div class="col-lg-5">
            <button class="btn btn-primary" type="submit">{{ !empty($kode) ? 'Ubah' : 'Simpan'  }}</button>
        </div>
    </div>
</form>
