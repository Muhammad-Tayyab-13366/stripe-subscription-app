@extends('layout.app')

@section('content')

<div class="container">
    <h2>Welcome {{ auth()->user()->name }}</h2>
</div>
@endsection
   
