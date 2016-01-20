<?php
class MW_Affiliate_Model_Type extends Varien_Object
{
    const INVITATION			= 1;
    const REGISTERING		    = 2;
	const BUY_PRODUCT			= 3;
	const REFUND_PRODUCT		= 4;
	

    static public function getOptionArray()
    {
        return array(
            self::INVITATION    		=> Mage::helper('affiliate')->__('Click Referral Link'),
            self::REGISTERING   		=> Mage::helper('affiliate')->__('Referred Customer Register'),
            self::BUY_PRODUCT    		=> Mage::helper('affiliate')->__('Referred Customer Buys Product'),
            self::REFUND_PRODUCT    	=> Mage::helper('affiliate')->__('Referred Customer Refunds Product'),
            
        );
    }
	static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
    static public function getTransactionDetail($customerId,$type, $detail, $title, $status)
    {
    	$result = "";
    	switch($type)
    	{
    		case self::INVITATION:
    			$result = Mage::helper('affiliate')->__("Ip address: <b>".$detail."</b>  click referral link");
    			break;
    		case self::REGISTERING:
    			$EmailCustomer = Mage::getModel('customer/customer')->load($detail)->getEmail();
    			$result = Mage::helper('affiliate')->__("Invited Customer registers having email address: <b>".$EmailCustomer."</b>");
    			break;
    		case self::BUY_PRODUCT:
    			$customer = Mage::getModel('customer/customer')->load($detail);
    			$order = Mage::getModel("sales/order")->loadByIncrementId($title);
    			if($status==MW_Affiliate_Model_Status::PENDING)
    			{
    				$result = Mage::helper('affiliate')->__("Invited Customer has email address: <b>".$customer->getEmail()."</b> placing orders that cost: <b>".Mage::helper('core')->currency($order->getGrandTotal())."</b>");
    			}
    			if($status==MW_Affiliate_Model_Status::COMPLETE)
    			{
    				$result = Mage::helper('affiliate')->__("Invited Customer has email address: <b>".$customer->getEmail()."</b> buying products that cost: <b>".Mage::helper('core')->currency($order->getGrandTotal())."</b>");
    				
    			}
    			if($status==MW_Affiliate_Model_Status::CANCELED)
    			{
    				$result = Mage::helper('affiliate')->__("Invited Customer has email address: <b>".$customer->getEmail()."</b> cancelling orders that cost: <b>".Mage::helper('core')->currency($order->getGrandTotal())."</b>");
    			}
    			break;
    		case self::REFUND_PRODUCT:
    			$customer = Mage::getModel('customer/customer')->load($detail);
    			$order = Mage::getModel("sales/order")->loadByIncrementId($title);
    			$result = Mage::helper('affiliate')->__("Invited Customer has email address: <b>".$customer->getEmail()."</b> refunding orders that cost: <b>".Mage::helper('core')->currency($order->getGrandTotal())."</b>");
    			break;
    	}
    	return $result;
    }
    static public function getDetailCustomer($customerId)
    {
    	$result = "";
    	$customer = Mage::getModel('customer/customer')->load($customerId);
    	$result = Mage::helper('affiliate')->__("Invited Customer registers having email address: <b>".$customer->getEmail()."</b>");
    	return $result;
    }
} 