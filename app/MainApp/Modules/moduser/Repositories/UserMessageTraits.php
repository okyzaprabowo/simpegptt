<?php
namespace App\MainApp\Modules\moduser\Repositories;

use Illuminate\Notifications\Notification;
//use Illuminate\Support\Facades\Mail;
use App\MainApp\Modules\moduser\Models\User;
use App\MainApp\Modules\moduser\Models\UserOTP;
use App\MainApp\Modules\moduser\Models\PasswordReset;

trait UserMessageTraits     
{
    public function __construct(User $model)
    {
        $this->model = $model;        
    }

    /**
     * kirim email mengenai error atau waring ke seluruh super admin
     * 
     * @param string $title
     * @param mix $content
     * @return type
     */
    public function notifyWarning($title,$content)
    {
        //pilih semua level user system admin
        $users = User::where('role','LIKE',"%;1:%")->get();
        foreach ($users as $user) {
            $user->notify($user['id'], new \App\MainApp\Modules\moduser\Notifications\WarningReport($title,$content));
        }
        return true;
    }
    
    public function sendEmail($userId,$email)
    {        
        $user = User::find($userId);
        if($user)return false;
        Mail::to($user->email)->send($email);
        return true;
    }
    
    public function isMainEmail($userId,$email)
    {
        $user = User::find($userId);
        if(!$user)return false;        
        if($user->email == $email)return true;
        return false;
    }
    /**
     * =========================================================================
     */
    /**
     * 
     * @param type $userId
     * @param type $isSecondary
     * @return type
     */
    public function sendVerificationEmail($userId)
    {
        return $this->notify(
            $userId,
            new \App\MainApp\Modules\moduser\Notifications\EmailVerification($userId)
        );
    }    
    
    
    public function sendUserActivationEmail($userId)
    {
        return $this->notify(
            $userId,
            new \App\MainApp\Modules\moduser\Notifications\UserActivation($userId)
        );
    }

    /**
     * format data yg diperlukan untuk email verifikasi dan activasi
     * 
     * @param type $userId
     * @param type $isSecondary
     * 
     * @return array $userData array record data user
     *      name
     *      email
     *      verifyCode
     */
    public function verificationEmailDataFormat($userId,$isSecondary=false)
    {
        
        $user = User::find($userId);
        if(!$user)return false; 
        $userData = $user->toArray();
                
        if($isSecondary){
            $userProfile = UserProfile::where('user_id',$userId)->first();
            if($userProfile)return false; 
            $userData['email'] = $userProfile->email2;
        }
        
        $userData['verifyCode'] = $this->generateEmailVerfifyCode($userData['email']);
        $userData['verifyUrl'] = route('auth.emailVerification',[
            'email' => $userData['email'],
            'verifyCode' => $userData['verifyCode']
            ]);
        
        return $userData;
    }
    
    public function sendUserResetPasswordEmail($userId)
    {
        return $this->notify(
            $userId,
            new \App\MainApp\Modules\moduser\Notifications\UserResetPassword($userId) 
        );
    }
    
    public function resetPasswordEmailDataFormat($userId)
    {        
        $user = User::find($userId);
        if(!$user)return false; 
        $userData = $user->toArray();
        
        $userData['verifyCode'] = $this->generateEmailVerfifyCode($userData['email']);
        
        $userData['resetPasswordUrl'] = route('auth.resetPassword',[
            'email' => $userData['email'],
            'verifyCode' => $userData['verifyCode']
            ]);
        PasswordReset::create([
            'email' => $userData['email'],
            'token' => $userData['verifyCode']            
        ]);
        return $userData;
    }
    public function generateEmailVerfifyCode($email)
    {
        return hash('sha256',$email.'somesaltbrooooooo');
    }
    
    /**
     * OTP 
     * =========================================================================
     */
    
    /**
     * generate dan kirim kode OTP ke not
     * 
     * @param type $userId
     * @param booloean $isMainPhone 
     *      true jika dikirim ke phone di table user
     *      false jika dikirim ke phone2 di table user_profile
     * @return boolean
     */
    public function sendOTP($userId,$isMainPhone=true)
    {        
        $user = User::find($userId);
        if(!$user)return false;
        $phone = $isMainPhone ? $user->phone : $user->profile->phone2;
        // dd($user);
        $otpCode = $this->generateOTP($user->id,$phone);

        return $user->notify(new \App\MainApp\Modules\moduser\Notifications\SendOTP($otpCode,$phone));
    }
    
    /**
     * generate dan save kode OTP table, jika sebelum telah ada maka akan dihapus
     * terlebih dahulu
     * 
     * @param int $userId
     * @param string $phone
     * @return type
     */
    public function generateOTP($userId,$phone)
    {
        $otpData = UserOTP::where('phone',$phone)->first();
        if($otpData)UserOTP::where('phone',$phone)->delete();

        $otpCode = rand(1000,9999);
        $otp = UserOTP::create([
            'user_id' => $userId,
            'token' => $otpCode,
            'phone' => $phone,
            'timeout' => now()->addMinutes(5)
        ]);

        return $otpCode;
    }

    /**
     * Cek apakah otp valid, jika valid true dan kode otp langsung dihapus
     * 
     * @param type $phone
     * @param type $otpCode
     * @return boolean
     */
    public function isOTPValid($phone,$otpCode)
    {
        $otpData = UserOTP::where('phone',$phone)        
                ->where('token',$otpCode)
                ->first();
        
        //jika otp valid
        if($otpData){
            UserOTP::where('phone',$phone)->delete();
            return true;
        }
        return false;
    }
    
    /**
     * cek apakah telepon merupakan telepon utama dan ada
     * 
     * @param type $userId
     * @param type $phone
     * @return boolean
     */
    public function isMainPhone($userId,$phone)
    {
        $phone = UserRepo::phoneFormat($phone);
        $user = User::find($userId);
        if(!$user)return false;        
        if($user->phone == $phone)return true;
        return false;
    }
    
    /**
     * Notifikasi
     * =========================================================================
     */
    
    /**
     * send notifkasi 
     * 
     * @param type $userId
     * @param Notification $notification
     * @return type
     */
    public function notify($userId,Notification $notification)
    {
        $user = User::find($userId);
        if($user)return $user->notify($notification);
        return false;
    }
    // public function notifyInvoice($userId,$order)
    // {
    //     return $this->notify(
    //         $userId, 
    //         new \App\MainApp\Modules\moduser\Notifications\Invoice($order)
    //         );
    // }        
    
}