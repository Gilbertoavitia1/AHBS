<?php
class MW_Affiliate_Block_Affiliatefooter extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {   
    	if(Mage::helper('affiliate')->getAffiliateActive()>0)
    	{   
    		$dk = 1;
    		$store_id = Mage::app()->getStore()->getId();
    		$currentUrls = Mage::helper('core/url')->getCurrentUrl();
    		$not_shares = Mage::helper('affiliate/data')->getAffiliateShareStore($store_id);
	 		$not_shares = explode("\n",$not_shares);
	 		//var_dump($not_shares);die();
	 		foreach ($not_shares as $not_share)
	 		 {      
	 		 	if(trim($not_share) !='')  
		 		 {  
		 		 	if(substr_count($currentUrls,trim($not_share))>0) $dk = 2;
		 		 }
	 				
	 		}
	 		if($dk == 1)
	 		{
	 			$currentUrl = explode("//",$currentUrls);
	    		$invitationUrl = Mage::getUrl('affiliate/invitation');
	    		$url_link = $invitationUrl."?mw_link=".$currentUrl[1].'&mw_pro='.$currentUrl[0];
	 		}
	 		else if($dk == 2) $url_link ='#';
    		//footer_links
    		
	    	return $this->getLayout()->getBlock('footer_links')->addLink($this->__('Share this page'), $url=$url_link, 
		    	$title=$this->__('Share this page'), $prepare=false, $urlParams=array(),
		        $position=null, $liParams=null, $aParams=null, $beforeText='', $afterText='');
    	}
    }
    
}