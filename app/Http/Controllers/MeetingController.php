<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Meeting;

use Carbon\Carbon;

class MeetingController extends Controller
{
	public function __construct()
	{
		$this->middleware('jwt.auth', ['only' => [
			'store', 'update', 'destroy'
		]]);
	}

	public function index()
	{
		$meetings = Meeting::all();

		$response = [
			'msg' => 'List of all meetings',
			'result' => $meetings
		];

		return response()->json($response, 200);
	}

	public function store(Request $request)
	{

		$this->validate($request, [
			"title" => "required",
			"description" => "required",
			"time" => "required|date_format:YmdHie"
		]);

		$title = $request->input('title');
		$description = $request->input('description');
		$time = $request->input('time');
		$user_id = $request->user_id;

		$meeting = new Meeting([
			'title' => $title,
			'description' => $description,
			'time' => Carbon::createFromFormat('YmdHie', $time)
		]);

		if ($meeting->save()) {
			$meeting->users()->attach($user_id);
			$meeting->view_meeting = [
				'href' => 'api/v1/meeting/' . $meeting->id,
				'method' => 'GET'
			];
		}

		$response = [
			'msg' => 'Meeting created',
			'result' => $meeting
		];

		return response()->json($response, 201);
	}

	public function show($id)
	{
		$meeting = Meeting::with('users')->where('id', $id)->firstOrFail();

		$response = [
			'msg' => 'Meeting information',
			'result' => $meeting
		];

		return response()->json($response, 200);
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			"title" => "required",
			"description" => "required",
			"time" => "required|date_format:YmdHie",
			"user_id" => "required"
		]);

		$title = $request->input('title');
		$description = $request->input('description');
		$time = $request->input('time');
		$user_id = $request->input('user_id');
		
		$meeting = Meeting::with('users')->findOrFail($id);

		if (!$meeting->users()->where('users.id', $user_id)->first()) {
			return response()->json(['msg' => 'user not registered for meeting, update failed'], 401);
		}

		$meeting->title = $title;
		$meeting->description = $description;
		$meeting->time = Carbon::createFromFormat('YmdHie', $time);

		if (!$meeting->update()) {
			return response()->json(['msg' => 'an error occured'], 404);
		}

		$response = [
			'msg' => 'Meeting Updated',
			'result' => $meeting
		];

		return response()->json($response, 200);


	}

	public function destroy($id) 
	{
		$meeting = Meeting::findOrFail($id);
		$meeting->users()->detach();

		if (!$meeting->delete()) {
			$users = $meeting->users;
			foreach ($users as $user) {
				$meeting->users()->attach($user);
			}
			return response()->json(['msg' => 'Deletion faild'], 404);
		}

		$response = [
			'msg' => 'Meeting deleted',
			'create' => [
				'href' => 'api/v1/meeting',
				'method' => 'POST'
			]
		];

		return response()->json($response, 200);

	}
}