<?php

namespace App\Jobs;

use App\Models\lung_scan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ImageScan implements ShouldQueue
{
    use Queueable;
    private $image;
    private $report_id;
    /**
     * Create a new job instance.
     */
    public function __construct($image,$report_id = null)
    {
        $this->image = $image;
        $this->report_id = $report_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'image' => $this->image
        ];

        $response = Http::timeout(400)->post(env('FAST_API_ROOT_FOR_APP').'/predict/scan', $data);
        if($response->successful()){
            $data = [
                'report_id'=>$this->report_id,
                'class'=>$response->json()['class'],
                'confidance'=>(string)$response->json()['confidence'],
            ];

            lung_scan::insertScan($data,$this->image);


            
        }
    }
}
