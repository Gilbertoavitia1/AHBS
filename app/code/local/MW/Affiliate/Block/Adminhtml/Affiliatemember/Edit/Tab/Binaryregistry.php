<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Binaryregistry extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form_credit = new Varien_Data_Form();
      	$this->setForm($form_credit);
      	$fieldset = $form_credit->addFieldset('affiliate_form_credit', 
                array('legend'=>Mage::helper('affiliate')->__('Binary Registry')));

        $affiliate_val = Mage::registry('affiliate_data_member');
       	$fieldset->addField('left_leg', 'text', array(
           	'label' => Mage::helper('affiliate')->__('Left Leg'),
           	'name'  => 'left_leg',
                'value' => $affiliate_val->getLeftLeg()
       	));
       	
       	$fieldset->addField('right_leg', 'text', array(
           	'label' => Mage::helper('affiliate')->__('Right Leg'),
           	'name'  => 'right_leg',
                'value' => $affiliate_val->getRightLeg()
       	));       	
        
        return parent::_prepareForm();
    }
}
