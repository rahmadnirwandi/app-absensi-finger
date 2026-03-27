<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <?php
        $kode=!empty($model->tanggal) ? $model->tanggal : '';
    ?>
    <input type="hidden" name="key_old" value="{{ $kode }}">

    <div class="row justify-content-start align-items-end mb-3">
        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">
                <div class="col-lg-4">
                    <div class='bagan_form'>
                        <div class='input-date-bagan'>
                            <label for="tanggal" class="form-label">Tanggal : <span class="text-danger">*</span></label>
                            <input type="text" class="form-control input-date" id='tanggal' autocomplete="off">
                            <?php
                                $tanggal='';
                                $mtanggal=!empty($model->tanggal) ? $model->tanggal : '';
                                $mtanggal_req_tmp=Request::get('filter_tahun_bulan');
                                $mtanggal_req_tmp=new \Datetime($mtanggal_req_tmp);
                                $mtanggal_req=$mtanggal_req_tmp->format('Y-m-d');
                                if(!empty($mtanggal)){
                                    $tanggal=$mtanggal;
                                }else{
                                    $tanggal=$mtanggal_req;
                                }
                            ?>
                            <input type="hidden" id="tgl" required name="tanggal" value="{{ $tanggal }}">
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="jumlah" class="form-label">Jumlah Hari<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" min='1' required value="{{ !empty($model->jumlah) ? $model->jumlah : 1 }}">
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class='bagan_form'>
                        <label for="uraian" class="form-label">Nama Hari Libur<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="uraian" name='uraian'  required value="{{ !empty($model->uraian) ? $model->uraian : '' }}">
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
