$.fn.editable.defaults.mode = 'inline';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('.pil_jadwal').editable({
    mode: 'popup',
    success: function(response, newValue) {
        // if(response[0] == 'error') return response[1]; //msg will be shown in editable form
        $(document).find('#loading_black_screen').show();
        
        setTimeout(function() {
            $(document).find('#loading_black_screen').hide();
            window.location.reload();
        }, 800);
    }
});