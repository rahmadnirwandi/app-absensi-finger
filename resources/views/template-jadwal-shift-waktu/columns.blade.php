<style>
    .box_waktu{
        padding:10px;
    }
</style>
<hr>
<div>
    <div class="row d-flex justify-content-between">
        <div>
            <div style="overflow-x: auto; max-width: auto;">
                <table class="table border table-responsive-tablet" style="width:100%">
                    <thead>
                        <tr>
                            <th style='width:10%'>*</th>
                            <th>Uraian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $jml_periode=$get_template_shift_detail->jml_periode;
                            $type_periode = (new \App\Models\RefTemplateJadwalShiftDetail())->list_type_periode_system($get_template_shift_detail->type_periode);
                        
                            $tgl_start=new \DateTime($get_template_shift_detail->tgl_mulai);
                            $tgl_start_tmp=new \DateTime($get_template_shift_detail->tgl_mulai);

                            $rumus_tmp="+".$jml_periode." ".$type_periode;
                            $tgl_end = $tgl_start_tmp->modify($rumus_tmp);
                            
                            $tgl_start_text = $tgl_start->format('Y-m-d');
                            $tgl_end_text = $tgl_end->format('Y-m-d');

                            $looping_range_date = new DatePeriod($tgl_start, DateInterval::createFromDateString('1 day'), $tgl_end);

                            $data_shift=$grafik_data;

                            $list_data_besok=[];
                        ?>
                        @foreach($looping_range_date as $key_date => $valeu_date)
                            <?php 
                                $single_date=$valeu_date->format('D');
                                $number_date=$valeu_date->format('d');
                                $number_date=(int)$number_date;
                                $nm_hari=(new \App\Http\Traits\GlobalFunction)->hari($single_date);
                            ?>
                            <tr style='border-bottom:1px solid #ccc;'>
                                <td>
                                    <div>Hari Ke {{ $number_date }}</div>
                                </td>
                                <td>
                                    <?php 
                                        $list_shift=[];
                                        if(!empty($data_shift[$number_date])){
                                            foreach($data_shift[$number_date] as $key_list => $value){
                                                $text="";
                                                $value=(object)$value;
                                                if(!empty($value->type_jadwal)){
                                                    if($value->type_jadwal==2){
                                                        $text="
                                                            <span class='box_waktu' style='background:".$value->bgcolor."; display:block; width:20%;'>".$value->nm_shift."</span>
                                                        ";
                                                    }
                                                }else{
                                                    $text="
                                                        <span class='box_waktu' style='background:".$value->bgcolor.";'>"
                                                            .$value->nm_shift." : ".$value->start.' s/d '.$value->end.
                                                        "</span>
                                                    ";
                                                    
                                                    if(!empty($value->besok)){
                                                        $text="
                                                            <span class='box_waktu' style='background:".$value->bgcolor.";'>"
                                                                .$value->nm_shift." : ".$value->start.' s/d '.$value->end.' Esok hari'.
                                                            "</span>
                                                        ";

                                                        $text_next="";

                                                        // $text_next="
                                                        //     <span class='box_waktu' style='background:".$value->bgcolor.";'>"
                                                        //         .$value->nm_shift." : ".' s/d '.$value->end.
                                                        //     "</span>
                                                        // ";
                                                        $list_data_besok[$key_date+1][]=$text_next;
                                                    }
                                                }
                                                $list_shift[]=$text;
                                            }
                                        }
                                        if(!empty($list_shift)){
                                            $list_shift=implode(' ',$list_shift);
                                        }else{
                                            $list_shift='';
                                        }

                                        $list_shift_besok='';
                                        if(!empty($list_data_besok[$key_date])){
                                            $list_shift_besok=implode(' ',$list_data_besok[$key_date]);
                                        }
                                    ?>
                                    {!! $list_shift_besok !!}
                                    {!! $list_shift !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>