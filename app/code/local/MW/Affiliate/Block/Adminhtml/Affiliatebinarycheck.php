<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebinarycheck extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_affiliatebinarycheck';
        $this->_blockGroup = 'affiliate';
        $this->_headerText = Mage::helper('affiliate')->__('Affiliate Binary: Igualable Check');

        parent::__construct();
        $this->_removeButton('add');        
        $this->_addButton('run_report', array(
            'label'   => Mage::helper('affiliate')->__('Run Report'),
            'onclick' => "setLocation('{$this->getUrl('affiliate/adminhtml_affiliatebinaryreport/check')}')",
            'class'   => 'run'
        ));
    }
}