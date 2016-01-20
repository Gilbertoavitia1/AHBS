<?php
/**
 * Source model for Require Billing Address
 */
class Ebcomm_PaypalMx_Model_System_Config_Source_RequireBillingAddress
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var $configModel Mage_Paypal_Model_Config */
        $configModel = Mage::getModel('paypalmx/config');
        return $configModel->getRequireBillingAddressOptions();
    }
}
