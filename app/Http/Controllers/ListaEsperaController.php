<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ListaEspera;
use App\Models\Servico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ListaEsperaController extends Controller
{
    public function index(): View
    {
        return view('lista-espera.index', [
            'listas' => ListaEspera::query()
                ->with(['cliente:id,nome_completo,email,telefone', 'servico:id,nome'])
                ->orderByRaw("CASE status WHEN 'aguardando' THEN 0 WHEN 'avisado' THEN 1 ELSE 2 END")
                ->orderBy('created_at')
                ->paginate(10),
            'clientes' => Cliente::query()->orderBy('nome_completo')->get(['id', 'nome_completo']),
            'servicos' => Servico::query()->where('ativo', true)->orderBy('nome')->get(['id', 'nome']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'servico_id' => ['required', 'integer', Rule::exists('servicos', 'id')->where('ativo', true)],
            'data_inicio' => ['required', 'date', 'after_or_equal:today'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'horario' => ['nullable', 'date_format:H:i'],
        ]);

        $cliente = Cliente::findOrFail($validated['cliente_id']);
        $temEmail = $cliente->receber_notificacoes_email && filled($cliente->email);
        $temWhatsApp = $cliente->receber_notificacoes_whatsapp && filled($cliente->telefone);

        if (! $temEmail && ! $temWhatsApp) {
            return back()->withInput()->withErrors([
                'cliente_id' => 'O cliente precisa ter e-mail ou WhatsApp cadastrado e aceitar notificações.',
            ]);
        }

        ListaEspera::create($validated + ['status' => 'aguardando']);

        return redirect()->route('lista-espera.index')
            ->with('sucesso', 'Cliente adicionado à lista de espera.');
    }

    public function update(Request $request, ListaEspera $listaEspera): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:atendido,cancelado'],
        ]);

        $listaEspera->update($validated);

        return redirect()->route('lista-espera.index')
            ->with('sucesso', 'Registro da lista de espera atualizado.');
    }

    public function destroy(ListaEspera $listaEspera): RedirectResponse
    {
        $listaEspera->delete();

        return redirect()->route('lista-espera.index')
            ->with('sucesso', 'Registro removido da lista de espera.');
    }
}
