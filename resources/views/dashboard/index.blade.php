@extends('layouts.app')
@section('content_title', 'Dashboard')
@section('content')
    <div class="card">
        <div class="card-body">
            Welcome to POS Application, <strong class="capitalize"> {{ auth()->user()->name }}</strong>
        </div>
    </div>
@endsection