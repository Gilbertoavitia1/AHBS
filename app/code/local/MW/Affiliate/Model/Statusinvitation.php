<?php
class MW_Affiliate_Model_Statusinvitation extends Varien_Object
{
	const INVITATION				= 1;		
    const CLICKLINK					= 2;
    const REGISTER			    	= 3;
    const PURCHASE			    	= 4;
    const SUBSCRIBE					= 5;

    static public function getOptionArray()
    {
        return array(
            self::INVITATION    				=> Mage::helper('affiliate')->__('Invitation'),
            self::CLICKLINK  			 		=> Mage::helper('affiliate')->__('Referral Visitor'),
            self::REGISTER	    		    	=> Mage::helper('affiliate')->__('Referral Sign-up'),
            self::PURCHASE	    		    	=> Mage::helper('affiliate')->__('Referral Purchase'),
        	self::SUBSCRIBE						=> Mage::helper('affiliate')->__('Referral Subscribe')	
        );
    }
    
 	static public function getLabel($status)
    {
    	$options = self::getOptionArray();
    	return $options[$status];
    }
    static public function getTransactionDetail($order_id,$email, $type)
    {
    	$result = "";
    	switch($type)
    	{
    		case self::CLICKLINK:
    			$result = Mage::helper('affiliate')->__('Commission for referral visitors');
    			break;
    		case self::REGISTER:
    			$registerCustomerName = Mage::helper('affiliate')->getBackendCustomerNameByEmail($email);
    			$result = Mage::helper('affiliate')->__('New customer account: %s (%s)', $registerCustomerName, $email);
    			break;
    			
    		case self::PURCHASE:
    			$purchaseCustomerName = Mage::helper('affiliate')->getBackendCustomerNameByEmail($email);
    			$result = Mage::helper('affiliate')->__('Order <b>#%s</b> of %s (%s)', $order_id, $purchaseCustomerName, $email);
    			break;
    			
    		case self::SUBSCRIBE:
    			$subscriberName = Mage::helper('affiliate')->getBackendCustomerNameByEmail($row['email']);
    			$result = Mage::helper('affiliate')->__('New subscriber: %s (%s)', $subscriberName, $email);
    			break;
    	}
    	return $result;
    }
	static public function getTransactionDetailCsv($order_id,$email, $type)
    {
    	$result = "";
    	switch($type)
    	{
    		case self::CLICKLINK:
    			
    			$configValue =  Mage::getStoreConfig('affiliate/general/referral_visitor_commission');
				$configComponents = explode('/', $configValue);
				$visitorNo  = intval($configComponents[1]);
				$plural = ($visitorNo > 1) ? 's' : '';
	            				
    			$result = Mage::helper('affiliate')->__('Commission for %s referral visitor%s', $visitorNo, $plural);
    			break;
    			
    		case self::REGISTER:
    			$registerCustomerName = Mage::helper('affiliate')->getBackendCustomerNameByEmail($email);
    			$result = Mage::helper('affiliate')->__('New customer account: %s (%s)', $registerCustomerName, $email);
    			break;
    			
    		case self::PURCHASE:
    			$purchaseCustomerName = Mage::helper('affiliate')->getBackendCustomerNameByEmail($email);
    			$result = Mage::helper('affiliate')->__('Order #%s of %s (%s)', $order_id, $purchaseCustomerName, $email);
    			break;
    			
    		case self::SUBSCRIBE:
    			$subscriberName = Mage::helper('affiliate')->getBackendCustomerNameByEmail($row['email']);
    			$result = Mage::helper('affiliate')->__('New subscriber: %s (%s)', $subscriberName, $email);
    			break;
    	}
    	return $result;
    }
}