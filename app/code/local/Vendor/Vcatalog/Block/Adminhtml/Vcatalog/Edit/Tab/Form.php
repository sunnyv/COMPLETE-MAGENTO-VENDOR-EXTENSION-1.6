<?php

class Vendor_Vcatalog_Block_Adminhtml_Vcatalog_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vcatalog_form', array('legend'=>Mage::helper('vcatalog')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('vcatalog')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('vcatalog')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('vcatalog')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('vcatalog')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('vcatalog')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('vcatalog')->__('Content'),
          'title'     => Mage::helper('vcatalog')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getVcatalogData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getVcatalogData());
          Mage::getSingleton('adminhtml/session')->setVcatalogData(null);
      } elseif ( Mage::registry('vcatalog_data') ) {
          $form->setValues(Mage::registry('vcatalog_data')->getData());
      }
      return parent::_prepareForm();
  }
}