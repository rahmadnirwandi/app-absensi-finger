<style>
    .btn-ubah:hover {
        background-color: #008BFF !important;
    }
    .btn-ubah {
        font-size: 20px
    }

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

<form action="{{ url($action_form) }}" method="POST" class="px-4">
    @csrf
    <div class="row justify-content-start align-items-end mb-3">
        <div class="col-lg-12">
            <div class="row justify-content-start align-items-end">

                <input type="text" name="id_karyawan" hidden value="{{ $data->id_karyawan }}">
                <input type="text" name="id" hidden value="{{ $data->id }}">

                <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="jumlah_cuti" class="form-label">Karyawan</label>
                            <input type="text" class="form-control" id="jumlah_cuti" name="jumlah_cuti" value="{{ $data->nm_karyawan }}" required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-6 col-sm-12 mb-3">
                    <label class="form-label">Awal Cuti</label>
                    <input type="date" class="form-control" name="awal_cuti" value="{{ $data->awal_cuti }}" required>
                </div>

                <div class="col-lg-12 col-md-6 col-sm-12 mb-3">
                    <label class="form-label">Akhir Cuti</label>
                    <input type="date" class="form-control" name="akhir_cuti" value="{{ $data->akhir_cuti }}" required>
                </div>

                <div class="col-lg-12 mb-3">
                    <label for="id_jenis_cuti" class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                    <select id="id_jenis_cuti" class="form-select" name="id_jenis_cuti" style="width: 100%" required>
                        <option value="{{ $data->id_jenis_cuti }}" selected>
                            {{ $data->nama_jenis_cuti }}
                        </option>
                    </select>
                </div>


                 <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="jumlah_cuti" class="form-label">Jumlah Cuti<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah_cuti" name="jumlah_cuti" min='0' value="{{ $data->jumlah }}" required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="pakai" class="form-label">Pakai<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pakai" name="pakai" min='0' value="{{ $data->pakai }}" required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="sisa" class="form-label">Sisa<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="sisa" name="sisa" min='0' value="{{ $data->sisa }}" required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class='bagan_form'>
                        <div class="bagan_form">
                            <label for="tukar" class="form-label">Tukar<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="tukar" name="tukar" min='0' value="{{ $data->tukar }}" required>
                        </div>
                        <div class="message"></div>
                    </div>
                </div>

                
            </div>
        </div>
    </div>

    <div class="row justify-content-start align-items-end">
        <div class="col-lg-12">
            <button class="btn btn-primary btn-ubah validate_submit w-100 p-2" type="submit">Ubah</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {

        $('#id_jenis_cuti').select2({
            dropdownParent: $("#showModalCustome"),
            ajax: {
                url: base_url + "/jenis-cuti/ajax",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

    });
</script>