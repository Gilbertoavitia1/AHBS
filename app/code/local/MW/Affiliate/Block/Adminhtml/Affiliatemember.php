<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_affiliatemember';
        $this->_blockGroup = 'affiliate';
        $this->_headerText = Mage::helper('affiliate')->__('Active Affiliates');
        $this->_addButtonLabel = Mage::helper('affiliate')->__('Add Affiliate');

        parent::__construct();
    }
}