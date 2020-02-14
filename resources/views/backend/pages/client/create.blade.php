@extends('backend.welcome')
@section('title')
Clients
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
    <client-form />
</default-container>
@endsection