<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\DefaultList;
use App\Meeting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingSecretaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        //Modificado para mostrar error en la API
        $this->middleware('checkrolesapi:PRESIDENT|COORDINATOR|REGISTER_COORDINATOR|SECRETARY|STUDENT');
    }

    public function list()
    {
        if(auth('api')->user()->secretary) {

        $meetings = auth('api')->user()->secretary->comittee->meetings()->get();
        return $meetings;
        } else {

            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos para mostrar las reuniones del comité.'
            ], 403);

        }
    }
/*
    public function create()
    {
        $instance = \Instantiation::instance();

        $users = User::orderBy('surname')->get();
        $defaultlists = auth('api')->user()->secretary->default_lists;

        return view('meeting.createandedit',
            ['instance' => $instance, 'users' => $users, 'defaultlists' => $defaultlists, 'route' => route('secretary.meeting.new',$instance)]);
    }
*/
    public function new(Request $request)
    {

        if(auth('api')->user()->secretary) {

            $validatedData = $request->validate([
                'title' => 'required|min:5|max:255',
                'type' => 'required|numeric|min:1|max:2',
                'hours' => 'required|numeric|between:0.5,99.99|max:100',
                'place' => 'required|min:5|max:255',
                'date' => 'required|date_format:Y-m-d|before:today',
                'time' => 'required',
                'users' => 'required|array|min:1'
            ]);

            $meeting = Meeting::create([
                'title' => $request->input('title'),
                'hours' => $request->input('hours'),
                'type' => $request->input('type'),
                'place' => $request->input('place'),
                'datetime' => $request->input('date') . " " . $request->input('time')
            ]);

            $meeting->comittee()->associate(auth('api')->user()->secretary->comittee);

            $meeting->save();

            // Asociamos los usuarios a la reunión
            $users_ids = $request->input('users', []);

            foreach ($users_ids as $user_id) {

                $user = User::find($user_id);
                $meeting->users()->attach($user);

            }
            return $meeting;

        } else {

            return response()->json([
                'success' => false,
                'message' => 'El usuario es secretario, no puede crear una reunion.'
            ], 403);

        }
    }

    /*
    public function edit($instance,$id)
    {
        $meeting = Meeting::find($id);
        $users = User::orderBy('surname')->get();
        $defaultlists = Auth::user()->secretary->default_lists;

        return view('meeting.createandedit',
            ['instance' => $instance, 'meeting' => $meeting, 'edit' => true, 'users' => $users, 'defaultlists' => $defaultlists, 'route' => route('secretary.meeting.save',$instance)]);
    }

    */
    public function defaultlist($instance,$id)
    {
        return DefaultList::find($id)->users;
    }

    public function save(Request $request, $instance, $id)
    {

        if(auth('api')->user()->secretary) {

            $validatedData = $request->validate([
                'title' => 'required|min:5|max:255',
                'type' => 'required|numeric|min:1|max:2',
                'hours' => 'required|numeric|between:0.5,99.99|max:100',
                'place' => 'required|min:5|max:255',
                'date' => 'required',
                'time' => 'required',
                'users' => 'required|array|min:1'
            ]);

            $meeting = Meeting::find($id);
            $meeting->title = $request->input('title');
            $meeting->hours = $request->input('hours');
            $meeting->type = $request->input('type');
            $meeting->place = $request->input('place');
            $meeting->datetime = $request->input('date')." ".$request->input('time');

            $comittee_meeting = $meeting->comittee;

            $secretary = auth('api')->user()->secretary;
            $comittee_secretary = $secretary->comittee;

            if($comittee_meeting == $comittee_secretary) {
                $meeting->save();

                // Asociamos los usuarios a la reunión
                $users_ids = $request->input('users',[]);

                // eliminamos usuarios antiguos de la reunión
                foreach($meeting->users as $user)
                {
                    $meeting->users()->detach($user);
                }

                // agregamos los usuarios nuevos de la reunión
                foreach($users_ids as $user_id)
                {
                    $user = User::find($user_id);
                    $meeting->users()->attach($user);
                }

                return $meeting;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene permisos sobre este comité.'
                ], 403);
            }

        } else {

            return response()->json([
                'success' => false,
                'message' => 'El usuario es secretario, no puede editar una reunion.'
            ], 403);


        }

    }

    public function remove(Request $request, $instance, $id)
    {

        if(auth('api')->user()->secretary) {

            $meeting = Meeting::find($id);
            $comittee_meeting = $meeting->comittee;

            $secretary = auth('api')->user()->secretary;
            $comittee_secretary = $secretary->comittee;

            if($comittee_meeting == $comittee_secretary) {
                $meeting->delete();

                return response()->json('Reunión eliminada con éxito');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene permisos sobre este comité.'
                ], 403);
            }

        } else {

            return response()->json([
                'success' => false,
                'message' => 'El usuario no es secretario.'
            ], 403);

        }


    }
}
