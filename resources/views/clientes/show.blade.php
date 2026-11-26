@extends('layouts.app')
@section('title', 'Cliente')
@section('content')

<link rel="stylesheet" href="{{ asset('css/form.css') }}">

    <div class="container mt-4">

    @include('partials.alerts')

     <form action="{{ route('clientes.show', $cliente) }}" method="POST" class="form-cliente">

        <h1>Visualizar Cliente</h1>

        @include('clientes._form')

    </form>

    </div>

@endsection
