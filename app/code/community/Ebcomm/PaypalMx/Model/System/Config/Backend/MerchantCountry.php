<?php
class Ebcomm_PaypalMx_Model_System_Config_Backend_MerchantCountry extends Mage_Core_Model_Config_Data
{
    /**
     * Config path to default country
     * @deprecated since 1.4.1.0
     * @var string
     */
    const XML_PATH_COUNTRY_DEFAULT = 'general/country/default';

    /**
     * Substitute empty value with Default country.
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (empty($value)) {
            if ($this->getWebsite()) {
                $defaultCountry = Mage::app()->getWebsite($this->getWebsite())
                    ->getConfig(Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY);
            } else {
                $defaultCountry = Mage::helper('core')->getDefaultCountry($this->getStore());
            }
            $this->setValue($defaultCountry);
        }
    }
}
