@extends('layouts.master')

@section('title-header', $title)

@section('breadcrumbs')
@include('layouts.breadcrumbs')
@endsection

<?php 
    $router_name=(new \App\Http\Traits\GlobalFunction)->getRouterIndex();
?>

<style>

.modal-top-rejected {
    align-items: flex-start !important;
    margin-top: 8vh;
}
.modal-top-approved {
    align-items: flex-start !important;
    margin-top: 8vh;
}

@media (min-width: 992px) {
    .modal-top {
        margin-top: 10vh;
    }
}

@media (max-width: 576px) {
    .btn-persetujuan {
        width: 14vh;
    }

    .border-2 {
        border: 1px solid #dee2e6 !important;
    }
    .permohonan {
        font-size: 15px;
    }
}


</style>

@section('content')
@include('pengajuan.lembur.tab_pengajuan_lembur', ["active"=>1])
@if( (new \App\Http\Traits\AuthFunction)->checkAkses('lembur/create') )
    <div class="collapse mb-2" id="bagan-form-tambah-collapse">
        <div class="card card-body" style='background:#f2f2f2'>
            <?php 
                
                $bagan_form=\App::call($router_name->base_controller.'@actionCreate');
            ?>
            @if(!empty($bagan_form))
                {!! $bagan_form !!}
            @endif
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <a class="btn btn-info collapse-cus" style='color:#fff' data-bs-toggle="collapse" href="#bagan-form-tambah-collapse"
            role="button" aria-expanded="false" aria-controls="bagan-form-tambah-collapse">
            <span id='collapse-open'><i class="fa-solid fa-angles-down"></i> Buka Form Input</span>
            <span id='collapse-closed'><i class="fa-solid fa-angles-up"></i> Tutup Form Input</span>
        </a>
    </div>
@endif

@include('pengajuan.lembur.columns')
@endsection


