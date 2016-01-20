<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Invitationdiscount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['status'])) {
    		return '';
    	}
    	
    	if($row['status'] == MW_Affiliate_Model_Statusinvitation::PURCHASE) {
			$orderId = $row['order_id'];
			$collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()->addFieldToFilter('order_id', array('eq' => $orderId));
			$totalDiscount = 0;
			foreach($collection as $item) {
				$totalDiscount += $item->getHistoryDiscount();
			}
			return Mage::helper('core')->currency($totalDiscount,true,false);
            //return Mage::helper('affiliate')->formatMoney($totalDiscount);
        }else{
        	return Mage::helper('core')->currency(0,true,false);
        }
    }

}