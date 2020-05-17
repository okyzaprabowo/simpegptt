<?php

namespace App\Providers;

// use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\View\ViewServiceProvider as BaseViewServiceProvider;
use App\Services\Utilities;

class ViewServiceProvider extends BaseViewServiceProvider
{

    public function register()
    {

        $config = $this->app['config']['hpsynapse'];
        $paths = Utilities::findNamespaceResources(
            $config['namespaces'], $config['view_folder_name'], $config['resource_namespace']
        );
        $paths[] = app_path('MainApp' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views');
        $this->app['config']['view.paths'] = array_merge($paths,$this->app['config']['view.paths']);
        parent::register();
    }

}
