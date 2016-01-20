<?php

/**
 * Paypal expess checkout shortcut link
 */
class Ebcomm_PaypalMx_Block_Debito2_Shortcut extends Mage_Core_Block_Template
{
    /**
     * Position of "OR" label against shortcut
     */
    const POSITION_BEFORE = 'before';
    const POSITION_AFTER = 'after';

    /**
     * Whether the block should be eventually rendered
     *
     * @var bool
     */
    protected $_shouldRender = true;

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_paymentMethodCode = Ebcomm_PaypalMx_Model_Config::METHOD_PPMX_DEBITO;

    /**
     * Start express action
     *
     * @var string
     */
    protected $_startAction = 'paypalmx/express/start';

    /**
     * Express checkout model factory name
     *
     * @var string
     */
    protected $_checkoutType = 'paypalmx/express_checkout';

    protected function _beforeToHtml()
    {
        $result = parent::_beforeToHtml();
        $config = Mage::getModel('paypalmx/config', array($this->_paymentMethodCode));
        $isInCatalog = $this->getIsInCatalogProduct();
        $isInCart = $this->getIsInCart();
        $quote = ($isInCatalog || '' == $this->getIsQuoteAllowed())
            ? null : Mage::getSingleton('checkout/session')->getQuote();

        // check visibility on cart, product page or shiping
        $context = ($isInCatalog) ? 'visible_on_product' : (($isInCart) ? 'visible_on_cart' : 'visible_on_shipping');
       
		
        if ($isInCatalog) {
            // Show PayPal shortcut on a product view page only if product has nonzero price
            /** @var $currentProduct Mage_Catalog_Model_Product */
            $currentProduct = Mage::registry('current_product');
            if (!is_null($currentProduct)) {
                $productPrice = (float)$currentProduct->getFinalPrice();
                if (empty($productPrice) && !$currentProduct->isGrouped()) {
                    $this->_shouldRender = false;
                    return $result;
                }
            }
        }
        
        // validate minimum quote amount and validate quote for zero grandtotal
        if (null !== $quote && (!$quote->validateMinimumAmount()
            || (!$quote->getGrandTotal() && !$quote->hasNominalItems()))) {
            $this->_shouldRender = false;
            return $result;
        }

        // check payment method availability
        $methodInstance = Mage::helper('payment')->getMethodInstance($this->_paymentMethodCode);
        if (!$methodInstance || !$methodInstance->isContextAvailable($context,$this->_paymentMethodCode) ){
        	 
        	$this->_shouldRender = false;
        
        	return $result;
        }
        
        if (!$methodInstance || !$methodInstance->isAvailable($quote)) {
            $this->_shouldRender = false;
            return $result;
        }

        // set misc data
        $this->setShortcutHtmlId($this->helper('core')->uniqHash('ec_shortcut_'))
            ->setCheckoutUrl($this->getUrl($this->_startAction))
        ;

        // use static image if in catalog
        if ($isInCatalog || null === $quote) {
            $this->setImageUrl($config->getExpressCheckoutShortcutImageUrl(Mage::app()->getLocale()->getLocaleCode()));
        } else {
            $this->setImageUrl(Mage::getModel($this->_checkoutType, array(
                'quote'  => $quote,
                'config' => $config,
            ))->getCheckoutShortcutImageUrl());
        }

        // ask whether to create a billing agreement
        $customerId = Mage::getSingleton('customer/session')->getCustomerId(); // potential issue for caching
        if (Mage::helper('paypalmx')->shouldAskToCreateBillingAgreement($config, $customerId)) {
            $this->setConfirmationUrl($this->getUrl($this->_startAction,
                array(Ebcomm_PaypalMx_Model_Debito2_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT => 1)
            ));
            $this->setConfirmationMessage(Mage::helper('paypalmx')->__('Would you like to sign a billing agreement to streamline further purchases with PayPal?'));
        }

        return $result;
    }

    /**
     * Render the block if needed
     *
     * @return string
     */
    protected function _toHtml()
    {
    	
        if (!$this->_shouldRender) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Check is "OR" label position before shortcut
     *
     * @return bool
     */
    public function isOrPositionBefore()
    {
        return ($this->getIsInCatalogProduct() && !$this->getShowOrPosition())
            || ($this->getShowOrPosition() && $this->getShowOrPosition() == self::POSITION_BEFORE);

    }

    /**
     * Check is "OR" label position after shortcut
     *
     * @return bool
     */
    public function isOrPositionAfter()
    {
        return (!$this->getIsInCatalogProduct() && !$this->getShowOrPosition())
            || ($this->getShowOrPosition() && $this->getShowOrPosition() == self::POSITION_AFTER);
    }
}
