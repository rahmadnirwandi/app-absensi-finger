$('#proses').click(function(e) {
    e.preventDefault();

    $url=$(document).find('#url_get_mesin').val();
    $(this).prop('disabled', true);

    $.ajax({
        type: "GET",
        url:$url,
        success: function(hasil){
            $bagan=$(document).find('#progress-item');
            $bagan.html(hasil.html);
            setTimeout(function(){
                proses_data();
            }, 500);
        },
        error: function(xhr, status, error){
            console.log(error);
        }
    });

    return false;
});

function proses_data(){
    $get_data=$(document).find('#progress-item').find('tbody').find('tr[ data-status="0" ]');
    
    $tanggal_start=$(document).find('#tgl_start').val();
    $tanggal_max=$(document).find('#tgl_end').val();
    
    if($get_data.length){
        $get_data.each( function(idx, elem) {
            $data=$(this).find('.data');
            $key=$data.find('.id_mesin').val();
            $status=$(this).attr('data-status');
            if($key && $status==0){
                setTimeout(function(){
                    import_data($key,1,0,0,$tanggal_start,$tanggal_start,$tanggal_max);
                }, 500);
                return false;
            }
        });
    }else{
        setTimeout(function(){
            alert('Proses Selesai');
            $('#proses').prop('disabled', false);
        }, 400);
        return false;
    }

    return false;
}

function import_data($key,$urut_proses,$start_query,$end_query,$tgl_first,$tgl_proses,$tgl_max){
    $url=$(document).find('#url_proses').val();
    
    $.ajax({
        type: "GET",
        url:$url,
        data:{
            key:$key,
            tanggal_first:$tgl_first,
            tanggal_proses_start:$tgl_proses,
            tanggal_max:$tgl_max,
            urut_proses:$urut_proses,
            start_query:$start_query,
            end_query:$end_query,
        },
        success: function(hasil){
            $tampil_console={
                'callback':hasil.hasil,
                'mesin':hasil.id_mesin,
                'proses_ke':hasil.no_proses,
                // 'limit_ke':hasil.start_query+' - '+hasil.end_query,
                'tgl_proses':hasil.tanggal_proses_tmp,
                'message':hasil.message
            }
            console.log($tampil_console);

            if(hasil.hasil==504){
                alert('Proses Terhenti, System Error');
                $('#proses').prop('disabled', false);
                return false;
            }

            $get_html=$(document).find('#progress-item').find('#item_'+$key);
            $progress=$get_html.find('.progress-bar');
            $progress.css('width',hasil.progres_bar+'%');
            $get_html.find('#bar-progress-label').html(hasil.progres_bar+'%');
            $progress.css('background-color','#0d6efd');
            if(hasil.status_mesin==1){
                $get_html.find('.status_mesin').html(hasil.message);
                $progress.css('background-color','#f20862');
            }
            if(hasil.proses_selesai==0){
                $get_html.find('.status_mesin').html('Proses');
                setTimeout(function(){
                    import_data($key,hasil.no_proses,hasil.start_query,hasil.end_query,$tgl_first,hasil.tanggal_proses_start,$tgl_max);
                }, 500);
            }else{
                $get_html.find('.status_mesin').html('Selesai');
                if(hasil.status_mesin==1){
                    $get_html.find('.status_mesin').html(hasil.message);
                }
                $get_html.attr('data-status',1);

                setTimeout(function(){
                    proses_data();
                }, 500);
                return false;
            }
        },
        error: function(xhr, status, error){
            $('#proses').prop('disabled', false);
            console.log(error);
        }
    });

    return false;
}