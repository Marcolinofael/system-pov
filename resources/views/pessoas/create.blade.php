@extends('adminlte::page')

@section('title', 'Nova pessoa')

@section('content_header')
    <h1>Nova pessoa</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('pessoas.store') }}">
        @include('pessoas._form')
    </form>
@stop
