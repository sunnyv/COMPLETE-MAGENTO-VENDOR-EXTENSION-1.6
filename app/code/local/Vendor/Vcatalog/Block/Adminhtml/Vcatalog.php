<?php
class Vendor_Vcatalog_Block_Adminhtml_Vcatalog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_vcatalog';
    $this->_blockGroup = 'vcatalog';
    $this->_headerText = Mage::helper('vcatalog')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('vcatalog')->__('Add Item');
    parent::__construct();
  }
}