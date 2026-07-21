<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingUserId = null;
    public $selectedRole = '';

    // Create
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $newUserRole = 'kasir';

    // Edit
    public $editUserId = null;
    public $editName = '';
    public $editEmail = '';
    public $editPassword = '';
    public $editPassword_confirmation = '';  // ← Perbaiki nama
    public $editRole = '';

    protected $queryString = ['search' => ['except' => '']];

    // ========== CREATE ==========
    
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'newUserRole']);
        $this->newUserRole = 'kasir';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'newUserRole']);
    }

    public function createUser()
    {
        $this->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'newUserRole' => ['required', Rule::in(['admin', 'kasir'])],
        ]);

        User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
            'role'     => $this->newUserRole,
        ]);

        $this->dispatch('notify', 'Anggota baru berhasil ditambahkan.', 'success');
        $this->closeCreateModal();
    }

    // ========== ROLE (Cepat) ==========

    public function startEditing($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $userId;
        $this->selectedRole = $user->role;  
    }

    public function cancelEditing()
    {
        $this->reset(['editingUserId', 'selectedRole']);
    }

    public function updateRole($userId)
    {
        // dd($userId);
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id() && $this->selectedRole === 'kasir') {
            $this->dispatch('notify', 'Anda tidak dapat mengubah role sendiri menjadi kasir.', 'error');
            $this->cancelEditing();
            return;
        }

        $user->update(['role' => $this->selectedRole]);
        $this->dispatch('notify', 'Role user berhasil diperbarui.', 'success');
        $this->cancelEditing();
    }

    // ========== DELETE ==========

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        $this->dispatch('notify', 'User berhasil dihapus.', 'success');
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('components.users-index', compact('users'));
    }
}