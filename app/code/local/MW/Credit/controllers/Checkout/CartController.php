<?php
class MW_Credit_Checkout_CartController extends Mage_Core_Controller_Front_Action
{
	
    private function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    private function _goBack()
    {
    	return $this->_redirect('checkout/cart');
    }
    
    /**
     * 
     * @return int
     */
	private function _getCredit() 
	{
		$customerId = (int)Mage::getSingleton('customer/session')->getCustomer()->getId();
		return Mage::getSingleton('credit/creditcustomer')->load($customerId)->getCredit();	
	}
    
	/**
	 * Calculate if credit is gretter than grandTotal
	 * 
	 * @param $credit
	 * @return int
	 */
	private function _getAmountCredit($credit) 
	{   
		$addCredit  = $this->_getSession()->getCredit();
		//$addCredit  = $this->_getSession()->getQuote()->getCredit();
		$grandTotal = $this->_getSession()->getQuote()->getGrandTotal() + $addCredit;
		if ($credit > $grandTotal) {
			$credit = $grandTotal;
		}
		return $credit;
	}
	public function referralcodePostAction()
	{
		$referral_code = $this->getRequest()->getParam('code_value');
        if ($this->getRequest()->getParam('removeCode') == 1) {
        	Mage::getSingleton('checkout/session') ->unsetData('referral_code');
            $referral_code = '';
        }
        if($referral_code != ''){
        	$check = Mage::helper('affiliate') ->checkReferralCodeCart($referral_code);
        	if($check == 0){
        		$this->_getSession()->addError($this->__('The referral code is invalid.'));
    			$this->_goBack();
          		return;
        	}
        }
        
	  	try {
            if ($referral_code !='') {
            	$this->_getSession()->setReferralCode($referral_code); // set session
        		$this->_getSession()->addSuccess($this->__('The referral code was applied successfully.'));
            } else {
            	$this->_getSession()->addSuccess($this->__('The referral code has been cancelled successfully.'));
        	}

        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('Can not apply the referral code.'));
        }
		$this->_goBack();
	}
	public function creditPostAction()
    {
		$rate = Mage::helper('core')->currency(1,false);
    	// no login
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
        	$this->_getSession()->addError($this->__('You must login to use this function'));
			$this->_goBack();
          	return;
    	}
    	$customerId = (int)Mage::getSingleton('customer/session')->getCustomer()->getId();
//    	if(Mage::helper('affiliate')->getActiveAffiliate($customerId) == 0)
//    	{
//    		$this->_getSession()->addError($this->__('You must join our Affiliate to use this function'));
//			$this->_goBack();
//          	return;
//    	}
    	if(Mage::helper('affiliate')->getLockAffiliate($customerId) == 1)
    	{
    		$this->_getSession()->addError($this->__('Affiliate Account was disabled, please contact us to solve this problem.'));
			$this->_goBack();
          	return;
    	}
    
    	$MaxCredit=Mage::helper('credit')->getMaxCreditToCheckOut();
        $credit = $this->getRequest()->getParam('credit_value');
        $credit = abs($credit);
        $credit = $credit/$rate; // convert sang gia base
        if ($this->getRequest()->getParam('removeCredit') == 1) {
            $credit = '';
            $this->_getSession()->setCredit($credit);
            $this->_getSession()->addSuccess($this->__('The credit has cancelled successfully.'));
            $this->_goBack();
          	return;
            
        }
        //$credit = $this->_getAmountCredit($credit);
		$grandTotal = $this->_getSession()->getQuote()->getGrandTotal();
		$quote = $this->_getSession()->getQuote();
		$address = $quote->isVirtual()?$quote->getBillingAddress():$quote->getShippingAddress();
		$subtotal = $address->getBaseSubtotal();
		$subtotal += $address->getBaseDiscountAmount() + $this->_getSession()->getCredit();
		if ($credit > $subtotal) {
			//$credit = $grandTotal;
			$this->_getSession()->addError(
            	$this->__('Your entered amount (%s) is greater than subtotal.', Mage::helper('core')->currency($credit,true,false))
          	);
          	$this->_goBack();
          	return;
		}
        
        // max credit to checkout
        if(	$MaxCredit < $credit && $MaxCredit != 0 ){
        		
        	$this->_getSession()->addError(
        		$this->__('Maximum amount of credit to checkout is "%s". Please insert a number that must be less than or equal "%s"',Mage::helper('core')->currency($MaxCredit,true,false),Mage::helper('core')->currency($MaxCredit,true,false))
        	);	
        	$this->_goBack();
          	return;
        }
        
        // if credit is gretter than credit customer
        if ($credit > $this->_getCredit()) {
      		$this->_getSession()->addError(
            	$this->__('Your balance is not enough "%s".', Mage::helper('core')->currency($credit,true,false))
          	);
          	$this->_goBack();
          	return;
        }
       
        try {
            $this->_getSession()->setCredit($credit); // set session
            //$this->_getSession()->getQuote()->setCredit($credit)->save(); // set session
            //$this->_getSession()->getQuote()->collectTotals()->save();
            //echo $this->_getSession()->getQuote()->getCredit();die();
            if ($credit) {
        		$this->_getSession()->addSuccess(
            		$this->__('Credit "%s" was applied successfully.', Mage::helper('core')->currency($credit,true,false))
        		);
            } else {
            	$this->_getSession()->addSuccess($this->__('The credit has cancelled successfully.'));
        	}

        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('Can not apply credit.'));
        }

        $this->_goBack();
    }
}
?>