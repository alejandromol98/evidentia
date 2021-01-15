<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Meeting;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkrolesapi:PRESIDENT|COORDINATOR|REGISTER_COORDINATOR|SECRETARY|STUDENT');
    }

    public function list()
    {
        //$instance = \Instantiation::instance();

        $meetings = auth('api')->user()->meetings()->get();

        return $meetings;
    }
}
