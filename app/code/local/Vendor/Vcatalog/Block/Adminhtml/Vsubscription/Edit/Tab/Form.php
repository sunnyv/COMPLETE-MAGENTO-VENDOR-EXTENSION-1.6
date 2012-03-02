<?php

class Vendor_Vcatalog_Block_Adminhtml_Vsubscription_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vcatalog_form', array('legend'=>Mage::helper('vcatalog')->__('Subscription Information')));
     
      $fieldset->addField('subsname', 'text', array(
          'label'     => Mage::helper('vcatalog')->__('Subscription Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'subsname',
      ));
	  
	  $fieldset->addField('duration', 'text', array(
          'label'     => Mage::helper('vcatalog')->__('Duration'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'duration',
      ));

      $fieldset->addField('amout', 'text', array(
          'label'     => Mage::helper('vcatalog')->__('Amount'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'amout',
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