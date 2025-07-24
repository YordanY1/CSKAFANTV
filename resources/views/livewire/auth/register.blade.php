<div class="space-y-4">
    <form wire:submit.prevent="register" class="space-y-4 text-text">

        <div class="space-y-1">
            <label for="name" class="block text-sm font-medium text-text">Име</label>
            <input id="name" type="text" wire:model.defer="name" placeholder="Твоето име"
                class="w-full px-4 py-2 bg-white text-text border border-gray-300 rounded-lg shadow-sm focus:ring-accent focus:border-accent focus:outline-none @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1">
            <label for="email" class="block text-sm font-medium text-text">Имейл</label>
            <input id="email" type="email" wire:model.defer="email" placeholder="someone@example.com"
                class="w-full px-4 py-2 bg-white text-text border border-gray-300 rounded-lg shadow-sm focus:ring-accent focus:border-accent focus:outline-none @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1">
            <label for="password" class="block text-sm font-medium text-text">Парола</label>
            <input id="password" type="password" wire:model.defer="password" placeholder="••••••••"
                class="w-full px-4 py-2 bg-white text-text border border-gray-300 rounded-lg shadow-sm focus:ring-accent focus:border-accent focus:outline-none @error('password') border-red-500 @enderror">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1">
            <label for="password_confirmation" class="block text-sm font-medium text-text">Потвърди паролата</label>
            <input id="password_confirmation" type="password" wire:model.defer="password_confirmation"
                placeholder="••••••••"
                class="w-full px-4 py-2 bg-white text-text border border-gray-300 rounded-lg shadow-sm focus:ring-accent focus:border-accent focus:outline-none">
        </div>

        <button type="submit"
            class="w-full bg-accent hover:bg-accent-2 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 shadow hover:shadow-md cursor-pointer">
            Регистрация
        </button>
    </form>
</div>
