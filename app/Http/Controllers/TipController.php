<?php

namespace App\Http\Controllers;

use App\Models\Tip;
use Illuminate\Http\Request;

class TipController extends Controller
{
   public function GetAll(){
        $data = Tip::all()->select('id','title','description','image');
        return response()->json($data);
   }


    public function GetTip($id){
          $data = Tip::find($id)->select('id','title','description','image');
          if($data){
                return response()->json($data);
          }
          return response()->json(['message' => 'Tip not found'], 404);
    }


    public function createTip(Request $data){
       $x = Tip::CreateTip($data);
        if( $x instanceof Tip){
            return response()->json([
                'message' => 'Tip created successfully',
                'tip' => $x
            ]);
        }else{
            return response()->json([
                'message' => 'Failed to create tip'
            ], 500);
        }
    }


    public function updateTip(Request $data){
        $x = Tip::UpdateTip($data);
        if( $x instanceof Tip){
            return response()->json([
                'message' => 'Tip updated successfully',
                'tip' => $x
            ]);
        }else{
            return response()->json([
                'message' => 'Failed to update tip'
            ], 500);
        }
    }

    public function deleteTip($id){
        $x = Tip::DeleteTip($id);
        if($x){
            return response()->json([
                'message' => 'Tip deleted successfully'
            ]);
        }else{
            return response()->json([
                'message' => 'Failed to delete tip'
            ], 500);
        }
    }


}
