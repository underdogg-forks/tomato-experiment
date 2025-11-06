@extends('cms::layouts.app')

@section('body')
    <div class="flex flex-col justify-center items-center min-h-[calc(100vh-4rem)] w-full gap-4">
        <x-application-logo />
        <h2 class="text-3xl font-bold">Page Not Found (404)</h2>
        <p class="text-lg text-gray-400">The page you try to access is not found, if you think it's warning please let us know in <x-filament::link color="danger" href="https://discord.gg/vKV9U7gD3c" target="_blank">discord server</x-filament::link></p>
        <div class="flex justify-center gap-4 my-2">
            <x-filament::link href="{{ url(app()->getLocale() . '/') }}">Home</x-filament::link>
            <x-filament::link href="{{ url(app()->getLocale() . '/open-source') }}" target="_blank">Docs</x-filament::link>
            <x-filament::link href="https://www.github.com/tomatophp" target="_blank">Github</x-filament::link>
            <x-filament::link href="https://discord.gg/vKV9U7gD3c" target="_blank">Support</x-filament::link>
            <x-filament::link href="https://github.com/sponsors/3x1io" target="_blank">Buy Me a Coffee</x-filament::link>
        </div>
    </div>
@endsection
