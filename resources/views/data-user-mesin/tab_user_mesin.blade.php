<?php

$item = [
    1 => (object) [
        'nama' => 'Sikronisasi Data Mesin & Database',
        'key' => 'data-user-mesin-sinkronisasi',
    ],
    2 => (object) [
        'nama' => 'Data User Pada Database',
        'key' => 'data-user-mesin',
    ],
    3 => (object) [
        'nama' => 'Copy Data User Mesin',
        'key' => 'data-user-mesin-copy',
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

<ul class="nav nav-tabs">
    @foreach ($item as $key => $value)
        <li class="nav-item border-radius-top text-center button-tabs ms-2">
            <a class="nav-link border-radius-top tabs text-muted  <?= $active == $key ? 'active' : '' ?>" href="<?= url($value->key) ?>"><?= $value->nama ?></a>
        </li>
    @endforeach
</ul>