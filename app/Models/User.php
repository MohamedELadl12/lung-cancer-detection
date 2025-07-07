<?php

namespace App\Models;

use App\Http\Controllers\AuthController;
use App\Mail\VerficationMail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'facebook_id',
    ];


    protected $with=[
        'reports',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function reports(){
        return $this->hasMany(report::class, 'user_id', 'id');
    }


     // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public static function createUser(array $data){
        try {
            $password = Hash::make($data['password']);
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = $password;
            $user->role = $data['role'] ?? 'user';
            if(!$user->save()) throw new \Exception('Error creating user');
            return $user;

        } catch (\Throwable $th) {
            
            return response()->json([
                'message' => 'Error creating user',
                'error' => $th->getMessage()
            ], 500);
        }
        

    }


    public static function createUserSocialate(array $data){

        try {
            $googleUser = User::where('email', $data['email'])->first();
            if($googleUser && $googleUser->google_id != null){
                $token = auth('api')->login($googleUser);
                $authController = new AuthController();
                $authController->respondWithToken($token);
                
            }elseif($googleUser && $googleUser->google_id == null){
                return response()->json([
                    'message' => 'User already exists',
                ], 200);
            }
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->google_id = $data['google_id'] ?? null;
            $user->email_verified_at = now();
            if( !$user->save() ) throw new \Exception('Error creating user');
           
            return $user;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error creating user',
                'error' => $th->getMessage()
            ], 500);
        }
        
    }



    public function updateUser(array $data){
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->save();
        return $this;
    }


    public function deleteUser(){
        $this->delete();
        return $this;
    }


    public function sendEmailVerificationNotification(){
        $otp = $this->otpGenerator();
        Cache::put('otp_'.$this->email, $otp, 60*5);
        Mail::to($this->email)->send(new VerficationMail($otp,$this->email));
    }


    private function otpGenerator(){
        $otp = '';
        // Generate a 5 digit OTP
        for($i=0; $i<5; $i++){
            $otp .= rand(0,9);
        }
        return $otp;
    }


    public function verfiyEmail($otp){
        $cachedOtp = Cache::get('otp_'.$this->email);
        if($cachedOtp == $otp){
            $this->email_verified_at = now();
            $this->save();
            // Delete the cached OTP
            Cache::forget('otp_'.$this->email);
            return true;
        }else{
            return false;
        }
    }


}
