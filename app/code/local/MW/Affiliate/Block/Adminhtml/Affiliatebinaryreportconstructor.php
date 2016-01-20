<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebinaryreportconstructor extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $range = Mage::helper('affiliate_binary')->getRangeDate();
        $this->_controller = 'adminhtml_affiliatebinaryreportconstructor';
        $this->_blockGroup = 'affiliate';
        $this->_headerText = Mage::helper('affiliate')->__('Affiliate Binary Report Constructor') . ':  '.$range[0] . ' a '. $range[1];

        parent::__construct();
        $this->_removeButton('add');
    }
}