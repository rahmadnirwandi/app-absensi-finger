@if(!empty($breadcrumbs))
    <style>
        .breadcrumbs ul li a{
            margin-top:5px;
            padding: 13px 0px 13px 28px;
            text-align: left;
            min-width: auto;
        }
        @media (max-width: 480px) {
            .d {
                font-size: 10px;
                margin: 0 -15px;
                padding: 0 15px;
            }
        }
    </style>
    <div>
        <div class="breadcrumbs mt-3">
            <ul>
                @foreach($breadcrumbs as $key => $item)
                    <?php 
                        $item=(object)$item;
                        $class="";
                        $seq=Request::segment(1);
                        $get_url=parse_url($item->url);
                        $get_url=!empty($get_url['path']) ? $get_url['path'] : '';
                        
                        if (strpos( trim(strtolower( $get_url )), trim(strtolower( $seq )) ) !== false) {
                            $class="breadcrumbs-active";
                        }
                        if(!empty($item->active)){
                            $class="breadcrumbs-active";
                        }
                    ?>
                    <li class="hover-pointer"><a  href="{{ $item->url }}" class="{{ $class }}">{{ $item->title }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
@endif