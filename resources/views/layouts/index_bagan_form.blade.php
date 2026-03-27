@extends('layouts.master')

@section('title-header', $title)

@if(!empty($breadcrumbs))
    <?php 
        $breadcrumbs_plus=['title'=>!empty($title_plus) ? $title_plus : 'Form','url'=>"#",'active'=>1];
        array_push($breadcrumbs,$breadcrumbs_plus);
    ?>
    @section('breadcrumbs')
        @include('layouts.breadcrumbs')
    @endsection
@endif

@section('content')
    @if(!empty($url_back))
        @php 
            $url_back = route($url_back);
            if(!empty($paramater_url_back)){
                $url_back=$url_back.'?'.$paramater_url_back;
            }
        @endphp
        <div class="text-primary mb-3">
            <a href="{{ url($url_back) }}" style="color:#f0ca3b">
                <i class="fa-solid fa-square-caret-left"></i><span class="mx-2">Kembali Ke form sebelumnya</span>
            </a>
        </div>
        <hr>
    @endif

    @if(!empty($bagan_form))
        {!! $bagan_form !!}
    @endif
@endsection