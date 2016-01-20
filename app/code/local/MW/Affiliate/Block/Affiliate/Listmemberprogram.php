<?php
class MW_Affiliate_Block_Affiliate_Listmemberprogram extends Mage_Core_Block_Template
{
	
	public function __construct()
    {
        parent::__construct();

		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
    	$program_ids = array();
    	$customer_groups =  Mage::getModel('affiliate/affiliategroupmember')->getCollection()
    										->addFieldToFilter('customer_id',$customer_id);
    	if(sizeof($customer_groups) >0 )
    	{
	    	foreach ($customer_groups as $customer_group) {
	    		$group_id = $customer_group ->getGroupId();
	    		break;
	    	}
	    	$customer_programs = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
	    										->addFieldToFilter('group_id',$group_id);
	    	foreach ($customer_programs as $customer_program) {
	    		$program_ids[] = $customer_program->getProgramId();
	    	}
    	}
    	
    	if (!Mage::app()->isSingleStoreMode()) $program_ids = $this ->getProgramByStoreView($program_ids);
        $programs = Mage::getModel('affiliate/affiliateprogram')->getCollection()
        			 ->addFieldtoFilter('program_id',array('in'=>$program_ids))
					 ->addFieldtoFilter('status',MW_Affiliate_Model_Statusprogram::ENABLED);
        $this->setListMemberProgram($programs); // set data for display to frontend
    }
    
	public function getProgramByStoreView($programs)
    {   
    	$program_ids = array();
    	$store_id = Mage::app()->getStore()->getId();
    	foreach ($programs as $program) {
    		
    		$store_view = Mage::getModel('affiliate/affiliateprogram')->load($program) ->getStoreView();
 			$store_views = explode(',',$store_view);
 			if(in_array($store_id, $store_views) OR $store_views[0]== '0') $program_ids[] = $program; 
    		
    	}
    	return $program_ids;
    }
	
	public function getTypeLabel($type)
	{
		return MW_Affiliate_Model_Type::getLabel($type);
	}
	
	public function getTransactionDetail($customerId,$type, $detail,$title,$status)
	{
		return MW_Affiliate_Model_Type::getTransactionDetail($customerId,$type,$detail,$title,$status);
	}
	
	public function getStatusText($status)
	{
		return MW_Affiliate_Model_Status::getLabel($status);
	}

	// prepare navigation
	public function _prepareLayout()
    {
		//return parent::_prepareLayout();
		parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'list_member_program')
					  ->setCollection($this->getListMemberProgram());	// set data for navigation
        $this->setChild('pager', $pager);
        return $this;
    }
	
	// get navigation
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
 	public function getCollection()
    {
    	return $this->getChild("pager")->getCollection();
    }
}