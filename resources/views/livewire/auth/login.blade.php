<form wire:submit.prevent="login" class="space-y-4 text-left">
    @if ($errorMessage)
        <div class="text-red-600 text-sm font-semibold">{{ $errorMessage }}</div>
    @endif

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


    <div class="flex items-center space-x-2">
        <input type="checkbox" wire:model="remember" id="remember"
            class="rounded border-gray-300 text-accent focus:ring-accent">
        <label for="remember" class="text-sm text-gray-600">Запомни ме</label>
    </div>

    <button type="submit"
        class="w-full bg-accent text-white font-semibold py-2 px-4 rounded hover:bg-primary transition cursor-pointer">
        Вход
    </button>
</form>
