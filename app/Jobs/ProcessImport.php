<?php

namespace App\Jobs;

use App\Http\Controllers\ResponseFormatter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ProcessImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;
    public function __construct($request)
    {
        $this->data = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(isset($this->data)){
            switch($this->data){
                case "get-data" :
                    Artisan::call('get:data');
                    return ResponseFormatter::success(null, "Success", 200);
                default :
                    return ResponseFormatter::error(null, "Data Not Found", 404);
            }
        }else{
            return ResponseFormatter::error(null, "Data Not Found", 404);
        }
    }
}
