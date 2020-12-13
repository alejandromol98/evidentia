<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Rules\MaxCharacters;
use App\Rules\MinCharacters;
class UserController extends Controller
{
    /****************************************************************************
     * LIST ALL USERS
     ****************************************************************************/

    public function index()
    {
        return User::all();
    }

    /****************************************************************************
     * SHOW AN USER
     ****************************************************************************/

    public function view($instance,$id)
    {
        $user = User::find($id);
        return $user;
    }

    /****************************************************************************
     * CREATE AN USER
     ****************************************************************************/

    public function new(Request $request)
    {

        // $instance = \Instantiation::instance();

        $user = $this->new_user($request);

        return $user->toJson();

    }

    private function new_user($request)
    {

        $request->validate([
            'name' => 'required|min:5|max:255',
            'username' => 'required|min:5|max:255',
            'password' => 'required|min:8|max:255',
            'email' => 'required|string',
            'biography' => ['required',new MinCharacters(10),new MaxCharacters(20000)],
        ]);

        // creación de un nuevo usuario
        $user = User::create([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'email' => $request->input('email'),
            'dni' => $request->input('dni'),
            'participation' => $request->input('participation'),
            'biography' => $request->input('biography')
        ]);

        $user->save();

        return $user;
    }

    /****************************************************************************
     * EDIT AN USER
     ****************************************************************************/

    public function edit(Request $request, $instance,$id)
    {
        return $this->save($request, $id);
    }


    public function save($request, $id)
    {
        $request->validate([
            'name' => 'required|min:5|max:255',
            'username' => 'required|min:5|max:255',
            'password' => 'required|min:8|max:255',
            'email' => 'required|string',
            'biography' => ['required',new MinCharacters(10),new MaxCharacters(20000)],
        ]);

        $user = User::find($id);
        if($user){
            $user_new = $user->fill($request->all())->save();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User selected doesn´t exist.'
            ],500);
        }

        return $user;
    }

    /****************************************************************************
     * REMOVE AN USER
     ****************************************************************************/

    public function remove(Request $request, $instance, $id)
    {
        $user = User::find($id);

        // Eliminamos todas las entradas de las entidades asociadas a usuario
        $this->delete_user($user);

        return response()->json('Se ha eliminado el usuario correctamente');
    }

    private function delete_user($user){

        //$instance = \Instantiation::instance();

        $user->delete();

    }
}
