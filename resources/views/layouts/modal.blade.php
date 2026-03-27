<button type="button" style="display: none;" id="buttonModalCustome" data-bs-toggle="modal" href="#showModalCustome"></button>
<div class="modal fade card-modal" id='showModalCustome' tabindex="-1" aria-hidden="true" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title mx-4 mt-4" id="title"></h5>
                <button type="button" class="btn-close me-4 mt-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<button type="button" style="display: none;" id="buttonModalCustomeDelete" data-bs-toggle="modal" data-bs-target="#showModalCustomeDelete" data-bs-dismiss="modal"></button>
<div class="modal fade card-modal" id='showModalCustomeDelete' tabindex="-1" aria-hidden="true" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title mx-4 mt-4" id="title">Konfirmasi</h5>
                <button type="button" class="btn-close me-4 mt-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" class="px-4">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                    <button type="submit" class="btn btn-primary">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>

<button type="button" style="display: none;" id="buttonModalCustomeConfir" data-bs-toggle="modal" data-bs-target="#showModalCustomeConfir" data-bs-dismiss="modal"></button>
<div class="modal fade card-modal" id='showModalCustomeConfir' tabindex="-1" aria-hidden="true" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title mx-4 mt-4" id="title">Konfirmasi</h5>
                <button type="button" class="btn-close me-4 mt-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                    <button type="submit" class="btn btn-primary">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>


<button type="button" style="display: none;" id="buttonModalData" data-bs-toggle="modal" href="#showModalData"></button>
<div class="modal fade bagan-data-table-2 card-modal" id='showModalData' tabindex="-1" aria-hidden="true" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0" style='padding:0px'>
                <h5 class="modal-title mx-4 mt-4" id="title"></h5>
                <button type="button" class="btn-close me-4 mt-3" id="closeModalData" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div id='form-pencarian'style='padding:20px 20px 0px 20px'>
                <div class='row'>
                    <div class="col-lg-8 col-md-10">
                        <div class="d-flex justify-content-center align-items-center border">
                            <input type="text" class="form-control border-0 search-data-table-2" id="" placeholder="Masukkan kata yang akan dicari">
                            <button type="submit" class="btn btn-white">
                                <span class="iconify" style="font-size: 24px; color: #CFD0D7;" data-icon="fe:search"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body table-responsive" style="padding-top:0px">
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>