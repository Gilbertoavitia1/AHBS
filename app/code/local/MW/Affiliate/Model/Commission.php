<?php
class MW_Affiliate_Model_Commission
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('affiliate')->__('Before Discount')),
            array('value'=>2, 'label'=>Mage::helper('affiliate')->__('After Discount')),                                   
        );
    }

}
