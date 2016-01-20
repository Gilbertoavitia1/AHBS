<?php
/**
 * Store Pickup shipping method
 *
 * ParadoxLabs, Inc.
 * http://www.paradoxlabs.com
 * +1-717-431-3330
 *
 * Having a problem with the plugin?
 * Not sure what something means?
 * Need custom development?
 * Give us a call!
 *
 * @category ParadoxLabs
 * @package ParadoxLabs_StorePickup
 * @author Joseph Leedy <joseph@paradoxlabs.com>
 */
class ParadoxLabs_StorePickup_Model_Carrier_Pickup
	extends Mage_Shipping_Model_Carrier_Abstract
	implements Mage_Shipping_Model_Carrier_Interface
{
	/**
	 * unique internal shipping method identifier
	 *
	 * @var string [a-z0-9_]
	 */
	protected $_code = 'pl_store_pickup';

	/**
	 * Collect rates for this shipping method based on information in $request
	 *
	 * @param Mage_Shipping_Model_Rate_Request $request
	 *
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if (!$this->getConfigData('active')) {
			return false;
		}

		$subtotal     = $request->getBaseSubtotalInclTax();
		$handlingFee  = $this->getConfigData('handling_fee');
		$handlingType = $this->getConfigData('handling_type');
		$carrierTitle = $this->getConfigData('title');
		$methodTitle  = $this->getConfigData('method');
		$minAmount    = $this->getConfigData('min_amount');
		$maxAmount    = $this->getConfigData('max_amount');
		$locations    = $this->getConfigData('locations');
		$locations    = array_filter(explode("\n", $locations));
		$result       = Mage::getModel('shipping/rate_result');
		$total        = 0;

		Mage::log($total, null, 'store_pickup.log');
		Mage::log($minAmount, null, 'store_pickup.log');
		Mage::log($maxAmount, null, 'store_pickup.log');
		Mage::log((int)($total >= $minAmount && $total <= $maxAmount), null, 'store_pickup.log');
		Mage::log((int)($total >= $minAmount), null, 'store_pickup.log');
		Mage::log((int)($total <= $maxAmount), null, 'store_pickup.log');

		if (!empty($handlingFee) && $handlingFee > 0) {
			$total = $handlingType == 'P' ? round(($subtotal * $handlingFee) / 100, 2) : $handlingFee;
		/*} else {
			$total = $subtotal;*/
		}

		if (($this->_isCurrency($minAmount) && $this->_isCurrency($maxAmount)) && ($total >= $minAmount && $total <= $maxAmount)) {
			if (empty($locations)) {
				$result->append($this->_createMethod($carrierTitle, $methodTitle, $total));
			} else {
				foreach ($locations as $location) {
					$result->append($this->_createMethod($carrierTitle, $location, $total));
				}
			}
		} else {
			$result->append($this->_createMethod($carrierTitle, $this->getConfigData('specificerrmsg'), null, 'shipping/rate_result_error'));
		}

		return $result;
	}

	/**
	 * This method is used when viewing / listing Shipping Methods with Codes programmatically
	 */
	public function getAllowedMethods() {
		return array($this->_code => $this->getConfigData('name'));
	}

	/**
	 * Instantiates a new shipping method
	 *
	 * @param  string $carrierTitle The name of the carrier
	 * @param  string $methodTitle  The name of the method or error message
	 * @param  int    $total         The total dollar amount of the shipping cost
	 * @param  string $model         Which Magento model to use to create the shipping method. Can either be "shipping/rate_result_method" or "shipping/rate_result_error"
	 * @return object                The new shipping method
	 */
	private function _createMethod($carrierTitle, $methodTitle, $total, $model = 'shipping/rate_result_method') {
		$method = Mage::getModel($model);

		$method->setCarrier($this->_code);
		$method->setCarrierTitle($carrierTitle);

		if ($model == 'shipping/rate_result_method') {
			$code = rtrim(preg_replace('/[^a-z0-9_]+/', '_', strtolower($carrierTitle . '_' . $methodTitle)), '_');
			$method->setMethod($code);
			$method->setMethodTitle($methodTitle);
		} elseif ($model == 'shipping/rate_result_error') {
			$method->setErrorMessage($methodTitle);
		}

		if (!is_null($total)) {
			$method->setCost($total);
			$method->setPrice($total);
		}

		return $method;
	}

	/**
	 * Tests if a provided amount is a valid dollar figure
	 *
	 * @param  mixed $amount The dollar amount to validate
	 * @return bool          Whether the provided amount is a valid dollar figure
	 */
	private function _isCurrency($amount) {
		return preg_match('/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/', $amount);
	}
}