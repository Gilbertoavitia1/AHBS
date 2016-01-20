<?php
/**
 * Source model for available logo types
 */
class Ebcomm_PaypalMx_Model_System_Config_Source_Logo
{
    public function toOptionArray()
    {
        $result = array('' => Mage::helper('paypalmx')->__('No Logo'));
        $result += Mage::getModel('paypalmx/config')->getAdditionalOptionsLogoTypes();
        return $result;
    }
}
