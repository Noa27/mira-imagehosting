<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

#[Fillable(['name', 'password', 'is_admin'])]
#[Hidden(['password', 'remember_token', 'api_token'])]
class User extends Authenticatable
{
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function regenerateToken(): string
    {
        $token = Str::random(48);
        $this->forceFill(['api_token' => hash('sha256', $token)])->save();

        return $token;
    }
}
