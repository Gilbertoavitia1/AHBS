<?php
class MW_Affiliate_Block_Adminhtml_Affiliateparent extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_affiliateparent';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Manage Customers');
    parent::__construct();
    $this->_removeButton('add');
  }
}