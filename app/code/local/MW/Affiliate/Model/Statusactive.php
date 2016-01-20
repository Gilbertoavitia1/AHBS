<?php

class MW_Affiliate_Model_Statusactive extends Varien_Object
{
	const PENDING				= 1;		
    const ACTIVE			    = 2;
    const INACTIVE			    = 3; 
    const NOTAPPROVED			= 4;
  
    static public function getOptionArray()
    {
        return array(
            self::PENDING    				=> Mage::helper('affiliate')->__('Pending'),
            self::ACTIVE  			 		=> Mage::helper('affiliate')->__('Active'),
            self::INACTIVE  			 	=> Mage::helper('affiliate')->__('Inactive'),
            self::NOTAPPROVED 			 	=> Mage::helper('affiliate')->__('Not Approved')
        );
    }
 	static public function getLabel($status)
    {
    	$options = self::getOptionArray();
    	return $options[$status];
    }
}