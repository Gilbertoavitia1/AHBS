<?php

class MW_Affiliate_Block_Adminhtml_Affiliategroup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {   
      $form_group_detail = new Varien_Data_Form();
      $this->setForm($form_group_detail);
      $fieldset = $form_group_detail->addFieldset('affiliate_form', array('legend'=>Mage::helper('affiliate')->__('Group Information')));
      
      $group_id = $this->getRequest()->getParam('id');      

      $affiliate_group = Mage::getModel('affiliate/affiliategroup')->load($group_id);
	  $group_name = $affiliate_group->getGroupName();

      $fieldset->addField('group_name', 'text', array(
          'label'     => Mage::helper('affiliate')->__('Group Name'),
          'name'      => 'group_name',
          'required'  => true,
      	  'value'     =>$group_name
      ));
      $fieldset->addField('limit_day', 'text', array(
           	'label' => Mage::helper('affiliate')->__('Maximum number of days affiliate will earn commission from new registered customer (referral) and new referred customer will receive discount'),
           	'name'  => 'limit_day',
      	    'required'  => true,
       		'note' => Mage::helper('affiliate')->__('For unregistered customers go to Affiliate Pro - Configuration-General-Referred and Unregistered Customer...'),
       		'class' => 'validate-digits',
       	));
       $fieldset->addField('limit_order', 'text', array(
           	'label' => Mage::helper('affiliate')->__('Maximum number of orders affiliate will earn commission from new referral and new referral will receive discount'),
           	'name'  => 'limit_order',
      	    'required'  => true,
       		'note' => Mage::helper('affiliate')->__('Insert 0 if no limitation.'),
       		'class' => 'validate-digits',
       	));
       	$fieldset->addField('limit_commission', 'text', array(
           	'label' => Mage::helper('affiliate')->__('Maximum commission affiliate can earn from each referral'),
           	'name'  => 'limit_commission',
      	    'required'  => true,
       		'note' => Mage::helper('affiliate')->__('Insert 0 if no limitation.'),
       		'class' => 'validate-digits',
       	));
	   
       //$form_member_detail->getElement('referral_name')->setValue(1);
      if ( Mage::getSingleton('adminhtml/session')->getAffiliateDataGroup() )
      {
          $form_group_detail->setValues(Mage::getSingleton('adminhtml/session')->getAffiliateDataGroup());
          Mage::getSingleton('adminhtml/session')->setAffiliateData(null);
      } elseif ( Mage::registry('affiliate_data_group') ) {
          $form_group_detail->setValues(Mage::registry('affiliate_data_group')->getData());
      }
      return parent::_prepareForm();
  }

}