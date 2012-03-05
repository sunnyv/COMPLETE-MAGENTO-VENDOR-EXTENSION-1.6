<?php
class Vendor_Vcatalog_Block_Vcatalog extends Mage_Core_Block_Template
{
	public function _prepareLayout()
     {
		return parent::_prepareLayout();
     }
    
 
     public function getVcatalog()     
      { 
	  
        if (!$this->hasData('vcatalog')) {
            $this->setData('vcatalog', Mage::registry('vcatalog'));
        }
        return $this->getData('vcatalog');
 	 }
 
   
}
?>