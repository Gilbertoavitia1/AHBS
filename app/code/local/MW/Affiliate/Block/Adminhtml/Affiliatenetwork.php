<?php
class MW_Affiliate_Block_Adminhtml_Affiliateorders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
      $this->_controller = 'adminhtml_affiliatenetwork';
      $this->_blockGroup = 'affiliate';
      $this->_headerText = Mage::helper('affiliate')->__('Affiliate Network');

      parent::__construct();
    }
}