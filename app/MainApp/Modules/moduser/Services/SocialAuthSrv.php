<?php

namespace App\MainApp\Modules\moduser\Services;

use App\MainApp\Modules\moduser\Models\User;
use App\MainApp\Modules\moduser\Models\UserProfile;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;

/**
 * Social Sign On Service Handling
 */
class SocialAuthSrv
{

    private function formatAuthModel($user)
    {
        $data = [
            'id' => $user->getId(),
            'token' => $user->token,
            'name' => $user->getName(),
            'nickname' => $user->getNickname(),
            'email' => $user->getEmail(),
            'avatar' => $user->getAvatar()
        ];
        
        return $data;
    }
    
    public function getUserBySocnetIdOrEmail($id,$email,$provider)
    {
        $userData = User::where('email', $email)
            ->orWhere('socialauth_'.$provider.'_id',$id)
            ->first();
        
        if(!$userData)return false;
        return $userData->toArray();
    }
    
    public function getUserBySocnetId($id,$provider)
    {
        $userData = User::where('socialauth_'.$provider.'_id',$id)
            ->first();
        
        if(!$userData)return false;
        return $userData->toArray();
    }
    /**
     * If a user has registered before using social auth, return the user
     * else, create a new user object.
     * @param  $user Socialite user object
     * @param $provider Social auth provider
     * 
     * @return  User
     */
    public function replaceUser($user, $provider)
    {
        $socialUser = $this->formatAuthModel($user);
        $authUser = User::where('email', $user->getEmail())->orWhere('socialauth_'.$provider.'_id',$socialUser['id'])->first();
        
        $regUserData = [
            'socialauth_'.$provider.'_id'=>$socialUser['id'],
            'socialauth_'.$provider.'_token'=>$socialUser['token'],
            'socialauth_'.$provider.'_data'=> json_encode($socialUser)
        ];
        
        if ($authUser) {
            $regUserData['name'] = $user->getName();
            $authUser->update($regUserData);
            
            $userData = $authUser->toArray();
            $apiTokenData = UserRepo::generateToken($userData['id']);
            $userData['token_id'] = $apiTokenData['id'];
            $userData['token'] = $apiTokenData['api_token'];
            return $userData;
        } else {
            
            $regUserData['name'] = $socialUser['name'];
            $regUserData['email'] = $socialUser['email'];
            $regUserData['status'] = 1;
            $regUserData['registration_reff'] = 3;//registration by oauth socmend
            
            $data = UserRepo::register($regUserData);
            return $data;
        }
    }
}