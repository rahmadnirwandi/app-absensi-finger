<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <?php
    $kode = !empty($model->id_karyawan) ? $model->id_karyawan : '';
    
    ?>
    <input type="hidden" name="key_old" value="{{ $kode }}">

    <div class="row justify-content-start align-items-end mb-3">
        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">

                <div class="col-lg-3">
                    <div class='bagan_form'>
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" class="form-control" id="nip" name='nip' required
                            value="{{ !empty($model->nip) ? $model->nip : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class='bagan_form'>
                        <label for="nm_karyawan" class="form-label">Nama Karyawan</label>
                        <input type="text" class="form-control" id="nm_karyawan" name='nm_karyawan' required
                            value="{{ !empty($model->nm_karyawan) ? $model->nm_karyawan : '' }}">
                        <div class="message"></div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class='bagan_form'>
                        <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
                        <input
                                type="date"
                                class="form-control"
                                id="tgl_masuk"
                                name="tgl_masuk"
                                required
                                value="{{ !empty($model->tgl_masuk) ? $model->tgl_masuk : '' }}"
                        >
                        <div class="message"></div>
                    </div>
                </div>

            </div>
        </div>

{{--        <div class="col-lg-12">--}}
{{--            <div class="row justify-content-start align-items-end">--}}
{{--                <div class="col-lg-12 mb-3">--}}
{{--                    <div class='bagan_form'>--}}
{{--                        <label for="alamat" class="form-label">Alamat</label>--}}
{{--                        <textarea class="form-control" id="alamat" name="alamat" rows="2">{{ !empty($model->alamat) ? $model->alamat : '' }}</textarea>--}}
{{--                        <div class="message"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">
                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="nm_jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <div class="button-icon-inside">
                            <input type="text" class="input-text" id='nm_jabatan' name="nm_jabatan" disabled required
                                value="{{ !empty($model->nm_jabatan) ? $model->nm_jabatan : '' }}" />
                            <input type="hidden" id="id_jabatan" name='id_jabatan' required
                                value="{{ !empty($model->id_jabatan) ? $model->id_jabatan : '' }}">
                            <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_jabatan') }}"
                                data-modal-key="" data-modal-pencarian='true' data-modal-title='Jabatan'
                                data-modal-width='30%'
                                data-modal-action-change="function=.set-data-list-from-modal@data-target=#id_jabatan|#nm_jabatan@data-key-bagan=0@data-btn-close=#closeModalData">
                                <img class="iconify hover-pointer text-primary"
                                    src="{{ asset('') }}icon/selected.png" alt="">
                            </span>
                            <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="nm_departemen" class="form-label">Departemen <span
                                class="text-danger">*</span></label>
                        <div class="button-icon-inside" id='tes'>
                            <input type="text" class="input-text" id='nm_departemen' name="nm_departemen" readonly
                                disabled value="{{ !empty($model->nm_departemen) ? $model->nm_departemen : '' }}" />
                            <input type="hidden" id="id_departemen" name='id_departemen' required
                                value="{{ !empty($model->id_departemen) ? $model->id_departemen : '' }}">
                            <span class="modal-remote-data"
                                data-modal-src="{{ url('ajax?action=get_list_departemen') }}" data-modal-key=""
                                data-modal-pencarian='true' data-modal-title='Departemen' data-modal-width='50%'
                                data-modal-action-change="function=.set-data-list-from-modal@data-target=#id_departemen|#nm_departemen@data-key-bagan=0@data-btn-close=#closeModalData">
                                <img class="iconify hover-pointer text-primary"
                                    src="{{ asset('') }}icon/selected.png" alt="">
                            </span>
                            <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="nm_ruangan" class="form-label">Ruangan <span class="text-danger">*</span></label>
                        <div class="button-icon-inside">
                            <input type="text" class="input-text" id='nm_ruangan' name="nm_ruangan" readonly
                                disabled value="{{ !empty($model->nm_ruangan) ? $model->nm_ruangan : '' }}" />
                            <input type="hidden" id="id_ruangan" name='id_ruangan'
                                value="{{ !empty($model->id_ruangan) ? $model->id_ruangan : '' }}">
                            <span class="modal-remote-data"
                                data-modal-src="{{ url('ajax?action=get_list_ruangan') }}"
                                data-modal-key-with-form="#id_departemen" data-modal-pencarian='true'
                                data-modal-title='Departemen' data-modal-width='70%'
                                data-modal-action-change="function=.set-data-list-from-modal@data-target=#id_ruangan|#nm_ruangan@data-key-bagan=0@data-btn-close=#closeModalData">
                                <img class="iconify hover-pointer text-primary"
                                    src="{{ asset('') }}icon/selected.png" alt="">
                            </span>
                            <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="nm_status_karyawan" class="form-label">Status Karyawan <span
                                class="text-danger">*</span></label>
                        <div class="button-icon-inside">
                            <input type="text" class="input-text" id='nm_status_karyawan'
                                name="nm_status_karyawan" disabled required
                                value="{{ !empty($model->nm_status_karyawan) ? $model->nm_status_karyawan : '' }}" />
                            <input type="hidden" id="id_status_karyawan" name='id_status_karyawan' required
                                value="{{ !empty($model->id_status_karyawan) ? $model->id_status_karyawan : '' }}">
                            <span class="modal-remote-data"
                                data-modal-src="{{ url('ajax?action=get_list_status_karyawan') }}" data-modal-key=""
                                data-modal-pencarian='true' data-modal-title='Jabatan' data-modal-width='30%'
                                data-modal-action-change="function=.set-data-list-from-modal@data-target=#id_status_karyawan|#nm_status_karyawan@data-key-bagan=0@data-btn-close=#closeModalData">
                                <img class="iconify hover-pointer text-primary"
                                    src="{{ asset('') }}icon/selected.png" alt="">
                            </span>
                            <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-start align-items-end">
        <div class="col-lg-5">
            <button class="btn btn-primary validate_submit"
                type="submit">{{ !empty($kode) ? 'Ubah' : 'Simpan' }}</button>
        </div>
    </div>
</form>
