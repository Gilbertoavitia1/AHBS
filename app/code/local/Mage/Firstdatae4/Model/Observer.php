<?php
class Mage_Firstdatae4_Model_Observer
{
    public function runCron()
    {
     	$username = Mage::getStoreConfig('firstdatae4/settings/reporteruser');
        $password = Mage::getStoreConfig('firstdatae4/settings/reporterpwd');

        $_date = date('Y-m-d');
        $start_date = $_date.'%2000:00:00';
        $end_date = $_date.'%2023:59:59';

        $standard = Mage::getModel('firstdatae4/standard');
        $base_url = $standard->getFirstdatae4ReportUrl();
        $url = "{$base_url}?start_date={$start_date}&end_date={$end_date}&status=A";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($ch);
        curl_close($ch);

        $report = explode("\n", $output);
        array_pop($report);

        $filter_a = array('eq'=>$_date);
       	$filter_b = array('from'=>$_date,'to'=>$_date.' 23:59:59');

        $recurring = Mage::getModel('firstdatae4/recurring')->getCollection()
     	    ->addFieldToFilter('next_date', array($filter_a, $filter_b))
            ->addFieldToFilter('status', array('nin' => array('DELETED')));

        $data = $recurring->getData();

        foreach ($data as $key => $value) {
            $orderId = $value['order_id'];
            Mage::unregister('rule_data');
            Mage::getSingleton('adminhtml/session_quote')->clear();

            $order = Mage::getModel('sales/order')->load($orderId);
            $incId = $order->getIncrementId();

            foreach ($report as $k => $v) {
                if ($k > 0){
                    if (sizeof($v)>0){
                        $line = explode(",", $v);
                       	if (($line[6]== "Purchase") && ($line[10] == $incId)){
                            $approvalcode = $line[8];
                            $transid = $line[0];

                            $newQuote = new Mage_Sales_Model_Quote();
                            $newQuote->setStoreId($order->getStoreId());
                            Mage::getSingleton('adminhtml/sales_order_create')->setQuote($newQuote);
                            
                            $order_model = Mage::getSingleton('adminhtml/sales_order_create');
                            $order_model->getSession()->clear();

                            try {
                                $order->setReordered(true);
                                Mage::getSingleton('adminhtml/session_quote')->setUseOldShippingMethod(true);

                                $reorder = new Varien_Object();
                                $reorder = $order_model->initFromOrder($order);
                                $newOrder = $reorder->createOrder();

                                $reOrderId = $newOrder->getId();
                                $reOrderIncId = $newOrder->getIncrementId();

                                $next = Mage::getModel('firstdatae4/plans')->nextDate($_date,$value['type']);
                                $model = Mage::getModel('firstdatae4/recurring');
                                $model->nextRun($orderId, $next);
                                $model->prepareHistory(array('recurring_id'=> $value['recurring_id'], 'date_of_run'=>$_date, 'order_created'=>$reOrderIncId));

                                $newOrder->getPayment()->setTransactionId($transid);
                               	$invoice = $newOrder->prepareInvoice();
                                $invoice->register()->pay();
                                Mage::getModel('core/resource_transaction')
                                    ->addObject($invoice)
                                    ->addObject($invoice->getOrder())
                                    ->save();

                                $newOrder->setState(
                                    Mage_Sales_Model_Order::STATE_PROCESSING, true,
                                    Mage::helper('firstdatae4')->__('Notified customer about invoice #%s.', $invoice->getIncrementId()),
                                    $notified = true);
                                $newOrder->save();
                                $newOrder->sendNewOrderEmail();

                                $newOrder->save();
                                
                                Mage::log("Order #{$incId} is Reorders To New Order #{$reOrderIncId} Successfully",null,"reorder.log");
                            } catch (Exception $e) {
                                Mage::log("Order #{$incId} Reorder Error : {$e}",null,"reorder.log");
                            }                            
                        }
                    }
                }
            }
        }
    }
}
