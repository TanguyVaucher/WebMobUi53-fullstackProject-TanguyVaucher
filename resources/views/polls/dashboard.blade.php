<x-vue-app-layout>
    <x-slot:scripts>
        @vite(['resources/js/poll-dashboard.js'])
    </x-slot>

    <x-slot:title>
        Sondages
    </x-slot>

    @php
        $props = [
            'polls'    => $polls,
            'loginUrl' => route('login'),
            'username' => $username,
        ];
    @endphp

    <div id="app" data-props='@json($props)'></div>
</x-vue-app-layout>
