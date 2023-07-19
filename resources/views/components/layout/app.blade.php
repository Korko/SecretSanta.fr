<x-layout.main>
    <x-slot:head>
        {{ $head }}
    </x-slot>

    <div class="w-full h-screen max-h-full">
        <header class="header" id="header">
            <nav class="z-10 absolute w-full">
                <img
                    class="absolute w-64 mt-5 ml-10"
                    src="{{ Vite::asset('resources/images/logo.svg') }}"
                    alt="logo"
                />
                <ul class="ml-[60vh] mr-20 mt-10 flex justify-between uppercase font-xs">
                    <li>Accueil</li>
                    <li>Comment ça marche ?</li>
                    <li>Mes évènements</li>
                    <li>Rejoindre un évènement</li>
                </ul>
            </nav>

            {{  $header }}
        </header>

        {{ $slot  }}
    </div>
</x-layout.main>
