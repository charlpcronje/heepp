<?php
namespace core\extension\centred\tz;
use core\extension\centred\CMSReq;

class TenantReq extends CMSReq {
    public function __construct($type,$endPoint,$headers = null,$params = null) {
        $this->zone = 'Tenant Zone';
        $this->headers['role'] = 'Shop Tenant';
        parent::__construct($type,$endPoint,$headers,$params,true);
    }
}
