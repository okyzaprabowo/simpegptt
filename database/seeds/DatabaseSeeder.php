<?php

use Illuminate\Database\Seeder;
use App\Services\Utilities;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $modulePath = Utilities::listModulePath(config('hpsynapse.namespaces'), function($namespace,$pathToModule){            
            $pathToModule .= DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'SeedList.php';
            if(file_exists($pathToModule)){
                return $pathToModule;
            }
        });
        
        $projectSeeds = include(app_path('MainApp/database/SeedList.php'));
        $moduleSeeds = [];
        foreach($modulePath as $value){
            if($value) $moduleSeeds = array_merge($moduleSeeds,include($value));
        }
        $projectSeeds = array_merge($moduleSeeds,$projectSeeds);
        foreach($projectSeeds as $class){
            $this->call($class);
        }

    }
}
