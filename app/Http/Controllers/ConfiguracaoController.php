<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfiguracaoController extends Controller
{
    /**
     * Show the configuration settings form.
     */
    public function index(): View
    {
        $config = Configuracao::obter();

        return view('configuracoes.index', compact('config'));
    }

    /**
     * Update the configuration settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome_estabelecimento' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'horario_abertura' => ['required', 'date_format:H:i'],
            'horario_fechamento' => ['required', 'date_format:H:i', 'after:horario_abertura'],
            'duracao_padrao' => ['required', 'integer', 'min:5', 'max:480'],
        ], [
            'nome_estabelecimento.required' => 'O nome do estabelecimento é obrigatório.',
            'telefone.required' => 'O telefone de contato é obrigatório.',
            'email.required' => 'O e-mail de contato é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'horario_fechamento.after' => 'O horário de fechamento deve ser posterior ao horário de abertura.',
            'horario_abertura.date_format' => 'Informe o horário de abertura no formato HH:MM.',
            'horario_fechamento.date_format' => 'Informe o horário de fechamento no formato HH:MM.',
            'duracao_padrao.min' => 'A duração padrão mínima é de 5 minutos.',
            'duracao_padrao.max' => 'A duração padrão máxima é de 480 minutos.',
        ]);

        $config = Configuracao::first();

        if ($config) {
            $config->update($validated);
        } else {
            Configuracao::create($validated);
        }

        return redirect()->route('configuracoes.index')
            ->with('sucesso', 'Configurações salvas com sucesso!');
    }
}
