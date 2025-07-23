<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8" />

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YE5TV8L4BF"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-YE5TV8L4BF');
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $title ?? 'CSKA FAN TV – Подкаст, интервюта и всичко за ЦСКА' }}</title>
    <meta name="description"
        content="{{ $description ?? 'Официалният фен подкаст на червените. Гледай интервюта с легенди на ЦСКА, анализи на мачове, фен коментари и още!' }}" />
    <meta name="robots" content="{{ $robots ?? 'index, follow' }}" />
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}" />

    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="{{ $og_title ?? ($title ?? 'CSKA FAN TV – Всичко за ЦСКА') }}" />
    <meta property="og:description"
        content="{{ $og_description ?? ($description ?? 'Подкаст за ЦСКА, интервюта с футболисти и коментари от червени фенове.') }}" />
    <meta property="og:image" content="{{ $og_image ?? url('images/og-cska.png') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $og_url ?? url()->current() }}" />
    <meta property="og:site_name" content="CSKA FAN TV" />

    <!-- Twitter Meta -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $og_title ?? 'CSKA FAN TV – Подкаст и интервюта за ЦСКА' }}" />
    <meta name="twitter:description"
        content="{{ $og_description ?? 'Гледай подкасти и интервюта за любимия ти отбор – ЦСКА София. Само за верните фенове!' }}" />
    <meta name="twitter:image" content="{{ $og_image ?? url('images/og-cska.png') }}" />

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">


    <!-- PWA / Web Manifest -->
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">
    <meta name="apple-mobile-web-app-title" content="CSKA FAN TV">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />

    @if (request()->is('/'))
        <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "CSKA FAN TV",
  "url": "{{ url('/') }}",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "{{ url('/') }}/search?q={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>

        <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "CSKA FAN TV",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('images/og-cska.png') }}",
  "sameAs": [
    "https://www.facebook.com/cskafantv",
    "https://www.instagram.com/cskafantv",
    "https://www.youtube.com/@cskafantv",
  ]
}
</script>
    @endif


    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-card text-text font-primary min-h-screen flex flex-col">

    <livewire:components.navbar />

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <livewire:components.prediction-modal />

    <livewire:components.footer />
    <livewire:components.cookie-consent-banner />
    @livewireScripts

</body>

</html>
