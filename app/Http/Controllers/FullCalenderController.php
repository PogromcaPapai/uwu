<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;
use App\Models\Attendance;

class FullCalenderController extends Controller
{
	public function list(Request $request)
    {
      /*
      Funkcja do eventów
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

    public function index(Request $request)
    {
      /*
      Funkcja do eventów
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
    Requesty
    */
    {
    	if($request->ajax())
    	{
    		if($request->type == 'add')
    		{
    			$event = new Event;
				$event->title = $request->title;
				$event->start =	$request->start;
    			$event->end = $request->end;
				$event->save();
				Attendance::create([
					'is_admin'	=> TRUE,
					'event_id'	=> $event->id,
					'user_id'	=> Auth::id(),
				]);

    			return response()->json($event);
    		}

    		if($request->type == 'update')
    		{

    			$event = Event::join('attendances', 'events.id', '=', 'attendances.event_id')::find($request->id);
				if ($event->find(1)->is_admin) {
					$event->update([
						'title'		=>	$request->title,
						'start'		=>	$request->start,
						'end'		=>	$request->end
					]);
				};

    			return response()->json($event);
    		}

			if($request->type == 'delete')
    		{
    			$event = Attendance::where('user_id', '=', Auth::id())
					->where('event_id', '=', $request->id)
					->delete();
				if (Attendance::where('event_id', '=', $request->id)
					->where('is_admin', '=', '1')
					->count() == 0) 
					{
						Event::find($request->id)->delete();
					}

    			return response()->json($event);
    		}
    	}
    }
}
?>
