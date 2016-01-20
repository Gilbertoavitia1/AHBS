<?php
class Ebcomm_PaypalMx_Adminhtml_Paypal_ReportssController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Grid action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('paypalmx/adminhtml_settlement_report'))
            ->renderLayout();
    }

    /**
     * Ajax callback for grid actions
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('paypalmx/adminhtml_settlement_report_grid')->toHtml()
        );
    }

    /**
     * View transaction details action
     */
    public function detailsAction()
    {
        $rowId = $this->getRequest()->getParam('id');
        $row = Mage::getModel('paypalmx/report_settlement_row')->load($rowId);
        if (!$row->getId()) {
            $this->_redirect('*/*/');
            return;
        }
        Mage::register('current_transaction', $row);
        $this->_initAction()
            ->_title($this->__('View Transaction'))
            ->_addContent($this->getLayout()->createBlock('paypalmx/adminhtml_settlement_details', 'settlementDetails'))
            ->renderLayout();
    }

    /**
     * Forced fetch reports action
     */
    public function fetchAction()
    {
        try {
            $reports = Mage::getModel('paypalmx/report_settlement');
            /* @var $reports Mage_Paypal_Model_Report_Settlement */
            $credentials = $reports->getSftpCredentials();
            if (empty($credentials)) {
                Mage::throwException(Mage::helper('paypalmx')->__('Nada que buscar porque no se encuentra la configuracion apropiada.'));
            }
            foreach ($credentials as $config) {
                try {
                    $fetched = $reports->fetchAndSave($config);
                    $this->_getSession()->addSuccess(
                        Mage::helper('paypalmx')->__("Se encontraron %s reportes de '%s@%s'.", $fetched, $config['username'], $config['hostname'])
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError(
                        Mage::helper('paypalmx')->__("No se encontraron reportes de '%s@%s'.", $config['username'], $config['hostname'])
                    );
                    Mage::logException($e);
                }
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Initialize titles, navigation
     * @return Mage_Paypal_Adminhtml_Paypal_ReportsController
     */
    protected function _initAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Reportes de Solución PayPal México'));
        $this->loadLayout()
            ->_setActiveMenu('report/sales')
            ->_addBreadcrumb(Mage::helper('paypalmx')->__('Reports'), Mage::helper('paypalmx')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('paypalmx')->__('Sales'), Mage::helper('paypalmx')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('paypalmx')->__('PayPal Settlement Reports'), Mage::helper('paypalmx')->__('PayPal Settlement Reports'));
        return $this;
    }

    /**
     * ACL check
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'index':
            case 'details':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/paypal_settlement_reports/view');
                break;
            case 'fetch':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/paypal_settlement_reports/fetch');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/paypal_settlement_reports');
                break;
        }
    }
}
