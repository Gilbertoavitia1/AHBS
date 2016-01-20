<?php
class MW_Affiliate_Model_Signupform
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('affiliate')->__('Enable, signup checkbox')),
            array('value'=>2, 'label'=>Mage::helper('affiliate')->__('Enable, signup form')),
            array('value'=>3, 'label'=>Mage::helper('affiliate')->__('Disable')),                                   
        );
    }

}
