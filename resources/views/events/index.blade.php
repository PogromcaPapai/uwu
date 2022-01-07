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
                            <h2 class="font-bold text-xl mb-2">{{ $item->title }}</h2>
                            <div class="flex flex-row flex-wrap">
                                <div class="border-8 border-sky-100 bg-sky-100 rounded-full m-1">
                                    <span
                                        class="px-2 border-white border-4 bg-white rounded-full">{{ $item->start }}</span>
                                    <span
                                        class="px-2 border-white border-4 bg-white rounded-full">{{ $item->end }}</span>
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
                            <div class="space-y-2">
                                @if (!is_null($weather[$item->event_id]))
                                    <div class="bg-blue-100 border-l-4 border-blue-300 text-blue-600 p-4" role="alert">
                                        <h3 class="font-bold">{{ __('app.forecast') }} -
                                            {{ $weather[$item->event_id]['stacja'] }}
                                        </h3>
                                        <p>Temperatura w tym punkcie wynosiła o
                                            {{ $weather[$item->event_id]['godzina_pomiaru'] }}:00
                                            {{ $weather[$item->event_id]['temperatura'] }} stopni, wiatr wiał z
                                            prędkością {{ $weather[$item->event_id]['predkosc_wiatru'] }} km/h, a
                                            suma opadów wyniosła {{ $weather[$item->event_id]['suma_opadu'] }} mm.
                                        </p>
                                    </div>
                                @endif
                                @foreach ($prog[$item->event_id] as $warn)
                                    <div class="bg-orange-100 border-l-4 border-orange-300 text-orange-600 p-4"
                                        role="alert">
                                        <h3 class="font-bold">{{ $warn['event'] }} ({{ __('app.level') }}
                                            {{ $warn['lvl'] }})
                                        </h3>
                                        <p>{{ $warn['rso'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6 "><a
                                    class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded-full shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
                                    href="/events/edit/{{ $item->event_id }}">
                                    {{ __('app.view') }}
                                </a></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
