<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebinarypreferred extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_affiliatebinarypreferred';
        $this->_blockGroup = 'affiliate';
        $this->_headerText = Mage::helper('affiliate')->__('Affiliate Binary: Preferred Customer');

        parent::__construct();
        $this->_removeButton('add');
        $this->_addButton('run_report', array(
            'label'   => Mage::helper('affiliate')->__('Run Report'),
            'onclick' => "setLocation('{$this->getUrl('affiliate/adminhtml_affiliatebinaryreport/preferred')}')",
            'class'   => 'run'
        ));
    }
}