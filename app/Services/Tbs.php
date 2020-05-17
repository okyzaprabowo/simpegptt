<?php

namespace App\Services;

include_once(app_path('Services/tbs/tbs_class.php')); // Load the TinyButStrong template engine
include_once(app_path('Services/tbs/plugins/opentbs/tbs_plugin_opentbs.php')); // Load the OpenTBS plugin 

class Tbs
{
    public $TBS;

    public function init($templatePath=false,$config=[]) {

        if(!$templatePath)return false;

        $this->TBS = new \clsTinyButStrong; // new instance of TBS
        $this->TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load the OpenTBS plugin
        $this->TBS->NoErr = false;
        $this->TBS->LoadTemplate($templatePath, OPENTBS_ALREADY_UTF8);
    }

    public function setVar($var=[],$varNama = 'inlinedoc') {
        $this->TBS->MergeBlock($varNama, $var); 
    }

    public function download($outputFIleName=''){
        if(empty($outputFIleName))return false;
        $this->TBS->Show(OPENTBS_DOWNLOAD, $outputFIleName);
        exit(); 
    }


}