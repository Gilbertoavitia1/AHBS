<?php
class MW_Affiliate_Model_Order_Invoice_Total_Affiliate extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
//	public function __construct(){
//        $this->setCode('affiliate');
//    }
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
		$order = $invoice->getOrder();

        //$affiliate = Mage::getModel('credit/creditorder')->load($order->getIncrementId())->getAffiliate();
        $affiliate = 0;
		//$credit = Mage::getModel('credit/creditorder')->load($order->getIncrementId())->getCredit();
		$credit = 0;
        $totalDiscountAmount     = $affiliate + $credit;
        $baseTotalDiscountAmount = $affiliate + $credit;
        

        $items = $invoice->getAllItems();
    	if (!count($items)) {
            return $this;
        }
        $invoice->setBaseDiscountAmount($invoice->getBaseDiscountAmount()- $baseTotalDiscountAmount);

        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);
        
        return $this;
    }


}
