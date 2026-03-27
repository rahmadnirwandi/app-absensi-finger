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
@include('persetujuan.cuti.tab_persetujuan_cuti', ["active"=>1])

@include('persetujuan.cuti.columns')
@endsection


