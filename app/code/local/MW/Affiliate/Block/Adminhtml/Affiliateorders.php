<?php
class MW_Affiliate_Block_Adminhtml_Affiliateorders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_affiliateorders';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Affiliate Orders');
//    $this->_addButtonLabel = Mage::helper('affiliate')->__('Add Affiliate');
    
    parent::__construct();
  }
}