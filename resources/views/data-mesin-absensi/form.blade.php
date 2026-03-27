<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <?php 
        $kode=!empty($model->id_mesin_absensi) ? $model->id_mesin_absensi : '';
    ?>
    <input type="hidden" name="key_old" value="{{ $kode }}">

    <div class="row justify-content-start  mb-3">
        <div class="col-lg-6">
            <div class="row justify-content-start ">
                <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <label for="ip_address" class="form-label">Ip Address</label>
                        <input type="text" class="form-control format_ip_address" id="ip_address" name='ip_address' required value="{{ !empty($model->ip_address) ? $model->ip_address : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <label for="comm_key" class="form-label">Comm Key Mesin</label>
                        <input type="number" class="form-control" id="comm_key" name='comm_key' required value="{{ !empty($model->comm_key) ? $model->comm_key : 0 }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <label for="nm_mesin" class="form-label">Nama/Alias Mesin</label>
                        <input type="text" class="form-control" id="nm_mesin" name='nm_mesin' value="{{ !empty($model->nm_mesin) ? $model->nm_mesin : '' }}">
                        <div class="message"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row justify-content-start ">

                <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <label for="sn" class="form-label">Serial Number (SN)</label>
                        <input type="text" class="form-control" id="sn" name='sn' value="{{ !empty($model->sn) ? $model->sn : '' }}">
                        <div class="message"></div>
                    </div>
                </div>
                
                <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <label for="lokasi_mesin" class="form-label">Lokasi Mesin</label>
                        <textarea class="form-control" id="lokasi_mesin" name="lokasi_mesin" rows="2">{{!empty($model->lokasi_mesin) ? $model->lokasi_mesin : ''}}</textarea>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class='bagan_form'>
                        <label for="prefix" class="form-label">Status Aktif</label>
                        <div class="form-check form-switch">
                            <?php $checked=!empty($model->status_mesin) ? ($model->status_mesin == 1 ? 'checked' : '') : ''; ?>
                            <input class="form-check-input" type="checkbox" name="status_mesin" value="1" {{ $checked }} id="flexSwitchCheckChecked">
                            <div class="message"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        
    </div>

    <div class="row justify-content-start">
        <div class="col-lg-5">
            <button class="btn btn-primary" type="submit">{{ !empty($kode) ? 'Ubah' : 'Simpan'  }}</button>
        </div>
    </div>
</form>
