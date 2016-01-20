<?php
class MW_Affiliate_Block_Sales_Order_Invoice_Totals extends Mage_Sales_Block_Order_Invoice_Totals
{

	protected function _initTotals()
    {
		parent::_initTotals();
    	$affiliate_credit = Mage::getModel('credit/creditorder')->load($this->getOrder()->getIncrementId());
    	//$affiliate = $affiliate_credit->getAffiliate();
    	$affiliate = 0;
    	//$credit = $affiliate_credit ->getCredit();
    	$credit = 0;
    	if($affiliate > 0)
    	{
            //$this->_totals['affiliate_discount'] = new Varien_Object(array(
              $total = new Varien_Object(array(
                'code'      => 'affiliate_discount',
                'value'     => -$affiliate,
                'base_value'=> -$affiliate,
                'label'     => 'Affiliate Discount'
            ));
            $this->addTotal($total);
    	}
    	if($credit > 0)
    	{
            //$this->_totals['credit_discount'] = new Varien_Object(array(
              $total = new Varien_Object(array(
                'code'      => 'credit_discount',
                'value'     => -$credit,
                'base_value'=> -$credit,
                'label'     => 'Credit Discount'
            ));
            $this->addTotal($total);
    	}
        return $this;
    }
}
