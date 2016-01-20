<?php
class MW_Affiliate_Block_Invitation_Transaction extends Mage_Core_Block_Template
{   
	public function __construct()
    {
        parent::__construct();

		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
        $invitations = Mage::getModel('affiliate/affiliateinvitation')->getCollection()
							->addFieldtoFilter('customer_id',$customer_id)
							->addFieldToFilter('status', array('in' => array(MW_Affiliate_Model_Statusinvitation::CLICKLINK, MW_Affiliate_Model_Statusinvitation::REGISTER,MW_Affiliate_Model_Statusinvitation::SUBSCRIBE,MW_Affiliate_Model_Statusinvitation::PURCHASE)))
							->setOrder('invitation_time', 'DESC');
        
        $this->setInvitationHistory($invitations); 
    }
    
	public function getStatusText($status)
	{
		return MW_Affiliate_Model_Statusinvitation::getLabel($status);
	}
	
	public function getInvitationReport()
	{
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		$collections = Mage::getModel('affiliate/affiliateinvitation')->getCollection()->setReportInvitation($customer_id);
		$result = array();
		$result['click'] = 0;
		$result['register'] = 0;
		$result['purchase'] = 0;
		$result['subscribe'] = 0;
		
		foreach ($collections as $collection) {
			$result['click'] 		= $collection->getCountClickLinkSum();
			$result['register'] 	= $collection->getCountRegisterSum();
			$result['purchase'] 	= $collection->getCountPurchaseSum();
			$result['subscribe'] 	= $collection->getCountSubscribeSum();
			
			break;
		}
		return $result;
	}	
	
	public function _prepareLayout()
    {
		parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'customer_invitation_transaction_')
					  ->setCollection($this->getInvitationHistory());
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
