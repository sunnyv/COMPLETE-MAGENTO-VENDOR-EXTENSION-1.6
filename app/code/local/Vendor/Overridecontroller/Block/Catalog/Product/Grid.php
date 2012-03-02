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
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Vendor_Overridecontroller_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    protected function _prepareCollection() 
	{
        $store = $this->_getStore();
        
		/*custom code to show the respective profile to respective vendor 21-2-12*/
		#get the array of profiles related to logged in vendor
	    $getLoggedInVendorProfileInfo=$this->getLoggedInVendorProfileInfo();
              foreach($getLoggedInVendorProfileInfo as $getLoggedInVendorProfileInfos):
                $arr[] = $getLoggedInVendorProfileInfos['profileid'];
	          endforeach;
	       $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id');
	     
		 /*add FieldToFilter for to gether the respective profiles for 
		   loggen in vendor(product)if logged in vendor is not admin 23-2-12*/
	       if($this->getLoggedInVendorId()!=1){
		     $collection->addFieldToFilter('entity_id',$arr);	
		    }
			
			if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

            $this->setCollection($collection);
            //parent::_prepareCollection();
            $this->getCollection()->addWebsiteNamesToResult();
            return $this;
    	}
 
       protected function getLoggedInVendorProfileInfo(){
              $loggedInVendorId=$this->getLoggedInVendorId();
              #get the model of vprelation to find the respective product of same user 23-2-12
              $vprelationCollection=Mage::getModel('vcatalog/vprelation')->getCollection()->addFieldToFilter('vendorid',$loggedInVendorId)->getData();
              return $vprelationCollection;
       }
    
	  protected function getLoggedInVendorId()
	   {
	     return Mage::getSingleton('admin/session')->getUser()->user_id;
	   }

      /*
	  *override the function to hide the options of visibility for non logged in user 29-1-12
	  *@ovveride _prepareColumns
	  */
	   protected function _prepareColumns(){	   
	    
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
            ));
        }

   #add this column only for admin not for vendor 29-2-12
        if(Mage::getSingleton('admin/session')->getUser()->user_id==1){
	    $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));
    }
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),

                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
         ));

         if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
             $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
         }

        //return parent::_prepareColumns();
    
	 
	    }

}