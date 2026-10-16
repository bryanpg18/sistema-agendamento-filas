<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'busca' => ['nullable', 'string', 'max:100'],
        ]);

        $clientes = Cliente::when($filtros['busca'] ?? null, function ($query, $busca) {
            $documento = preg_replace('/\D+/', '', $busca) ?? '';

            $query->where(function ($query) use ($busca, $documento) {
                $query->where('nome_completo', 'like', "%{$busca}%")
                    ->orWhere('email', 'like', "%{$busca}%");

                if ($documento !== '') {
                    $query->orWhere('cpf', 'like', "%{$documento}%")
                        ->orWhere('telefone', 'like', "%{$documento}%");
                }
            });
        })
            ->orderBy('nome_completo')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatedClienteData($request);

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('sucesso', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('agendamentos.servico');

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $this->validatedClienteData($request, $cliente);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('sucesso', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->agendamentos()->exists()) {
            return redirect()->route('clientes.index')
                ->with('erro', 'Não é possível remover um cliente que possui agendamentos no histórico.');
        }

        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('sucesso', 'Cliente removido com sucesso!');
    }

    private function validatedClienteData(Request $request, ?Cliente $cliente = null): array
    {
        $data = $request->only([
            'nome_completo',
            'cpf',
            'telefone',
            'email',
            'data_nascimento',
            'observacoes',
            'receber_notificacoes_email',
            'receber_notificacoes_whatsapp',
        ]);

        $data['receber_notificacoes_email'] = $request->boolean('receber_notificacoes_email');
        $data['receber_notificacoes_whatsapp'] = $request->boolean('receber_notificacoes_whatsapp');
        $data['cpf'] = $this->normalizeDigits($data['cpf'] ?? null);
        $data['telefone'] = $this->normalizeDigits($data['telefone'] ?? null);

        $cpfRule = Rule::unique('clientes', 'cpf');

        if ($cliente !== null) {
            $cpfRule = $cpfRule->ignore($cliente->id);
        }

        return Validator::make($data, [
            'nome_completo' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'digits:11', $cpfRule],
            'telefone' => ['required', 'digits_between:10,11'],
            'email' => ['nullable', 'email', 'max:255'],
            'data_nascimento' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string'],
            'receber_notificacoes_email' => ['boolean'],
            'receber_notificacoes_whatsapp' => ['boolean'],
        ])->validate();
    }

    private function normalizeDigits(?string $value): string
    {
        return preg_replace('/\D+/', '', $value ?? '') ?? '';
    }
}
