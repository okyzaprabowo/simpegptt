<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Updater extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syndeploy:update '
        . '{scope : all, project, appsBase, module} '
        . '{--project : [OPTIONAL] update project (app/MainApp)} '
        . '{--appsBase : [OPTIONAL] update apps-base (laravel project struture nya)} '
        . '{--appsGenerator : [OPTIONAL] update vendor/hp-synapse/apps-generator (khusus development mode)} '
        . '{--module= : [OPTIONAL] update package module-module yang dicantumkan, format :  moduleNamespace1,moduleNamespace2} '
        . '{--syndeploy : [OPTIONAL] update installer syndeploy} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synapse - System Updater';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mode = $this->argument('scope')=='dev'?'dev':'prod';        
        
        $config_dev_package_path = config('synapse.dev_package_path');
        
        $composerJson = json_decode(File::get(base_path('composer.json')),true);
        foreach ($composerJson['repositories'] as $key => $value) {
            
            if($mode == 'dev'){
                $package_name = explode('/',$value['name']);
                $repo[] = [
                    "name" => $value['name'],
                    "type" => "path",
                    "url" => $config_dev_package_path.$package_name[1],
                    "options" => [
                        "symlink" => true
                    ]
                ];
            }else{
                $repo[] = [
                    "name" => $value['name'],
                    "type" => "git",
                    "url" => 'https://gitlab.com/'.$value['name'].'.git',
                    "reference" => "master"
                ];
            }
        }
        
        $composerJson['repositories'] = $repo;
        
        if (File::put(base_path('composer.json') , str_replace('\/','/',json_encode($composerJson,JSON_PRETTY_PRINT)) )) {
            $this->info('PROJECT composer.json : updated');
        }
        
        $this->info('Change Development Mode To : '.$mode);
        $this->info('SUCCESS!');
    }
}