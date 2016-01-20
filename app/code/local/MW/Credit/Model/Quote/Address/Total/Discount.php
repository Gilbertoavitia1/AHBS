<?php

//class MW_Credit_Model_Quote_Address_Total_Discount extends Mage_Sales_Model_Quote_Address_Total_Discount
class MW_Credit_Model_Quote_Address_Total_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
 	public function __construct()
    {
        //parent::__construct();
        $this->setCode('credit_discount');
    }
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {   
    	parent::collect($address);
    	
        $quote = $address->getQuote();        
    	$items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }
        $customerId = (int)Mage::getSingleton('customer/session')->getCustomer()->getId();
		$customer_credit =  Mage::getSingleton('credit/creditcustomer')->load($customerId)->getCredit();	
		if(Mage::getSingleton('checkout/session')->getCredit() > $customer_credit) Mage::getSingleton('checkout/session')->unsetData('credit');
		
		//echo Mage::getSingleton('checkout/session')->getCredit();die();
        $discountAmount_show = Mage::helper('core')->currency(Mage::getSingleton('checkout/session')->getCredit(),false,false);
        $discountAmount = Mage::getSingleton('checkout/session')->getCredit();
        //echo $discountAmount;die();
        $discountAmountTiem = $discountAmount;
        foreach ($items as $item) {
         	$qty = $item->getQty();
	    	$price = $item->getPrice();
	    	$itemPrice = ($qty * $price - $item->getBaseDiscountAmount());
	    	if($discountAmountTiem <= $itemPrice)
	    	{
	    		$item->setDiscountAmount($item->getDiscountAmount()+ Mage::helper('core')->currency($discountAmountTiem,false,false));
				$item->setBaseDiscountAmount($item->getBaseDiscountAmount() + $discountAmountTiem);
				$item->setMwCreditDiscount($discountAmountTiem);
				break;
	    	}else{
	    		$item->setDiscountAmount($item->getDiscountAmount()+ Mage::helper('core')->currency($itemPrice,false,false));
				$item->setBaseDiscountAmount($item->getBaseDiscountAmount() + $itemPrice);
				$item->setMwCreditDiscount($itemPrice);
				$discountAmountTiem = $discountAmountTiem - $itemPrice;
	    	};
	    	
         }
        $address->setBaseDiscountAmount($address->getBaseDiscountAmount() - $discountAmount);// gia de tinh toan
        $address->setCreditDiscount($discountAmount_show); // gia de hien thi
		$address->setBaseCreditDiscount($discountAmount);
        $address->setGrandTotal($address->getGrandTotal() - $address->getCreditDiscount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal()-$address->getBaseCreditDiscount());
		//Mage::dispatchEvent('sales_quote_address_discount_collect',array('address'=>$address));
        return $this;
    }
	public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getCreditDiscount();
        if ($amount!=0) {
            $title = Mage::helper('credit')->__('Credit Discount');
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>$title,
                'value'=>-$amount
            ));
        }
        return $this;
    }
	
}