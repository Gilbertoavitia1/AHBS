<?php
class MW_Credit_Model_Transactiontype extends Varien_Object
{
    const ADMIN_CHANGE 				= 1;
    const REFUND_PRODUCT 			= 2;
    const CANCEL_WITHDRAWN			= 3;	
	const USE_TO_CHECKOUT			= 5;
	const CANCEL_USE_TO_CHECKOUT	= 6;
	const BUY_PRODUCT				= 7;
	const WITHDRAWN			    	= 8;
	const REFUND_PRODUCT_AFFILIATE  = 9;
	const REFERRAL_VISITOR			= 10;
	const REFERRAL_SIGNUP			= 11;
	const REFERRAL_SUBSCRIBE		= 12;
		
    static public function getOptionArray()
    {
        return array(
			self::ADMIN_CHANGE   			=> Mage::helper('credit')->__('Updated by Admin'),	
    		self::USE_TO_CHECKOUT			=> Mage::helper('credit')->__('Use Credit to Checkout'),
    		self::CANCEL_USE_TO_CHECKOUT	=> Mage::helper('credit')->__('Canceled Order using Credit'),
    		self::REFUND_PRODUCT    		=> Mage::helper('credit')->__('Refund Product'),//khi tra lai don hang,��n h�ng �� d�ng credit checkout
    		self::WITHDRAWN				    => Mage::helper('credit')->__('Withdraw'),
    		self::CANCEL_WITHDRAWN		    => Mage::helper('credit')->__('Canceled Withdraw'),
    		self::BUY_PRODUCT				=> Mage::helper('credit')->__('Referral Purchase'),
    		self::REFUND_PRODUCT_AFFILIATE  => Mage::helper('credit')->__('Referred Customer Receives Refund'),
        	self::REFERRAL_VISITOR			=> Mage::helper('credit')->__('Referral Visitor'),
        	self::REFERRAL_SIGNUP			=> Mage::helper('credit')->__('Referral Sign-up'),
        	self::REFERRAL_SUBSCRIBE		=> Mage::helper('credit')->__('Referral Subscribe')	
        );
    }
	static public function getAffiliateTypeArray()
    {
        return array(
    		self::BUY_PRODUCT				=> Mage::helper('credit')->__('Referral Purchase'),
        	self::REFERRAL_VISITOR			=> Mage::helper('credit')->__('Referral Visitor'),
        	self::REFERRAL_SIGNUP			=> Mage::helper('credit')->__('Referral Sign-up'),
        	self::REFERRAL_SUBSCRIBE		=> Mage::helper('credit')->__('Referral Subscribe')	
        );
    }
    
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
    
	static public function getTransactionDetail($type, $detail, $is_admin=false)
    {
    	$result = "";
    	if($is_admin) {
    		$url = "adminhtml/sales_order/view";
    	} else {
    		$url = "sales/order/view";
    	}
    	
    	switch($type)
    	{
    		case self::ADMIN_CHANGE:
    			$result = Mage::helper('credit')->__($detail);
    			break;
    		case self::REFUND_PRODUCT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$result = Mage::helper('credit')->__("You refurn to order : <b><a href=%s>#%s</a></b>, checkout by credit",
    											  	  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			break;
    		case self::USE_TO_CHECKOUT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$result = Mage::helper('credit')->__("You used credit to checkout order : <b><a href=%s>#%s</a></b>",
    												  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			break;
    		case self::CANCEL_USE_TO_CHECKOUT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$result = Mage::helper('credit')->__("You cancelled order : <b><a href=%s>#%s</a></b>, checkout by credit",
    												  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			break;
    		
    		case self::BUY_PRODUCT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			if ($is_admin)
    				$result = Mage::helper('credit')->__("Commission from order : <b><a href=%s>#%s</a></b>", 
    												  Mage::getUrl($url,array('order_id' => $order->getId())),$detail
    												);
    			else
    				$result = Mage::helper('credit')->__("Commission from order: <b>#%s</b>", $detail);
    			break;
    		case self::REFUND_PRODUCT_AFFILIATE:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			if ($is_admin)
    				$result = Mage::helper('credit')->__("Customer granted refund. Order <b><a href=%s>#%s</a></b>. Affiliate commission reversed.",
    												  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			else
    				$result = Mage::helper('credit')->__("Customer granted refund. Order <b>#%s</b>. Affiliate commission reversed.",$detail);
    			break;
    		case self::WITHDRAWN:
    			$withdrawn = Mage::getModel("affiliate/affiliatewithdrawn")->load($detail);
    			$result = Mage::helper('credit')->__("Commission Withdrawal: <b>%s</b> (Processing Fee: <b>%s</b>)",Mage::helper('core')->currency($withdrawn->getWithdrawnAmount()),Mage::helper('core')->currency($withdrawn->getFee()));
    			break;
    		case self::CANCEL_WITHDRAWN:
    			$withdrawn = Mage::getModel("affiliate/affiliatewithdrawn")->load($detail);
    			$result = Mage::helper('credit')->__("Canceled Commission Withdrawal: <b>%s</b> (Processing Fee: <b>%s</b>)",Mage::helper('core')->currency($withdrawn->getWithdrawnAmount()),Mage::helper('core')->currency($withdrawn->getFee()));
    			break;
    		case self::REFERRAL_VISITOR:
    			//$invitation = Mage::getModel('affiliate/affiliateinvitation')->load($detail);
    			$visitorNo = Mage::helper('affiliate')->getReferralVisitorNumber(Mage::app()->getStore()->getId());
    			
    			$plural = ($visitorNo > 1) ? 's' : '';
    			
    			$result = Mage::helper('credit')->__('Commission of %s referral visitor' . $plural, $visitorNo);
    	   		break;
    	   	case self::REFERRAL_SIGNUP:
    	   		if($detail =='') $detail = 0;
    	   		$email = Mage::getModel('customer/customer')->load($detail)->getEmail();
    	   		$result = Mage::helper('credit')->__('Commission of referral sign-up: <b>%s</b>', $email);
    	   		break;
    	   	case self::REFERRAL_SUBSCRIBE:
    	   		if($detail =='') $detail = 0;
    	   		$email = Mage::getModel('customer/customer')->load($detail)->getEmail();
    	   		$result = Mage::helper('credit')->__('Commission of referral subscriber: <b>%s</b>', $email);
    	   		break;
    	}
    	return $result;
    }
}