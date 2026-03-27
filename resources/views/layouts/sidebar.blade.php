<?php
    $get_user=(new \App\Http\Traits\AuthFunction)->getUser();
    $menu_permission=[ 
     
        [
            'title'=>'Presensi',
            'key'=>'presensi',
            'url'=>url('/')."/presensi",
            'icon'=>"<i class='fa-solid fa-chart-pie'></i>",
        ],
        [
            'title'=>'Pengajuan',
            'key'=>'sub-menu',
            'url'=>url('/')."/sub-menu?type=7",
            'icon'=>"<i class='fa-solid fa-file-circle-check'></i>",
            'key_active'=>\App::call('App\Http\Controllers\SubMenuController@listAkses',['type' => 7])->list_menu
        ],
        [
            'title'=>'Kalender Kerja',
            'key'=>'kalender-kerja-karyawan',
            'url'=>url('/')."/kalender-kerja-karyawan",
            'icon'  => "<i class='fa-solid fa-calendar-days'></i>",
        ],
        [
            'title'=>'Persetujuan',
            'key'=>'sub-menu',
            'url'=>url('/')."/sub-menu?type=8",
            'icon'=>"<i class='fa-solid fa-file-circle-check'></i>",
            'key_active'=>\App::call('App\Http\Controllers\SubMenuController@listAkses',['type' => 8])->list_menu
        ],
        [
            'title'=>'Data Karyawan',
            'key'=>'sub-menu',
            'url'=>url('/')."/sub-menu?type=4",
            'icon'=>"<i class='fa-solid fa-user-gear'></i>",
            'key_active'=>\App::call('App\Http\Controllers\SubMenuController@listAkses',['type' => 4])->list_menu
        ],
        [
            'title'=>'Mesin Absensi',
            'key'=>'sub-menu',
            'url'=>url('/')."/sub-menu?type=5",
            'icon'=>"<i class='fa-solid fa-user-gear'></i>",
            'key_active'=>\App::call('App\Http\Controllers\SubMenuController@listAkses',['type' => 5])->list_menu
        ],
        [
            'title'=>'Manajemen Absensi',
            'key'=>'sub-menu',
            'url'=>url('/')."/sub-menu?type=6",
            'icon'=>"<i class='fa-solid fa-clock'></i>",
            'key_active'=>\App::call('App\Http\Controllers\SubMenuController@listAkses',['type' => 6])->list_menu
        ],
//        [
//            'title'=>'Absensi',
//            'key'=>'absensi-per-karyawan',
//            'url'=>url('/')."/absensi-per-karyawan",
//            'icon'=>"<i class='fa-solid fa-clock'></i>",
//        ],
        [
            'title'=>'Manajemen User',
            'key'=>'sub-menu',
            'url'=>url('/')."/sub-menu?type=2",
            'icon'=>"<i class='fa-solid fa-user-gear'></i>",
            'key_active'=>\App::call('App\Http\Controllers\SubMenuController@listAkses',['type' => 2])->list_menu
        ],
        [
            'title'=>'Setting Aplikasi',
            'key'=>'sub-menu',
            'url'=>url('/')."/sub-menu?type=3",
            'icon'=>" <i class='fa-solid fa-gear'></i>",
            'key_active'=>\App::call('App\Http\Controllers\SubMenuController@listAkses',['type' => 3])->list_menu
        ],
    ];

    $menu=(new \App\Http\Traits\AuthFunction)->checkMenuAkses($menu_permission);
?>
<style>
    li.nav-item-custom{
        margin-bottom:20px;
    }
    .nav-item .list-menu-custom{
        padding:20px 20px 20px 20px !important;
    }
</style>


<div class="sidebar bg-blue-dark1">
    <div class="
    d-flex
    justify-content-center
    align-items-center
    my-3 mb-md-0
    text-white text-decoration-none
    " data-bs-scroll="true" data-bs-backdrop="false">
        @php
            $sidebar_header_1='Permata Hati';
            $sidebar_header_2='Absensi';
            $sidebar_header_3='PA';
        @endphp
        <span id="title" class="fs-4 fw-semibold text-center md-show">{{ $sidebar_header_1  }} <br> {{ $sidebar_header_2 }}</span>
        <span id="title" class="fs-4 mt-3 fw-semibold text-center sm-show">{{ $sidebar_header_3 }}</span>
    </div>
    <hr class="mx-4 line" />
    <div style="height: 80vh;" classd="d-flex justify-content-center">
        <ul class="nav flex-column mt-5 mb-auto">
                 @if( (new \App\Http\Traits\AuthFunction)->checkAkses('/dashboard') )
                <li class="nav-item nav-item-custom">
                    <a href="{{ url('/') }}" onclick="removeLocalStrg()" class="nav-link menu {{ Request::is('/') ? 'active' : '' }} text-white p-4 list-menu-custom fw-semibold" aria-current="page">
                        <i class="fa-solid fa-house"></i>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                @endif
            <?php foreach($menu as $value){  $value=(object)$value; ?>
                <?php
                    $rw='';
                    if(!Request::is($value->key)){
                        if(!empty($_GET['fr'])){
                            $hasil=check_fr($_GET['fr']);
                            if(!empty($hasil)){
                                $rw=$hasil;
                            }
                        }
                    }else{
                        $rw=$value->key;
                    }

                    $is_image='';
                    try{
                        $is_image=getimagesize($value->icon);
                        if(!empty($is_image['mime'])){
                            $is_image=1;
                        }
                    } catch (\Throwable $e) {
                        $is_image='';
                    }

                    $active_child=0;
                    if(!empty($value->key_active)){
                        $router_name=(new \App\Http\Traits\GlobalFunction)->getRouterIndex()->uri;
                        if(in_array($router_name,$value->key_active)){
                            $active_child=1;
                        }
                    }

                    $hidden_menu=0;
                    if(isset($value->key_active)==true){
                        if(empty($value->key_active)){
                            $hidden_menu=1;
                        }
                    }
                ?>

                @if($rw=='sub-menu')
                    <?php
                        $s_key=$value->url;
                        $s_key=parse_url( $value->url );
                        if(!empty($s_key['query'])){
                            parse_str($s_key['query'], $get_data);
                        }
                        if(!empty($get_data)){
                            $get_data=(object)$get_data;
                            $s_type=$get_data->type;
                        }

                        $sub_req=Request::all();
                        $type=!empty($sub_req['type']) ? $sub_req['type'] : '';
                    ?>
                    @if(!empty($s_type))
                        @if(empty($hidden_menu))
                            <li class="nav-item nav-item-custom">
                                <a href="{{ $value->url }}" class="nav-link menu {{ ($s_type==$type) ? 'active' : '' }} text-white p-4 list-menu-custom fw-semibold" aria-current="page">
                                    @if(!empty( $is_image ))
                                        <img src="{{ ($s_type==$type) ? $value->icon_active :  $value->icon }}" class="icon-sidebar" alt="">
                                    @else
                                        {!! $value->icon !!}
                                    @endif
                                    <span class='title'>{{ $value->title }}</span>
                                </a>
                            </li>
                        @endif
                    @endif
                @else
                    <?php
                        $active='';
                        $icon=($value->icon) ? $value->icon : '';
                        if($rw==$value->key or $active_child){
                            $active='active';
                            $icon=!empty($value->icon_active) ? $value->icon_active :  '' ;
                            if(empty($icon)){
                                $icon=!empty($value->icon) ? $value->icon : '';
                            }
                        }
                    ?>
                    @if(empty($hidden_menu))
                        <li class="nav-item nav-item-custom">
                            <a href="{{ $value->url }}" class="nav-link menu {{ $active }} text-white p-4 list-menu-custom fw-semibold" aria-current="page">
                                    @if(!empty( $is_image ))
                                        <img src="{{ $icon }}" class="icon-sidebar" alt="">
                                    @else
                                        {!! $icon !!}
                                    @endif
                                <span class='title'>{{ $value->title }}</span>
                            </a>
                        </li>
                    @endif
                @endif

            <?php } ?>
            
            <li class="nav-item nav-item-custom">
                <a href="{{ url('/') }}/logout" onclick="removeLocalStrg()" class="nav-link menu text-white p-4 list-menu-custom fw-semibold">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span class="title" id="menu3">Keluar</span>
                </a>
            </li>
            <li class="nav-item minimize nav-item-custom" style="display: none;">
                <a id="minimize" class="nav-link hover-pointer text-white p-4 list-menu-custom fw-semibold" onclick="minimize()">
                    <img src="{{ asset('') }}icon/sidebar/minimize.png" class="icon-sidebar" alt="">
                    <span class="title" id="menu4">Minimize</span>
                </a>
            </li>
        </ul>
    </div>
</div>
@push('link-script')
<script src="{{ asset('js/sidebars.js') }}"></script>
@endpush
@push('script-end-2')
<script src="{{ asset('js/sidebars.js') }}"></script>
@endpush
