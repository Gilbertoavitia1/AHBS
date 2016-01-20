<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Add_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
  	{
    	parent::__construct();
      	$this->setId('affiliatemember_tabs_new');
      	$this->setDestElementId('add_form');
      	$this->setTitle(Mage::helper('affiliate')->__('Affiliate Member Information'));
  	}

 	protected function _beforeToHtml()
  	{   
    	$this->addTab('form_member_detail_new', array(
        	'label'     => Mage::helper('affiliate')->__('General information'),
          	'title'     => Mage::helper('affiliate')->__('General information'),
          	'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_add_tab_form')->toHtml(),
      	));
          
      	return parent::_beforeToHtml();
  	}
}