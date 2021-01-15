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
        $this->middleware('checkrolesapi:SECRETARY');
    }

    public function list()
    {
        //$instance = \Instantiation::instance();
        $defaultlists = auth('api')->user()->secretary->default_lists()->get();
        return $defaultlists;
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
    }
}
