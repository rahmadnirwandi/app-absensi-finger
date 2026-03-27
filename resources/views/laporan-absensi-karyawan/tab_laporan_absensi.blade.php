<?php

$item = [
    1 => (object) [
        'nama' => 'Report Absensi Histori Dari Mesin',
        'key' => 'report-log-mesin',
    ],
    2 => (object) [
        'nama' => 'Report Absensi Histori Dari Karyawan',
        'key' => 'report-log-mesin-by-karyawan',
    ],
    3 => (object) [
        'nama' => 'Laporan Absensi Rutin',
        'key' => 'laporan-absensi-karyawan',
    ],
    4 => (object) [
        'nama' => 'Report Absensi karyawan',
        'key' => 'report-absensi-karyawan',
    ]
];

$item = (new \App\Http\Traits\AuthFunction())->checkMenuAkses($item);

if (!empty($kode_key_old)) {
    foreach ($item as $key => $value) {
        if ($active != $key) {
            unset($item[$key]);
        }
    }
}
?>

<ul class="nav nav-tabs mt-4">
    @foreach ($item as $key => $value)
        <li class="nav-item border-radius-top text-center button-tabs ms-2">
            <a class="nav-link border-radius-top tabs text-muted  <?= $active == $key ? 'active' : '' ?>" href="<?= url($value->key) ?>"><?= $value->nama ?></a>
        </li>
    @endforeach
</ul>