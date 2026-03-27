<div class="card border-2 mt-2">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">Rekap Lembur</h6>
    </div>

    <div class="card-body">
        <form action="" method="GET">
            <div class="row justify-content-end mb-3">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="bagan_form">
                        <label for="filter_nm_departemen" class="form-label">
                            Bagian
                        </label>

                        <div class="button-icon-inside">
                            <input type="text" class="input-text" id="filter_nm_departemen"
                                name="filter_nm_departemen" readonly value="{{ Request::get('filter_nm_departemen') }}">

                            <input type="hidden" id="filter_id_departemen" name="filter_id_departemen" required
                                value="{{ Request::get('filter_id_departemen') }}">

                            <span class="modal-remote-data"
                                data-modal-src="{{ url('ajax?action=get_list_departemen') }}" data-modal-key=""
                                data-modal-pencarian="true" data-modal-title="Departemen" data-modal-width="30%"
                                data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_departemen|#filter_nm_departemen@data-key-bagan=0@data-btn-close=#closeModalData">
                                <img src="{{ asset('icon/selected.png') }}" alt="Pilih Departemen"
                                    class="iconify hover-pointer text-primary">
                            </span>

                            <a href="#" class="reset-input">
                                <i class="fa-solid fa-square-xmark"></i>
                            </a>
                        </div>

                        <div class="message"></div>
                    </div>
                </div>

                <!-- Ruangan -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="bagan_form">
                        <label for="filter_nm_ruangan" class="form-label">
                            Unit
                        </label>

                        <div class="button-icon-inside">
                            <input type="text" class="input-text" id="filter_nm_ruangan" name="filter_nm_ruangan"
                                readonly value="{{ Request::get('filter_nm_ruangan') }}">

                            <input type="hidden" id="filter_id_ruangan" name="filter_id_ruangan" required
                                value="{{ Request::get('filter_id_ruangan') }}">

                            <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_ruangan') }}"
                                data-modal-key-with-form="#filter_id_departemen" data-modal-pencarian="true"
                                data-modal-title="Ruangan" data-modal-width="30%"
                                data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_ruangan|#filter_nm_ruangan@data-key-bagan=0@data-btn-close=#closeModalData">
                                <img src="{{ asset('icon/selected.png') }}" alt="Pilih Ruangan"
                                    class="iconify hover-pointer text-primary">
                            </span>

                            <a href="#" class="reset-input">
                                <i class="fa-solid fa-square-xmark"></i>
                            </a>
                        </div>

                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <label for="tgl_rawat" class="form-label">Tanggal</label>
                    <div class='input-date-range-bagan'>
                        <input type="text" class="form-control input-daterange input-date-range" id="tgl_rawat"
                            placeholder="Tanggal">
                        <input type="hidden" id="tgl_start" name="form_filter_start"
                            value="{{ Request::get('form_filter_start') }}">
                        <input type="hidden" id="tgl_end" name="form_filter_end"
                            value="{{ Request::get('form_filter_end') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <label for="filter_search_text" class="form-label">Search</label>

                    <div class="input-group">
                        <input type="text" class="form-control py-2" id="filter_search_text" name="form_filter_text"
                            value="{{ Request::get('form_filter_text') }}" placeholder="Masukkan kata kunci..."
                            style="border-right: none;">

                        <button type="submit" class="btn btn-primary px-3">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>

            
        </form>
    </div>


    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0"
            style="width:100%; border: 1px solid #dee2e6;">
            <thead class="table" style="background-color: #CEE5FF;">
                <tr class="text-center">

                    <th style="width: 5%">No</th>
                    <th style="width: 20%">Nama</th>
                    <th style="width: 10%">Bagian</th>
                    <th style="width: 15%">Unit</th>
                    <th style="width: 15%">Total Jam Lembur</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($list_data) && count($list_data) > 0)
                    @foreach ($list_data as $key => $item)
                        <tr>
                            <td class="text-center font-monospace">{{ $key + 1 }}</td>
                            <td class="fw-bold">{{ $item->nm_karyawan ?? '-' }}</td>
                            <td class="text-nowrap text-center">
                                {{ $item->nm_departemen }}

                            </td>
                            <td class="text-nowrap text-center">
                                {{ $item->nm_ruangan }}

                            </td>
                            <td class="text-nowrap text-center">
                                {{ $item->total_jam_lembur }}

                            </td>

                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Data tidak ditemukan</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if (!empty($list_data))
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Showing {{ $list_data->count() }} entries</span>
            {{ $list_data->withQueryString()->links() }}
        </div>
    @endif
</div>

</div>
