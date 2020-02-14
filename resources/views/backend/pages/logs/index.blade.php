@extends('backend.welcome')
@section('title')
Log
@endsection
@section('content')
<default-container>
    <div class="card-body table-responsive">
        <log-list />
    </div>
</default-container>
@endsection