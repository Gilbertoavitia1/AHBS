<?php

class MW_Affiliate_Model_Mysql4_Affiliatehistory_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliatehistory');
    }
	public function addFieldToFilter($attribute, $condition=null)
    {	
    	if($attribute=='status') $attribute = 'main_table.'.$attribute;
    	return parent::addFieldToFilter($attribute, $condition);
    }
	public function setDateRange($from, $to)
	{
	        $this->_reset() ->addFieldToFilter('transaction_time', array('from' => $from, 'to' => $to, 'datetime' => true));
	        $this ->addFieldToFilter('status',MW_Affiliate_Model_Status::COMPLETE);
	        $this->addExpressionFieldToSelect('product_id_count','count(product_id)','product_id_count');
	        $this->addExpressionFieldToSelect('customer_id_count','count( distinct customer_id)','customer_id_count');
	        $this->addExpressionFieldToSelect('order_id_count','count( distinct order_id)','order_id_count');
	        $this->addExpressionFieldToSelect('total_amount_sum','sum(total_amount)','total_amount_sum');
	        $this->addExpressionFieldToSelect('history_commission_sum','sum(history_commission)','history_commission_sum');
	        $this->addExpressionFieldToSelect('history_discount_sum','sum(history_discount)','history_discount_sum');
	        $this->getSelect()->group(array('customer_invited'));
	        return $this;
	}
 	public function setStoreIds($storeIds)
    {
        return $this;
    }
}