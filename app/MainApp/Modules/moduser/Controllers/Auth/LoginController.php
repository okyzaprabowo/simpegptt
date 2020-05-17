<?php

namespace App\MainApp\Modules\moduser\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Log;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Repositories\UserNotifRepo;
use Facades\App\MainApp\Modules\moduser\Repositories\RoleRepo;

use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

//use Carbon\Carbon;

use App\Base\BaseController;

class LoginController extends BaseController
{

    use ThrottlesLogins,
        AuthResponseTraits;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function username()
    {
        return ['username','email'];
    }

    protected function redirectTo()
    {
        return route('dashboard');
    }

    /**
     * halaman web login
     */
    public function login(Request $request)
    {
        $data['backlink'] = $request->input('backlink');
        
        return view('auth.login', $data);
    }

    public function doLogin(Request $request)
    {
        $returnParam['backlink'] = $response['backlink'] = $request->input('backlink')?$request->input('backlink'):config('cur_apps.home_url');
        $response['reff'] = 'login';
        
        $request->validate([
            'username' => 'required|min:3|max:255',
            'password' => 'required|min:3|max:255'
        ]);

        $authData = $request->only('username', 'password');
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            Log::info('Login Failed ! user : "'.$authData['username'].'" - password : "'.$authData['password'].'"');
            Log::info('Too many login attemp');
            return $this->sendLockoutResponse($request);
        }

        $tenantId = config('tenant.id');
        if (config('AppConfig.system.web_admin.multitenant.autodetect_login')==1) $tenantId = null;
        
        if($user = UserRepo::loginCheck($authData['username'],$authData['password'], $tenantId)){
            if (Auth::attempt(
                    ['username'=>$user['username'],'password'=>$authData['password']], $request->filled('remember')
                ) || 
                Auth::attempt(
                    ['email'=>$user['email'],'password'=>$authData['password']], $request->filled('remember')
                )) {
                
                //$request->session()->regenerate();
                $this->clearLoginAttempts($request);
                $userData = Auth::user();
                
                //jika di banned
                if($userData->status==2){
                    return redirect()->route('auth.login', $returnParam)->with('alert', ['type' => 'danger', 'message' => 'Login Failed. Account Banned.']);
                //jika pertama kali aktifikasi
                }else if($userData->status==0){
                    UserRepo::activateUser($userData->id);
                }
                
                UserAuth::setUser($userData->id);
                
                $response['isLogin'] = 1;
                $response['token'] = UserAuth::getToken();
                return $this->authDone(                  
                    $response,
                    route('dashboard')
                );
            }else{
                Log::info('Login Failed ! user : "'.$authData['username'].'" - password : "'.$authData['password'].'"');
            }
        }else{
            Log::info('Login Failed ! user : "'.$authData['username'].'" - password : "'.$authData['password'].'"');
            Log::info(UserRepo::error());
        }

        $this->incrementLoginAttempts($request);

        return redirect()->route('auth.login', $returnParam)->with('alert', ['type' => 'warning', 'message' => '<b>Login gagal !</b> pastikan username dan password telah sesuai, termasuk kapital tidaknya huruf yang digunakan.']);
    }
    
    public function logout(Request $request)
    {
        
        UserAuth::unsetUser();
        Auth::logout();
        
        //$request->session()->invalidate();
        $response['backlink'] = $request->input('backlink')?$request->input('backlink'):config('cur_apps.home_url');
        $response['reff'] = 'logout';
        $response['isLogin'] = 0;
        
        return $this->authDone($response,route('auth.login'));
    }
    
    /**
     * API
     * =========================================================================
     */
    
    /**
     * API OUTPUT ONLY
     * create token user
     * 
     * @param Request $request
     *      username
     *      password
     *      
     * @return json array
     */
    public function apiLogin(Request $request)
    {
        $this->forceApiOutput();

        $authParam = $request->only('username', 'password');

        if(!isset($authParam['username']) ||!isset($authParam['password'])){
            $this->setError(__('alert.incorect_parameter'));
            return $this->done();
        }        

        $tenantId = config('tenant.id');
        if (config('AppConfig.system.web_admin.multitenant.autodetect_login')==1) $tenantId = null;

        if($user = UserRepo::loginCheck($authParam['username'],$authParam['password'], $tenantId)){
            $pushParam = false;
            if($request->input('pushNotifToken')){
                $pushParam = [
                    'token' => $request->input('pushNotifToken'),
                    'type' => $request->input('pushType',1)
                ];
            }
            $token = UserRepo::generateToken($user['id'],1,$request->input('deviceId',''),$pushParam);
            $this->output['message'] = __('alert.auth_success');
            $this->output['data'] = UserAuth::getCurTimeStamp();
            $this->output['data']['token'] =$token['api_token'];            
            $this->output['data']['user'] = $user;

            if(isset($user['tenant']))$this->output['data']['tenant'] = $user['tenant'];
            
            $this->output['data']['role'] = UserRepo::getUserRole($user['id']);
            foreach($this->output['data']['role'] as $key => $val) {
                if($val['is_main_role']){
                    $this->output['data']['role_code'] = $key;
                }
            }          
            
            // if(!isset($this->output['data']['role_code'])){
            //     $tmp = explode(';', trim($user['role'],";"));
            //     $this->output['data']['role_code'] = $tmp[0];
            // }
            
            //subscribekan ke channel/topic berdasarkan user role nya
            if($request->input('pushNotifToken')){
                $notifChannel[] = 'all';
                // if($response['data']['userData']['is_admin'])$notifChannel[] = 'admin';
                                
                UserNotifRepo::subscribeToChannel($notifChannel,$pushParam['token']);
            }
            return $this->done();
        }
        $this->setError(__('alert.auth_failed'));
        return $this->done();
    }
    
    /**
     * 
     * @param Request $request
     */
    public function apiLogout(Request $request)
    {
        
    }
}
