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
    private static function overlap(Event $ev, DateTime $start2, DateTime $end2)
    {
        $start1 = $ev->start;
        $end1 = $ev->end;
        $start_time = $ev->start_time;
        $end_time = $ev->end_time;

        $start1->setTime($start_time->format('H'), $start_time->format('i'), $start_time->format('s'));
        $end1->setTime($end_time->format('H'), $end_time->format('i'), $end_time->format('s'));
        if ($start2 >= $end1) {
            return false;
        } else {
            return $end2 >= $start1;
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
        $data = Attendance::join('events', 'events.id', '=', 'attendances.event')
            ->where('attendances.user', '=', Auth::id())
            ->join('places', 'places.id', '=', 'events.place')
            ->get(['title', 'start', 'end', 'start_time', 'end_time', 'is_admin', 'name', 'powiat', 'event', 'description', 'wojew'])
            ->sortBy('start', 'start_time');

        // Zbieranie danych o ostrzeżeniach i danych pogodowych
        $prognosis = [];
        $weather = [];
        return view('events/index', ['events' => $data]);
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
        $event->start = $request->start;
        $event->start_time = $request->start_time;
        $event->end = $request->end;
        $event->end_time = $request->end_end_time;
        $event->description = is_null($request->description) ? "" : $request->description;
        $event->place = $request->place;
        $event->save();

        // Zapisanie autora wydarzenia jako administratora
        Attendance::create([
            'is_admin'    => TRUE,
            'event'    => $event->id,
            'user'    => Auth::id(),
        ]);

        // Zapisanie zaproszonych użytkowników jako uczestników
        $users = User::whereIn("email", explode(", ", $request->place))->get('id');
        foreach ($users as $user) {
            if ($user->id == Auth::id()) continue;
            Attendance::create([
                'is_admin'    => false,
                'event'    => $event->id,
                'user'    => $user,
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
        $data = Event::join('attendances', 'events.id', '=', 'attendances.event')->where('attendances.user', '=', Auth::id())
            ->join('places', 'events.place', '=', 'places.id');

        if ($data->where('events.id', $id)->count() > 0) {

            // Sprawdzenie kompetencji użytkownika
            $event = $data->where('events.id', $id)->first();
            if ($event->is_admin) $editable = '';
            else $editable = 'readonly';

            // Utworzenie listy zaproszonych użytkowników
            $invites_arr = Attendance::where('event', '=', $id)->where('user', '!=', Auth::id())->join('users', 'user', '=', 'users.id')->pluck('email')->toArray();
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
        $events = Event::join('attendances', 'events.id', '=', 'attendances.event')->where('attendances.user', '=', Auth::id())->where('events.id', $id);
        if ($events->where('events.id', $id)->count() > 0) {
            if ($events->first()->is_admin) {
                // Sprawdzanie, czy id miejsca powinno być zmienione
                $place = is_null($request->place) ? $events->where('events.id', $id)->first()->place :  $request->place;

                // Zmiany w danych wydarzenia
                Event::where('id', $id)->update([
                    'title'        =>    $request->title,
                    'start'        =>    $request->start,
                    'start_time'     =>    $request->start_time,
                    'end'        =>    $request->end,
                    'end_time'        =>    $request->end_time,
                    'description' => is_null($request->description) ? "" : $request->description,
                    'place'     => $place
                ]);

                // Aktualizacja użytkowników uczestniczących w wydarzeniu
                Attendance::where('event', '=', $id)->where('user', '!=', Auth::id())->delete();
                $mails = explode(", ", $request->invites);
                $users = User::whereIn("email", $mails)->get('id');
                foreach ($users as $user) {
                    if ($user->id == Auth::id()) continue;
                    Attendance::create([
                        'is_admin'    => false,
                        'event'    => $id,
                        'user'    => $user->id,
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
        Attendance::where('user', '=', Auth::id())
            ->where('event', '=', $id)
            ->delete();

        // Usuwanie wydarzenia, jeśli nie posiada ono administratora 
        if (
            Attendance::where('event', '=', $id)
            ->where('is_admin', '=', '1')
            ->count() == 0
        ) {
            Event::find($id)->delete();
        }

        return redirect('/events/index');
    }
}
