@extends('layouts.master')

@section('title-header', $title)

@section('breadcrumbs')
@include('layouts.breadcrumbs')
@endsection

<?php 
    $router_name=(new \App\Http\Traits\GlobalFunction)->getRouterIndex();
?>

@section('content')

@if (!empty($url_back_index))
    <div class="text-warning">
        <a href="{{ url($url_back_index) }}" class="hover-pointer btn-back">
            <span class="hover-pointer btn-back text-warning">
                <img src="{{ asset('') }}icon/backwards.png" alt="">
                <span class="mx-2">Kembali ke Halaman Sebelumnya</span>
            </span>
        </a>    
    </div>
@endif

<hr>
<div>
    <form action="" method="GET">
        <input type="hidden" name='data_sent' value='{{ !empty($data_sent) ? $data_sent : '' }}'>
        <input type="hidden" name='params' value='{{ !empty($params_json) ? $params_json : '' }}'>

        <div class="row d-flex justify-content-between">
            <div class="col-lg-6">
                <div style="overflow-x: auto; max-width: auto;">
                    <table class="table border table-responsive-tablet">
                        <tbody>
                            <tr>
                                <td style='width: 20%; vertical-align: middle;'>Nama</td>
                                <td style='width: 1%; vertical-align: middle;'>:</td>
                                <td style='width: 69%; vertical-align: middle;'>{{ !empty($data_karyawan->nm_karyawan) ? $data_karyawan->nm_karyawan : '' }}</td>
                            </tr>

                            <tr>
                                <td style='width: 20%; vertical-align: middle;'>NIP</td>
                                <td style='width: 1%; vertical-align: middle;'>:</td>
                                <td style='width: 69%; vertical-align: middle;'>{{ !empty($data_karyawan->nip) ? $data_karyawan->nip : '' }}</td>
                            </tr>
                            
                            <tr>
                                <td style='width: 20%; vertical-align: middle;'>Jabatan</td>
                                <td style='width: 1%; vertical-align: middle;'>:</td>
                                <td style='width: 69%; vertical-align: middle;'>{{ !empty($data_karyawan->nm_jabatan) ? $data_karyawan->nm_jabatan : '' }}</td>
                            </tr>

                            <tr>
                                <td style='width: 20%; vertical-align: middle;'>Bidang/Departemen</td>
                                <td style='width: 1%; vertical-align: middle;'>:</td>
                                <td style='width: 69%; vertical-align: middle;'>{{ !empty($data_karyawan->nm_departemen) ? $data_karyawan->nm_departemen : '' }}</td>
                            </tr>

                            <tr>
                                <td style='width: 20%; vertical-align: middle;'>Ruangan</td>
                                <td style='width: 1%; vertical-align: middle;'>:</td>
                                <td style='width: 69%; vertical-align: middle;'>{{ !empty($data_karyawan->nm_ruangan) ? $data_karyawan->nm_ruangan : '' }}</td>
                            </tr>

                            
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-6">
                <div style="overflow-x: auto; max-width: auto;">
                    <table class="table border table-responsive-tablet">
                        <tbody>
                            <tr>
                                <td style='width: 20%; vertical-align: middle;'>Model Jadwal Shift</td>
                                <td style='width: 1%; vertical-align: middle;'>:</td>
                                <td style='width: 69%; vertical-align: middle;'>{{ !empty($model_shift->nm_shift) ? $model_shift->nm_shift : '' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

@include($router_name->path_base.'.columns')

@endsection