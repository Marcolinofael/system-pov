@extends('layouts.app')

@section('title', 'Novo cadastro')

@section('content_header')
    <h1>Novo cadastro de beneficiário</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('beneficiarios.store') }}" novalidate>
        @include('beneficiarios._form')
    </form>
@stop
