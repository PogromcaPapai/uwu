<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventForm;
use App\Http\Requests\CreateEventForm;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

define("STACJE", [
    "dolnośląskie" => "wroclaw",
    "kujawsko-pomorskie" => "torun",
    "lubelskie" => "lublin",
    "lubuskie" => "zielona gora",
    "łódzkie" => "lodz",
    "małopolskie" => "krakow",
    "mazowieckie" => "warszawa",
    "opolskie" => "opole",
    "podkarpackie" => "rzeszow",
    "podlaskie" => "bialystok",
    "pomorskie" => "gdansk",
    "śląskie" => "katowice",
    "świętokrzyskie" => "kielce",
    "warmińsko-mazurskie" => "olsztyn",
    "wielkopolskie" => "poznan",
    "zachodniopomorskie" => "szczecin",
]);

class EventController extends Controller
{
    /**
     * Funkcja Sprawdza, czy dwa terminy się nakładają
     *
     * Sprawdza, czy zakresy start1-end1 oraz start2-end2 się nakładają.
     * Format stringa powinien być odczytywalny przez konstruktor klasy DateTime 
     *
     * @return bollean
     */
    private static function overlap(string $start1, string $start2, string $end1, string $end2,)
    {
        if (new DateTime($start2) >= new DateTime($end1)) {
            return false;
        } else {
            return new DateTime($end2) >= new DateTime($start1);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Zbieranie danych o wydarzeniach w których uczestniczy użytkownik
        $data = Attendance::join('events', 'events.id', '=', 'attendances.event_id')
            ->where('attendances.user_id', '=', Auth::id())
            ->join('places', 'places.id', '=', 'events.place')
            ->get(['title', 'start', 'end', 'is_admin', 'name', 'powiat', 'event_id', 'description', 'wojew'])
            ->sortBy('start');

        // Zbieranie danych o ostrzeżeniach i danych pogodowych
        $prognosis = [];
        $weather = [];
        foreach ($data as $event) {
            // Zbieranie informacji o ostrzeżeniach
            $prognosis[$event->event_id] = [];

            // Komunikacja z Plumber API
            $found = Http::get('http://127.0.0.1:3447/warn?place=' . $event->powiat);

            if ($found->successful()) {
                foreach ($found->json() as $warn) {
                    if (EventController::overlap($event->start, $warn["starttime"][0], $event->end, $warn["endtime"][0]))
                        array_push($prognosis[$event->event_id], $warn);
                }
            }

            // Zbieranie informacji o aktualnej pogodzie z danych IMGW
            $now = (new DateTime())->format('Y-m-d H:i:s');
            if (EventController::overlap($event->start, $now, $event->end, $now)) {
                $weather[$event->event_id] = Http::get('https://danepubliczne.imgw.pl/api/data/synop/station/' . STACJE[$event->wojew])->json();
            } else $weather[$event->event_id] = null;
        }
        return view('events/index', ['events' => $data, "prog" => $prognosis, "weather" => $weather]);
    }

    /**
     * Show the form for creating a new resource.
     * Dodatkowo czyta z requestu informacje o start i end, aby wypełnić odpowiednie miejsca formularzu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create_for_call(Request  $request)
    {
        $start = new DateTime($request->start);
        $end = new DateTime($request->end);
        return view('events/edit', ["edit" => false, 'start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('events/edit', ["edit" => false, 'start' => null, 'end' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateEventForm $request)
    {
        // Zapisywanie danych
        $event = new Event;
        $event->title = $request->title;
        $event->start =    $request->start;
        $event->end = $request->end;
        $event->description = is_null($request->description) ? "" : $request->description;
        $event->place = $request->place;
        $event->save();

        // Zapisanie autora wydarzenia jako administratora
        Attendance::create([
            'is_admin'    => TRUE,
            'event_id'    => $event->id,
            'user_id'    => Auth::id(),
        ]);

        // Zapisanie zaproszonych użytkowników jako uczestników
        $users = User::whereIn("email", explode(", ", $request->place))->get('id');
        foreach ($users as $user) {
            if ($user->id == Auth::id()) continue; 
            Attendance::create([
                'is_admin'    => false,
                'event_id'    => $event->id,
                'user_id'    => $user,
            ]);
        }
        return redirect('/events/index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        // Zebranie wydarzeń
        $data = Event::join('attendances', 'events.id', '=', 'attendances.event_id')->where('attendances.user_id', '=', Auth::id())
            ->join('places', 'events.place', '=', 'places.id');

        if ($data->where('events.id', $id)->count() > 0) {

            // Sprawdzenie kompetencji użytkownika
            $event = $data->where('events.id', $id)->first();
            if ($event->is_admin) $editable = '';
            else $editable = 'readonly';

            // Utworzenie listy zaproszonych użytkowników
            $invites_arr = Attendance::where('event_id', '=', $id)->where('user_id', '!=', Auth::id())->join('users', 'user_id', '=', 'users.id')->pluck('email')->toArray();
            $invites = join(", ", $invites_arr);

            return view('events/edit', ['event' => $event, 'invites' => $invites, "editable" => $editable, 'edit' => true]);
        } else {
            return abort(401, "You don't have access to any such event"); // Nie znaleziono wydarzenia
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventForm $request, int $id)
    {
        // Zebranie wydarzeń
        $events = Event::join('attendances', 'events.id', '=', 'attendances.event_id')->where('attendances.user_id', '=', Auth::id())->where('events.id', $id);
        if ($events->where('events.id', $id)->count() > 0) {
            if ($events->first()->is_admin) {
                // Sprawdzanie, czy id miejsca powinno być zmienione
                $place = is_null($request->place) ? $events->where('events.id', $id)->first()->place :  $request->place;
                
                // Zmiany w danych wydarzenia
                Event::where('id', $id)->update([
                    'title'        =>    $request->title,
                    'start'        =>    $request->start,
                    'end'        =>    $request->end,
                    'description' => is_null($request->description) ? "" : $request->description,
                    'place'     => $place
                ]);

                // Aktualizacja użytkowników uczestniczących w wydarzeniu
                Attendance::where('event_id', '=', $id)->where('user_id', '!=', Auth::id())->delete();
                $mails = explode(", ", $request->invites);
                $users = User::whereIn("email", $mails)->get('id');
                foreach ($users as $user) {
                    if ($user->id == Auth::id()) continue; 
                    Attendance::create([
                        'is_admin'    => false,
                        'event_id'    => $id,
                        'user_id'    => $user->id,
                    ]);
                }
                return redirect('/events/index');
            } else {
                return abort(405, "You're not the administrator of this event");  // Brak uprawnień
            }
        } else {
            return abort(401, "You don't have access to any such event"); // Nie znaleziono wydarzenia
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        // Usuwanie uczestnictwa w wydarzeniu
        Attendance::where('user_id', '=', Auth::id())
            ->where('event_id', '=', $id)
            ->delete();
            
        // Usuwanie wydarzenia, jeśli nie posiada ono administratora 
        if (
            Attendance::where('event_id', '=', $id)
            ->where('is_admin', '=', '1')
            ->count() == 0
        ) {
            Event::find($id)->delete();
        }

        return redirect('/events/index');
    }
}
