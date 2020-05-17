<?php

namespace App\MainApp\Modules\moduser\Controllers\Auth;


trait AuthResponseTraits
{
 
    /**
     * redirect hasil SSO dan Auth kembali ke halaman aplikasi client sso grab
     * 
     * @param array $param parameter yg di passing
     * 
     * @return redirect parameter yg di passing saat redirect :
     */
    protected function authDone($param=[],$backlink=false)
    {
        if($backlink==false){
            $backlink = config('cur_apps.session_grab_url');
        }
        return redirect()->away($backlink.'?'.http_build_query($param));
    }
    
    protected function filterAuthParam($param)
    {
        
    }
}