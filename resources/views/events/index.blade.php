<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg space-y-10">
                @foreach ($events as $item)
                    <div class="bg-white rounded shadow-lg">
                        <div class="px-6 py-4 space-y-3">
                            <div class="font-bold text-xl mb-2">{{ $item->title }}</div>
                            <div class="flex space-x-3">
                                <div class="flex-initial border-8 border-sky-100 bg-sky-100 lg:rounded-full divide-x ">
                                    <span class="px-2 border-white border-4 bg-white lg:rounded-full">{{ $item->start }}</span>
                                    <span class="px-2 border-white border-4 bg-white lg:rounded-full">{{ $item->end }}</span>
                                </div> 
                                <div class="flex-initial border-8 border-green-100 bg-green-100 lg:rounded-full divide-x ">
                                    <span class="px-2 border-white border-4 bg-white lg:rounded-full">{{ $item->name }}</span>
                                </div> 
                                @if ($item->is_admin == 1)
                                    <div class="flex-initial border-8 border-red-100 bg-red-100 lg:rounded-full divide-x ">
                                        <span class="px-2 border-white border-4 bg-white lg:rounded-full">Zarządzasz wydarzeniem</span>
                                    </div>
                                @endif
                            </div>
                            <p class="text-gray-700 text-base">
                                {{ $item->description }}
                            </p>
                            <div class="space-y-2">                         
                                @foreach ($prog[$item->event_id] as $warn)
                                    <div class="bg-orange-100 border-l-4 border-orange-300 text-orange-600 p-4" role="alert">
                                        <p class="font-bold">{{ $warn['event'] }} stopnia {{ $warn['lvl'] }}</p>
                                        <p>{{ $warn['rso'] }}</p>
                                    </div>
                                @endforeach
                            </div>                         
                            <div class="mt-6 "><a class="bg-blue-300 hover:bg-blue-400 text-white font-bold py-2 px-4 rounded-full" href="/events/edit/{{$item->event_id}}">
                                Edytuj
                            </a></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
