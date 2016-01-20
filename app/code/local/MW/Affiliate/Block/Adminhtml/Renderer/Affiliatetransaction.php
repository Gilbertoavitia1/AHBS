<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Affiliatetransaction extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) 
    {
    	if(empty($row['history_id'])) {
    		return '';
    	}
		$affiliateTransaction = Mage::getModel('affiliate/affiliatetransaction')->load($row['history_id']);
		$type = $affiliateTransaction->getCommissionType();
		$detail = $affiliateTransaction->getOrderId();
		if($type == MW_Credit_Model_Transactiontype::REFERRAL_VISITOR || $type == MW_Credit_Model_Transactiontype::REFERRAL_SIGNUP || $type == MW_Credit_Model_Transactiontype::REFERRAL_SUBSCRIBE )
		$detail = $affiliateTransaction->getCustomerId();
		
    	$transactionDetail = Mage::getModel('credit/transactiontype')->getTransactionDetail($type,$detail,true); 
    	return $transactionDetail;
    }

}