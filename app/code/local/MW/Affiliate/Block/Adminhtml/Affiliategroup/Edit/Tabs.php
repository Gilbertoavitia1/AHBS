<?php

class MW_Affiliate_Block_Adminhtml_Affiliategroup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('affiliategroup_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('affiliate')->__('Affiliate Group Information'));
  }

  protected function _beforeToHtml()
  {   
      $this->addTab('form_member_detail', array(
          'label'     => Mage::helper('affiliate')->__('General information'),
          'title'     => Mage::helper('affiliate')->__('General information'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliategroup_edit_tab_form')->toHtml(),
      	  'active'    =>true,
      ));
      $this->addTab('form_group_program', array(
             'label'     => Mage::helper('affiliate')->__('Programs'),
             'url'       => $this->getUrl('*/*/program', array('_current' => true)),
             'class'     => 'ajax',
      		 //'active'    =>true,
            ));
        $this->addTab('form_group_member', array(
             'label'     => Mage::helper('affiliate')->__('Members'),
             'url'       => $this->getUrl('*/*/member', array('_current' => true)),
             'class'     => 'ajax',
      		 //'active'    =>true,
            ));
     
      return parent::_beforeToHtml();
  }
}