<?php
class MW_Affiliate_Model_Statuswithdraw extends Varien_Object
{
    const COMPLETE	= 2;
    const CANCELED	= 3;

    static public function getOptionArray()
    {
        return array(
            self::COMPLETE  => Mage::helper('affiliate')->__('Complete'),
            self::CANCELED	=> Mage::helper('affiliate')->__('Canceled'),
        );
    }
    
 	static public function getLabel($status)
    {
    	$options = self::getOptionArray();
    	return $options[$status];
    }
}