<?php
class MW_Affiliate_Block_Adminhtml_Affiliatereport_Code extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
	    $this->_controller = 'adminhtml_affiliatereport_code';
	    $this->_headerText = Mage::helper('affiliate')->__('Affiliate Sale Statistic by time range');
	    $this->_blockGroup = 'affiliate';
	    parent::__construct();
	    $this->_removeButton('add');
  }
  
}