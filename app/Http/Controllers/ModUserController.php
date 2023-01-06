<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ModUserController extends Controller
{

    public function index(Request $request)
    {
        if (User::where('id', '=', Auth::id())->where('is_mod', '=', '1')->count() <= 0) {
            return abort(401, "You are not a moderator");
        }
        $users = User::get(['id', 'name', 'email', 'is_mod']);
        return view('/users/index', ['users' => $users]);
    }
    
    public function post(Request $request, int $id)
    {
        if (User::where('id', '=', Auth::id())->where('is_mod', '=', '1')->count() <= 0) {
            return abort(401, "You are not a moderator");
        }
        User::find($id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_mod' => $request->is_mod
        ]);
        if (strlen($request->password) > 0) {
            User::find($id)->update([
                'password' => Hash::make($request->password),
            ]);
        }
        return redirect('/admin/users/index');
    }
    public function destroy(int $id)
    {
        if (User::where('id', '=', Auth::id())->where('is_mod', '=', '1')->count() <= 0) {
            return abort(401, "You are not a moderator");
        }
        User::find($id)->delete();
        return redirect('/admin/users/index');
    }
}
