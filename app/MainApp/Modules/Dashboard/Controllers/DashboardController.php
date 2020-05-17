<?php

namespace App\MainApp\Modules\Dashboard\Controllers;

use Illuminate\Http\Request; 
use App\Base\BaseController;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

class DashboardController extends BaseController
{
    public function index(Request $request)
    {
        if(UserAuth::hasAccess('Absensi.approval')){
            $this->response = redirect()->route('permohonan_absen.approval');
        }else{
            $this->output['data'] =['data'=> 'masuk'];
            $this->response = 'dashboard.index';
        }
        
        return $this->done();
    }
}