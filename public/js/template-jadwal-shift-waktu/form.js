function get_terpilih(){
    $form_data=$(document).find('#list_tgl_terpilih');
    $jenis_jadwal=$(document).find("#id_jenis_jadwal").val();
    if($form_data.html() && $jenis_jadwal ){
        $tamplate='';
        $item=[];
        $item=JSON.parse($form_data.html());

        if($item[$jenis_jadwal]){
            $data_hari=$item[$jenis_jadwal].item;
            if($data_hari){
                $(document).find(".checkbox_hari").each(function () {

                    if( $.inArray($(this).val(), $data_hari) !== -1 ) {
                        $(this).prop( 'checked', true );
                    }else{
                        $(this).prop( 'checked', false );
                    }
                });
            }
        }else{
            $(document).find(".checkbox_hari").prop( 'checked', false );
        }
    }
}

function set_data(){
    $data_sent={};

    $target=$(document).find('#list_tgl_terpilih');
    if($($target).html()){
        $data_sent=JSON.parse($target.html());
    }
    
    $jenis_jadwal=$(document).find("#id_jenis_jadwal").val();
    $type_jadwal=$(document).find("#type_jadwal").val();

    $me_data=[];
    $me_data.push({
        type_jadwal : '',
        item : []
    });
    $(document).find(".checkbox_hari").each(function () {
        if($(this).is(":checked")){
            if($(this).val()){
                $me_data[0].item.push($(this).val());
                $me_data[0].type_jadwal=$type_jadwal;
            }
        }
    });
    $data_sent[$jenis_jadwal]=$me_data[0];

    if(!jQuery.isEmptyObject($data_sent)){
        $return=JSON.stringify($data_sent);
        $target.html($return);
    }
}

$(document).find(".radio_pil").on("change", function (e) {
    e.preventDefault();
    $parent = $(this).parents('td');
    if($(this).is(":checked")){
        $(document).find("#list_hari").show();
        $(document).find("#list_hari_non_change").hide();
        $(document).find("#id_jenis_jadwal").val($(this).val());
        $type_jadwal = (typeof $(this).data("type-jadwal") != "undefined" || $(this).data("type-jadwal") != null) ? $(this).data("type-jadwal") : '';
        $(document).find("#type_jadwal").val($type_jadwal);
        
        $(document).find(".radio_pil").parents('td').css('background',"none");
        $(document).find(".radio_pil").parents('td').css('color',"#555");
        $parent.css('background',"#555");
        $parent.css('color',"#fff");
        
        $(document).find("#title_jadwal").html($parent.find('.radio_pil_nama').val());
        
        get_terpilih();
        return false;
    }else{
        $parent.css('background',"none");
        return false;
    }
    return false;
});

$(document).find(".checkbox_hari").on("change", function (e) {
    e.preventDefault();
    set_data();
    return false;
});