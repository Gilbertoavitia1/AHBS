<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Invitationpurchasetotal extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['status'])) {
    		return '';
    	}
    	
    	if($row['status'] == MW_Affiliate_Model_Statusinvitation::PURCHASE) {
			$orderId = $row['order_id'];
			$collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()->addFieldToFilter('order_id', array('eq' => $orderId));
			
			$totalPurchase = 0;
			foreach($collection as $item) {
				$totalPurchase += $item->getTotalAmount();
			}
			return Mage::helper('core')->currency($totalPurchase,true,false);
            //return Mage::helper('affiliate')->formatMoney($totalPurchase);
        }else{
        	return Mage::helper('core')->currency(0,true,false);
        }
    }

}