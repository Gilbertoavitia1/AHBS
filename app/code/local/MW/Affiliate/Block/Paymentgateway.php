<?php
class MW_Affiliate_Block_Paymentgateway extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('gateway_value', array(
            'label' => Mage::helper('adminhtml')->__('Payment Method Code'),
            'style' => 'width:120px',
        ));
		$this->addColumn('gateway_title', array(
            'label' => Mage::helper('adminhtml')->__('Payment Method Title'),
            'style' => 'width:200px',
        ));	
        $this->addColumn('mw_status', array(
          'label'    => Mage::helper('affiliate')->__('Enable Frontend'),
          'style' => 'width:100px',
      ));
						 
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Payment Method');
        parent::__construct();
    }
	
}