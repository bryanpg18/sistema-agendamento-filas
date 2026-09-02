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
        $clientes = Cliente::when($request->busca, function ($query, $busca) {
            $query->where('nome_completo', 'like', "%{$busca}%")
                ->orWhere('cpf', 'like', "%{$busca}%");
        })
            ->orderBy('nome_completo')
            ->paginate(10);

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
        ]);

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
        ])->validate();
    }

    private function normalizeDigits(?string $value): string
    {
        return preg_replace('/\D+/', '', $value ?? '') ?? '';
    }
}
