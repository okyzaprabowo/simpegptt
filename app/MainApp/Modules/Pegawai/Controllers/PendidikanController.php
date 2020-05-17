<?php

namespace App\MainApp\Modules\Pegawai\Controllers;

use Illuminate\Http\Request;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;
use Facades\App\MainApp\Repositories\Kepegawaian;
use Facades\App\MainApp\Repositories\Master;
use App\Base\BaseController;

class PendidikanController extends BaseController
{
    public function __construct()
    {
        // $this->forceApiOutput();
    }
}