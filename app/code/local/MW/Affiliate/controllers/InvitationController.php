<?php
include_once 'lib/mw_affiliate/openinviter.php';
class MW_Affiliate_InvitationController extends Mage_Core_Controller_Front_Action
{
	const EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH 	= 'affiliate/invitation/email_template';
    const XML_PATH_EMAIL_IDENTITY				= 'affiliate/invitation/email_sender';
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        if (!preg_match('/^(create|login|logoutSuccess|forgotpassword|forgotpasswordpost|confirm|confirmation)/i', $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }
    
	/**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    protected function getStringBetween($string, $startStr, $endStr)
    {
    	$startStrIndex = strpos($string,$startStr);
    	if($startStrIndex === false) return false;
    	$startStrIndex ++;
    	$endStrIndex = strpos($string,$endStr,$startStrIndex);
    	if($endStrIndex === false) return false;
    	return substr($string,$startStrIndex,$endStrIndex-$startStrIndex);
    }
    
   	protected function _sendEmailTransaction($emailto, $name, $template, $data)
   	{
		$storeId = Mage::app()->getStore()->getId();  
   		$templateId = Mage::getStoreConfig($template,$storeId);
		$customer = $this->_getSession()->getCustomer();
		  $translate  = Mage::getSingleton('core/translate');
		  $translate->setTranslateInline(false);
		  $sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId);
		  if(Mage::getStoreConfig('affiliate/invitation/using_customer_email'))
		  	$sender = array('name'=>$customer->getName(),'email'=>$customer->getEmail());
		  try{
		  	
			  Mage::getModel('core/email_template')
			      ->sendTransactional(
			      $templateId, 
			      $sender, 
			      $emailto, 
			      $name, 
			      $data, 
			      $storeId);
			  $translate->setTranslateInline(true);
		 /*
	      $customer_id = (int)$customer->getId();
		  $collection = Mage::getModel('affiliate/affiliateinvitation')->getCollection()
			            				->addFieldToFilter('customer_id',$customer_id)
			            				->addFieldToFilter('email',$emailto);
          if(sizeof($collection) == 0)
            	{
			 	 	$historyData = array('customer_id'=>$customer_id,
			              				 'email'=>$emailto,
			                        	 'invitation_time'=>now(),
						 	 			 'ip'=>'',
						                 'status'=>MW_Affiliate_Model_Statusinvitation::INVITATION); 
			 	 	//var_dump($historyData);exit;
            		 Mage::getModel('affiliate/affiliateinvitation')->setData($historyData)->save();
            	}*/
		  }catch(Exception $e){
		  		$this->_getSession()->addError($this->__("Email can not send !"));
		  }
   	}
   	
	public function indexAction()
    {   
    	 $store_id = Mage::app()->getStore()->getId();
   		 if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
		$active = Mage::helper('affiliate') ->getAffiliateActive();
		$lock = Mage::helper('affiliate') ->getAffiliateLock();
		if($active > 0)
		{   
			if($lock > 0)
			{
				$this->_redirect('affiliate/index/referralaccount/');
				return;
			}
	    	$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('checkout/session');
			$this->renderLayout();
		}
    	else if($active == 0)
		{
			$this->_redirect('affiliate/index/createaccount/');
		}
    }
	public function loginmailAction()
    {
    	$this->loadLayout();
    	$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
    	
    }
    public function submitmailAction()
    {
    	$ket_qua = implode(",", $this->getRequest()->getPost('mw_contact_mail'));
    	$ket_qua = "'".$ket_qua."'";
    	$contents = "";
    	$contents="<script type='text/javascript'>
    				//<![CDATA[
    					var value_old = window.opener.document.getElementById('email').value;
    					if(value_old !='')value_old = value_old+',';
    					window.opener.document.getElementById('email').value= value_old+$ket_qua;
            			window.close();
            		//]]>
					</script>";
    	echo $contents;
    	//zend_debug::dump($ket_qua);die();
    }
 	public function processmailAction()
    {
    	$ers = array();
    	$inviter = new OpenInviter();
		$oi_services = $inviter->getPlugins();
		
		$email_box = $this->getRequest()->getPost('email_box');
		$password_box = $this->getRequest()->getPost('password_box');
		$provider_box = $this->getRequest()->getPost('provider_box');
		$inviter ->startPlugin($provider_box);
		$internal = $inviter->getInternalError();
		if ($internal)
			$ers['inviter'] = $internal;
		elseif (!$inviter->login($email_box,$password_box))
		{
			$internal = $inviter->getInternalError();
			$ers['login'] = ($internal?$internal:$this->__("Login failed. Please check the email and password you have provided and try again later !"));
		}
		elseif (false === $contacts = $inviter->getMyContacts())
			$ers['contacts'] = $this->__("Unable to get contacts !");
		else
			{
				$this->loadLayout();
				$this->_initLayoutMessages('customer/session');
				$this->renderLayout();
			}
    	if(sizeof($ers))
    	{
	    	$err = implode("<br>",$ers);
	    	$this->_getSession()->addError($this->__("%s<br>",$err));
	    	$this->_redirect('affiliate/invitation/loginmail');
    	}
    }
    
    public function inviteAction()
    {   
    	$customer_id = $this->_getSession()->getCustomer()->getId();
    	$referral_code = Mage::getModel('affiliate/affiliatecustomers') ->load($customer_id)->getReferralCode();
    	$referral_sponsor = Mage::getModel('affiliate/affiliatecustomers') ->load($customer_id)->getReferralSponsor();
    	$url = $this->getRequest()->getPost('url_link');
    	$post = $this->getRequest()->getPost('email');
    	$post = trim($post," ,");
    	$emails = explode(',',$post);
    	
    	$validator = new Zend_Validate_EmailAddress();
    	$error = array();
    	foreach($emails as $email){
    		$name = $email;
    		$_name = $this->getStringBetween($email,'"','"');
    		$_email = $this->getStringBetween($email,'<','>');
    		
    		if($_email!== false && $_name !== false) 
    		{
    			$email = $_email;
    			$name = $_name;
    		}else if($_email!== false && $_name === false)
    		{
    			if(strpos($email,'"')===false)
    			{
    				$email = $_email;
    				$name = $email;
    			}
    		}
    		$email = trim($email);
	    	if($validator->isValid($email)) {
	    		// Send email to friend
				$template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH;
				$postObject = new Varien_Object();
				$customer = $this->_getSession()->getCustomer();
				$postObject->setSender($customer);
				$postObject->setMessage($this->getRequest()->getPost('message'));
				$postObject->setData('invitation_link',$url);
				$postObject->setData('referral_code',$referral_code);
				$postObject->setData('referral_sponsor',$referral_sponsor);
				$storeId = Mage::app()->getStore()->getId();
    			$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
				$postObject->setData('store_name',$store_name);
				$this->_sendEmailTransaction($email, $name, $template, $postObject->getData());
			}
			else {
			   $error[] = $email;
			}
    	}
    	if(sizeof($error))
    	{
	    	$err = implode("<br>",$error);
	    	$this->_getSession()->addError($this->__("These emails are invalid, the invitation message will not be sent to:<br>%s",$err));
    	}
		$msg = "Your email was sent success";
		if(sizeof($emails) >1) $msg = "Your Emails were sent successfully";
		if(sizeof($emails) > sizeof($error)) $this->_getSession()->addSuccess($this->__($msg));
    	$this->_redirect('affiliate/invitation/index');
    }
	public function inviteajaxAction()
    {   
    	$customer_id = $this->_getSession()->getCustomer()->getId();
    	$referral_code = Mage::getModel('affiliate/affiliatecustomers') ->load($customer_id)->getReferralCode();
    	$referral_sponsor = Mage::getModel('affiliate/affiliatecustomers') ->load($customer_id)->getReferralSponsor();
    	$url = trim($_POST["url_link"]);
    	$post = trim($_POST["email"]);
    	$message = trim($_POST["message"]);
    	if($post == "" || $message == ""){
    		header('content-type: text/javascript');
    		$mw_email = 1;
    		$mw_message = 1;
    		if($post == "") $mw_email = 0;
    		if($message == "") $mw_message = 0;
			$jsondata=array("message"=>$mw_message, 
							"email"=>$mw_email,
							"error"=>0,
							"success"=>0);
			
			echo json_encode($jsondata);
			die();	
    	}
    	$post = trim($post," ,");
    	$emails = explode(',',$post);
    	
    	$validator = new Zend_Validate_EmailAddress();
    	$error = array();
    	foreach($emails as $email){
    		$name = $email;
    		$_name = $this->getStringBetween($email,'"','"');
    		$_email = $this->getStringBetween($email,'<','>');
    		
    		if($_email!== false && $_name !== false) 
    		{
    			$email = $_email;
    			$name = $_name;
    		}else if($_email!== false && $_name === false)
    		{
    			if(strpos($email,'"')===false)
    			{
    				$email = $_email;
    				$name = $email;
    			}
    		}
    		$email = trim($email);
	    	if($validator->isValid($email)) {
	    		// Send email to friend
				$template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH;
				$postObject = new Varien_Object();
				$customer = $this->_getSession()->getCustomer();
				$postObject->setSender($customer);
				$postObject->setMessage($message);
				$postObject->setData('invitation_link',$url);
				$postObject->setData('referral_code',$referral_code);
				$postObject->setData('referral_sponsor',$referral_sponsor);
				$storeId = Mage::app()->getStore()->getId();
    			$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
				$postObject->setData('store_name',$store_name);
				$this->_sendEmailTransaction($email, $name, $template, $postObject->getData());
			}
			else {
			   $error[] = $email;
			}
    	}
    	if(sizeof($error))
    	{
	    	$err = implode("<br>",$error);
	    	$mw_error = $this->__("These emails are invalid, the invitation message will not be sent to:<br>%s",$err);
	    	//$this ->print_error($error);
	    	header('content-type: text/javascript');
    	
			$jsondata=array("message"=>1, 
							"email"=>1,
							"error"=>$mw_error,
							"success"=>0);
			echo json_encode($jsondata);
			die();
    	}
		//$msg = "Your email was sent success";
		$msg = 1;
		if(sizeof($emails) >1) $msg = 2; //$msg = "Your Emails were sent successfully";
    	if(sizeof($emails) > sizeof($error)){
    		header('content-type: text/javascript');
    	
			$jsondata=array("message"=>1, 
							"email"=>1,
							"error"=>0,
							"success"=>$msg);
			echo json_encode($jsondata);
			die();
    	}
    }
    
    public function widgetAction()
    {
    	$body = '<html><head><script type="text/javascript" src="https://www.plaxo.com/ab_chooser/abc_comm.jsdyn"></script></head><body></body></html>';
    	$this->getResponse()->setBody($body);
    }
    
    
    /*----------------------------------- Affiliate Pro v4.0 ------------------------------------*/
    public function gmailAction() {
    	$this->loadLayout();
    	$this->renderLayout();
    } 
	
}
