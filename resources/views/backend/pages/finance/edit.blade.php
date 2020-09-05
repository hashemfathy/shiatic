@extends('backend.welcome')
@section('title')
Pahment item
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
    <payment-item-form :item="{{json_encode($payment_item)}}" />
</default-container>
@endsection