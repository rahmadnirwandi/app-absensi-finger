<button type="button" style="display:none;" id="buttonModalJks" data-bs-toggle="modal" data-bs-target="#showModalJks" data-bs-dismiss="modal"></button>
<div class="modal fade" id='showModalJks' tabindex="-1" aria-hidden="true" data-bs-keyboard="false">
    <div class="modal-dialog" style='max-width:45%'>
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title mx-4 mt-4" id="title">Konfirmasi</h5>
                <button type="button" class="btn-close me-4 mt-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" id='proses_jadwal' method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name='data_sent' id='data_sent' value=''>
                    <input type="hidden" name='params' id='params' value=''>
                    <input type="hidden" name='tgl_ubah' id='tgl_ubah' value=''>
                    <textarea style='display:none' id='jadwal_terpilih_sistem' name='jadwal_terpilih_sistem' style='width:100%'></textarea>
                    <textarea style='display:none' id='jadwal_terpilih_sendiri' name='jadwal_terpilih_sendiri' style='width:100%'></textarea>
                    
                    <div class='card'>
                        <div class='card-body' style="background:#fff2f2">
                            <h4>Informasi</h4>
                            <p>Jika Jadwal libur di pilih, maka semua jadwal tidak dapat di pilih,<br> silahkan tidak menceklist jadwal libur, untuk dapat menceklist jadwal kerja</p>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-lg-12 text-end">
                            <a href="#" class="btn btn-warning" id='btn_reset'>Reset ke jadwal sistem</a><span></span>
                        </div>  
                    </div>
                    <div style="overflow-x: auto; max-width: auto;">
                        <table class="table border table-responsive-tablet">
                            <thead>
                                <tr>
                                    <th style='width:48%'>Nama Jadwal</th>
                                    <th style='width:50%'>Waktu</th>
                                    <th style='width:2%'>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($list_data_jadwal))
                                    @foreach($list_data_jadwal as $item)
                                        <?php 
                                            $bgcolor=!empty($item->bg_color) ? $item->bg_color : "#fff";
                                            
                                            $waktu_text='';
                                            if($item->type_jadwal==2){
                                                $waktu_text='--';
                                            }
                                            if($item->type_jadwal==1){
                                                $waktu_text=$item->masuk_kerja.' s/d '.$item->pulang_kerja;
                                                if(!empty($item->pulang_kerja_next_day)){
                                                    $waktu_text=$waktu_text.' Esok hari';
                                                }
                                            }
                                        ?>
                                        <tr style="border-bottom:1px solid; background:{{ $bgcolor }}">
                                            <td>{{ !empty($item->nm_jenis_jadwal) ? $item->nm_jenis_jadwal : '' }}</td>
                                            <td>{{ $waktu_text }}</td>
                                            <td>
                                                <input class="form-check-input pilih_jadwal" type="checkbox" data-type-jadwal='{{ $item->type_jadwal }}' value='{{ $item->id_jenis_jadwal }}' >
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id='modal-closes' data-bs-dismiss="modal">Tidak</button>
                    <button type="submit" class="btn btn-primary">Ubah</button>
                </div>
            </form>
        </div>
    </div>
</div>