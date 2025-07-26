<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('name', 'admin')->orWhere('email', 'admin')->first();

if ($user) {
    echo "Usuário admin encontrado: " . $user->email . "\n";
    $user->password = Hash::make('Skala@2025$');
    $user->save();
    echo "Senha atualizada com sucesso\!\n";
} else {
    echo "Usuário admin não encontrado.\n";
    $users = User::all();
    echo "Usuários existentes:\n";
    foreach ($users as $u) {
        echo "- ID: " . $u->id . ", Nome: " . $u->name . ", Email: " . $u->email . "\n";
    }
}
EOF < /dev/null