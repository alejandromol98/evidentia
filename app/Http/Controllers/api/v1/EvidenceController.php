<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Comittee;
use App\Evidence;
use App\File;
use App\Proof;
use App\Rules\MaxCharacters;
use App\Rules\MinCharacters;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class EvidenceController extends Controller
{
    public function __construct()
    {
        //middleware('auth:api');
        //$this->middleware('checkrolesapi:PRESIDENT|COORDINATOR|REGISTER_COORDINATOR|SECRETARY|STUDENT');
    }


    public function view($instance,$id)
    {
        $evidence = Evidence::find($id);
        $userid = $evidence->user->id;

        if(auth('api')->id() == $userid){
            return $evidence;
        }


        return response()->json([
            'success' => false,
            'message' => 'El usuario no tiene permisos.'
        ], 403);

    }

    public function list()
    {
        $evidences = Evidence::where(['user_id' => auth('api')->id(),'last' => true])->orderBy('created_at', 'desc')->get();
        //$instance = \Instantiation::instance();
        if($evidences){
            return $evidences;
        }

        return response()->json([
            'success' => false,
            'message' => 'El usuario no tiene evidencias.'
        ], 403);


    }

    /****************************************************************************
     * CREATE AN EVIDENCE
     ****************************************************************************/

    /**public function create()
    {
        $instance = \Instantiation::instance();
        $comittees = Comittee::all();

        return view('evidence.createandedit', ['route_draft' => route('evidence.draft',$instance),
            'route_publish' => route('evidence.publish',$instance),
            'instance' => $instance,
            'comittees' => $comittees]);
    }
    **/

    public function draft(Request $request)
    {
        return $this->new($request,"DRAFT");
    }

    public function publish(Request $request)
    {
        return $this->new($request,"PENDING");
    }

    private function new($request,$status)
    {

        // $instance = \Instantiation::instance();

        $evidence = $this->new_evidence($request,$status);

        $this->new_files($request,$evidence);

        return $evidence;

    }

    private function new_evidence($request,$status)
    {
        $request->validate([
            'title' => 'required|min:5|max:255',
            'hours' => 'required|numeric|between:0.5,99.99|max:100',
            'description' => ['required',new MinCharacters(10),new MaxCharacters(20000)],
        ]);

        // datos necesarios para crear evidencias
        $user = auth('api')->user();
        //$instance = \Instantiation::instance();

        // creación de una nueva evidencia
        $evidence = Evidence::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'hours' => $request->input('hours'),
            'status' => $status,
            'user_id' => $user->id,
            'comittee_id' => $request->input('comittee')
        ]);

        // cómputo del sello
        $evidence = \Stamp::compute_evidence($evidence);
        $evidence->save();

        return $evidence;
    }

    private function new_files($request,$evidence)
    {
        $user = auth('api')->user();
        $instance = \Instantiation::instance();

        // creación de la prueba o pruebas adjuntas
        $files = $request->file('files');
        if($files) {
            foreach ($files as $file) {

                // almacenamos en disco la prueba
                $path = Storage::putFileAs($instance . '/proofs/' . $user->username . '/evidence_' . $evidence->id . '', $file, $file->getClientOriginalName());

                // almacenamos en la BBDD la información del archivo
                $file_entity = File::create([
                    'name' => $file->getClientOriginalName(),
                    'type' => strtolower($file->getClientOriginalExtension()),
                    'route' => $path,
                    'size' => $file->getSize(),
                ]);

                // cómputo del sello
                $file_entity = \Stamp::compute_file($file_entity);
                $file_entity->save();

                // almacenamos en la BBDD la información de la prueba de la evidencia
                $proof = Proof::create([
                    'evidence_id' => $evidence->id,
                    'file_id' => $file_entity->id
                ]);
            }
        }
    }

    private function copy_files($evidence_previous, $evidence_new, $removed_files)
    {
        $user = auth('api')->user();
        $instance = \Instantiation::instance();

        foreach($evidence_previous->proofs as $proof){

            $file = $proof->file;

            // los archivos que hemos "eliminado" de la evidencia anterior no se incluyen en la nueva
            $collection = Str::of($removed_files)->explode('|');
            if($collection->contains($file->id))
                continue;

            try {

                // copiamos el archivo en sí
                Storage::copy($file->route, $instance . '/proofs/' . $user->username . '/evidence_' . $evidence_new->id . '/' . $file->name . '.' . $file->type);

                // almacenamos en la BBDD la información del archivo
                $file_entity = File::create([
                    'name' => $file->name,
                    'type' => $file->type,
                    'route' => $file->route,
                    'size' => $file->size,
                ]);

                // cómputo del sello
                $file_entity = \Stamp::compute_file($file_entity);
                $file_entity->save();

                // almacenamos en la BBDD la información de la prueba de la evidencia
                $proof = Proof::create([
                    'evidence_id' => $evidence_new->id,
                    'file_id' => $file_entity->id
                ]);

            } catch (\Exception $e) {

            }
        }
    }


    /****************************************************************************
     * EDIT AN EVIDENCE
     ****************************************************************************/

    /**  public function edit($instance,$id)
     * {
     * $evidence = Evidence::find($id);
     * $comittees = Comittee::all();
     *
     * return view('evidence.createandedit', ['evidence' => $evidence, 'instance' => $instance,
     * 'comittees' => $comittees,
     * 'edit' => true,
     * 'route_draft' => route('evidence.draft.edit',$instance),
     * 'route_publish' => route('evidence.publish.edit',$instance)]);
     * }
     * @param Request $request
     * @return
     */

    public function draft_edit(Request $request,$instance,$id)
    {
        if(auth('api')->id() == $id){
            return $this->save($request,"DRAFT",$id);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ], 403);
        }

    }

    public function publish_edit(Request $request,$instance,$id)
    {
        if(auth('api')->id() == $id){
            return $this->save($request,"PENDING",$id);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ], 403);
        }
    }


    private function save($request,$status,$id){

            $request->validate([
                'title' => 'required|min:5|max:255',
                'hours' => 'required|numeric|between:0.5,99.99|max:100',
                'description' => ['required', new MinCharacters(10), new MaxCharacters(20000)],
            ]);

            $evidence = Evidence::find($id);
            $userid = $evidence->user->id;
            if(auth('api')->id() == $userid){
                if ($evidence->status = "DRAFT") {

                    $evidence->status = $status;

                    $evidence_new = $evidence->fill($request->all())->save();

                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Evidence in status PENDING can not be updated'
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene permisos.'
                ], 403);
            }

        return $evidence;
    }

    /****************************************************************************
     * REMOVE AN EVIDENCE
     ****************************************************************************/

    public function remove(Request $request,$instance,$id)
    {
        $evidence = Evidence::find($id);
        $userid = $evidence->user->id;
        if(auth('api')->id() == $userid){
            // eliminamos recursivamente la evidencia y todas las versiones anteriores, incluyendo archivos
            $this->delete_evidence($evidence);

            return response()->json('Eliminada con éxito');

        } else {

            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ], 403);
        }

    }

    private function delete_evidence($evidence)
    {
        $instance = \Instantiation::instance();
        $user = auth('api')->user();

        // por si la evidencia apunta a otra anterior
        $evidence_previous = Evidence::find($evidence->points_to);

        // eliminamos los archivos almacenados
        $this->delete_files($evidence);
        Storage::deleteDirectory($instance.'/proofs/'.$user->username.'/evidence_'.$evidence->id.'');
        $evidence->delete();

        if($evidence_previous != null)
        {
            $this->delete_evidence($evidence_previous);
        }
    }

    private function delete_files($evidence)
    {
        foreach($evidence->proofs as $proof)
        {
            $proof->file->delete();
        }
    }

    /****************************************************************************
     * REEDIT AN EVIDENCE
     ****************************************************************************/

    public function reedit(Request $request,$instance,$id)
    {
        $evidence = Evidence::find($id);
        $userid = $evidence->user->id;
        if(auth('api')->id() == $userid){

            $evidence->status = "DRAFT";

            $evidence->save();

            return response()->json( 'Evidencia reasignada como borrador con éxito.');

        } else {

            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos.'
            ], 403);
        }

    }

}
