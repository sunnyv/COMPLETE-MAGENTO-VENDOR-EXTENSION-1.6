<?php
class Vendor_Vcatalog_IndexController extends Mage_Core_Controller_Front_Action
{ 
    public function indexAction()
    {
		  
     	/*
    	if($vcatalog == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$vcatalogTable = $resource->getTableName('vcatalog');
			
			$select = $read->select()
			   ->from($vcatalogTable,array('vcatalog_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
  			$vcatalog = $read->fetchRow($select);
		}
		Mage::register('vcatalog', $vcatalog);
		*/

		
		$this->loadLayout();   
	    $this->renderLayout();
   	}
	
	 public function createAction()
	  {
				 
			#custom code to check the roles resources
			/*$resources = Mage::getModel('admin/roles')->getResourcesTree();
				$nodes = $resources->xpath('//*[@aclpath]');            
				echo '<dl>';
				foreach($nodes as $node)
				{
					echo '<dt>' . (string)$node->title . '</dt>';
					echo '<dd>' . $node->getAttribute('aclpath') . '</dd>';
				}
				echo '</dl>';   die;
				echo "<pre>";
				print_r($resources);*/
				
	  
	  	   /* $resources = Mage::getModel('admin/roles')->getResourcesTree();
    		//alternate syntax for the same thing
			    $resources = Mage::getModel('admin/roles')
			 	->getResourcesTree();
			    //echo "<pre>";
				//print_r($resources);*/
						
        	$business_category=$this->getRequest()->getParam('business_category');
	       	$region=$this->getRequest()->getParam('region');
		   	$country=$this->getRequest()->getParam('country');
		    $business_name=$this->getRequest()->getParam('business_name');
		    $business_phone_number=$this->getRequest()->getParam('business_phone_number');
		    $postal_code=$this->getRequest()->getParam('postal_code');
			$username=$this->getRequest()->getParam('username');
		    $first_name=$this->getRequest()->getParam('first_name');
	 	    $last_name=$this->getRequest()->getParam('last_name');
		    $eaddr=$this->getRequest()->getParam('eaddr');
		    $password=$this->getRequest()->getParam('password');
	if(!empty($business_name)){
		
  		  #save the values to admin tables to get to register the vendors
			try{
				     $user = Mage::getModel('admin/user');
				     $user->setData('username',$username);
					 $user->setData('firstname',$first_name);
					 $user->setData('lastname',$last_name);
					 $user->setData('email',$eaddr);
					 $user->setData('password',$password);
					 $user->setData('is_active',0);	
					
					if($user->save()) {
                                  $user->getUserId();
								  $vinfo=Mage::getModel('vcatalog/vinfo');
								  $vinfo->setData('admin_user', $user->getUserId());
								  $vinfo->setData('business_category', $business_category);
								  $vinfo->setData('region', $region);
								  $vinfo->setData('country', $country);
								  $vinfo->setData('business_name', $business_name);
								  $vinfo->setData('business_phone_number', $business_phone_number);
								  $vinfo->setData('postal_code', $postal_code);
								  $vinfo->setData('username', $username);
								  $vinfo->setData('first_name', $first_name);
								  $vinfo->setData('last_name', $last_name);
								  $vinfo->setData('eaddr', $eaddr);
								  $vinfo->setData('password', $password);
								  $vinfo->save();
								  
							#check wheather role already exists or not
						
							  $rolesCollection = Mage::getModel('admin/roles')->getCollection()->addFieldToFilter('role_name', 'Vendors')->getData(); // here you'll get a collection
								if(empty($rolesCollection))
								 { 
										 //create new role
										   $role = Mage::getModel("admin/roles")
										  ->setName('Vendors')
										  ->setRoleType('G')
										  ->save();
				
									 //give "all" privileges to role
											Mage::getModel("admin/rules")
											->setRoleId($role->getId())
											->setResources(array("admin/vcatalog","admin/vcatalog/items","admin/catalog","admin/catalog/categories","admin/catalog/products"))
											->saveRel();	
									  
										#assign role to user
										   $user->setRoleIds(array($role->getId()))
											->setRoleUserId($user->getUserId())
											->saveRelations();
							    }else{
									  foreach($rolesCollection as $rolesCollections):
										$user->setRoleIds(array($rolesCollections['role_id']))
										->setRoleUserId($user->getUserId())
										->saveRelations();
									   endforeach;
								    }
			   
									   #get lastinsertid	  
										$lastInsertId = $vinfo->getid();
										$params =array ('message'=>'success','id'=>$lastInsertId);
										$this->_redirect('vregistration/index/payment', $params);
												
						  }	   
			   
			   
		      }catch(Exception $e){
	            Mage::getSingleton('vcatalog/session')->addError($this->__($e->getMessage()));
	       }
		
	}		
				 
            
			$this->loadLayout();    
			$this->_initLayoutMessages('vcatalog/session');  
			$this->renderLayout();
   
  }

   public function paymentAction()
    {
	 
	#get id from utl
	   $id=$this->getRequest()->getParam('id');
	   Mage::register('id',$id);
	#End  
	    
     $message=$this->getRequest()->getParam('message');
	   if($message=='success') {
	    Mage::getSingleton('vcatalog/session')->addSuccess($this->__('Please choose the respective plan and click the button to pay'));
	  }
	   if($message=='pfailed') {
	    Mage::getSingleton('vcatalog/session')->addError($this->__('Payment has not completed sucessfully'));
	   }
 
	  $this->loadLayout();
	  $this->_initLayoutMessages('vcatalog/session');  
		  $this->renderLayout();    
	}

  function testAction()
  {
    echo "<pre>";
    print_r(Mage::getModel('catalog/product')->getCollection());

  }
}