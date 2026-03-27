
<form action="{{ url($action_form) }}" method="POST"
    enctype="multipart/form-data">

    @csrf
    <input type="hidden" name="key_old" value="{{ $model->id_karyawan ?? '' }}">
    <input type="hidden" id="id_ruangan" name='id_ruangan' value="{{ !empty($model->id_ruangan) ? $model->id_ruangan : '' }}">
    <input type="hidden" id="id_departemen" name='id_departemen' required value="{{ !empty($model->id_departemen) ? $model->id_departemen : '' }}">
    <input type="hidden" id="id_jabatan" name='id_jabatan' required value="{{ !empty($model->id_jabatan) ? $model->id_jabatan : '' }}">

    <div class="row g-3">

        {{-- NIP & Nama --}}
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label class="form-label">NIP</label>
            <input type="text" class="form-control" name="nip" required value="{{ $model->nip ?? '' }}">
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12">
            <label class="form-label">Nama Karyawan</label>
            <input type="text" class="form-control" name="nm_karyawan" required
                value="{{ $model->nm_karyawan ?? '' }}">
        </div>

        {{-- Keterangan --}}
        <div class="col-12">
            <label class="form-label">Keterangan</label>
            <textarea class="form-control" name="keterangan" rows="2"></textarea>
        </div>

        <div class="col-lg-4 mb-3">
            <div class='bagan_form'>
                <label for="nama_jenis_cuti" class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                <div class="button-icon-inside">
                    <input type="text" class="input-text" id='nama_jenis_cuti' name="nama_jenis_cuti" readonly required  />
                    <input type="hidden" id="id_jenis_cuti" name='id_jenis_cuti' required>
                    <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_jenis_cuti_karyawan') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Jenis Cuti' data-modal-width='40%' 
                    data-modal-action-change="function=.set-data-list-from-modal@data-target=#id_jenis_cuti|#nama_jenis_cuti|#sisa@data-key-bagan=0@data-btn-close=#closeModalData">
                        <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                    </span>
                    <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                </div>
                <div class="message"></div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-control" name="tgl_mulai" value="{{ date('Y-m-d') }}" required>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" class="form-control" name="tgl_selesai" value="{{ date('Y-m-d') }}" required>
        </div>

        {{-- Submit --}}
        <div class="col-12 mt-3">
            <button class="btn btn-primary px-4">
                Simpan
            </button>
        </div>

    </div>
</form>
