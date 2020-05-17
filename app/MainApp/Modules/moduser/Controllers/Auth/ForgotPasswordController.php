<?php

namespace App\MainApp\Modules\moduser\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;

use App\Base\BaseController;

class ForgotPasswordController extends BaseController
{

    use AuthResponseTraits;
    
    public function __construct()
    {
        $this->middleware('guest');//->except('logout');
    }

    public function forgotPassword(Request $request, $apps_code = '')
    {
        $data['backlink'] = $request->query('backlink');
        return view('auth.forgotpassword', $data);
    }

    /**
     * 
     * @param Request $request
     * @param type $apps_code
     * @return type
     */
    public function doForgotPassword(Request $request, $apps_code = '')
    {        
        $this->output['data']['email'] = $request->input('email');
        $this->response = 'auth.forgotpasswordsuccess';
        
        $user = UserRepo::getUser(['email', $this->output['data']['email']]);
        
        if(!$user || $user['status'] != 1){
            $this->setError('Email tidak terdaftar.',false,400);
            $this->response = redirect(url()->previous())->withInput();
            return $this->done();            
        }

        
        UserRepo::sendUserResetPasswordEmail($user['id'],config('cur_apps.id'));
        $this->output['message'] = 'Email instruksi forgot password telah dikirim ke email Anda.';
        return $this->done();
    }

}
