<!-- <div> -->
    <!-- Smile, breathe, and go slowly. - Thich Nhat Hanh -->
    <div id="formRacikanResep">
        <hr class="mb-5">
        <input type="text" hidden class="form-control" name="resepDokterRacikan[0][no_resep]" id="racikan_noresep" value="202106010014">
        <div class="row justify-content-start align-items-end mb-3">
            <div class="col-lg-4 mb-3">
                <label for="nm_racikan" class="form-label">Nama Racikan</label>
                <input type="text" class="form-control" name="resepDokterRacikan[0][nama_racik]" id="nm_racikan">
            </div>
            <div class="col-lg-3 mb-3">
                <label for="metode_racik" class="form-label">Metode Racik</label>
                <div class="button-icon-inside">
                    <input type="text" class="input-text" id="metode_racik" />
                    <input type="text" hidden class="input-text" id="no_racik" name="resepDokterRacikan[0][no_racik]" value="1" />
                    <input type="text" hidden class="input-text" id="kd_racik" name="resepDokterRacikan[0][kd_racik]" />
                    <span id="modalMetodeRacik">
                        <span class="iconify hover-pointer text-primary" style="font-size: 24px;" data-icon="ant-design:select-outlined" data-rotate="270deg"></span>
                    </span>
                </div>
            </div>
            <div class="col-lg-3 mb-3">
                <label for="jumlah_racik" class="form-label">Jumlah Racik</label>
                <input type="text" name="resepDokterRacikan[0][jml_dr]" class="form-control" id="jumlah_racik">
            </div>
        </div>
        <div class="row justify-content-start align-items-end mb-3">
            <div class="col-lg-3 mb-3">
                <label for="aturan_pakai" class="form-label">Aturan Pakai</label>
                <input type="text" class="form-control" name="resepDokterRacikan[0][aturan_pakai]" id="aturan_pakai">
            </div>
            <div class="col-lg-7 mb-3">
                <label for="keteranganRacikan" class="form-label">Keterangan</label>
                <input type="text" class="form-control" name="resepDokterRacikan[0][keterangan]" id="keteranganRacikan">
            </div>
        </div>
        <div class="row justify-content-start align-items-end mb-3">
            <div class="d-flex align-items-center">
                <div class="col-lg-3 mb-3">
                    <input type="text" class="form-control" id="pencarian_obat" placeholder="Tambahkan Data Obat">
                </div>
                <label for="pencarian_obat" class="form-label ms-2" style="width: auto">*Masukkan karakter nama obat</label>
            </div>
        </div>
        <div style="overflow-x: auto; max-width: auto;">
            <table class="table border table-responsive-tablet">
                <thead>
                    <tr>
                        <th scope="col" class="py-4 ps-4">Kode Barang</th>
                        <th scope="col" class="py-4">Nama Barang</th>
                        <th scope="col" class="py-4">Satuan</th>
                        <th scope="col" class="py-4">Harga</th>
                        <th scope="col" class="py-4">Jenis</th>
                        <th scope="col" class="py-4">Stok</th>
                        <th scope="col" class="py-4">Kps</th>
                        <th scope="col" class="py-4">P1</th>
                        <th scope="col" class="py-4">P2</th>
                        <th scope="col" class="py-4">Kandungan</th>
                        <th scope="col" class="py-4">Jml</th>
                        <th scope="col" class="py-4">I.F</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataRacikan as $item)
                    <tr>
                        <td class="py-3 ps-4">{{ $item["kode_barang"] }}</td>
                        <td class="py-3">{{ $item["nama_barang"] }}</td>
                        <td class="py-3">{{ $item["satuan"] }}</td>
                        <td class="py-3"><?php FormatMoney($item["harga"]) ?></td>
                        <td class="py-3">{{ $item["jenis"] }}</td>
                        <td class="py-3">{{ $item["stok"] }}</td>
                        <td class="py-3">{{ $item["kps"] }}</td>
                        <td class="py-3"><input type="text" class="form-control" style="width: 50px" name="resepDokterRacikanDetail[0][p1]" value=""> </td>
                        <td class="py-3"><input type="text" class="form-control" style="width: 50px" name="resepDokterRacikanDetail[0][p2]" value=""> </td>
                        <td class="py-3"><input type="text" class="form-control" style="width: 100px" name="resepDokterRacikanDetail[0][kandungan]" value=""> </td>
                        <td class="py-3"><input type="text" class="form-control" style="width: 50px" name="resepDokterRacikanDetail[0][jml]" value=""> </td>
                        <td class="py-3">{{ $item["if"] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row justify-content-end align-items-end my-0">
            <div class="col-lg-2 mb-3">
                <div class="d-grid gap-2">
                    <button class="btn btn-warning text-white addNewForm" type="button">Tambah Data Racikan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Button trigger modal -->
    <button type="button" hidden id="showModal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-header border-0 px-0">
                        <div class="col-10">
                            <div class="d-flex justify-content-center align-items-center border">
                                <input type="text" class="form-control border-0" id="exampleFormControlInput1" placeholder="Masukkan kata yang akan dicari">
                                <button type="submit" class="btn btn-white">
                                    <span class="iconify" style="font-size: 24px; color: #CFD0D7;" data-icon="fe:search"></span>
                                </button>
                            </div>
                        </div>
                        <button type="button" id="close" class="btn-close me-1" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" class="py-4 ps-4">Kode</th>
                                <th scope="col" class="py-4 pe-4">Metode Racik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($metodeRacik as $item) <tr>
                                <?php $metode = $item["nm_racik"] ?>
                                <?php $no = $item["kd_racik"] ?>
                                <td class="py-3 ps-4">{{ $item["kd_racik"] }}</td>
                                <td class="py-3"> <span class="text-primary hover-pointer" onclick="setValue('{{ $metode }}', '{{ $no }}')">{{ $metode }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer" style="display: none;">
                    <button type="button" class="btn btn-secondary" id="close" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<!-- </div> -->