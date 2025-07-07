<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class report extends Model
{
    protected $fillable = [
        'user_id',
        'age',
        'gender',
        'smoker',
        'yellow_fingers',
        'anxiety',
        'peer_pressure',
        'chronic_disease',
        'fatigue',
        'allergy',
        'wheezing',
        'alcohol_consuming',
        'coughing',
        'shortness_of_breath',
        'swallowing_difficulty',
        'chest_pain',
        'probability',
        'message',
    ];

    protected $with=[
        'scan'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function scan(){
        return $this->hasOne(lung_scan::class, 'report_id','id');
    }



    /**
     * Stores the probability and message in the database
     *
     * @param int $probability The probability of the user having lung cancer
     * @param string $message The message to be displayed to the user
     * @return void
     */
    public function insertResults($probability, $message){
        $this->probability = $probability;
        $this->message = $message;
        $this->save();
    }



    /**
     * Stores a new report in the database
     *
     * @param array $data The data to be stored in the report
     * @return self The newly created report
     */
    public static function storeReport(array $data){
        $new = new self();
        $new->user_id = $data['user_id'];
        $new->age = (integer) $data['age'];
        $new->gender= $data['gender'] == 'male' ? 'male' : 'female';
        $new->smoker= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->yellow_fingers= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->anxiety= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->peer_pressure= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->chronic_disease= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->fatigue= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->allergy= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->wheezing= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->alcohol_consuming= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->coughing= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->shortness_of_breath= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->swallowing_difficulty= filter_var($data['smoker'],FILTER_VALIDATE_BOOL);
        $new->chest_pain= filter_var($data['smoker'],FILTER_VALIDATE_BOOL); 
        $new->fill($data);
        $new->save();
        return $new;
    }



}
