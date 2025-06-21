<div class="w-full max-w-sm mx-auto bg-gray-50 border border-gray-200 rounded-xl shadow-inner p-4">
    <div class="flex items-center gap-4">
        <img src="{{ $avatarUrl }}" alt="Аватар"
            class="w-24 h-24 rounded-full ring-2 ring-accent object-cover shadow-md" />

        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Смени снимка</label>

            <input type="file" wire:model="avatar" accept="image/*"
                class="block w-full text-sm text-gray-900 bg-white border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring-accent focus:border-accent" />

            @error('avatar')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

            @if (session()->has('success'))
                <p class="text-green-600 text-sm mt-2 font-medium">{{ session('success') }}</p>
            @endif

            <div wire:loading wire:target="avatar" class="text-gray-500 text-xs mt-1 animate-pulse">
                Качване на файл...
            </div>

            @if (auth()->user()->avatar)
                <button wire:click="deleteAvatar"
                    class="mt-3 text-sm text-red-600 hover:text-red-800 font-medium transition">
                    <i class="fas fa-trash-alt mr-1"></i> Изтрий аватар
                </button>
            @endif
        </div>
    </div>
</div>
