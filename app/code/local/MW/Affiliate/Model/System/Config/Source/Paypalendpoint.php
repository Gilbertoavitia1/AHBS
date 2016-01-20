<?php
class MW_Affiliate_Model_System_Config_Source_Paypalendpoint 
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('affiliate')->__('Live')),
            array('value' => 0, 'label'=>Mage::helper('affiliate')->__('Sandbox')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            0 => Mage::helper('affiliate')->__('Sandbox'),
            1 => Mage::helper('affiliate')->__('Live'),
        );
    }

}
