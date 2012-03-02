<?php

class Vendor_Vcatalog_Block_Adminhtml_Vcatalog_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vcatalog';
        $this->_controller = 'adminhtml_vcatalog';
        
        $this->_updateButton('save', 'label', Mage::helper('vcatalog')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('vcatalog')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vcatalog_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vcatalog_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vcatalog_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('vcatalog_data') && Mage::registry('vcatalog_data')->getId() ) {
            return Mage::helper('vcatalog')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('vcatalog_data')->getTitle()));
        } else {
            return Mage::helper('vcatalog')->__('Add Item');
        }
    }
}