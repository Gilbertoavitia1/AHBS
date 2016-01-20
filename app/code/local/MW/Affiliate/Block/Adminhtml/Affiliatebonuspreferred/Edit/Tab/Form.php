<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebonuspreferred_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('preferred_form', 
                array('legend' => Mage::helper('affiliate')->__('Description')));
        
        
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('affiliate')->__('Affiliate Parent Sponsor'),
            'name'      => 'title',
        ));
        
        
        $fieldset->addField('start_date', 'text', array(
            'label'     => Mage::helper('affiliate')->__('Start Date'),
            'name'      => 'start_date',
        ));
        
        $fieldset->addField('end_date', 'text', array(
            'label'     => Mage::helper('affiliate')->__('End Date'),
            'name'      => 'end_date',
        ));
        
        $fieldset->addField('total', 'text', array(
            'label'     => Mage::helper('affiliate')->__('Total'),
            'name'      => 'total',
        ));
        
        $fieldset->addField('currency', 'text', array(
            'label'     => Mage::helper('affiliate')->__('Currency'),
            'name'      => 'currency',
        ));
        
        $fieldset->addField('currencdy', 'textarea', array(
            'label'     => Mage::helper('affiliate')->__('Currency'),
            'name'      => 'currencyd',
        ));
        

        
//        $form->setValues($data);
        return parent::_prepareForm();
    }

}