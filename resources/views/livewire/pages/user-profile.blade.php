<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-lg p-6 space-y-6">
        <h2 class="text-3xl font-bold text-gray-800 text-center">Профил на потребителя</h2>

        <div class="flex flex-col items-center space-y-4">
            <div class="relative">
                <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.png') }}" alt="Аватар"
                    class="w-32 h-32 rounded-full object-cover ring-4 ring-accent shadow-md" />
            </div>

            <livewire:profile.avatar-upload />

        </div>

        <div class="text-gray-700 space-y-1 text-center">
            <p><strong>Име:</strong> {{ auth()->user()->name }}</p>
            <p><strong>Имейл:</strong> {{ auth()->user()->email }}</p>
        </div>

        <div class="flex justify-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-accent text-white rounded-lg hover:bg-primary transition">
                    <i class="fas fa-sign-out-alt"></i>
                    Изход
                </button>
            </form>
        </div>
    </div>
</div>
