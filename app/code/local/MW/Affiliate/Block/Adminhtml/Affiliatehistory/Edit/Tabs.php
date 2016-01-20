<?php

class MW_Affiliate_Block_Adminhtml_Affiliatehistory_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('affiliatehistory_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('affiliate')->__('Manage Importing'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('affiliate')->__('Update Affiliate Transactions via CSV'),
          'title'     => Mage::helper('affiliate')->__('Update Affiliate Transactions via CSV'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatehistory_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}