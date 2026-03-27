<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <?php
        $kode=!empty($model->id_cuti_kary) ? $model->id_cuti_kary : '';
    ?>
    <input type="hidden" name="key_old" value="{{ $kode }}">

    <div class="row justify-content-start align-items-end mb-3">
        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">
                <div class="col-lg-3">
                    <div class='bagan_form'>
                        <label for="nm_karyawan" class="form-label">Pilih Karyawan <span class="text-danger">*</span></label>
                        <div class="button-icon-inside">
                            <input type="text" class="input-text" id='nm_karyawan' readonly required value="{{ !empty($model->nm_karyawan) ? $model->nm_karyawan : '' }}" />
                            <input type="hidden" id="id_karyawan" name='id_karyawan' required value="{{ !empty($model->id_karyawan) ? $model->id_karyawan : '' }}">
                            <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_karyawan') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Data Karyawan' data-modal-width='80%' 
                            data-modal-action-change="function=.set-data-list-from-modal@data-target=#id_karyawan|#nm_karyawan|#nip||#nm_jabatan||#nm_departemen@data-key-bagan=0@data-btn-close=#closeModalData">
                                <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                            </span>
                            <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class='bagan_form'>
                        <label for="tanggal_data" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <div class="input-group input-date-range-bagan">
                            <input type="text" class="form-control input-date-range" id="tanggal_data" placeholder="Tanggal">
                            <input type="hidden" id="tgl_start" name="tgl_mulai" value="{{ !empty($model->tgl_mulai) ? $model->tgl_mulai : '' }}">
                            <input type="hidden" id="tgl_end" name="tgl_selesai" value="{{ !empty($model->tgl_selesai) ? $model->tgl_selesai : '' }}">
                            <span class="input-group-text bg-primary">
                                <i class="fa-solid fa-calendar-days text-white"></i>
                            </span>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class='bagan_form'>
                        <label for="jumlah" class="form-label">Jumlah Hari<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" min='1' value="{{ !empty($model->jumlah) ? $model->jumlah : '0' }}" readonly>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class='bagan_form'>
                        <label for="uraian" class="form-label">Nama Hari Cuti<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="uraian" name='uraian'  required value="{{ !empty($model->uraian) ? $model->uraian : '' }}">
                        <div class="message"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button class="btn btn-primary validate_submit" type="submit">{{ !empty($kode) ? 'Ubah' : 'Simpan'  }}</button>
    </div>
</form>
