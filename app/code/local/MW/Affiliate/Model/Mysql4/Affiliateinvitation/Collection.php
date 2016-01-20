<?php

class MW_Affiliate_Model_Mysql4_Affiliateinvitation_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
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
			$array_stuatus = array(MW_Affiliate_Model_Statusinvitation::CLICKLINK,MW_Affiliate_Model_Statusinvitation::REGISTER,MW_Affiliate_Model_Statusinvitation::SUBSCRIBE,MW_Affiliate_Model_Statusinvitation::PURCHASE);
	        $this->_reset() ->addFieldToFilter('invitation_time', array('from' => $from, 'to' => $to, 'datetime' => true));
	        $this->getSelect()->joinLeft(
      							array('customer_entity'=>$customer_table),'main_table.customer_id = customer_entity.entity_id',array('website_id'));
	        $this ->addFieldToFilter('status',$array_stuatus);
	        $this->addExpressionFieldToSelect('count_click_link_sum','sum(count_click_link)','count_click_link_sum');
	        $this->addExpressionFieldToSelect('count_register_sum','sum(count_register)','count_register_sum');
	        $this->addExpressionFieldToSelect('count_purchase_sum','sum(count_purchase)','count_purchase_sum');
	        $this->addExpressionFieldToSelect('count_subscribe_sum','sum(count_subscribe)','count_subscribe_sum');
	        $this->getSelect()->group(array('customer_id'));
	        
	        return $this;
	}
	public function setReportInvitation($customer_id)
	{
		$array_status = array(MW_Affiliate_Model_Statusinvitation::CLICKLINK,MW_Affiliate_Model_Statusinvitation::REGISTER,MW_Affiliate_Model_Statusinvitation::SUBSCRIBE, MW_Affiliate_Model_Statusinvitation::PURCHASE);
        $this->_reset()->addFieldToFilter('customer_id', $customer_id);
 
        $this->addFieldToFilter('status',$array_status);
        $this->addExpressionFieldToSelect('count_click_link_sum','sum(count_click_link)','count_click_link_sum');
        $this->addExpressionFieldToSelect('count_register_sum','sum(count_register)','count_register_sum');
        $this->addExpressionFieldToSelect('count_purchase_sum','sum(count_purchase)','count_purchase_sum');
        $this->addExpressionFieldToSelect('count_subscribe_sum','sum(count_subscribe)','count_subscribe_sum');
        $this->getSelect()->group(array('customer_id'));
        
        return $this;
		
	}
 	public function setStoreIds($storeIds)
    {
        return $this;
    }
}