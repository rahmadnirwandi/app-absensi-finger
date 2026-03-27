@extends('layouts.master')

@section('title-header', $title)

@section('breadcrumbs')
@include('layouts.breadcrumbs')
@endsection

<?php 
    $router_name=(new \App\Http\Traits\GlobalFunction)->getRouterIndex();
?>

@section('content')

@include('absensi-karyawan.tab_absensi', ["active"=>3])

@include('report-absensi-karyawan.tab_laporan_absensi', ["active"=>4])

@include($router_name->path_base.'.columns')

@endsection
