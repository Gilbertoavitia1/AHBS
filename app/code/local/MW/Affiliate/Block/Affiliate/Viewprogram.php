<?php
class MW_Affiliate_Block_Affiliate_Viewprogram extends Mage_Core_Block_Template
{
	
	public function __construct()
    {
        parent::__construct();

		$program_id = $this->getRequest()->getParam('id');
		$collection = Mage::getModel('affiliate/affiliateprogram')->getCollection()
							->addFieldtoFilter('program_id',$program_id);
        $this->setListProduct($collection); // set data for display to frontend
    }
	// prepare navigation
	public function _prepareLayout()
    {
		//return parent::_prepareLayout();
		parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'view_program')
					  ->setCollection($this->getListProduct());	// set data for navigation
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
	public function getProgramName()
    {	
    	$program_id = $this->getRequest()->getParam('id');
		$program_name = Mage::getModel('affiliate/affiliateprogram')->load($program_id)->getProgramName();
    	return $program_name;
    }
}