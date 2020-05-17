<?php

namespace App\MainApp\Modules\moduser\Services;

use GuzzleHttp\Client;

class Sms {

    /**
     * Zenziva::$userkey and $passkey
     *
     * Zenziva Account .
     *
     * @access  protected
     * @type    string
     */
    protected $userkey = "userkey"; //userkey zenziva
    protected $passkey = "passkey"; //passkey zenziva
    protected $apiurl = "passkey"; //passkey zenziva

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
    public function __construct()
    {
        $this->userkey = config('services.zenziva.userkey');
        $this->passkey = config('services.zenziva.passkey');
        $this->apiurl = config('services.zenziva.api');
    }
    /**
     * Set destination phone number
     *
     * @param $to  Phone number
     *
     * @return self
     */
    public function to($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * Set userkey
     *
     * @param $userkey
     *
     * @return self
     */
    public function userkey($userkey)
    {
        $this->userkey = $userkey;
        return $this;
    }

    /**
     * Set passkey
     *
     * @param $passkey
     *
     * @return self
     */
    public function passkey($passkey)
    {
        $this->passkey = $passkey;
        return $this;
    }

    /**
     * Set messages
     *
     * @param $text  Message
     *
     * @return self
     */
    public function text($text)
    {
        if (! is_string($text)) {
            throw new \Exception('Text should be string type.');
        }
        $this->text = $text;
        return $this;
    }

    /**
     * Send 
     */
    public function send(){

        $client = new Client(['base_uri' => $this->apiurl]);
        $data = http_build_query([
		    "userkey"  => $this->userkey,
		    "passkey"  => $this->passkey,
		    "nohp"  =>  $this->to,
		    "tipe"  => 'reguler',
		    "pesan"  => $this->text
        ]);

        $request = $client->post('/apps/smsapi.php?'.$data.'');
        $response = $request->getBody()->getContents();
        return $response;
    }
}