<?php
namespace App\Http\Controllers\api\v1;

use App\Bonus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class BonusController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('checkrolesapi:SECRETARY');
    }

    public function list($instance)
    {
        $secretary = auth('api')->user()->secretary;
        $comittee = $secretary->comittee;
        $bonus = $comittee->bonus()->get();


        return $bonus;
    }

    public function new(Request $request)
    {

        // $instance = \Instantiation::instance();

        $bonus = $this->new_bonus($request);

        return $bonus;

    }

    private function new_bonus($request)
    {

        $data = $request->all();

        $validator = Validator::make($data, [
            'reason' => 'required|min:5|max:255',
            'hours' => 'required|numeric|between:0.5,99.99|max:100',
            'users' => 'required|array|min:1'
        ]);

        if($validator -> fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ],401);
        }
        else {
        // creación de un nuevo bonus
        $bonus = Bonus::create([
            'reason' => $request->input('reason'),
            'hours' => $request->input('hours')
        ]);

        $bonus->comittee()->associate(auth('api')->user()->secretary->comittee);

        $bonus->save();

        // Asociamos los usuarios a la reunión

        $users_ids = $request->input('users',[]);

        foreach($users_ids as $user_id)
        {

            $user = User::find($user_id);
            $bonus->users()->attach($user);

        }

        return $bonus->toJson();
        }
    }
}
