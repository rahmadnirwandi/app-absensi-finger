@extends('layouts.master')

@section('title-header', $title)

<style>
    .validate_submit:hover {
        background-color: #008BFF !important;
    }

</style>

@section('breadcrumbs')
    @include('layouts.breadcrumbs')
@endsection


@section('content')
    @include('password.form')

@endsection
