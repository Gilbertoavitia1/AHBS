<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Invitationstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['status'])) {
    		return '';
    	}
    	
    	if($row['status'] == MW_Affiliate_Model_Statusinvitation::PURCHASE) {
    		$orderId = $row['order_id'];
			$collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()->addFieldToFilter('order_id', array('eq' => $orderId));
			foreach($collection as $item) {
				$orderStatus = $item->getStatus();
				break;
			}
            return MW_Affiliate_Model_Status::getLabel($orderStatus);
    	} else {
    		return Mage::helper('affiliate')->__('Completed');
    	}
    }

}