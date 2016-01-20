<?php
class MW_Affiliate_Block_Adminhtml_Affiliatelikebox extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_affiliatelikebox';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Likebox Default Product List Manager');
    $this->_addButtonLabel = Mage::helper('affiliate')->__('Add New Default Product List');
    parent::__construct();
  }
  
  public function _prepareLayout()
  {
  	parent::_prepareLayout();
  }
}