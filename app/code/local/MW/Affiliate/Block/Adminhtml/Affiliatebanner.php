<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebanner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_affiliatebanner';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Banner Manager');
    $this->_addButtonLabel = Mage::helper('affiliate')->__('Add Banner');
    parent::__construct();
  }
  
  public function _prepareLayout()
  {
  	parent::_prepareLayout();
  }
}