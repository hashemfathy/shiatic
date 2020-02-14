@extends('backend.welcome')
@section('title')
Visit
@endsection
<style media="screen">
    .analytic {
        background-color: #59cdcb5c;
        padding: 14px;
        border: 2px solid white;
        text-align: center;
    }
</style>
@section('content')
<default-container>
    <visit-form :visit="{{json_encode($visit)}}" />
</default-container>
@endsection