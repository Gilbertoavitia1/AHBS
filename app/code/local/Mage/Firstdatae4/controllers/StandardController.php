<?php
class Mage_Firstdatae4_StandardController extends Mage_Core_Controller_Front_Action
{

    protected $_order;

    public function getOrder()
    {
        if ($this->_order == null) {
        }
        return $this->_order;
    }

    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    public function getStandard()
    {
        return Mage::getSingleton('firstdatae4/standard');
    }

    /**
     * When a customer chooses Firstdatae4 on Checkout/Payment page
     *
     */
    public function redirectAction()
    {        
        if ($this->getRequest()->getPost()){
            $session = Mage::getSingleton("core/session");
            $session->setData("fde4-recurring", $this->getRequest()->getPost('recurring'));                        
            $session = Mage::getSingleton('checkout/session');
            $this->create($this->getRequest()->getPost('recurring'));
            $session->setFirstdatae4StandardQuoteId($session->getQuoteId());
            $this->getResponse()->setBody($this->getLayout()->createBlock('firstdatae4/standard_redirect')->toHtml());
            $session->unsQuoteId();
        }
        else {
            $recurring = new Varien_Data_Form();
            $recurring->setAction(Mage::getBaseUrl().'firstdatae4/standard/redirect/')
                ->setId('recurring-profiles')
                ->setName('recurring-profiles')
                ->setMethod('POST')
                ->setUseContainer(true);

            $model = Mage::getModel('firstdatae4/plans');
            $plans = $model->toOptionArray();
            foreach ($plans as $key => $value) {            
                $radio = 'rp_'.$key;
                $config = Mage::getStoreConfig('firstdatae4/config/'.$radio,Mage::app()->getStore()); 
                if ($config != '0')
                    $recurring->addField($radio, 'radio', array('name'=>'recurring', 'value'=>$config, 'label'=> $value));
            }

            $recurring->addField('none', 'radio', array('name'=>'recurring', 'value'=>0, 'label'=> Mage::helper('firstdatae4')->__("I do not want any plan at this time")));
            $recurring->addField('submit', 'submit', array('name'=>'button', 'value'=>'Submit'));

            $html = '<html>'
                    .'<style>'
                    .' *{ margin: 0; padding:0 ; font-family: Arial; } #top-box{ background-color: #166700; padding: 10px; color: #fff; }'
                    . ' #content { padding: 10px; } input { margin-right: 7px; }'
                    . ' #submit { background: none repeat scroll 0 0 #738d00; border: 0 none; border-radius: 3px; box-shadow: none;  color: #fff; filter: none; line-height: 22px; outline: medium none; padding: 9px 20px; text-shadow: none; transition: background 0.2s linear 0s, color 0.2s linear 0s; white-space: normal; cursor: pointer; display: inline-block; font-size: 14px; font-weight: normal;  margin-top: 20px; }'
                    . '</style>'
                    . '<body>';
            
            $html .= '<div id="top-box">Habitos Saludables</div>';
            $html.= '<div id="content">'.$this->__('If you want to automatically repeat this purchases, please select the type of profiles you want:');
            $html.= $recurring->toHtml();
            $html.= '</div>';
            $html.= '<script type="text/javascript">document.getElementById("none").checked = true;</script>';

            $html.= '<style>.field-row{ display:block; } .field-row input { float: left;white-space:nowrap;}</style>';

            $html.= '</body></html>';

            echo  $html;
        } 
    }
    
    public function cancelAction() 
    {
        $msg = $_POST['fail_reason'];
        // load order
        $session = Mage::getSingleton('checkout/session');
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->addStatusToHistory(
                    //$order->getStatus(),//continue setting current order status
                    Mage_Sales_Model_Order::STATE_CANCELED,
                    urldecode($msg) . ' at FirstData',
                    Mage::helper('firstdatae4')->__($msg . ' from FirstData')
                );
                $order->save();
            }
        }

        // cancel order
        $session->setQuoteId($session->getFirstDataStandardQuoteId(true));
		
        if ($session->getLastRealOrderId()) {
                $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
                if ($order->getId()) {
                        $order->cancel()->save();
                }
        }

        $display_message = 'Payment could not be completed! Reason: ' . "\n" . $display_message;
        $session->addError(nl2br($msg)); 
        $this->_redirect('checkout/cart');
    }
	
    public function successAction($msg ='')
    {
        // load order
        $session = Mage::getSingleton('checkout/session');
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                if ($_POST['x_response_code'] != 1) {
                    // transaction failed or error!					
                    $msg = $_POST['x_response_reason_text'];					
                    // load order
                    $session = Mage::getSingleton('checkout/session');
                    if ($session->getLastRealOrderId()) {
                        $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
                        if ($order->getId()) {
                            $order->addStatusToHistory(
                                //$order->getStatus(),//continue setting current order status
                                Mage_Sales_Model_Order::STATE_CANCELED,
                                urldecode($msg) . ' at FirstData',
                                Mage::helper('firstdatae4')->__($msg . ' from FirstData')
                            );
                            $order->save();
                        }
                    }
					// cancel order
                    $session->setQuoteId($session->getFirstDataStandardQuoteId(true));
					
                    if ($session->getLastRealOrderId()) {
                        $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
                        if ($order->getId()) {
                            $order->cancel()->save();
                        }
                    }

                    $display_message = 'Payment could not be completed! Reason: ' . "\n" . $msg;
                    $session->addError(nl2br($display_message)); 
                    $this->_redirect('checkout/cart');					
					
                } 
                else {
                    // transaction successful					
                    $approvalcode = $_POST['x_auth_code'];
                    $transid = $_POST['x_trans_id'];
                    $msg = $_POST['x_response_reason_text'] . '; ApprovalCode: ' . $approvalcode . ', TranxID: ' . $transid . ', OID: ' .  $_POST['x_invoice_num'];
			
                    if ($_POST['x_type'] == 'AUTH_CAPTURE') {
                        // capture the payment
                        $payment = $order->getPayment();
						
                        if (method_exists($payment, 'registerCaptureNotification')) {
                            $payment->registerCaptureNotification($_POST['x_amount']);						
                        } 
                        else {
                            // for versions before 1.4 registerCaptureNotification() function does not exist
                            $newOrderStatus = $order->getStatus();
                            if (!$order->canInvoice()) {
                               // when order cannot create invoice, need to have some logic to take care
                               $order->addStatusToHistory(
                                    $order->getStatus(), // keep order status/state
                                    Mage::helper('firstdatae4')->__('Error in creating an invoice', true),
                                    $notified = true
                               );
                            } 
                            else {
                               // need to save transaction id
                               $order->getPayment()->setTransactionId($transid);

                               // need to convert from order into invoice
                               $invoice = $order->prepareInvoice();
                               $invoice->register()->pay();
                               Mage::getModel('core/resource_transaction')
                                       ->addObject($invoice)
                                       ->addObject($invoice->getOrder())
                                       ->save();

                                $order->setState(
                                    Mage_Sales_Model_Order::STATE_PROCESSING, true,
                                    Mage::helper('firstdatae4')->__('Notified customer about invoice #%s.', $invoice->getIncrementId()),
                                    $notified = true
                                );
                            }
                        }
						
                        $payment->setStatus(Mage_Payment_Model_Method_Cc::STATUS_APPROVED);
                        $payment->setCcTransId($transid);
                        $payment->setLastTransId($transid);
                        if ($transid != $payment->getParentTransactionId()) {
                            $payment->setTransactionId($transid);
                        }	
                        
                        $payment
                            ->setIsTransactionClosed(0)
                            ->setTransactionAdditionalInfo('real_transaction_id', $transid);
                        
                        $order->save();						
                    } 
                    else {
                        // authorized payment
                        $order->addStatusToHistory(
                            //$order->getStatus(),
                            Mage_Sales_Model_Order::STATE_PROCESSING,
                            urldecode($msg) . ' at Firstdatae4',
                            Mage::helper('firstdatae4')->__($msg . ' from Firstdatae4')
                        );
                        
                        $payment = $order->getPayment();						
                        $payment->setStatus(Mage_Payment_Model_Method_Cc::STATUS_APPROVED);
                        if ($transid != $payment->getParentTransactionId()) {
                            $payment->setTransactionId($transid);
                        }	
                        
                        $payment->setIsTransactionClosed(0)
                            ->setTransactionAdditionalInfo('real_transaction_id', $transid);
                        
                        $order->save();
						
                    } // end authorize
                    
                    $this->processing($order->getId());
                    
                                    
                    $order = Mage::getModel('sales/order')->load($order->getId());
                    $order->setData('state', "complete");
                    $order->setStatus("complete");          
                    $history = $order->addStatusHistoryComment('Esta orden fue cambiada automaticamente. Verificar antes de envio.', false);
                    $history->setIsCustomerNotified(false);
                    $order->save();
                    
                    /**/
                    $items = $order->getItemsCollection();
                    $qtys = array(); 

                    foreach($items as $item){
                        $qty_to_invoice = $item['qty_ordered'];
                        $qtys[$item->getId()] = $qty_to_invoice;
                    }

                    $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($qtys);
                    $amount = $invoice->getGrandTotal();
                    $invoice->register()->pay();
                    $invoice->getOrder()->setIsInProcess(true);

                    $history = $invoice->getOrder()->addStatusHistoryComment('Recibo Enviado Automaticamente.', false);

                    $history->setIsCustomerNotified(false);

                    $order->save();

                    Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();
                    $invoice->save();
                    $invoice->sendEmail(false, '');
                    /**/
                    
                    $session->setQuoteId($session->getFirstdatae4StandardQuoteId(true));                   
                    Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();                    
                    
                    $this->_redirect('checkout/onepage/success', array('_secure'=>true));
					
                } // end transaction success				
            } // end order id check			
        } // end session check		
    }// end successAction function
    
      
    public function create($recurring){
        if ($recurring){
            $session = Mage::getSingleton('checkout/session');
            if ($session->getLastRealOrderId()) {
                $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
                if ($order->getId()) {
                    $address = $order->getShippingAddress();
                    $_type = '';
                    foreach (Mage::getStoreConfig('firstdatae4/config') as $key => $value) {
                        if ($recurring == $value)
                            $_type = ltrim($key, 'rp_');    
                    }

                    $_date = date('Y-m-d h:i:s');

                    $payment = $order->getPayment();
                    $array = array(
                        'order_id'      =>  $order->getId(),
                        'customer_id'   =>  $order->getCustomerId(),
                        'address_id'    =>  $address->getData('customer_address_id'),
                        'type'          =>  $_type,
                        'created_date'  =>  $_date,
                        'updated_date'  =>  $_date,
                        'next_date'     =>  Mage::getModel('firstdatae4/plans')->nextDate($_date,$_type),

                    );

                    $model = Mage::getModel('firstdatae4/recurring');
                    $model->recurring_data($array);
                }
            }
        }
    }
    
    public function processing($id){
        $model = Mage::getModel('firstdatae4/recurring');
        $model->processing_data($id);
    }    
}