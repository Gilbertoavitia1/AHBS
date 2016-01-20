<?php

class MW_Affiliate_Block_Adminhtml_Affiliatebanner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('affiliate_banner_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('affiliate')->__('Banner Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_banner', array(
          'label'     => Mage::helper('affiliate')->__('Banner Information'),
          'title'     => Mage::helper('affiliate')->__('Banner Information'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebanner_edit_tab_form')->toHtml(),
		  'active'    =>true,
      ));
      $this->addTab('form_banner_member', array(
             'label'     => Mage::helper('affiliate')->__('Members'),
             'url'       => $this->getUrl('*/*/member', array('_current' => true)),
             'class'     => 'ajax',
      	 	 //'active'    =>true,
            ));
     
      return parent::_beforeToHtml();
  }
}