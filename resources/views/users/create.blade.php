@extends('layouts.app')

@section('title', 'Novo usuário')

@section('content_header')
    <h1>Novo usuário</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('users.store') }}">
        @include('users._form')
    </form>
@stop
