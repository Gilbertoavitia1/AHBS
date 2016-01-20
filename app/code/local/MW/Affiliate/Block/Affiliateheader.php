<?php
class MW_Affiliate_Block_Affiliateheader extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {   
    	if(Mage::helper('affiliate')->getAffiliateActive()>0)
    	{
	    	return $this->getLayout()->getBlock('top.links')->addLink($this->__('My Affiliate Account'), $url='affiliate', $title = $this->__('My Affiliate'), $prepare=true, $urlParams=array(),
	        $position=11, $liParams=null, $aParams=null, $beforeText='', $afterText='');
    	}
		//return parent::_prepareLayout();
    }
    
}