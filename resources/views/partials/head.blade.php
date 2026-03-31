<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="{{ asset('image/logo_casa.png') }}" media="(prefers-color-scheme: light)">
<link rel="icon" href="{{ asset('image/logo_casa_dark.png') }}" media="(prefers-color-scheme: dark)">
<link rel="apple-touch-icon" href="{{ asset('image/logo_casa.png') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
