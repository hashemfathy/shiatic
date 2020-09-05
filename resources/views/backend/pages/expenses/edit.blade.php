@extends('backend.welcome')
@section('title')
expenses
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
    <expenses-form :payment_value="{{json_encode($payment_value)}}" :payment_items="{{json_encode($payment_items)}}" />
</default-container>
@endsection