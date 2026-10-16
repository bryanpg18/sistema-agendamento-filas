@extends('layouts.app')

@section('titulo', 'Perfis de acesso')

@section('conteudo')
<div class="space-y-6" x-data="{ adicionarUsuario: {{ $errors->hasAny(['name', 'email', 'password', 'role']) ? 'true' : 'false' }} }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Perfis de acesso</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Defina quais usuários podem administrar o sistema.</p>
        </div>
        <button type="button" @click="adicionarUsuario = !adicionarUsuario" class="inline-flex items-center gap-2 rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700">
            <span aria-hidden="true" class="text-lg leading-none">+</span>
            Adicionar usuário
        </button>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-950/50 dark:text-rose-300">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <section class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-1 font-semibold text-slate-800 dark:text-slate-100">Administrador</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Acessa configurações, serviços, bloqueios, relatórios e perfis.</p>
        </section>
        <section class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-1 font-semibold text-slate-800 dark:text-slate-100">Atendente</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Acessa clientes, agendamentos, horários, atendimentos e histórico.</p>
        </section>
    </div>

    <section id="adicionar-usuario" x-show="adicionarUsuario" class="rounded-xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-100 px-5 py-4 font-medium text-slate-800 dark:border-slate-800 dark:text-slate-100">Adicionar novo usuário</div>
        <form method="POST" action="{{ route('usuarios-acesso.store') }}" class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2">
            @csrf
            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nome</label>
                <input id="name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">E-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Senha inicial</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Confirmar senha</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
            </div>
            <div>
                <label for="novo-role" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Perfil</label>
                <select id="novo-role" name="role" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    <option value="atendente" @selected(old('role', 'atendente') === 'atendente')>Atendente</option>
                    <option value="admin" @selected(old('role') === 'admin')>Administrador</option>
                </select>
                @error('role')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-end">
                <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700">Criar usuário</button>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 md:col-span-2">Um e-mail de verificação será enviado ao novo usuário.</p>
        </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Usuário</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">E-mail</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Perfil</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-slate-500 dark:text-slate-400">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-3 text-sm font-medium text-slate-800 dark:text-slate-200">{{ $usuario->name }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $usuario->email }}</td>
                            <td class="px-5 py-3">
                                <form id="perfil-{{ $usuario->id }}" method="POST" action="{{ route('usuarios-acesso.update', $usuario) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" aria-label="Perfil de {{ $usuario->name }}"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                        <option value="admin" @selected($usuario->role === 'admin')>Administrador</option>
                                        <option value="atendente" @selected($usuario->role === 'atendente')>Atendente</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <button type="submit" form="perfil-{{ $usuario->id }}"
                                        class="rounded-lg bg-teal-700 px-3 py-2 text-xs font-medium text-white transition hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700">
                                    Salvar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-400 dark:text-slate-500">Nenhum usuário cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $usuarios->links() }}</div>
    </section>
</div>
@endsection
