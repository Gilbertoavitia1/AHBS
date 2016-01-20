<?php
class MW_Credit_Block_Credit_Transaction extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();

		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
        $credits = Mage::getModel('credit/credithistory')
        		   ->getCollection()
				   ->addFieldToFilter('customer_id',$customer_id)
				   ->addFieldToFilter('status', array('neq' => MW_Credit_Model_Orderstatus::HOLDING))
				   ->setOrder('created_time', 'DESC')
				   ->setOrder('credit_history_id', 'DESC');
        
        $this->setCreditHistory($credits);	// set data for display to frontend
    }
    
	public function getTypeLabel($type)
	{
		return MW_Credit_Model_Transactiontype::getLabel($type);
	}
	
	public function getTransactionDetail($type, $detail)
	{
		return MW_Credit_Model_Transactiontype::getTransactionDetail($type,$detail,false);
	}
	
	public function getStatusText($status)
	{
		return MW_Credit_Model_Orderstatus::getLabel($status);
	}

	// prepare navigation
	public function _prepareLayout()
    {
    	parent::_prepareLayout();
    	
        $pager = $this->getLayout()->createBlock('page/html_pager', 'transaction_pager')
					  ->setCollection($this->getCreditHistory());

        $this->setChild('pager', $pager);

        return $this;
    }
	
	// get navigation
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    public function getCollection() {
    	return $this->getChild("pager")->getCollection();
    }
}