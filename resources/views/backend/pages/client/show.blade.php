@extends('backend.welcome')
@section('title')
{{$client->name}}
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
    <client-show :specialists="{{json_encode($specialists)}}" :client="{{json_encode($client)}}" />
</default-container>
@endsection