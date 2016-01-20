<?php

class Ebcomm_PaypalMx_Block_Mensualidades2_Form extends Ebcomm_PaypalMx_Block_Standard_Form
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
        $this->setMensualidadesAcvtive($this->_config->getIsMensualidadesActive());
        return $result;
    }

    /**
     * Set data to block
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if (Mage::helper('paypalmx')->shouldAskToCreateBillingAgreement($this->_config, $customerId)
             && $this->canCreateBillingAgreement()) {
            $this->setCreateBACode(Ebcomm_PaypalMx_Model_Mensualidades2_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
        }
        return parent::_beforeToHtml();
    }
}
