<?php

namespace App\MainApp\Modules\moduser\Services;

use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

//use GuzzleHttp\Client;

class FirebaseApi
{

    protected static $config;
    protected static $firebase = false;
    protected static $messaging = false;

    public function __construct()
    {
        
    }

    public static function initService()
    {   
        if (!self::$firebase) {
            self::$config = config('AppConfig.packageLocal.moduser.notification');
            $serviceAccount = ServiceAccount::fromJsonFile(base_path(self::$config['firebase_config_path']));
            
            self::$firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->withDatabaseUri(self::$config['database_url']) //url from database firebase
                ->create();
        }
        return self::$firebase;
    }
    
    /**
     * initialize messaging object
     * 
     * @return messageing object
     */
    public static function getMessaging()
    {
        if (!self::$firebase)self::initService();
        if (!self::$messaging)self::$messaging = self::$firebase->getMessaging();
        
        return self::$messaging;
    }
    
    /**
     * kirim push message ke salah satu device
     * @param string $token
     * @param array $message format :
     *      'notification'
     *      'data'
     */
    public static function sendMessageToToken($token,$message)
    {
        $messageToToken = self::messageToToken($token,$message);
        self::getMessaging()->send($messageToToken);
    }
    
    /**
     * kirim push message ke topic tertentu
     * @param string $topic
     * @param array $message format :
     *      'notification'
     *      'data'
     */
    public static function sendMessageToTopic($topic,$message)
    {
        $messageToTopic = self::messageToTopic($topic,$message);
        self::getMessaging()->send($messageToTopic);
    }
    
    /**
     * format message untuk kirim ke topic
     * 
     * @param type $topic
     * @param type $data
     * @return type
     */
    public static function messageToTopic($topic, $data)
    {
        return self::messageFormat('topic', $topic, $data);
    }
    
    /**
     * format message untuk kirim ke salah satu device token
     * 
     * @param type $token
     * @param type $data
     * @return type
     */
    public static function messageToToken($token, $data)
    {
        return self::messageFormat('token', $token, $data);
    }
    
    public static function messageFormat($type, $id, $data)
    {        
        $notification = Notification::create(
            $data['notification']['title'],
            $data['notification']['body']);
        return CloudMessage::withTarget($type, $id)
                ->withNotification($notification) // optional
                ->withData($data['data']) // optional
        ;        
    }
    
    /**
     * =========================================================================
     */
    
    /**
     * 
     * @param type $topic
     * @param type $token
     */
    public static function subscribeToTopic($topic,$token)
    {
        self::getMessaging()->subscribeToTopic($topic,$token);
    }
    /**
     * 
     * @param type $topic
     * @param type $token
     */
    public static function unsubscribeFromTopic($topic,$token)
    {
        self::getMessaging()->unsubscribeFromTopic($topic,$token);
    }

}
