<?php
use App\Services\Utilities;

$config = [
    'namespaces' => [
        'App\\MainApp\\Modules' => [app_path('MainApp' . DIRECTORY_SEPARATOR . 'Modules') . DIRECTORY_SEPARATOR, false],
        'hpsynapse' => [base_path('vendor' . DIRECTORY_SEPARATOR . 'hp-synapse') . DIRECTORY_SEPARATOR, 'mod-']
    ],
    'language_folder_name' => 'lang',
    'resource_namespace' => 'resources'
];

$langPath = Utilities::findNamespaceResources(
    $config['namespaces'] ,
    $config['language_folder_name'],
    $config['resource_namespace']
);

$langPath = array_merge(
    [
        resource_path('lang')
    ], 
    $langPath
);
$langPath[] = app_path('MainApp' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'lang');

return [
    'lang_path' => $langPath,
    /*
     * namespace ke path lokasi module
     *  NAMESPACE => [path_to_module_group, FILTER PREFIX
     */
    'namespaces' => $config['namespaces'],
    'lib_namespace' => ['hpsynapse' => [base_path('vendor' . DIRECTORY_SEPARATOR . 'hp-synapse'), 'lib-']],
    
    'resource_namespace' => $config['resource_namespace'],
    
    'language_folder_name' => $config['language_folder_name'],
    
    'view_folder_name' => 'views',

    /*
     * dev_package_path : path ke package disimpan secara fisik saat development
     * relative ke base_path()
     */
    'dev_package_path' => '../',
    'protection_middleware' => [
        
    ],    
    /*
     * struktur table default yang akan digenerate jika tidak mencantumkan 
     * nama tabel saat generate
     */
    'generate_table_default' => [
        'name' => 'varchar',
        'description' => 'text'
    ],
    /*
     * field yang akan di hilangkan form dan list serta akan dimasukan
     * ke model guarded attribut
     */
    'generate_table_field_exclude' => [
        'id','created_at','updated_at'
    ],
    /*
     * template layout utama yg akan di extend saat generate module
     */
    'generate_default_layout' => 'layouts.app',
    /*
     * view dari sidebar menu yang akan ditambahkan menu baru oleh system
     */
    'generate_sidebar_layouts' => 'layouts.adminsidebar',
    /*
     * tag html container menu sidebar yg akan ditambah
     */
    'generate_sidebar_menu_tag' => 'ul',
    /*
     * zappid dari tag container menu sidebar yg akan ditambah
     */
    'generate_sidebar_menu_id' => 'menusidebar'
];
