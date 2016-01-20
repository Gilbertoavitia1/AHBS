<?php
class MW_Affiliate_Block_Affiliate_History extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();

		// get collection follow filter customer_id
	
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
        $affiliates = Mage::getModel('affiliate/affiliatetransaction')->getCollection()
							->addFieldtoFilter('customer_invited',$customer_id)
							->setOrder('transaction_time', 'DESC');
        $this->setAffiliateHistory($affiliates); // set data for display to frontend
        
        /*
        $customerId = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
        $invitationCollection = Mage::getModel('affiliate/affiliateinvitation')->getCollection();
        $invitationCollection->addFieldToFilter('customer_id', array('eq' => $customerId))
                             ->addFieldToFilter('commission', array('gt' => 0));
        
        $invitationCollection->setOrder('invitation_time', 'DESC');
        
        $this->setAffiliateInvitation($invitationCollection);*/
    }
    public function getCommissionType($type)
    {
    	return Mage::getModel('credit/transactiontype')->getLabel($type);
    }
	public function getStatusText($status)
	{
		return MW_Affiliate_Model_Status::getLabel($status);
	}
	public function getInvitationCommission($order_id,$status,$commission)
	{
		if($status == MW_Affiliate_Model_Statusinvitation::PURCHASE) {
            $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()->addFieldToFilter('order_id', array('eq' => $order_id));
            $totalCommission = 0;
            foreach($collection as $item) {
            	$totalCommission += $item->getHistoryCommission();
            }
            
            return Mage::helper('core')->currency($totalCommission,true,false);
        }else{
        	return Mage::helper('core')->currency($commission,true,false);
        }
	}
	public function getInvitationDiscount($order_id,$status)
	{
		if($status == MW_Affiliate_Model_Statusinvitation::PURCHASE) {
			$collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()->addFieldToFilter('order_id', array('eq' => $order_id));
			$totalDiscount = 0;
			foreach($collection as $item) {
				$totalDiscount += $item->getHistoryDiscount();
			}
			return Mage::helper('core')->currency($totalDiscount,true,false);
        }else{
        	return Mage::helper('core')->currency(0,true,false);
        }
		
	}
	public function getInvitationDetail($order_id,$email, $status)
	{
		return Mage::getModel('affiliate/statusinvitation')->getTransactionDetail($order_id,$email, $status);
	}
	public function getTransactionDetail($type, $detail, $is_admin=false,$customer_id)
	{
		if($type == MW_Credit_Model_Transactiontype::REFERRAL_VISITOR || $type == MW_Credit_Model_Transactiontype::REFERRAL_SIGNUP || $type == MW_Credit_Model_Transactiontype::REFERRAL_SUBSCRIBE )
		$detail = $customer_id;
		
		return Mage::getModel('credit/transactiontype')->getTransactionDetail($type, $detail, false);
	}
	public function getInvitationStatus($order_id,$status)
	{
		if($status == MW_Affiliate_Model_Statusinvitation::PURCHASE) {
			$collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()->addFieldToFilter('order_id', array('eq' => $order_id));
			foreach($collection as $item) {
				$orderStatus = $item->getStatus();
				break;
			}
            return MW_Affiliate_Model_Status::getLabel($orderStatus);
    	} else {
    		return Mage::helper('affiliate')->__('Completed');
    	}
	}
	
	public function _prepareLayout()
    {   
		parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'customer_affiliate_history')
					  ->setCollection($this->getAffiliateHistory());
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