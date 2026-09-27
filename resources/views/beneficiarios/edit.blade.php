@extends('layouts.app')

@section('title', 'Editar cadastro')

@section('content_header')
    <h1>Editar cadastro <small class="text-muted">{{ $beneficiario->nome_exibicao }}</small></h1>
@stop

@section('content')
    <form method="POST" action="{{ route('beneficiarios.update', $beneficiario) }}" novalidate>
        @method('PUT')
        @include('beneficiarios._form')
    </form>
@stop
