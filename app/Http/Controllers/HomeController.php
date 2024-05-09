<?php

namespace App\Http\Controllers;

use Ramsey\Uuid\Uuid;

class HomeController extends Controller
{
    
    function index() {
        $id = Uuid::uuid4()->toString();
        $link = config("app.url");
        $link .= "/api/webhook/";
        $link .= $id;
        return view('welcome', [ "link" => $link, "webhook_id" => $id ]);
    }
}
