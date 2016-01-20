<?php

class MW_Affiliate_Block_Adminhtml_Affiliatehistory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'history_id';
        $this->_blockGroup = 'affiliate';
        $this->_controller = 'adminhtml_affiliatehistory';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Import'));
        $this->_removeButton('delete');
    }

    public function getHeaderText()
    {
    	return Mage::helper('affiliate')->__('Update Affiliate Transactions via CSV');
    }
}