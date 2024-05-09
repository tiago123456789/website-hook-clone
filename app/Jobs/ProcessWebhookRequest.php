<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Requests;


class ProcessWebhookRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $request = new Requests;

        $request->webhook_id = $this->data["webhook_id"];
        $request->url = $this->data["url"];
        $request->method = $this->data["method"];
        $request->query = json_encode($this->data["query"]);
        $request->body = json_encode($this->data["body"]);

        $header = [];
        foreach ($this->data["header"] as $key => $innerArray) {
            $header[$key] = $this->data["header"][$key][0];
        }
    
        $request->header = json_encode($header);
        $request->save();
    }

    public function failed($exception)
    {
        echo $exception->getMessage();
        return $exception;
    }

}
