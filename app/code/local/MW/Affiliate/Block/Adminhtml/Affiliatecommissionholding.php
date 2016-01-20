<?php
class MW_Affiliate_Block_Adminhtml_Affiliatecommissionholding extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
  	parent::__construct();
  	
    $this->_controller = 'adminhtml_affiliatecommissionholding';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Commission Holding Manager');
    $this->_removeButton('add');
  }
  
  public function _prepareLayout()
  {
  	parent::_prepareLayout();
  }
}