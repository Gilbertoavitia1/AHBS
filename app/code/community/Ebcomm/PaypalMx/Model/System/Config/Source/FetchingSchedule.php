<?php

/**
 * Source model for available settlement report fetching intervals
 */
class Ebcomm_PaypalMx_Model_System_Config_Source_FetchingSchedule
{
    public function toOptionArray()
    {
        return array (
            1 => Mage::helper('paypalmx')->__("Diario"),
            3 => Mage::helper('paypalmx')->__("Cada 3 dias"),
            7 => Mage::helper('paypalmx')->__("Cada 7 dias"),
            10 => Mage::helper('paypalmx')->__("Cada 10 dias"),
            14 => Mage::helper('paypalmx')->__("Cada 14 dias"),
            30 => Mage::helper('paypalmx')->__("Cada 30 dias"),
            40 => Mage::helper('paypalmx')->__("Cada 40 dias"),
        );
    }
}
