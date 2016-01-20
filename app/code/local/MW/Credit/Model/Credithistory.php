<?php

class MW_Credit_Model_Credithistory extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('credit/credithistory');
    }
    
	private function _getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}
    
}