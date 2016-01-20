<?php
/**
 * Source model for available paypal express payment actions
 */
class Ebcomm_PaypalMx_Model_System_Config_Source_PaymentActions_Express
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configModel = Mage::getModel('paypalmx/config');
        $configModel->setMethod(Ebcomm_PaypalMx_Model_Config::METHOD_PPMX);
        return $configModel->getPaymentActions();
    }
}
