<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Meeting;
use App\User;

class RegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'meeting_id' => 'required',
            'user_id' => 'required'
        ]);

        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');

        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);

        if ($meeting->users()->where('users.id', $user->id)->first()) {
            return response()->json(['msg' => 'User already registered for meeting'], 404);
        }

        $meeting->users()->attach($user);

        return response()->json(['msg' => 'User registered for meeting'], 201);
    }

    public function destroy(Request $request, $id)
    {
        return $request->user_id;
        
        $meeting = Meeting::findOrFail($id);
        $meeting->users()->detach();

        return response()->json(['msg' => 'User unregistered for meeting'], 200);
    }
}
