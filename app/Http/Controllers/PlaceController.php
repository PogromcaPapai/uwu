<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
    public function search(Request $request)
    {
        $found = Place::where(DB::raw('lower(name)'), 'like', strtolower($request->q)."%")->limit(5)->get(['id','name', 'desc', 'powiat']);
        return response()->json($found);
    }
}
