<?php

class MW_Affiliate_Model_Mysql4_Affiliatecustomers extends Mage_Core_Model_Mysql4_Abstract
{
	protected $_isPkAutoIncrement = false;
	
    public function _construct()
    {    
        // Note that the affiliate_id refers to the key field in your database table.
        $this->_init('affiliate/affiliatecustomers', 'customer_id');
    }
}