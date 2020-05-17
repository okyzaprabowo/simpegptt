<?php

if (!function_exists('recuresive_array_merge')) {
    /**
     * Merge Array
     * 
     * @param array $array1
     * @param array $array2
     * @return array merged array
     */
    function recuresive_array_merge(array $array1,array $array2){
        $merged = $array1;
        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = recuresive_array_merge($merged[$key], $value);
            } else if (is_numeric($key)) {
                 if (!in_array($value, $merged)) {
                    $merged[] = $value;
                 }
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}
if (!function_exists('route_api_opt')) {
    /**
     * Autogenerate option route untuk api menyesuakan url home dan api nya
     * 
     * @param type $appsUrl
     * @param type $apiUrl
     * @return array format 
     *      prefix
     *      domain
     */
    function route_api_opt($appsUrl,$apiUrl){
        $appsPathInfo = parse_url($appsUrl);
        $apiPathInfo = parse_url($apiUrl);
        if(!isset($appsPathInfo['path']))$appsPathInfo['path']='';
        if(!isset($apiPathInfo['path']))$apiPathInfo['path']='';
        $prefix = str_replace($appsPathInfo['path'],'',$apiPathInfo['path']);
        if($prefix){  
            $routeOpt['prefix'] = $prefix;  
        }
        if($appsPathInfo['host'] != $apiPathInfo['host']){
            $routeOpt['domain'] = $apiPathInfo['host']; 
        }
        return $routeOpt;
    }
}
if (!function_exists('route_web_opt')) {
    /**
     * Autogenerate option route untuk api menyesuakan url home dan api nya
     * 
     * @param type $appsUrl
     * @param type $apiUrl
     * @return array format 
     *      prefix
     *      domain
     */
    function route_web_opt($appsUrl,$apiUrl){
        $appsPathInfo = parse_url($appsUrl);
        $apiPathInfo = parse_url($apiUrl);
        if(!isset($appsPathInfo['path']))$appsPathInfo['path']='';
        if(!isset($apiPathInfo['path']))$apiPathInfo['path']='';
        $routeOpt = [];
        if($appsPathInfo['host'] != $apiPathInfo['host']){
            $routeOpt['domain'] = $appsPathInfo['host']; 
        }
        return $routeOpt;
    }
}

if (!function_exists('is_route')) {
    /*
     * detect apakah route yang sedang diakses sekarang adalah route tertentu
     * 
     * @param string $routeName nama route yang akan dicek
     * @param type $class
     * 
     * @return string nama class
     */
    function is_route($routeName,$class='active')
    {
        if(is_array($routeName)){
            $isTrue = in_array(Route::current()->getName(), $routeName);
        }else{
            $isTrue = Route::current()->getName()==$routeName;
        }
        return $isTrue?$class:'';
 
    }
}

if (!function_exists('is_route_prefix')) {
    
    function is_route_prefix($route,$class='active')
    {
        $curPrefix = trim(Route::current()->getPrefix(),'/');
        if(is_array($route)){
            $isTrue = in_array($curPrefix, $route);
        }else{
            $isTrue = $curPrefix==$route;
        }
        return $isTrue?$class:'';
 
    }
}

if (!function_exists('pagination_format')) {    
    /**
     * 
     * @param type $count
     * @param type $offset
     * @param type $limit
     * @return array format
     *      total
     *      per_page
     *      current_page
     *      from
     *      to
     */
    function pagination_format($count,$offset=1,$limit=10){
        $curPage = (int) floor(($offset+1) / $limit) + 1;
        $to = (int) floor(($count+1) / $limit) + 1;
        $paginationData = [
            'total' => $count,
            'per_page' => $limit,
            'current_page' => $curPage,            
            'from' => 1,
            'to' => $to,
        ];
        
        return $paginationData;
    }
}

if (!function_exists('pagination_convert_link')) {    
    /**
     * convert link pagination default laravel menjadi default system asalnya page ke offset & limit
     * 
     * @param type $url
     * @param type $limit
     * @return string
     */
    function pagination_convert_link($url,$limit){
        $path = parse_url($url);

        if(!isset($path['query']))return $url;
        parse_str($path['query'], $queryParams);
        
        $queryParams['offset']=($queryParams['page']-1)*$limit;
        $queryParams['limit']=$limit;
        unset($queryParams['page']);

        $domain = $path['scheme'].'://'.$path['host'];
        if(isset($path['port']))$domain .= ':'.$path['port'];

        $newUrl = $domain.$path['path'].'?'.http_build_query($queryParams);
        
        return $newUrl;
    }
}

if (!function_exists('pagination_generate')) {    
    /**
     * Generate hasil list repo ke class pagination laravel
     * 
     * @param array $paginationParam
     *      data array data yg ditampilkannya
     *      count int 
     *      offset int
     *      limit int limit perpage
     * @param type $path
     * @return pagination pagination object
     */
    function pagination_generate($paginationParam,$path){
        
        $dataPagination = pagination_format(
            $paginationParam['count'],
            $paginationParam['offset'],
            $paginationParam['limit']
        );
        //ubah current page berdasarkan perhitungan
        request()->merge(['page'=>$dataPagination['current_page']]);
        
        \Illuminate\Pagination\Paginator::defaultView('pagination');
        
        // set current page
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        // set limit 
        $perPage = $paginationParam['limit'];
        
        $results = new \Illuminate\Pagination\LengthAwarePaginator(collect($paginationParam['data']), $paginationParam['count'], $perPage);
        
        return $results->withPath($path);
    }
}