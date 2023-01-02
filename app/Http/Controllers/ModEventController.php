<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;

function authAdmin() {
    return User::where('id', '=', Auth::id())->where('is_mod', '=', '1')->count() > 0;
}

class ModEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!authAdmin()) {
            return abort(401, "You are not a moderator");
        }
        // Zbieranie danych o wydarzeniach w których uczestniczy użytkownik
        $data = Event::join('places', 'places.id', '=', 'events.place')
            ->get(['events.id', 'title', 'start', 'end', 'start_time', 'end_time', 'name', 'powiat', 'description', 'wojew'])
            ->sortBy(['start', 'start_time']);

        // Zbieranie danych o ostrzeżeniach i danych pogodowych
        $prognosis = [];
        $weather = [];
        return view('events/index', ['events' => $data, 'is_mod'=>1]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        if (!authAdmin()) {
            return abort(401, "You are not a moderator");
        }
        // Zebranie wydarzeń
        $data = Event::join('places', 'events.place', '=', 'places.id');

        if ($data->where('events.id', $id)->count() > 0) {

            // Sprawdzenie kompetencji użytkownika
            $event = $data->where('events.id', $id)->first();
            $editable = '';

            // Utworzenie listy zaproszonych użytkowników
            $invites_arr = Attendance::where('event', '=', $id)->join('users', 'user', '=', 'users.id')->pluck('email')->toArray();
            $invites = join(", ", $invites_arr);

            return view('events/edit', ['eid' => $id, 'event' => $event, 'invites' => $invites, "editable" => $editable, 'edit' => true, 'is_mod' => 1]);
        } else {
            return abort(401, "No such event"); // Nie znaleziono wydarzenia
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
        if (!authAdmin()) {
            return abort(401, "You are not a moderator");
        }
        // Zebranie wydarzeń
        $events = Event::join('attendances', 'events.id', '=', 'attendances.event')->where('events.id', $id);
        if ($events->where('events.id', $id)->count() > 0) {
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
            
            return redirect('/admin/events/index');
        } else {
            return abort(401, "No such event"); // Nie znaleziono wydarzenia
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
        if (!authAdmin()) {
            return abort(401, "You are not a moderator");
        }
        Event::find($id)->delete();
        return redirect('/admin/events/index');
    }
}
