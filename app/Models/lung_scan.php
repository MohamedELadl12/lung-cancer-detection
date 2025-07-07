<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class lung_scan extends Model
{
    protected $fillable = [
        'report_id',
        'img_path',
        'img_link',
        'class',
        'confidance',
    ];



    public function report(){
        return $this->belongsTo(report::class, 'report_id','id');
    }


    public static function insertScan(array $data,$image = null){
        $new = new self();
        $new->report_id = $data['report_id'];
        if($image != null){
            $path = $image->store('images', 'public');
            $new->img_path = $path;
            $new->img_link = Storage::url($path);
        }else{
            $new->img_path = null;
            $new->img_link = null;
        }
        
        $new->class = $data['class'] ?? null;
        $new->confidance = $data['confidance'] ?? null;
    }
}
