@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="card mx-auto p-4"
         style="max-width: 450px;">

        <h3 class="text-center">
            Verificação de segurança
        </h3>

        <p class="text-center">
            Digite o código enviado para seu e-mail.
        </p>

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('2fa.verificar') }}">

            @csrf

            <input
                type="text"
                name="codigo"
                class="form-control text-center"
                placeholder="000000"
                maxlength="6"
                inputmode="numeric"
                required
            >

            <button type="submit"
                    class="btn btn-warning w-100 mt-3">
                Verificar código
            </button>

        </form>

    </div>

</div>

@endsection
