<?php

class MW_Affiliate_Model_Gateway extends Varien_Object
{
	const PAYPAL				= 1;		//haven't change points yet
    const MONEYBOOKERS			= 2;
	

    static public function getOptionArray()
    {
        return array(
            self::PAYPAL    				=> Mage::helper('affiliate')->__('Paypal'),
            self::MONEYBOOKERS  			=> Mage::helper('affiliate')->__('Moneybokers'),
        );
    }
 	static public function getLabel($gateway)
    {
    	$options = self::getOptionArray();
    	return $options[$gateway];
    }
}