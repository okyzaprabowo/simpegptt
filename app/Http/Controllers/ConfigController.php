<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MConfig;

use App\Base\BaseController;

class ConfigController extends BaseController
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
     * GET - api list config
     * 
     * @param Request $request
     *      group *optional
     *      key *optional
     *
     * @return array list data config
     */
    public function readList(Request $request)
    {
        $model = new MConfig;
    
        if($request->input('group',false)){
            $model = $model->where('group',$request->input('group'));
        }
        if($request->input('key',false)){
            $model = $model->where('key',$request->input('key'));
        }
    
        $data = $model->get();           
        
        return response()->json($data);
    }

    /**
     * POST - create dan update
     * 
     * @param Request $request
     *      data array list data config yang akan di create / update
     *
     * @return array list data config
     */
    public function createUpdate(Request $request)
    {
        if($data = $request->input('data',false)){
            foreach($data as $value){
                $updateData = [];
                if(isset($value['name']))
                    $updateData['name'] = $value['name'];
                if(isset($value['value']))
                    $updateData['value'] = $value['value'];
                if($updateData){
                    $model = MConfig::where('group',$value['group'])->where('key',$value['key']);
                    if($model->exists()){
                        $model->update($updateData);
                    }else{
                        $updateData['group'] = $value['group'];
                        $updateData['key'] = $value['key'];
                        $model->create($updateData);
                    }
                }
            }
        }

        return response()->json(MConfig::get());
    }

}
