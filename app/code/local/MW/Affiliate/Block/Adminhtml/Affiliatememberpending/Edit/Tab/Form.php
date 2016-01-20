<?php

class MW_Affiliate_Block_Adminhtml_Affiliatememberpending_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {   
      $form_member_detail = new Varien_Data_Form();
      $this->setForm($form_member_detail);
      $fieldset = $form_member_detail->addFieldset('affiliate_form', array('legend'=>Mage::helper('affiliate')->__('Member Pending Information')));
      
      $customer_id = $this->getRequest()->getParam('id');      
      $customer = Mage::getModel('customer/customer')->load($customer_id);
      $affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);

	  $referral_name = $customer->getName();
	  $customer_email = $customer->getEmail();
	  $website_name = Mage::app()->getWebsite($customer ->getWebsiteId())->getName();
	  $payment_gateway = $affiliate_customer->getPaymentGateway();
	  $payment_email = $affiliate_customer->getPaymentEmail();
	  $auto_withdrawn = $affiliate_customer ->getAutoWithdrawn();
	  $withdrawn_level = round($affiliate_customer ->getWithdrawnLevel(),0);
	  $reserve_level = round($affiliate_customer ->getReserveLevel(),0);
	  if($payment_gateway == 'banktransfer') $payment_email = '';
	    	
  	  $bank_name = $affiliate_customer->getBankName();
	  $name_account = $affiliate_customer->getNameAccount();
	  $bank_country = $affiliate_customer->getBankCountry();
	  $swift_bic = $affiliate_customer->getSwiftBic();
	  $account_number = $affiliate_customer->getAccountNumber();
	  $re_account_number = $affiliate_customer->getReAccountNumber();
	  $referral_site = $affiliate_customer->getReferralSite();
	   
	  $fieldset->addField('website_name', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Link Affiliate With'),
          'value'      => $website_name,
      ));
      $fieldset->addField('referral_name', 'note', array(
          'label'     => Mage::helper('affiliate')->__('Pending Affiliate Name'),
          'text'     => Mage::helper('affiliate')->getLinkCustomer($customer_id,$referral_name),
      ));
      $fieldset->addField('customer_email', 'note', array(
          'label'     => Mage::helper('affiliate')->__('Pending Affiliate Email'),
          'text'      => Mage::helper('affiliate')->getLinkCustomer($customer_id,$customer_email),
      ));
      
      $fieldset->addField('payment_gateway', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Payment Method'),
      	  //'readonly'  => 'readonly',
          'value'      => Mage::helper('affiliate')->getLabelPaymentGateway($payment_gateway),
      ));
      if($payment_gateway != 'banktransfer')
      {
      $fieldset->addField('payment_email', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Withdrawal Notification Email'),
          'name'      => 'payment_email',
      	  'class'     => 'validate-email',
      	  'value'     =>$payment_email
      ));
      }
      $fieldset->addField('auto_withdrawn', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Withdrawal Request Method '),
          'value'      => MW_Affiliate_Model_Autowithdrawn::getLabel($auto_withdrawn),
      ));

       $fieldset->addField('withdrawn_level', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Auto payment when account balance reaches'),
      	  'readonly'  => 'readonly',
      	  'value'     =>Mage::helper('core')->currency($withdrawn_level,true,false)
      ));

      $fieldset->addField('reserve_level', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Reserve Level (to be kept in account)'),
          'class'     => 'required-entry',
      	  'value'     =>Mage::helper('core')->currency($reserve_level,true,false)
      ));
      if($payment_gateway == 'banktransfer')
      {
      $fieldset->addField('bank_name', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Bank Name'),
	      'name'      => 'bank_name',
      	  'value'     => $bank_name
      ));
      $fieldset->addField('name_account', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Name on account'),
      	  'name'      => 'name_account',
	      'value'      => $name_account
      ));

	   $fieldset->addField('bank_country', 'label', array(
        'name'  => 'bank_country',
        'label'     => Mage::helper('affiliate')->__('Bank Country'),
      	'value'     => Mage::getModel('directory/country')->load($bank_country)->getName()
      ));
      $fieldset->addField('swift_bic', 'label', array(
          'label'     => Mage::helper('affiliate')->__('SWIFT code'),
	      'name'      => 'swift_bic',
      	  'value'     => $swift_bic
      ));
      $fieldset->addField('account_number', 'label', array(
          'label'     => Mage::helper('affiliate')->__('Account Number'),
	      'name'      => 'account_number',
      	  'value'     => $account_number
      ));
      }
      $fieldset->addField('referral_site', 'textarea', array(
          'label'     => Mage::helper('affiliate')->__('Affiliate\'s Website(s)'),
	      'name'      => 'referral_site',
      	  'readonly'  => true,
      	  'value'     => $referral_site
      ));
   
     
      return parent::_prepareForm();
  }

}