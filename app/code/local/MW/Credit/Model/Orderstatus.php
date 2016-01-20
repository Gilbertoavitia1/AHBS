<?php
class MW_Credit_Model_Orderstatus extends Varien_Object
{
	const PENDING				= 1;		//haven't change points yet
    const COMPLETE				= 2;
    const CANCELED			    = 3;
    const CLOSED				= 4;
    const HOLDING				= 5;

    static public function getOptionArray()
    {
        return array(
            self::PENDING    	=> Mage::helper('credit')->__('Pending'),
            self::COMPLETE	    => Mage::helper('credit')->__('Complete'),
            self::CANCELED  	=> Mage::helper('credit')->__('Canceled'),
            self::CLOSED  		=> Mage::helper('credit')->__('Closed'),
        	self::HOLDING		=> Mage::helper('credit')->__('Holding') 
        );
    }
    
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
}