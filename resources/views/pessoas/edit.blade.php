@extends('adminlte::page')

@section('title', 'Editar pessoa')

@section('content_header')
    <h1>Editar pessoa</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('pessoas.update', $pessoa) }}">
        @method('PUT')
        @include('pessoas._form')
    </form>
@stop
