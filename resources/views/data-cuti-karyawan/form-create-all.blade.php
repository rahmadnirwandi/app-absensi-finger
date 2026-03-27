<form action="{{ url($action_form) }}" method="POST">
    @csrf
    <div class="row justify-content-start align-items-end mb-3">
        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">

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
                        <label for="nama_jenis_cuti_all" class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                        <div class="button-icon-inside">
                            <input type="text" class="input-text" id='nama_jenis_cuti_all' readonly required  />
                            <input type="hidden" id="id_jenis_cuti_all" name='id_jenis_cuti_all' required>
                            <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_jenis_cuti') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Jenis Cuti' data-modal-width='40%' 
                            data-modal-action-change="function=.set-data-list-from-modal@data-target=#id_jenis_cuti_all|#nama_jenis_cuti_all|#jumlah_cuti_all@data-key-bagan=0@data-btn-close=#closeModalData">
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
                            <label for="jumlah_cuti_all" class="form-label">Jumlah Cuti<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah_cuti_all" name="jumlah_cuti_all" min='0' required>
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
