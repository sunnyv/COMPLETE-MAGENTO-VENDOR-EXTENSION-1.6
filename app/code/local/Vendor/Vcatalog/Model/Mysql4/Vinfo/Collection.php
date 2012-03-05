<?php

class Vendor_Vcatalog_Model_Mysql4_Vinfo_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vcatalog/vinfo');
    }
}