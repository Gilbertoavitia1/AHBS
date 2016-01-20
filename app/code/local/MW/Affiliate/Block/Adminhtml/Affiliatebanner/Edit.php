<?php

class MW_Affiliate_Block_Adminhtml_Affiliatebanner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'affiliate_banner_id';
        $this->_blockGroup = 'affiliate';
        $this->_controller = 'adminhtml_affiliatebanner';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('affiliate')->__('Delete Banner'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('affiliate_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'affiliate_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'affiliate_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('affiliate_data_banner') && Mage::registry('affiliate_data_banner')->getId() ) {
            return Mage::helper('affiliate')->__("Edit Banner '%s'", $this->htmlEscape(Mage::registry('affiliate_data_banner')->getTitleBanner()));
        } else {
            return Mage::helper('affiliate')->__('Add Banner');
        }
    }
}