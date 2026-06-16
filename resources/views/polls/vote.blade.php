{{-- Charge l'app Vue de vote et lui transmet le token pour récupérer le sondage --}}

<x-vue-app-layout>
    {{-- Chargement du point d'entree JS  --}}
    <x-slot:scripts>
        @vite(['resources/js/poll-dashboard.js'])
    </x-slot>

    {{-- Titre HTML --}}
    <x-slot:title>
        Voter
    </x-slot>

    {{-- Le token est passé dans le container de montage --}}
    <div
        id="app"
        data-props='@json([
            "token"    => $token,
            "loginUrl" => route("login"),
        ])'
    ></div>
</x-vue-app-layout>
