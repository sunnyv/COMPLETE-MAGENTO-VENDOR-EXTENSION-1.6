<?php
class Vendor_Vcatalog_PaypalController extends Mage_Core_Controller_Front_Action
 { 
      
    public function indexAction()
    {
		  
     		
		$this->loadLayout();   
	    $this->renderLayout();
   	}
   
   
   
 function hash_call($methodName,$nvpStr)
  {
     #call the function where we have defined the elememts
	$this->defineElements();

	$API_UserName=API_USERNAME;
	
	$API_Password=API_PASSWORD;
	
	$API_Signature=API_SIGNATURE;

	$API_Endpoint =API_ENDPOINT;
	
	$version=VERSION;

	$subject = SUBJECT;

	//setting the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	//turning off the server and peer verification(TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST, 1);
    //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
   //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php 
	if(USE_PROXY)
	curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT); 

	//NVPRequest for submitting to server
	if(API_AUTHENTICATION_MODE == '3TOKEN')
	{
		$nvpreq="METHOD=".urlencode($methodName)."&VERSION=".urlencode($version)."&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature).$nvpStr;
	}
	else if(API_AUTHENTICATION_MODE == 'UNIPAY')
	{
		$nvpreq="METHOD=".urlencode($methodName)."&VERSION=".urlencode($version)."&SUBJECT=".urlencode($subject).$nvpStr;
			
	}
	
	
	//setting the nvpreq as POST FIELD to curl
	curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

	//getting response from server
	$response = curl_exec($ch);

	//convrting NVPResponse to an Associative Array
	$nvpResArray=$this->deformatNVP($response);
	$nvpReqArray=$this->deformatNVP($nvpreq);
	$_SESSION['nvpReqArray']=$nvpReqArray;

	if (curl_errno($ch)) {
		// moving to display page to display curl errors
	      echo curl_error;
     } else {
		 //closing the curl
			curl_close($ch);
	  }

return $nvpResArray;
}

/** This function will take NVPString and convert it to an Associative Array and it will decode the response.
  * It is usefull to search for a particular key and displaying arrays.
  * @nvpstr is NVPString.
  * @nvpArray is Associative Array.
  */

function deformatNVP($nvpstr)
{   
    #call the function where we have defined the elememts
	 $this->defineElements();
   	$intial=0;
 	$nvpArray = array();


	while(strlen($nvpstr)){
		//postion of Key
		$keypos= strpos($nvpstr,'=');
		//position of value
		$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

		/*getting the Key and Value values and storing in a Associative Array*/
		$keyval=substr($nvpstr,$intial,$keypos);
		$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
		//decoding the respose
		$nvpArray[urldecode($keyval)] =urldecode( $valval);
		$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
     }
	return $nvpArray;
}
    
	
	
	public function getResponseAction()
	 {
	  
	     #get the requested parametes
		 $payInfo=$this->getRequest()->getPost('payment');
  
       	#get the first name and last name for the use that we need to send to the api
	     $vendorInfoCollection=Mage::getModel('vcatalog/vinfo')->load($payInfo['vid'])->getData();
        
		 #get the subsciption details to store in the datanase and need to get the amount to send to payment gateway API
	     $subsInfo=Mage::getModel('vcatalog/vsubscription')->load($payInfo['subsid'])->getData();
	    
		
	  
	   
	     $creditCardType = $payInfo['cc_type'];
	  	 switch($creditCardType){
	 
			 case 'VI':
			 $creditCardTypes='Visa';
			 break;
			 
			 case 'AE':
			 $creditCardTypes='Amex'; 
			 break;
            
			 case 'MC':
			 $creditCardTypes='MasterCard';
			 break;
			 
			 case 'DI':
			 $creditCardTypes='Discover';
			 break;
			 
			 default:
			 $creditCardTypes='Visa';
 
		 }
		  #set of values to send to api
		   	$creditCardNumber=$payInfo['cc_number'];
			$expDateMonth=$payInfo['cc_exp_month'];
		    $expDateYear=$payInfo['cc_exp_year'];	
			$cvv2Number=$payInfo['cc_cid'];
		    $amount = $subsInfo['amout'];  
			$currencyCode="USD"; 
			$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
			$paymentType='Sale';
			$first_name=$vendorInfoCollection['first_name'];
			$last_name=$vendorInfoCollection['last_name'];
		    $profilestartDate=date('Y-m-d H:i:s');
			
		
 $nvpstr="&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardTypea&ACCT=$creditCardNumber&BILLINGPERIOD=Month&BILLINGFREQUENCY=1&PROFILESTARTDATE=$profilestartDate&EXPDATE=".$padDateMonth.$expDateYear."&CVV2=$cvv2Number&FIRSTNAME=$first_name&LASTNAME=$last_name&CURRENCYCODE=$currencyCode";
 
//$nvpstr="&PAYMENTACTION=Sale&AMT=328&CREDITCARDTYPE=visa&ACCT=4197762548840495&BILLINGPERIOD=day&BILLINGFREQUENCY=1&EXPDATE=012020&CVV2=962&FIRSTNAME=John&LASTNAME=Doe&COUNTRYCODE=US&CURRENCYCODE=USD";		
			$resArray=$this->hash_call("doDirectPayment",$nvpstr);	
        
		echo "<pre>";
		print_r($resArray);
		
		if(!empty($resArray)){
		 if($resArray['ACK']=='Success')
		  {
		    $vpaymentinfo=Mage::getModel('vcatalog/vpaymentinfo');
		    $vpaymentinfo->setData('vendor_id', $payInfo['vid']);
			$vpaymentinfo->setData('subscription_years',$subsInfo['duration']);
			$vpaymentinfo->setData('amount',$amount);
			$vpaymentinfo->setData('payment_methoed','credit/debit');
			$vpaymentinfo->setData('payment_status','1');
			$vpaymentinfo->setData('payment_date',$resArray['TIMESTAMP']);
			$vpaymentinfo->setData('transaction_id',$resArray['TRANSACTIONID']);
		
			if($vpaymentinfo->save()){
	  		  $vendorInfo=Mage::getModel('vcatalog/vinfo');
	  
	  	  #set promary key to update the payment status in vinfo table
			   $vendorInfo->setData('id',$payInfo['vid']);
			   $vendorInfo->setData('payment_status',1);
			    if($vendorInfo->save())
				 {
				    $userInfoCollection = Mage::getModel('admin/user')->load($vendorInfoCollection['admin_user'])->getData();
					
					  #once payment is active actiavte the user account
					  #set the admin user model
					  $adminuser = Mage::getModel('admin/user'); 
					  $adminuser->setData('user_id',$vendorInfoCollection['admin_user']);
					  $adminuser->setData('is_active',1);
					  $adminuser->save();
					  
					  #set admin url to redirect
					  $url = Mage::getUrl()."admin";
					  header("Status: 301");
					  header("Location:$url");
					  exit;
			     }
			   }
    	     }      
			 else{$params=array('message'=>'pfailed');
		     $this->_redirect('vregistration/index/payment', $params);
		      }
    	} 
		
        
	
	}
   
    function defineElements()
	 {
            define('API_AUTHENTICATION_MODE','3TOKEN');
            define('API_USERNAME', 'testin_1312185902_biz_api1.gmail.com');
            define('API_PASSWORD', '1312185958');
            define('API_SIGNATURE', 'AmqF7.XT.maPF3Qnxk5ZJ8vzG3oQAOjRwureZ5AYSW0BgdyE2zaTxN6v ');
            define('API_ENDPOINT', 'https://api-3t.sandbox.paypal.com/nvp');
            define('SUBJECT','sdk-three@sdk.com');
            define('USE_PROXY',FALSE);
            define('PROXY_HOST', '127.0.0.1');
            define('PROXY_PORT', '808');
            define('PAYPAL_URL', 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=');
            define('VERSION', '60.0');
            define('ACK_SUCCESS', 'SUCCESS');
            define('ACK_SUCCESS_WITH_WARNING', 'SUCCESSWITHWARNING');
 
	 } 
}
?>
