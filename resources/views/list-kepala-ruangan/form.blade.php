<?php
    $get_user=(new \App\Http\Traits\AuthFunction)->getUser();
?>
<style>
    .select2-selection {
        height: 2.3em !important;
        padding-top: 4px;
    }

    .select2 {
        width: 100% !important;
    }

    .select2-results > ul.select2-results__options {
        width: auto !important;
        min-height: auto !important;
        max-height: 75vh !important;
    }
</style>
<form action="{{ $actionForm }}" method="POST" class="px-4 py-2">
    @csrf
    @method("POST") 
    <div class="alert alert-warning" role="alert">
        kepala Ruangan hanya bisa di setting untuk 1 ruang saja!
    </div>
    <div class="row justify-content-start align-items-end">
        <div class="col-lg-12 mb-3">
            <label for="no_rawat" class="form-label">Departemen / Bidang</label>
            <input type="text" class="form-control" disabled value="{{ !empty($nm_departemen) ? $nm_departemen : '' }}">
        </div>
    </div>
    <div class="row justify-content-start align-items-end">
        <div class="col-lg-12 mb-3">
            <label for="id_user" class="form-label">Ruangan <span class="text-danger">*</span></label>
            <input type="hidden" name="ruangan" value="{{ !empty($id_ruangan) ? $id_ruangan : '' }}">
            <input type="text" class="form-control" disabled value="{{ !empty($nm_ruangan) ? $nm_ruangan : '' }}">
        </div>
    </div>
    <div class="row justify-content-start align-items-end">
        <div class="col-lg-12 mb-3">
            <label for="password" class="form-label">Pilih Karyawan <span class="text-danger">*</span></label>
            <select id="item_poli" class="form-select" name="id_karyawan" style="width: 100%" required>
            </select>
        </div>
    </div>

    <hr class="mb-4"> 

    <div class="row justify-content-start align-items-end table-responsive">
        <div class="col-lg-2 mb-3">
            <div class="d-grid gap-2">
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#item_poli').select2({
            dropdownParent: $("#showModalCustome"),
            ajax: {
                url: base_url + "/list-kepala-ruangan/ajax",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    var search = params.term;
                    return {
                        search: search
                    };
                },
                processResults: function(data) {
                    var results = [];

                    $.each(data, function(index, item) {
                        results.push({
                            id: item.id_karyawan,
                            text: item.nm_karyawan
                        });
                    });
                    return {
                        results: results
                    };
                }
            }
        });
    });
</script>