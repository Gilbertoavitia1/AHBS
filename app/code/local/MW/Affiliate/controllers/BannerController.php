<?php
class MW_Affiliate_BannerController extends Mage_Core_Controller_Front_Action
{
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
    
}
