@extends('layouts.painel')

@section('titulo', 'Dashboard')

@section('conteudo')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-gray-600">{{ __("Você está conectado!") }}</p>
    </div>
@endsection