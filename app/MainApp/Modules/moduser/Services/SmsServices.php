<?php

namespace App\MainApp\Modules\moduser\Services;

use App\MainApp\Modules\moduser\Repositories\UserRepo;
use App\MainApp\Modules\moduser\Repositories\UserMessageTraits;

/**
 * 
 */
class SmsServices
{
	/**
     * Zenziva::$userkey and $passkey
     *
     * Zenziva Account .
     *
     * @access  protected
     * @type    string
     */
    // protected $userkey = "userkey"; //userkey zenziva
    // protected $passkey = "passkey"; //passkey zenziva

    /**
     * Phone number
     *
     * @var string
     */
    public $to;
    /**
     * Message
     *
     * @var string
     */
    public $text;

    /**
     * Set destination phone number
     *
     * @param $to  Phone number
     *
     * @return self
     */
    // public function to($to)
    // {
    //     $this->to = $to;
    //     return $this;
    // }

    /**
     * Set userkey
     *
     * @param $userkey
     *
     * @return self
     */
    // public function userkey()
    // {
    //     $this->userkey = $userkey;
    //     return $this;
    // }

    /**
     * Set passkey
     *
     * @param $passkey
     *
     * @return self
     */
    // public function passkey()
    // {
    //     $this->passkey = $passkey;
    //     return $this;
    // }

    /**
     * Set messages
     *
     * @param $text  Message
     *
     * @return self
     */
    // public function text($text)
    // {
    //     if (! is_string($text)) {
    //         throw new \Exception('Text should be string type.');
    //     }
    //     $this->text = $text;
    //     return $this;
    // }

    /**
     * Send 
     */
    public function send($data){

        /*$data = http_build_query([
		    "userkey"  => $this->userkey,
		    "passkey"  => $this->passkey,
		    "nohp"  =>  $this->to,
		    "tipe"  => 'reguler',
		    "pesan"  => $this->text
        ]);*/
        $client = new Client(['base_uri' => 'http://subdomain.zenziva.com']);
        $configSms = [];
        if (config('hpsynapse.send_sms_zenziva')) {
            $configSms = array_merge($configSms, config('hpsynapse.send_sms_zenziva'));
        }

        config(['hpsynapse.send_sms_zenziva' => $configSms]);
        

        $request = $client->post('/apps/smsapi.php?'.$data.'');
        $response = $request->getBody()->getContents();
       
        return $response;
    }
	
}

?>