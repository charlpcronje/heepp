<?php
namespace core\element\centred\mall;
use core\extension\ui\coreFO;
use core\extension\api\client\RestClient;

class tradinghours extends \core\Element {
    public $docs = false;
    
    function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }
    
    private function apiCall() {
        $baseUrl = 'http://cmscentred.co.za/cmscentred/';
        $apiLocation = 'api/v1/';
        
        $client = new RestClient([
            'base_url'    => $baseUrl.$apiLocation,
            'headers'    => ['Authorization' => 'Bearer '.MALL_KEY, 'domain' => 'clearwatermall.co.za', 'Accept' => 'application/json'],
            'user_agent' => 'core',
            'format' => '',
            'curl_options' => [CURLOPT_SSL_VERIFYPEER => false]
        ]);
        
        $result = $client->get('malls/1');
        if ($result->info->http_code == 200) {
            return json_decode($result->response,true);
        } else {
            $this->setError($result->error);
        }
    }
    
    function render() {
        $hours = $this->apiCall();
        $this->setData('hours',$hours);
    }
}
