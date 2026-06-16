{{-- Layout Blade pour afficher une page Laravel qui héberge une app Vue --}}

@props([
    // Classes CSS appliquées au body
    'bodyClass' => 'min-h-screen bg-slate-50 text-slate-900 antialiased',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    {{-- Description de page --}}
    @isset($description)
        <meta name="description" content="{{ $description }}">
    @endisset

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Titre si fourni, sinon nom de l'application --}}
    @isset($title)
        <title>{{ $title }} - {{ config('app.name') }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endisset

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="/favicon.png">

    {{-- Chargement du CSS compilé par Vite --}}
    @vite(['resources/css/app.css'])

    {{-- Zone d'injection de scripts --}}
    @isset($scripts)
        {{ $scripts }}
    @endisset
</head>

{{-- Body de la page --}}
<body {{ $attributes->class([$bodyClass]) }}>

    {{-- Contenu de la page (insertion des vues) --}}
    {{ $slot }}
</body>

</html>
