@use(Illuminate\Support\Facades\Route)

<header class="w-full flex flex-col items-center bg-white p-4 border-b-2 border-b-gray-800">
    <div class="w-full max-w-[1024px]">
        <div class="flex flex-row items-center justify-between pb-4 border-b-2 border-b-gray-800">
            <a href="{{ route('index') }}">
                <h1 class="text-2xl">🚀 FWSTATS</h1>
            </a>

            <input type="text" class="border-2 border-gray-800">
        </div>

        <nav class="flex flex-col items-center pt-4">
            <ul class="flex flex-row items-center gap-8">
                <li><a href="{{ route('index') }}" class="{{ Route::is('index') ? 'underline' : '' }} hover:underline">Rangliste</a></li>
                <li><a href="{{ route('index') }}" class="hover:underline">Erfahrungspunkte</a></li>
                <li><a href="{{ route('index') }}" class="hover:underline">Spielzeit</a></li>
                <li><a href="{{ route('index') }}" class="hover:underline">Berufe</a></li>
                <li><a href="{{ route('index') }}" class="hover:underline">Namen</a></li>
                <li><a href="{{ route('index') }}" class="hover:underline">Bans</a></li>
                <li><a href="{{ route('index') }}" class="hover:underline">Löschungen</a></li>
                <li><a href="{{ route('index') }}" class="hover:underline">Bilder</a></li>
            </ul>
        </nav>
    </div>
</header>
