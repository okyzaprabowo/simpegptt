<?php

namespace App\MainApp\Modules\moduser\Controllers\Auth;

use Facades\App\Services\AcSSOService;
use Facades\App\MainApp\Modules\moduser\Services\SocialAuthSrv;
use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Repositories\AppsRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Base\BaseController;

class SocialSignOnController extends BaseController
{

    use AuthResponseTraits;

    
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    /**
     * Redirect ke Oauth2 Provider
     * 
     * @param Request $request
     * @param type $apps_code
     * @param type $provider
     * @return type
     */
    public function redirectToProvider(Request $request, $apps_code = '', $provider)
    {
        $with['apps_code'] = $apps_code;
        $with['backlink'] = $request->query('backlink');

        return Socialite::driver($provider)
                ->with($with)
                ->redirect();
    }

    /**
     * Obtain the user information from provider.  Check if the user already exists in our
     * database by looking up their provider_id in the database.
     * If the user exists, log them in. Otherwise, create a new user then log them in. After that
     * redirect them to the authenticated users homepage.
     *
     * @return Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        $returnParam['apps_code'] = $request->input('apps_code')?$request->input('apps_code'):config('bssystem.default_apps.apps_code');
        
        $appsData = AppsRepo::getApps('apps_code',$returnParam['apps_code']);
        config(['cur_apps' => $appsData]);
        
        $returnParam['backlink'] = $response['backlink'] = $request->input('backlink')?$request->input('backlink'):config('cur_apps.home_url');
                        
        //jika ditolak
        if (!$request->has('code') || $request->has('denied')) {
            return redirect()->route(
                    'auth.login', 
                    $returnParam)->with('alert', ['type' => 'warning', 'message' => 'Authentification with <b>'.strtoupper($provider).'</b> Denied']
                );
        }
                
        $user = Socialite::driver($provider)->user();
        
        $userData = SocialAuthSrv::getUserBySocnetId($user->getId(),$provider);
        
        $apiTokenData = false;
        //jika user belum ada maka lakukan proses registrasi
        if(!$userData){
            $userData = SocialAuthSrv::replaceUser($user, $provider);
            $apiTokenData = ['id'=>$userData['token_id'],'api_token'=>$userData['token']];
            
            //jika tidak allow_register maka maka tolak
            if(!$appsData['allow_register']){
                $response['isNotRegistered'] = 1;
                //return redirect()->route('auth.login', $returnParam)->with('alert', ['type' => 'warning', 'message' => 'Login Failed, user not registered.']);
            }else{            
                AppsRepo::addUserToApps($user->id,config('cur_apps.id'));
                $response['isRegistered'] = 1;
                $response['reff'] = 'registersocialauth';
            }            
            
        //jikas sudah ada berarti hanya login saja
        }else{
            $response['reff'] = 'login';
        }
        
        $response['isLogin'] = 1;
        $userModel = UserRepo::getUserModel($userData['id']);
        Auth::login($userModel, true);        
        AcSSOService::setUser($userData['id'],$apiTokenData);
        $response['token'] = AcSSOService::getUserToken();
        
        return $this->authDone($response);
    }

}
