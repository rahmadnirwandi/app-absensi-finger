
<form action="{{ url($action_form) }}" method="POST">

    @csrf

    <div class="row g-3">

        <div class="col-lg-6 mb-3">
            <div class='bagan_form'>
                <label for="nm_karyawan" class="form-label">Pilih Karyawan <span class="text-danger">*</span></label>
                <div class="button-icon-inside">
                    <input type="text" class="input-text" id='nm_karyawan' readonly required value="{{ !empty($model->nm_karyawan) ? $model->nm_karyawan : '' }}" />
                    <input type="hidden" id="id_karyawan" name='id_karyawan' required value="{{ !empty($model->id_karyawan) ? $model->id_karyawan : '' }}">
                    <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_karyawan') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Data Karyawan' data-modal-width='80%' 
                    data-modal-action-change="function=.set-data-list-from-modal@data-target=#id_karyawan|#nm_karyawan|#nip@data-key-bagan=0@data-btn-close=#closeModalData">
                        <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                    </span>
                    <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                </div>
                <div class="message"></div>
            </div>
        </div>

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
                    <label for="nm_departemen" class="form-label">Bagian <span
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
                    <label for="nm_ruangan" class="form-label">Unit <span class="text-danger">*</span></label>
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
            <div class="col-lg-4 col-md-6 col-sm-12">
                <label class="form-label">Jenis Pengajuan</label>
                <select name="jenis_pengajuan" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="izin">Izin</option>
                    <option value="cuti">Cuti</option>
                    <option value="lembur">Lembur</option>
                </select>
            </div>

        {{-- Submit --}}
        <div class="col-12 mt-3">
            <button class="btn btn-primary px-4">
                Simpan
            </button>
        </div>

    </div>
</form>
