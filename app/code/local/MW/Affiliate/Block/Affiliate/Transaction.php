<?php
class MW_Affiliate_Block_Affiliate_Transaction extends Mage_Core_Block_Template
{
	
	public function __construct()
    {
        parent::__construct();

		// get collection follow filter customer_id
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
        $affiliates = Mage::getModel('affiliate/affiliatehistory')->getCollection()
					->addFilter('customer_invited',$customer_id)
					->setOrder('transaction_time', 'DESC');
        $this->setAffiliateHistory($affiliates); // set data for display to frontend
    }
	public function getProductName($product_id)
	{   
		return Mage::getModel('catalog/product')->load($product_id)->getName();
	}
	public function getProgramName($program_id)
	{   
		return Mage::getModel('affiliate/affiliateprogram')->load($program_id)->getProgramName();
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

        $pager = $this->getLayout()->createBlock('page/html_pager', 'customer_affiliate_transaction')
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