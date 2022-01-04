<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST">
                        @csrf
                        <label for="title">Nazwa wydarzenia</label>
                        <input type="text" name="title" id="title" value={{ $event->title }}>
                        <label for="start">Dzień rozpoczęcia</label>
                        <input type="date" name="start" id="start" value={{ $event->start }}>
                        <label for="end">Dzień zakończenia</label>
                        <input type="date" name="end" id="end" value={{ $event->end }}>
                        <label for="description">Opis</label>
                        <textarea name="description" id="description" cols="30"
                            rows="10">{{ $event->description }}</textarea>

                        <label for="place">Miejsce wydarzenia</label>
                        <br>
                        <select name="places" id="places"></select>

                        <script>
                            $("#places").selectize({
                                valueField: "id",
                                labelField: "label",
                                searchField: "name",
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
                            });
                        </script>

                        @error('place', 'title')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
