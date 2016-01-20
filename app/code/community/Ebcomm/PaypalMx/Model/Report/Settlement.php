<?php
class Ebcomm_PaypalMx_Model_Report_Settlement extends Mage_Core_Model_Abstract
{
    /**
     * Default PayPal SFTP host
     * @var string
     */
    const REPORTS_HOSTNAME = "reports.paypal.com";

    /**
     * Default PayPal SFTP host for sandbox mode
     * @var string
     */
    const SANDBOX_REPORTS_HOSTNAME = "reports.sandbox.paypal.com";

    /**
     * PayPal SFTP path
     * @var string
     */
    const REPORTS_PATH = "/ppreports/outgoing";

    /**
     * Original charset of old report files
     * @var string
     */
    const FILES_IN_CHARSET = "UTF-16";

    /**
     * Target charset of report files to be parsed
     * @var string
     */
    const FILES_OUT_CHARSET = "UTF-8";

    /**
     * Reports rows storage
     * @var array
     */
    protected $_rows = array();

    protected $_csvColumns = array(
        'old' => array(
        		'section_columns' => array(
        				'' => 0,
        				'IDdeTransacción' => 1,
        				'IDdelafactura' => 2,
        				'IdentificacióndeReferenciaPayPal' => 3,
        				'TipodeIdentificacióndeReferencia PayPal' => 4,
        				'CódigodeEventodeTransacción' => 5,
        				'FechadeIniciodeTransacción' => 6,
        				'FechadeFinalizacióndeTransacción' => 7,
        				'TransacciónconTarjetadeDébitooCrédito' => 8,
        				'ImporteBrutodeTransacción' => 9,
        				'DivisadeTransacciónBruta' => 10,
        				'ComisióndeTarjetadeDébitooCrédito' => 11,
        				'ImportedelaComisión' => 12,
        				'ComisióndeDivisa' => 13,
        				'CampoPersonalizado' => 14,
        				'Identificacióndelconsumidor' => 15,
        		),
        		'rowmap' => array(
        				'IDdeTransacción' => 'transaction_id',
        				'IDdelafactura' => 'invoice_id',
        				'IdentificacióndeReferenciaPayPal' => 'paypal_reference_id',
        				'TipodeIdentificacióndeReferenciaPayPal' => 'paypal_reference_id_type',
        				'CódigodeEventodeTransacción' => 'transaction_event_code',
        				'FechadeIniciodeTransacción' => 'transaction_initiation_date',
        				'FechadeFinalizacióndeTransacción' => 'transaction_completion_date',
        				'TransacciónconTarjetadeDébitooCrédito' => 'transaction_debit_or_credit',
        				'ImporteBrutodeTransacción' => 'gross_transaction_amount',
        				'DivisadeTransacciónBruta' => 'gross_transaction_currency',
        				'ComisióndeTarjetadeDébitooCrédito' => 'fee_debit_or_credit',
        				'ImportedelaComisión' => 'fee_amount',
        				'ComisióndeDivisa' => 'fee_currency',
        				'CampoPersonalizado' => 'custom_field',
        				'Identificacióndelconsumidor' => 'consumer_id',
        		)
        		/*
            'section_columns' => array(
                '' => 0,
                'TransactionID' => 1,
                'InvoiceID' => 2,
                'PayPalReferenceID' => 3,
                'PayPalReferenceIDType' => 4,
                'TransactionEventCode' => 5,
                'TransactionInitiationDate' => 6,
                'TransactionCompletionDate' => 7,
                'TransactionDebitOrCredit' => 8,
                'GrossTransactionAmount' => 9,
                'GrossTransactionCurrency' => 10,
                'FeeDebitOrCredit' => 11,
                'FeeAmount' => 12,
                'FeeCurrency' => 13,
                'CustomField' => 14,
                'ConsumerID' => 15
            ),
            'rowmap' => array(
                'TransactionID' => 'transaction_id',
                'InvoiceID' => 'invoice_id',
                'PayPalReferenceID' => 'paypal_reference_id',
                'PayPalReferenceIDType' => 'paypal_reference_id_type',
                'TransactionEventCode' => 'transaction_event_code',
                'TransactionInitiationDate' => 'transaction_initiation_date',
                'TransactionCompletionDate' => 'transaction_completion_date',
                'TransactionDebitOrCredit' => 'transaction_debit_or_credit',
                'GrossTransactionAmount' => 'gross_transaction_amount',
                'GrossTransactionCurrency' => 'gross_transaction_currency',
                'FeeDebitOrCredit' => 'fee_debit_or_credit',
                'FeeAmount' => 'fee_amount',
                'FeeCurrency' => 'fee_currency',
                'CustomField' => 'custom_field',
                'ConsumerID' => 'consumer_id'
            )*/
        ),
        'new' => array(
        	'section_columns' => array(
       			'' => 0,
                'Identificación de transacción' => 1,
                'Identificación de la factura' => 2,
                'Identificación de referencia de PayPal' => 3,
                'Tipo de Identificación de referencia PayPal' => 4,
                'Código de evento de la transacción' => 5,
                'Fecha de Inicio de Transacción' => 6,
                'Fecha de Finalización de Transacción' => 7,
                'Transacción con Tarjeta de Débitoo Crédito' => 8,
                'Importe Bruto de Transacción' => 9,
                'Divisa de Transacción Bruta' => 10,
                'Comisión de Tarjeta de Débito o Crédito' => 11,
                'Importe de la Comisión' => 12,
                'Comisión de Divisa' => 13,
                'Campo Personalizado' => 14,
                'Identificación del consumidor' => 15,
                'Identificación de seguimiento del pago' => 16
        	),/*
            'section_columns' => array(
            	
                '' => 0,
                'Transaction ID' => 1,
                'Invoice ID' => 2,
                'PayPal Reference ID' => 3,
                'PayPal Reference ID Type' => 4,
                'Transaction Event Code' => 5,
                'Transaction Initiation Date' => 6,
                'Transaction Completion Date' => 7,
                'Transaction  Debit or Credit' => 8,
                'Gross Transaction Amount' => 9,
                'Gross Transaction Currency' => 10,
                'Fee Debit or Credit' => 11,
                'Fee Amount' => 12,
                'Fee Currency' => 13,
                'Custom Field' => 14,
                'Consumer ID' => 15,
                'Payment Tracking ID' => 16
            ),/*
            'rowmap' => array(
                'Transaction ID' => 'transaction_id',
                'Invoice ID' => 'invoice_id',
                'PayPal Reference ID' => 'paypal_reference_id',
                'PayPal Reference ID Type' => 'paypal_reference_id_type',
                'Transaction Event Code' => 'transaction_event_code',
                'Transaction Initiation Date' => 'transaction_initiation_date',
                'Transaction Completion Date' => 'transaction_completion_date',
                'Transaction  Debit or Credit' => 'transaction_debit_or_credit',
                'Gross Transaction Amount' => 'gross_transaction_amount',
                'Gross Transaction Currency' => 'gross_transaction_currency',
                'Fee Debit or Credit' => 'fee_debit_or_credit',
                'Fee Amount' => 'fee_amount',
                'Fee Currency' => 'fee_currency',
                'Custom Field' => 'custom_field',
                'Consumer ID' => 'consumer_id',
                'Payment Tracking ID' => 'payment_tracking_id'
            )*/
        	'rowmap' => array(
                'Identificación de transacción' => 'transaction_id',
                'Identificación de recibo PayPal' => 'invoice_id',
                'Identificación de referencia de PayPal' => 'paypal_reference_id',
                'Tipo de Identificación de referencia de PayPal' => 'paypal_reference_id_type',
                'Código de evento de la transacción' => 'transaction_event_code',
                'Fecha de inicio de la transacción' => 'transaction_initiation_date',
                'Fecha de finalización de la transacción' => 'transaction_completion_date',
                'Transacción con tarjeta de débito o crédito' => 'transaction_debit_or_credit',
                'Importe bruto de la transacción' => 'gross_transaction_amount',
                'Divisa de la transacción bruta' => 'gross_transaction_currency',
                'Comisión de la tarjeta de débito o crédito' => 'fee_debit_or_credit',
                'Importe de la comisión' => 'fee_amount',
                'Divisa de comisión' => 'fee_currency',
                'Campo personalizado' => 'custom_field',
                'Identificación del consumidor' => 'consumer_id',
                'Identificación de seguimiento del pago' => 'payment_tracking_id'
            )
        )
    );
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('paypalmx/report_settlement');
    }

    /**
     * Stop saving process if file with same report date, account ID and last modified date was already ferched
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->_dataSaveAllowed = true;
        if ($this->getId()) {
            if ($this->getLastModified() == $this->getReportLastModified()) {
                $this->_dataSaveAllowed = false;
            }
        }
        $this->setLastModified($this->getReportLastModified());
        return parent::_beforeSave();
    }

    /**
     * Goes to specified host/path and fetches reports from there.
     * Save reports to database.
     *
     * @param array $config SFTP credentials
     * @return int Number of report rows that were fetched and saved successfully
     */
    public function fetchAndSave($config)
    {
        $connection = new Varien_Io_Sftp();
        $connection->open(array(
            'host'     => $config['hostname'],
            'username' => $config['username'],
            'password' => $config['password']
        ));
        $connection->cd($config['path']);
        $fetched = 0;
        $listing = $this->_filterReportsList($connection->rawls());
        foreach ($listing as $filename => $attributes) {
            $localCsv = tempnam(Mage::getConfig()->getOptions()->getTmpDir(), 'PayPal_STL');
            if ($connection->read($filename, $localCsv)) {
                if (!is_writable($localCsv)) {
                    Mage::throwException(Mage::helper('paypalmx')->__('Cannot create target file for reading reports.'));
                }

                $encoded = file_get_contents($localCsv);
                $csvFormat = 'new';
                if (self::FILES_OUT_CHARSET != mb_detect_encoding(($encoded))) {
                    $decoded = @iconv(self::FILES_IN_CHARSET, self::FILES_OUT_CHARSET.'//IGNORE', $encoded);
                    file_put_contents($localCsv, $decoded);
                    $csvFormat = 'old';
                }

                // Set last modified date, this value will be overwritten during parsing
                if (isset($attributes['mtime'])) {
                    $lastModified = new Zend_Date($attributes['mtime']);
                    $this->setReportLastModified($lastModified->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
                }

                $this->setReportDate($this->_fileNameToDate($filename))
                    ->setFilename($filename)
                    ->parseCsv($localCsv, $csvFormat);

                if ($this->getAccountId()) {
                    $this->save();
                }

                if ($this->_dataSaveAllowed) {
                    $fetched += count($this->_rows);
                }
                // clean object and remove parsed file
                $this->unsetData();
                unlink($localCsv);
            }
        }
        return $fetched;
    }

    /**
     * Parse CSV file and collect report rows
     *
     * @param string $localCsv Path to CSV file
     * @param string $format CSV format(column names)
     * @return Mage_Paypal_Model_Report_Settlement
     */
    public function parseCsv($localCsv, $format = 'new')
    {
        $this->_rows = array();

        $sectionColumns = $this->_csvColumns[$format]['section_columns'];
        $rowMap = $this->_csvColumns[$format]['rowmap'];

        $flippedSectionColumns = array_flip($sectionColumns);
        $fp = fopen($localCsv, 'r');
        while($line = fgetcsv($fp)) {
            if (empty($line)) { // The line was empty, so skip it.
                continue;
            }
            $lineType = $line[0];
            switch($lineType) {
                case 'RH': // Report header.
                    $lastModified = new Zend_Date($line[1]);
                    $this->setReportLastModified($lastModified->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
                    //$this->setAccountId($columns[2]); -- probably we'll just take that from the section header...
                    break;
                case 'FH': // File header.
                    // Nothing interesting here, move along
                    break;
                case 'SH': // Section header.
                    $this->setAccountId($line[3]);
                    $this->loadByAccountAndDate();
                    break;
                case 'CH': // Section columns.
                    // In case ever the column order is changed, we will have the items recorded properly
                    // anyway. We have named, not numbered columns.
                    for ($i = 1; $i < count($line); $i++) {
                        $sectionColumns[$line[$i]] = $i;
                    }
                    $flippedSectionColumns = array_flip($sectionColumns);
                    break;
                case 'SB': // Section body.
                    $bodyItem = array();
                    for($i = 1; $i < count($line); $i++) {
                        $bodyItem[$rowMap[$flippedSectionColumns[$i]]] = $line[$i];
                        
                    }
                    $this->_rows[] = $bodyItem;
                    break;
                case 'SC': // Section records count.
                case 'RC': // Report records count.
                case 'SF': // Section footer.
                case 'FF': // File footer.
                case 'RF': // Report footer.
                    // Nothing to see here, move along
                    break;
            }
        }
        return $this;
    }

    /**
     * Load report by unique key (accoutn + report date)
     *
     * @return Mage_Paypal_Model_Report_Settlement
     */
    public function loadByAccountAndDate()
    {
        $this->getResource()->loadByAccountAndDate($this, $this->getAccountId(), $this->getReportDate());
        return $this;
    }

    /**
     * Return collected rows for further processing.
     *
     * @return array
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Return name for row column
     *
     * @param string $field Field name in row model
     * @return string
     */
    public function getFieldLabel($field)
    {
        switch ($field) {
            case 'report_date':
                return Mage::helper('paypalmx')->__('Fecha de reporte');
            case 'account_id':
                return Mage::helper('paypalmx')->__('Cuenta del comerciante');
            case 'transaction_id':
                return Mage::helper('paypalmx')->__('ID de transacción');
            case 'invoice_id':
                return Mage::helper('paypalmx')->__('ID de factura');
            case 'paypal_reference_id':
                return Mage::helper('paypalmx')->__('ID de referencia para PayPal');
            case 'paypal_reference_id_type':
                return Mage::helper('paypalmx')->__('Referencia al tipo de ID de PayPal');
            case 'transaction_event_code':
                return Mage::helper('paypalmx')->__('Codigo de evento');
            case 'transaction_event':
                return Mage::helper('paypalmx')->__('Evento');
            case 'transaction_initiation_date':
                return Mage::helper('paypalmx')->__('Fecha de incio');
            case 'transaction_completion_date':
                return Mage::helper('paypalmx')->__('Fecha de termino');
            case 'transaction_debit_or_credit':
                return Mage::helper('paypalmx')->__('Débito o Crédito');
            case 'gross_transaction_amount':
                return Mage::helper('paypalmx')->__('Monto bruto');
            /*case 'fee_debit_or_credit':
                return Mage::helper('paypalmx')->__('Cuota por Débito o Crédito');*/
            case 'fee_amount':
                return Mage::helper('paypalmx')->__('Monto de cuota');
            case 'custom_field':
                return Mage::helper('paypalmx')->__('Personalizado');
            default:
                return $field;
        }
    }

    /**
     * Iterate through website configurations and collect all SFTP configurations
     * Filter config values if necessary
     *
     * @param bool $automaticMode Whether to skip settings with disabled Automatic Fetching or not
     * @return array
     */
    public function getSftpCredentials($automaticMode = false)
    {
        $configs = array();
        $uniques = array();
        foreach(Mage::app()->getStores() as $store) {
            /*@var $store Mage_Core_Model_Store */
            $active = (bool)$store->getConfig('paypalmx/fetch_reports/active');
            if (!$active && $automaticMode) {
                continue;
            }
            $cfg = array(
                'hostname'  => $store->getConfig('paypalmx/fetch_reports/ftp_ip'),
                'path'      => $store->getConfig('paypalmx/fetch_reports/ftp_path'),
                'username'  => $store->getConfig('paypalmx/fetch_reports/ftp_login'),
                'password'  => $store->getConfig('paypalmx/fetch_reports/ftp_password'),
                'sandbox'   => $store->getConfig('paypalmx/fetch_reports/ftp_sandbox'),
            );
            if (empty($cfg['username']) || empty($cfg['password'])) {
                continue;
            }
            if (empty($cfg['hostname']) || $cfg['sandbox']) {
                $cfg['hostname'] = $cfg['sandbox'] ? self::SANDBOX_REPORTS_HOSTNAME : self::REPORTS_HOSTNAME;
            }
            if (empty($cfg['path']) || $cfg['sandbox']) {
                $cfg['path'] = self::REPORTS_PATH;
            }
            // avoid duplicates
            if (in_array(serialize($cfg), $uniques)) {
                continue;
            }
            $uniques[] = serialize($cfg);
            $configs[] = $cfg;
        }
        return $configs;
    }

    /**
     * Converts a filename to date of report.
     *
     * @param string $filename
     * @return string
     */
    protected function _fileNameToDate($filename)
    {
        // Currently filenames look like STL-YYYYMMDD, so that is what we care about.
        $dateSnippet = substr(basename($filename), 4, 8);
        $result = substr($dateSnippet, 0, 4).'-'.substr($dateSnippet, 4, 2).'-'.substr($dateSnippet, 6, 2);
        return $result;
    }

    /**
     * Filter SFTP file list by filename format
     *
     * @param array $list List of files as per $connection->rawls()
     * @return array Trimmed down list of files
     */
    protected function _filterReportsList($list)
    {
        $result = array();
        $pattern = '/^STL-(\d{8,8})\.(\d{2,2})\.(.{3,3})\.CSV$/';
        foreach ($list as $filename => $data) {
            if (preg_match($pattern, $filename)) {
                $result[$filename] = $data;
            }
        }
        return $result;
    }
}
