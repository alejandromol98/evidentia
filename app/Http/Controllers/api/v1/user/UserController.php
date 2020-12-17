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

        if(auth('api')->id() == $id()){
            $user = User::find($id);
            if($user){
                return $user;
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario seleccionado no existe.'
                ],400);
            }
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ],401);
        }

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
        if(auth('api')->id() == $id()){
            return $this->save($request, $id);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ],401);
        }
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
                'message' => 'El usuario seleccionado no existe.'
            ],400);
        }

        return $user;


    }

    /****************************************************************************
     * REMOVE AN USER
     ****************************************************************************/

    public function remove(Request $request, $instance, $id)
    {


        // Eliminamos todas las entradas de las entidades asociadas a usuario
        if(auth('api')->id() == $id()){
            $user = User::find($id);
            if($user){
                $this->delete_user($user);
                return response()->json('Se ha eliminado el usuario correctamente');
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario seleccionado no existe.'
                ],400);
            }
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ],401);
        }

    }

    private function delete_user($user){

        //$instance = \Instantiation::instance();

        $user->delete();

    }
}
