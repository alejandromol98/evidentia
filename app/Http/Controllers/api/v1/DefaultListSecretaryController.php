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
}
