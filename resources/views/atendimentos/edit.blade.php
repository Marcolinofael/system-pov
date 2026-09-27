@extends('layouts.app')

@section('title', 'Editar atendimento')

@section('content_header')
    <h1>Editar atendimento</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('atendimentos.update', $atendimento) }}">
        @method('PUT')
        @include('atendimentos._form')
    </form>
@stop
