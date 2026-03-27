<div class="modal fade" id="modalUnggahBerkas" tabindex="-1" aria-labelledby="modalUnggahBerkasLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <!-- <form id="modalUnggahBerkasForm">
                @csrf
            </form> -->
            <form id="formUnggahBerkas">

                @csrf
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title mx-4" id="modalUnggahBerkasLabel">
                            <span>Unggah Berkas Pasien</span> 
                            <div class="spinner-border spinner-border-sm d-none" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </h5>
                        <button type="button" class="btn-close closeUnggahBerkasLabel" ></button>
                    </div>
                    <div class="modal-body ">
                        <table class="table border text-center">
                            <thead>
                                <tr>
                                    <th span="2" width="30%" class="py-4">No Rawat</th>
                                    <th span="2" width="30%" class="py-4">No. RM</th>
                                    <th span="2" width="30%" class="py-4">Nama Pasien</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="berkas-nrwt" class="py-3"></td>
                                    <td id="berkas-nrm" class="py-3"></td>
                                    <td id="berkas-nm" class="py-3"></td>
                                </tr>
                            </tbody>
                            
                        </table>
                        <div>
                            
                            <label for="form-select" class="form-label fw-bolder">Jenis Berkas</label>
                            <select name="jenis_berkas" class="form-select" aria-label="Default select example" required>
                                <option value="-" selected>Pilih Jenis Berkas</option>
                            @foreach($berkas_list as $item)
                                <option value="{{$item['nama']}},{{$item['type']}},{{$item['prefix']}},{{$item['kode']}}">{{$item['nama']}}</option>
                            @endforeach
                            </select>
                            <div class="mt-3">
                                <label for="formFile" class="form-label fw-bolder">File Berkas (PDF), max = 3MB</label>
                                <input name="berkas" class="form-control" type="file" id="formFile" accept=".pdf" required>
                            </div>
                            
                        </div>
                        <div class="alert alert-danger mt-3 d-none" role="alert">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class=" closeUnggahBerkasLabel btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="submitPenyerahanResep" class="btn btn-primary">Kirim</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    