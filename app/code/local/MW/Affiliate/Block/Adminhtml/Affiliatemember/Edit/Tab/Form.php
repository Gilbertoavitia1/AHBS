<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {   
    	$form_member_detail = new Varien_Data_Form();
      	$this->setForm($form_member_detail);
      	$fieldset = $form_member_detail->addFieldset('affiliate_form', array('legend' => Mage::helper('affiliate')->__('Member Information')));
      
      	$customer_id = $this->getRequest()->getParam('id');      
      	$customer = Mage::getModel('customer/customer')->load($customer_id);
      	
     	$affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
      	$credit_customer 	= Mage::getModel('credit/creditcustomer')->load($customer_id);
        $group_members 		= Mage::getModel('affiliate/affiliategroupmember')->getCollection()
                
                ->addFieldToFilter('customer_id',$customer_id);
        $group_id = 0;
        if(sizeof($group_members)>0){
            foreach ($group_members as $group_member) {
                $group_id = $group_member->getGroupId();
            }
        }
	  	
        $referral_name 		= $customer->getName();
        $customer_email 	= $customer->getEmail();
        $website_name 		= Mage::app()->getWebsite($customer ->getWebsiteId())->getName();
        $affiliate_status 	= $affiliate_customer->getStatus();
        $affiliate_parent 	= Mage::getModel('customer/customer')->load($affiliate_customer->getCustomerInvited())->getEmail();
        $referral_code 		= $affiliate_customer->getReferralCode();

        $affiliate_type		= $affiliate_customer->getAnetwork();
        $referral_sponsor	= $affiliate_customer->getReferralSponsor();
        $customer_photo 	= $affiliate_customer->getCustomerPhoto();
        $customer_url   	= $affiliate_customer->getCustomerUrl();
        $payment_gateway 	= $affiliate_customer->getPaymentGateway();
        $payment_email 		= ($payment_gateway == 'banktransfer') ? '' : $affiliate_customer->getPaymentEmail();

        $bank_name 		= $affiliate_customer->getBankName();
        $name_account 		= $affiliate_customer->getNameAccount();
        $bank_country 		= $affiliate_customer->getBankCountry();
        $swift_bic	 	= $affiliate_customer->getSwiftBic();
        $account_number 	= $affiliate_customer->getAccountNumber();
        $re_account_number 	= $affiliate_customer->getReAccountNumber();
        $referral_site 		= $affiliate_customer->getReferralSite();

        $auto_withdrawn 	= $affiliate_customer ->getAutoWithdrawn();
        $withdrawn_level 	= round($affiliate_customer ->getWithdrawnLevel(),0);
        $reserve_level 		= round($affiliate_customer ->getReserveLevel(),0);
        $balance 		= $credit_customer->getCredit();

        $total_commission 	= $affiliate_customer->getTotalCommission();
        $total_paid 		= $affiliate_customer->getTotalPaid();

        /* Create fieldsets */
        $fieldset->addField('customer_id', 'hidden', array(
            'value'     => $customer_id	
        ));
	  	
      	$fieldset->addField('referral_name', 'note', array(
            'label'     => Mage::helper('affiliate')->__('Affiliate Name'),
            'text'     	=> Mage::helper('affiliate')->getLinkCustomer($customer_id,$referral_name),
      	));
      	
      	$fieldset->addField('customer_email', 'note', array(
            'label'     => Mage::helper('affiliate')->__('Affiliate Account'),
            'text'      => Mage::helper('affiliate')->getLinkCustomer($customer_id,$customer_email),
      	));
        
        $fieldset->addField('anetwork', 'select', array(
            'label'     => Mage::helper('affiliate')->__('Affiliate Network'),
            'name'      => 'anetwork',
            'required'  => true,
            'values'    => $this->_getAffiliateNetworkArray(),
            'value'     => $affiliate_type
      	));
      
      	$fieldset->addField('group_id', 'select', array(
            'label'     => Mage::helper('affiliate')->__('Group Name'),
            'name'      => 'group_id',
            'required'  => true,
            'values'    => $this->_getGroupArray(),
            'value'     => $group_id
      	));
      	
      	$fieldset->addField('referral_code', 'text', array(
            'label'         => Mage::helper('affiliate')->__('Referral Code (manual)'),
            'name'		=> 'referral_code',
            'required'	=> true,	
            'class'		=> 'validate-referral-code',	
            'value'         => $referral_code
      	));
      	
      	$fieldset->addField('referral_sponsor', 'text', array(
            'label'     => Mage::helper('affiliate')->__('Referral Sponsor (manual)'),
            'name'      => 'referral_sponsor',
            'required'	=> true,	
            'class'     => 'validate-referral-sponsor',	
            'value'     => $referral_sponsor
      	));
        
      	$fieldset->addField('customer_photo', 'text', array(
            'label'     => Mage::helper('affiliate')->__('Customer Photo (manual)'),
            'name'      => 'customer_photo',
            'class'     => '',	
            'value'     => $customer_photo
      	));
      	$fieldset->addField('normal_photo', 'hidden', array(
            'name'      => 'normal_photo',
            'value'     => $this->getSkinUrl('images/avatar.png')
      	));
        
      	$fieldset->addField('customer_url', 'text', array(
            'label'     => Mage::helper('affiliate')->__('Customer URL (manual)'),
            'name'	=> 'customer_url',
            'class'	=> 'validate-customer-unique-url',	
            'value'     => $customer_url
      	));
      	
      	$fieldset->addField('check_referral_code', 'hidden', array(
      			'value'		=> '0'
      	));
      	$fieldset->addField('check_referral_sponsor', 'hidden', array(
      			'value'		=> '0'
      	));
        
      	$fieldset->addField('check_unique_customer_url', 'hidden', array(
      			'value'		=> '0'
      	));
      	 
      	
      	$fieldset->addField('affiliate_status', 'select', array(
        	'label'     => Mage::helper('affiliate')->__('Status'),
          	'name'      => 'affiliate_status',
          	'values'    => MW_Affiliate_Model_Statusreferral::getOptionArray(),
          	'value'     => $affiliate_status
      	));
      	
      	$fieldset->addField('affiliate_parent', 'text', array(
        	'label'     => Mage::helper('affiliate')->__('Affiliate Parent'),
          	'class'     => 'validate-email',
          	'name'      => 'affiliate_parent',
      	  	'value'     => $affiliate_parent
      	));
      	
      	$fieldset->addField('payment_gateway', 'select', array(
        	'label'     => Mage::helper('affiliate')->__('Payment Method'),
          	'name'      => 'payment_gateway',
          	'values'    => $this->_getPaymentGatewayArray(),
          	'value'     => $payment_gateway
      	));
      	
      	$fieldset->addField('payment_email', 'text', array(
        	'label'     => Mage::helper('affiliate')->__('Withdrawal Notice Email'),
          	'name'      => 'payment_email',
      	  	'value'     => $payment_email
      	));
      	
      	$fieldset->addField('auto_withdrawn', 'select', array(
        	'label'     => Mage::helper('affiliate')->__('Withdrawal Method'),
          	'name'      => 'auto_withdrawn',
          	'values'    => MW_Affiliate_Model_Autowithdrawn::getOptionArray(),
          	'value'     => $auto_withdrawn
      	));
      	
      	$fieldset->addField('withdrawn_level', 'text', array(
       		'label'     => Mage::helper('affiliate')->__('Auto payment when account balance reaches'),
          	'name'     	=> 'withdrawn_level',
		  	'class'    	=> 'required-entry validate-digits',
      	  	'value'     => $withdrawn_level,
      		'note' 		=> Mage::helper('affiliate')->__('Note: Over reserve level')	
      	));
      	
	   	$fieldset->addField('reserve_level', 'text', array(
        	'label'     => Mage::helper('affiliate')->__('Reserve Level (to be kept in account)'),
	      	'name'      => 'reserve_level',
	   	  	'class'     => 'validate-digits',
      	  	'value'     => $reserve_level
      	));
	   	
      	$fieldset->addField('bank_name', 'text', array(
        	'label'     => Mage::helper('affiliate')->__('Bank Name'),
	      	'name'      => 'bank_name',
	   	  	'required'  => true,
      	  	'value'     => $bank_name
      	));
      	
      	$fieldset->addField('name_account', 'text', array(
        	'label'     => Mage::helper('affiliate')->__('Name on account'),
	      	'name'      => 'name_account',
	   	  	'required'  => true,
      	  	'value'     => $name_account
      	));
      	
      	$fieldset->addField('bank_country', 'select', array(
        	'name'  => 'bank_country',
        	'required'  => true,
        	'label'     => Mage::helper('affiliate')->__('Bank Country'),
        	'values'    => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
      		'value'     => $bank_country
      	));
      	
      	$fieldset->addField('swift_bic', 'text', array(
        	'label'     => Mage::helper('affiliate')->__('SWIFT code'),
	      	'name'      => 'swift_bic',
	   	  	'required'  => true,
      	  	'value'     => $swift_bic
      	));
      	
      	$fieldset->addField('account_number', 'text', array(
        	'label'     => Mage::helper('affiliate')->__('Account Number'),
	      	'name'      => 'account_number',
	   	  	'required'  => true,
      	  	'value'     => $account_number
      	));
      	
      	$fieldset->addField('re_account_number', 'text', array(
        	'label'     => Mage::helper('affiliate')->__('Re Account Number'),
	      	'name'      => 're_account_number',
          	'class'     => 'validate-re_account_number',
	   	  	'required'  => true,
      	  	'value'     => $re_account_number
      	));
      	
      	/*
      	$fieldset->addField('referral_site', 'textarea', array(
        	'label'     => Mage::helper('affiliate')->__('Affiliate Website(s)'),
	      	'name'      => 'referral_site',
      	  	'value'     => $referral_site
      	));
      	*/
      
      	$fieldset->addField('total_commission', 'label', array(
        	'label'     => Mage::helper('affiliate')->__('Total Credit/Adjustments'),
          	'name'      => 'total_commission',
     	  	'readonly'  => 'readonly',
      	  	'value'     => Mage::helper('core')->currency($total_commission,true,false)
      	));
      	
      	$fieldset->addField('total_paid', 'label', array(
        	'label'     => Mage::helper('affiliate')->__('Total Paid Out/Credits Used'),
          	'name'      => 'total_paid',
     	  	'readonly'  => 'readonly',
      	  	'value'     => Mage::helper('core')->currency($total_paid,true,false)
      	));
      
      	$fieldset->addField('balance', 'label', array(
        	'label'     => Mage::helper('affiliate')->__('Current Balance'),
          	'name'      => 'balance',
      	  	'readonly'  => 'readonly',
      	  	'value'     => Mage::helper('core')->currency($balance,true,false)
	    ));
      
      	return parent::_prepareForm();
	}
      
	private function _getPaymentGatewayArray()
  	{
    	$arr = array();
  		$gateways = unserialize(Mage::helper('affiliate/data')->getGatewayStore());
		foreach ($gateways as $gateway) 
		{
			$arr[$gateway['gateway_value']] = $gateway['gateway_title'];
		}
		return $arr;
    }
    
    private function _getGroupArray()
    {
    	$arr = array();
    	$arr[''] = Mage::helper('affiliate')->__('Please select a group');
    	$groups = Mage::getModel('affiliate/affiliategroup')->getCollection();
		foreach ($groups as $group) 
		{   
			$group_id = $group->getGroupId();
			$group_name = $group ->getGroupName();
			$arr[$group_id] =  $group_name;
		}
		return $arr;
    }
    
    private function _getAffiliateNetworkArray()
    {
    	$arr = array();
        $arr[1] =  Mage::helper('affiliate')->__('Affiliate Uninivel');
        $arr[2] =  Mage::helper('affiliate')->__('Affiliate Binary');
        $arr[3] =  Mage::helper('affiliate')->__('Affiliate Both Networks');
        
        return $arr;
    }
    
}
