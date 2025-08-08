@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title === null ? config()->string('app.name') : $title . ' - ' . config()->string('app.name') }}</title>

    @vite('resources/css/app.css')
</head>
<body>
    {{ $slot }}

    @vite('resources/js/app.js')
</body>
</html>
