<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubMenuController extends Controller
{
    function listAkses($type){

        $menu='';
        $list_menu='';
        if($type){
            $menu_permission=[];

            if($type==1){
                $menu_permission=[

                ];
            }

           if ($type == 2) {
                $menu_permission = [
                    [
                        'title' => 'User Group',
                        'key'   => 'user-group-app',
                        'url'   => url('/')."/user-group-app",
                        'icon'  => "<i class='fa-solid fa-users-rectangle'></i>",
                    ],
                    [
                        'title' => 'User Akses',
                        'key'   => 'user-akses',
                        'url'   => url('/')."/user-akses",
                        'icon'  => "<i class='fa-solid fa-user-lock'></i>",
                    ],
                ];
            }

            // if($type==3){
            //     $menu_permission=[
            //         [
            //             'title'=>'Setting Variabel Aplikasi',
            //             'key'=>'setting-app-variabel',
            //             'url'=>url('/')."/setting-app-variabel",
            //         ],
            //     ];
            // }

            if ($type == 4) {
                $menu_permission = [
                    [
                        'title' => 'Referensi Jabatan Karyawan',
                        'key'   => 'jabatan',
                        'url'   => url('/')."/jabatan",
                        'icon'  => "<i class='fa-solid fa-briefcase'></i>",
                    ],
                    [
                        'title' => 'Referensi Departemen/Bidang',
                        'key'   => 'departemen',
                        'url'   => url('/')."/departemen",
                        'icon'  => "<i class='fa-solid fa-sitemap'></i>",
                    ],
                    [
                        'title' => 'Referensi Ruangan',
                        'key'   => 'data-ruangan',
                        'url'   => url('/')."/data-ruangan",
                        'icon'  => "<i class='fa-solid fa-building'></i>",
                    ],
                    [
                        'title' => 'Referensi Status Karyawan',
                        'key'   => 'status-karyawan',
                        'url'   => url('/')."/status-karyawan",
                        'icon'  => "<i class='fa-solid fa-user-check'></i>",
                    ],
                    [
                        'title' => 'Data Karyawan',
                        'key'   => 'data-karyawan',
                        'url'   => url('/')."/data-karyawan",
                        'icon'  => "<i class='fa-solid fa-users'></i>",
                    ],
                    [
                        'title' => 'Data Jadwal Karyawan',
                        'key'   => 'data-jadwal-karyawan',
                        'url'   => url('/')."/data-jadwal-karyawan",
                        'icon'  => "<i class='fa-solid fa-calendar-days'></i>",
                    ],
                    [
                        'title' => 'List Kabid',
                        'key'   => 'list-kabid',
                        'url'   => url('/')."/list-kabid",
                        'icon'  => "<i class='fa-solid fa-user-tie'></i>",
                    ],
                    [
                        'title' => 'List Kepala Ruangan',
                        'key'   => 'list-kepala-ruangan',
                        'url'   => url('/')."/list-kepala-ruangan",
                        'icon'  => "<i class='fa-solid fa-user-shield'></i>",
                    ],
                    [
                        'title' => 'Approval Setting Level 1',
                        'key'   => 'approval-setting-level1',
                        'url'   => url('/')."/approval-setting-level1",
                        'icon' => "<i class='fa-solid fa-list-check'></i>",
                    ],
                    [
                        'title' => 'Approval Setting Level 2',
                        'key'   => 'approval-setting-level2',
                        'url'   => url('/')."/approval-setting-level2",
                        'icon' => "<i class='fa-solid fa-list-check'></i>",
                    ],
                ];
            }


            if ($type == 5) {
                $menu_permission = [
                    [
                        'title' => 'Data Mesin Absensi',
                        'key'   => 'data-mesin-absensi',
                        'url'   => url('/')."/data-mesin-absensi",
                        'icon'  => "<i class='fa-solid fa-server'></i>",
                    ],
                    [
                        'title' => 'Data User Mesin',
                        'key'   => 'data-user-mesin',
                        'url'   => url('/')."/data-user-mesin",
                        'icon'  => "<i class='fa-solid fa-users-gear'></i>",
                    ],
                ];
            }


            if ($type == 6) {
                $menu_permission = [
                    [
                        'title' => 'Data Jadwal Kerja',
                        'key'   => 'jenis-jadwal-absensi',
                        'url'   => url('/')."/jenis-jadwal-absensi",
                        'icon'  => "<i class='fa-solid fa-calendar-week'></i>",
                    ],
                    [
                        'title' => 'Pengaturan Jadwal Presensi',
                        'key'   => 'jadwal-absensi',
                        'url'   => url('/')."/jadwal-absensi",
                        'icon'  => "<i class='fa-solid fa-sliders'></i>",
                    ],
                    [
                        'title' => 'Hari Libur Umum',
                        'key'   => 'hari-libur-umum',
                        'url'   => url('/')."/hari-libur-umum",
                        'icon'  => "<i class='fa-solid fa-calendar-xmark'></i>",
                    ],
//                    [
//                        'title' => 'Cuti Karyawan',
//                        'key'   => 'cuti-karyawan',
//                        'url'   => url('/')."/cuti-karyawan",
//                        'icon'  => "<i class='fa-solid fa-calendar-check'></i>",
//                    ],
                    [
                        'title' => 'Perjalanan Dinas Karyawan',
                        'key'   => 'perjalanan-dinas',
                        'url'   => url('/')."/perjalanan-dinas",
                        'icon'  => "<i class='fa-solid fa-briefcase'></i>",
                    ],
                    [
                        'title' => 'Absensi',
                        'key'   => 'absensi-karyawan',
                        'url'   => url('/')."/absensi-karyawan",
                        'icon'  => "<i class='fa-solid fa-fingerprint'></i>",
                    ],
                    [
                        'title' => 'Jenis Cuti',
                        'key'   => 'jenis-cuti',
                        'url'   => url('/')."/jenis-cuti",
                        'icon'  => "<i class='fa-solid fa-calendar-days'></i>",
                    ],
                    [
                        'title' => 'Data Cuti Karyawan',
                        'key'   => 'data-cuti-karyawan',
                        'url'   => url('/')."/data-cuti-karyawan",
                        'icon'  => "<i class='fa-solid fa-user-clock'></i>",
                    ],
                    [
                        'title' => 'Rekap Lembur',
                        'key'   => 'rekap-lembur',
                        'url'   => url('/')."/rekap-lembur",
                        'icon' => "<i class='fa-solid fa-clock'></i>",
                    ],
                    [
                        'title' => 'Pengaturan Kalander Kerja',
                        'key'   => 'kalender-kerja',
                        'url'   => url('/')."/kalender-kerja",
                        'icon'  => "<i class='fa-solid fa-calendar-days'></i>",
                    ],
                ];
            }


            if($type==7){
                $menu_permission=[
                    [
                        'title'=>'Izin',
                        'key'=>'izin',
                        'url'=> url('/')."/izin",
                        'icon' => "<i class='fa-solid fa-envelope-open-text'></i>"
                    ],
                    [
                        'title'=>'Cuti',
                        'key'=>'cuti',
                        'url'=>url('/')."/cuti",
                        'icon' => "<i class='fa-solid fa-calendar-days'></i>"
                    ],
                    [
                        'title'=>'Lembur',
                        'key'=>'lembur',
                        'url'=>url('/')."/lembur",
                        'icon'  => "<i class='fa-solid fa-clock-rotate-left'></i>",
                    ],
                ];
            }
            
            if($type==8){
                $menu_permission=[
                    [
                        'title'=>'Izin',
                        'key'=>'approver-izin',
                        'url'=> url('/')."/approver-izin",
                        'icon' => "<i class='fa-solid fa-clipboard-check'></i>"

                    ],
                    [
                        'title'=>'Cuti',
                        'key'=>'approver-cuti',
                        'url'=>url('/')."/approver-cuti",
                        'icon' => "<i class='fa-solid fa-calendar-check'></i>"

                    ],
                    [
                        'title'=>'Lembur',
                        'key'=>'approver-lembur',
                        'url'=>url('/')."/approver-lembur",
                        'icon' => "<i class='fa-solid fa-clock'></i>"

                    ],
                ];
            }

            if($menu_permission){
                $list_menu=[];
                $menu=(new \App\Http\Traits\AuthFunction)->checkMenuAkses($menu_permission);
                if($menu){
                    foreach($menu as $value){
                        $value=(object)$value;
                        $list_menu[]=$value->key;
                    }
                }
            }
        }

        return (object)[
            'menu'=>$menu,
            'list_menu'=>$list_menu
        ];
        return (object)[];
    }

    function index(Request $request){
        $type=!empty($request->get('type')) ? $request->get('type') : '';

        $get_menu=$this->listAkses($type);

        $parameter_view=[
            'type'=>$type,
            'menu'=>!empty($get_menu->menu) ? $get_menu->menu : []
        ];
        return view('layouts.sub_menu',$parameter_view);
    }
}
