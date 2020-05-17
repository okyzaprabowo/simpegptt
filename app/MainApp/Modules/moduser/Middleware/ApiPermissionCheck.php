<?php

namespace App\MainApp\Modules\moduser\Middleware;

use Closure;

/**
 * cek permission akses aplikasi yg menggunakan Billionaire Stroe Account Center
 */
class ApiPermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        switch ($request->route()->getName()) {
            
            //jika mengakses halaman registrasi
            case 'auth.register':
            case 'auth.doRegister':
            case 'auth.verifyPhone':
            case 'auth.doVerifyPhone':
            case 'auth.resendVerifyPhone':
                //jika tidak ada fitur registrasi maka tolak
                if(!config('cur_apps.allow_register')){
                    return redirect()->route('auth.login', ['apps_code' => config('cur_apps.apps_code')])->with('alert', [
                        'type' => 'warning',
                        'message' => 'No registration allowed.'
                    ]);
                }

                break;

            default:
                break;
        }
        return $next($request);
    }
}
