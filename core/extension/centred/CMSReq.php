<?php
namespace core\extension\centred;
use core\Heepp;
use core\extension\api\client\RestClient;
use core\extension\Extension;
use core\extension\database\Model;
use core\system\log;

class CMSReq extends Extension {
    protected $success   = false;
    protected $verb      = 'get';
    protected $apiEndPoint;
    protected $zone      = 'Web';
    protected $userAgent = 'core';
    protected $headers   = [
        'Authorization' => 'Bearer ',
        'Accept'        => 'application/json',
        'role'          => null
    ];
    protected $result;
    protected $response;
    protected $params    = [];
    protected $error;
    private $logRequests = false;
    private $startTime   = 0;
    private $endTime     = 0;
    private $elapsedTime = 0;
    private $cmsAPIkey;

    public function __construct($verb = 'GET',$endPoint = null,$headers = null,$params = null,$request = false) {
        parent::__construct();
        $this->setVerb($verb);
        $this->verb = strtolower($verb);
        $this->headers['Authorization'] = 'Bearer '.$this->getData('app.api.cms.auth.key');
        if ($request) {
            if(isset($endPoint)) {
                $this->apiEndPoint = $endPoint;
            }
            $this->mergeArgs($headers,$params);
            $this->request();
        }
    }

    public function setVerb($verb) {
        $verb = strtoupper($verb);
        $this->verb = $verb;
        return $verb;
    }

    public function verb($verb) {
        $this->setVerb($verb);
        return $this;
    }

    public function resetAndVerb($verb) {
        $this->reset();
        $this->setVerb($verb);
        return $this;
    }

    public function method($method) {
        $this->resetAndVerb($method);
        return $this;
    }

    public function endpoint($endpoit) {
        $this->setEndPoint($endpoit);
        return $this;
    }

    public function url($url) {
        $this->setEndPoint($url);
        return $this;
    }

    public function setEndPoint($endpoint) {
        if (isset($endpoint)) {
            $this->apiEndPoint = $endpoint;
        }
    }

    public function addHeader($key,$value) {
        $this->headers[$key] = $value;
        return $value;
    }

    public function header($key,$value) {
        $this->addHeader($key,$value);
        return $this;
    }

    public function params($params) {
        foreach($params as $key => $value) {
            $this->addParam($key,$value);
        }
        return $this;
    }

    public function addParam($key,$value) {
        $this->params[$key] = $value;
        return $value;
    }

    public function param($key,$value) {
        $this->addParam($key,$value);
        return $this;
    }

    private function wait($reset = false) {
        if ($reset) {
            $this->reset();
        }
    }

    private function reset() {
        $this->verb        = 'get';
        $this->apiEndPoint = null;
        $this->zone        = 'Web';
        $this->userAgent   = 'core';
        $this->headers     = [
            'Authorization' => 'Bearer ',
            'Accept'        => 'application/json',
            'role'          => null
        ];
        $this->result    = null;
        $this->response  = null;
        $this->params    = [];
        $this->error     = null;
    }

    public function call($dotDataKey = null) {
        $this->getConfigSettings();
        $this->mergeArgs($this->headers,$this->params);
        $this->request();
        $this->result();
        return $this->result->response;
    }

    public function getConfigSettings() {
        $this->addHeader('role',Heepp::data('app.api.cms.role'));
        $this->setAPIKey();
    }

    private function setAPIKey() {
        $this->cmsAPIkey = Heepp::data('app.api.cms.auth.key');
        $this->headers['Authorization'] = 'Bearer '.$this->cmsAPIkey;
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
        $this->getConfigSettings();
        return md5(json_encode([$this->headers,$this->params]),true);
    }

    protected function request() {
        $this->startTime = getMicrotime();
        $api = new RestClient([
            'base_url' => $this->getData('app.api.cms.url'),
            'user_agent' => 'core',
            'headers' => $this->headers,
            'curl_options' => [CURLOPT_SSL_VERIFYPEER => true],
            'parameters'=> $this->params
        ]);


        $this->endTime = getMicrotime();
        $this->elapsedTime = (float)$this->endTime - (float)$this->startTime;
        $this->verb = strtoupper($this->verb);
        //pd($this->headers);
        $response = $api->{$this->verb}($this->apiEndPoint);

        if ($response->info->http_code == 200) {
            $this->response = json_decode($response->response);
            $this->success = true;
        } else {
            $this->success = false;
            $this->response = json_decode($response->response);
            $this->error = $response->error;
        }
        if ($this->logRequests) {
            $this->logRequest();
        }
    }

    private function logRequest() {
        $logKeyValuePairs = [
            'from_app'     => $this->getData('app.website.name'),
            'from_url'     => $this->getData('app.website.url'),
            'endpoint'     => $this->apiEndPoint,
            'verb'         => $this->verb,
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
        ];
        (new Model('apiRequestLog'))->insert($logKeyValuePairs);
        log::info(['json' => json_encode($logKeyValuePairs,JSON_PRETTY_PRINT)],str_replace('\\','.',__CLASS__.'-'.CMSReq::class));
    }

    /** @noinspection ClassMethodNameMatchesFieldNameInspection */
    public function result() {
        // Do not return the API key in result
        unset($this->headers['Authorization']);        
        return $this->result = (object)[
            'success'     => $this->success,
            'verb'        => $this->verb,
            'endPoint'    => $this->apiEndPoint,
            'zone'        => $this->zone,
            'userAgent'   => $this->userAgent,
            'headers'     => $this->headers,
            'response'    => $this->response,
            'params'      => $this->params,
            'error'       => $this->error
        ];
    }
}
