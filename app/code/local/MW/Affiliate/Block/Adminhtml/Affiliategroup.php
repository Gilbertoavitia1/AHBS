<?php
class MW_Affiliate_Block_Adminhtml_Affiliategroup extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_affiliategroup';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Manage Affiliate Groups');
    $this->_addButtonLabel = Mage::helper('affiliate')->__('Add Group');
    parent::__construct();
  }
}