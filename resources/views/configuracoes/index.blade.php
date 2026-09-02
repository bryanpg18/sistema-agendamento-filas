@extends('layouts.app')

@section('conteudo')
<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Configurações</h1>
        <p class="text-sm text-slate-500">Dados do estabelecimento e preferências do sistema</p>
    </div>

        @if (session('sucesso'))
            <div class="bg-emerald-50 text-emerald-700 text-sm px-4 py-3 rounded-lg mb-4">
                {{ session('sucesso') }}
            </div>
        @endif

        <form method="POST" action="{{ Route::has('configuracoes.update') ? route('configuracoes.update') : '#' }}"
              class="bg-white rounded-xl border border-slate-100 p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm text-slate-600 mb-1">Nome do estabelecimento</label>
                <input type="text" name="nome_estabelecimento" value="{{ old('nome_estabelecimento', $config->nome_estabelecimento ?? '') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Telefone</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $config->telefone ?? '') }}"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">E-mail de contato</label>
                    <input type="email" name="email" value="{{ old('email', $config->email ?? '') }}"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Abertura</label>
                    <input type="time" name="horario_abertura" value="{{ old('horario_abertura', $config->horario_abertura ?? '08:00') }}"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Fechamento</label>
                    <input type="time" name="horario_fechamento" value="{{ old('horario_fechamento', $config->horario_fechamento ?? '18:00') }}"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                </div>
            </div>

            <div>
                <label class="block text-sm text-slate-600 mb-1">Duração padrão do atendimento (minutos)</label>
                <input type="number" name="duracao_padrao" value="{{ old('duracao_padrao', $config->duracao_padrao ?? 30) }}"
                       class="w-40 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium px-5 py-2.5 rounded-lg">
                    Salvar alterações
                </button>
            </div>
        </form>
</div>
@endsection
