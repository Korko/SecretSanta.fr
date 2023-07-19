<x-layout.app>
    <x-slot:head>
        @vite(['resources/css/hero.css'])
    </x-slot>

    <x-slot:header>
    </x-slot:header>

    <div class="w-full mx-auto bg-white 2xl:flex 2xl:justify-start">
        <img
            class="h-full max-h-screen -mr-[60vh] w-auto hidden 2xl:inline"
            src="{{ Vite::asset('resources/images/hero.svg') }}"
            alt="hero"
        />
        <div
            class="2xl:hidden bg-independance"
        >
            <img
                class="h-full max-h-screen"
                src="{{ Vite::asset('resources/images/hero-full-flip.svg') }}"
                alt="hero mobile"
            />
        </div>
        <div class="flex flex-col justify-center 2xl:-mt-[15%]">
            <h2 class="text-melon text-4xl font-bold leading-normal">Offrez-vous des cadeaux <span class="text-desert-sand block">secrètement !</span></h2>
            <h3 class="text-2xl font-bold leading-relaxed mt-5">Qu'est-ce que Secret Santa ?</h3>
            <p class=" pr-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer euismod tempor ornare. Mauris gravida sagittis magna convallis lacinia. Suspendisse id commodo tellus. Fusce eget hendrerit arcu. Suspendisse vehicula est non massa interdum pellentesque vitae vitae elit. Duis quis mauris eu sem convallis cursus et eget nulla. Morbi aliquam nisl elit, eget sollicitudin nunc dictum eget. Donec efficitur pellentesque malesuada. Sed euismod auctor sem sit amet malesuada.</p>
        </div>
    </div>
</x-layout.app>
