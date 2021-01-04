<?php


namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Apicontroller extends Controller
{
    public function evidences_list()
    {
        $response = Http::withBasicAuth('profesor1', 'profesor1')->get('http://www.evidentia-api.com/20/api/v1/evidences');

        $jsonData = $response->json();

        dd($jsonData);
    }
}
