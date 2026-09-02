<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
            <div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col md:flex-row">

                <div class="w-full md:w-1/2 bg-teal-700 text-white p-10 flex flex-col justify-center items-center text-center">
                    <div class="mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold mb-2">Bem-vindo!</h1>
                    <h2 class="text-lg font-semibold mb-4">Sistema de Organização<br>de Filas e Agendamentos</h2>
                    <p class="text-teal-100 text-sm max-w-xs">
                        Gerencie clientes, agendamentos e atendimentos de forma simples e eficiente.
                    </p>
                </div>

                <div class="w-full md:w-1/2 p-10 flex flex-col justify-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Acesse sua conta</h2>
                    <p class="text-gray-500 mb-6">Entre para continuar</p>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Usuário</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-600 focus:ring-teal-600 py-2.5 px-4">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-600 focus:ring-teal-600 py-2.5 px-4">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-600">
                            <label for="remember_me" class="ms-2 text-sm text-gray-600">Lembrar de mim</label>
                        </div>

                        <button type="submit"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2.5 rounded-lg transition">
                            Entrar
                        </button>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a href="{{ route('password.request') }}" class="text-sm text-teal-600 hover:underline">
                                    Esqueceu sua senha?
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
