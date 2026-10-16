<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UsuarioAcessoController extends Controller
{
    public function index(): View
    {
        return view('usuarios-acesso.index', [
            'usuarios' => User::query()->orderBy('name')->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'atendente'])],
        ]);

        $usuario = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $usuario->forceFill(['role' => $validated['role']])->save();

        event(new Registered($usuario));

        return redirect()->route('usuarios-acesso.index')
            ->with('sucesso', 'Usuário adicionado. Um e-mail de verificação foi enviado para ele.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'atendente'])],
        ]);

        if ($user->isAdmin() && $validated['role'] === 'atendente') {
            $outroAdministradorExiste = User::query()
                ->where('role', 'admin')
                ->whereKeyNot($user->getKey())
                ->exists();

            if (! $outroAdministradorExiste) {
                return back()->withErrors([
                    'role' => 'O sistema precisa manter pelo menos um administrador.',
                ]);
            }
        }

        $user->forceFill(['role' => $validated['role']])->save();

        return redirect()->route('usuarios-acesso.index')
            ->with('sucesso', 'Perfil de acesso atualizado.');
    }
}
