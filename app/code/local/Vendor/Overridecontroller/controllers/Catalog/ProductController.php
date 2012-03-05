<?php
include_once("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Vendor_Overridecontroller_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
#overrided save action 
/**
     * Save product action
     */
    public function saveAction()
    {
    
      
	   #check wheather in edit mode or new mode  as we need to check only in new mode  
	     $productId   = $this->getRequest()->getParam('id');	   
          if(empty($productId)){
  	    /*check wheather the logged in vendor has already product or not*/
    	     $getLoggedInVendorProfileInfo=$this->getLoggedInVendorProfileInfo();
	            if($getLoggedInVendorProfileInfo){
   		          $this->_getSession()->addError($this->__('you are not allowed to add one more profile'));
	               $url = Mage::getUrl()."admin/catalog_product/index";
					  header("Status: 301");
					  header("Location:$url");
					  exit;
	            }
	       } 
	   
	    $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();
        if ($data) {
            if (!isset($data['product']['stock_data']['use_config_manage_stock'])) {
                $data['product']['stock_data']['use_config_manage_stock'] = 0;
            }
            $product = $this->_initProductSave();

            try {	
                $product->save();
                $productId = $product->getId();

                 #get the vendor profile(product) relation table model to save the respective data 19-2-12
			       $vprelationCollection=Mage::getModel('vcatalog/vprelation')->getCollection()->AddFieldToFilter('profileid',$productId)->getData();
				
   				 #call the model of the vprelation table 19-2-11
				   $vprelation=Mage::getModel('vcatalog/vprelation');
					   if(empty($vprelationCollection)){
					   $loggedInVendorId=Mage::getSingleton('admin/session')->getUser()->user_id;
					   #save the vendor and profile realtion in the vprelation table 19-2-12
					   $vprelation->setData('vendorid',$loggedInVendorId);
					   $vprelation->setData('profileid',$productId);
					   $vprelation->save();
					 }
				
			    /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newProduct = Mage::getModel('catalog/product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }

                Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($productId);
                $this->_getSession()->addSuccess($this->__('The product has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }
    
        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $productId,
                '_current'=>true
            ));
        } elseif($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current'   => true,
                'id'         => $productId,
                'edit'       => $isEdit
            ));
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
   
   
    }
 
 
     /**
     * Delete product action
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
               $product = Mage::getModel('catalog/product')
			          ->load($id);
            $sku = $product->getSku();
            try {
                //$product->delete();
			#delete the respective profile id  from vprelation table 
			$vprelationCollection=Mage::getModel('vcatalog/vprelation')->getCollection()->addFieldToFilter('profileid',$id)->getData();
    		 $vprelation=Mage::getModel('vcatalog/vprelation')
			   ->setId($vprelationCollection[0]['id'])
		       ->delete();
			   
			   /*if the profile(product) for a vendor has been deleted then update the profile status to '0' in vinfo.. 
					     check wheather the profileid for logged in vendor is empty 
					     or not if empry in vprelation table set profile status to '0' in vinfo table..23-2-11*/ 
					    
						 $vprofileCollection=Mage::getModel('vcatalog/vprelation')->getCollection()->addFieldToFilter('vendorid',$this->getLoggedInVendorId())->getData();
					       if(empty($vprofileCollection)){
					         $vinfoCollection=Mage::getModel('vcatalog/vinfo')->getCollection()->addFieldToFilter('admin_user',$this->getLoggedInVendorId())->getData();
						     #get the model for vinfo table to update the profile status field 23-2-11
					           $vendorId=$vinfoCollection[0]['id'];
							   $vinfo=Mage::getModel('vcatalog/vinfo');
						       $vinfo->setData('id',$vendorId);
							   $vinfo->setData('profile_status',0);
							   $vinfo->save();
					} 
			     	
                $this->_getSession()->addSuccess($this->__('The product has been deleted.'));
            } catch (Exception $e) {
               $this->_getSession()->addError($e->getMessage());
            }
        }
     $this->getResponse()
           ->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }
 
 
     public function massDeleteAction()
     {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                         $product = Mage::getSingleton('catalog/product')->load($productId);
                          Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
                        $product->delete();
                    
					  #delete the bulk records from vprelation table respective to deleted product from backend 23-2-11
					     $vprelationCollection=Mage::getModel('vcatalog/vprelation')->getCollection()->addFieldToFilter('profileid',$productId)->getData();
					     $vprelation=Mage::getModel('vcatalog/vprelation')
			             ->load($vprelationCollection[0]['id'])
		                 ->delete();
					  
					   /*if the profile(product) for a vendor has been deleted then update the profile status to '0' in vinfo.. 
					     check wheather the profileid for logged in vendor is empty 
					     or not if empry in vprelation table set profile status to '0' in vinfo table..23-2-11*/ 
					    
						 $vprofileCollection=Mage::getModel('vcatalog/vprelation')->getCollection()->addFieldToFilter('vendorid',$this->getLoggedInVendorId())->getData();
					       if(empty($vprofileCollection)){
					         $vinfoCollection=Mage::getModel('vcatalog/vinfo')->getCollection()->addFieldToFilter('admin_user',$this->getLoggedInVendorId())->getData();
						     #get the model for vinfo table to update the profile status field 23-2-11
					           $vendorId=$vinfoCollection[0]['id'];
							   $vinfo=Mage::getModel('vcatalog/vinfo');
						       $vinfo->setData('id',$vendorId);
							   $vinfo->setData('profile_status',0);
							   $vinfo->save();
							 
						   
						}
					}
					
					
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    
	
	 }
	
 
     /*
	 *get loggen in vendor's profiles ids from vprelation table 21-2-12
	 *
	 */
      protected function getLoggedInVendorProfileInfo()
	   {
	            $loggedInVendorId=Mage::getSingleton('admin/session')->getUser()->user_id;
                #get the model of vinfo to find the profile status of vendor
                $vprelationCollection=Mage::getModel('vcatalog/vinfo')->getCollection()->addFieldToFilter('admin_user',$loggedInVendorId)->getData();
                return $vprelationCollection[0]['profile_status'];
       }

  /*
  *Get loggen in vendor id
  *function getLoggedInVendorId 21-2-12
  */
  
   protected function getLoggedInVendorId()
	   {
	     return Mage::getSingleton('admin/session')->getUser()->user_id;
	   }

}
?>