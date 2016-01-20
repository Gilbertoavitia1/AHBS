<?php

class MW_Affiliate_Model_Mysql4_Affiliatewithdrawn_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliatewithdrawn');
    }
	
    public function addFieldToFilter($attribute, $condition=null)
    {	
    	$affiliatecustomer = Mage::getModel('affiliate/affiliatecustomers')->getCollection();
  	  	$affiliatecustomer_table = $affiliatecustomer->getTable('affiliatecustomers');
  	  	
    	if($attribute=='customer_email'){
    		$attribute = $affiliatecustomer_table.'.customer_id';
    	}elseif($attribute=='payment_gateway'){
    		$attribute = $affiliatecustomer_table.'.payment_gateway';
    	}elseif($attribute=='status'){
    		$attribute = 'main_table.'.$attribute;
    	};
    	return parent::addFieldToFilter($attribute, $condition);
    	
    	
    	
    	
//		if($attribute=='customer_email'){
//			if(is_array($condition))
//			{
//				if(isset($condition['eq']))
//					$this->getSelect()->where('mw_affiliate_customers.customer_id="'.$condition['eq'].'"');
//			}
//			else
//				$this->getSelect()->where('mw_affiliate_customers.customer_id="'.$condition.'"');
//		return $this;
//		}
//		elseif($attribute=='payment_gateway'){
//			if(is_array($condition))
//			{
//				if(isset($condition['eq']))
//					$this->getSelect()->where('mw_affiliate_customers.payment_gateway="'.$condition['eq'].'"') ;
//			}
//			else
//				$this->getSelect()->where('mw_affiliate_customers.payment_gateway="'.$condition.'"') ;
//			return $this;
//		}
//    	elseif($attribute=='status'){
//    		if(is_array($condition))
//    		{
//    			if(isset($condition['eq']))
//					$this->getSelect()->where('main_table.status="'.$condition['eq'].'"') ;
//    		}
//			else
//				$this->getSelect()->where('main_table.status="'.$condition.'"') ;
//			return $this;
//		}
//		else
//			//return $this;
//        return parent::addFieldToFilter($attribute, $condition);
//        //return $this->addFieldToFilter($attribute, $condition);
    }
    
    
}