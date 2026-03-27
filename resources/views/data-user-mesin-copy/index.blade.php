@extends('layouts.master')

@section('title-header', $title)

@section('breadcrumbs')
@include('layouts.breadcrumbs')
@endsection

<?php 
    $router_name=(new \App\Http\Traits\GlobalFunction)->getRouterIndex();
?>

@section('content')

@include('data-user-mesin.tab_user_mesin', ["active"=>3])

<div>
    <br>
    <div class="row d-flex justify-content-between">
        <div>
            <form action="" method="POST">
                @csrf
                <div class="col-md-12">
                    <div class="row justify-content-start align-items-end mb-3">
                        <div class="col-lg-3 col-md-10">
                            <div class='bagan_form'>
                                <label for="ip_mesin_tujuan" class="form-label">Pilih Data Mesin Tujuan<span class="text-danger">*</span></label>
                                <div class="button-icon-inside">
                                    <input type="text" class="input-text" id='ip_mesin_tujuan' required value="" />
                                    <input type="hidden" id="id_mesin_tujuan" name="id_mesin_tujuan" value="" />
                                    <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_mesih_absensi') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Jenis' data-modal-width='40%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#id_mesin_tujuan|#ip_mesin_tujuan|null|#nama_mesin_tujuan|#lokasi_mesin_tujuan@data-key-bagan=0@data-btn-close=#closeModalData">
                                        <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                    </span>
                                    <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>
                                </div>
                                <div class="message"></div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-10">
                            <div class='bagan_form'>
                            <label for="nama_mesin_tujuan" class="form-label">Nama Mesin Tujuan</label>
                                <input type="text" class="form-control" id="nama_mesin_tujuan" readonly value="">
                                <div class="message"></div>
                            </div>
                        </div>

                        <div class="col-lg-5 col-md-10">
                            <div class='bagan_form'>
                            <label for="lokasi_mesin_tujuan" class="form-label">Lokasi Mesin Tujuan</label>
                                <input type="text" class="form-control" id="lokasi_mesin_tujuan" readonly value="">
                                <div class="message"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="card card-body" style='background:#bbe7fa'>
                            <div id="data-terpilih">
                                <h4>List Data Terpilih</h4>
                                <div style="overflow-x: auto; max-width: auto;">
                                    <table class="table border table-responsive-tablet">
                                        <thead>
                                            <tr>
                                                <th class="py-3" style="width: 25%">User Mesin</th>
                                                <th class="py-3" style="width: 10%">Group/Privilege</th>
                                                <th class="py-3" style="width: 15%">Nama Karyawan</th>
                                                <th class="py-3" style="width: 15%">Departemen</th>
                                                <th class="py-3" style="width: 15%">Ruangan</th>
                                                <th class="py-3" style="width: 1%">action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                
                            </div>
                        </div>

                        <textarea id='item_list_terpilih' name='item_list_terpilih' style='display:block' >{{ !empty($item_list_terpilih) ? $item_list_terpilih : '' }}</textarea>
                        
                    </div>
                </div>

                @if( (new \App\Http\Traits\AuthFunction)->checkAkses($router_name->uri.'/update') )
                    <div class="col-lg-12 col-md-12">
                        <div class="row align-items-end">
                            <div class="d-flex justify-content-end">
                                <button type="submit" name='proses' class="btn btn-primary validate_submit" value=1>
                                    <span>Proses</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
    

    
<hr>
<div class="card card-body mt-5">
    <h4>List User Mesin</h4>
    <div id="list-data">
        @include($router_name->path_base.'.columns')
    </div>
</div>

@push('script-end-2')
    <script src="{{ asset('js/data-user-mesin-copy/form.js') }}"></script>
@endpush

@endsection
