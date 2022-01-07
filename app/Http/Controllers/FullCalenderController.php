<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;

class FullCalenderController extends Controller
{
	public function index(Request $request)
    {
      /*
      GET
      */
    	if($request->ajax())
    	{
			$found = Event::join('attendances', 'events.id', '=', 'attendances.event_id')
						->where('user_id', '=', Auth::id());
    		$data = $found->whereDate('start', '>=', $request->start)
                       ->whereDate('end',   '<=', $request->end)
                       ->get(['id', 'title', 'start', 'end']);
            return response()->json($data);
    	}
    	return view('full-calender');
    }

    public function action(Request $request)
    /*
    Requesty POST (pozostała tylko aktualizacja terminu)
    */
    {
    	if($request->ajax())
    	{
			$events = Event::join('attendances', 'events.id', '=', 'attendances.event_id')->where('attendances.user_id', '=', Auth::id())->where('events.id', $request->id);
			if ($events->where('events.id', $request->id)->count() > 0) {
				if ($events->first()->is_admin) {
					Event::where('id', $request->id)->update([
						'start'        =>    $request->start,
						'end'        =>    $request->end,
					]);
					return response();
				} else {
					return abort(405);
				}
			} else {
				return abort(401);
			}
    	}
    }
}
?>
