<?php
class MW_Affiliate_InvitedController extends Mage_Core_Controller_Front_Action
{
protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	public function indexAction()
    {   
    	$invite = $this->getRequest()->getParam('c');
		Mage::dispatchEvent('affiliate_referral_link_click',array('invite'=>$invite,'request'=>$this->getRequest()));
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('affiliate')->__('Thank you for visiting our site'));
		$this->_redirectUrl(Mage::getBaseUrl());
    }
	
}
