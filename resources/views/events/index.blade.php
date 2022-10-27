<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('app.agenda') }}
        </h2>
    </x-slot>

    <a class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-md leading-tight uppercase rounded-full hover:bg-blue-700 focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out shadow-xl"
        style="position:fixed; bottom:30px; right:40px;" href="{{ route('add_event') }}">
        <span>{{ __('app.add_event') }}</span>
    </a>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg space-y-10">
                @foreach ($events as $item)
                    <div class="bg-white rounded shadow-lg">
                        <div class="px-6 py-4 space-y-3">
                            {{-- Pojedyncze wydarzenie w terminarzu --}}
                            <h2 class="font-bold text-xl mb-2">{{ $item->title }}</h2>
                            <div class="flex flex-row flex-wrap">
                                <div class="border-8 border-sky-100 bg-sky-100 rounded-full m-1">
                                    <span
                                        class="px-2 border-white border-4 bg-white rounded-full">{{ $item->start }} {{ $item->start_time }}</span>
                                    <span
                                        class="px-2 border-white border-4 bg-white rounded-full">{{ $item->end }} {{ $item->end_time }}</span>
                                </div>
                                <div class="border-8 border-green-100 bg-green-100 rounded-full m-1">
                                    <span
                                        class="px-2 border-white border-4 bg-white rounded-full">{{ $item->name }}</span>
                                </div>
                                @if ($item->is_admin == 1)
                                    <div class="border-8 border-red-100 bg-red-100 rounded-full m-1">
                                        <span
                                            class="px-2 border-white border-4 bg-white rounded-full">{{ __('app.admin') }}</span>
                                    </div>
                                @endif
                            </div>
                            <p class="text-gray-700 text-base">
                                {{ $item->description }}
                            </p>

                            <div class="space-y-2" id="infos">

                                {{-- Informacje o aktualnej pogodzie --}}
                                <template id="current">
                                    <div class="bg-blue-100 border-l-4 border-blue-300 text-blue-600 p-4" role="alert">
                                        <h3 class="font-bold">{{ __('app.current') }}
                                        </h3>
                                        <p>Panuje <span id="desc"></span>. Temperatura wynosi
                                            <span id="temp"></span> stopni, ciśnienie <span id="pressure"></span> hPA, a wiatr wieje z
                                            prędkością <span id="wind"></span> km/h.
                                        </p>
                                    </div>
                                </template>

                                {{-- Informacje o prognozie --}}
                                <template id="forecast">
                                    <div class="bg-blue-100 border-l-4 border-blue-300 text-blue-600 p-4" role="alert">
                                        <h3 class="font-bold">{{ __('app.forecast') }}
                                        </h3>
                                        <p>
                                            Prognozujemy <span id="description"></span>. Temperatura będzie wynosić
                                            <span id="temp"></span> stopni, ciśnienie <span id="pressure"></span> hPA, a wiatr będzie wiał z
                                            prędkością <span id="wind_speed"></span> km/h (w porywach do <span id="wind_gust"></span>).
                                        </p>
                                    </div>
                                </template>

                                {{-- Ostrzeżenia pogodowe --}}
                                <template id="warn">
                                    <div class="bg-orange-100 border-l-4 border-orange-300 text-orange-600 p-4"
                                        role="alert">
                                        <h3 class="font-bold" id="title"></h3>
                                        <p id="short"></p>
                                    </div>
                                </template>
                            </div>
                            <script>window.weather_getter({{$item->id}})</script>
                            
                            {{-- Przycisk --}}
                            <div class="mt-6 "><a
                                    class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded-full shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
                                    href="/events/edit/{{ $item->event }}">
                                    {{ __('app.view') }}
                                </a></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
