{{-- resources/views/auth/register.blade.php --}}
<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Create an account')"
            :description="__('Enter your details below to create your account')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            {{-- Nama Lengkap --}}
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            {{-- Email --}}
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            {{-- Pilihan Role --}}
            <fieldset>
                <legend class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                    {{ __('Role') }}
                </legend>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="role"
                            value="kasir"
                            class="border-zinc-300 text-indigo-600 focus:ring-indigo-500"
                            {{ old('role', 'kasir') === 'kasir' ? 'checked' : '' }}
                        >
                        <span>{{ __('Kasir') }}</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="role"
                            value="admin"
                            class="border-zinc-300 text-indigo-600 focus:ring-indigo-500"
                            {{ old('role') === 'admin' ? 'checked' : '' }}
                        >
                        <span>{{ __('Admin') }}</span>
                    </label>
                </div>
                @error('role')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Password --}}
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            {{-- Konfirmasi Password --}}
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button" color="blue">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>