@extends('layouts.master')

@section('title-header', $title)

@section('breadcrumbs')
@include('layouts.breadcrumbs')
@endsection

<?php 
    $router_name=(new \App\Http\Traits\GlobalFunction)->getRouterIndex();
?>

@section('content')

@include('data-user-mesin.tab_user_mesin', ["active"=>1])

<div>
    <br>
    <div class="row d-flex justify-content-between">
        <div class="card card-body" style='background:#9bf80030'>
            <h4>1. Form Untuk Tarik Data User Dari Mesin</h4>
            <p>
                Keterangan<br>
                Tarik Data User dari mesin mengunakan form ini
                <br>
                <span style='color:RED'>
                    NOTE : Jika Form ini di proses, maka data di form 2 akan berubah kembali berdasarkan data di mesin<br>
                </span>
            </p>
            <hr>
            <form action="" method="GET">
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-3 col-md-10">
                        <div class='bagan_form'>
                            <label for="filter_ip_mesin" class="form-label">Pilih Data Mesin <span class="text-danger">*</span></label>
                            <div class="button-icon-inside">
                                <input type="text" class="input-text" id='filter_ip_mesin' required value="{{ !empty($data_mesin->ip_address) ? $data_mesin->ip_address : '' }}" />
                                <input type="hidden" id="filter_id_mesin" name="filter_id_mesin" value="{{ Request::get('filter_id_mesin') }}" />
                                <span class="modal-remote-data" data-modal-src="{{ url('ajax?action=get_list_mesih_absensi') }}" data-modal-key="" data-modal-pencarian='true' data-modal-title='Jenis' data-modal-width='40%' data-modal-action-change="function=.set-data-list-from-modal@data-target=#filter_id_mesin|#filter_ip_mesin|null|#filter_nama_mesin|#filter_lokasi_mesin@data-key-bagan=0@data-btn-close=#closeModalData">
                                    <img class="iconify hover-pointer text-primary" src="{{ asset('') }}icon/selected.png" alt="">
                                </span>
                                <a href="#" id='reset_input'><i class="fa-solid fa-square-xmark"></i></a>
                            </div>
                            <div class="message"></div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-10">
                        <div class='bagan_form'>
                        <label for="filter_nama_mesin" class="form-label">Nama Mesin</label>
                            <input type="text" class="form-control" id="filter_nama_mesin" readonly value="{{ !empty($data_mesin->nm_mesin) ? $data_mesin->nm_mesin : '' }}">
                            <div class="message"></div>
                        </div>
                    </div>

                    <div class="col-lg-5 col-md-10">
                        <div class='bagan_form'>
                        <label for="filter_lokasi_mesin" class="form-label">Lokasi Mesin</label>
                            <input type="text" class="form-control" id="filter_lokasi_mesin" readonly value="{{ !empty($data_mesin->lokasi_mesin) ? $data_mesin->lokasi_mesin : '' }}">
                            <div class="message"></div>
                        </div>
                    </div>

                    <div class="col-lg-1 col-md-1">
                        <div class="d-grid grap-2">
                            <button type="submit" name='searchbymesin' class="btn btn-primary" value=1>
                                <span>Proses</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<br><br>


<div class="row d-flex justify-content-between">
    <div class="card card-body" style='background:#f2f2f2'>
        <h4>2. Form Proses Data sebelum di masukan ke database</h4>
        <p>
            Keterangan<br>
            Proses data sebelum di disimpan ke dalam database utama,data ini bersifat temporari, jika form 1 di jalankan, data akan berubah kembali
            <br>
            <span style='color:RED'>
                NOTE : Data dengan id user dan username yang sama( duplicate )akan mengubah data yang telah tersimpan di database utama,<br> 
                silahkan cek kembali id user dan username dari database utama, sebelum anda melakukan sinkronisasi
            </span>
        </p>
        @include($router_name->path_base.'.columns')
        
        @if(!empty( $list_data ))
            @if(!empty( $list_data->total() ))
                <?php $url_sinkron=$router_name->uri.'/sinkron'; ?>
                @if( (new \App\Http\Traits\AuthFunction)->checkAkses($url_sinkron) )
                    <form action="{{ url($url_sinkron) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
                        @csrf
                        <input type="hidden" name="key" value="{{ !empty($data_mesin->id_mesin_absensi) ? $data_mesin->id_mesin_absensi : '' }}">

                        <div class="row justify-content-start align-items-end">
                            <div class="col-lg-5">
                                <button class="btn btn-primary" type="submit">Sinkronisasi</button>
                            </div>
                        </div>
                    </form>
                @endif
            @endif
        @endif
    </div>
</div>

@endsection