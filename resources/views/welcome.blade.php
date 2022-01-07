{{-- Strona główna --}}
<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <article class="flex flex-row p-3 flex-wrap">
                        <div class="p-4 basis-1/2">
                            <h2 class="font-black text-4xl uppercase py-3">Co robimy?</h2>
                            <p class="text-gray-800 text-4xl text-justify">uvvv to kalendarz z informacjami o anomaliach
                                pogodowych.
                            </p>
                        </div>
                        <div class="p-4 basis-1/2">
                            <h2 class="font-black text-4xl uppercase py-3">Dlaczego?</h2>
                            <p class="text-gray-800 text-justify">Od ponad stu lat ludzkość może obserwować skutki
                                swojej industrialnej działalności na klimacie. Powoduje ona wyższe szanse na anomalie
                                pogodowe. Wraz z nasileniem się tych zjawisk ich wpływ na nasze życie będzie się
                                zwiększał, wymuszając na wielu osobach zmiany w trybie życia.</p>
                        </div>
                    </article>
                    <script type="text/javascript" src="https://www.climatelevels.org/graphs/js/temperature.php?theme=grid-light&pid=2degreesinstitute">
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
