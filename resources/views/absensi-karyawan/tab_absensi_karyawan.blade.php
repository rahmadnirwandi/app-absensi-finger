<?php

$item = [
    1 => (object) [
        'nama' => 'Data Absensi Rutin',
        'key' => 'absensi-karyawan',
    ],
    2 => (object) [
    'nama' => 'Data Absensi Shift',
    'key' => 'absensi-karyawan-shift',
    ],
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