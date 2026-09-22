<div class="rounded-xl border border-slate-200/80 bg-white p-6 shadow-sm transition-colors dark:border-slate-800 dark:bg-slate-900">
    <h2 class="mb-4 font-semibold text-slate-700 dark:text-slate-200">Dados do serviço</h2>

    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div>
            <label for="nome" class="mb-1 block text-sm text-slate-600 dark:text-slate-300">Nome</label>
            <input id="nome" type="text" name="nome" value="{{ old('nome', $servico?->nome) }}"
                   class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder-slate-500"
                   placeholder="Ex.: Corte de cabelo">
            @error('nome') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="duracao_minutos" class="mb-1 block text-sm text-slate-600 dark:text-slate-300">Duração (minutos)</label>
                <input id="duracao_minutos" type="number" name="duracao_minutos" min="5" max="480" value="{{ old('duracao_minutos', $servico?->duracao_minutos) }}"
                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="30">
                @error('duracao_minutos') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="preco" class="mb-1 block text-sm text-slate-600 dark:text-slate-300">Preço (opcional)</label>
                <input id="preco" type="number" name="preco" min="0" step="0.01" value="{{ old('preco', $servico?->preco) }}"
                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="0,00">
                @error('preco') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="ativo" class="mb-1 block text-sm text-slate-600 dark:text-slate-300">Status</label>
            <select id="ativo" name="ativo" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                <option value="1" @selected(old('ativo', $servico?->ativo ?? true))>Ativo</option>
                <option value="0" @selected(! old('ativo', $servico?->ativo ?? true))>Inativo</option>
            </select>
            <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Serviços inativos não aparecem em novos agendamentos.</p>
            @error('ativo') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('servicos.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Cancelar</a>
            <button type="submit" class="rounded-lg bg-teal-700 px-5 py-2 text-sm font-medium text-white transition hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700">Salvar</button>
        </div>
    </form>
</div>
