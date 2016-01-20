<?php
class Ebcomm_PaypalMx_Block_Adminhtml_Settlement_Details extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Block construction
     * Initialize titles, buttons
     */
    public function __construct()
    {
        parent::__construct();
        $this->_controller = '';
        $this->_headerText = Mage::helper('paypalmx')->__('Ver detalles de la transacción');
        $this->_removeButton('reset')
            ->_removeButton('delete')
            ->_removeButton('save');
    }

    /**
     * Initialize form
     * @return Mage_Paypal_Block_Adminhtml_Settlement_Details
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild('form', $this->getLayout()->createBlock('paypalmx/adminhtml_settlement_details_form'));
        return $this;
    }
}
