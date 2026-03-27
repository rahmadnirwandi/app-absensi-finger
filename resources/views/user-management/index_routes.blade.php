@extends('layouts.master')

@section('title-header', 'Routes Applikasi  ')

<?php
?>

@push('link')
<script type="text/javascript" src="{{ asset('libs\jquery\latest\jquery.min.js' )}}"></script>
<script type="text/javascript" src="{{ asset('libs\jquery\latest\moment.min.js' )}}"></script>
<script type="text/javascript" src="{{ asset('libs\daterangepicker\3.1.0\js\daterangepicker.min.js' )}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('libs\daterangepicker\3.1.0\css\daterangepicker.css' )}}" />
@endpush

@push('custom-style')
<style>
    
</style>
@endpush

@section('content')
<div>
    <form action="{{ url('/routers-generate') }}" method="GET">
        <button type="submit" id="submitPenyerahanResep" class="btn btn-primary">Generate Router</button>
    </form> 
    
    <div style="overflow-x: auto; max-width: auto;">
        <table class="table border table-responsive-tablet" id="tableRawatJalan">
            <thead>
                <tr>
                    <th span="2" class="py-4">Pasien</th>
                    <th span="2"  class="py-4">Poli</th>
                    <th span="2"  class="py-4">Dokter</th>
                    <th span="2"  class="py-4">No Resep</th>
                    <th span="2"  class="py-4">Tanggal Verifikasi</th>
                    <th class="py-4">Loket</th>
                    <th class="py-4">Panggil</th>
                    <th class="py-4  pe-4">Penyerahan</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <div class="d-flex flex-row align-items-center"></div>
        </div>
        <div class="col-12 col-md-6 d-flex justify-content-end">
            
        </div>
    </div>
</div>
@endsection

@push('custom-script')
    <script src="{{ asset('js/globalScript.js') }}"></script>
@endpush