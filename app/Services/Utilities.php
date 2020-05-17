<?php

namespace App\Services;

class Utilities
{
    public static function findNamespaceResources(array $namespaces, $resourceFolderName, $resourceNamespace)
    {        
        return array_reduce($namespaces, function ($carry, $namespacePath) use ($resourceNamespace, $resourceFolderName) {
            $modulePrefix = $namespacePath[1];
            $components = glob(sprintf('%s*', $namespacePath[0]), GLOB_ONLYDIR);           
            $isModuleOk = true;            
            $paths = array_map(function ($component) use ($resourceNamespace, $resourceFolderName, $modulePrefix, $isModuleOk) {
                
                if($modulePrefix){
                    $moduleName = substr($component, strrpos($component, DIRECTORY_SEPARATOR) + 1);                    
                    if(strpos($moduleName, $modulePrefix) !== 0)    
                        return false;    
                    
                    $component .= DIRECTORY_SEPARATOR.'src';
                }
                
                $path = [$component];

                if (!empty($resourceNamespace)) {
                    $path[] = $resourceNamespace;
                }
                
                $path[] = $resourceFolderName;

                $path = implode(DIRECTORY_SEPARATOR, $path);

                return is_dir($path) ? $path : false;
            }, $components);

            return array_merge($carry, array_filter($paths));
        }, []);
    }
    
    /*
     * looping per module per module namespace nya (letak modul bisa dimana saja)
     */
    public static function listModulePath(array $namespaces,$func)
    {
        $return = [];
        foreach ($namespaces as $namespace => $path) {
            $tmp = glob(sprintf('%s*', $path[0]),GLOB_ONLYDIR);
            foreach ($tmp as $modulePath){                
                //$component : nama/folder module nya
                $component = substr($modulePath, strrpos($modulePath, DIRECTORY_SEPARATOR) + 1);
                
                if($path[1]){
                    if(strpos($component, $path[1]) !== 0)    
                        continue;
                }
                
                if($namespace=='hpsynapse'){
                    $modulePath .= DIRECTORY_SEPARATOR.'src';
                }
                //$newNamespace : namespace ke folder per modulenya App/Modules/NAMAMODULE
                $newNamespace = sprintf(
                    '%s\\%s\\',
                    $namespace,
                    str_replace('-', '', $component)
                );

                $return[] = $func($newNamespace,$modulePath);
                
            }
        }
        return $return;
    }
}
