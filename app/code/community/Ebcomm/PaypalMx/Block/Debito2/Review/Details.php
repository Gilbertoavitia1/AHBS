<?php
class Ebcomm_PaypalMx_Block_Debito2_Review_Details extends Mage_Checkout_Block_Cart_Totals
{
    protected $_address;

    /**
     * Return review shipping address
     *
     * @return Mage_Sales_Model_Order_Address
     */
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    /**
     * Return review quote totals
     *
     * @return array
     */
    public function getTotals()
    {
        return $this->getQuote()->getTotals();
    }
}
