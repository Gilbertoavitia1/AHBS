<?php
class Mage_Firstdatae4_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'firstdatae4_standard';
    protected $_formBlockType = 'firstdatae4/standard_form';
    protected $_allowCurrencyCode = array('AUD', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'JPY', 'NOK', 'NZD', 'PLN', 'SEK', 'SGD','USD');

	// This payment method doesn't support capture later, void or refund
	protected $_authorize				= '';
	protected $_isGateway               = true;
	protected $_canAuthorize            = true;
    protected $_canCapture              = false;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc 				= false;

    public function getSession()
    {
        return Mage::getSingleton('firstdatae4/session');
    }

    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function canUseInternal()
    {
        return false;
    }

    public function canUseForMultishipping()
    {
        return false;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('firstdatae4/standard_form', $name)
            ->setMethod('firstdatae4_standard')
            ->setPayment($this->getPayment())
            ->setTemplate('firstdatae4/standard/form.phtml');

        return $block;
    }

    /* validate the currency code is avaialable to use for firstdatae4 or not */
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode() ? $this->getQuote()->getBaseCurrencyCode() : 'USD';
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('firstdatae4')->__('Selected currency code ('.$currency_code.') is not compatabile with FirstdataE4'));
        }
        return $this;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
       return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }

    public function canCapture()
    {
        return $this->_canCapture;
    }
    public function canRefund()
    {
        return $this->_canRefund;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('firstdatae4/standard/redirect', array('_secure' => true));
    }
	
    public function getSuccessUrl()
    {
          return Mage::getUrl('firstdatae4/standard/success', array('_secure' => true));
    }	

	// The fields that you set here are acces in Redirect.php
	// and can be sent to the external payment gateway if needed.
	// All the fields that are entered by the user on the checkout page can be accessed here.
    public function getStandardCheckoutFormFields()
    {
        // This is for Magento ver 1.4.0.1 and below.
		$quote 			= $this->getQuote();
		$currency_code 	= $quote->getBaseCurrencyCode();
        $isQuoteVirtual = $quote->getIsVirtual();
        $address 		= $isQuoteVirtual ? $quote->getBillingAddress() : $quote->getShippingAddress();
		$shipping 		= $isQuoteVirtual ? $quote->getShippingAddress() : $quote->getBillingAddress();
		$customer_email	= $address->getEmail();
		
		// This is for Magento ver 1.4.1.1
		if ($address->getFirstname() == '') {			
			$orderIncrementId = $this->getCheckout()->getLastRealOrderId();
			$quote 			= Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
			$currency_code 	= $quote->getBaseCurrencyCode();
			$isQuoteVirtual = $quote->getIsVirtual();
			$address 		= $isQuoteVirtual ? $quote->getBillingAddress() : $quote->getShippingAddress();
			$shipping 		= $isQuoteVirtual ? $quote->getShippingAddress() : $quote->getBillingAddress();
			$customer_email	= $quote->getCustomerEmail();
		}
		
		if (!isset($shipping) || empty($shipping)) {
			$shipping = $address;
		}
		
		$payment_action = $this->getConfigData('payment_action');
		switch ($payment_action) {
			case 'authorize_capture':
				$payment_action = 'AUTH_CAPTURE';
				break;
			default:
				$payment_action = 'AUTH_ONLY';
		}
		
		$sArr = array(
			'store_id'       => $this->getConfigData('store_id'),
			'shared_secret'  => $this->getConfigData('shared_secret'),
			'sequence'  	 => time()+1,
			'testmode'	 	 => $this->getConfigData('testmode') ? 'TRUE' : 'FALSE',
			'payment_action' => $payment_action,
			'email_customer' => $this->getConfigData('email_cust') ? 'TRUE' : 'FALSE',
			'merchant_email' => $this->getConfigData('merchant_email'),
			
			'invoice'           => $this->getCheckout()->getLastRealOrderId(),
			'currency_code'     => $currency_code,
			'address_override'  => 1,

			// billing address
			'first_name'        => $address->getFirstname(),
			'last_name'         => $address->getLastname(),
			'address1'          => $address->getStreet(1),
			'address2'          => $address->getStreet(2),
			'city'              => $address->getCity(),
			'state'             => $address->getRegionCode(),
			'country'           => $address->getCountry(),
			'zip'               => $address->getPostcode(),
			'telephone'         => $address->getTelephone(),

			// shipping address
			's_first_name'        => $shipping->getFirstname(),
			's_last_name'         => $shipping->getLastname(),
			's_address1'          => $shipping->getStreet(1),
			's_address2'          => $shipping->getStreet(2),
			's_city'              => $shipping->getCity(),
			's_state'             => $shipping->getRegionCode(),
			's_country'           => $shipping->getCountry(),
			's_zip'               => $shipping->getPostcode(),
			's_telephone'         => $shipping->getTelephone(),

			'email'             => $customer_email,
		);
		
		$final_amount = 0;
		$final_tax = 0;
		$items = $quote->getAllItems();
		if ($items) {
			$i = 1;
			foreach ($items as $item) {
				if ($item->getParentItem()) {
					continue;
				}
				//echo "<pre>"; print_r($item->getData()); echo"</pre>";
				if ($item->getQty() == '') {
					// Magento ver 1.4.1.1
					
					$item_tax = 0;
					if ($item->getBaseTaxAmount() > 0){
						$item_tax = $item->getBaseTaxAmount()/$item->getQtyOrdered();
					}			
					$individual_discount = $item->getBaseDiscountAmount() / $item->getQtyOrdered(); // we divide because the base discount amount is for all the quantity together
					$sArr = array_merge($sArr, array(
						'item_name_'.$i    		=> $item->getName(),
						'item_number_'.$i  		=> $item->getSku(),
						'quantity_'.$i      	=> sprintf('%d', $item->getQtyOrdered()),
						//'amount_'.$i      	=> sprintf('%.2f', ($item->getBasePrice() - $item->getBaseDiscountAmount()) + $item_tax),
						//'base_price_'.$i   	=> sprintf('%.2f', ($item->getBasePrice() - $item->getBaseDiscountAmount())),
						'individual_discount_'.$i	=> sprintf('%.2f', $individual_discount),
						'amount_'.$i      		=> sprintf('%.2f', ($item->getBasePrice() - $individual_discount) + $item_tax),
						'base_price_'.$i   		=> sprintf('%.2f', ($item->getBasePrice() - $individual_discount)),
						'individual_total_'.$i  => sprintf('%.2f', ((($item->getBasePrice() - $individual_discount) + $item_tax) * $item->getQtyOrdered())),
						'tax_'.$i   			=> sprintf('%.2f', $item_tax),
					));
					//$final_amount 	+= (($item->getBasePrice() - $item->getBaseDiscountAmount()) + $item_tax) * $item->getQtyOrdered();
					$final_amount 	+= (($item->getBasePrice() - $individual_discount) + $item_tax) * $item->getQtyOrdered();
					$final_tax 		+= $item_tax * $item->getQtyOrdered();
					$final_discount	+= $individual_discount * $item->getQtyOrdered();
				} else {
					$item_tax = 0;
					$sree_item_discount = $item->getBaseDiscountAmount();
					if ($item->getBaseTaxAmount() > 0){
						$item_tax = $item->getBaseTaxAmount()/$item->getQty();
					}
					$individual_discount = $item->getBaseDiscountAmount() / $item->getQty();
					$sArr = array_merge($sArr, array(
						'item_name_'.$i    		=> $item->getName(),
						'item_number_'.$i  		=> $item->getSku(),
						'quantity_'.$i      	=> sprintf('%d', $item->getQty()),
						//'amount_'.$i      	=> sprintf('%.2f', ($item->getBaseCalculationPrice() - $individual_discount) + $item_tax),
						//'base_price_'.$i   	=> sprintf('%.2f', ($item->getBaseCalculationPrice() - $item->getBaseDiscountAmount())),
						'individual_discount_'.$i	=> sprintf('%.2f', $individual_discount),
						'amount_'.$i      		=> sprintf('%.2f', ($item->getBaseCalculationPrice() - $item->getBaseDiscountAmount()) + $item_tax),
						'base_price_'.$i   		=> sprintf('%.2f', ($item->getBaseCalculationPrice() - $individual_discount)),
						'individual_total_'.$i  => sprintf('%.2f', ((($item->getBaseCalculationPrice() - $individual_discount) + $item_tax) * $item->getQty())),
						'tax_'.$i   			=> sprintf('%.2f', $item_tax),
					));
					//$final_amount += (($item->getBaseCalculationPrice() - $item->getBaseDiscountAmount()) + $item_tax) * $item->getQty();
					$final_amount   += (($item->getBaseCalculationPrice() - $individual_discount) + $item_tax) * $item->getQty();
					$final_tax 	    += $item_tax * $item->getQtyOrdered();
					$final_discount	+= $individual_discount * $item->getQtyOrdered();
				}
				$i++;
			}
		}
		
		// versions before 1.5 use $address and after use quote
		$shippingDesc = $address->getShippingDescription();
		if (empty($shippingDesc)) {
			$shippingDesc = $quote->getShippingDescription();
		}
		
		// Shipping amounts
		$sArr = array_merge($sArr, array(
			'item_name_'.$i   	=> $shippingDesc,
			'quantity_'.$i    	=> 1,
			'amount_'.$i     	=> sprintf('%.2f',($quote->getShippingAmount() + $quote->getShippingTaxAmount())),
			'base_price_'.$i    => sprintf('%.2f',$quote->getBaseShippingAmount()),
			'tax_'.$i         	=> sprintf('%.2f',$quote->getBaseShippingTaxAmount()),
		));
		$i++;
		
		$sArr['shipping_amount'] = $address->getShippingAmount() + $address->getShippingTaxAmount();
		if ($sArr['shipping_amount'] <= 0) {
			$sArr['shipping_amount'] = $quote->getShippingAmount() + $quote->getShippingTaxAmount();
		}		
		$sArr['subtotal_amount'] 	= sprintf('%.2f', $final_amount);
		$sArr['tax_amount'] 		= sprintf('%.2f', $final_tax);
		$sArr['discount_amount']	= sprintf('%.2f', $final_discount);
		$sArr['grandtotal_amount'] 	= sprintf('%.2f', ($sArr['shipping_amount'] + $sArr['subtotal_amount']));

		//echo '<pre>' . print_r($sArr, 1) . '</pre>';exit;
        return $sArr;
    }

    public function getFirstdatae4Url()
    {
		$testmode = $this->getConfigData('testmode');
		if ($testmode) {
			//$url = "https://connect.merchanttest.firstdataglobalgateway.com/IPGConnect/gateway/processing";
			$url = "https://demo.globalgatewaye4.firstdata.com/payment";
		} else {
			//$url = "https://connect.firstdataglobalgateway.com/IPGConnect/gateway/processing";
			$url = "https://checkout.globalgatewaye4.firstdata.com/payment";
		}
        return $url;
    }
    
    public function getFirstdatae4ReportUrl()
    {
		$testmode = $this->getConfigData('testmode');
		if ($testmode) {
			$url = "https://api.demo.globalgatewaye4.firstdata.com/transaction/search";
		} else {
			//$url = "https://connect.firstdataglobalgateway.com/IPGConnect/gateway/processing";
			$url = "https://api.globalgatewaye4.firstdata.com/transaction/search";
		}
        return $url;
    }

    public function getDebug()
    {
        return Mage::getStoreConfig('firstdatae4/wps/debug_flag');
    }

    public function isInitializeNeeded()
    {
        return true;
    }

    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }
}