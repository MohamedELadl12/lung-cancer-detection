<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Tip extends Model
{
    //

    protected $fillable = [
        'title',
        'description',
        'image',
        'image_path',
        
    ];

    public function CreateTip(Request $data){
        $tip = new Tip();
        $tip->title = $data->title ;
        $tip->description = $data->description ?? null;
        if($data->hasFile('image')) {
            $path = $data->file('image')->store('images', 'public');
            $tip->image_path = $path;
            $tip->image = Storage::url($path);
        }

        $tip->save();
        return $tip;
    }

    public function UpdateTip(Request $data){
        $tip = Tip::find($data->id);
        if($tip){
            $tip->title = $data->title ;
            $tip->description = $data->description ?? null;
            if($data->hasFile('image')) {
                if(Storage::exists($tip->image_path)){
                    Storage::delete($tip->image_path);
                }
                $path = $data->file('image')->store('images', 'public');
                $tip->image_path = $path;
                $tip->image = Storage::url($path);
            }
            $tip->save();
            return $tip;
        }
        return null;
    }


    public function DeleteTip($id){
        $tip = Tip::find($id);
        if($tip){
            if(Storage::exists($tip->image_path)){
                Storage::delete($tip->image_path);
            }
            $tip->delete();
            return true;
        }
        return false;
    }

    public static function GetTip($id){
        $tip = Tip::find($id);
        if($tip){
            return $tip;
        }
        return null;
    }

     static public function GetAllTips(){
        $tips = Tip::all();
        return $tips;
    }
}
