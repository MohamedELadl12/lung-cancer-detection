<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create( )
    {
        //
            

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreateRequest $request)
    {
        
        // Create a new user
        $x = User::createUser($request->all());
       if( $x instanceof User){
        try{
            $x->sendEmailVerificationNotification();
        }catch(\Exception $e){
            $x->deleteUser();
            return response()->json([
                'message' => 'Error creating user',
            ], 500);
        }
        
        return response()->json([
            'message' => 'User created successfully',
            'user' => $x
        ], 200);
       }else{
        return response()->json([
            'message' => 'Error creating user',
        ], 500);
       }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request)
    {

        $user = User::find(auth('api')->id());
        $user->updateUser($request->all());
        if( $user instanceof User){
            
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
           }else{
            return response()->json([
                'message' => 'Error updating user',
            ], 500);
           }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $user = User::find(auth('api')->id());
        $user->deleteUser();
        auth('api')->logout();
        return response()->json([
            'message' => 'User deleted successfully',
        ], 200);
    }


    public function verifyEmail(Request $request)
    {
        $user = User::find(auth('api')->id());
        if($user->email_verified_at != null){
            return response()->json([
                'message' => 'Email already verified',
            ], 200);
        }
        elseif($user->verifyEmail($request->otp)){
            return response()->json([
                'message' => 'Email has been verified',
            ], 200);
        }else{
            return response()->json([
                'message' => 'Error verifying email, try to get new otp',
            ], 500);
        }
        
    }


    public function newOtp()
    {
        $user = User::find(auth('api')->id());
        $user->sendEmailVerificationNotification();
        return response()->json([
            'message' => 'New OTP sent to your email',
        ], 200);
    }
}
