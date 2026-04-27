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
    public $editingUserId = null;
    public $selectedRole = '';

    // Untuk create user
    public $showCreateModal = false;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $newUser = 'kasir';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'newUser' => ['required', Rule::in(['admin', 'kasir'])],
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'newUser']);
        $this->newUser = 'kasir';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'newUser']);
    }

    public function createUser()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->newUser,
        ]);

        session()->flash('success', 'Anggota baru berhasil ditambahkan.');
        $this->closeCreateModal();
    }

    public function startEditing($userId, $currentRole)
    {
        $this->editingUserId = $userId;
        $this->selectedRole = $currentRole;
    }

    public function cancelEditing()
    {
        $this->reset(['editingUserId', 'selectedRole']);
    }

    public function updateRole($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id() && $this->selectedRole === 'kasir') {
            session()->flash('error', 'Anda tidak dapat mengubah role sendiri menjadi kasir.');
            $this->cancelEditing();
            return;
        }

        $user->update(['role' => $this->selectedRole]);
        session()->flash('success', 'Role user berhasil diperbarui.');
        $this->cancelEditing();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('components.users-index', [
            'users' => $users,
        ]);
    }
}