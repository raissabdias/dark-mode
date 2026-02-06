<?php

namespace App\Filament\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use App\Filament\Resources\NewsResource;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->to(NewsResource::getUrl('index'));
    }
}