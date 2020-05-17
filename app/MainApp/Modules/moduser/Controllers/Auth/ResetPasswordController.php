<?php

namespace App\MainApp\Modules\moduser\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use App\Base\BaseController;

class ResetPasswordController extends BaseController
{

    use AuthResponseTraits;

    public function __construct()
    {
        $this->middleware('guest');//->except('logout');
    }

    public function resetPassword(Request $request, $apps_code = '')
    {
        $data['email'] = $request->query('email');
        $data['verifyCode'] = $request->query('verifyCode');
        $data['setNewPassword'] = $request->query('setNewPassword',false);

        if(!UserRepo::varifyResetPasswordToken($data['email'],$data['verifyCode'])){
            return redirect()->route('auth.forgotPassword', ['apps_code'=>$apps_code,'error_message'=>UserRepo::error()]);
        }

        return view('auth.resetpassword', $data);
    }

    public function doResetPassword(Request $request, $apps_code = '')
    {
        $data['verifyCode'] = $request->input('verifyCode');
        $data['email'] = $request->input('email');        
        
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:5|max:255',
            'password_confirmation' => 'required|min:5|max:255|same:password',
        ]);
        
        //jika verified
        if(!UserRepo::varifyResetPasswordToken($data['email'],$data['verifyCode'],true)){
            return redirect()->route('auth.resetPassword.fail', ['apps_code'=>$apps_code,'error_message'=>UserRepo::error()]);
        }
        $user = UserRepo::getOne('email',$data['email']);
        
        //jika banned
        if($user['status']==2){
            return redirect()->route('auth.login', ['apps_code'=>$apps_code])->with('alert', ['type' => 'danger', 'message' => 'Login Failed. Account Banned.']);
        }else {
            //jika aktifkasi pertama kali
            if($user['status']==0){
                UserRepo::updateUser($user['id'],['status'=>1]);
            }
            UserRepo::resetPassword($user['id'],$request->input('password'));
        }
        
        return redirect()->route('auth.login', ['apps_code'=>$apps_code]);
    }
    
    public function verifyFail(Request $request, $apps_code = '')
    {
        return view('auth.resetpasswordfail', $request->all());
    }

}
