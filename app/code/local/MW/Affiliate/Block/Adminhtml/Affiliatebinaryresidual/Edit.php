<?php

class MW_Affiliate_Block_Adminhtml_Affiliatebinaryresidual_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'affiliate';
        $this->_controller = 'adminhtml_affiliatebinaryresidual';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save'));
        
        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }

    public function getHeaderText()
    {
        return Mage::helper('affiliate')->__('Binary Residual');
    }
}