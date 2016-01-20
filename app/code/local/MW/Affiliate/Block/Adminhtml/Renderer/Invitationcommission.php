<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Invitationcommission extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['status'])) {
    		return '';
    	}
    	
    	if($row['status'] == MW_Affiliate_Model_Statusinvitation::PURCHASE) {
            $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()->addFieldToFilter('order_id', array('eq' => $row['order_id']));
            $totalCommission = 0;
            foreach($collection as $item) {
            	$totalCommission += $item->getHistoryCommission();
            }
            
            return Mage::helper('core')->currency($totalCommission,true,false);
        }else{
        	return Mage::helper('core')->currency($row['commission'],true,false);
        }
    }

}