<?php
class Ebcomm_PaypalMx_Model_Report_Settlement_Row extends Mage_Core_Model_Abstract
{
    /**
     * Assoc array event code => label
     *
     * @var array
     */
    protected static $_eventList = array();

    /**
     * Casted amount keys registry
     *
     * @var array
     */
    protected $_castedAmounts = array();

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('paypal/report_settlement_row');
    }

    /**
     * Return description of Reference ID Type
     * If no code specified, return full list of codes with their description
     *
     * @param string code
     * @return string|array
     */
    public function getReferenceType($code = null)
    {
        $types = array(
            'TXN' => Mage::helper('paypalmx')->__('Transaction ID'),
            'ODR' => Mage::helper('paypalmx')->__('Order ID'),
            'SUB' => Mage::helper('paypalmx')->__('Subscription ID'),
            'PAP' => Mage::helper('paypalmx')->__('Preapproved Payment ID')
        );
        if($code === null) {
            asort($types);
            return $types;
        }
        if (isset($types[$code])) {
            return $types[$code];
        }
        return $code;
    }

    /**
     * Get native description for transaction code
     *
     * @param string code
     * @return string
     */
    public function getTransactionEvent($code)
    {
        $this->_generateEventLabels();
        if (isset(self::$_eventList[$code])) {
            return self::$_eventList[$code];
        }
        return $code;
    }

    /**
     * Get full list of codes with their description
     *
     * @return &array
     */
    public function &getTransactionEvents()
    {
        $this->_generateEventLabels();
        return self::$_eventList;
    }

    /**
     * Return description of "Debit or Credit" value
     * If no code specified, return full list of codes with their description
     *
     * @param string code
     * @return string|array
     */
    public function getDebitCreditText($code = null)
    {
        $options = array(
            'CR' => Mage::helper('paypalmx')->__('Credit'),
            'DR' => Mage::helper('paypalmx')->__('Debit'),
        );
        if($code === null) {
            return $options;
        }
        if (isset($options[$code])) {
            return $options[$code];
        }
        return $code;
    }

    /**
     * Invoke casting some amounts
     *
     * @param mixed $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        $this->_castAmount('fee_amount', 'fee_debit_or_credit');
        $this->_castAmount('gross_transaction_amount', 'transaction_debit_or_credit');
        return parent::getData($key, $index);
    }

    /**
     * Cast amounts of the specified keys
     *
     * PayPal settlement reports contain amounts in cents, hence the values need to be divided by 100
     * Also if the "credit" value is detected, it will be casted to negative amount
     *
     * @param string $key
     * @param string $creditKey
     */
    public function _castAmount($key, $creditKey)
    {
        if (isset($this->_castedAmounts[$key]) || !isset($this->_data[$key]) || !isset($this->_data[$creditKey])) {
            return;
        }
        if (empty($this->_data[$key])) {
            return;
        }
        $amount = $this->_data[$key] / 100;
        if ('CR' === $this->_data[$creditKey]) {
            $amount = -1 * $amount;
        }
        $this->_data[$key] = $amount;
        $this->_castedAmounts[$key] = true;
    }

    /**
     * Fill/translate and sort all event codes/labels
     */
    protected function _generateEventLabels()
    {
        if (!self::$_eventList) {
            self::$_eventList = array(
            'T0000' => Mage::helper('paypalmx')->__('General: Recibir pagos de de tipo no perteneciente a la categoria T00xx'),
            'T0001' => Mage::helper('paypalmx')->__('Pagos masivos'),
            'T0002' => Mage::helper('paypalmx')->__('Pagos subscritos, tambien pagos enviados y recibidos'),
            'T0003' => Mage::helper('paypalmx')->__('Pagos previamente aprovados (BillUser API), ya sean enviados o recividos'),
            'T0004' => Mage::helper('paypalmx')->__('eBay Auction Payment'),
            'T0005' => Mage::helper('paypalmx')->__('Pagos Directos del API'),
            'T0006' => Mage::helper('paypalmx')->__('Express Checkout APIs'),
            'T0007' => Mage::helper('paypalmx')->__('Pagos regulares de la tienda'),
            'T0008' => Mage::helper('paypalmx')->__('Pagos de envio ya sea USPS o UPS'),
            'T0009' => Mage::helper('paypalmx')->__('Pagos de certificados de regalo: compra de un certificado de regalo'),
            'T0010' => Mage::helper('paypalmx')->__('Auction Payment other than through eBay'),
            'T0011' => Mage::helper('paypalmx')->__('Pago echos desde un telefono (echos por via telefonica)'),
            'T0012' => Mage::helper('paypalmx')->__('Pagos por terminal virtual'),
            'T0100' => Mage::helper('paypalmx')->__('General: sin cuota de pago, no perteneciente a la categoria T01xx'),
            'T0101' => Mage::helper('paypalmx')->__('Cuota: Web Site Payments Pro Account Monthly'),
            'T0102' => Mage::helper('paypalmx')->__('Cuota: Retiro foraneo de ACH'),
            'T0103' => Mage::helper('paypalmx')->__('Cuota: Retiro de WorldLink Check'),
            'T0104' => Mage::helper('paypalmx')->__('Cuota: solicitud de pagos masivos'),
            'T0200' => Mage::helper('paypalmx')->__('General de conversion de moneda'),
            'T0201' => Mage::helper('paypalmx')->__('Conversion de moneda usado por el usuario'),
            'T0202' => Mage::helper('paypalmx')->__('Conversion de moneda requerido para cubrir el balance negativo'),
            'T0300' => Mage::helper('paypalmx')->__('Fundacion general de la cuenta PayPal'),
            'T0301' => Mage::helper('paypalmx')->__('Ayudante del balance PayPal funcion de la cuenta PayPal'),
            'T0302' => Mage::helper('paypalmx')->__('ACH Fundacion para recuperacion de fondos del balance de la cuenta.'),
            'T0303' => Mage::helper('paypalmx')->__('Fundacion EFT (Banco aleman)'),
            'T0400' => Mage::helper('paypalmx')->__('Retiro general de la cuenta de Paypal'),
            'T0401' => Mage::helper('paypalmx')->__('Limpiado Automático'),
            'T0500' => Mage::helper('paypalmx')->__('General: Uso de la cuenta Paypal para comprar asi como para recibir pagos'),
            'T0501' => Mage::helper('paypalmx')->__('Transaccion de Paypal tarjeto de debito virtual'),
            'T0502' => Mage::helper('paypalmx')->__('Retiros de la tarjeta de debito PayPal del cajero automatico'),
            'T0503' => Mage::helper('paypalmx')->__('Tranascciones escodndidas de la tarjeta de debito Virtual de PayPal'),
            'T0504' => Mage::helper('paypalmx')->__('Avanzado Tarjeta de debito de PayPal'),
            'T0600' => Mage::helper('paypalmx')->__('General: retiro de la cuenta de PayPalt'),
            'T0700' => Mage::helper('paypalmx')->__('General (Compras con una tarjeta de credito)'),
            'T0701' => Mage::helper('paypalmx')->__('Balance Negativo'),
            'T0800' => Mage::helper('paypalmx')->__('General: bonus del tipo no perteneciente a las otras T08xx categories'),
            'T0801' => Mage::helper('paypalmx')->__('Tarjeta de debito regreso de dinero'),
            'T0802' => Mage::helper('paypalmx')->__('Bonus a referencias del comerciante'),
            'T0803' => Mage::helper('paypalmx')->__('Bonus del ayudante'),
            'T0804' => Mage::helper('paypalmx')->__('Bonus del seguro al comprador PayPal'),
            'T0805' => Mage::helper('paypalmx')->__('Bonus de proteccion de PayPal'),
            'T0806' => Mage::helper('paypalmx')->__('Bonus por primer uso de ACH'),
            'T0900' => Mage::helper('paypalmx')->__('Redención General'),
            'T0901' => Mage::helper('paypalmx')->__('Certificado de regalo de Redención'),
            'T0902' => Mage::helper('paypalmx')->__('Points Incentive Redemption'),
            'T0903' => Mage::helper('paypalmx')->__('Cupon de Redención'),
            'T0904' => Mage::helper('paypalmx')->__('Reward Voucher Redemption'),
            'T1000' => Mage::helper('paypalmx')->__('General. Product no longer supported'),
            'T1100' => Mage::helper('paypalmx')->__('General: reversal of a type not belonging to the other T11xx categories'),
            'T1101' => Mage::helper('paypalmx')->__('ACH Withdrawal'),
            'T1102' => Mage::helper('paypalmx')->__('Debit Card Transaction'),
            'T1103' => Mage::helper('paypalmx')->__('Reversal of Points Usage'),
            'T1104' => Mage::helper('paypalmx')->__('ACH Deposit (Reversal)'),
            'T1105' => Mage::helper('paypalmx')->__('Reversal of General Account Hold'),
            'T1106' => Mage::helper('paypalmx')->__('Account-to-Account Payment, initiated by PayPal'),
            'T1107' => Mage::helper('paypalmx')->__('Payment Refund initiated by merchant'),
            'T1108' => Mage::helper('paypalmx')->__('Fee Reversal'),
            'T1110' => Mage::helper('paypalmx')->__('Hold for Dispute Investigation'),
            'T1111' => Mage::helper('paypalmx')->__('Reversal of hold for Dispute Investigation'),
            'T1200' => Mage::helper('paypalmx')->__('General: adjustment of a type not belonging to the other T12xx categories'),
            'T1201' => Mage::helper('paypalmx')->__('Chargeback'),
            'T1202' => Mage::helper('paypalmx')->__('Reversal'),
            'T1203' => Mage::helper('paypalmx')->__('Charge-off'),
            'T1204' => Mage::helper('paypalmx')->__('Incentive'),
            'T1205' => Mage::helper('paypalmx')->__('Reimbursement of Chargeback'),
            'T1300' => Mage::helper('paypalmx')->__('General (Authorization)'),
            'T1301' => Mage::helper('paypalmx')->__('Reauthorization'),
            'T1302' => Mage::helper('paypalmx')->__('Void'),
            'T1400' => Mage::helper('paypalmx')->__('General (Dividend)'),
            'T1500' => Mage::helper('paypalmx')->__('General: temporary hold of a type not belonging to the other T15xx categories'),
            'T1501' => Mage::helper('paypalmx')->__('Open Authorization'),
            'T1502' => Mage::helper('paypalmx')->__('ACH Deposit (Hold for Dispute or Other Investigation)'),
            'T1503' => Mage::helper('paypalmx')->__('Available Balance'),
            'T1600' => Mage::helper('paypalmx')->__('Funding'),
            'T1700' => Mage::helper('paypalmx')->__('General: Withdrawal to Non-Bank Entity'),
            'T1701' => Mage::helper('paypalmx')->__('WorldLink Withdrawal'),
            'T1800' => Mage::helper('paypalmx')->__('Buyer Credit Payment'),
            'T1900' => Mage::helper('paypalmx')->__('General Adjustment without businessrelated event'),
            'T2000' => Mage::helper('paypalmx')->__('General (Funds Transfer from PayPal Account to Another)'),
            'T2001' => Mage::helper('paypalmx')->__('Settlement Consolidation'),
            'T9900' => Mage::helper('paypalmx')->__('General: event not yet categorized'),
            );
            asort(self::$_eventList);
        }
    }
}
