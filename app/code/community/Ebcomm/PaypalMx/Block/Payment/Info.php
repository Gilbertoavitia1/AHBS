<?php

/**
 * PayPal common payment info block
 * Uses default templates
 */
class Ebcomm_PaypalMx_Block_Payment_Info extends Mage_Payment_Block_Info_Cc
{
    /**
     * Don't show CC type for non-CC methods
     *
     * @return string|null
     */
    public function getCcTypeName()
    {
        if (Ebcomm_PaypalMx_Model_Config::getIsCreditCardMethod($this->getInfo()->getMethod())) {
            return parent::getCcTypeName();
        }
    }

    /**
     * Prepare PayPal-specific payment information
     *
     * @param Varien_Object|array $transport
     * return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $payment = $this->getInfo();
        $paypalInfo = Mage::getModel('paypalmx/info');
        if (!$this->getIsSecureMode()) {
            $info = $paypalInfo->getPaymentInfo($payment, true);
        } else {
            $info = $paypalInfo->getPublicPaymentInfo($payment, true);
        }
        return $transport->addData($info);
    }
}
