<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Base\BaseController;

class LangController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * initiate language resource for vue apps
     * 
     * @param Request $request *semua optional
     *      lang : lang id nya
     *      item : item nya jika diperlukan
     *
     */
    public function readList(Request $request)
    {
        if($request->input('lang')){
            app()->setLocale($request->input('lang'));
        }
        if($request->input('item')){
            return response()->json(trans($request->input('item')));
        }
        $lang = app()->getLocale();
        $trans = [];
        //get all language namespace
        foreach (config('hpsynapse.lang_path') as $path) {
            $langItem = glob($path.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'*');                
            foreach($langItem as $langFile){
                $filename = basename($langFile, ".php");
                $var = trans($filename);
                if(is_array($var)){
                    if(isset($trans[$filename])){
                        $trans[$filename] = recuresive_array_merge($trans[$filename],$var);
                    }else{
                        $trans[$filename] = $var;
                    }
                }                
            }
        }
        return response()->json($trans);
    }

}
