<?php

namespace App\MainApp\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UpdateRole extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moduser:aclupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update data json role di /config/acl';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filenames = glob(app_path('MainApp/config/acl/*'));
                   
        $paths = array_map(function ($filename) {
            return str_ireplace('.json','',str_replace(app_path('MainApp/config/acl/'),'',$filename));
        }, $filenames);
        
        foreach ($paths as $roleCode) {
            DB::table('roles')->where('role_code',$roleCode)->update(['rule' => file_get_contents(app_path('MainApp/config/acl/'.$roleCode.'.json'))]);
            $this->info('Update : '.$roleCode);
        }

        $this->info('SUCCESS!');

    }
}