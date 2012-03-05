<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog manage products block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Vendor_Overridecontroller_Block_Catalog_Product extends Mage_Adminhtml_Block_Catalog_Product
{   
   
    protected function _prepareLayout()
    {
        #get the info to check wheather the loggedin vendor has profile or not if have then disable the add product button 23-2-11
		$getLoggedInVendorProfileInfo=$this->getLoggedInVendorProfileInfo();
        if(!$getLoggedInVendorProfileInfo)
		{
	         $this->_addButton('add_new', array(
             'label'   => Mage::helper('catalog')->__('Add Product'),
             'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
             'class'   => 'add'
          )); 
       }
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/catalog_product_grid', 'product.grid'));
        
	    //return parent::_prepareLayout();
    }

    /*
	 *function to check wheather the user has already profile or not 21-2-11
	 *function getLoggedInVendorProfileInfo
	 */
	 
      protected function getLoggedInVendorProfileInfo()
	   {
               $loggedInVendorId=Mage::getSingleton('admin/session')->getUser()->user_id;
              #get the model of vinfo to find the profile status of vendor
               $vprelationCollection=Mage::getModel('vcatalog/vinfo')->getCollection()->addFieldToFilter('admin_user',$loggedInVendorId)->getData();
              return $vprelationCollection[0]['profile_status'];
      }

}
