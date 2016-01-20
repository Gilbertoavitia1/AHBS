<?php
class MW_Affiliate_Block_Adminhtml_Affiliateexpired extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_affiliateexpired';
        $this->_blockGroup = 'affiliate';
        $this->_headerText = Mage::helper('affiliate')->__('Affiliate Expired');

        parent::__construct();
        $this->_removeButton('add');
    }
}