<style>
    .file-upload-modern {
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        background: #f9fafb;
        cursor: pointer;
        transition: 0.3s;
    }

    .file-upload-modern:hover {
        background: #eef2ff;
        border-color: #6366f1;
    }

    .file-upload-modern input {
        display: none;
    }

    .file-upload-modern label {
        cursor: pointer;
    }

    .file-upload-modern i {
        font-size: 28px;
        color: #6366f1;
        display: block;
        margin-bottom: 8px;
    }

    .file-upload-modern span {
        font-weight: 600;
        display: block;
    }

    .file-upload-modern small {
        color: #6b7280;
    }

    .file-name {
        margin-top: 10px;
        font-size: 14px;
        color: #16a34a;
        font-weight: 600;
        display: none;
        word-break: break-all;
    }
</style>
<form action="{{ url('/izin/update') }}" method="POST" class="px-4"
    enctype="multipart/form-data">

    @csrf
    <input type="hidden" name="key_old" value="{{ $data->id_karyawan ?? '' }}">
    <input type="hidden" name="id_pengajuan" value="{{ $data->id ?? '' }}">

    

    <div class="row g-3">

        {{-- NIP & Nama --}}
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label class="form-label">NIP</label>
            <input type="text" class="form-control" name="nip" required value="{{ $data->nip ?? '' }}">
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12">
            <label class="form-label">Nama Karyawan</label>
            <input type="text" class="form-control" name="nm_karyawan" required
                value="{{ $data->nm_karyawan ?? '' }}">
        </div>

        {{-- Keterangan --}}
        <div class="col-12">
            <label class="form-label">Keterangan</label>
            <textarea class="form-control" name="keterangan" rows="2">{{ !empty($data->keterangan) ? $data->keterangan : '' }}</textarea>
        </div>

        {{-- Jenis & Tanggal --}}
        <div class="col-lg-4 col-md-6 col-sm-12">
            <label class="form-label">Jenis Pengajuan</label>
            <select name="jenis_izin" class="form-control" required>
                <option value="">-- Pilih --</option>
                <option value="izin" {{ !empty($data->jenis_pengajuan == 'izin') ? 'selected' : '' }}>Izin</option>
                <option value="sakit" {{ !empty($data->jenis_pengajuan == 'sakit') ? 'selected' : '' }}>Sakit</option>
            </select>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-control" name="tgl_mulai" value="{{ !empty($data->tgl_mulai) ? $data->tgl_mulai : '' }}" required>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" class="form-control" name="tgl_selesai" value="{{ !empty($data->tgl_selesai) ? $data->tgl_selesai : '' }}" required>
        </div>


       <div class="col-lg-12 col-md-12 col-sm-12">
            <label class="form-label">File Pendukung</label>

            <div class="file-upload-modern">
                <input type="file" name="file_pendukung" id="fileUpload" accept=".pdf,.jpg,.jpeg,.png">

                <label for="fileUpload">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span id="uploadText">
                        {{ !empty($data->file_pendukung) ? 'Ganti file' : 'Pilih atau drop file di sini' }}
                    </span>
                    <small id="uploadHint">PDF / JPG / PNG (Max 2MB)</small>
                </label>

                <div class="file-name" id="fileName"
                    style="{{ !empty($data->file_pendukung) ? 'display:block' : '' }}">
                    {{ !empty($data->file_pendukung) ? '✔ File terlampir: ' . basename($data->file_pendukung) : '' }}
                </div>
            </div>

            @if(!empty($data->file_pendukung))
                <div class="mt-2">
                    <i class="fa-solid fa-paperclip text-success"></i>
                    <a href="{{ asset( $data->file_pendukung) }}"
                    target="_blank"
                    class="ms-1 text-primary text-decoration-underline">
                        Lihat file pendukung
                    </a>
                </div>
            @endif
        </div>

        <hr>
        {{-- Submit --}}
        <div class="col-12">
            <button class="btn btn-primary px-4">
                Ubah
            </button>
        </div>

    </div>
</form>

<script>
    document.getElementById('fileUpload').addEventListener('change', function() {
        const fileName = this.files[0]?.name;
        const fileNameEl = document.getElementById('fileName');
        const uploadText = document.getElementById('uploadText');

        if (fileName) {
            fileNameEl.style.display = 'block';
            fileNameEl.innerHTML = '✔ File dipilih: ' + fileName;
            uploadText.innerHTML = 'Ganti file';
        }
    });
</script>
