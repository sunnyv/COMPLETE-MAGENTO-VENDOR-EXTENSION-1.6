<?php
class Vendor_Vcatalog_Block_Adminhtml_Vsubscription extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
				$this->_controller = 'adminhtml_vsubscription';
				$this->_blockGroup = 'vcatalog';
				$this->_headerText = Mage::helper('vcatalog')->__('Subscription Manager');
				$this->_addButtonLabel = Mage::helper('vcatalog')->__('Add Subscription');
				parent::__construct();
  }
}