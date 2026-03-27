<?php

$item = [
    1 => (object) [
        'nama' => 'Data Rutin',
        'key' => 'report-absensi-karyawan',
        'type_link'=>1
    ],
     2 => (object) [
         'nama' => 'Data Shift',
         'key' => 'report-absensi-karyawan',
         'type_link'=>2
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
            <?php 
                $parameter_url=Request::all();
                $parameter_url['type_link']=$value->type_link;
                $url_link=(new \App\Http\Traits\GlobalFunction)->set_paramter_url($value->key,$parameter_url);
            ?>
            <a class="nav-link border-radius-top tabs text-muted  <?= $active == $key ? 'active' : '' ?>" href="<?= url($url_link) ?>"><?= $value->nama ?></a>
        </li>
    @endforeach
</ul>