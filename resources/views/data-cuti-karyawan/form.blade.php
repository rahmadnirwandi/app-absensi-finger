<form action="{{ url($action_form) }}" method="POST">
    @csrf
    <div class="row justify-content-start align-items-end mb-3">
        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">

                <div class="col-lg-4 mb-3">
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

                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                    <label class="form-label">Awal Cuti</label>
                    <input type="date" class="form-control" name="awal_cuti" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                    <label class="form-label">Akhir Cuti</label>
                    <input type="date" class="form-control" name="akhir_cuti" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class='bagan_form'>
                        <label for="nama" class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                        <div class="button-icon-inside">
                            <input type="text" class="input-text" id='nama' readonly required  />
                            <input type="hidden" id="id_jenis_cuti" name='id_jenis_cuti' required>
                            <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_jenis_cuti') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Jenis Cuti' data-modal-width='40%' 
                            data-modal-action-change="function=.set-data-list-from-modal@data-target=#id_jenis_cuti|#nama|#jumlah_cuti@data-key-bagan=0@data-btn-close=#closeModalData">
                                <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                            </span>
                            <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                 <div class="col-lg-4 mb-3">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="jumlah_cuti" class="form-label">Jumlah Cuti<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah_cuti" name="jumlah_cuti" min='0' required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="pakai" class="form-label">Pakai<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pakai" name="pakai" min='0' required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="sisa" class="form-label">Sisa<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="sisa" name="sisa" min='0' required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="tukar" class="form-label">Tukar<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="tukar" name="tukar" min='0' required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                
            </div>
        </div>
    </div>

    <div class="row justify-content-start align-items-end">
        <div class="col-lg-5">
            <button class="btn btn-primary validate_submit" type="submit">Simpan</button>
        </div>
    </div>
</form>
