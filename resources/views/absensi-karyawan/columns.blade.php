<hr style="margin-top:0px">
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="GET">
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-12 col-md-12">
                        <div class="row justify-content-start align-items-end mb-3">

                            <div class="col-lg-3 col-md-10">
                                <div class='input-date-range-bagan'>
                                    <label for="tanggal_data" class="form-label">Tanggal</label>
                                    <span class='icon-bagan-date'></span>
                                    <input type="text" class="form-control input-date-range" id="tanggal_data" placeholder="Tanggal" data-max-range=6>
                                    <input type="hidden" id="tgl_start" name="filter_date_start" value="{{ Request::get('filter_date_start') }}">
                                    <input type="hidden" id="tgl_end" name="filter_date_end" value="{{ Request::get('filter_date_end') }}">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-10">
                                <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                                <input type="text" class="form-control" name='form_filter_text' value="{{ Request::get('form_filter_text') }}" id='filter_search_text' placeholder="Masukkan Kata">
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="row justify-content-start align-items-end mb-3">

                            <div class="col-lg-3 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_nm_jabatan" class="form-label">Jabatan </label>
                                    <div class="button-icon-inside">
                                        <input type="text" class="input-text" id='filter_nm_jabatan' name="filter_nm_jabatan" readonly value="{{ Request::get('filter_nm_jabatan') }}" />
                                        <input type="hidden" id="filter_id_jabatan" name='filter_id_jabatan' readonly required value="{{ Request::get('filter_id_jabatan') }}">
                                        <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_jabatan') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Jabatan' data-modal-width='30%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_jabatan|#filter_nm_jabatan@data-key-bagan=0@data-btn-close=#closeModalData">
                                            <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                        </span>
                                        <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>
                                    </div>
                                    <div class="message"></div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_nm_departemen" class="form-label">Departemen</label>
                                    <div class="button-icon-inside">
                                        <input type="text" class="input-text" id='filter_nm_departemen' name="filter_nm_departemen" readonly value="{{ Request::get('filter_nm_departemen') }}" />
                                        <input type="hidden" id="filter_id_departemen" name='filter_id_departemen' readonly value="{{ Request::get('filter_id_departemen') }}">
                                        <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_departemen') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Departemen' data-modal-width='50%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_departemen|#filter_nm_departemen@data-key-bagan=0@data-btn-close=#closeModalData">
                                            <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                        </span>
                                        <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                                    </div>
                                    <div class="message"></div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_nm_ruangan" class="form-label">Ruangan</label>
                                    <div class="button-icon-inside">
                                        <input type="text" class="input-text" id='filter_nm_ruangan' name="filter_nm_ruangan" readonly value="{{ Request::get('filter_nm_ruangan') }}" />
                                        <input type="hidden" id="filter_id_ruangan" name='filter_id_ruangan' readonly value="{{ Request::get('filter_id_ruangan') }}">
                                        <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_ruangan') }}" data-modal-key-with-form="#filter_id_departemen" data-modal-pencarian='true' data-modal-title='Ruangan' data-modal-width='70%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_ruangan|#filter_nm_ruangan@data-key-bagan=0@data-btn-close=#closeModalData">
                                            <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                        </span>
                                        <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                                    </div>
                                    <div class="message"></div>
                                </div>
                            </div>

                            {{--
                            <div class="col-lg-2 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_cara_absensi" class="form-label">Cara Absen : </label>
                                    <select class="form-select" id="filter_cara_absensi" name="filter_cara_absensi"  aria-label="Default select ">
                                        <option value=""  {{ (Request::get('filter_cara_absensi')=='') ? 'selected' : '' }}>Semua</option>
                                        <option value="1" {{ (Request::get('filter_cara_absensi')=='1') ? 'selected' : '' }}>Finger</option>
                                        <option value="3" {{ (Request::get('filter_cara_absensi')=='3') ? 'selected' : '' }}>Password</option>
                                    </select>
                                    <div class="message"></div>
                                </div>
                            </div>
                            --}}
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="row justify-content-start align-items-end mb-3">
                            <div class="col-lg-4 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_presensi_masuk" class="form-label">Status Presensi Masuk : </label>
                                    <select class="form-select" id="filter_presensi_masuk" name="filter_presensi_masuk"  aria-label="Default select ">
                                        <option value=""  {{ (Request::get('filter_presensi_masuk')=='') ? 'selected' : '' }}>Semua</option>
                                        @if(!empty($get_presensi_masuk))
                                            @foreach($get_presensi_masuk as $key => $val)
                                                <?php $val=(object)$val; ?>
                                                <option value='{{ $key }}' {{ (Request::get('filter_presensi_masuk')==$key) ? 'selected' : '' }}>{{ $val->text }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="message"></div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_presensi_istirahat" class="form-label">Status Presensi istirahat : </label>
                                    <select class="form-select" id="filter_presensi_istirahat" name="filter_presensi_istirahat"  aria-label="Default select ">
                                        <option value=""  {{ (Request::get('filter_presensi_istirahat')=='') ? 'selected' : '' }}>Semua</option>
                                        @if(!empty($get_presensi_istirahat))
                                            @foreach($get_presensi_istirahat as $key => $val)
                                                <?php $val=(object)$val; ?>
                                                <option value='{{ $key }}' {{ (Request::get('filter_presensi_istirahat')==$key) ? 'selected' : '' }}>{{ $val->text }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="message"></div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-10">
                                <div class='bagan_form'>
                                    <label for="filter_presensi_pulang" class="form-label">Status Presensi pulang : </label>
                                    <select class="form-select" id="filter_presensi_pulang" name="filter_presensi_pulang"  aria-label="Default select ">
                                        <option value=""  {{ (Request::get('filter_presensi_pulang')=='') ? 'selected' : '' }}>Semua</option>
                                        @if(!empty($get_presensi_pulang))
                                            @foreach($get_presensi_pulang as $key => $val)
                                                <?php $val=(object)$val; ?>
                                                <option value='{{ $key }}' {{ (Request::get('filter_presensi_pulang')==$key) ? 'selected' : '' }} >{{ $val->text }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="message"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-1 col-md-1">
                        <div class="d-grid grap-2">
                            <button type="submit" class="btn btn-primary" value=1>
                                <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <input type="hidden" id='url_data' value="{{ url('absensi-karyawan/ajax') }}" />
        <textarea id="list_data" style="display:none">{{ $hasil_data }}</textarea>

        <div id="list_columns"></div>
    </div>
</div>

@push('script-end-2')
    <script src="{{ asset('js/absensi-karyawan/columns.js') }}"></script>
@endpush