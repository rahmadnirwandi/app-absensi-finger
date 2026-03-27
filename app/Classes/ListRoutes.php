<?php

namespace App\Classes;

class ListRoutes
{
    function getDataAuth($index = null)
    {
        $data = [
            [
                'title' => 'Sub Menu',
                'item' => [
                    [
                        'type' => ' index ',
                        'method' => 'get',
                        'url' => '/sub-menu',
                        'controller' => 'SubMenuController@index',
                        'name' => 'sub_menu',
                        'middleware' => '',
                    ],
                    [
                        'type' => ' index ',
                        'method' => 'get',
                        'url' => '/sub-menu/list_akses',
                        'controller' => 'SubMenuController@listAkses',
                        'name' => '',
                        'middleware' => '',
                    ]
                ]
            ],
            [
                'title' => 'Halaman Awal',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/',
                        'controller' => 'DashboardController@index',
                        'name' => 'dashboard_utama',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/dashboard',
                        'controller' => 'DashboardController@index',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'logout',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/logout',
                        'controller' => 'AuthController@logout',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'ajax',
                'item' => [
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/ajax',
                        'controller' => 'AjaxController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Data Karyawan | Referensi Jabatan Karyawan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/jabatan',
                        'controller' => 'JabatanController@actionIndex',
                        'name' => 'jabatan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/jabatan/create',
                        'controller' => 'JabatanController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/jabatan/update',
                        'controller' => 'JabatanController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/jabatan/delete',
                        'controller' => 'JabatanController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Data Karyawan | Referensi Departemen/Bidang',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/departemen',
                        'controller' => 'DepartemenController@actionIndex',
                        'name' => 'departemen',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/departemen/create',
                        'controller' => 'DepartemenController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/departemen/update',
                        'controller' => 'DepartemenController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/departemen/delete',
                        'controller' => 'DepartemenController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Data Karyawan | Data Ruangan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/data-ruangan',
                        'controller' => 'DataRuanganController@actionIndex',
                        'name' => 'data_ruangan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/data-ruangan/create',
                        'controller' => 'DataRuanganController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/data-ruangan/update',
                        'controller' => 'DataRuanganController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/data-ruangan/delete',
                        'controller' => 'DataRuanganController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],

            [
                'title' => 'Data Karyawan | Referensi Status Karyawan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/status-karyawan',
                        'controller' => 'StatusKaryawanController@actionIndex',
                        'name' => 'status_karyawan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/status-karyawan/create',
                        'controller' => 'StatusKaryawanController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/status-karyawan/update',
                        'controller' => 'StatusKaryawanController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/status-karyawan/delete',
                        'controller' => 'StatusKaryawanController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],

            [
                'title' => 'Data Karyawan | Data karyawan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/data-karyawan',
                        'controller' => 'DataKaryawanController@actionIndex',
                        'name' => 'data_karyawan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/data-karyawan/create',
                        'controller' => 'DataKaryawanController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/data-karyawan/update',
                        'controller' => 'DataKaryawanController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/data-karyawan/delete',
                        'controller' => 'DataKaryawanController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],

            [
                'title' => 'Data Karyawan | Data Jadwal karyawan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => ['get', 'post'],
                        'url' => '/data-jadwal-karyawan',
                        'controller' => 'DataJadwalKaryawanController@actionIndex',
                        'name' => 'data_jadwal_karyawan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'atur waktu',
                        'method' => ['get', 'post'],
                        'url' => '/data-jadwal-karyawan-shift',
                        'controller' => 'DataJadwalKaryawanShiftController@actionIndex',
                        'name' => 'data_jadwal_karyawan_shift',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => ['get', 'post'],
                        'url' => '/data-jadwal-karyawan/ajax',
                        'controller' => 'DataJadwalKaryawanController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],

            [
                'title' => 'Mesin Absensi | Data Mesin',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/data-mesin-absensi',
                        'controller' => 'DataMesinAbsensiController@actionIndex',
                        'name' => 'data_mesin_absensi',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/data-mesin-absensi/create',
                        'controller' => 'DataMesinAbsensiController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/data-mesin-absensi/update',
                        'controller' => 'DataMesinAbsensiController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/data-mesin-absensi/delete',
                        'controller' => 'DataMesinAbsensiController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Mesin Absensi | Sinkronisasi Mesin & Database',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => ['get', 'post'],
                        'url' => '/data-user-mesin-sinkronisasi',
                        'controller' => 'DataUserMesinSinkronisasiController@actionIndex',
                        'name' => 'data_user_mesin_sinkronisasi',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Sinkronisasi',
                        'method' => 'post',
                        'url' => '/data-user-mesin-sinkronisasi/sinkron',
                        'controller' => 'DataUserMesinSinkronisasiController@actionSinkron',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Mesin Absensi | Data User Pada Database',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/data-user-mesin',
                        'controller' => 'DataUserMesinController@actionIndex',
                        'name' => 'data_user_mesin',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/data-user-mesin/update',
                        'controller' => 'DataUserMesinController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/data-user-mesin/delete',
                        'controller' => 'DataUserMesinController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Mesin Absensi | Copy Data User Mesin',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => ['get', 'post'],
                        'url' => '/data-user-mesin-copy',
                        'controller' => 'DataUserMesinCopyController@actionIndex',
                        'name' => 'data_user_mesin_copy',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'proses',
                        'method' => ['get', 'post'],
                        'url' => '/data-user-mesin-copy/update',
                        'controller' => 'DataUserMesinController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/data-user-mesin-copy/ajax',
                        'controller' => 'DataUserMesinCopyController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Manajemen Absensi | Data Jadwal Kerja',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/jenis-jadwal-absensi',
                        'controller' => 'JenisJadwalAbsensiController@actionIndex',
                        'name' => 'jenis_jadwal_absensi',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/jenis-jadwal-absensi/create',
                        'controller' => 'JenisJadwalAbsensiController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/jenis-jadwal-absensi/update',
                        'controller' => 'JenisJadwalAbsensiController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/jenis-jadwal-absensi/delete',
                        'controller' => 'JenisJadwalAbsensiController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Manajemen Absensi | Pengaturan Jadwal Presensi',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/jadwal-absensi',
                        'controller' => 'JadwalAbsensiController@actionIndex',
                        'name' => 'jadwal_absensi',
                        'middleware' => '',
                    ],
                    // [
                    // 	'type' => 'create',
                    // 	'method' => ['get', 'post'],
                    // 	'url' => '/jadwal-absensi/create',
                    // 	'controller' => 'JadwalAbsensiController@actionCreate',
                    // 	'name' => '',
                    // 	'middleware' => '',
                    // ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/jadwal-absensi/update',
                        'controller' => 'JadwalAbsensiController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    // [
                    // 	'type' => 'delete',
                    // 	'method' => 'delete',
                    // 	'url' => '/jadwal-absensi/delete',
                    // 	'controller' => 'JadwalAbsensiController@actionDelete',
                    // 	'name' => '',
                    // 	'middleware' => '',
                    // ],
                ]
            ],
            [
                'title' => 'Manajemen Absensi | Templat Jadwal Shift',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/template-jadwal-shift',
                        'controller' => 'TemplateJadwalShiftController@actionIndex',
                        'name' => 'template_jadwal_shift',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/template-jadwal-shift/create',
                        'controller' => 'TemplateJadwalShiftController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/template-jadwal-shift/update',
                        'controller' => 'TemplateJadwalShiftController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/template-jadwal-shift/delete',
                        'controller' => 'TemplateJadwalShiftController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],

            [
                'title' => 'Manajemen Absensi | Templat Jadwal Shift Atur waktu',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/template-jadwal-shift-waktu',
                        'controller' => 'TemplateJadwalShiftWaktuController@actionIndex',
                        'name' => 'template_jadwal_shift_waktu',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/template-jadwal-shift-waktu/update',
                        'controller' => 'TemplateJadwalShiftWaktuController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Absensi | Hari Libur Umum',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/hari-libur-umum',
                        'controller' => 'HariLiburUmumController@actionIndex',
                        'name' => 'hari_libur_umum',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/hari-libur-umum/create',
                        'controller' => 'HariLiburUmumController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/hari-libur-umum/update',
                        'controller' => 'HariLiburUmumController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/hari-libur-umum/delete',
                        'controller' => 'HariLiburUmumController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
//            [
//                'title' => 'Absensi | Cuti Karyawan',
//                'item' => [
//                    [
//                        'type' => 'index',
//                        'method' => 'get',
//                        'url' => '/cuti-karyawan',
//                        'controller' => 'CutiKaryawanController@actionIndex',
//                        'name' => 'cuti_karyawan',
//                        'middleware' => '',
//                    ],
//                    [
//                        'type' => 'create',
//                        'method' => ['get', 'post'],
//                        'url' => '/cuti-karyawan/create',
//                        'controller' => 'CutiKaryawanController@actionCreate',
//                        'name' => '',
//                        'middleware' => '',
//                    ],
//                    [
//                        'type' => 'update',
//                        'method' => ['get', 'post'],
//                        'url' => '/cuti-karyawan/update',
//                        'controller' => 'CutiKaryawanController@actionUpdate',
//                        'name' => '',
//                        'middleware' => '',
//                    ],
//                    [
//                        'type' => 'delete',
//                        'method' => 'delete',
//                        'url' => '/cuti-karyawan/delete',
//                        'controller' => 'CutiKaryawanController@actionDelete',
//                        'name' => '',
//                        'middleware' => '',
//                    ],
//                ]
//            ],
            [
                'title' => 'Absensi | Perjalanan Dinas Karyawan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/perjalanan-dinas',
                        'controller' => 'PerjalananDinasController@actionIndex',
                        'name' => 'perjalanan_dinas',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/perjalanan-dinas/create',
                        'controller' => 'PerjalananDinasController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/perjalanan-dinas/update',
                        'controller' => 'PerjalananDinasController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/perjalanan-dinas/delete',
                        'controller' => 'PerjalananDinasController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Data Karyawan | List Kabid',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/list-kabid',
                        'controller' => 'ListKabidController@actionIndex',
                        'name' => 'list_kabid',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/list-kabid/create',
                        'controller' => 'ListKabidController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/list-kabid/update',
                        'controller' => 'ListKabidController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/list-kabid/delete',
                        'controller' => 'ListKabidController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/list-kabid/ajax',
                        'controller' => 'ListKabidController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Data Karyawan | List Kepala Ruangan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/list-kepala-ruangan',
                        'controller' => 'ListKepalaRuanganController@actionIndex',
                        'name' => 'list_kepala_ruangan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Setting Karu',
                        'method' => ['get', 'post', 'delete'],
                        'url' => '/list-kepala-ruangan/setting',
                        'controller' => 'ListKepalaRuanganController@actionSetting',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/list-kepala-ruangan/ajax',
                        'controller' => 'ListKepalaRuanganController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Manajemen Absensi | Tarik Log Absensi Karyawan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/tarik-data-absensi-karyawan',
                        'controller' => 'TarikDataAbsensiKaryawanController@actionIndex',
                        'name' => 'tarik_data_absensi_karyawan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'view',
                        'method' => 'get',
                        'url' => '/tarik-data-absensi-karyawan/view',
                        'controller' => 'TarikDataAbsensiKaryawanController@actionView',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => ['get', 'post'],
                        'url' => '/tarik-data-absensi-karyawan/ajax',
                        'controller' => 'TarikDataAbsensiKaryawanController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],

            [
                'title' => 'Manajemen | Data Absensi',
                'item' => [
                    [
                        'type' => 'Absensi Rutin',
                        'method' => 'get',
                        'url' => '/absensi-karyawan',
                        'controller' => 'AbsensiKaryawanController@actionIndex',
                        'name' => 'absensi_karyawan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Absensi Shift',
                        'method' => 'get',
                        'url' => '/absensi-karyawan-shift',
                        'controller' => 'AbsensiKaryawanShiftController@actionIndex',
                        'name' => 'absensi_karyawan_shift',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => ['get', 'post'],
                        'url' => '/absensi-karyawan/ajax',
                        'controller' => 'AbsensiKaryawanController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],

            [
                'title' => 'Manajemen | Report Data Absensi',
                'item' => [
                    [
                        'type' => 'Report Absensi Histori Dari Mesin',
                        'method' => 'get',
                        'url' => '/report-log-mesin',
                        'controller' => 'ReportLogMesinController@actionIndex',
                        'name' => 'report_log_mesin',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Cetak Report Absensi Histori Dari Mesin',
                        'method' => 'get',
                        'url' => '/report-log-mesin/cetak',
                        'controller' => 'ReportLogMesinController@actionCetak',
                        'name' => '',
                        'middleware' => '',
                    ],

                    [
                        'type' => 'Report Absensi Histori Dari Karyawan',
                        'method' => 'get',
                        'url' => '/report-log-mesin-by-karyawan',
                        'controller' => 'ReportLogMesinByKaryawanController@actionIndex',
                        'name' => 'report_log_mesin_by_karyawan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Cetak Report Absensi Histori Dari Karyawan',
                        'method' => 'get',
                        'url' => '/report-log-mesin-by-karyawan/cetak',
                        'controller' => 'ReportLogMesinByKaryawanController@actionCetak',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Report Absensi Karyawan',
                        'method' => 'get',
                        'url' => '/report-absensi-karyawan',
                        'controller' => 'ReportAbsensiKaryawanController@actionIndex',
                        'name' => 'report_absensi_karyawan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Cetak Report Absensi Karyawan',
                        'method' => 'get',
                        'url' => '/report-absensi-karyawan/cetak',
                        'controller' => 'ReportAbsensiKaryawanController@actionCetak',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],

//            [
//                'title' => 'Absensi | Data Absensi Perkaryawan ',
//                'item' => [
//                    [
//                        'type' => 'index',
//                        'method' => 'get',
//                        'url' => '/absensi-per-karyawan',
//                        'controller' => 'AbsensiPerKaryawanController@actionIndex',
//                        'name' => 'absensi_per_karyawan',
//                        'middleware' => '',
//                    ],
//                    [
//                        'type' => 'view',
//                        'method' => 'get',
//                        'url' => '/absensi-per-karyawan/view',
//                        'controller' => 'AbsensiPerKaryawanController@actionView',
//                        'name' => '',
//                        'middleware' => '',
//                    ],
//                ]
//            ],

            [
                'title' => 'Manajemen User | Group User',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/user-group-app',
                        'controller' => 'UserGroupAppController@actionIndex',
                        'name' => 'user_group_app',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => 'post',
                        'url' => '/user-group-app/create',
                        'controller' => 'UserGroupAppController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => 'post',
                        'url' => '/user-group-app/update',
                        'controller' => 'UserGroupAppController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/user-group-app/delete',
                        'controller' => 'UserGroupAppController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'form',
                        'method' => 'get',
                        'url' => '/user-group-app/form',
                        'controller' => 'UserGroupAppController@form',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Manajemen User | Group User | Permission',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/permission-group-app',
                        'controller' => 'PermissionGroupAppController@actionIndex',
                        'name' => 'permission_group_app',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => 'post',
                        'url' => '/permission-group-app/update',
                        'controller' => 'PermissionGroupAppController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Manajemen User | Akses User',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/user-akses',
                        'controller' => 'UserAksesController@actionIndex',
                        'name' => 'user_akses',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/user-akses/update',
                        'controller' => 'UserAksesController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/user-akses/delete',
                        'controller' => 'UserAksesController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'form',
                        'method' => 'get',
                        'url' => '/user-akses/form',
                        'controller' => 'UserAksesController@form',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
             [
                'title' => 'Presensi',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/presensi',
                        'controller' => 'PresensiController@index',
                        'name' => 'presensi',
                        'middleware' => '',
                    ]
                ]
            ],
            // [
            //     'title'=>'Setting Variabel Aplikasi',
            //     'item'=>[
            //         [
            //             'type'=>'index',
            //             'method'=>'get',
            //             'url'=>'/setting-app-variabel',
            //             'controller'=>'SettingAppVariabelController@actionIndex',
            //             'name'=>'',
            //             'middleware'=>'',
            //         ],
            //         [
            //             'type'=>'create',
            //             'method'=>['get','post'],
            //             'url'=>'/setting-app-variabel/create',
            //             'controller'=>'SettingAppVariabelController@actionCreate',
            //             'name'=>'',
            //             'middleware'=>'',
            //         ],
            //         [
            //             'type'=>'update',
            //             'method'=>['get','post'],
            //             'url'=>'/setting-app-variabel/update',
            //             'controller'=>'SettingAppVariabelController@actionUpdate',
            //             'name'=>'',
            //             'middleware'=>'',
            //         ],
            //         [
            //             'type'=>'delete',
            //             'method'=>'delete',
            //             'url'=>'/setting-app-variabel/delete',
            //             'controller'=>'SettingAppVariabelController@actionDelete',
            //             'name'=>'',
            //             'middleware'=>'',
            //         ],
            //     ]
            // ],

            [
                'title' => 'Pengajuan | Izin',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/izin',
                        'controller' => 'IzinController@actionIndex',
                        'name' => 'izin',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => 'post',
                        'url' => '/izin/create',
                        'controller' => 'IzinController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/izin/create',
                        'controller' => 'IzinController@actionFormCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/izin/update',
                        'controller' => 'IzinController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/izin/delete',
                        'controller' => 'IzinController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/izin/ajax',
                        'controller' => 'IzinController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Disetujui',
                        'method' => 'get',
                        'url' => '/izin/izin-disetujui',
                        'controller' => 'IzinController@actionIndexApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Ditolak',
                        'method' => 'get',
                        'url' => '/izin/izin-ditolak',
                        'controller' => 'IzinController@actionIndexRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            

            [
                'title' => 'Pengajuan | Cuti',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/cuti',
                        'controller' => 'CutiController@actionIndex',
                        'name' => 'cuti',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/cuti/create',
                        'controller' => 'CutiController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/cuti/update',
                        'controller' => 'CutiController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/cuti/delete',
                        'controller' => 'CutiController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/cuti/ajax',
                        'controller' => 'CutiController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Disetujui',
                        'method' => 'get',
                        'url' => '/cuti/cuti-disetujui',
                        'controller' => 'CutiController@actionIndexApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Ditolak',
                        'method' => 'get',
                        'url' => '/cuti/cuti-ditolak',
                        'controller' => 'CutiController@actionIndexRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Manajemen Absensi | Jenis Cuti',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/jenis-cuti',
                        'controller' => 'JenisCutiController@actionIndex',
                        'name' => 'jenis-cuti',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/jenis-cuti/create',
                        'controller' => 'JenisCutiController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/jenis-cuti/update',
                        'controller' => 'JenisCutiController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/jenis-cuti/delete',
                        'controller' => 'JenisCutiController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/jenis-cuti/ajax',
                        'controller' => 'JenisCutiController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],

            [
                'title' => 'Manajemen Absensi | Data Cuti Karyawan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/data-cuti-karyawan',
                        'controller' => 'DataCutiKaryawanController@actionIndex',
                        'name' => 'data-cuti-karyawan',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/data-cuti-karyawan/create',
                        'controller' => 'DataCutiKaryawanController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create all',
                        'method' => ['get', 'post'],
                        'url' => '/data-cuti-karyawan/create-all',
                        'controller' => 'DataCutiKaryawanController@actionCreateAll',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/data-cuti-karyawan/update',
                        'controller' => 'DataCutiKaryawanController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/data-cuti-karyawan/delete',
                        'controller' => 'DataCutiKaryawanController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ]
                ]
            ],

            [
                'title' => 'Pengajuan | Lembur',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/lembur',
                        'controller' => 'LemburController@actionIndex',
                        'name' => 'lembur',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/lembur/create',
                        'controller' => 'LemburController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/lembur/update',
                        'controller' => 'LemburController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/lembur/delete',
                        'controller' => 'LemburController@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/lembur/ajax',
                        'controller' => 'LemburController@ajax',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Disetujui',
                        'method' => 'get',
                        'url' => '/lembur/lembur-disetujui',
                        'controller' => 'LemburController@actionIndexApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Ditolak',
                        'method' => 'get',
                        'url' => '/lembur/lembur-ditolak',
                        'controller' => 'LemburController@actionIndexRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],

            [
                'title' => 'Persetujuan | Izin',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/approver-izin',
                        'controller' => 'ApproverIzinController@actionIndex',
                        'name' => 'approver-izin',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/approver-izin/approved',
                        'controller' => 'ApproverIzinController@actionApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Approved',
                        'method' => 'post',
                        'url' => '/approver-izin/approved',
                        'controller' => 'ApproverIzinController@actionApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/approver-izin/rejected',
                        'controller' => 'ApproverIzinController@actionRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Rejected',
                        'method' => 'post',
                        'url' => '/approver-izin/rejected',
                        'controller' => 'ApproverIzinController@actionRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Disetujui',
                        'method' => 'get',
                        'url' => '/approver-izin/izin-disetujui',
                        'controller' => 'ApproverIzinController@actionIndexApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Ditolak',
                        'method' => 'get',
                        'url' => '/approver-izin/izin-ditolak',
                        'controller' => 'ApproverIzinController@actionIndexRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            
            [
                'title' => 'Persetujuan | Cuti',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/approver-cuti',
                        'controller' => 'ApproverCutiController@actionIndex',
                        'name' => 'approver-cuti',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/approver-cuti/approved',
                        'controller' => 'ApproverCutiController@actionApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Approved',
                        'method' => 'post',
                        'url' => '/approver-cuti/approved',
                        'controller' => 'ApproverCutiController@actionApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/approver-cuti/rejected',
                        'controller' => 'ApproverCutiController@actionRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Rejected',
                        'method' => 'post',
                        'url' => '/approver-cuti/rejected',
                        'controller' => 'ApproverCutiController@actionRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Disetujui',
                        'method' => 'get',
                        'url' => '/approver-cuti/cuti-disetujui',
                        'controller' => 'ApproverCutiController@actionIndexApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Ditolak',
                        'method' => 'get',
                        'url' => '/approver-cuti/cuti-ditolak',
                        'controller' => 'ApproverCutiController@actionIndexRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Persetujuan | Lembur',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/approver-lembur',
                        'controller' => 'ApproverLemburController@actionIndex',
                        'name' => 'approver-cuti',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/approver-lembur/approved',
                        'controller' => 'ApproverLemburController@actionApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Approved',
                        'method' => 'post',
                        'url' => '/approver-lembur/approved',
                        'controller' => 'ApproverLemburController@actionApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'system',
                        'method' => 'get',
                        'url' => '/approver-lembur/rejected',
                        'controller' => 'ApproverLemburController@actionRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Rejected',
                        'method' => 'post',
                        'url' => '/approver-lembur/rejected',
                        'controller' => 'ApproverLemburController@actionRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Disetujui',
                        'method' => 'get',
                        'url' => '/approver-lembur/lembur-disetujui',
                        'controller' => 'ApproverLemburController@actionIndexApproved',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Ditolak',
                        'method' => 'get',
                        'url' => '/approver-lembur/lembur-ditolak',
                        'controller' => 'ApproverLemburController@actionIndexRejected',
                        'name' => '',
                        'middleware' => '',
                    ],
                ]  
            ],
             [
                'title' => 'Manajemen Absensi | Rekap Lembur',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/rekap-lembur',
                        'controller' => 'RekapLemburController@actionIndex',
                        'name' => 'rekap-lembur',
                        'middleware' => '',
                    ],
                ]
            ],
            [
                'title' => 'Approval Setting | Level 1',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/approval-setting-level1',
                        'controller' => 'ApprovalSettingLevel1Controller@actionIndex',
                        'name' => 'approval-setting-level1',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/approval-setting-level1/create',
                        'controller' => 'ApprovalSettingLevel1Controller@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/approval-setting-level1/update',
                        'controller' => 'ApprovalSettingLevel1Controller@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/approval-setting-level1/delete',
                        'controller' => 'ApprovalSettingLevel1Controller@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                    
                ]  
            ],
            [
                'title' => 'Approval Setting | Level 2',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/approval-setting-level2',
                        'controller' => 'ApprovalSettingLevel2Controller@actionIndex',
                        'name' => 'approval-setting-level2',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'create',
                        'method' => ['get', 'post'],
                        'url' => '/approval-setting-level2/create',
                        'controller' => 'ApprovalSettingLevel2Controller@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/approval-setting-level2/update',
                        'controller' => 'ApprovalSettingLevel2Controller@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'delete',
                        'method' => 'delete',
                        'url' => '/approval-setting-level2/delete',
                        'controller' => 'ApprovalSettingLevel2Controller@actionDelete',
                        'name' => '',
                        'middleware' => '',
                    ],
                    
                ]  
            ],
             [
                'title' => 'Manajemen Absensi | Kalender Kerja',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/kalender-kerja',
                        'controller' => 'KalenderKerjaController@actionIndex',
                        'name' => 'kalender-kerja',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'post',
                        'method' => ['get', 'post'],
                        'url' => '/kalender-kerja/create',
                        'controller' => 'KalenderKerjaController@actionCreate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' => ['get', 'post'],
                        'url' => '/kalender-kerja/update',
                        'controller' => 'KalenderKerjaController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'import excel',
                        'method' => 'post',
                        'url' => '/kalender-kerja/import-excel',
                        'controller' => 'KalenderKerjaController@importExcel',
                        'name' => '',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'Download Template Kerja',
                        'method' => 'post',
                        'url' => '/kalender-kerja/download-template',
                        'controller' => 'KalenderKerjaController@downloadTemplateKerja',
                        'name' => '',
                        'middleware' => '',
                    ],

                ]
            ],
            [
                'title' => 'Kalender Kerja Karyawan',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/kalender-kerja-karyawan',
                        'controller' => 'KalenderKerjaKaryawanController@actionIndex',
                        'name' => 'kalender-kerja-karyawan',
                        'middleware' => '',
                    ],
                    

                ]
            ],
            [
                'title' => 'Edit Password',
                'item' => [
                    [
                        'type' => 'index',
                        'method' => 'get',
                        'url' => '/edit-password',
                        'controller' => 'PasswordController@actionIndex',
                        'name' => 'edit-password',
                        'middleware' => '',
                    ],
                    [
                        'type' => 'update',
                        'method' =>  'post',
                        'url' => '/edit-password/update',
                        'controller' => 'PasswordController@actionUpdate',
                        'name' => '',
                        'middleware' => '',
                    ]
                ]
            ],
            
        ];

        if (!empty($index)) {
            return !empty($data[$index]) ? $data[$index] : null;
        }
        return $data;
    }

    function getIgnoreType($type = null)
    {
        $data = ['/', 'form', 'ajax', 'system'];
        if (!empty($type)) {
            if (!in_array($type, $data)) {
                return 1;
            }
            return 0;
        }
        return $data;
    }
}