<?php

class MW_Affiliate_Block_Adminhtml_Affiliatebonuspreferred_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'affiliate';
        $this->_controller = 'adminhtml_affiliatebonuspreferred';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Reference'));
        
        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }

    public function getHeaderText()
    {
        return Mage::helper('affiliate')->__('Preferred Customer Bonus');
    }
}