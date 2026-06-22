<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        return match ($user->role) {
            'admin' => redirect()->route('dashboard'),  // Nama rute yang benar
            'kasir' => redirect()->route('kasir'),
            default => redirect('/'),
        };
    }
}