<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('users:make-admin {email : E-mail do usuário que será promovido}')]
#[Description('Promove um usuário existente a administrador')]
class PromoteUserToAdmin extends Command
{
    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error('Usuário não encontrado.');

            return self::FAILURE;
        }

        $user->forceFill(['role' => 'admin'])->save();

        $this->info("{$user->email} agora é administrador.");

        return self::SUCCESS;
    }
}
