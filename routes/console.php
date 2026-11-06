<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

Artisan::command('user:create {name} {--admin} {--password=}', function (string $name) {
    if (User::where('name', $name)->exists()) {
        return $this->error('Ce compte existe déjà');
    }

    $password = $this->option('password') ?: $this->secret('Mot de passe');
    User::create(['name' => $name, 'password' => $password, 'is_admin' => $this->option('admin')]);
    $this->info('Compte '.$name.' créé');
})->purpose('Créer un compte');
