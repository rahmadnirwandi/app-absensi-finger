<style>
    #color_view{
        background-color: #456;
        width: 80px;
    }
</style>
<form action="{{ url($action_form) }}" method="{{ !empty($method_form) ? $method_form : 'POST' }}">
    @csrf
    <?php
        $kode=!empty($model->id_jenis_jadwal) ? $model->id_jenis_jadwal : '';
    ?>
    <input type="hidden" name="key_old" value="{{ $kode }}">
    <input type="hidden" name="type_jenis" value="1">

{{--    <div class="row justify-content-start align-items-end mb-3">--}}
{{--        <div class="col-lg-6">--}}
{{--            <div class="row justify-content-start align-items-end">--}}
{{--                <div class="col-lg-6 mb-3">--}}
{{--                    <div class='bagan_form'>--}}
{{--                        <label for="type_jenis" class="form-label">Kelompok Jadwal</label>--}}
{{--                        <select class="form-select" id="type_jenis" name="type_jenis" required aria-label="Default select ">--}}
{{--                            @if(!empty($type_jenis_jadwal))--}}
{{--                                @foreach($type_jenis_jadwal as $key => $val)--}}
{{--                                    <?php--}}
{{--                                        $model_type_jenis_jadwal=!empty($model->type_jenis) ? $model->type_jenis : 2;--}}
{{--                                    ?>--}}
{{--                                    <option value='{{ $key }}' {{ ($model_type_jenis_jadwal==$key) ? 'selected' : '' }}>{{ $val }}</option>--}}
{{--                                @endforeach--}}
{{--                            @endif--}}
{{--                        </select>--}}
{{--                        <div class="message"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="col-lg-6 mb-3">--}}
{{--                    <div class='bagan_form'>--}}
{{--                        <label for="nm_jenis_jadwal" class="form-label">Nama Jadwal</label>--}}
{{--                        <input type="text" class="form-control" id="nm_jenis_jadwal" name='nm_jenis_jadwal' required value="{{ !empty($model->nm_jenis_jadwal) ? $model->nm_jenis_jadwal : '' }}">--}}
{{--                        <div class="message"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="col-lg-12">--}}
{{--            <div class="row justify-content-start align-items-end">--}}
{{--                <div class="col-lg-2">--}}
{{--                    <div class='bagan_form'>--}}
{{--                        <label for="masuk_kerja" class="form-label">Jam Masuk Kerja</label>--}}
{{--                        <input type="time" class="form-control input-daterange" id="masuk_kerja" name='masuk_kerja' required value="{{ !empty($model->masuk_kerja) ? $model->masuk_kerja : '' }}">--}}
{{--                        <div class="message"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="col-lg-4">--}}
{{--                    <div class='bagan_form'>--}}
{{--                        <label for="pulang_kerja" class="form-label">--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-6">--}}
{{--                                    Jam Pulang Kerja--}}
{{--                                </div>--}}
{{--                                <div class="col-md-6">--}}
{{--                                    <?php --}}
{{--                                        $check_pk_next_day=!empty($model->pulang_kerja_next_day) ? "checked" : ''; --}}
{{--                                    ?>--}}
{{--                                    <div class="form-check">--}}
{{--                                        <input class="form-check-input" type="checkbox" {{ $check_pk_next_day }} name='pulang_kerja_next_day' value="1" id="pulang_kerja_next_day">--}}
{{--                                        <label class="form-check-label" style='margin-top: 7px;margin-left: 5px;' for="pulang_kerja_next_day">Besok</label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </label>--}}
{{--                        <input type="time" class="form-control input-daterange" id="pulang_kerja" name='pulang_kerja' required value="{{ !empty($model->pulang_kerja) ? $model->pulang_kerja : '' }}">--}}
{{--                        <div class="message"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="col-lg-2">--}}
{{--                    <div class='bagan_form'>--}}
{{--                        <label for="awal_istirahat" class="form-label">Awal Istirahat</label>--}}
{{--                        <input type="time" class="form-control input-daterange" id="awal_istirahat" name='awal_istirahat' value="{{ !empty($model->awal_istirahat) ? $model->awal_istirahat : '' }}">--}}
{{--                        <div class="message"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="col-lg-4">--}}
{{--                    <div class='bagan_form'>--}}
{{--                        <label for="akhir_istirahat" class="form-label">--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-6">--}}
{{--                                    Akhir Istirahat--}}
{{--                                </div>--}}
{{--                                <div class="col-md-6">--}}
{{--                                    <?php --}}
{{--                                        $check_ais_next_day=!empty($model->akhir_istirahat_next_day) ? "checked" : ''; --}}
{{--                                    ?>--}}
{{--                                    <div class="form-check">--}}
{{--                                        <input class="form-check-input" type="checkbox" {{ $check_ais_next_day }} name='akhir_istirahat_next_day' value="1" id="akhir_istirahat_next_day">--}}
{{--                                        <label class="form-check-label" style='margin-top: 7px;margin-left: 5px;' for="akhir_istirahat_next_day">Besok</label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </label>--}}
{{--                        <input type="time" class="form-control input-daterange" id="akhir_istirahat" name='akhir_istirahat' value="{{ !empty($model->akhir_istirahat) ? $model->akhir_istirahat : '' }}">--}}
{{--                        <div class="message"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="col-lg-12 mt-3">--}}
{{--            <div class="row justify-content-start align-items-end">--}}
{{--                <div class="col-lg-9">--}}
{{--                    <div class='bagan_form'>--}}
{{--                        <label for="akhir_istirahat" class="form-label">Hari Kerja</label>--}}
{{--                            <div class="row justify-content-start">--}}
{{--                                <?php--}}
{{--                                    $get_hari=(new \App\Http\Traits\GlobalFunction)->hari();--}}
{{--                                    $get_vhktmp=!empty($model->hari_kerja) ? $model->hari_kerja : '';--}}
{{--                                    $get_vhktmp_array=explode(',',$get_vhktmp);--}}
{{--                                    $hari_kerja_me=[];--}}
{{--                                    if(!empty($get_vhktmp_array)){--}}
{{--                                        foreach($get_vhktmp_array as $value){--}}
{{--                                            $hari_kerja_me[$value]=$value;--}}
{{--                                        }--}}
{{--                                    }--}}
{{--                                ?>--}}
{{--                                @foreach($get_hari as $key_hari => $item_hari)--}}
{{--                                    <?php--}}
{{--                                        $checked=!empty($hari_kerja_me[$key_hari]) ? 'checked' : '';--}}
{{--                                    ?>--}}
{{--                                    <div class="col">--}}
{{--                                        <div class="form-check">--}}
{{--                                            <input class="form-check-input" type="checkbox" {{ $checked }} name='hari_kerja[]' value="{{ $key_hari }}" id="hari_{{ $key_hari }}">--}}
{{--                                            <label class="form-check-label" style='margin-top: 7px;margin-left: 5px;' for="hari_{{ $key_hari }}">{{ $item_hari }}</label>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endforeach--}}
{{--                            </div>--}}
{{--                        <div class="message"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="col-lg-12 mt-3">--}}
{{--            <div class="row justify-content-start align-items-end">--}}
{{--                <div class="col-lg-3">--}}
{{--                    <div class='bagan_form'>--}}
{{--                        <label for="bg_color" class="form-label">Kelompok Warna</label>--}}
{{--                        <div class="form-row align-items-center">--}}
{{--                            <div class="col-auto">--}}
{{--                                <!-- <label class="sr-only" for="inlineFormInputGroup">Username</label> -->--}}
{{--                                <div class="input-group mb-2">--}}
{{--                                    <div class="input-group-prepend">--}}
{{--                                        <div class="input-group-text" id='color_view'>&nbsp;</div>--}}
{{--                                    </div>--}}
{{--                                    <!-- <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Username"> -->--}}
{{--                                    <select class="form-control" id="bg_color"  name="bg_color"  aria-label="Default select ">--}}
{{--                                        @if($get_bgcolor)--}}
{{--                                            @foreach($get_bgcolor as $key_color => $item_color)--}}
{{--                                                <?php --}}
{{--                                                    $check=!empty($model->bg_color) ? $model->bg_color : "";--}}
{{--                                                    $checked=($check==$item_color) ? 'selected' : '';--}}
{{--                                                ?>--}}
{{--                                                --}}
{{--                                                @if($checked)--}}
{{--                                                    <option value='{{ $item_color }}' {{ $checked }}>{{ $item_color }}</option>--}}
{{--                                                @elseif(!in_array($item_color,$get_bgcolor_use))--}}
{{--                                                    <option value='{{ $item_color }}' {{ $checked }}>{{ $item_color }}</option>--}}
{{--                                                @endif--}}

{{--                                            @endforeach--}}
{{--                                        @endif--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
            <div class="row align-items-end mb-3">
                <div class="col-lg-4 mb-3">
                    <div class='bagan_form'>
                        <label for="nm_jenis_jadwal" class="form-label">Nama Jadwal</label>
                        <input type="text" class="form-control" id="nm_jenis_jadwal" name='nm_jenis_jadwal' required value="{{ !empty($model->nm_jenis_jadwal) ? $model->nm_jenis_jadwal : '' }}">
                        <div class="message"></div>
                    </div>
                </div>
                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="masuk_kerja" class="form-label">Jam Masuk Kerja</label>
                        <input type="time" class="form-control input-daterange" id="masuk_kerja" name='masuk_kerja' required value="{{ !empty($model->masuk_kerja) ? $model->masuk_kerja : '' }}">
                        <div class="message"></div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class='bagan_form'>
                        <label for="pulang_kerja" class="form-label">
                            <div class="row">
                                <div class="col-md-6">
                                    Jam Pulang Kerja
                                </div>

                            </div>
                        </label>
                        <input type="time" class="form-control input-daterange" id="pulang_kerja" name='pulang_kerja' required value="{{ !empty($model->pulang_kerja) ? $model->pulang_kerja : '' }}">
                        <div class="message"></div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class='bagan_form'>
                        <label for="bg_color" class="form-label">Kelompok Warna</label>
                        <div class="form-row align-items-center">
                            <div class="col-auto">
                                <!-- <label class="sr-only" for="inlineFormInputGroup">Username</label> -->
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" id='color_view'>&nbsp;</div>
                                    </div>
                                    <!-- <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Username"> -->
                                    <select class="form-control" id="bg_color"  name="bg_color"  aria-label="Default select ">
                                        @if($get_bgcolor)
                                            @foreach($get_bgcolor as $key_color => $item_color)
                                                    <?php
                                                    $check=!empty($model->bg_color) ? $model->bg_color : "";
                                                    $checked=($check==$item_color) ? 'selected' : '';
                                                    ?>

                                                @if($checked)
                                                    <option value='{{ $item_color }}' {{ $checked }}>{{ $item_color }}</option>
                                                @elseif(!in_array($item_color,$get_bgcolor_use))
                                                    <option value='{{ $item_color }}' {{ $checked }}>{{ $item_color }}</option>
                                                @endif

                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <div class="row justify-content-start align-items-end">
        <div class="col-lg-5">
            <button class="btn btn-primary validate_submit" type="submit">{{ !empty($kode) ? 'Ubah' : 'Simpan'  }}</button>
        </div>
    </div>
</form>

@push('script-end-2')
    <script src="{{ asset('js/jenis-jadwal-absensi/form.js') }}"></script>
@endpush
