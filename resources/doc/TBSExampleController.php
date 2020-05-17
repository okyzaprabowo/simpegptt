<?php
namespace App\MainApp\Modules\Example\Controllers;
use Illuminate\Http\Request;
use App\Services\Tbs;

use App\Base\BaseController;

class TBSExampleController extends BaseController {
    public function downloadDoc(Request $request)
    {
        $templateVar = [[
            'namafield_1' => 'tulisan field 1',
            'namafield_2' => 'tulisan field 2',
            'namafield_3' => 'tulisan field 3',
            'namafield_4' => 'tulisan field 4',
        ]];

        $tbs = new Tbs();
        $tbs->init(app_path('MainApp/resources/doc/template.docx'));//filename path ke template file .docx
        /**
         * parameter setVar(variable, prefix) :
         *      variable : variable format array(array('item')) , item template yang di passing
         *      prefix : prefix tulisan di file .docx
         */
        $tbs->setVar($templateVar,'inlinedoc');
        $tbs->download('hasil_dari_template.docx');//autput file hasil jadi (didownload)
    }
}