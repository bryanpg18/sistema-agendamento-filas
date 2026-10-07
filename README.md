# Sistema de Agendamento e Filas

Aplicação web para organizar clientes, serviços, horários, agendamentos e o fluxo de atendimento de um estabelecimento.

## Requisitos

- PHP 8.3 ou superior e Composer
- Node.js e npm
- Um banco de dados compatível com Laravel (SQLite, MySQL ou PostgreSQL)

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure a conexão do banco no `.env`. Para SQLite, crie o arquivo configurado em `DB_DATABASE` antes de migrar. Em seguida, execute:

```bash
php artisan migrate
npm install
npm run build
```

Para iniciar o servidor local:

```bash
php artisan serve
```

Em desenvolvimento, `npm run dev` mantém o Vite ativo para atualizar os assets.

## Funcionalidades

- Cadastro e consulta de clientes, com validação e normalização de CPF e telefone.
- Cadastro de serviços e configuração do expediente e da duração padrão dos horários.
- Criação, edição, cancelamento e consulta de agendamentos.
- Painel de atendimento com fila, início e conclusão dos atendimentos.
- Histórico, indicadores do dashboard e relatórios por período e serviço.
- Autenticação, verificação de e-mail e gerenciamento de perfil.

O painel exige autenticação e verificação de e-mail. Cadastre um usuário pela tela de registro para acessar o sistema.

## Testes

```bash
php artisan test
```

## Stack

- Laravel 13 e PHP 8.3+
- Blade, Alpine.js e Tailwind CSS
- Vite
