<?php

class MW_Affiliate_Block_Adminhtml_Affiliatememberpending_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('affiliatememberpending_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('affiliate')->__('Affiliate Member Pending'));
  }

  protected function _beforeToHtml()
  {   

      $this->addTab('form_member_detail', array(
          'label'     => Mage::helper('affiliate')->__('General information'),
          'title'     => Mage::helper('affiliate')->__('General information'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatememberpending_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}