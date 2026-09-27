@extends('layouts.app')

@section('title', 'Registrar atendimento')

@section('content_header')
    <h1>Registrar atendimento</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('atendimentos.store') }}" enctype="multipart/form-data">
        @include('atendimentos._form')
    </form>
@stop
