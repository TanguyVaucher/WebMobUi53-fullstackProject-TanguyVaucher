{{-- Charge le dashboard Vue dans le layout blade --}}

<x-vue-app-layout>
    {{-- Chargement du point d'entree JS  --}}
    <x-slot:scripts>
        @vite(['resources/js/poll-dashboard.js'])
    </x-slot>

    {{-- Titre HTML --}}
    <x-slot:title>
        Sondages
    </x-slot>

    {{-- Donnees polls / auth à passer de Laravel à Vue --}}
    @php
        $props = [
            'polls'    => $polls,
            'loginUrl' => route('login'),
            'username' => $username,
        ];
    @endphp

    {{-- Container HTML pour monter Vue, avec les props passées en attribut --}}
    <div id="app" data-props='@json($props)'></div>
</x-vue-app-layout>
