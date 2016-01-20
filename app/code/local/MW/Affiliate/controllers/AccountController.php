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
class MW_Affiliate_AccountController extends Mage_Customer_AccountController
{    
    public function createPostAction()
    {
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            //$this->_redirect('*/*/');
            $this->_redirect('customer/account/');
            return;
        }
        if(version_compare(Mage::getVersion(),'1.4.2.0','>='))
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
	             	$session->unsetData('check_affiliate');
	             	$session->unsetData('payment_gateway');
	             	$session->unsetData('payment_email');
	             	$session->unsetData('auto_withdrawn');
	             	$session->unsetData('withdrawn_level');
	             	$session->unsetData('reserve_level');
	             	$getway_withdrawn = $this->getRequest()->getPost('getway_withdrawn');
	             	$auto_withdrawn = (int)$this->getRequest()->getPost('auto_withdrawn');
	             	$reserve_level = $this->getRequest()->getPost('reserve_level');
	             	$store_id = Mage::app()->getStore()->getId();
	             	$max = (double)Mage::helper('affiliate/data')->getWithdrawMaxStore($store_id);
	  				$min = (double)Mage::helper('affiliate/data')->getWithdrawMinStore($store_id);
					$paypalemail = $this->getRequest()->getPost('email');
					$payment_release_level = (double)$this->getRequest()->getPost('payment_release_level');
					$check_affiliate = $this->getRequest()->getPost('check_affiliate');
					if($check_affiliate)
					{   
						// set session
						$session->setCheckAffiliate($check_affiliate);
						$session->setPaymentGateway($getway_withdrawn);
						$session->setPaymentEmail($paypalemail);
						$session->setAutoWithdrawn($auto_withdrawn);
						if($payment_release_level) $session->setWithdrawnLevel($payment_release_level);
						if($reserve_level) $session->setReserveLevel($reserve_level);
						//var_dump($paypalemail);exit;
		        		$collectionFilter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
			                    			->addFieldToFilter('payment_email',$paypalemail);
			            //var_dump(sizeof($collectionFilter));exit;
			            if(sizeof($collectionFilter) > 0 )
			            {
			            	$session->addError($this->__('There is already an account with this emails paypal'));
			            	throw new Exception($this->__('There is already an account with this emails paypal')); 
			            }
			            if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::AUTO)
			            {
			            	if($payment_release_level < $min  || $payment_release_level > $max)
			            	{
			            		$session->addError($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max));
			            		throw new Exception($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max)); 
			            	}
			            }
					}
	
	                if (true === $validationResult) {
	                    $customer->save();
	                    $session->unsetData('check_affiliate');
		             	$session->unsetData('payment_gateway');
		             	$session->unsetData('payment_email');
		             	$session->unsetData('auto_withdrawn');
		             	$session->unsetData('withdrawn_level');
		             	$session->unsetData('reserve_level');
	                    Mage::dispatchEvent('mw_affiliate_account',array('customer_affiliate'=>$customer,'affiliate_account'=>$this->getRequest()->getPost(),'check_affiliate'=>$check_affiliate));
	                    if ($customer->isConfirmationRequired()) {
	                        $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl());
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
	        $this->_redirectError(Mage::getUrl('customer/account/create', array('_secure' => true)));
        	
        }
        else
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
	                $session->unsetData('check_affiliate');
	             	$session->unsetData('payment_gateway');
	             	$session->unsetData('payment_email');
	             	$session->unsetData('auto_withdrawn');
	             	$session->unsetData('withdrawn_level');
	             	$session->unsetData('reserve_level');
	                $getway_withdrawn = $this->getRequest()->getPost('getway_withdrawn');
	             	$auto_withdrawn = (int)$this->getRequest()->getPost('auto_withdrawn');
	             	$reserve_level = $this->getRequest()->getPost('reserve_level');
	             	$store_id = Mage::app()->getStore()->getId();
	            	$max = (double)Mage::helper('affiliate/data')->getWithdrawMaxStore($store_id);
	  				$min = (double)Mage::helper('affiliate/data')->getWithdrawMinStore($store_id);
					$paypalemail = $this->getRequest()->getPost('paypal_email');
					$payment_release_level = (double)$this->getRequest()->getPost('payment_release_level');
					$check_affiliate = $this->getRequest()->getPost('check_affiliate');
					if($check_affiliate)
					{   
						// set session
						$session->setCheckAffiliate($check_affiliate);
						$session->setPaymentGateway($getway_withdrawn);
						$session->setPaymentEmail($paypalemail);
						$session->setAutoWithdrawn($auto_withdrawn);
						if($payment_release_level) $session->setWithdrawnLevel($payment_release_level);
						if($reserve_level) $session->setReserveLevel($reserve_level);
						//var_dump($paypalemail);exit;
		        		$collectionFilter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
			                    			->addFieldToFilter('payment_email',$paypalemail);
			            //var_dump(sizeof($collectionFilter));exit;
			            if(sizeof($collectionFilter) > 0 )
			            {
			            	$session->addError($this->__('There is already an account with this emails paypal'));
			            	throw new Exception($this->__('There is already an account with this emails paypal')); 
			            }
			            if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::AUTO)
			            {
			            	if($payment_release_level < $min  || $payment_release_level > $max)
			            	{
			            		$session->addError($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max));
			            		throw new Exception($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max)); 
			            	}
			            }
					}
	                if (true === $validationResult) {
	                    $customer->save();
	                    $session->unsetData('check_affiliate');
		             	$session->unsetData('payment_gateway');
		             	$session->unsetData('payment_email');
		             	$session->unsetData('auto_withdrawn');
		             	$session->unsetData('withdrawn_level');
		             	$session->unsetData('reserve_level');
	                    Mage::dispatchEvent('mw_affiliate_account',array('customer_affiliate'=>$customer,'affiliate_account'=>$this->getRequest()->getPost(),'check_affiliate'=>$check_affiliate));
	
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
	        $this->_redirectError(Mage::getUrl('customer/account/create', array('_secure' => true)));
        	
        }
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
    
    
    public function editurlAction()
    {
        $session = $this->_getSession();
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            
            $url = $this->getRequest()->getPost('url');
            
            $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('customer_id',$customerData->getId())
                ->addFieldToFilter('status ',MW_Affiliate_Model_Statusreferral::ENABLED)
                ->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE)
                ->addFieldToFilter('customer_url', $url);
        
            $viewUrl = Mage::getUrl('affiliate/index/referralaccount');
            
            if (!(sizeof($collection))){
                $colreview = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                    ->addFieldToFilter('customer_url', $url);
                
                if (sizeof($colreview)){
                    $viewUrl = Mage::getUrl('affiliate/index/url');
                    Mage::getSingleton('core/session')->addError($this->__('The URL is already taken'));                         
                }
                else{
                    $pattern = '/^[a-zA-Z\d\.\-\_]+$/';   
                    preg_match($pattern, $url, $matches, PREG_OFFSET_CAPTURE);
                    
                    if($matches){
                        Mage::getSingleton('affiliate/affiliatecustomers')->load($customerData->getId())
                        ->setCustomerUrl($url)
                        ->setCustomerTime(now())
                        ->save(); 
                    }
                    else{
                         $viewUrl = Mage::getUrl('affiliate/index/url');
                        Mage::getSingleton('core/session')->addError($this->__('URL must contains: no spaces, letters, numbers and special characters as dot, dash and underscore'));                         
                
                    }                                        
                }                
            }
            
            Mage::app()->getResponse()->setRedirect($viewUrl);
        }
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

