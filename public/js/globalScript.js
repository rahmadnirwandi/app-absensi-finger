/*
    function ini berfungsi jika halaman di reload,maka data yang dikirimkan sebelumnya akan di tampilkan lagi
    1. pada blade tambahkan script <x-set-form-request></x-set-form-request>
    2. tambahkan atribute gsf_nama_field sebagai target data
    3. data-gsf-type = type excutenya
    4. data-gsf-nilai = sebagai nilai pembanding
    4. khusus table gunakan gsf_nama_field,gsf_nama_field ( check di tindakan_petugas_daftar.blade)
*/


function is_decimal($val) {
    return ($val % 1 != 0) ? 1 : 0;
}

Number.prototype.countDecimals = function () {
    if (Math.floor(this.valueOf()) === this.valueOf()) return 0;
    return this.toString().split(".")[1].length || 0;
}

function digit_decimal($val) {
    if (is_decimal($val)) {
        return $val.countDecimals();
    } else {
        return 0;
    }
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = number.toFixed(decimals);

    var nstr = number.toString();
    nstr += '';
    var x = nstr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? dec_point + x[1] : '';
    var rgx = /(\d+)(\d{3})/;

    while (rgx.test(x1))
        x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');

    return x1 + x2;
}

function is_valid_json_string(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function decode_html_raw(str) {
    if(!$.isNumeric(str)){
        pos = str.indexOf('&lt;');
        while (pos >= 0) {
            str = str.replace('&lt;', '<')
            pos = str.indexOf('&lt;');
        }
        pos = str.indexOf('&gt;');
        while (pos >= 0) {
            str = str.replace('&gt;', '>')
            pos = str.indexOf('&gt;');
        }

        return $.trim(str);
    }
    
    return str;
}

$(document).ready(function () {
    let data_form = (typeof $(document).find('#gsf_item')) ? $(document).find('#gsf_item').html() : null;
    if (data_form) {
        if (data_form.length) {
            data_form = JSON.parse(data_form);

            Object.keys(data_form).forEach(function (key) {
                let form_ = '.gsf_' + key;
                let form = (typeof $(document).find(form_)) ? $(document).find(form_) : '';

                if (form.length) {
                    let type_form = form.attr('data-gsf-type');
                    if (type_form) {
                        if (type_form == 'table') {
                            let form__ = '.gsf_set_' + key;

                            let value_sent_array = (data_form[key][0]) ? data_form[key][0] : '';
                            if (value_sent_array) {
                                let value_sent = [];

                                Object.keys(data_form[key]).forEach(function (key_data) {
                                    value_sent[data_form[key][key_data]] = data_form[key][key_data];
                                });

                                form.find(form__).each(function () {
                                    let type = $(this).attr('type');
                                    let nilai = $(this).attr('data-gsf-nilai');

                                    if (type == 'checkbox') {

                                        if (value_sent[nilai]) {
                                            $(this).prop('checked', true);
                                        } else {
                                            $(this).prop('checked', false);
                                        }
                                    }
                                });
                            }
                        }
                    }
                }
            });
        }
    }
});


$(document).find('.input-date-range').each(function () {
    let parent = $(this).parent('.input-date-range-bagan');
    let form_start = (typeof parent.find('#tgl_start')) ? parent.find('#tgl_start').val() : null;
    let form_end = (typeof parent.find('#tgl_end')) ? parent.find('#tgl_end').val() : null;

    let max_range = (typeof $(this).attr('data-max-range') != "undefined" || $(this).attr('data-max-range') != null) ? $(this).attr('data-max-range') : '';

    var formattedDate = new Date();
    var d = formattedDate.getDate();
    var m = formattedDate.getMonth();
    m += 1;
    var y = formattedDate.getFullYear();

    let new_date = y + "-" + m + "-" + d;

    if (!form_start.length) {
        form_start = new_date;
    }

    if (!form_end.length) {
        form_end = new_date;
    }

    parent.find('#tgl_start').val(form_start);
    parent.find('#tgl_end').val(form_end);

    $option={
        startDate: form_start,
        endDate: form_end,
        locale: {
            format: "YYYY-MM-DD",
        },
        showDropdowns: true,
        linkedCalendars: false,
    };

    if(max_range.length){
        $option_extend={
            maxSpan: { days: max_range }
        };
        $.extend($option,$option_extend );
    }

    $(this).daterangepicker($option);
});

$(document).find(".input-date-range").on("change keyup", function () {
    let parent = $(this).parent('.input-date-range-bagan');
    let form_start = (typeof parent.find('#tgl_start')) ? parent.find('#tgl_start') : null;
    let form_end = (typeof parent.find('#tgl_end')) ? parent.find('#tgl_end') : null;

    if (form_start.length && form_end.length) {
        let date = $(this).val().split(" ");
        let start = date[0].split("/").reverse().join("-");
        let end = date[2].split("/").reverse().join("-");

        parent.find("#tgl_start").val(start);
        parent.find("#tgl_end").val(end);

        let startDate = moment(start, "YYYY-MM-DD");
        let endDate = moment(end, "YYYY-MM-DD");
        let days = endDate.diff(startDate, 'days') + 1;
        console.log(days);
        $("#jumlah").val(days);
    }
});


$(document).find('.input-date-time').each(function () {

    let parent = $(this).parent('.input-date-time-bagan');
    let form_tgl = (typeof parent.find('#tgl')) ? parent.find('#tgl').val() : null;
    let form_jam = (typeof parent.find('#jam')) ? parent.find('#jam').val() : null;

    let date_this = '';
    if ((!form_tgl) || (!form_jam)) {
        date_this = new Date();
    } else {
        date_this = form_tgl + ' - ' + form_jam;
    }

    $(this).daterangepicker({
        singleDatePicker: true,
        startDate: date_this,
        locale: {
            format: "YYYY-MM-DD - HH:mm",
        },
        timePicker: true,
        showDropdowns: true,
        linkedCalendars: false,
    });
});

$(document).find(".input-date-time").on("change keyup", function () {
    let parent = $(this).parent('.input-date-time-bagan');
    let form_tgl = (typeof parent.find('#tgl')) ? parent.find('#tgl') : null;
    let form_jam = (typeof parent.find('#jam')) ? parent.find('#jam') : null;

    if (form_tgl.length && form_jam.length) {
        let date = $(this).val();
        let tgl = date.substring(0, 10);
        let jam = date.substring(13);

        parent.find("#tgl").val(tgl);
        parent.find("#jam").val(jam);
    }
});

$(document).find('.input-date').each(function () {

    let parent = $(this).parent('.input-date-bagan');
    let form_tgl = (typeof parent.find('#tgl')) ? parent.find('#tgl').val() : null;

    let date_this = '';
    if ((!form_tgl)) {
        date_this = new Date();
    } else {
        date_this = form_tgl;
    }

    $(this).daterangepicker({
        singleDatePicker: true,
        startDate: date_this,
        locale: {
            format: "YYYY-MM-DD",
        },
        showDropdowns: true,
        linkedCalendars: false,
    });
});

$(document).find(".input-date").on("change keyup", function () {
    let parent = $(this).parent('.input-date-bagan');
    let form_tgl = (typeof parent.find('#tgl')) ? parent.find('#tgl') : null;

    if (form_tgl.length) {
        let date = $(this).val();
        let tgl = date.substring(0, 10);

        let startDate = moment(date, "YYYY-MM-DD");
        let days = startDate.diff(startDate, 'days')+1;
        parent.find("#tgl").val(tgl);
        $("#jumlah").val(days);
    }
});


$(document).find('.input-month').each(function () {

    let parent = $(this).parent('.input-month-year-bagan');
    let form_value=$(this).val();

    var currentDate = new Date();
    var month = currentDate.getMonth() + 1; // Menggunakan +1 karena nilai bulan dimulai dari 0 (Januari) hingga 11 (Desember)
    var year = currentDate.getFullYear();

    let date_this = '';
    if ((!form_value)) {
        date_this = year+'-'+month;
    } else {
        date_this = form_value;
    }

    $(this).daterangepicker({
        singleDatePicker: true,
        startDate: date_this,
        locale: {
            format: "YYYY-MM",
        },
        showDropdowns: true,
        linkedCalendars: false,
    }).on('show.daterangepicker', function (ev, picker) {
        picker.container.addClass('monthly');
    }).on('hide.daterangepicker', function (ev, picker) {
        $td = $(picker.container).find('.table-condensed tbody tr:nth-child(3) td:first-child');
        setTimeout(function() {
            $td.trigger('mousedown');
        }, 1);
    });
});

$(document).find('.input-year').each(function () {

    let parent = $(this).parent('.input-month-year-bagan');
    let form_value=$(this).val();

    var currentDate = new Date();
    var year = currentDate.getFullYear();

    let date_this = '';
    if ((!form_value)) {
        date_this = year;
    } else {
        date_this = form_value;
    }

    $(this).daterangepicker({
        singleDatePicker: true,
        startDate: date_this,
        locale: {
            format: "YYYY",
        },
        showDropdowns: true,
        linkedCalendars: false,
    }).on('show.daterangepicker', function (ev, picker) {
        picker.container.addClass('yearly');
    }).on('hide.daterangepicker', function (ev, picker) {
        $td = $(picker.container).find('.table-condensed tbody tr:nth-child(3) td:first-child');
        setTimeout(function() {
            $td.trigger('mousedown');
        }, 1);
    });
});

$(document).find(".get-data-by-date").on("change keyup", function () {

    let url = (typeof $(this).data("url") != "undefined" || $(this).data("url") != null) ? $(this).data("url") : '';
    let data = (typeof $(this).data("value") != "undefined" || $(this).data("value") != null) ? $(this).data("value") : '';
    let target = (typeof $(this).data("target") != "undefined" || $(this).data("target") != null) ? $(this).data("target") : '';

    $me_value = $(this);
    $data_tmp = data.split("|");

    $data_sent = [];
    $.each($data_tmp, function (key, value) {
        $value_tmp = value.split("@");

        $form_data = (typeof $(document).find($value_tmp[1]) != "undefined" || $(document).find($value_tmp[1]) != null) ? $(document).find($value_tmp[1]) : '';
        if ($form_data) {
            $text = $value_tmp[0] + '=' + $form_data.val();
            $data_sent.push($text);
        }
    });
    if ($data_sent) {
        $data_sent = $data_sent.join('&');
        $url_tmp = url.split("?");
        $data_sent = ($url_tmp[1]) ? $data_sent + '&' + $url_tmp[1] : $data_sent;
        url = $url_tmp[0];
        url = url + '?' + $data_sent;
    }

    if (url) {
        $.ajax({
            url: url,
            method: "GET",
            success: function (hasil) {
                $value_tmp = hasil.content.split("@");
                $data_hasil = [];
                $.each($value_tmp, function (key, value) {
                    $data_hasil.push(value);
                });

                $target_tmp = target.split("|");
                $.each($target_tmp, function (key, value) {
                    $form_data = (typeof $(document).find(value) != "undefined" || $(document).find(value) != null) ? $(document).find(value) : '';
                    if ($form_data) {
                        if ($data_hasil[key]) {
                            $form_data.val($data_hasil[key]);
                        }
                    }
                });
            },
        });
    }
    return false;
});

$(document).delegate(".show-modal", "click", function () {
    let dataId = $(this).attr('id');
    let buttonModal = '#button' + dataId;
    $(document).find(buttonModal).click();
});

let urutanDataTable = 0;
var variableDataTable = {};
$(document).find('.data-table').each(function () {

    let data_scrollx = (typeof $(this).attr("data-scrollx") != "undefined" || $(this).attr("data-scrollx") != null) ? $(this).attr("data-scrollx") : '';
    if (data_scrollx) {
        data_scrollx = true;
    } else {
        data_scrollx = false;
    }

    let tag = 'data-search' + urutanDataTable;
    $(this).attr('id', tag);

    variableDataTable[urutanDataTable] = $(document).find("#" + tag).DataTable({
        ordering: false,
        scrollX: data_scrollx,
        paging: false,
        info: false,
        searching: true,
        language: { search: "" },
        fnDrawCallback: function () {
            $(".dataTables_filter").find("input[type='search']").attr("hidden", true);
        },
    });

    let parent = $(this).parents('.bagan-data-table');
    parent.find('.search-data-table').attr('data-search', urutanDataTable);

    urutanDataTable++;

    $(this).attr("style", "width: 100%");
});

$(document).delegate(".search-data-table", "change keyup paste", function () {
    let target = $(this).attr('data-search');
    variableDataTable[target].search($(this).val()).draw();
});

$(document).delegate(".set-value-data-table", "click", function () {
    let parent = $(this).parents('.modal');
    let targets = $(this).attr('data-target');
    let target = targets.split("|");

    let nilais = $(this).attr('data-value');
    let nilai = nilais.split("|");

    $.each(target, function (key, value) {
        let forms = value.split("@");
        let form = forms[0];
        let type = (forms[1]) ? forms[1] : 'val';

        if (nilai[key]) {
            if (type == 'val') {
                $(document).find(form).val(nilai[key]);
            }
        }
    });
    parent.find(".btn-close").click();
    return false;
});


$(document).find('.bagan-data-table-cus').each(function () {
    $parent = $(this);
    $url_base = (typeof $(this).data("url") != "undefined" || $(this).data("url") != null) ? $(this).data("url") : '';
    $name_base = 'data-table-cus';
    $bagan_tabel = (typeof $(this).find('.' + $name_base) != "undefined" || $(this).find('.' + $name_base) != null) ? $(this).find('.' + $name_base) : '';
    if ($url_base && $bagan_tabel.length) {
        $test = $bagan_tabel.DataTable({
            'processing': true,
            'serverSide': true,
            'ordering': false,
            "paginate": true,
            'info': false,
            'serverMethod': 'get',
            'searching': true,
            fnDrawCallback: function () {
                $parent.find(".dataTables_length").hide();
                $parent.find(".dataTables_filter").hide();
            },
            'ajax': {
                'url': $url_base,
                'method': "GET",
            },
            'createdRow': function (row, data, index) {
                $(row).find('.money').inputmask({ alias: "money" });
            }
        });

        $bagan_tabel.css("width", "100%");
    }
});

$(document).find('.bagan-data-table-cus').delegate("form", "submit", function (event) {
    $parent = $(this).parents('.bagan-data-table-cus');
    $name_base = 'data-table-cus';
    $bagan_tabel = (typeof $parent.find('.' + $name_base) != "undefined" || $parent.find('.' + $name_base) != null) ? $parent.find('.' + $name_base) : '';
    if ($bagan_tabel.length) {

        $form = $(this);
        $data_form = $form.serializeArray();

        $data_sent = JSON.stringify($data_form);
        $bagan_tabel = $bagan_tabel.DataTable();
        $bagan_tabel.search($data_sent).draw();
    }

    return false;
});


$(".checkboxDaftar").on("change", function () {
    let toggle = false;
    if ($(this).prop("checked")) {
        toggle = true;
    } else if (!$(this).prop("checked")) {
        toggle = false;
    }
    let parent = $(this).parent().parent()
    let tarif = parent.children().eq(4).text()
    let kode = parent.children().eq(1).text()

    if (toggle) {
        $("#response").append(`
            <input type="text" class="form-control" parent-id="${kode}" value="${kode}">
        `);
    } else if (!toggle) {
        $(
            `input[parent-id="${kode}"]`
        ).remove();
    }
});

$(document).delegate(".modal-remote,.modal-remote-delete,.modal-remote-confir", "click", function (event) {
    let meClass = $(this).attr("class");
    let modalRow = (typeof $(this).attr("data-modal-row") != "undefined" || $(this).attr("data-modal-row") != null) ? $(this).attr("data-modal-row") : '';
    let modal_bagan = (typeof $(this).attr('data-modal-bagan') != "undefined" || $(this).attr('data-modal-bagan') != null) ? $(this).attr('data-modal-bagan') : '#showModalCustome';
    let modal_confirm = (typeof $(this).attr('data-modal-confirm') != "undefined" || $(this).attr('data-modal-confirm') != null) ? $(this).attr('data-modal-confirm') : false;
    let button_modal = "buttonModalCustome";

    if (meClass.indexOf('modal-remote-delete') != -1) {
        meClass = 'modal-remote-delete';
    } else if (meClass.indexOf('modal-remote-confir') != -1) {
        meClass = 'modal-remote-confir';
    } else {
        meClass = 'modal-remote';
    }

    if (meClass == 'modal-remote-delete') {
        modal_bagan = (typeof $(this).attr('data-modal-bagan') != "undefined" || $(this).attr('data-modal-bagan') != null) ? $(this).attr('data-modal-bagan') : '#showModalCustomeDelete';
        button_modal = "buttonModalCustomeDelete";
    }

    if (meClass == 'modal-remote-confir') {
        modal_bagan = (typeof $(this).attr('data-modal-bagan') != "undefined" || $(this).attr('data-modal-bagan') != null) ? $(this).attr('data-modal-bagan') : '#showModalCustomeConfir';
        button_modal = "buttonModalCustomeConfir";
    }

    let modal_confir_message = (typeof $(this).attr('data-confirm-message') != "undefined" || $(this).attr('data-confirm-message') != null) ? $(this).attr('data-confirm-message') : 'informasi';
    let modal_title = (typeof $(this).attr('data-modal-title') != "undefined" || $(this).attr('data-modal-title') != null) ? $(this).attr('data-modal-title') : 'informasi';
    let modal_width = (typeof $(this).attr('data-modal-width') != "undefined" || $(this).attr('data-modal-width') != null) ? $(this).attr('data-modal-width') : null;
    let modal_backdrop = (typeof $(this).attr('data-modal-backdrop') != "undefined" || $(this).attr('data-modal-backdrop') != null) ? $(this).attr('data-modal-backdrop') : 'static';
    let ajax_href = ($(this).attr('href') != "undefined" || $(this).attr('href') != null) ? $(this).attr('href') : null;
    let ajax_src = (typeof $(this).attr('data-modal-src') != "undefined" || $(this).attr('data-modal-src') != null) ? $(this).attr('data-modal-src') : null;
    let ajax_url = '';
    if (ajax_href) {
        ajax_url = ajax_href;
    } else if (ajax_src) {
        ajax_url = ajax_src;
    }

    let type_ajax = (typeof $(this).attr('data-modal-method') != "undefined" || $(this).attr('data-modal-method') != null) ? $(this).attr('data-modal-method') : 'GET';
    let data_sent = (typeof $(this).attr('data-modal-key') != "undefined" || $(this).attr('data-modal-key') != null) ? $(this).attr('data-modal-key') : '';

    let meModal = $(document).find(modal_bagan);
    if (meModal && ajax_url) {
        meModal.find('#title').html(modal_title);
        // if (modal_width) {
        //     meModal.find('.modal-dialog').css('max-width', modal_width);
        // }

        if (modal_width) {
            if (window.innerWidth < 768) {
                meModal.find('.modal-dialog')
                .addClass('mt-4') 
                .css('max-width', '95%');

            } else {
                meModal.find('.modal-dialog').css('max-width', modal_width);
            }
        }


        if (modal_backdrop) {
            meModal.prop('data-bs-backdrop', true);
            meModal.attr('data-bs-backdrop', modal_backdrop);
        }

        if (meClass == 'modal-remote-delete' || meClass == 'modal-remote-confir') {
            meModal.find('.modal-body').html(modal_confir_message);
            action_delete_data(meModal, button_modal, ajax_url, type_ajax, data_sent);
        } else {
            action_set_data(meModal, button_modal, ajax_url, type_ajax, data_sent, modalRow);
        }
        return false;
    }
});

function action_set_data($meModal, $button_modal, $ajax_url, $type_ajax, $data_sent, $modal_row) {
    $.ajax({
        url: $ajax_url,
        type: $type_ajax,
        async: false,
        cache: false,
        ContentType: 'application/json',
        data: { data_sent: $data_sent },
        beforeSend: function () {

        },
        success: function (data) {
            if (data.html) {
                $meModal.find('.modal-body').html(data.html);
                $meModal.find('.modal-body').attr("data-modal-row", $modal_row);
                document.getElementById($button_modal).click();
            }
        },
        error: function (data) {
            alert("Maaf data tidak dapat di tampilkan");
        },
        complete: function () {

        }
    });
}

function action_delete_data($meModal, $button_modal, $ajax_url, $type_ajax, $data_sent) {
    let url = $ajax_url;
    if ($data_sent) {
        url = $ajax_url + '?data_sent=' + $data_sent;
    }
    $meModal.find('form').attr('action', url);
    document.getElementById($button_modal).click();
}

$(document).delegate(".modal-remote-data", "click", function (event) {
    let modal_bagan = (typeof $(this).attr('data-modal-bagan') != "undefined" || $(this).attr('data-modal-bagan') != null) ? $(this).attr('data-modal-bagan') : '#showModalData';
    let button_modal = (typeof $(this).attr('data-modal-button') != "undefined" || $(this).attr('data-modal-button') != null) ? $(this).attr('data-modal-button') : '#buttonModalData';
    let modal_title = (typeof $(this).attr('data-modal-title') != "undefined" || $(this).attr('data-modal-title') != null) ? $(this).attr('data-modal-title') : '';
    let modal_width = (typeof $(this).attr('data-modal-width') != "undefined" || $(this).attr('data-modal-width') != null) ? $(this).attr('data-modal-width') : null;
    let modal_backdrop = (typeof $(this).attr('data-modal-backdrop') != "undefined" || $(this).attr('data-modal-backdrop') != null) ? $(this).attr('data-modal-backdrop') : 'static';
    let modal_form_pencarian = (typeof $(this).attr('data-modal-pencarian') != "undefined" || $(this).attr('data-modal-pencarian') != null) ? $(this).attr('data-modal-pencarian') : false;

    let ajax_href = ($(this).attr('href') != "undefined" || $(this).attr('href') != null) ? $(this).attr('href') : null;
    let ajax_src = (typeof $(this).attr('data-modal-src') != "undefined" || $(this).attr('data-modal-src') != null) ? $(this).attr('data-modal-src') : null;
    let ajax_url = '';
    if (ajax_href) {
        ajax_url = ajax_href;
    } else if (ajax_src) {
        ajax_url = ajax_src;
    }

    let data_table_checkbox = ($(this).attr("data-table-checkbox") != "undefined" || $(this).attr("data-table-checkbox") != null) ? $(this).attr("data-table-checkbox") : null;
    let data_table_page_modal = ($(this).attr("data-table-page") != "undefined" || $(this).attr("data-table-page") != null) ? $(this).attr("data-table-page") : null;

    let type_ajax = (typeof $(this).attr('data-modal-method') != "undefined" || $(this).attr('data-modal-method') != null) ? $(this).attr('data-modal-method') : 'GET';
    let data_sent = (typeof $(this).attr('data-modal-key') != "undefined" || $(this).attr('data-modal-key') != null) ? $(this).attr('data-modal-key') : '';
    let data_change_row = (typeof $(this).attr('data-modal-action-change') != "undefined" || $(this).attr('data-modal-action-change') != null) ? $(this).attr('data-modal-action-change') : '';

    let data_form = (typeof $(this).attr('data-modal-key-with-form') != "undefined" || $(this).attr('data-modal-key-with-form') != null) ? $(this).attr('data-modal-key-with-form') : '';
    data_form = data_form.split("|");

    $hasil_value=[];
    $.each(data_form, function (key, value) {
        if (value.indexOf('@') == -1) {
            $check = (typeof $(document).find(value) != "undefined" || $(document).find(value) != null) ? $(document).find(value) : '';
            $hasil_check='';
            if ($check.length == 1) {
                if ($check[0].value !== undefined) {
                    $hasil_check=$check.val();
                } else {
                    $hasil_check=$check.html();
                }
            }
            $hasil_value.push($hasil_check);
        }else{
            $modul = value.split("@");
            $parent_modul=$modul[0];
            $value_modul=$modul[1];

            $check_parent = (typeof $(document).find($parent_modul) != "undefined" || $(document).find($parent_modul) != null) ? $(document).find($parent_modul) : '';
            $hasil_check='';
            if ($check_parent.length == 1) {
                $check = (typeof $($check_parent).find($value_modul) != "undefined" || $($check_parent).find($value_modul) != null) ? $($check_parent).find($value_modul) : '';
                $hasil_check='';
                if ($check.length == 1) {
                    if ($check[0].value !== undefined) {
                        $hasil_check=$check.val();
                    } else {
                        $hasil_check=$check.html();
                    }
                }
            }
            $hasil_value.push($hasil_check);
        }
    });
    $hasil_value=$hasil_value.join("@");
    if($hasil_value){
        data_sent=$hasil_value;
    }

    let meModal = $(document).find(modal_bagan);
    if (meModal) {
        meModal.find('.modal-body').html('');

        if (modal_title) {
            meModal.find('#title').html(modal_title);
        }
        if (modal_form_pencarian == 'true') {
            meModal.find('#form-pencarian').show();
        } else {
            meModal.find('#form-pencarian').hide();
        }

        if (modal_width) {
            meModal.find('.modal-dialog').css('max-width', modal_width);
        }

        if (modal_backdrop) {
            meModal.prop('data-bs-backdrop', true);
            meModal.attr('data-bs-backdrop', modal_backdrop);
        }

        if (ajax_url) {
            $.ajax({
                url: ajax_url,
                type: type_ajax,
                async: false,
                cache: false,
                ContentType: 'application/json',
                data: { data_sent: data_sent },
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.html) {
                        meModal.find('.modal-body').html(data.html);

                        if (data_change_row) {
                            $row_terpilih = meModal.find('.pil');
                            if ($row_terpilih) {

                                let data_ = data_change_row.split("@");
                                $.each(data_, function (key, value) {
                                    $tmp_ = value.split("=");
                                    $ac = ($tmp_[0]) ? $tmp_[0] : '';
                                    $nil = ($tmp_[1]) ? $tmp_[1] : '';
                                    if ($ac) {
                                        if ($ac != 'function') {
                                            $row_terpilih.attr($ac, $nil);
                                        } else {
                                            $get_type = $nil.charAt(0);

                                            $type = 'class';
                                            if ($get_type == '#') {
                                                $type = 'id';
                                                $custome_type = $row_terpilih.prop('id');
                                            } else {
                                                $type = 'class';
                                                $custome_type = $row_terpilih.prop('class');
                                            }
                                            $nil = $nil.replace($get_type, "", "g");
                                            $custome_type += ' ' + $nil;

                                            $row_terpilih.attr($type, $custome_type);
                                        }
                                    }
                                });


                                let last_thead_th = meModal.find('.modal-body').find("table thead tr th:last-child");
                                let last_tbody_td = meModal.find('.modal-body').find("table tbody tr td:last-child");
                                // let itemData = meModal.find('.modal-body').find("table tbody tr td:last-child a");

                                if (data_table_checkbox == "true" && last_tbody_td.length > 1) {
                                    last_thead_th.after(`<th scope="col" class="py-4 text-center"><input class="form-check-input" id="check-all" type="checkbox" value=""></th>`)
                                    last_tbody_td.after(`<td class="py-3 text-center"><input class="form-check-input checklist" type="checkbox"></td>`)
                                    meModal.find('.modal-body').find("table").after(`<button class="btn btn-primary" id="apply-checklist">Data Terpilih</button>`)
                                }

                                $data_tabel = meModal.find('.modal-body').find('.data-table-2');
                                if ($data_tabel) {

                                    let table_leng_page = 10;
                                    let table_leng_page_tmp = (typeof $data_tabel.attr('data-table-page') != "undefined" || $data_tabel.attr('data-table-page') != null) ? $data_tabel.attr('data-table-page') : null;

                                    if (table_leng_page_tmp) {
                                        table_leng_page = table_leng_page_tmp;
                                    }

                                    if (data_table_page_modal) {
                                        table_leng_page = data_table_page_modal;
                                    }

                                    $data_tabel.DataTable({
                                        ordering: false,
                                        paging: true,
                                        pageLength: table_leng_page,
                                        info: false,
                                        searching: true,
                                        language: { search: "" },
                                        fnDrawCallback: function () {
                                            $(".dataTables_filter").find("input[type='search']").attr("hidden", true);
                                        },
                                    });

                                    $data_tabel.css("width", "100%");
                                    meModal.find('.dataTables_length').hide();

                                }
                            }
                        }
                    }
                },
                error: function (data) {
                    meModal.find('.modal-body').html("<h3>Maaf data tidak dapat di tampilkan</h3>");
                },
                complete: function () {

                }
            });
        }
        $(document).find(button_modal).trigger("click");
    }
    return false;
});

$(document).delegate("#showModalData .search-data-table-2", "change keyup paste", function () {
    let parent = $(this).parents('#showModalData');
    parent.find('.data-table-2').dataTable().fnFilter($(this).val());
});

$(document).delegate(".get-data-list-from-modal", "click", function (event) {
    let closeModal = (typeof $(this).attr('data-btn-close') != "undefined" || $(this).attr('data-btn-close') != null) ? $(this).attr('data-btn-close') : '';
    if (closeModal) {
        closeModal = (typeof $(document).find(closeModal) != "undefined" || $(document).find(closeModal) != null) ? $(document).find(closeModal) : '';
    }
    let target = (typeof $(this).attr('data-target') != "undefined" || $(this).attr('data-target') != null) ? $(this).attr('data-target') : '';
    if (target) {
        target = (typeof $(document).find(target) != "undefined" || $(document).find(target) != null) ? $(document).find(target) : '';
    }
    let target2 = (typeof $(this).attr('data-target2') != "undefined" || $(this).attr('data-target2') != null) ? $(this).attr('data-target2') : '';
    if (target2) {
        target2 = (typeof $(document).find(target2) != "undefined" || $(document).find(target2) != null) ? $(document).find(target2) : '';
    }
    let showError = (typeof $(this).attr('data-show-error') != "undefined" || $(this).attr('data-show-error') != null) ? $(this).attr('data-show-error') : '';
    if (showError) {
        showError = (typeof $(document).find(showError) != "undefined" || $(document).find(showError) != null) ? $(document).find(showError) : '';
    }

    if (closeModal && target) {

        let item = (typeof $(this).attr('data-item') != "undefined" || $(this).attr('data-item') != null) ? $(this).attr('data-item') : '';

        target.removeClass("border border-danger");
        target.addClass("border border-default");
        if (showError) {
            showError.text("");
        }

        let data = [];
        if (target.val()) {
            var data_ = target.val().split(", ");
            $.each(data_, function (key, value) {
                data.push(value);
            });
        }

        data.push(item);

        //menghilangkan duplicate data
        data = data.filter(function (c, pos) {
            return data.indexOf(c) == pos;
        })

        let hasil = data.join(', ').replace("\n", "", "g");

        target.val(hasil);
        if (target2) {
            $type = (target2.val() ? 'val' : 'html');
            if ($type == 'val') {
                target2.val(hasil);
            } else {
                target2.html(hasil);
            }
        }

        $(document).find(closeModal).trigger("click");
    }

    return false;
});

$(document).delegate(".set-data-list-from-modal", "click", function (event) {
    let closeModal = (typeof $(this).attr('data-btn-close') != "undefined" || $(this).attr('data-btn-close') != null) ? $(this).attr('data-btn-close') : '';
    if (closeModal) {
        closeModal = (typeof $(document).find(closeModal) != "undefined" || $(document).find(closeModal) != null) ? $(document).find(closeModal) : '';
    }
    let target_tmp = (typeof $(this).attr('data-target') != "undefined" || $(this).attr('data-target') != null) ? $(this).attr('data-target') : '';
    let showError = (typeof $(this).attr('data-show-error') != "undefined" || $(this).attr('data-show-error') != null) ? $(this).attr('data-show-error') : '';
    if (showError) {
        showError = (typeof $(document).find(showError) != "undefined" || $(document).find(showError) != null) ? $(document).find(showError) : '';
    }

    if (closeModal && target_tmp) {

        let hasil = (typeof $(this).attr('data-item') != "undefined" || $(this).attr('data-item') != null) ? $(this).attr('data-item') : '';
        hasil = hasil.split("@");

        target_tmp = target_tmp.split("|");

        if (hasil) {
            $.each(hasil, function (key, value) {
                let target = '';
                if (target_tmp[key]) {
                    target = (typeof $(document).find(target_tmp[key]) != "undefined" || $(document).find(target_tmp[key]) != null) ? $(document).find(target_tmp[key]) : '';
                    if (target.length == 1) {
                        if (target[0].value !== undefined) {
                            target.val(value);
                        } else {
                            target.html(value);
                        }
                    }
                }
            });
        }

        $(document).find(closeModal).trigger("click");
    }

    return false;
});

function validate_form($parent=null) {
    $jumlah = 0;
    $position = '';
    $bagan=$('[required]');
    if($parent){
        if ($parent.length == 1) {
            $bagan=$parent.find('[required]');
        }
    }

    $bagan.each(function (idx, elem) {
        $parent = $(elem).parents('.bagan_form');
        $target = $parent.find('.message');

        if ($target) {
            if (!$(elem).val()) {
                $confir_message = (typeof $target.attr('data-message') != "undefined" || $target.attr('data-message') != null) ? $target.attr('data-message') : 'Form ini tidak boleh kosong';
                $color_message = (typeof $target.attr('data-class-color') != "undefined" || $target.attr('data-class-color') != null) ? $target.attr('data-class-color') : 'text-danger';

                $html = "<span class='" + $color_message + "'>" + $confir_message + "</span>";
                $target.html($html);

                if ($jumlah == 0) {
                    $position = ($target.offset().top) - $parent.height();
                }

                $jumlah++;
            } else {
                $parent.find('.message').html('');
            }
        }
    });

    if ($jumlah > 0) {
        window.scrollTo(0, $position);
        return false;
    } else {
        return true;
    }
}

$(document).delegate(".bagan_form #reset_input", "click", function (event) {
    let parent = $(this).parents('.bagan_form');
    parent.find(':input').each(function () {
        $(this).val('');
    });

    return false;
});

$(document).delegate(".validate_submit", "click", function (event) {
    let parent = $(this).parents('form');
    $check = validate_form(parent);
    if ($check == false) {
        return false;
    }
});

$(document).delegate(".submit_confirmasi", "click", function (event) {

    $check = validate_form();
    if ($check == true) {
        $action = (typeof $(this).attr('data-action') != "undefined" || $(this).attr('data-action') != null) ? $(this).attr('data-action') : '';
        if ($action) {
            var data_ = $action.split("@");
            $target = '';
            $action_target = '';
            $.each(data_, function (key, value) {
                $tmp_ = value.split("=");
                $ac = ($tmp_[0]) ? $tmp_[0] : '';
                $nil = ($tmp_[1]) ? $tmp_[1] : '';
                if ($ac == 'target') {
                    if ($nil) {
                        $target = $nil;
                    }
                }

                if ($ac == 'action') {
                    if ($nil) {
                        $action_target = $nil;
                    }
                }
            });

            if ($target && $action_target) {
                $(document).find($target).trigger($action_target);
            }

            return false;
        } else {
            return true;
        }
    }
    return false;

});

$(document).delegate(".form-duplicate", "change keyup", function (event) {
    $target = (typeof $(this).attr('data-target') != "undefined" || $(this).attr('data-target') != null) ? $(this).attr('data-target') : '';

    if ($target) {
        $hasil = $(this).val();

        let data = $target.split("|");
        $.each(data, function (key, value) {
            $me = (typeof $(document).find(value) != "undefined" || $(document).find(value) != null) ? $(document).find(value) : '';
            if ($me) {
                $taq = 'input';
                if ($me.prop("tagName")) {
                    $taq = $me.prop("tagName").toLowerCase();
                }

                if ($taq == 'input' || $taq == 'selected') {
                    $type = 'val';
                } else {
                    $type = 'html';
                }

                if ($type == 'val') {
                    $me.val($hasil);
                } else {
                    $me.html($hasil);
                }

            }
        });
    }
    return false;
});

$(document).delegate(".checklist", "change", function () {
    let value = $(this).parents().eq(1).find(".text-primary").text()
    if (this.checked) {
        $(this).val(value)
    } else {
        $(this).val("")
    }
})

$(document).delegate("#check-all", "change", function () {

    let status_check = 0;
    if ($(this).is(":checked")) {
        status_check = 1;
    }

    let table = $(this).parents('.data-table-2');
    if (table.length >= 1) {
        table = table.DataTable();
        table.rows().every(function (rowIdx) {
            $get_checklist = $(this.node()).find('.checklist');
            if (status_check == 1) {
                $get_checklist.prop('checked', true);
            } else {
                $get_checklist.prop('checked', false);
            }
        });
    }
    return false;
})

$(document).delegate("#apply-checklist", "click", function () {
    let parent = $(this).parents().children().eq(0)
    let table = parent.find(".checklist")

    let data_table = $(document).find('.data-table-2');

    let target = (typeof parent.find(".pil").attr("data-target") != "undefined" || parent.find(".pil").attr("data-target") != null) ? parent.find(".pil").attr("data-target") : '';
    if (target) {
        target = (typeof $(document).find(target) != "undefined" || $(document).find(target) != null) ? $(document).find(target) : '';
    }
    let target2 = (typeof parent.find(".pil").attr("data-target2") != "undefined" || parent.find(".pil").attr("data-target2") != null) ? parent.find(".pil").attr("data-target2") : '';
    if (target2) {
        target2 = (typeof $(document).find(target2) != "undefined" || $(document).find(target2) != null) ? $(document).find(target2) : '';
    }

    let closeBtn = parent.find(".pil").attr("data-btn-close")
    if (closeBtn) {
        closeBtn = (typeof parent.find(".pil").attr("data-btn-close") != "undefined" || parent.find(".pil").attr("data-btn-close") != null) ? parent.find(".pil").attr("data-btn-close") : '';
    }

    let titleHeader = parent.find("th").eq(2).text()

    if (closeBtn && target) {

        target.removeClass("border border-danger");
        target.addClass("border border-default");

        let dataValue = [];
        let validate = []
        if (target.val()) {
            var data_ = $(target).val().split(", ");
            $.each(data_, function (key, value) {
                dataValue.push(value);
            });
        }

        if (data_table.length >= 1) {
            data_table = data_table.DataTable();
            data_table.rows().every(function (rowIdx) {
                $get_checklist = $(this.node()).find('.checklist');
                if ($get_checklist.is(":checked")) {
                    let parent_tr = $get_checklist.parents('tr');
                    let item = parent_tr.find('.pil').data('item');
                    dataValue.push(item)
                    validate.push(item)
                }
            });
        }

        dataValue = dataValue.filter(function (c, pos) {
            return dataValue.indexOf(c) == pos;
        })

        let hasil = dataValue.join(', ').replace("\n", "", "g");
        if (validate.length !== 0) {
            $(target).val(hasil);

            if (target2) {
                $type = (target2.val() ? 'val' : 'html');
                if ($type == 'val') {
                    target2.val(hasil);
                } else {
                    target2.html(hasil);
                }
            }

            $(closeBtn).trigger("click")
        } else {
            $(this).after(`<span class="mx-2 text-danger">Silakan ceklis minimal satu <b>${titleHeader}</b> yang ingin dipilih</span>`)
        }
    }

    return false;

})

$(document).find('.money').each(function () {
    $(this).inputmask({ alias: "money" });
});

$(document).find('.format_ip_address').each(function () {
    $(this).inputmask({ alias: "ip",greedy: false });
});

function collapse_cus($me) {
    $check = $me.attr("aria-expanded");

    $icon_open = (typeof $me.find('#collapse-open') != "undefined" || $me.find('#collapse-open') != null) ? $me.find('#collapse-open') : '';
    $icon_closed = (typeof $me.find('#collapse-closed') != "undefined" || $me.find('#collapse-closed') != null) ? $me.find('#collapse-closed') : '';
    if ($icon_open.length && $icon_closed.length) {
        $icon_open.hide();
        $icon_closed.hide();
        if ($check == 'true') {
            $icon_closed.show();
        } else {
            $icon_open.show();
        }
    }
}

function collapse_cus($me) {
    let $check = $me.attr("aria-expanded");

    let $icon_open = $me.find('#collapse-open, #collapse-open-semua');
    let $icon_closed = $me.find('#collapse-closed, #collapse-closed-semua');

    if ($icon_open.length && $icon_closed.length) {
        $icon_open.hide();
        $icon_closed.hide();

        if ($check === 'true') {
            $icon_closed.show();
        } else {
            $icon_open.show();
        }
    }
}

$(document).delegate(".collapse-cus", "click", function () {
    collapse_cus($(this));
});

$(document).find('.collapse-cus').each(function () {
    collapse_cus($(this));
});
// program to generate random strings

// declare all characters

function generateString(length) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = ' ';
    const charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }

    return result;
};