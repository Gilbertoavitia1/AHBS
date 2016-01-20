<?php
class MW_Affiliate_Model_Order_Creditmemo_Total_Affiliate extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {

        $order = $creditmemo->getOrder();

        //$affiliate = Mage::getModel('credit/creditorder')->load($order->getIncrementId())->getAffiliate();
        $affiliate = 0;
		//$credit = Mage::getModel('credit/creditorder')->load($order->getIncrementId())->getCredit();
		$credit = 0;
        $totalDiscountAmount     = $affiliate + $credit;
        $baseTotalDiscountAmount = $affiliate + $credit;
		
    	$items = $creditmemo->getAllItems();
    	if (!count($items)) {
            return $this;
        }
        $creditmemo->setBaseDiscountAmount($creditmemo->getBaseDiscountAmount() - $baseTotalDiscountAmount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);
        return $this;
    }
}
