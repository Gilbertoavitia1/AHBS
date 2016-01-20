<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Invitationaction extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['status'])) {
    		return '';
    	}
    	
    	if($row['commission_type'] == MW_Credit_Model_Transactiontype::BUY_PRODUCT) {
			$url = Mage::helper('adminhtml')->getUrl('affiliate/adminhtml_affiliateviewhistory', array('orderid' => $row['order_id']));

			return '<a href="' . $url . '">' . Mage::helper('affiliate')->__('View') . '</a>';
        }
    }

}