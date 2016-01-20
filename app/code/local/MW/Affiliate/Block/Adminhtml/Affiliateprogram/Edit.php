<?php

class MW_Affiliate_Block_Adminhtml_Affiliateprogram_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'program_id';
        $this->_blockGroup = 'affiliate';
        $this->_controller = 'adminhtml_affiliateprogram';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Program'));
        $this->_updateButton('delete', 'label', Mage::helper('affiliate')->__('Delete Program'));
		
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
        if( Mage::registry('affiliate_data_program') && Mage::registry('affiliate_data_program')->getId() ) {
            return Mage::helper('affiliate')->__("Edit Program '%s'", $this->htmlEscape(Mage::registry('affiliate_data_program')->getProgramName()));
        } else {
            return Mage::helper('affiliate')->__('Add Program');
        }
    }
}