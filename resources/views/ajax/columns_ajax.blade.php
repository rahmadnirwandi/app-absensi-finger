
@if(!empty($table))
    <table class="table data-table-2 border">
        <thead>
            <tr>
                @foreach($table['header']['title'] as $key => $value)
                    <?php 
                        $parameter=!empty($table['header']['parameter'][$key]) ? $table['header']['parameter'][$key] : '';
                    ?>
                    <th {{ $parameter }}>{{ $value }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if(!empty($list_data))
                @foreach($list_data as $item)
                <tr>
                    <?php 
                        $key_me=0;
                    ?>
                    @foreach($item as $value)
                        <?php 
                            $parameter_columns=!empty($table['columns_parameter'][$key_me]) ? $table['columns_parameter'][$key_me] : '';
                        ?>
                        @if( is_array($value) )
                            <td {{ $parameter_columns }}> <a href='#' class="pil text-primary hover-pointer" data-item="{{ $value['data-item'] }}" >{{ $value['value'] }}</a></td>
                        @else
                            <td {{ $parameter_columns }} >{{ $value }}</td>
                        @endif
                        <?php $key_me++;  ?>
                    @endforeach
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
@endif
