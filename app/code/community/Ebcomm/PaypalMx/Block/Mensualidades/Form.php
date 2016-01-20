<?php

class Ebcomm_PaypalMx_Block_Mensualidades_Form extends Ebcomm_PaypalMx_Block_Standard_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Ebcomm_PaypalMx_Model_Config::METHOD_PPMX_MENSUALIDADES;

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
