<?php
class MW_Affiliate_Block_Affiliate_Withdrawnhistory extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
    {
        parent::__construct();

		// get collection follow filter customer_id
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
        $affiliates = Mage::getModel('affiliate/affiliatewithdrawn')->getCollection()
							->addFieldtoFilter('customer_id',$customer_id)
							->setOrder('withdrawn_time', 'DESC');
        $this->setAffiliateHistory($affiliates); // set data for display to frontend
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
		parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'customer_affiliate_withdrawn_history')
					  ->setCollection($this->getAffiliateHistory());	// set data for navigation
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