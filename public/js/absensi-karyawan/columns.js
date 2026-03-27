function get_list_data(page)
{
    $url_data = typeof $(document).find("#url_data") != "undefined" || $(document).find("#url_data") != null ? $(document).find("#url_data") : "";
    $list_data = typeof $(document).find("#list_data") != "undefined" || $(document).find("#list_data") != null ? $(document).find("#list_data") : '';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        url:$url_data.val(),
        data:{ 
            action:'get_list_data',
            page:page,
            list_data:$list_data.html()
        },
        success:function(data){
            $('#list_columns').html(data);
            $(document).find('#loading_black_screen').hide();
        }
    });
}

$(document).ready(function(){

    $(document).find('#loading_black_screen').show();
    get_list_data(1);

    $(document).on('click', '.pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        get_list_data(page);
    });
});