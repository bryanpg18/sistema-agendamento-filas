@extends('layouts.app')

@section('conteudo')
<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Configurações</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Dados do estabelecimento e preferências do sistema</p>
    </div>

    @if (session('sucesso'))
        <div class="bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm px-4 py-3 rounded-lg mb-4">
            {{ session('sucesso') }}
        </div>
    @endif

    <form method="POST" action="{{ route('configuracoes.update') }}"
          class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-5 shadow-sm transition-colors">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                Nome do estabelecimento <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nome_estabelecimento" value="{{ old('nome_estabelecimento', $config->nome_estabelecimento ?? '') }}"
                   required
                   class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600 @error('nome_estabelecimento') border-rose-400 @enderror">
            @error('nome_estabelecimento')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                    Telefone <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="telefone" value="{{ old('telefone', $config->telefone ?? '') }}"
                       x-data x-mask:dynamic="$input.replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-9999'"
                       placeholder="(00) 00000-0000"
                       required
                       class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600 @error('telefone') border-rose-400 @enderror">
                @error('telefone')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                    E-mail de contato <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $config->email ?? '') }}"
                       required
                       class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600 @error('email') border-rose-400 @enderror">
                @error('email')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                    Abertura <span class="text-rose-500">*</span>
                </label>
                <input type="time" name="horario_abertura" value="{{ old('horario_abertura', $config->horario_abertura ?? '08:00') }}"
                       required
                       class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600 @error('horario_abertura') border-rose-400 @enderror">
                @error('horario_abertura')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                    Fechamento <span class="text-rose-500">*</span>
                </label>
                <input type="time" name="horario_fechamento" value="{{ old('horario_fechamento', $config->horario_fechamento ?? '18:00') }}"
                       required
                       class="w-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600 @error('horario_fechamento') border-rose-400 @enderror">
                @error('horario_fechamento')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                Duração padrão do atendimento (minutos) <span class="text-rose-500">*</span>
            </label>
            <input type="number" name="duracao_padrao" value="{{ old('duracao_padrao', $config->duracao_padrao ?? 30) }}" min="5" max="480"
                   required
                   class="w-40 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600 @error('duracao_padrao') border-rose-400 @enderror">
            @error('duracao_padrao')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="bg-teal-700 hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                Salvar alterações
            </button>
        </div>
    </form>
</div>
@endsection
