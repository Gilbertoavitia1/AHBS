<?php
class MW_Affiliate_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{

    protected function _initTotals()
    {
		parent::_initTotals();
    	$affiliate_credit = Mage::getModel('credit/creditorder')->load($this->getOrder()->getIncrementId());
    	$affiliate = $affiliate_credit->getAffiliate();
    	$credit = $affiliate_credit ->getCredit();
    	$baseCurrencyCode = Mage::getModel('sales/order')->loadByIncrementId($this->getOrder()->getIncrementId())->getData('base_currency_code');
    	$currentCurrencyCode = Mage::getModel('sales/order')->loadByIncrementId($this->getOrder()->getIncrementId())->getData('order_currency_code');
    	
    	$affiliate_show = Mage::helper('directory')-> currencyConvert($affiliate,$baseCurrencyCode, $currentCurrencyCode);
    	$credit_show = Mage::helper('directory')-> currencyConvert($credit,$baseCurrencyCode, $currentCurrencyCode);
    	if($affiliate > 0)
    	{
            $this->_totals['affiliate_discount'] = new Varien_Object(array(
                'code'      => 'affiliate_discount',
                'value'     => -$affiliate_show,
                'base_value'=> -$affiliate,
                'label'     => Mage::helper('affiliate')->__('Affiliate Discount')
            ));
    	}
    	if($credit > 0)
    	{	
            $this->_totals['credit_discount'] = new Varien_Object(array(
                'code'      => 'credit_discount',
                'value'     => -$credit_show,
                'base_value'=> -$credit,
                'label'     => Mage::helper('credit')->__('Credit Discount')
            ));
    	}
        return $this;
    }
}
