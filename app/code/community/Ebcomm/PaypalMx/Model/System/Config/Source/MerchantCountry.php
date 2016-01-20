<?php

/**
 * Source model for merchant countries supported by PayPal
 */
class Ebcomm_PaypalMx_Model_System_Config_Source_MerchantCountry
{
    public function toOptionArray($isMultiselect = false)
    {
        $supported = Mage::getModel('paypalmx/config')->getSupportedMerchantCountryCodes();
        $options = Mage::getResourceModel('directory/country_collection')
            ->addCountryCodeFilter($supported, 'iso2')
            ->loadData()
            ->toOptionArray($isMultiselect ? false : Mage::helper('adminhtml')->__('--Please Select--'));

        return $options;
    }
}
