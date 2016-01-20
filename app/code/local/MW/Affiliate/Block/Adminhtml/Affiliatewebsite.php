<?php
class MW_Affiliate_Block_Adminhtml_Affiliatewebsite extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
  	parent::__construct();
  	
    $this->_controller = 'adminhtml_affiliatewebsite';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Affiliate Website');
    $this->_removeButton('add');
  }
  
  public function _prepareLayout()
  {
  	parent::_prepareLayout();
  }
}