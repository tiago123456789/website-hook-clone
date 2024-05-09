<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessWebhookRequest;
use App\Models\Requests;

class WebhookController extends Controller
{
    function index(Request $request, $uuid)
    {
        $data = [
            "webhook_id" => $uuid,
            "url" => $request->url(),
            "method" => $request->method(),
            "header" => $request->header(),
            "query" => $request->query(),
            "body" => $request->all()
        ];

        ProcessWebhookRequest::dispatch($data);
        return response()->json([], 202);
    }

    function getLastRequests(Request $request, $uuid)
    {
        $lastRequestAt = $request->query('lastRequestAt');
        $requests = Requests::where([
            ["webhook_id", $uuid],
            ["created_at", ">", $lastRequestAt]
        ])
            ->orderByDesc("created_at")
            ->get();
        return response()->json($requests, 200);
    }
}
