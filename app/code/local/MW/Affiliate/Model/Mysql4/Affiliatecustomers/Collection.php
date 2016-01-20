<?php

class MW_Affiliate_Model_Mysql4_Affiliatecustomers_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliatecustomers');
    }
	public function addFieldToFilter($attribute, $condition=null)
    {	
    	$customer = Mage::getModel('customer/customer')->getCollection();
  	  	$customer_table = $customer->getTable('customer/entity');
  	  	
    	if($attribute == 'customer_id'){
    		$attribute = 'main_table.'.$attribute;
    	} elseif($attribute == 'referral_name'){
    		$attribute = $customer_table.'.entity_id';
    	}elseif($attribute == 'status'){
    		$attribute = 'main_table.'.$attribute;
    	};
    	return parent::addFieldToFilter($attribute, $condition);
//	    if($attribute=='customer_id'){
//	    	//zend_debug::dump( $condition);die();
//	    	//var_dump($condition);die();
//	    	if(is_array($condition))
//	    	{
//	    		if(isset($condition['like'])){
//					$this->getSelect()->where('main_table.customer_id like "'.$condition['like'].'"');
//				}
//				elseif(isset($condition['eq'])){
//					$this->getSelect()->where('main_table.customer_id="'.$condition['eq'].'"');
//				}
//	    	}
//			else
//			{
//				$this->getSelect()->where('main_table.customer_id="'.$condition.'"');
//			}
//				
//		return $this;
//		}
//    	elseif($attribute=='referral_name'){
//    		if(is_array($condition))
//    		{
//    			if(isset($condition['eq']))
//					$this->getSelect()->where('customer_entity.entity_id="'.$condition['eq'].'"');
//    		}
//			else
//				$this->getSelect()->where('customer_entity.entity_id="'.$condition.'"');
//			return $this;
//		}
//    	elseif($attribute=='status'){
//    		//zend_debug::dump( $condition);die();
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