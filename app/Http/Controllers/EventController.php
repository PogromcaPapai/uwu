<?php

namespace App\Http\Controllers;

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
        }
        else {
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
        $data = Event::join('attendances', 'events.id', '=', 'attendances.event_id')->where('attendances.user_id', '=',Auth::id())
            ->join('places', 'events.place', '=', 'places.id')
            ->get(['title', 'start', 'end', 'is_admin', 'name', 'powiat', 'event_id', 'description']);
        $prognosis = [];
        foreach ($data as $event) {
            $prognosis[$event->event_id] = [];

            $found = Http::get('http://127.0.0.1:3447/test?place='.$event->powiat);
            if ($found ->successful()){
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $Event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $Event)
    {
        return view('events/edit', ['event' => $Event]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $Event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $Event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $Event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $Event)
    {
        //
    }
}
