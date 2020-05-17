<?php

namespace App\MainApp\Modules\moduser\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use App\Base\BaseController;

class VerificationController extends BaseController
{

    public function verify(Request $request)
    {
        $data['verifyCode'] = $request->query('verifyCode');
        $data['email'] = $request->query('email');
                        
        //jika verified
        if(!UserRepo::varifyEmail($data['email'],$data['verifyCode'])){
            return redirect()->route('auth.emailVerification.fail', ['error_message'=>UserRepo::error()]);
        }

        // $userData = UserRepo::getUser(['email'=>$data['email']]);
        // if($userData['status']==0){
        //     $userData = UserRepo::resetPasswordEmailDataFormat($userData['id']);
        //     return redirect($userData['resetPasswordUrl'].'&setNewPassword=true');
        // }else{
            return redirect()->route('auth.emailVerification.success');
        // }
        
    }
    
    public function verifySuccess(Request $request)
    {
        return view('user.auth.emailvalidatesuccess', $request->all());
    }
    
    public function verifyFail(Request $request)
    {
        return view('user.auth.emailvalidatefail', $request->all());
    }
    

}
