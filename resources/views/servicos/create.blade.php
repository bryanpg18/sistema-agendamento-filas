@extends('layouts.app')

@section('titulo', 'Novo Serviço')

@section('conteudo')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <p class="text-sm text-slate-400 dark:text-slate-500"><a href="{{ route('servicos.index') }}" class="hover:underline">Serviços</a> &gt; Novo serviço</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Novo serviço</h1>
        </div>

        @include('servicos.partials.form', ['action' => route('servicos.store'), 'method' => 'POST', 'servico' => null])
    </div>
@endsection
