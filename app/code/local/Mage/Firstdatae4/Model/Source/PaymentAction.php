<?php
class Mage_Firstdatae4_Model_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Payment_Model_Method_Cc::ACTION_AUTHORIZE, 'label' => Mage::helper('paygate')->__('Auth Only')),
            array('value' => Mage_Payment_Model_Method_Cc::ACTION_AUTHORIZE_CAPTURE, 'label' => Mage::helper('paygate')->__('Sale')),
        );
    }
}