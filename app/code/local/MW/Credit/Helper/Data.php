<?php

class MW_Credit_Helper_Data extends Mage_Core_Helper_Abstract
{	
	public function getMaxCreditToCheckOut()
	{
		if(Mage::getStoreConfig('credit/options/max_credit_to_checkout'))
			return Mage::getStoreConfig('credit/options/max_credit_to_checkout');
		return 0;
	}
	public function allowCreditToCheckout()
	{
		return Mage::getStoreConfig('credit/options/allow_using_credit_to_checkout');
	}
	public function allowSendCreditToFriend()
	{
		return Mage::getStoreConfig('credit/options_send_credit/allow_send_credit_to_friend');
	}
	public function getMaxRecipients()
    {
        if(Mage::getStoreConfig('credit/options_send_credit/max_recipients'))
			return Mage::getStoreConfig('credit/options_send_credit/max_recipients');
		return 3;
    }
	public function getMaxCreditToSend()
	{
		if(Mage::getStoreConfig('credit/options_send_credit/max_credit_to_send'))
			return Mage::getStoreConfig('credit/options_send_credit/max_credit_to_send');
		return 0;
	}
	
	public function formatMoney($money)
	{
		return Mage::helper('core')->currency($money);
	}
    public function getCreditByCheckout()
    {
    	return Mage::getSingleton('checkout/session')->getCredit();
    	//return Mage::getSingleton('checkout/session')->getQuote()->getCredit();
    	
    }
	public function getCreditByOrder($order)
	{
		return Mage::getModel('credit/creditorder')->load($order->getId())->getCredit();
	}
	public function getCreditByCustomer()
	{
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		return Mage::getModel('credit/creditcustomer')->load($customer_id)->getCredit();
	}
	public function getSizeCreditHistoryByCustomer()
	{
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		$collection = Mage::getModel('credit/credithistory')->getCollection()->addFieldToFilter('customer_id',$customer_id);
		return sizeof($collection);
	}
  
	public function getEmailByCustomer()
	{
		$customer = Mage::getSingleton("customer/session")->getCustomer();
		return $customer->getEmail();
	}
	public function getCurrency()
	{
		return Mage::app()->getBaseCurrencyCode();
	}
	public function formatNumber($value)
    {
    	return number_format($value,0,'.',',');
    }
	public function getCurrencyMoney($value)
	{
		return $value." ".$this->getCurrency();
	}
	public function getCreditByCustomerWithdraw($customerid)
	{
		return Mage::getModel('credit/creditcustomer')->load($customerid)->getCredit();
	}
	
}