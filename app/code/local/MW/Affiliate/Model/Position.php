<?php
class MW_Affiliate_Model_Position
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('affiliate')->__('By maximum commission')),
            array('value'=>2, 'label'=>Mage::helper('affiliate')->__('By maximum discount')),
            array('value'=>3, 'label'=>Mage::helper('affiliate')->__('By program priority')),                                   
        );
    }

}
