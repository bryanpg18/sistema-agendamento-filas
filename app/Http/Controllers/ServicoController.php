<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServicoController extends Controller
{
    public function index(): View
    {
        return view('servicos.index', [
            'servicos' => Servico::orderBy('nome')->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('servicos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Servico::create($this->validatedServicoData($request));

        return redirect()->route('servicos.index')
            ->with('sucesso', 'Serviço cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Servico $servico): View
    {
        return view('servicos.edit', compact('servico'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Servico $servico): RedirectResponse
    {
        $servico->update($this->validatedServicoData($request, $servico));

        return redirect()->route('servicos.index')
            ->with('sucesso', 'Serviço atualizado com sucesso!');
    }

    private function validatedServicoData(Request $request, ?Servico $servico = null): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('servicos', 'nome')->ignore($servico)],
            'duracao_minutos' => ['required', 'integer', 'min:5', 'max:480'],
            'preco' => ['nullable', 'decimal:0,2', 'min:0'],
            'ativo' => ['required', 'boolean'],
        ], [
            'nome.required' => 'Informe o nome do serviço.',
            'nome.unique' => 'Já existe um serviço com este nome.',
            'duracao_minutos.required' => 'Informe a duração do serviço.',
            'duracao_minutos.min' => 'A duração mínima é de 5 minutos.',
            'duracao_minutos.max' => 'A duração máxima é de 480 minutos.',
            'preco.decimal' => 'Informe um preço válido com até duas casas decimais.',
        ]);
    }
}
