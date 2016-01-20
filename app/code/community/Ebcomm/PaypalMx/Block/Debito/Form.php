<?php

class Ebcomm_PaypalMx_Block_Debito_Form extends Ebcomm_PaypalMx_Block_Standard_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Ebcomm_PaypalMx_Model_Config::METHOD_PPMX_DEBITO;

    /**
     * Set template and redirect message
     */
    protected function _construct()
    {
        $result = parent::_construct();
        $this->setRedirectMessage(Mage::helper('paypalmx')->__(''));
        return $result;
    }

}
