<x-guest-layout>
    <div class="mb-4 text-sm text-slate-600 dark:text-slate-400">
        {{ __('Obrigado por se cadastrar! Antes de começar, verifique seu endereço de e-mail clicando no link que acabamos de enviar para você. Se você não recebeu o e-mail, teremos o prazer de lhe enviar outro.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-emerald-600 dark:text-emerald-400">
            {{ __('Um novo link de verificação foi enviado para o endereço de e-mail informado no cadastro.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Reenviar E-mail de Verificação') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                {{ __('Sair') }}
            </button>
        </form>
    </div>
</x-guest-layout>
