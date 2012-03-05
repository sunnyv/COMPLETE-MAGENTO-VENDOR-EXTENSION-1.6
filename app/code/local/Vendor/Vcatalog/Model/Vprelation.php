<?php

class Vendor_Vcatalog_Model_Vprelation extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vcatalog/vprelation');
    }
}