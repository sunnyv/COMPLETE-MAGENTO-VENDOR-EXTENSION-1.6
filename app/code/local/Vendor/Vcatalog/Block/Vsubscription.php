<?php
class Vendor_Vcatalog_Block_Vsubscription extends Mage_Core_Block_Template
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
 
    
	public function getSubscriptions()     
     { 
	   
	   $vcatalog=Mage::getModel('vcatalog/vsubscription')->getCollection()->getData();
     
	   if(!empty($vcatalog)):
	 
	     return $vcatalog; 
	 
	  endif;
     }
 
    public function vendorId()
    {
	  return Mage::registry('id');
	} 
}
?>