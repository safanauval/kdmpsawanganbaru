<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('pages.admin.users.index');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,kasir',
        ]);

        // Cegah admin mengubah rolenya sendiri menjadi kasir (opsional)
        if ($user->id === auth()->id() && $request->role === 'kasir') {
            return back()->with('error', 'Anda tidak dapat mengubah role sendiri menjadi kasir.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', 'Role user berhasil diperbarui.');
    }
}