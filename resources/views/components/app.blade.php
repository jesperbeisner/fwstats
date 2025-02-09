<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-dvh antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FWSTATS</title>

    @vite('resources/css/app.css')
</head>
<body class="font-mono flex flex-col items-center h-dvh text-gray-800 font-semibold bg-violet-400">
    <x-layout.header />

    <main class="flex-1 p-4 md:p-8 flex flex-col w-full items-center">
        <div class="w-full max-w-[1024px]">
            {{ $slot }}
        </div>
    </main>

    <x-layout.footer />

    @vite('resources/js/app.js')
</body>
</html>
