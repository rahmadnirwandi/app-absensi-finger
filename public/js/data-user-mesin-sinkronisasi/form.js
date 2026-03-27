$.fn.editable.defaults.mode = 'inline';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('.form_text_change').editable({
    mode: 'popup',
    success: function(response, newValue) {
        // if(response.status == 'error') {
        //     return 'Data tidak berhasil di ubah';
        // }
        // return 'Data berhasil di ubah';
        location.reload();
    }
});