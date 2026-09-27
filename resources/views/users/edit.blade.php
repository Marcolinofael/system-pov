@extends('layouts.app')

@section('title', 'Editar usuário')

@section('content_header')
    <h1>Editar usuário</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('users.update', $user) }}">
        @method('PUT')
        @include('users._form')
    </form>
@stop
