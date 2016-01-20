<?php
class MW_Affiliate_Block_Adminhtml_Affiliatereport_Referral extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
	    $this->_controller = 'adminhtml_affiliatereport_referral';
	    $this->_headerText = Mage::helper('affiliate')->__('Affiliate Invitation Statistic');
	    $this->_blockGroup = 'affiliate';
	    parent::__construct();
	    $this->_removeButton('add');
  }
  
}