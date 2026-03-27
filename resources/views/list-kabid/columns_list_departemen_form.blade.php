<hr>
<div>
    <div class="row d-flex justify-content-between">
        <div class='bagan-data-table-cus' data-url="{{ url('list-kabid/ajax?action=list_kabid_master_form') }}">
            <form action="" method="GET">
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-3 col-md-10">
                        <div class='bagan_form'>
                            <label for="filter_nm_departemen" class="form-label">Departemen <span class="text-danger">*</span></label>
                            <div class="button-icon-inside">
                                <input type="text" class="input-text" id='filter_nm_departemen' name="filter_nm_departemen" readonly value="{{ Request::get('filter_nm_departemen') }}" />
                                <input type="hidden" id="filter_id_departemen" name='filter_id_departemen' readonly required value="{{ Request::get('filter_id_departemen') }}">
                                <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_departemen') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Departemen' data-modal-width='50%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_departemen|#filter_nm_departemen@data-key-bagan=0@data-btn-close=#closeModalData">
                                    <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                </span>
                                <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>                            
                            </div>
                            <div class="message"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-10">
                        <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                        <input type="text" class="form-control" name='form_filter_text' value="{{ Request::get('form_filter_text') }}" id='filter_search_text' placeholder="Masukkan Kata">
                    </div>

                    <div class="col-lg-1 col-md-1">
                        <div class="d-grid grap-2">
                            <button class="btn btn-primary">
                                <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div style="overflow-x: auto; max-width: auto;">
                <table class="table border table-responsive-tablet data-table-cus">
                    <thead>
                        <tr>
                            <th class="py-3" style="width: 1%">Pil</th>
                            <th class="py-3" style="width: 20%">Nama Ruangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>