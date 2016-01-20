<?php

class MW_Affiliate_Model_Autowithdrawn extends Varien_Object
{
	const AUTO				= 1;		//haven't change points yet
    const MANUAL			= 2;
	

    static public function getOptionArray()
    {
        return array(
            self::AUTO    				=> Mage::helper('affiliate')->__('Auto'),
            self::MANUAL  			    => Mage::helper('affiliate')->__('Manual'),
        );
    }
 	static public function getLabel($gateway)
    {
    	$options = self::getOptionArray();
    	return $options[$gateway];
    }
}