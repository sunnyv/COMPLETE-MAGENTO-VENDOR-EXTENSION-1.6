<?php

class Vendor_Vcatalog_Model_Mysql4_Vinfo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the vcatalog_id refers to the key field in your database table.
		 $this->_init('vcatalog/vinfo', 'id');
    }
}