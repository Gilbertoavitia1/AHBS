<?php
class MW_Affiliate_Block_Website extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
	
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		$collection = Mage::getModel('affiliate/affiliatewebsitemember')->getCollection()
						->addFieldtoFilter('customer_id', $customer_id);
		$collection->setOrder('status','ASC');
		$this->setWebsiteCollection($collection); // set data for display to frontend
	}
	
	public function _prepareLayout()
    {
		parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'website')
					  ->setCollection($this->getWebsiteCollection());
        
        $this->setChild('pager', $pager);
        return $this;
    }
	
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
 	public function getCollection()
    {
    	return $this->getChild("pager")->getCollection();
    }
}