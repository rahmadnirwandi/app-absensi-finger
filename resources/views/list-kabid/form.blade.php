<?php
    $router_name=(new \App\Http\Traits\GlobalFunction)->getRouterIndex();
?>
<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <?php 
        $kode=!empty($model->id_karyawan) ? $model->id_karyawan : '';
    ?>
    <input type="hidden" name="key_old" value="{{ $kode }}">
    <div class="row justify-content-start mb-3">
        <div class="col-lg-12">
            <div class="row justify-content-left">
                <div class="col-lg-6 mb-3">
                    <div class='bagan_form'>
                    <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nip" name='nip' readonly required value="{{ !empty($model->nip) ? $model->nip : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
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
    </div>
    <textarea id='item_list_terpilih' name='item_list_terpilih' style='display:none'>{{ !empty($item_list_terpilih) ? $item_list_terpilih : '' }}</textarea>
    
    <hr>
    <div class="card card-body" style='background:#bbe7fa'>
        <div id="data-terpilih">
            <h4>List Data Terpilih</h4>
            <div style="overflow-x: auto; max-width: auto;">
                <table class="table border table-responsive-tablet">
                    <thead>
                        <tr>
                            <th class="py-3" style="width: 5%">Aksi</th>
                            <th class="py-3" style="width: 20%">Nama Ruangan</th>
                            <th class="py-3" style="width: 1%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
        </div>
    </div>

    <div class="row justify-content-end align-items-end mt-1">
        <div class="col-md-2 text-center">
            <button class="btn btn-primary btn-block" id='btn_save' type="submit">{{ !empty($kode) ? 'Ubah' : 'Simpan'  }}</button>
        </div>
    </div>
</form>

<div class="card card-body mt-5">
    <h4>List Data Departemen</h4>
    <div id="list-data">
        @include($router_name->path_base.'.columns_list_departemen_form')
    </div>
</div>


@push('script-end-2')
    <script src="{{ asset('js/list-kabid/form.js') }}"></script>
@endpush