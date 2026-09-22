@extends('layouts.app')

@section('titulo', 'Cliente')

@section('conteudo')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <p class="text-sm text-slate-400 dark:text-slate-500"><a href="{{ route('clientes.index') }}" class="hover:underline">Clientes</a> &gt; Detalhes</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ $cliente->nome_completo }}</h1>
            </div>
            <a href="{{ route('clientes.edit', $cliente) }}" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-800 dark:bg-teal-600">Editar</a>
        </div>
        <div class="rounded-xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div><dt class="text-sm text-slate-500 dark:text-slate-400">CPF</dt><dd class="font-medium text-slate-800 dark:text-slate-200">{{ $cliente->cpf_formatado }}</dd></div>
                <div><dt class="text-sm text-slate-500 dark:text-slate-400">Telefone</dt><dd class="font-medium text-slate-800 dark:text-slate-200">{{ $cliente->telefone_formatado }}</dd></div>
                <div><dt class="text-sm text-slate-500 dark:text-slate-400">E-mail</dt><dd class="font-medium text-slate-800 dark:text-slate-200">{{ $cliente->email ?? '-' }}</dd></div>
                <div><dt class="text-sm text-slate-500 dark:text-slate-400">Agendamentos</dt><dd class="font-medium text-slate-800 dark:text-slate-200">{{ $cliente->agendamentos->count() }}</dd></div>
            </dl>
        </div>
    </div>
@endsection
