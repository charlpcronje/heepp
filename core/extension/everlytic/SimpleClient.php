<?php
namespace core\extension\everlytic;
use core\extension\api\client\RestClient;
use core\extension\Extension;
use core\extension\database\Model;

class SimpleClient extends Extension {
    protected $success   = false;
    protected $type      = 'get';
    protected $endPoint  = null;
    protected $userAgent = 'core';
    protected $headers   = [
        'Authorization' => 'Basic '.COMMS_AUTH,
        'Accept'        => 'application/json',
        'Content-Type'  => 'application/json'
    ];
    protected $result    = null;
    protected $response  = null;
    protected $params    = [];
    protected $error     = null;
    private $logRequests = true;
    private $startTime   = 0;
    private $endTime     = 0;
    private $elapsedTime = 0;
    
    function __construct() {
        parent::__construct();
    }
    
    function getLists() {
        $method = 'GET';
        $cSession = curl_init();
        $headers = array();
        $auth = base64_encode(COMMS_USERNAME . ':' . COMMS_API_KEY);
        $headers[] = 'Authorization: Basic ' . $auth;
        curl_setopt($cSession, CURLOPT_URL, COMMS_API_URL.'lists');
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cSession, CURLOPT_HEADER, false);
        curl_setopt($cSession, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($cSession);
        curl_close($cSession);
        return json_decode($result);
    }
    
    function createSMS($subject,$message) {
        $params = [
            'subject' => $subject,
            'text_original' => $message,
            'list_ids' => '14671'
        ];
        $json = json_encode($params);
        $url = COMMS_API_URL.'sms';
        $method = 'POST';
        $cSession = curl_init();
        $headers = array();
        $auth = base64_encode(COMMS_USERNAME . ':' . COMMS_API_KEY);
        $headers[] = 'Authorization: Basic ' . $auth;
        curl_setopt($cSession, CURLOPT_URL, $url);
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cSession, CURLOPT_HEADER, false);
        curl_setopt($cSession, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($cSession, CURLOPT_POSTFIELDS, $json);
        $headers[] = 'Content-Type: application/json';
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($cSession);
        curl_close($cSession);
        return json_decode($result);
    }
    
    function sendSMS($smsId) {
        $url = COMMS_API_URL.'sms_actions/send/'.$smsId;
        $method = 'POST';
        $cSession = curl_init();
        $headers = array();
        $auth = base64_encode(COMMS_USERNAME . ':' . COMMS_API_KEY);
        $headers[] = 'Authorization: Basic ' . $auth;
        curl_setopt($cSession, CURLOPT_URL, $url);
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cSession, CURLOPT_HEADER, false);
        curl_setopt($cSession, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($cSession);
        curl_close($cSession);
        return json_decode($result);
    }
    
    function getListOfAllContacts() {
        $url = COMMS_API_URL.'contacts';
        $method = 'GET';
        $cSession = curl_init();
        $headers = array();
        $auth = base64_encode(COMMS_USERNAME . ':' . COMMS_API_KEY);
        $headers[] = 'Authorization: Basic ' . $auth;
        curl_setopt($cSession, CURLOPT_URL, $url);
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cSession, CURLOPT_HEADER, false);
        curl_setopt($cSession, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($cSession);
        curl_close($cSession);
        return json_decode($result);
    }
    
    function getCommListsDetails($listId) {
        $url = COMMS_API_URL.'lists/'.$listId;
        $method = 'GET';
        $cSession = curl_init();
        $headers = array();
        $auth = base64_encode(COMMS_USERNAME . ':' . COMMS_API_KEY);
        $headers[] = 'Authorization: Basic ' . $auth;
        curl_setopt($cSession, CURLOPT_URL, $url);
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cSession, CURLOPT_HEADER, false);
        curl_setopt($cSession, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($cSession);
        curl_close($cSession);
        return json_decode($result);
    }
    
    function getCommContactsInList($listId) {
        $url = COMMS_API_URL.'list_subscriptions/'.$listId;
        $method = 'GET';
        $cSession = curl_init();
        $headers = array();
        $auth = base64_encode(COMMS_USERNAME . ':' . COMMS_API_KEY);
        $headers[] = 'Authorization: Basic ' . $auth;
        curl_setopt($cSession, CURLOPT_URL, $url);
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cSession, CURLOPT_HEADER, false);
        curl_setopt($cSession, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($cSession);
        curl_close($cSession);
        return json_decode($result);
    }
    
    function getContactDetails($contactId) {
        $url = COMMS_API_URL.'contacts/'.$contactId;
        $method = 'GET';
        $cSession = curl_init();
        $headers = array();
        $auth = base64_encode(COMMS_USERNAME . ':' . COMMS_API_KEY);
        $headers[] = 'Authorization: Basic ' . $auth;
        curl_setopt($cSession, CURLOPT_URL, $url);
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cSession, CURLOPT_HEADER, false);
        curl_setopt($cSession, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($cSession);
        curl_close($cSession);
        return json_decode($result);
    }
}
