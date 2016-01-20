<?php
class MW_Affiliate_Model_Days
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('affiliate')->__('Sunday')),
            array('value'=>1, 'label'=>Mage::helper('affiliate')->__('Monday')),
            array('value'=>2, 'label'=>Mage::helper('affiliate')->__('Tuesday')),
            array('value'=>3, 'label'=>Mage::helper('affiliate')->__('Wednesday')),
            array('value'=>4, 'label'=>Mage::helper('affiliate')->__('Thursday')),
            array('value'=>5, 'label'=>Mage::helper('affiliate')->__('Friday')),
            array('value'=>6, 'label'=>Mage::helper('affiliate')->__('Saturday')),
                                                
        );
    }
	public function getLabel($status)
    {
    	$options = $this->toOptionArray();
    	return $options[$status]['label'];
    }

}
