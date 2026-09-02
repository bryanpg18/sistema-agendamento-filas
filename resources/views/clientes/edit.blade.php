@extends('layouts.app')

@section('titulo', 'Editar Cliente')

@section('conteudo')
    @php
        $formatCpf = function (?string $value): string {
            $digits = preg_replace('/\D+/', '', $value ?? '') ?? '';

            if (strlen($digits) !== 11) {
                return $value ?? '';
            }

            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits) ?? $digits;
        };

        $formatTelefone = function (?string $value): string {
            $digits = preg_replace('/\D+/', '', $value ?? '') ?? '';

            if (strlen($digits) === 11) {
                return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $digits) ?? $digits;
            }

            if (strlen($digits) === 10) {
                return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $digits) ?? $digits;
            }

            return $value ?? '';
        };
    @endphp

    <div class="mb-6">
        <p class="text-sm text-gray-400">
            <a href="{{ route('clientes.index') }}" class="hover:underline">Clientes</a> &gt; Editar Cliente
        </p>
        <h1 class="text-2xl font-bold text-gray-800 mt-1">Editar Cliente</h1>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('clientes.update', $cliente) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm text-gray-600 mb-1">Nome completo</label>
                <input type="text" name="nome_completo" value="{{ old('nome_completo', $cliente->nome_completo) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">
                @error('nome_completo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">CPF</label>
                      <input type="text" name="cpf" value="{{ $formatCpf(old('cpf', $cliente->cpf)) }}"
                          x-data x-mask="999.999.999-99"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">
                    @error('cpf') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Telefone</label>
                      <input type="text" name="telefone" value="{{ $formatTelefone(old('telefone', $cliente->telefone)) }}"
                          x-data x-mask="(99) 99999-9999"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">
                    @error('telefone') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $cliente->email) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">
                @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Data de nascimento</label>
                <input type="date" name="data_nascimento"
                       value="{{ old('data_nascimento', $cliente->data_nascimento?->format('Y-m-d')) }}"
                       class="w-full max-w-xs border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Observações</label>
                <textarea name="observacoes" rows="3"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">{{ old('observacoes', $cliente->observacoes) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('clientes.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-teal-800 hover:bg-teal-900 text-white px-5 py-2 rounded-lg text-sm font-medium">
                    Salvar
                </button>
            </div>
        </form>
    </div>
@endsection