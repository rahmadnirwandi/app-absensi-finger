@extends('layouts.master')

@section('title-header', $title)

@section('breadcrumbs')
@include('layouts.breadcrumbs')
@endsection

<style>
.collapsed .collapse-open,
.collapsed .collapse-open-semua {
    display: inline !important;
}
.collapsed .collapse-closed,
.collapsed .collapse-closed-semua {
    display: none !important;
}

[aria-expanded="true"] .collapse-open,
[aria-expanded="true"] .collapse-open-semua {
    display: none !important;
}
[aria-expanded="true"] .collapse-closed,
[aria-expanded="true"] .collapse-closed-semua {
    display: inline !important;
}
@media (max-width: 576px) {
    .d-flex.justify-content-end.gap-2 {
        flex-direction: column;
        align-items: stretch;
    }

    .d-flex.justify-content-end.gap-2 a {
        width: 100%;
        text-align: center;
    }
}

</style>

<?php
    $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
    $canCreate = (new \App\Http\Traits\AuthFunction)->checkAkses($router_name->uri.'/create');
    $canCreateAll = (new \App\Http\Traits\AuthFunction)->checkAkses($router_name->uri.'/create-all');
?>

@section('content')

    @if($canCreate)
        <div class="collapse mb-2" id="bagan-form-tambah-collapse">
            <div class="card card-body" style="background:#f2f2f2">
                @php
                    $bagan_form = \App::call($router_name->base_controller.'@actionCreate');
                @endphp
                @if(!empty($bagan_form))
                    {!! $bagan_form !!}
                @endif
            </div>
        </div>
    @endif

    @if($canCreateAll)
        <div class="collapse mb-2" id="bagan-form-tambah-semua-collapse">
            <div class="card card-body" style="background:#f2f2f2">
                @php
                    $bagan_form_semua = \App::call($router_name->base_controller.'@actionCreateAll');
                @endphp
                @if(!empty($bagan_form_semua))
                    {!! $bagan_form_semua !!}
                @endif
            </div>
        </div>
    @endif

    @if($canCreate || $canCreateAll)
        <div class="d-flex justify-content-end gap-2">
            @if($canCreate)
                <a class="btn btn-info collapsed" style="color:#fff"
                data-bs-toggle="collapse"
                href="#bagan-form-tambah-collapse"
                role="button"
                aria-expanded="false"
                aria-controls="bagan-form-tambah-collapse">
                    <span class="collapse-open">
                        <i class="fa-solid fa-angles-down"></i> Buka Form Tambah Cuti
                    </span>
                    <span class="collapse-closed" style="display:none">
                        <i class="fa-solid fa-angles-up"></i> Tutup Form Tambah Cuti
                    </span>
                </a>
            @endif

            @if($canCreateAll)
                <a class="btn btn-success collapsed" style="color:#fff"
                data-bs-toggle="collapse"
                href="#bagan-form-tambah-semua-collapse"
                role="button"
                aria-expanded="false"
                aria-controls="bagan-form-tambah-semua-collapse">
                    <span class="collapse-open-semua">
                        <i class="fa-solid fa-angles-down"></i> Buka Form Cuti Semua Karyawan
                    </span>
                    <span class="collapse-closed-semua" style="display:none">
                        <i class="fa-solid fa-angles-up"></i> Tutup Form Cuti Semua Karyawan
                    </span>
                </a>
            @endif
        </div>
    @endif

    @include($router_name->path_base.'.columns')

@endsection
