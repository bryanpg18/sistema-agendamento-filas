<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::latest()->paginate(10);

        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_completo'    => ['required', 'string', 'max:255'],
            'cpf'              => ['required', 'string', 'max:14', 'unique:clientes,cpf'],
            'telefone'         => ['required', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:255'],
            'data_nascimento'  => ['nullable', 'date'],
            'observacoes'      => ['nullable', 'string'],
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('status', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}