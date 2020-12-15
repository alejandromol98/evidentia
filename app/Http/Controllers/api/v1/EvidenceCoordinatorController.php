<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Evidence;
use App\ReasonRejection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvidenceCoordinatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkrolesapi:COORDINATOR|SECRETARY');
    }

    /****************************************************************************
     * MANAGE EVIDENCES
     ****************************************************************************/

    public function all($instance)
    {
        //$instance = \Instantiation::instance();

        $coordinator = auth('api')->user()->coordinator;
        $comittee = $coordinator->comittee;
        $evidences = $comittee->evidences_not_draft()->paginate(10);;

        return $evidences;
    }

    public function pending()
    {
        //$instance = \Instantiation::instance();

        $coordinator = auth('api')->user()->coordinator;
        $comittee = $coordinator->comittee;
        $evidences = $comittee->evidences_pending()->paginate(10);

        return $evidences;
    }

    public function accepted()
    {
        //$instance = \Instantiation::instance();

        $coordinator = auth('api')->user()->coordinator;
        $comittee = $coordinator->comittee;
        $evidences = $comittee->evidences_accepted()->paginate(10);

        return $evidences;
    }

    public function rejected()
    {
        //$instance = \Instantiation::instance();

        $coordinator = auth('api')->user()->coordinator;
        $comittee = $coordinator->comittee;
        $evidences = $comittee->evidences_rejected()->paginate(10);

        return $evidences;
    }

    public function accept($instance,$id)
    {
        //$instance = \Instantiation::instance();

        $evidence = Evidence::find($id);
        $userid = $evidence->user->id;
        if(auth('api')->id() == $userid){
            $evidence->status = 'ACCEPTED';
            $evidence->save();

            return response()->json('Evidencia aceptada con éxito.');
        }
        return response()->json('El usuario no tiene permisos.');
    }

    public function reject(Request $request,$instance,$id)
    {
        //$instance = \Instantiation::instance();

        $evidence = Evidence::find($id);
        $userid = $evidence->user->id;
        if(auth('api')->id() == $userid){
            $evidence->status = 'REJECTED';
            $evidence->save();

            $reasonrejection = ReasonRejection::create([
                'reason' => $request->input('reasonrejection'),
                'evidence_id' => $id
            ]);
            $reasonrejection->save();

            return response()->json('Evidencia rechazada con éxito.');
        }
        return response()->json('El usuario no tiene permisos.');
    }
}
