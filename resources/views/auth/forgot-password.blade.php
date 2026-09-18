<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 dark:text-slate-100 bg-slate-100 dark:bg-slate-950 antialiased transition-colors duration-150">
        <div class="min-h-screen flex items-center justify-center px-4 py-8">
            <div class="w-full max-w-4xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row transition-colors">

                <div class="w-full md:w-1/2 bg-teal-700 dark:bg-teal-800 text-white p-10 flex flex-col justify-center items-center text-center">
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

                <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-center">
                    <div class="flex items-center justify-between mb-1">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Recuperar senha</h2>
                        <button type="button"
                                x-data="{
                                    dark: document.documentElement.classList.contains('dark'),
                                    toggleTheme() {
                                        this.dark = !this.dark;
                                        if (this.dark) {
                                            document.documentElement.classList.add('dark');
                                            localStorage.setItem('theme', 'dark');
                                        } else {
                                            document.documentElement.classList.remove('dark');
                                            localStorage.setItem('theme', 'light');
                                        }
                                    }
                                }"
                                @click="toggleTheme()"
                                class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                title="Alternar tema">
                            <svg x-show="!dark" class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>
                            <svg x-show="dark" style="display: none;" class="w-5 h-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
                        </button>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 mb-6 text-sm">
                        Esqueceu sua senha? Sem problema. Informe seu email e enviaremos um link para você criar uma nova.
                    </p>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">E-mail</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 shadow-sm focus:border-teal-600 focus:ring-teal-600 py-2.5 px-4 text-sm">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <button type="submit"
                            class="w-full bg-teal-700 hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700 text-white font-semibold py-2.5 rounded-lg transition text-sm">
                            Enviar link de recuperação
                        </button>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="text-sm text-teal-700 dark:text-teal-400 hover:underline">
                                Voltar para o login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
