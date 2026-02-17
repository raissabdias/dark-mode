<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Columnist;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Manipula o evento de "criado" do Usuário.
     */
    public function created(User $user): void
    {
        Columnist::create([
            'user_id' => $user->id,
            'name'    => $user->name,
            'slug'    => Str::slug($user->name) . '-' . $user->id,
            'is_active' => true,
        ]);
    }
}
