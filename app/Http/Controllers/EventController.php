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
        $data = Attendance::join('events', 'events.id', '=', 'attendances.event_id')
            ->where('attendances.user_id', '=', Auth::id())
            ->join('places', 'places.id', '=', 'events.place')
            ->get(['title', 'start', 'end', 'is_admin', 'name', 'powiat', 'event_id', 'description', 'wojew']);
        $prognosis = [];
        $weather = [];
        foreach ($data as $event) {
            $prognosis[$event->event_id] = [];

            $found = Http::get('http://127.0.0.1:3447/warn?place=' . $event->powiat);
            
            $now = (new DateTime())->format('Y-m-d H:i:s');
            if (EventController::overlap($event->start, $now, $event->end, $now)) {
                $weather[$event->event_id] = Http::get('https://danepubliczne.imgw.pl/api/data/synop/station/' . STACJE[$event->wojew])->json();
            }
            else $weather[$event->event_id] = null;
            if ($found->successful()) {
                foreach ($found->json() as $warn) {
                    if (EventController::overlap($event->start, $warn["starttime"][0], $event->end, $warn["endtime"][0]))
                        array_push($prognosis[$event->event_id], $warn);
                }
            }
        }
        return view('events/index', ['events' => $data, "prog" => $prognosis, "weather" => $weather]);
    }

    /**
     * Show the form for creating a new resource.
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
        $event = new Event;
        $event->title = $request->title;
        $event->start =	$request->start;
        $event->end = $request->end;
        $event->description = is_null($request->description) ? "" : $request->description;
        $event->place = $request->place;
        $event->save();

        Attendance::create([
            'is_admin'	=> TRUE,
            'event_id'	=> $event->id,
            'user_id'	=> Auth::id(),
        ]);

        $users = User::whereIn("email", explode(", ", $request->place))->get('id');
        foreach ($users as $user) {
            Attendance::create([
                'is_admin'	=> false,
                'event_id'	=> $event->id,
                'user_id'	=> $user,
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
        $data = Event::join('attendances', 'events.id', '=', 'attendances.event_id')->where('attendances.user_id', '=', Auth::id())
            ->join('places', 'events.place', '=', 'places.id');
        if ($data->where('events.id', $id)->count() > 0) {
            $event = $data->where('events.id', $id)->first();
            if ($event->is_admin) $editable = '';
            else $editable = 'readonly';
            $invites_arr = Attendance::where('event_id', '=', $id)->where('user_id', '!=', Auth::id())->join('users', 'user_id', '=', 'users.id')->pluck('email')->toArray();
            $invites = join(", ", $invites_arr);
            return view('events/edit', ['event' => $event, 'invites' => $invites, "editable" => $editable, 'edit' => true]);
        } else {
            return abort(401);
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
        $events = Event::join('attendances', 'events.id', '=', 'attendances.event_id')->where('attendances.user_id', '=', Auth::id())->where('events.id', $id);
        if ($events->where('events.id', $id)->count() > 0) {
            if ($events->first()->is_admin) {
                $place = is_null($request->place) ? $events->where('events.id', $id)->first()->place :  $request->place;
                Event::where('id', $id)->update([
                    'title'        =>    $request->title,
                    'start'        =>    $request->start,
                    'end'        =>    $request->end,
                    'description' => is_null($request->description) ? "" : $request->description,
                    'place'     => $place
                ]);

                Attendance::where('event_id', '=', $id)->where('user_id', '!=', Auth::id())->delete();
                $mails = explode(", ", $request->invites);
                $users = User::whereIn("email", $mails)->get('id');
                foreach ($users as $user) {
                    Attendance::create([
                        'is_admin'	=> false,
                        'event_id'	=> $id,
                        'user_id'	=> $user->id,
                    ]);
                }
                return redirect('/events/index');
            } else {
                return abort(405);
            }
        } else {
            return abort(401);
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
        $event = Attendance::where('user_id', '=', Auth::id())
            ->where('event_id', '=', $id)
            ->delete();
        if (Attendance::where('event_id', '=', $id)
            ->where('is_admin', '=', '1')
            ->count() == 0
        ) {
            Event::find($id)->delete();
        }

        return redirect('/events/index');
    }
}
