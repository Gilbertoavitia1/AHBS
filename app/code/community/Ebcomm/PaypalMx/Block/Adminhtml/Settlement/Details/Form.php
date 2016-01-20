<?php
class Ebcomm_PaypalMx_Block_Adminhtml_Settlement_Details_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare read-only data and group it by fieldsets
     * @return Mage_Paypal_Block_Adminhtml_Settlement_Details_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_transaction');
        /* @var $model Mage_Paypal_Model_Report_Settlement_Row */
        $settlement = Mage::getSingleton('paypalmx/report_settlement');
        /* @var $settlement Mage_Paypal_Model_Report_Settlement */

        $fieldsets = array(
            'reference_fieldset' => array(
                'fields' => array(
                    'transaction_id' => array('label' => $settlement->getFieldLabel('transaction_id')),
                    'invoice_id' => array('label' => $settlement->getFieldLabel('invoice_id')),
                    'paypal_reference_id' => array('label' => $settlement->getFieldLabel('paypal_reference_id')),
                    'paypal_reference_id_type' => array(
                        'label' => $settlement->getFieldLabel('paypal_reference_id_type'),
                        'value' => $model->getReferenceType($model->getData('paypal_reference_id_type'))
                    ),
                    'custom_field' => array('label' => $settlement->getFieldLabel('custom_field')),
                ),
                'legend' => Mage::helper('paypalmx')->__('Información de Referencia')
            ),

            'transaction_fieldset' => array(
                'fields' => array(
                    'transaction_event_code' => array(
                        'label' => $settlement->getFieldLabel('transaction_event_code'),
                        'value' => sprintf('%s (%s)', $model->getData('transaction_event_code'), $model->getTransactionEvent($model->getData('transaction_event_code')))
                    ),
                    'transaction_initiation_date' => array(
                        'label' => $settlement->getFieldLabel('transaction_initiation_date'),
                        'value' => $this->helper('core')->formatDate($model->getData('transaction_initiation_date'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
                    ),
                    'transaction_completion_date' => array(
                        'label' => $settlement->getFieldLabel('transaction_completion_date'),
                        'value' => $this->helper('core')->formatDate($model->getData('transaction_completion_date'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
                    ),
                    'transaction_debit_or_credit' => array(
                        'label' => $settlement->getFieldLabel('transaction_debit_or_credit'),
                        'value' => $model->getDebitCreditText($model->getData('transaction_debit_or_credit'))
                    ),
                    'gross_transaction_amount' => array(
                        'label' => $settlement->getFieldLabel('gross_transaction_amount'),
                        'value' => Mage::app()->getLocale()
                                       ->currency($model->getData('gross_transaction_currency'))
                                       ->toCurrency($model->getData('gross_transaction_amount'))
                    ),
                ),
                'legend' => Mage::helper('paypalmx')->__('Información de Pago Recibido')
            ),

            'fee_fieldset' => array(
                'fields' => array(
                    /*'fee_debit_or_credit' => array(
                        'label' => $settlement->getFieldLabel('fee_debit_or_credit'),
                        'value' => $model->getDebitCreditText($model->getData('fee_debit_or_credit'))
                    ),*/
                    'fee_amount' => array(
                        'label' => $settlement->getFieldLabel('fee_amount'),
                        'value' => Mage::app()->getLocale()
                                       ->currency($model->getData('fee_currency'))
                                       ->toCurrency($model->getData('fee_amount'))
                    ),
                ),
                'legend' => Mage::helper('paypalmx')->__('Información de comisión de PayPal')
            ),
        );

        $form = new Varien_Data_Form();
        foreach ($fieldsets as $key => $data) {
            $fieldset = $form->addFieldset($key, array('legend' => $data['legend']));
            foreach ($data['fields'] as $id => $info) {
                $fieldset->addField($id, 'label', array(
                    'name'  => $id,
                    'label' => $info['label'],
                    'title' => $info['label'],
                    'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
                ));
            }
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
