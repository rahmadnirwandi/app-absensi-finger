<?php

$item = [
    1 => (object) [
        'nama' => 'Data Karyawan',
        'key' => 'data-karyawan',
    ],
//    2 => (object) [
//        'nama' => 'Jadwal Karyawan',
//        'key' => 'data-jadwal-karyawan',
//    ]
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

<ul class="nav nav-tabs">
    @foreach ($item as $key => $value)
        <li class="nav-item border-radius-top text-center button-tabs ms-2">
            <a class="nav-link border-radius-top tabs text-muted  <?= $active == $key ? 'active' : '' ?>" href="<?= url($value->key) ?>"><?= $value->nama ?></a>
        </li>
    @endforeach
</ul>