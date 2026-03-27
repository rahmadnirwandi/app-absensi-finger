<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    
    <input type="hidden" name="data_sent" value="{{ Request::get('data_sent') }}">
    <input type="hidden" name="id_user" value="{{ !empty($model->id_user) ? $model->id_user : '' }}">
    <input type="hidden" name="id_karyawan_old" value="{{ !empty($model->id_karyawan) ? $model->id_karyawan : '' }}">
    <input type="hidden" name="params_respon" value="{{ !empty($paramater_url_back_tmp) ? $paramater_url_back_tmp : '' }}">

    <div class="row justify-content-start align-items-end mb-3">
        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">
                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="id_user" class="form-label">Id User Pada Mesin</label>
                        <input type="text" class="form-control" id="id_user" readonly value="{{ !empty($model->id_user) ? $model->id_user : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="name" class="form-label">Nama User Pada Mesin</label>
                        <input type="text" class="form-control" id="name" readonly value="{{ !empty($model->name) ? $model->name : '' }}">
                        <div class="message"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">
                <div class="col-lg-5 mb-3">
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
            </div>
        </div>

        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" class="form-control" id="nip" readonly value="{{ !empty($model->nip) ? $model->nip : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="nm_jabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control" id="nm_jabatan" readonly value="{{ !empty($model->nm_jabatan) ? $model->nm_jabatan : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="nm_departemen" class="form-label">Departemen</label>
                        <input type="text" class="form-control" id="nm_departemen" readonly value="{{ !empty($model->nm_departemen) ? $model->nm_departemen : '' }}">
                        <div class="message"></div>
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

@push('script-end-2')
<script src="{{ asset('js/data-user-mesin/form.js') }}"></script>
@endpush