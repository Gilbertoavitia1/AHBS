<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Credittransaction extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) 
    {
    	if(empty($row['credit_history_id'])) {
    		return '';
    	}
		$credithistory = Mage::getModel('credit/credithistory')->load($row['credit_history_id']);
    	$transactionDetail = MW_Credit_Model_Transactiontype::getTransactionDetail($credithistory->getTypeTransaction(),$credithistory->getTransactionDetail(),true); 
    	return $transactionDetail;
    }

}