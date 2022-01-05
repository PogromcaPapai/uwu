<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventForm;
use App\Models\Attendance;
use App\Models\Event;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EventController extends Controller
{
    private static function overlap(string $start1, string $start2, string $end1, string $end2,)
    {
        if (new DateTime($start2) >= new DateTime($end1)) {
            return false;
        } else {
            return new DateTime($end2) > new DateTime($start1);
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
            ->get(['title', 'start', 'end', 'is_admin', 'name', 'powiat', 'event_id', 'description']);
        $prognosis = [];
        foreach ($data as $event) {
            $prognosis[$event->event_id] = [];

            $found = Http::get('http://127.0.0.1:3447/test?place=' . $event->powiat);
            if ($found->successful()) {
                foreach ($found->json() as $warn) {
                    if (EventController::overlap($event->start, $warn["starttime"][0], $event->end, $warn["endtime"][0]))
                        array_push($prognosis[$event->event_id], $warn);
                }
            }
        }
        return view('events/index', ['events' => $data, "prog" => $prognosis]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create_for_call(Request  $request)
    {
        return view('events/edit', ["edit" => false, 'start' => $request->start, 'end' => $request->end]);
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
    public function store(EventForm $request)
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
            return view('events/edit', ['event' => $event, "editable" => $editable, 'edit' => true]);
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
