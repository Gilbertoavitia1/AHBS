<?php
class MW_Affiliate_Block_Affiliate_Banner extends Mage_Core_Block_Template
{
 	public function getInvitationBanners()
    {
    	  $store_id = Mage::app()->getStore()->getId();
		  $customer_id = (int)Mage::getSingleton('customer/session')->getCustomer()->getId();
		  $group_members = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
				        					 ->addFieldToFilter('customer_id',$customer_id);
		  $group_id = 0;
		  if(sizeof($group_members)>0){
		  	foreach ($group_members as $group_member) {
		  		 $group_id = $group_member->getGroupId();
		  	}
		  }
		  
	 	  $collection_banners = Mage::getModel('affiliate/affiliatebanner')->getCollection();
	 	  // filter banner by store
	 	  $banner_ids = array();
	 	  
	 	  foreach ($collection_banners as $collection_banner) {
	 	  	  $banner_id = $collection_banner ->getBannerId();
	 	  	  $member_banners = Mage::getModel('affiliate/affiliatebannermember')->getCollection()
		  					   			          ->addFieldToFilter('banner_id',$banner_id)
		                       					  ->addFieldToFilter('customer_id',$customer_id);
	 	  	  $group_id_banner = $collection_banner ->getGroupId();
	 	  	  $group_id_banners = explode(",",$group_id_banner);
	 		  $store_views = $collection_banner ->getStoreView();
	 		  //$store_views = explode(",",$store_view);
	 		  if((in_array($store_id, $store_views) OR $store_views[0]== '0') && (in_array($group_id, $group_id_banners) OR sizeof($member_banners)>0 )) 
	 		  	$banner_ids[] = $banner_id; 
	 	  }
	      $invitation_banners = Mage::getModel('affiliate/affiliatebanner')->getCollection()
	  				                ->addFieldToFilter('banner_id',array('in' => $banner_ids))
	                                ->addFieldToFilter('status',MW_Affiliate_Model_Statusprogram::ENABLED);
	      return $invitation_banners;
    }
}