@extends('layouts.app')

@section('titulo', 'Editar Serviço')

@section('conteudo')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <p class="text-sm text-slate-400 dark:text-slate-500"><a href="{{ route('servicos.index') }}" class="hover:underline">Serviços</a> &gt; Editar serviço</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Editar serviço</h1>
        </div>

        @include('servicos.partials.form', ['action' => route('servicos.update', $servico), 'method' => 'PUT', 'servico' => $servico])
    </div>
@endsection
