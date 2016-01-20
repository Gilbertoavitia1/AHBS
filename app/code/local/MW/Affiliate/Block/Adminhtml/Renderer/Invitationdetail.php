<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Invitationdetail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['status'])) {
    		return '';
    	}
    	
    	$order_id = $row['order_id'];
    	$email = $row['email'];
    	$type = $row['status'];
    	return Mage::getModel('affiliate/statusinvitation')->getTransactionDetail($order_id,$email, $type);
    	/*
    	$invitationStatus = $row['status'];
    	if($invitationStatus == MW_Affiliate_Model_Statusinvitation::CLICKLINK) {
    		$configValue =  Mage::getStoreConfig('affiliate/general/referral_visitor_commission');
    		$configComponents = explode('/', $configValue);
    		$visitorNo  = intval($configComponents[1]);
    		
    		$plural = ($visitorNo > 1) ? 's' : '';
    	
    		return Mage::helper('affiliate')->__('Commission for %s referral visitor%s', $visitorNo, $plural);
    	}
    	else if($invitationStatus == MW_Affiliate_Model_Statusinvitation::REGISTER) {
			$registerCustomerName = Mage::helper('affiliate')->getBackendCustomerNameByEmail($row['email']);
    		return Mage::helper('affiliate')->__('New customer account: %s (%s)', $registerCustomerName, $row['email']);
    	}
    	else if($invitationStatus == MW_Affiliate_Model_Statusinvitation::PURCHASE) {
    		$purchaseCustomerName = Mage::helper('affiliate')->getBackendCustomerNameByEmail($row['email']);
    		
    		return Mage::helper('affiliate')->__('Order <b>#%s</b> of %s (%s)', $row['order_id'], $purchaseCustomerName, $row['email']);
    	
    	}
    	else if($invitationStatus == MW_Affiliate_Model_Statusinvitation::SUBSCRIBE) {
    		$subscriberName = Mage::helper('affiliate')->getBackendCustomerNameByEmail($row['email']);
    		return Mage::helper('affiliate')->__('New subscriber: %s (%s)', $subscriberName, $row['email']);
    	}
    	*/
    }

}