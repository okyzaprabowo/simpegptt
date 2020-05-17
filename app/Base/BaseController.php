<?php

namespace App\Base;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as LaravelBaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Base\Traits\ResCacheTrait;

class BaseController extends LaravelBaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use ResCacheTrait;
    
    //default data parameter untuk responseable
    protected $output = [
            'status'=>200,
            'message'=>'',
            'message_type'=>'info',//khusus warning view (bukan api)
            'data'=>null,
            'viewdata'=>null,//data yang hanya disertakan di web request
            'errors'=>null,
        ];
    
    //nama variable wrap/grouping data di view (web request)
    protected $isViewVarWraped = false;//apakah seluruh variable diwrap/grupping ke variable $viewWrapVarName
    protected $viewWrapVarName = 'data';//nama variable wrap/grouping ,untuk data berbentuk list array, jadi di view akan jadi output->output['data'][VAR_NAME] dan di api akan jadi output->output['data']


    //default response paramter untuk
    protected $response = '';
    
    //nama class responseable nya
    protected $responsableName = '\App\Base\DefaultResponse';
    
    //force output menjadi api atau web
    private $forceOutput = 0;//0 auto, 1 WEB, 2 API
    
    /**
     * 
     * @param string $message
     * @param string $type 'warning','info','warning','danger'
     * @param integer $code http response code
     * @param mix $error
     * @param mix $response
     */
    protected function setWarning($message,$type='warning',$code=400,$error=false,$response=null)
    {        
        $this->output['status'] = $code;
        $this->output['message'] = $message;
        $this->output['message_type'] = $type;
        $this->output['errors'] = $error===true||$error===1||$error===false?[true]:$error;

        if(!is_null($response)){
            $this->response = 
                $response===true||$response===1||$response===false?
                redirect(url()->previous())->withInput():
                $response;
        }

        if($this->isWebCall() && $this->forceOutput != 2)
            \Session::put('alert', [
                    'type' => $type,
                    'message' => $message
                ]);
    }
    
    /**
     * 
     * @param string $message
     * @param mix $error
     * @param integer $code
     * @param type $response
     */
    protected function setError($message,$error=false,$code=400,$response=null)
    {        
        $this->setWarning($message,'danger',$code,$error,$response);
    }
    
    /**
     * set alert view
     * 
     * @param string $message
     * @param string $type 'warning','info','warning','danger'
     */
    protected function setAlert($message,$type='info')
    {        
        $this->setWarning($message,$type,'200');
    }

    /**
     * generate/get default parameter di resource listing, yang akan dipassing jug ke output
     * 
     * @param bool $mergeParam true jika parameter input lainnya langsung dimasukan ke query dan filter
     *                         false jika dipisah di key terpisah saja (all)
     * @param array $mergeExcept list parameter yg ditak di merge kan ke query & filter
     * @return array format :
     *  [
     *      all => seluruh parameter input
     *      query => [
     *          limit
     *          offset
     *          *orderBy --> optional jika menyertakan parameter orderBy atau orderType
     *          *orderType --> optional jika menyertakan parameter orderBy atau orderType
     *          q
     *      ],
     *      filter => [
     *          q
     *      ],
     *      orderBy => []
     *  ]
     */
    final protected function getListParam(bool $mergeParam = true,array $mergeExcept = [])
    {
        $params = [
            'all' => request()->except(['limit','offset','orderBy','orderType','q']),
            'query' => [//parameter yang dipassing di URL, termasuk juga parameter filter, untuk di passing ke pagination juga
                'limit' => request()->input('limit', 10),
                'offset' => request()->input('offset', 0)
            ],
            'filter' => [],//parameter filter ke method repo listing nya
            'orderBy' => []
        ];

        //jika menyertakan orderBy
        if(request()->input('orderBy',null)||request()->input('orderType',null)){
            $params['query']['orderBy'] = request()->input('orderBy','id');
            $params['query']['orderType'] = request()->input('orderType','ASC');
            $params['orderBy'] = [$params['query']['orderBy'] , $params['query']['orderType']];
        }

        //jika menyertakan query string
        if(request()->input('q', null)){
            $params['query']['q'] = request()->input('q','');
            $params['filter']['q'] = $params['query']['q'];
        }

        //jika merge parameter
        if($mergeParam && !empty($params['all'])){
            foreach ($params['all'] as $key => $param) {
                if(!in_array($key,$mergeExcept)){
                    $params['query'][$key] = $param;
                    $params['filter'][] = [$key,$param];
                }
            }            
        }

        return $params;
    }
    
    /**
     * cek apakah request dari ifframe atau bukan
     * @return boolean
     */
    protected function hasReferer()
    {
        return isset($_SERVER['HTTP_REFERER'])?true:false;
    }
    
    /**
     * cek apakah request API
     * @return boolean
     */
    protected function isApiCall()
    {
        return request()->wantsJson()?true:false;
    }
    
    /**
     * cek apakah request Ajax
     * @return boolean
     */
    protected function isAjaxCall()
    {
        return request()->ajax()?true:false;
    }
    
    /**
     * cek apakah request WEB
     * @return boolean
     */
    protected function isWebCall()
    {
        return !(request()->ajax()||request()->wantsJson())?true:false;
    }
    
    /**
     * bypass output responsable menjadi API (JSON) menghiraukan request yg masuk
     */
    protected function forceApiOutput()
    {
        $this->forceOutput = 2;
    }    
    
    /**
     * bypass output responsable menjadi WEB menghiraukan request yg masuk
     */
    protected function forceWebOutput()
    {
        $this->forceOutput = 1;
    }
    
    /**
     * 
     * @param type $response
     * @return \App\Base\responsableName
     */
    protected function done($response=false)
    {
        if($response)$this->response=$response;
        return new $this->responsableName(
            $this->output, 
            $this->response, 
            $this->forceOutput, 
            $this->isViewVarWraped, 
            $this->viewWrapVarName);
    }
    
    /*
     * controller level cache
     * -------------------------------------------------------------------------
     */
}