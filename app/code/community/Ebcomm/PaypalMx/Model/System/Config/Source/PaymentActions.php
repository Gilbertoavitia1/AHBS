<?php
class Ebcomm_PaypalMx_Model_System_Config_Source_PaymentActions
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configModel = Mage::getModel('paypalmx/config');
        return $configModel->getPaymentActions();
    }
}
