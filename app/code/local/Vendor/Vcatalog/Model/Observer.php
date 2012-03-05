<?php
class Vendor_Vcatalog_Model_Observer extends Mage_Core_Model_Abstract
{

     public function updatevEndorProfileStatus()
	    {
		  
		  #get logged in vendor id 23-2-11
          $loggedInVendorId=Mage::getSingleton('admin/session')->getUser()->user_id;
          $vinfoCollection=Mage::getModel('vcatalog/vinfo')->getCollection()->addFieldToFilter('admin_user',$loggedInVendorId)->getData();
	      $vinfo=Mage::getModel('vcatalog/vinfo');
		  $vinfo->setData('id',$vinfoCollection[0]['id']);
		  $vinfo->setData('profile_status',1);
    	  $vinfo->save();
		}
    
 }
?>
