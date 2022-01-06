<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('app.edit_event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8 max-w-xl">
            <div class="flex bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    <form method="POST">
                        @csrf
                        <div class="md:flex md:items-center mb-6 ">
                            <div class="md:w-1/3">
                                <label class="block md:text-right mb-1 md:mb-0 pr-4" for="title">Nazwa wydarzenia</label>
                            </div>
                            <div class="md:w-2/3">
                                <input type="text" name="title" id="title" @if ($edit)
                                value="{{ $event->title }}" {{ $editable }}
                                @endif
                                class="rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                            </div>
                        </div>

                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block md:text-right mb-1 md:mb-0 pr-4" for="start">Data
                                    rozpoczęcia</label>
                            </div>
                            <div class="md:w-2/3">
                                <input type="date" name="start" id="start" @if ($edit)
                                value="{{ $event->start }}" {{ $editable }}
                                @else
                                @if (!is_null($start))
                                value="{{ $start }}" 
                                @endif
                                @endif
                                class="rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                            </div>
                        </div>
                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block md:text-right mb-1 md:mb-0 pr-4" for="end">Data zakończenia</label>
                            </div>
                            <div class="md:w-2/3">
                                <input type="date" name="end" id="end" @if ($edit)
                                value="{{ $event->end }}" {{ $editable }}
                                @else
                                @if (!is_null($end))
                                value="{{ $end }}" 
                                @endif
                                @endif
                                class="rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                            </div>
                        </div>
                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block md:text-right mb-1 md:mb-0 pr-4" for="description">Opis
                                    wydarzenia</label>
                            </div>
                            <div class="md:w-2/3">
                                <textarea name="description" id="description" style="resize: none" @if ($edit)
                                        {{ $editable }}
                                    @endif
                                    class="rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">@if ($edit){{ $event->description }}@endif</textarea>
                            </div>
                        </div>
                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block md:text-right mb-1 md:mb-0 pr-4" for="invites">{{ __("forms.invites") }}</label>
                                <div class="block uppercase font-black text-gray-600 md:text-right text-xs pr-4 pb-2">{{ __("forms.invites_email") }}</div>
                            </div>
                            <div class="md:w-2/3">
                                <textarea name="invites" id="invites" style="resize: none" @if ($edit)
                                        {{ $editable }}
                                    @endif
                                    class="rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">@if ($edit){{ $invites }}@endif</textarea>
                            </div>
                        </div>
                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block md:text-right mb-1 md:mb-0 pr-4" for="place">Miejsce
                                    wydarzenia</label>
                            </div>
                            <div class="md:w-2/3">
                                @if (!$edit || $editable != 'readonly')
                                    <select name="place" id="place"></select>
                                    <script>
                                        $("#place").selectize({
                                            valueField: "id",
                                            labelField: "label",
                                            searchField: "name",

                                            @if ($edit)
                                                placeholder: "{{ $event->name }}",
                                            @endif

                                            load: function(query, callback) {
                                                if (!query.length) return callback();
                                                $.ajax({
                                                    url: "/place",
                                                    type: "get",
                                                    data: {
                                                        q: query
                                                    },
                                                    success: function(places) {
                                                        var items = [];
                                                        for (var i in places) {
                                                            items.push({
                                                                id: places[i].id,
                                                                name: places[i].name,
                                                                label: `${places[i].name} (${places[i].desc}, ${places[i].powiat})`
                                                            });
                                                        }
                                                        console.log(items);
                                                        callback(items);
                                                    }
                                                });
                                            },
                                        })
                                    </script>

                                @else
                                    <input type="text" name="title" id="title" value="{{ $event->name }}" readonly
                                        class="rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                                @endif
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="flex-initial flex-col bg-red-100 border-l-8 border-red-600 py-1 my-3">
                                @foreach ($errors->all() as $error)
                                    <p class="text-md font-bold text-red-600 text-sm p-1.5 pl-6">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        <div class="flex justify-center gap-1.5">
                            <a type="button"
                                class="inline-block px-6 py-2 border-2 border-blue-600 text-blue-600 font-medium text-xs leading-tight uppercase rounded-full hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out"
                                href="/events/index">{{__('app.goback')}}</a>
                            @if (!$edit || $editable != 'readonly')
                                <button type="submit"
                                    class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded-full shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">{{__('app.save')}}</button>
                            @endif
                            @if ($edit)
                                <a type="button"
                                    class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded-full shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                    href="/events/edit/{{ $event->event_id }}/delete">{{__('app.delete')}}</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
