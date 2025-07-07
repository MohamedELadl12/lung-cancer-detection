<?php

namespace App\Jobs;

use App\Models\report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ReportJob implements ShouldQueue
{
    use Queueable;

    private $report;
    private $image;
        // 'age',
        // 'gender',
        // 'smoker',
        // 'yellow_fingers',
        // 'anxiety',
        // 'peer_pressure',
        // 'chronic_disease',
        // 'fatigue',
        // 'allergy',
        // 'wheezing',
        // 'alcohol_consuming',
        // 'coughing',
        // 'shortness_of_breath',
        // 'swallowing_difficulty',
        // 'chest_pain',

   
    /**
     * Create a new job instance.
     */
    public function __construct(report $report,$image = null)
    {
        $this->report = $report;
        $this->image = $image;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'GENDER' => $this->report->gender === 'male' ? 'M' : 'F',
            'AGE'=>(integer)$this->report->age,
            'SMOKING'=>$this->report->smoker == true ? 1 : 2,
            'YELLOW_FINGERS'=>$this->report->yellow_fingers == true ? 1 : 2,
            'ANXIETY'=>$this->report->anxiety == true ? 1 : 2,
            'PEER_PRESSURE'=>$this->report->peer_pressure == true ? 1 : 2,
            'CHRONIC_DISEASE'=>$this->report->chronic_disease == true ? 1 : 2,
            'FATIGUE'=>$this->report->fatigue == true ? 1 : 2,
            'ALLERGY'=>$this->report->allergy == true ? 1 : 2,
            'WHEEZING'=>$this->report->wheezing == true ? 1 : 2,
            'ALCOHOL_CONSUMING'=>$this->report->alcohol_consuming == true ? 1 : 2,
            'COUGHING'=>$this->report->coughing == true ? 1 : 2,
            'SHORTNESS_OF_BREATH'=>$this->report->shortness_of_breath == true ? 1 : 2,
            'SWALLOWING_DIFFICULTY'=>$this->report->swallowing_difficulty == true ? 1 : 2,
            'CHEST_PAIN'=>$this->report->chest_pain == true ? 1 : 2,
        ];


        $response = Http::timeout(400)->post(env('FAST_API_ROOT_FOR_MAIN').'/predict', $data);
        if($response->successful()){
            $this->report->insertResults($response->json()['probability'], $response->json()['message']);
            ImageScan::dispatch($this->image,$this->report->id);
        }



        
        
    }
}
