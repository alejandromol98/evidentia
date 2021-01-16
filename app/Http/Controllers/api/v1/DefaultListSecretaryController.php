<?php


namespace App\Http\Controllers\api\v1;


use App\Http\Controllers\Controller;
use App\DefaultList;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DefaultListSecretaryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkrolesapi:PRESIDENT|COORDINATOR|REGISTER_COORDINATOR|SECRETARY|STUDENT');
    }

    public function list()
    {
        //$instance = \Instantiation::instance();

        if(auth('api')->user()->secretary) {
            $defaultlists = auth('api')->user()->secretary->default_lists()->get();
            return $defaultlists;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ], 403);
        }
    }

    /*public function create()
    {
        $instance = \Instantiation::instance();

        $users = User::orderBy('surname')->get();

        return view('defaultlist.createandedit',
            ['instance' => $instance, 'users' => $users, 'route' => route('secretary.defaultlist.new',$instance)]);
    } */

    public function new(Request $request)
    {
        // $instance = \Instantiation::instance();

        if(auth('api')->user()->secretary) {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'users' => 'required|array|min:1',
            ]);

            $secretary = Auth::user()->secretary;

            $defaultlist = DefaultList::create([
                'name' => $request->input('name'),
                'secretary_id' => $secretary->id
            ]);

            $defaultlist->save();

            // Asociamos los usuarios a la reunión
            $users_ids = $request->input('users',[]);

            foreach($users_ids as $user_id)
            {

                $user = User::find($user_id);
                $defaultlist->users()->attach($user);

            }
            return $defaultlist;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ], 403);
        }
    }

    /*public function edit($instance,$id)
    {

        $instance = \Instantiation::instance();
        $defaultlist = DefaultList::find($id);
        $users = User::orderBy('surname')->get();

        return view('defaultlist.createandedit',
            ['instance' => $instance, 'defaultlist' => $defaultlist,
                'users' => $users, 'route' => route('secretary.defaultlist.save',$instance), 'edit' => true]);

    } */

    public function save(Request $request, $instance, $id)
    {

        //$instance = \Instantiation::instance();

        $validatedData = $request->validate([
            'name' => 'required|max:255',
        ]);

        $defaultlist = DefaultList::find($id);
        $defaultlist->name = $request->input('name');

        $userid = $defaultlist->secretary->user->id;

        if(auth('api')->user()->secretary && auth('api')->id() == $userid) {
            $defaultlist->save();

            // Asociamos los usuarios a la reunión
            $users_ids = $request->input('users',[]);

            // eliminamos usuarios antiguos de la reunión
            foreach($defaultlist->users as $user)
            {
                $defaultlist->users()->detach($user);
            }

            // agregamos los usuarios nuevos de la reunión
            foreach($users_ids as $user_id)
            {
                $user = User::find($user_id);
                $defaultlist->users()->attach($user);
            }
            return $defaultlist;
        }  else {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ], 403);
        }
    }

    public function remove(Request $request, $instance, $id)
    {
        //$instance = \Instantiation::instance();

        $defaultlist = DefaultList::find($id);
        $userid = $defaultlist->secretary->user->id;

        if(auth('api')->user()->secretary && auth('api')->id() == $userid) {
            $defaultlist->delete();
            return response()->json('Lista por defecto eliminada con éxito');
        }  else {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ], 403);
        }
    }
}
