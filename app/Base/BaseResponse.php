<?php

namespace App\Base;

use Illuminate\Contracts\Support\Responsable;

abstract class BaseResponse implements Responsable
{

    protected $data, $response, $forceOutput, $listdataVarName;
    
    /**
     * 
     * 
     * @param array $output autput dari controller
     * @param mixed $response 
     *      string jika nama view
     *      instance recirect() jika redirect
     * @param int $forceOutput 0 auto, 1 force web, 2 force api
     */
    public function __construct($output=false,$response='list',$forceOutput=0, $listdataVarName='data')
    {
        $this->output = $output;
        $this->response = $response;
        $this->forceOutput = $forceOutput;
        $this->listdataVarName = $listdataVarName;
    }
    
    /**
     * prepare all data
     */
    abstract protected function prepare();
    
    public function toResponse($request)
    {        
        return $this->isApiCall($request) ? $this->apiResponse() : $this->viewResponse();
    }
    /**
     * Detect apakah request untuk output ke API atau View
     */
    protected function isApiCall($request)
    {
        if($this->forceOutput){
            return $this->forceOutput==2?true:false;
        }
        
        if($request->wantsJson()){
            return true;
        }
        
        return false;
    }
    
    /**
     * cek apakah request API atau WEB
     * @return boolean
     */
    protected function isAjaxCall($request)
    {
        return $request->ajax()?true:false;
    }
    
    /**
     * API REQUEST
     * =========================================================================
     */
    public function prepareApi()
    {
        $outputParam = [
            'status'=>200,
            'data'=>[],
            'message'=>'',
            'errors'=>null
        ];
        //delete semua data selain data khusus api
        foreach ($outputParam as $key => $value) {
            if(isset($this->output[$key])){
                $data[$key] = $this->output[$key];
            }else{
                $data[$key] = $value;
            }
        }
        
        //jika menyertakan data tipe listing
        // if(isset($this->output['listdata'])&&is_array($this->output['listdata'])){
        //     $data['data'] = $this->output['listdata'];
        // }
        
        //jika error maka kosongkan data
        if(!is_null($data['errors']))$data['data'] = null;
        
        $this->output = $data;
    }
    
    /**
     * Response output ajax
     */
    private function apiResponse()
    {
        $this->prepareApi();   
        $this->prepare();
        return response()->json($this->output, $this->output['status']);
    }
    
    /**
     * WEB REQUEST
     * =========================================================================
     */
    public function prepareView()
    {
        $this->alert = false;
        $this->errors = false;
        $this->with = false;
        $this->viewdata = false;
        
        $dataTmp = $this->output['data'];
        
        if(isset($this->output['message']) && $this->output['message']){            
            $this->alert = [
                'type' => $this->output['message_type'],
                'message' => $this->output['message']
            ];
        }
        
        if(isset($this->output['errors']) && $this->output['errors'] != null && $this->output['errors'] != [true]){
            $this->errors = $this->output['errors'];
        }
        
        //jika menyertakan data type listing
        // if(isset($this->output['listdata'])&&!is_null($this->output['listdata'])){
        //     $dataTmp[$this->listdataVarName] = $this->output['listdata'];
        // }
        
        //jika menyertakan data tambahan untuk view
        if(isset($this->output['viewdata'])&&is_array($this->output['viewdata'])){
            $dataTmp = array_merge($dataTmp,$this->output['viewdata']);
            $this->viewdata = $this->output['viewdata'];
        }
        
        if(!$dataTmp)$dataTmp=[];
        $this->output = $dataTmp;
    }
    
    /**
     * Response output view layer
     */
    private function viewResponse()
    {
        $this->prepareView();
        $this->prepare();
        
        //jika string berarti view
        if(is_string($this->response)){
            $this->response = view($this->response, $this->output);
        }else{
            //tambahkan get parameter jika menyertakan viewdata
            if($this->viewdata){
                $redirectUrl = $this->_viewResponseProccParam(
                    $this->response->getTargetUrl(),
                    $this->viewdata
                    );
                $this->response = $this->response->setTargetUrl($redirectUrl);
            }
        }
        if($this->alert)\Session::put('alert', $this->alert);
        
        if($this->errors)$this->response = $this->response->withErrors($this->errors);
        
        return $this->response;
    }
    
    /**
     * 
     */
    private function _viewResponseProccParam($redirectUrl,$addQuery)
    {
        $resultUrl = \parse_url($redirectUrl);
        $addQuery = http_build_query($addQuery);
        if(isset($resultUrl['query'])){
            $resultUrl['query'] = $resultUrl['query'].'&'.$addQuery;
        }else{
            $resultUrl['query'] = $addQuery;
        }
        $resultUrl = $resultUrl['scheme'].'://'.$resultUrl['host'].(isset($resultUrl['path'])?$resultUrl['path']:'').(isset($resultUrl['query'])?'?'.$resultUrl['query']:'');
        return $resultUrl;
    }

}
