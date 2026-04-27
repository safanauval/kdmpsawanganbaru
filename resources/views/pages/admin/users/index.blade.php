<x-layouts::app :title="__('Manajemen User')">
    <div class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl">Manajemen User</flux:heading>
                <p class="mt-3 text-gray-600 dark:text-gray-400 space-y-2">Kelola data pengguna dan hak akses</p>
            </div>
        </div>

        <livewire:user-index />
    </div>
</x-layouts::app>