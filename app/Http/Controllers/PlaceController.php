<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;

class PlaceController extends Controller
{
    public function search(Request $request)
    {
        return response()->json(Place::where('name', '=', )
    }
}
