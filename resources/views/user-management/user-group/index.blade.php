@extends('layouts.master')

@section('title-header', $title)

@section('breadcrumbs')
    @include('layouts.breadcrumbs')
@endsection

@push('custom-style')
<style>
    .table-number {
        counter-reset: numbering;
    }

    .table-number tbody td:first-child:before {
        counter-increment: numbering;
        content: counter(numbering) ".";
    }

</style>
@endpush

@section('content')
<div>
    <div class="row d-flex justify-content-between">
        <div class='bagan-data-table'>
            <form action="" method="GET">
                <div class="row justify-content-start align-items-end mb-3">
                    <div class="col-lg-4 col-md-10">
                        <label for="filter_search_text" class="form-label">Pencarian Dengan Keyword</label>
                        <input type="text" class="form-control search-data-table" name='form_filter_text' value="{{ Request::get('form_filter_text') }}" id='filter_search_text' placeholder="Masukkan Kata">
                    </div>
                </div>
            </form>

            <div class="d-flex align-items-end justify-content-end">
                <a href="{{ url('user-group-app/form') }}" class='btn btn-primary modal-remote ' data-modal-width='50%' data-modal-title='Tambah Group'>
                    Tambah
                </a>
            </div>
            @include('user-management.user-group.columns_index')
        </div>
    </div>
</div>
@endsection

@push('link-end-1')
    <link rel="stylesheet" type="text/css" href="{{ asset('libs\datatables\1.10.11\css\jquery.dataTables.min.css' )}}">
@endpush

@push('script-end-1')
    <script type="text/javascript" src="{{ asset('libs\datatables\1.10.11\js\jquery.dataTables.min.js' )}}"></script>    
@endpush