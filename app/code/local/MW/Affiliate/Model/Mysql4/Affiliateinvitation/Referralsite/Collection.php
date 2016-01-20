<?php

class MW_Affiliate_Model_Mysql4_Affiliateinvitation_Referralsite_Collection extends MW_Affiliate_Model_Mysql4_Affiliateinvitation_Collection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliateinvitation');
    }
	public function setDateRange($from, $to)
	{
			$resource = Mage::getModel('core/resource');
  	  		$customer_table = $resource->getTableName('customer/entity');
			$array_stuatus = array(MW_Affiliate_Model_Statusinvitation::CLICKLINK,MW_Affiliate_Model_Statusinvitation::REGISTER,MW_Affiliate_Model_Statusinvitation::PURCHASE);
	        $this->_reset() ->addFieldToFilter('invitation_time', array('from' => $from, 'to' => $to, 'datetime' => true));
	        $this ->addFieldToFilter('referral_from_domain',array('neq'=>''));
	        $this->getSelect()->joinLeft(
      							array('customer_entity'=>$customer_table),'main_table.customer_id = customer_entity.entity_id',array('website_id'));
	        $this ->addFieldToFilter('status',$array_stuatus);
	        $this->addExpressionFieldToSelect('count_click_link_sum','sum(count_click_link)','count_click_link_sum');
	        $this->addExpressionFieldToSelect('count_register_sum','sum(count_register)','count_register_sum');
	        $this->addExpressionFieldToSelect('count_purchase_sum','sum(count_purchase)','count_purchase_sum');
	        $this->getSelect()->group(array('referral_from_domain'));
	        //echo $this->getSelect(); die();
	        return $this;
	}
 	public function setStoreIds($storeIds)
    {
        return $this;
    }
}