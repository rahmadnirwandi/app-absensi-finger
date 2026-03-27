function set_data(){
    $data_sent={};
    
    $target=$(document).find('#item_list_terpilih');
    if($($target).html()){
        $data_sent=JSON.parse($target.html());
    }

    $check_checked=0;
    tabel=(typeof $(document).find('.data-table-cus') != "undefined" || $(document).find('.data-table-cus') != null) ? $(document).find('.data-table-cus') : '' ;
    if(tabel.length){
        tabel = tabel.DataTable();

        tabel.rows().every( function ( key ) {
            $data_me=tabel.rows(key).data();
            $data_me=$data_me[0];
            
            $form_checked_b = $( this.node() ).find('.checked_b');
            $kode_data=(typeof $form_checked_b.data("kode") != "undefined" || $form_checked_b.data("kode") != null) ? $form_checked_b.data("kode") : '' ;
            $data_json=(typeof $form_checked_b.data("me") != "undefined" || $form_checked_b.data("me") != null) ? $form_checked_b.data("me") : '' ;
            if($kode_data){ 
                $me_data={};
                
                $check_valid=0;
                if($form_checked_b.length){
                    if($form_checked_b.is(':checked')){
                        $me_data['value']=1;
                        $check_valid=1;
                        $check_checked++;
                    }else{
                        if($data_sent[$kode_data]){
                            delete $data_sent[$kode_data];
                        }
                    }
                }

                if($check_valid>=1){
                    if(!jQuery.isEmptyObject($data_json)){
                        // $data_json=JSON.stringify($data_json);
                        // $data_me.splice(0,$data_me.length-2);
                        
                        $me_data['data']=$data_json;
                        $data_sent[$kode_data]=$me_data;
                    }
                }
            }
        });
    }
    $return={};
    if($check_checked<=0){
        $target.html('{}');
    }
    
    if(!jQuery.isEmptyObject($data_sent)){
        $return=JSON.stringify($data_sent);
        $target.html($return);
    }
}

function get_terpilih(){
    $form_data=$(document).find('#item_list_terpilih');
    if($form_data.html()){
        $tamplate='';
        $item=[];
        $item=JSON.parse($form_data.html());

        $parent=$(document).find('#data-terpilih');
        $tabel=$parent.find('table');
        if($tabel.find('tbody').length){
            $.each( $item, function( key, value ) {
                if(value.data){
                    $tamplate+='<tr data-kode="'+key+'">';
                        $.each( value.data, function( key1, value1 ) {
                            $tamplate+="<td>"+decode_html_raw(value1)+"</td>";
                        });
                        $tamplate+="<td> <a href='#' class='btn btn-kecil btn-danger del_item'> <i class='fa-solid fa-trash'></i> </a> </td>";
                    $tamplate+='</tr>';
                }
            });

            $tabel.find('tbody').html('');
            $tabel.find('tbody').html($tamplate);

            $(document).find(".money").inputmask({ alias : "money" });
        }


        tabel=(typeof $(document).find('.data-table-cus') != "undefined" || $(document).find('.data-table-cus') != null) ? $(document).find('.data-table-cus') : '' ;
        if(tabel.length){
            tabel = tabel.DataTable();
            tabel.rows().every( function ( key ) {
                $form_checked_b = $( this.node() ).find('.checked_b');
        
                if($form_checked_b.length){
                    $kode_data=(typeof $form_checked_b.data("kode") != "undefined" || $form_checked_b.data("kode") != null) ? $form_checked_b.data("kode") : '' ;
                    if($kode_data){
                        if($item[$kode_data]){
                            $form_checked_b.prop('checked',true);
                        }else{
                            $form_checked_b.prop('checked',false);
                        }
                    }
                }
            });
        }
    }

    check_data_terpilih();
}



$(document).delegate(".checked_b", "change", function(event) {
    set_data();
    get_terpilih();
});

$(document).find('.data-table-cus').on( 'draw.dt', function () {
    get_terpilih();
});


$(document).delegate(".del_item", "click", function(event) {
    $parent=$(this).parents('tr');
    $kode_data=(typeof $parent.data("kode") != "undefined" || $parent.data("kode") != null) ? $parent.data("kode") : '' ;
    if($kode_data){
        $data_sent={};
        $target=$(document).find('#item_list_terpilih');
        if($($target).html()){
            $data_sent=JSON.parse($target.html());
        }

        if($data_sent[$kode_data]){
            delete $data_sent[$kode_data];
            $data_sent=JSON.stringify($data_sent);
            $target.html('');
            $target.html($data_sent);

            get_terpilih();
        }
    }

    return false;
});

$(document).delegate(".checked_all", "change", function(event) {
    $checked=0;
    if($(this).is(':checked')){
        $checked=1;
    }

    $(document).find('.checked_b').each(function () {
        if($checked==1){
            $(this).prop('checked',true);
        }else{
            $(this).prop('checked',false);
        }
    });

    setTimeout(function() {
        set_data();
        get_terpilih();
    }, 700);
    
});


function check_data_terpilih(){
    $form_data=$(document).find('#item_list_terpilih');
    $(document).find('#btn_save').hide();
    if($form_data.html()){
        $tamplate='';
        $item=[];
        $item=JSON.parse($form_data.html());
        
        if(!jQuery.isEmptyObject($item)){
            $(document).find('#btn_save').show();
            return 1;
        }else{
            $(document).find('#btn_save').hide();
            return 0;
        }
    }
}

$(document).ready(function() {
    // $check=check_data_terpilih();
    // if($check){
    //     set_data();
    //     get_terpilih();
    // }
});