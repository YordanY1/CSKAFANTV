<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Football Site</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-card text-text font-primary min-h-screen flex flex-col">

    <livewire:components.navbar />

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <livewire:components.footer />

    @livewireScripts

</body>

</html>
