@extends('layouts.master')

@section('title-header', 'User Group Aplikasi')

@section('breadcrumbs')
    @include('layouts.breadcrumbs')
@endsection

<?php 
    $ignore_type=$lib_routes_list_system->getIgnoreType();
    $router_name=(new \App\Http\Traits\GlobalFunction)->getRouterIndex();
?>


@push('custom-style')
<style>
    #table_user_group>tbody>tr>td:first-child {
        width: 20%;
        font-weight: bold;
        border-right: 1px solid #ddd;
        text-align: right;
    }

    .type_routes {
        list-style: none;
        margin: 0;
        padding: 0;
    }

</style>
@endpush


@section('content')

    <table id="table_user_group" class="table table-striped mb-5">
        <tbody>
            <tr>
                <td>Nama</td>
                <td>{{$user_group->name}}</td>
            </tr>
            <tr>
                <td>Alias</td>
                <td>{{$user_group->alias}}</td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>
                    {{$user_group->keterangan}}
                </td>
            </tr>
            <tr>
                <td>Dibuat Pada</td>
                <td>{{$user_group->created}}</td>
            </tr>
            <tr>
                <td colspan='2'>
                    <?php 
                        $params_request['rr']=1;
                        $url_refresh_router=(new \App\Http\Traits\GlobalFunction)->set_paramter_url($router_name->uri,$params_request);
                    ?>
                    <a href="{{ $url_refresh_router }}" class="btn btn-success">Refresh Link</a>
                </td>
            </tr>
        </tbody>
    </table>

    <form method="post" action="{{url($router_name->uri.'/update')}}" class="m-2 p-2 border rounded-3">
        <input type='hidden' name='key_group_alias' value='{{ $user_group->id }}'/>
        @csrf
        
        <table id="table_user_group" class="table table-striped mb-5">
            <tbody>
                <tr>
                    <td>Akses Router</td>
                    <td>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="">Nama</th>
                                    <th class="w-25">Hak Akses</th>
                                </tr>
                                <tr>
                                    <th class="w-25"></th>
                                    <th class="d-flex align-items-center justify-content-between">
                                        <div class="form-check ">
                                            <input class="form-check-input  mt-0 me-2" name="checkAll" type="checkbox" value=""
                                            id="checkedAll">
                                            <label class="form-check-label align-middle" for="check_all">
                                                Semua
                                            </label>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($routes_list as $value)
                                <?php 
                                        $get_title=explode('@',$value->title);
                                        $area_akses=!empty($get_title[0]) ? $get_title[0] : '';
                                        $title=!empty($get_title[1]) ? $get_title[1] : '';
                                        
                                        $title=empty($title) ? $area_akses : $title;
                                        $area_akses=empty($get_title[1]) ? '' : $area_akses;
                                    ?>
                                    <tr>
                                        <td class="w-25">{{ ucfirst($title) }}</td>
                                        <td class="">
                                            <?php 
                                                    $types = explode(",", $value->types);
                                                    $type_ids = explode(",", $value->type_ids);
                                                    $urls = explode(",", $value->urls);
                                                ?>
                                            @if(!empty($types))
                                            <ul class='type_routes'>
                                                @foreach($types as $key => $type)
                                                <?php
                                                    $check=$lib_routes_list_system->getIgnoreType($type);
                                                ?>
                                                @if(!empty($check))
                                                <li>
                                                    <div class="form-check form-check-inline ">
                                                        <input name="routes_{{$type_ids[$key]}}" value="{{$urls[$key]}}"
                                                            class="checkSingle form-check-input mt-0 me-2" type="checkbox"
                                                            value="" id="routes_{{$type_ids[$key]}}"
                                                            @if(in_array($type_ids[$key], $checked_permissions)) checked @endif>
                                                        <label class="form-check-label align-middle"
                                                            for="routes_{{$type_ids[$key]}}">{{ ucfirst($type) }}</label>
                                                    </div>
                                                </li>
                                                @endif
                                                @endforeach
                                            </ul>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-end align-items-center">
            <div id="alert_no_changes" class="alert alert-warning my-0 me-5 d-none" role="alert">
                Tidak Ada Data yang Diperbaharui
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endsection

@push('custom-script')
    <script src="{{ asset('js/user_group/permission_group.js') }}"></script>
@endpush
