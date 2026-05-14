<x-vue-app-layout>
    <x-slot:scripts>
        @vite(['resources/js/poll-dashboard.js'])
    </x-slot>

    <x-slot:title>
        Voter
    </x-slot>

    {{-- Le token est passé en prop à l'app Vue via data-props --}}
    <div
        id="app"
        data-props='@json([
            "token"    => $token,
            "loginUrl" => route("login"),
        ])'
    ></div>
</x-vue-app-layout>
