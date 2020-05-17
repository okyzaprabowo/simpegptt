<?php
require app_path('Helpers/Helper.php');

/**
 * Config utama yang menyimpan semua config aplikasi. Datanya disimpan di app/Module/System/config
 */
$client = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/client.json'), true);
$listener = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/listener.json'), true);

$keyConfig = json_decode(file_get_contents(__DIR__ . '/../resources/assets/src/config.json'), true);

/*
Load config system
*/
$system = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/system.json'), true);

if(file_exists(__DIR__ . '/../app/MainApp/config/systemEnv.json')){
    $tmpEnvSystem = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/systemEnv.json'), true);
}else{
    file_put_contents(__DIR__ . '/../app/MainApp/config/systemEnv.json', json_encode($system, JSON_PRETTY_PRINT));
    $tmpEnvSystem = $system;
}

$newEnv = [];
//hanya load systemEnv yang boleh dieditnya saja
foreach ($keyConfig['allowed_systemEnv_key'] as $value) {
    if (isset($tmpEnvSystem[$value])) {
        $newEnv[$value] = $tmpEnvSystem[$value];
    }
}
if (count($newEnv) >= 1) {
    //save ulang config pastikan tidak mengandung key yang tidak boleh diedit
    file_put_contents(__DIR__ . '/../app/MainApp/config/systemEnv.json', json_encode($newEnv, JSON_PRETTY_PRINT));
    $system = recuresive_array_merge($system, $newEnv);
}

/*
Proses package & packageLocal config.
merge config package & packageLocal menjadi packageLocal, karena package akan digunakan untuk default config package (module ataupun lib)
*/
/*
load config module & lib
*/
$moduleList = array_merge(
    glob(base_path('app/MainApp/Modules/*/packageconfig.json')),
    glob(base_path('vendor/hp-synapse/*/packageconfig.json'))
);
$package = [];
foreach ($moduleList as $path) {
    $tmpPackage = json_decode(file_get_contents($path), true);
    $package[$tmpPackage['package_namespace']] = $tmpPackage;
}

$packageOld = '';
if(file_exists(__DIR__ . '/../app/MainApp/config/package.json')){
    $packageOld = file_get_contents(__DIR__ . '/../app/MainApp/config/package.json');
}

$packageText = json_encode($package, JSON_PRETTY_PRINT);

//save hanya jika ada perubahan
if($packageOld != $packageText) {
    file_put_contents(__DIR__ . '/../app/MainApp/config/package.json', $packageText);
}

//merge config package dengan package local
$packageLocalString = '';
if(file_exists(__DIR__ . '/../app/MainApp/config/packageLocal.json')){
    $packageLocalString = file_get_contents(__DIR__ . '/../app/MainApp/config/packageLocal.json');
    $tmpPackageLocal = json_decode($packageLocalString, true);
}else{
    $tmpPackageLocal = [];
}

if(file_exists(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json')){
    $tmpPackageLocalEnv = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json'), true);
}else{
    file_put_contents(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json', json_encode($tmpPackageLocal, JSON_PRETTY_PRINT));
    $tmpPackageLocalEnv = $tmpPackageLocal;
}

$homeSlug = isset($client['endpoint'][$system['mode']]['home_slug'])?$client['endpoint'][$system['mode']]['home_slug']:'';
$homeSlug = $homeSlug?('/'.trim($homeSlug,'/').'/'):'';
//initiate config ednpoint.json
$endpoint = [
    'domain' => $client['endpoint'][$system['mode']]['domain'],
    'admin' => [
        'app' => $homeSlug.$client['endpoint'][$system['mode']]['admin'],
        'auth' => $homeSlug
    ],
    'frontend' => [
        'app' => $homeSlug.$client['endpoint'][$system['mode']]['frontend'],
        'auth' => $homeSlug
    ],
    'api' => [
        'app' => $homeSlug.$client['endpoint'][$system['mode']]['api'],
        'auth' => $homeSlug
    ],
    
];
$packageLocal = []; //untuk di load di config
$newPackageLocal = []; //untuk filtered packageLocal.json yang akan disave ulang
$newPackageLocalEnv = []; //untuk filtered packageLocalEnv.json yang akan disave ulang

$acl = [];
$tmpSidenav = [];
$sidenav = [];


/**
 * Filter acl
 */
if (!function_exists('processAcl')) {
    function processAcl($acl,$packageName,$aclPrefix,$children){
        foreach ($children as $aclId => $value) {
            if($value['enable'] && $value['acl_config']['show']){
                $aclPrefixTmp = $aclPrefix.'.'.$aclId;

                $acl[$packageName]['children'][$aclPrefixTmp] = $value['acl_config'];
                $acl[$packageName]['children'][$aclPrefixTmp]['acl_caption'] = isset($value['acl_caption'])?$value['acl_caption']:$value['caption'];
                $acl[$packageName]['children'][$aclPrefixTmp]['acl_description'] = isset($value['acl_description'])?$value['acl_description']:$value['description'];

                //jika masih ada child nya proses terus
                if(isset($value['children'])){
                    unset($acl[$aclPrefixTmp]['children']);
                    $acl = processAcl($acl,$packageName,$aclPrefixTmp,$value['children']);
                }
            }
        }
        return $acl;
    }
}
/**
 * Filter acl
 */
if (!function_exists('processSidenav')) {
    function processSidenav($children){
        $res = [];
        foreach ($children as $aclId => $value) {
            if($value['enable'] && $value['is_navbar']){
                $res[$aclId] = $value;
                //jika masih ada child nya proses terus
                if(isset($value['children'])){
                    $res[$aclId]['children'] = processSidenav($res[$aclId]['children']);
                }
            }
        }
        return $res;
    }
}

foreach ($package as $item) {
    
    $newPackageLocal[$item['package_namespace']] =
        isset($tmpPackageLocal[$item['package_namespace']])
        ? $tmpPackageLocal[$item['package_namespace']]
        : [];
    $newPackageLocalEnv[$item['package_namespace']] =
        isset($tmpPackageLocalEnv[$item['package_namespace']])
        ? $tmpPackageLocalEnv[$item['package_namespace']]
        : [];

    //hapus package key config yang tidak boleh diedit
    foreach ($keyConfig['protected_packageLocal_key'] as $value) {
        if(isset($newPackageLocal[$item['package_namespace']][$value]))
            unset($newPackageLocal[$item['package_namespace']][$value]);
        if(isset($newPackageLocalEnv[$item['package_namespace']][$value]))
            unset($newPackageLocalEnv[$item['package_namespace']][$value]);
    }
    
    $packageLocal[$item['package_namespace']] = recuresive_array_merge($item, $newPackageLocal[$item['package_namespace']]);
    if ($system['mode'] == 'dev') {
        $packageLocal[$item['package_namespace']] = recuresive_array_merge(
            $packageLocal[$item['package_namespace']],
            $newPackageLocalEnv[$item['package_namespace']]
        );
    }

    /**
     * proses generate _acl.json dan _sidenav
     */
    if($packageLocal[$item['package_namespace']]['enable']){
        //proses _acl.json
        if($packageLocal[$item['package_namespace']]['access']['has_acl']){
            $acl[$item['package_namespace']] = [
                'acl_caption' => 
                    isset($packageLocal[$item['package_namespace']]['access']['acl_caption'])?
                    $packageLocal[$item['package_namespace']]['access']['acl_caption']:
                    $packageLocal[$item['package_namespace']]['access']['caption'],
                'acl_description' => 
                    isset($packageLocal[$item['package_namespace']]['access']['acl_description'])?
                    $packageLocal[$item['package_namespace']]['access']['acl_description']:
                    $packageLocal[$item['package_namespace']]['access']['description'],
            ];
            if(isset($packageLocal[$item['package_namespace']]['access']['children'])){
                $acl = processAcl($acl,$item['package_namespace'],$item['package_namespace'],$packageLocal[$item['package_namespace']]['access']['children']);
            }
        }

        //proses _sidenav
        if($packageLocal[$item['package_namespace']]['access']['is_navbar']){
            $tmpSidenavTmp = ['package_namespace'=>$item['package_namespace'],$item['package_namespace'] => $packageLocal[$item['package_namespace']]['access']];
            
            if(isset($tmpSidenav[$item['package_namespace']]['children'])){
                $tmpSidenavTmp[$item['package_namespace']]['children'] = processSidenav($sidenav[$item['package_namespace']]['children']);
            }    
            
            if(isset($packageLocal[$item['package_namespace']]['access']['position'])){
                $tmpSidenav[ $packageLocal[$item['package_namespace']]['access']['position'] ] = $tmpSidenavTmp;
            }else{
                $tmpSidenav[] = $tmpSidenavTmp;
            }
        }
    }
    
}
ksort($tmpSidenav);
foreach ($tmpSidenav as $key => $value) {
    $sidenav[$value['package_namespace']] = $value[$value['package_namespace']];
}


$filePath = "";//path real saat build
$packagePath = "";//path untuk load selain main.js
$packageMainPath = "";//path untuk load main.js

$pathToBase = str_replace('\\', '/',base_path(''));

$moduleMainJs = [
    "// DO NOT EDIT MANUALY UNLESS YOU KNOW WHAT YOU ARE DOING \n" .
    "// This files is autogenerated on build and by app-generator \n" .
    "// containt list all main.js for every module registered to this project \n\n"
];
$moduleStore = [
    "// DO NOT EDIT MANUALY UNLESS YOU KNOW WHAT YOU ARE DOING \n" .
    "// This files is autogenerated on build and by app-generator \n" .
    "// load all vuex state for every module registered to this project \n\n"
];
$moduleStoreNamespace = [];
$moduleRouter = [
    "// DO NOT EDIT MANUALY UNLESS YOU KNOW WHAT YOU ARE DOING \n" .
    "// This files is autogenerated on build and by app-generator \n" .
    "// load all router for every module registered to this project \n\n"
];
$moduleRouterNamespace = [];
$moduleRouterAdmin = [
    "// DO NOT EDIT MANUALY UNLESS YOU KNOW WHAT YOU ARE DOING \n" .
    "// This files is autogenerated on build and by app-generator \n" .
    "// load all router admin endpoint for every module registered to this project \n\n"
];
$moduleRouterAdminNamespace = [];

foreach ($package as $item) {
    // dd($packageLocal['moduser']);
    /*
    generate endpoint masing-masing module
    ----------------------------
    */
    $moduleEndpoints = $packageLocal[$item['package_namespace']]['endpoint'][$system['mode']];
    foreach ($moduleEndpoints as $app => $moduleEndpoint) {        
        //jika module endpoint diawal "/" berarti tidak menggunakan apps endpoint
        if($moduleEndpoint == '' || $moduleEndpoint[0]!='/'){
            $endpoint[$app][$item['package_namespace']] = $endpoint[$app]['app'].'/'.$moduleEndpoint;
        }else{
            $endpoint[$app][$item['package_namespace']] = $homeSlug.$moduleEndpoint;
        }    
        //jika memiliki fitur auth dan module user maka assign auth endpointnya
        if($system['has_auth'] && isset($packageLocal['moduser']) && $packageLocal['moduser']['enable']){
            $authEndpoint = $packageLocal['moduser']['auth_endpoint'][$system['mode']];            
            if($authEndpoint[0]!='/'){
                $endpoint[$app]['auth'] = $endpoint[$app]['app'].'/'.$authEndpoint;
            }else{
                $endpoint[$app]['auth'] = $homeSlug.$authEndpoint;
            }
            // dd($endpoint[$app]['auth']);
        }
    }

    /*
    generate loader store, router, routerAdmin dan init.js untuk package
    -------------------------
    */
    if($item['is_package']){
        $filePath = "vendor/hp-synapse/".$item['package_dir']."/src/";
        $packagePath = $pathToBase."/vendor/hp-synapse/".$item['package_dir']."/src/";
        $packageMainPath = $pathToBase."/vendor/hp-synapse/".$item['package_dir']."/src/";
    }else{
        $filePath = "app/MainApp/Modules/".$item['package_dir']."/";
        $packagePath = "../../../Modules/".$item['package_dir']."/";
        $packageMainPath = "../../Modules/".$item['package_dir']."/";
    }
    
    //load package hanya jika aktif saja
    if($packageLocal[ $item['package_namespace'] ]['enable'] ){
        //untuk loader main.js
        if (file_exists($filePath . "resources/js/main.js")) {
            $moduleMainJs[] = 'require("' . $packageMainPath . 'resources/js/main");' . "\n";
        }
        
        //untuk loader vuex store
        if (file_exists($filePath."resources/js/store/store.js")) {
            $moduleStore[] = "import ".$item['package_namespace'].' from "'.$packagePath.'resources/js/store/store";' . "\n";
            $moduleStoreNamespace[] = "    ..." . $item['package_namespace'];
        }

        //untuk loader vue router
        if (file_exists($filePath."resources/js/router/index.js")) {
            $moduleRouter[] = "import " . $item['package_namespace'] . ' from "' . $packagePath . 'resources/js/router/index";' . "\n";
            $moduleRouterNamespace[] = "    .concat(" . $item['package_namespace'] . ")";
        }

        //untuk loader vue router admin endpoint
        if (file_exists($filePath."resources/js/router/indexAdmin.js")) {
            $moduleRouterAdmin[] = "import " . $item['package_namespace'] . ' from "' . $packagePath . 'resources/js/router/indexAdmin";' . "\n";
            $moduleRouterAdminNamespace[] = "    .concat(" . $item['package_namespace'] . ")";
        }
    }

}

$newPackageLocalString = json_encode($newPackageLocal, JSON_PRETTY_PRINT);
//save ulang pakcageLocal hanya jika ada perubahan
if($packageLocalString != $newPackageLocalString){
    file_put_contents(__DIR__ . '/../app/MainApp/config/packageLocal.json', $newPackageLocalString);
}
file_put_contents(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json', json_encode($newPackageLocalEnv, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_endpoint.json', json_encode($endpoint, JSON_PRETTY_PRINT));

$system['path'] = [
    'MainApp'=>app_path('MainApp'),
    'basePath'=>base_path('')
];

//---generated config
file_put_contents(__DIR__ . '/../app/MainApp/resources/js/modules.js', implode('',$moduleMainJs));
file_put_contents(__DIR__ . '/../app/MainApp/resources/js/store/modules.js', implode('',$moduleStore)."\nconst store = {\n".implode(",\n",$moduleStoreNamespace)."\n};\n\nexport default store;" );
file_put_contents(__DIR__ . '/../app/MainApp/resources/js/router/modules.js', implode('',$moduleRouter)."\nconst routes = []\n".implode("\n",$moduleRouterNamespace).";\n\nexport default routes;" );
file_put_contents(__DIR__ . '/../app/MainApp/resources/js/router/modulesAdmin.js', implode('',$moduleRouterAdmin)."\nconst routes = []\n".implode("\n",$moduleRouterAdminNamespace).";\n\nexport default routes;" );

//---generated config
file_put_contents(__DIR__ . '/../app/MainApp/config/_packageLocal.json', json_encode($packageLocal, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_system.json', json_encode($system, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_acl.json', json_encode($acl, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_sidenav.json', json_encode($sidenav, JSON_PRETTY_PRINT));

/*
package dan module berisi config yang sama persis
*/
return [
    'client' => $client,
    'system' => $system,
    'endpoint' => $endpoint,
    'packageLocal' => $packageLocal, //config2 dari module dan lib yang sudah diedit per project
    'package' => $package, //config2 default dari module dan lib
    'listener' => $listener,
    'sidenav' => $sidenav,
    'acl' => $acl
];
