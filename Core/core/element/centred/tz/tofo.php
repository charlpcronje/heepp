<?php
namespace core\element\centred\tz;
use core\extension\api\client\RestClient;

class tofo extends \core\Element {
    function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }
    
    function render() {
        $api = new RestClient([
            'base_url' => API_URL,
            'user_agent' => 'core',
            'headers' => [
                'Authorization' => API_KEY,
                'Accept' => 'application/json',
                'email' => $this->input('email'),
                'password' => $this->input('password'),
                'role' => 'Shop Tenant'
            ],
            'curl_options' => [CURLOPT_SSL_VERIFYPEER => false]
        ]);
        
        $result = $api->get('users/authenticate');
        if ($result->info->http_code == 200) {
            $response = json_decode($result->response);
            if ($response[0]->id) {
                $this->setNotify('success','Login Succesful');
                $this->session('user',$response[0]);
                $this->refreshPage();
            }
        } else {
            $this->setError('E-Mail And Password does not match');
        }
    }
}
