function set_data(){
    $data_sent={};

    $target=$(document).find('#jadwal_terpilih_sendiri');
    if($($target).html()){
        $data_sent=JSON.parse($target.html());
    }

    $me_data=[];
    $me_data.push({
        item : []
    });
    $(document).find(".pilih_jadwal").each(function () {
        if($(this).is(":checked")){
            $type_jadwal = (typeof $(this).attr('data-type-jadwal') != "undefined" || $(this).attr('data-type-jadwal') != null) ? $(this).attr('data-type-jadwal') : '';
            $kode=$type_jadwal+'@'+$(this).val();
            if($type_jadwal==1){
                if($(this).val()){
                    $me_data[0].item.push($kode);
                }
            }else if($type_jadwal==2){
                $me_data[0].item.push($kode);
            }
        }
    });
    $data_sent=$me_data[0];

    if(!jQuery.isEmptyObject($data_sent)){
        $return=JSON.stringify($data_sent);
        $target.html($return);
    }

    return 1;

}
function aturan_checked($this){

    $type_jadwal = (typeof $this.attr('data-type-jadwal') != "undefined" || $this.attr('data-type-jadwal') != null) ? $this.attr('data-type-jadwal') : '';

    if($type_jadwal==2){
        if($this.is(":checked")){
            $(document).find(".pilih_jadwal").prop( 'disabled', true );
            $(document).find(".pilih_jadwal").prop( 'checked', false );
            $this.prop( 'checked', true );
            $this.prop( 'disabled', false );
        }else{
            $(document).find(".pilih_jadwal").prop( 'disabled', false );
            $this.prop( 'checked', false );
        }
    }
}

function get_terpilih(){
    $form_data=$(document).find('#jadwal_terpilih_sendiri');
    $form_data_sistem=$(document).find('#jadwal_terpilih_sistem');
    if($form_data.html().length<=0){
        $form_data=$form_data_sistem;
    }
    
    if($form_data.html()){
        $item=[];
        $item=JSON.parse($form_data.html());
        
        $data_list=$item.item;

        $(document).find(".pilih_jadwal").each(function () {
            $type_jadwal = (typeof $(this).attr('data-type-jadwal') != "undefined" || $(this).attr('data-type-jadwal') != null) ? $(this).attr('data-type-jadwal') : '';
            $kode=$type_jadwal+'@'+$(this).val();

            if( $.inArray($kode, $data_list) !== -1 ) {
                $(this).prop( 'checked', true );
            }else{
                $(this).prop( 'checked', false );
            }

            aturan_checked($(this));
        });
        return false;
    }
}

$(document).find(".btn_change").on("click", function () {
    $data_sent = (typeof $(this).data("sent") != "undefined" || $(this).data("sent") != null) ? $(this).data("sent") : '';

    $modal_bagan = $(document).find('#showModalJks');
    if($modal_bagan.length>=1){
        $body=$modal_bagan.find('.modal-body');

        if($data_sent){
            $body.find('#tgl_ubah').val($data_sent.tgl);
            $body.find('#data_sent').val($data_sent.data_sent);
            $body.find('#params').val($data_sent.params);
            $body.find('#jadwal_terpilih_sistem').html($data_sent.list_sistem);
            $body.find('#jadwal_terpilih_sendiri').html($data_sent.list_sendiri);
            setTimeout(function() {
                get_terpilih();
            }, 800);

            setTimeout(function() {
                $(document).find('#buttonModalJks').click();
            }, 800);
            
        }
    }

    return false;
});

$(document).delegate(".pilih_jadwal", "change", function (e) {
    e.preventDefault();
    aturan_checked($(this));
    return false;
});

$(document).delegate("#proses_jadwal", "submit", function (e) {
    
    $hasil=set_data();
    $check_data=JSON.parse($(this).find('#jadwal_terpilih_sendiri').html());
    $check_data_sistem=JSON.parse($(this).find('#jadwal_terpilih_sistem').html());

    if($check_data.item.length<=0){
        $hasil==0;
        alert('Maaf data jadwal belum terpilih');
        e.preventDefault();
        return false;
    }

    // if(JSON.stringify($check_data.item)==JSON.stringify($check_data_sistem.item)){
    //     $hasil=0;
    //     $(document).find('#modal-closes').click();
    // }

    if($hasil==1){
        return true;    
    }
    e.preventDefault();
    return false;
});

$(document).find("#btn_reset").on("click", function (e) {
    e.preventDefault();
    $form_data=$(document).find('#jadwal_terpilih_sendiri');
    $form_data_sistem=$(document).find('#jadwal_terpilih_sistem');
    $form_data.html($form_data_sistem.html());
    get_terpilih();
    return false;
});
