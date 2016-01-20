<?php
class MW_Affiliate_Block_Adminhtml_Affiliateprogram extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_affiliateprogram';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Manage Programs');
    $this->_addButtonLabel = Mage::helper('affiliate')->__('Add Program');
    parent::__construct();
  }
}