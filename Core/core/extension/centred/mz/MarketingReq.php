<?php
namespace core\extension\centred\mz;
use core\extension\centred\CMSReq;

class MarketingReq extends CMSReq {
    public function __construct($type,$endPoint,$headers = null,$params = null) {
        $this->zone = 'Marketing Zone';
        $this->headers['role'] = 'Marketing Manager';
        parent::__construct($type,$endPoint,$headers,$params,true);
    }
}
