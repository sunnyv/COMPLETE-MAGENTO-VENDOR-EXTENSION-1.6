<?php

class Vendor_Vcatalog_Model_Mysql4_Vprelation extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
       // parent::_construct();
        $this->_init('vcatalog/vprelation','id');
    }
}