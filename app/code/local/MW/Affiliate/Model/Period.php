<?php
class MW_Affiliate_Model_Period
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('affiliate')->__('Weekly')),
            array('value'=>2, 'label'=>Mage::helper('affiliate')->__('Monthly')),                                      
        );
    }

}
