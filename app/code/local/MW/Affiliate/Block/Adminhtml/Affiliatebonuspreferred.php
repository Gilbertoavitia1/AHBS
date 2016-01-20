<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebonuspreferred extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_affiliatebonuspreferred';
        $this->_blockGroup = 'affiliate';
        $this->_headerText = Mage::helper('affiliate')->__('Affiliate Bonus: Preferred Customer');

        parent::__construct();
        $this->_removeButton('add');
    }
}