<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebinaryresidual_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('binaryresidual_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('affiliate')->__('Binary Residual'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_Bannerslider_Block_Adminhtml_Bannerslider_Edit_Tabs
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('affiliate')->__('Information'),
            'title' => Mage::helper('affiliate')->__('Information'),
            'content' => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebinaryresidual_edit_tab_form')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }

}