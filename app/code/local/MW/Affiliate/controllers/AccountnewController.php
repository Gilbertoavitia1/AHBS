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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once 'Mage/Customer/controllers/AccountController.php';
class MW_Affiliate_AccountnewController extends Mage_Customer_AccountController
{    
    public function createPostAction()
    {   
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            //$this->_redirect('*/*/');
            $this->_redirect('affiliate/index/createaccount');
            return;
        }
        $Enterprise_pro = Mage::helper('affiliate') ->getEnterprisePro();
        if($Enterprise_pro ||(!$Enterprise_pro && version_compare(Mage::getVersion(),'1.4.2.0','>=')) )
        {
        	$session->setEscapeMessages(true); // prevent XSS injection in user input
	        if ($this->getRequest()->isPost()) {
	            $errors = array();
	
	            if (!$customer = Mage::registry('current_customer')) {
	                $customer = Mage::getModel('customer/customer')->setId(null);
	            }
	
	            /* @var $customerForm Mage_Customer_Model_Form */
	            $customerForm = Mage::getModel('customer/form');
	            $customerForm->setFormCode('customer_account_create')
	                ->setEntity($customer);
	
	            $customerData = $customerForm->extractData($this->getRequest());
	
	            if ($this->getRequest()->getParam('is_subscribed', false)) {
	                $customer->setIsSubscribed(1);
	            }
	
	            /**
	             * Initialize customer group id
	             */
	            $customer->getGroupId();
	
	            if ($this->getRequest()->getPost('create_address')) {
	                /* @var $address Mage_Customer_Model_Address */
	                $address = Mage::getModel('customer/address');
	                /* @var $addressForm Mage_Customer_Model_Form */
	                $addressForm = Mage::getModel('customer/form');
	                $addressForm->setFormCode('customer_register_address')
	                    ->setEntity($address);
	
	                $addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
	                $addressErrors  = $addressForm->validateData($addressData);
	                if ($addressErrors === true) {
	                    $address->setId(null)
	                        ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
	                        ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
	                    $addressForm->compactData($addressData);
	                    $customer->addAddress($address);
	
	                    $addressErrors = $address->validate();
	                    if (is_array($addressErrors)) {
	                        $errors = array_merge($errors, $addressErrors);
	                    }
	                } else {
	                    $errors = array_merge($errors, $addressErrors);
	                }
	            }
	
	            try {
	                $customerErrors = $customerForm->validateData($customerData);
	                if ($customerErrors !== true) {
	                    $errors = array_merge($customerErrors, $errors);
	                } else {
	                    $customerForm->compactData($customerData);
	                    $customer->setPassword($this->getRequest()->getPost('password'));
	                    $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
	                    $customerErrors = $customer->validate();
	                    if (is_array($customerErrors)) {
	                        $errors = array_merge($customerErrors, $errors);
	                    }
	                }
	
	                $validationResult = count($errors) == 0;
	             	// them code vao-----------------------------------------------------
	             	$session->unsetData('payment_gateway');
	             	$session->unsetData('payment_email');
	             	$session->unsetData('auto_withdrawn');
	             	$session->unsetData('withdrawn_level');
	             	$session->unsetData('reserve_level');
	             	$session->unsetData('bank_name');
                        $session->unsetData('name_account');
                        $session->unsetData('bank_country');
                        $session->unsetData('swift_bic');
                        $session->unsetData('referral_site');
                        
	             	$getway_withdrawn = $this->getRequest()->getPost('getway_withdrawn');
	             	$auto_withdrawn = (int)$this->getRequest()->getPost('auto_withdrawn');
	             	$reserve_level = $this->getRequest()->getPost('reserve_level');
	             	$bank_name = $this->getRequest()->getPost('bank_name');
	             	$name_account = $this->getRequest()->getPost('name_account');
	             	$bank_country = $this->getRequest()->getPost('bank_country');
	             	$swift_bic = $this->getRequest()->getPost('swift_bic');
	             	$referral_site = $this->getRequest()->getPost('referral_site');
	             	$affiliate_network = $this->getRequest()->getPost('check_affiliate_network');
	             	
	             	$store_id = Mage::app()->getStore()->getId();
	             	$max = (double)Mage::helper('affiliate/data')->getWithdrawMaxStore($store_id);
                        $min = (double)Mage::helper('affiliate/data')->getWithdrawMinStore($store_id);
                        $paypalemail = $this->getRequest()->getPost('paypal_email');
                        $payment_release_level = (double)$this->getRequest()->getPost('payment_release_level');
                        $referral_code = $this->getRequest()->getPost('referral_code');
                        $referral_sponsor = $this->getRequest()->getPost('referral_sponsor');
                        // set session
                        $session->setPaymentGateway($getway_withdrawn);
                        $session->setPaymentEmail($paypalemail);
                        $session->setAutoWithdrawn($auto_withdrawn);
                        $session->setBankName($bank_name);
                        $session->setNameAccount($name_account);
                        $session->setBankCountry($bank_country);
                        $session->setSwiftBic($swift_bic);
                        
                        if($referral_site) $session->setReferralSite($referral_site);
                        if($payment_release_level) $session->setWithdrawnLevel($payment_release_level);
                        if($reserve_level) $session->setReserveLevel($reserve_level);
                        
                        if(isset($referral_code) && $referral_code != ''){
                            $check = Mage::helper('affiliate') ->checkReferralCode($referral_code);
                            if($check == 0){
                                    $session->addError($this->__('The referral code is invalid.'));
                            throw new Exception($this->__('The referral code is invalid.')); 
                            }
                        }
                                        
                        if(isset($referral_sponsor) && $referral_sponsor != ''){
                            $check = Mage::helper('affiliate') ->checkReferralSponsor($referral_sponsor);
                            if($check == 0){
                                    $session->addError($this->__('The `referral sponsor is invalid.'));
                            throw new Exception($this->__('The referral sponsor is invalid.')); 
                            }
                        }
                        
                        if($getway_withdrawn != 'banktransfer' && $getway_withdrawn != 'check'){
                            $collectionFilter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                                    ->addFieldToFilter('payment_email',$paypalemail);
                            //var_dump(sizeof($collectionFilter));exit;
                            if(sizeof($collectionFilter) > 0 )
                            {
                                $session->addError($this->__('There is already an account with this emails paypal'));
                                throw new Exception($this->__('There is already an account with this emails paypal')); 
                            }
                        }
                        
                        if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::AUTO)
                        {
                            if($payment_release_level < $min  || $payment_release_level > $max)
                            {
                                $session->addError($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max));
                                throw new Exception($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max)); 
                            }
                        }
					
	                if (true === $validationResult) {
	                    $customer->save();
	                    
	                    if(!$Enterprise_pro && version_compare(Mage::getVersion(),'1.6.0.0','>='))
	                    {
	                    	Mage::dispatchEvent('customer_register_success', array('account_controller' => $this, 'customer' => $customer));
	                    } 
	                    
		             	$session->unsetData('payment_gateway');
		             	$session->unsetData('payment_email');
		             	$session->unsetData('auto_withdrawn');
		             	$session->unsetData('withdrawn_level');
		             	$session->unsetData('reserve_level');
		             	$session->unsetData('bank_name');
			            $session->unsetData('name_account');
			            $session->unsetData('bank_country');
			            $session->unsetData('swift_bic');
			            $session->unsetData('referral_site');
	                    Mage::dispatchEvent('mw_affiliate_account',array('customer_affiliate'=>$customer,'affiliate_account'=>$this->getRequest()->getPost(),'check_affiliate'=>true));
	                    if ($customer->isConfirmationRequired()) {
	                    	
	                    	if(!$Enterprise_pro && version_compare(Mage::getVersion(),'1.6.0.0','>='))
		                    {
		                    	$customer->sendNewAccountEmail('confirmation',$session->getBeforeAuthUrl(),Mage::app()->getStore()->getId());
		                    }else{
		                    	$customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl());
		                    };
	                        
	                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
	                        //$this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
	                        $this->_redirectSuccess(Mage::getUrl('customer/account/index', array('_secure'=>true)));
	                        return;
	                    } else {
	                        $session->setCustomerAsLoggedIn($customer);
	                        $url = $this->_welcomeCustomer($customer);
	                        $this->_redirectSuccess($url);
	                        return;
	                    }
	                } else {
	                    $session->setCustomerFormData($this->getRequest()->getPost());
	                    if (is_array($errors)) {
	                        foreach ($errors as $errorMessage) {
	                            $session->addError($errorMessage);
	                        }
	                    } else {
	                        $session->addError($this->__('Invalid customer data'));
	                    }
	                }
	            } catch (Mage_Core_Exception $e) {
	                $session->setCustomerFormData($this->getRequest()->getPost());
	                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
	                    $url = Mage::getUrl('customer/account/forgotpassword');
	                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
	                    $session->setEscapeMessages(false);
	                } else {
	                    $message = $e->getMessage();
	                }
	                $session->addError($message);
	            } catch (Exception $e) {
	                $session->setCustomerFormData($this->getRequest()->getPost())
	                    ->addException($e, $this->__('Cannot save the customer.'));
	            }
	        }
	
	        //$this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
	        $this->_redirectError(Mage::getUrl('*/*/createaffiliate', array('_secure' => true)));
        	
        }
        else if(!$Enterprise_pro && version_compare(Mage::getVersion(),'1.4.2.0','<'))
        {   
        	$session->setEscapeMessages(true); // prevent XSS injection in user input
	        if ($this->getRequest()->isPost()) {
	            $errors = array();
	            if (!$customer = Mage::registry('current_customer')) {
	                $customer = Mage::getModel('customer/customer')->setId(null);
	            }
	
	            $data = $this->_filterPostData($this->getRequest()->getPost());
	
	            foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
	                if ($node->is('create') && isset($data[$code])) {
	                    if ($code == 'email') {
	                        $data[$code] = trim($data[$code]);
	                    }
	                    $customer->setData($code, $data[$code]);
	                }
	            }
	
	            if ($this->getRequest()->getParam('is_subscribed', false)) {
	                $customer->setIsSubscribed(1);
	            }
	
	            /**
	             * Initialize customer group id
	             */
	            $customer->getGroupId();
	
	            if ($this->getRequest()->getPost('create_address')) {
	                $address = Mage::getModel('customer/address')
	                    ->setData($this->getRequest()->getPost())
	                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
	                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false))
	                    ->setId(null);
	                $customer->addAddress($address);
	
	                $errors = $address->validate();
	                if (!is_array($errors)) {
	                    $errors = array();
	                }
	            }
	
	            try {
	                $validationCustomer = $customer->validate();
	                if (is_array($validationCustomer)) {
	                    $errors = array_merge($validationCustomer, $errors);
	                }
	                $validationResult = count($errors) == 0;
	                // them code vao-----------------------------------------------------
	             	$session->unsetData('payment_gateway');
	             	$session->unsetData('payment_email');
	             	$session->unsetData('auto_withdrawn');
	             	$session->unsetData('withdrawn_level');
	             	$session->unsetData('reserve_level');
	             	$session->unsetData('bank_name');
		            $session->unsetData('name_account');
		            $session->unsetData('bank_country');
		            $session->unsetData('swift_bic');
		            $session->unsetData('referral_site');
	                $getway_withdrawn = $this->getRequest()->getPost('getway_withdrawn');
	             	$auto_withdrawn = (int)$this->getRequest()->getPost('auto_withdrawn');
	             	$reserve_level = $this->getRequest()->getPost('reserve_level');
	             	$bank_name = $this->getRequest()->getPost('bank_name');
	             	$name_account = $this->getRequest()->getPost('name_account');
	             	$bank_country = $this->getRequest()->getPost('bank_country');
	             	$swift_bic = $this->getRequest()->getPost('swift_bic');
	             	$referral_site = $this->getRequest()->getPost('referral_site');
	             	
	             	$store_id = Mage::app()->getStore()->getId();
	            	$max = (double)Mage::helper('affiliate/data')->getWithdrawMaxStore($store_id);
	  				$min = (double)Mage::helper('affiliate/data')->getWithdrawMinStore($store_id);
					$paypalemail = $this->getRequest()->getPost('paypal_email');
					$payment_release_level = (double)$this->getRequest()->getPost('payment_release_level');
					$referral_code = $this->getRequest()->getPost('referral_code');  
					$referral_sponsor = $this->getRequest()->getPost('referral_sponsor');  
					
					// set session
					$session->setPaymentGateway($getway_withdrawn);
					$session->setPaymentEmail($paypalemail);
					$session->setAutoWithdrawn($auto_withdrawn);
					$session->setBankName($bank_name);
					$session->setNameAccount($name_account);
					$session->setBankCountry($bank_country);
					$session->setSwiftBic($swift_bic);
					if($referral_site) $session->setReferralSite($referral_site);
					if($payment_release_level) $session->setWithdrawnLevel($payment_release_level);
					if($reserve_level) $session->setReserveLevel($reserve_level);
					//var_dump($paypalemail);exit;
	            	if(isset($referral_code) && $referral_code != ''){
						$check = Mage::helper('affiliate') ->checkReferralCode($referral_code);
    	 				if($check == 0){
    	 					$session->addError($this->__('The referral code is invalid.'));
		            		throw new Exception($this->__('The referral code is invalid.')); 
    	 				}
					}
                                        
	            	if(isset($referral_sponsor) && $referral_sponsor != ''){
						$check = Mage::helper('affiliate') ->checkReferralSponsor($referral_sponsor); echo $check;
    	 				if($check == 0){
    	 					$session->addError($this->__('The referral sponsor is invalid.'));
		            		throw new Exception($this->__('The referral sponsor is invalid.')); 
    	 				}
					}
					if($getway_withdrawn != 'banktransfer' && $getway_withdrawn != 'check'){
		        		$collectionFilter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
			                    			->addFieldToFilter('payment_email',$paypalemail);
			            //var_dump(sizeof($collectionFilter));exit;
			            if(sizeof($collectionFilter) > 0 )
			            {
			            	$session->addError($this->__('There is already an account with this emails paypal'));
			            	throw new Exception($this->__('There is already an account with this emails paypal')); 
			            }
					}
		            if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::AUTO)
		            {
		            	if($payment_release_level < $min  || $payment_release_level > $max)
		            	{
		            		$session->addError($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max));
		            		throw new Exception($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max)); 
		            	}
		            }
			            
	                if (true === $validationResult) {
	                    $customer->save();
		             	$session->unsetData('payment_gateway');
		             	$session->unsetData('payment_email');
		             	$session->unsetData('auto_withdrawn');
		             	$session->unsetData('withdrawn_level');
		             	$session->unsetData('reserve_level');
		             	$session->unsetData('bank_name');
			            $session->unsetData('name_account');
			            $session->unsetData('bank_country');
			            $session->unsetData('swift_bic');
			            $session->unsetData('referral_site');
	                    Mage::dispatchEvent('mw_affiliate_account',array('customer_affiliate'=>$customer,'affiliate_account'=>$this->getRequest()->getPost(),'check_affiliate'=>true));
	
	                    if ($customer->isConfirmationRequired()) {
	                        $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl());
	                        $session->addSuccess($this->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
	                        //$this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
	                        $this->_redirectSuccess(Mage::getUrl('customer/account/index', array('_secure'=>true)));
	                        return;
	                    }
	                    else {
	                        $session->setCustomerAsLoggedIn($customer);
	                        $url = $this->_welcomeCustomer($customer);
	                        $this->_redirectSuccess($url);
	                        return;
	                    }
	                } else {
	                    $session->setCustomerFormData($this->getRequest()->getPost());
	                    if (is_array($errors)) {
	                        foreach ($errors as $errorMessage) {
	                            $session->addError($errorMessage);
	                        }
	                    }
	                    else {
	                        $session->addError($this->__('Invalid customer data'));
	                    }
	                }
	            }
	            catch (Mage_Core_Exception $e) {
	                $session->setCustomerFormData($this->getRequest()->getPost());
	                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
	                    $url = Mage::getUrl('customer/account/forgotpassword');
	                    $message = $this->__('There is already an account with this emails address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
	                    $session->setEscapeMessages(false);
	                }
	                else {
	                    $message = $e->getMessage();
	                }
	                $session->addError($message);
	            }
	            catch (Exception $e) {
	                $session->setCustomerFormData($this->getRequest()->getPost())
	                    ->addException($e, $this->__('Can\'t save customer'));
	            }
	        }
	        //$this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
	        $this->_redirectError(Mage::getUrl('*/*/createaffiliate', array('_secure' => true)));
        	
        }
      
    }
    public function createAffiliateAction()
    {
    	$store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
     	$session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('affiliate/index/createaccount');
            return;
        }
		$this->loadLayout(); 
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('core/session');    
		$this->renderLayout();
    }
	protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess($this->__('Thank you for registering with %s', Mage::app()->getStore()->getFrontendName()));

        $customer->sendNewAccountEmail($isJustConfirmed ? 'confirmed' : 'registered');

        //$successUrl = Mage::getUrl('*/*/index', array('_secure'=>true));
        $successUrl = Mage::getUrl('customer/account/index', array('_secure'=>true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }
    
    

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    
}

