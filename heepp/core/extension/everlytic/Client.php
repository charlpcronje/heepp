<?php
namespace core\extension\everlytic;
use core\extension\api\client\RestClient;
use core\extension\Extension;
use core\extension\database\Model;

class Client extends Extension {
    private $apiURL = 'http://comms.webally-mailers.com/api/2.0/';
    protected $success   = false;
    protected $type      = 'get';
    protected $endPoint  = null;
    protected $userAgent = 'core';
    protected $headers   = [
        'Authorization' => '',
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
    
    function __construct($type,$endPoint,$headers = null,$params = null) {
        parent::__construct();
        $this->headers['Authorization'] = 'Basic '.base64_encode('');
        $this->type = $type;
        
        if(!isset($endPoint) || empty($endPoint)) {
            $error = 'No endpoint was specified for the API request to Everlytic';
            $this->setError($error);
            $this->error = $error;
        }
        $this->endPoint = $endPoint;
        
        $this->mergeArgs($headers,$params);
        $this->request();
        return $this;
    }
    
    protected function mergeArgs($headers,$params) {
        // Merge class headers property with $headers argument
        if (isset($headers) && is_array($headers)) {
            $this->headers = array_merge($this->headers,$headers);
        }
        
        // Merge class params property with $params argument
        if (isset($params) && is_array($params)) {
            $this->params = array_merge($this->params,$params);
        }
    }
    
    protected function request() {
        $this->startTime = getMicrotime();
        $api = new RestClient([
            'base_url' => $this->apiURL,
            'user_agent' => 'core',
            'headers' => $this->headers,
            'curl_options' => [
                //CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_CUSTOMREQUEST => $this->type,
                CURLOPT_POSTFIELDS => json_encode($this->params)
            ],
            'parameters'=> $this->params
        ]);
        
        $this->endTime = getMicrotime();
        $this->elapsedTime = (float)$this->endTime - (float)$this->startTime;
        $response = $api->{$this->type}($this->endPoint);

        if ($response->info->http_code == 200) {
            $this->response = $response->headers;
            $this->success = true;
        } else {
            $this->success = false;
            $this->response = $response->headers;
            $this->error = $response->error;
        }
        
        if ($this->logRequests) {
            $this->logRequest();
        }
    }
    
    private function logRequest() {        
        (new Model('apiRequestLog'))->insert([
            'endpoint'     => $this->endPoint,
            'type'         => $this->type,
            'params'       => json_encode($this->params),
            'zone'         => $this->zone,
            'user_agent'   => $this->userAgent,
            'headers'      => json_encode($this->headers),
            'response'     => json_encode($this->response),
            'success'      => $this->success,
            'error'        => $this->error,
            'start_time'   => $this->startTime,
            'end_time'     => $this->endTime,
            'request_time' => $this->elapsedTime
        ]);
    }
    
    public function result() {
        // Do not return the API key in result
        unset($this->headers['Authorization']);        
        return $this->result = (object)[
            'success'     => $this->success,
            'type'        => $this->type,
            'endPoint'    => $this->endPoint,
            'zone'        => $this->zone,
            'userAgent'   => $this->userAgent,
            'headers'     => $this->headers,
            'response'    => $this->response,
            'params'      => $this->params,
            'error'       => $this->error
        ];
    }
}
    /*
    $json = '
{
"subject":"subject",
"text_original":"This is a test message",
"list_ids":"1"
}
';
$url = '(Your URL)/api/2.0/sms';
$method = 'POST';
$cSession = curl_init();
$headers = array();
$auth = base64_encode($username . ':' . $apikey);
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
    http://comms.webally-mailers.com 
}
*/
