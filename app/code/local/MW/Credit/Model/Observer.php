<?php
class MW_Credit_Model_Observer
{
    private function _getSession() 
    {
        return Mage::getSingleton('checkout/session');
    }

    private function _getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    } 
    
    public function saveOrderAfter($observer)
    {
        $order = $observer->getOrder();
        $storeId = $order->getStoreId();
        
        if(Mage::helper('affiliate/data')->getEnabledStore($storeId)) 
        {
            $customerId = (int)$order->getCustomerId();
            if($customerId)
            {            
                $amountCredit = $this->_getSession()->getCredit();
                if($amountCredit > 0)
                {	            	
                    $creditcustomer = Mage::getModel('credit/creditcustomer')->load($customerId);
                    $oldCredit = $creditcustomer->getCredit();
                    $newCredit = $oldCredit - $amountCredit;
                    $newCredit = round($newCredit,2);
                    $amountCredit = round($amountCredit,2);
                    $oldCredit = round($oldCredit,2);

                    $historyData = array(
                        'customer_id'           => $customerId,
                        'type_transaction'      => MW_Credit_Model_Transactiontype::USE_TO_CHECKOUT, 
                        'transaction_detail'	=> $order->getIncrementId(), 
                        'amount'                => -$amountCredit, 
                        'beginning_transaction' => $oldCredit,
                        'end_transaction'       => $newCredit,
                        'status'                => MW_Credit_Model_Orderstatus::PENDING,
                        'created_time'          => now());
                    
                    Mage::getModel("credit/credithistory")->setData($historyData)->save();

                    $creditcustomer->setCredit($newCredit)->save();

                    $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
                    $sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
                    $email = Mage::getModel('customer/customer')->load($customerId)->getEmail();
                    $name = Mage::getModel('customer/customer')->load($customerId)->getName();
                    $teampale = 'affiliate/customer/email_template_credit_balance_changed';
                    $sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
                    $customer_credit_link = Mage::app()->getStore($storeId)->getUrl('credit');
                    $data_mail['customer_name'] = $name;
                    $data_mail['transaction_amount'] = Mage::helper('core')->currency(-$amountCredit,true,false);
                    $data_mail['customer_balance'] = Mage::helper('core')->currency($newCredit,true,false);
                    $comment = Mage::helper('affiliate')->__('You checkout by credit order #%s',$order->getIncrementId());
                    $data_mail['transaction_detail'] = $comment;
                    $data_mail['transaction_time'] = now();
                    $data_mail['sender_name'] = $sender_name;
                    $data_mail['store_name'] = $store_name;
                    $data_mail['customer_credit_link'] = $customer_credit_link;
                    Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);	  		
                }
                //Reset credit in Session
                //$this->_getSession()->unsetData('credit');
            }
        }
    }
    
    public function updateStatusOrder($arvgs)
    {
        $order = $arvgs->getOrder();
        $store_id = $order->getStoreId();
        if(Mage::helper('affiliate/data')->getEnabledStore($store_id)) 
        {
            $customer_id = (int)$order->getCustomerId();
            $order_id = $order->getIncrementId();

            if($order->getStatus() == 'complete' || $order->getStatus() == 'processing') {
                    $this->saveOrderComplete($order_id, $customer_id);
            }
            if($order->getStatus() == 'closed') {
                    $this->saveOrderClosed($order_id, $customer_id);
            }

            if($order->getStatus() == 'canceled') {
                    $this->saveOrderCanceled($order,$order_id, $customer_id);
            }
        }
    }

    public function saveOrderComplete($order_id, $customer_id)
    {   
        if($customer_id)
        {
            $collection = Mage::getModel('credit/credithistory')
                ->getCollection()
                ->addFieldToFilter('type_transaction',MW_Credit_Model_Transactiontype::USE_TO_CHECKOUT)
                ->addFieldToFilter('customer_id',$customer_id)
                ->addFieldToFilter('transaction_detail',$order_id);

            foreach($collection as $credithistory)
            {
                $status = MW_Credit_Model_Orderstatus::COMPLETE;
                $credithistory ->setStatus($status)->save();
            }
        }	
    }
    
    public function saveOrderCanceled($order,$order_id, $customer_id)
    {
        if($customer_id)
        {
            $creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_id);
            $oldcredit = $creditcustomer->getCredit();
            $collection = Mage::getModel('credit/credithistory')
                ->getCollection()
                ->addFieldToFilter('type_transaction',MW_Credit_Model_Transactiontype::USE_TO_CHECKOUT)
                ->addFieldToFilter('customer_id',$customer_id)
                ->addFieldToFilter('transaction_detail',$order_id)
                ->addFieldToFilter('status',MW_Credit_Model_Orderstatus::PENDING);
                    	
            foreach($collection as $credithistory)
            {  
             	$amount=$credithistory->getAmount();
                $newcredit = $oldcredit - $amount;
                $oldcredit = round($oldcredit,2);
                $newcredit = round($newcredit,2);
                $amount = round($amount,2);
             	$status = MW_Credit_Model_Orderstatus::CANCELED;
             	$credithistory->setStatus($status)->save();

                $historyData = array(
                    'customer_id'           => $customer_id,
                    'type_transaction'      => MW_Credit_Model_Transactiontype::CANCEL_USE_TO_CHECKOUT, 
                    'status'                => MW_Credit_Model_Orderstatus::COMPLETE,
                    'transaction_detail'    => $order_id, 
                    'amount'                => -$amount, 
                    'beginning_transaction' => $oldcredit,
                    'end_transaction'       => $newcredit,
                    'created_time'          => now()
                );
                
                Mage::getModel("credit/credithistory")->setData($historyData)->save();	
                $creditcustomer->setCredit($newcredit)->save();
            }
            
            $credit = Mage::getModel('credit/creditorder')->load($order_id)->getCredit();
            if($credit > 0)
            {
            	$storeId = $order->getStoreId();
            	$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
                $sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
                $email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
                $name = Mage::getModel('customer/customer')->load($customer_id)->getName();
                $teampale = 'affiliate/customer/email_template_credit_balance_changed';
                $sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
                $customer_credit_link = Mage::app()->getStore($storeId)->getUrl('credit');
                $data_mail['customer_name'] = $name;
                $data_mail['transaction_amount'] = Mage::helper('core')->currency(-$amount,true,false);
                $data_mail['customer_balance'] = Mage::helper('core')->currency($newcredit,true,false);
                $comment = Mage::helper('affiliate')->__('You checkout by credit order #%s. Your order was cancelled',$order_id);
                $data_mail['transaction_detail'] = $comment;
                $data_mail['transaction_time'] = now();
                $data_mail['sender_name'] = $sender_name;
                $data_mail['store_name'] = $store_name;
                $data_mail['customer_credit_link'] = $customer_credit_link;
                Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
            } 
    	}
    }
	
    public function saveOrderClosed($order_id, $customer_id)
    {   
    	$collection = Mage::getModel('credit/credithistory')
            ->getCollection()
            ->addFieldToFilter('type_transaction',MW_Credit_Model_Transactiontype::USE_TO_CHECKOUT)
            ->addFieldToFilter('customer_id',$customer_id)
            ->addFieldToFilter('transaction_detail',$order_id)
            ->addFieldToFilter('status',MW_Credit_Model_Orderstatus::COMPLETE);
       
        foreach($collection as $credithistory)
        {  		
            $status = MW_Credit_Model_Orderstatus::CLOSED;
            $credithistory ->setStatus($status)->save();
        }
    }

    public function initializeCredit($observer)
    {
    	$customer_id = (int)$observer->getCustomer()->getId();
        $creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_id);
        
     	if(!($creditcustomer->getId())) 
     	{
            $customerData = array(
                'customer_id'   => $customer_id,
                'credit'        => 0
            );
            
            Mage::getModel("credit/creditcustomer")->saveCreditCustomer($customerData); 
        }
    }
    
    public function saveAffiliateCreditHistory($observer)
    {
        $order_ids = $observer->getOrderIds();
        foreach ($order_ids as $order_id) {
            $transaction_collections = Mage::getModel('affiliate/affiliatetransaction')
            ->getCollection()
            ->addFieldToFilter('status',array('in' => array(MW_Credit_Model_Orderstatus::COMPLETE, MW_Affiliate_Model_Status::HOLDING)))
            ->addFieldToFilter('order_id',$order_id);
            
            if(sizeof($transaction_collections) > 0)
            {
                foreach($transaction_collections as $transaction_collection) 
                {
                    $customer_id = $transaction_collection->getCustomerInvited();
                    if($customer_id != 0)
                    {   
                        $affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
                        $creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_id);
                        $oldTotalCommission = $affiliate_customer->getTotalCommission();
                        $oldCredit = $creditcustomer->getCredit();
                        $amount = $transaction_collection->getTotalCommission();
                        $newTotalCommission = $oldTotalCommission + $amount;
                        $newCredit=$oldCredit + $amount;

                        $newTotalCommission = round($newTotalCommission,2);
                        $newCredit=round($newCredit,2);
                        $amount=round($amount,2);
                        $oldCredit=round($oldCredit,2);

                        $check_holding = $transaction_collection->getStatus();
                        
                        if($check_holding == MW_Affiliate_Model_Status::HOLDING){                            
                            $historyData = array(
                                'customer_id'           => $customer_id,
                                'type_transaction'      => MW_Credit_Model_Transactiontype::BUY_PRODUCT, 
                                //'status'              => MW_Credit_Model_Orderstatus::COMPLETE,
                                'status'                => MW_Credit_Model_Orderstatus::HOLDING,
                                'transaction_detail'	=> $order_id, 
                                'amount'                => $amount, 
                                'beginning_transaction' => $oldCredit,
                                'end_transaction'       => $newCredit,
                                'created_time'          => now()
                            );
                            
                            Mage::getModel("credit/credithistory")->setData($historyData)->save();	   						
                        }else{				 			
                            $historyData = array(
                                'customer_id'           => $customer_id,
                                'type_transaction'      => MW_Credit_Model_Transactiontype::BUY_PRODUCT, 
                                'status'                => MW_Credit_Model_Orderstatus::COMPLETE,
                                'transaction_detail'	=> $order_id, 
                                'amount'                => $amount, 
                                'beginning_transaction' => $oldCredit,
                                'end_transaction'       => $newCredit,
                                'created_time'          => now()
                            );
                            
                            Mage::getModel("credit/credithistory")->setData($historyData)->save();				 			
				
                            $affiliate_customer->setData('total_commission',$newTotalCommission)->save();
                            $creditcustomer->setData('credit',$newCredit)->save();		   					
                            $storeId = Mage::getModel("sales/order")->loadByIncrementId($order_id)->getStoreId();
                            $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
                            $sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
                            $email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
                            $name = Mage::getModel('customer/customer')->load($customer_id)->getName();
                            $teampale = 'affiliate/customer/email_template_credit_balance_changed';
                            $sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
                            $customer_credit_link = Mage::app()->getStore($storeId)->getUrl('credit');
                            $data_mail['customer_name'] = $name;
                            $data_mail['transaction_amount'] = Mage::helper('core')->currency($amount,true,false);
                            $data_mail['customer_balance'] = Mage::helper('core')->currency($newCredit,true,false);
                            $comment = Mage::helper('affiliate')->__('Commission from orderrrr #%s',$order_id);
                            $data_mail['transaction_detail'] = $comment;
                            $data_mail['transaction_time'] = now();
                            $data_mail['sender_name'] = $sender_name;
                            $data_mail['store_name'] = $store_name;
                            $data_mail['customer_credit_link'] = $customer_credit_link;
                            Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
                        }
                    }
                }
            }
        }
    }
	
    public function saveAffiliateRefundOrder($observer)
    {
        $order_ids = $observer->getOrderIds();
        foreach ($order_ids as $order_id) {
            $transaction_collections = Mage::getModel('affiliate/affiliatetransaction')
                ->getCollection()
                ->addFieldToFilter('status',MW_Credit_Model_Orderstatus::CLOSED)
                ->addFieldToFilter('order_id',$order_id);
            
            if(sizeof($transaction_collections) > 0){
                foreach ($transaction_collections as $transaction_collection) {
                    $customer_id = $transaction_collection->getCustomerInvited();
	        		
                    $collection_refund = Mage::getModel('credit/credithistory')->getCollection()
                        ->addFieldToFilter('type_transaction',MW_Credit_Model_Transactiontype::REFUND_PRODUCT_AFFILIATE)
                        ->addFieldToFilter('customer_id',$customer_id)
                        ->addFieldToFilter('transaction_detail',$order_id)
                        ->addFieldToFilter('status',MW_Credit_Model_Orderstatus::COMPLETE);
                      
                    if($customer_id != 0 && sizeof($collection_refund) == 0)
                    {   
                        $affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
                        $creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_id);

                        $oldTotalCommission = $affiliate_customer->getTotalCommission();
                        $oldCredit = $creditcustomer->getCredit();
                        $amount=$transaction_collection->getTotalCommission();
                        $newTotalCommission = $oldTotalCommission - $amount;
                        $newCredit=$oldCredit - $amount;

                        $newTotalCommission = round($newTotalCommission,2);
                        $newCredit=round($newCredit,2);
                        $amount=round($amount,2);
                        $oldCredit=round($oldCredit,2);

                        $affiliate_customer->setData('total_commission',$newTotalCommission)->save();
                        $creditcustomer->setData('credit',$newCredit)->save();
						
                        $historyData = array(
                            'customer_id'           => $customer_id,
                            'type_transaction'      => MW_Credit_Model_Transactiontype::REFUND_PRODUCT_AFFILIATE, 
                            'status'                => MW_Credit_Model_Orderstatus::COMPLETE,
                            'transaction_detail'    => $order_id,
                            'amount'                => -$amount, 
                            'beginning_transaction' => $oldCredit,
                            'end_transaction'       => $newCredit,
                            'created_time'          => now()
                        );
                        
                        Mage::getModel("credit/credithistory")->setData($historyData)->save();
                        $storeId = Mage::getModel("sales/order")->loadByIncrementId($order_id)->getStoreId();
                        $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
                        $sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
                        $email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
                        $name = Mage::getModel('customer/customer')->load($customer_id)->getName();
                        $teampale = 'affiliate/customer/email_template_credit_balance_changed';
                        $sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
                        $customer_credit_link = Mage::app()->getStore($storeId)->getUrl('credit');
                        $data_mail['customer_name'] = $name;
                        $data_mail['transaction_amount'] = Mage::helper('core')->currency(-$amount,true,false);
                        $data_mail['customer_balance'] = Mage::helper('core')->currency($newCredit,true,false);
                        $comment = Mage::helper('affiliate')->__('Customer granted refund. Order #%s. Affiliate commission reversed.',$order_id);
                        $data_mail['transaction_detail'] = $comment;
                        $data_mail['transaction_time'] = now();
                        $data_mail['sender_name'] = $sender_name;
                        $data_mail['store_name'] = $store_name;
                        $data_mail['customer_credit_link'] = $customer_credit_link;
                        Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
                    }
	        }
	    }
	}
    }
    
    public function collectTotalBefore($argv)
    {
    	$store_id = Mage::app()->getStore()->getId();
    	if(Mage::helper('affiliate/data')->getEnabledStore($store_id)) 
    	{
            $credit  = $this->_getSession()->getCredit();
            $quote = $argv->getQuote();
            $address = $quote->isVirtual()?$quote->getBillingAddress():$quote->getShippingAddress();
            $subtotal = $address->getBaseSubtotal();
            $subtotal += $address->getBaseDiscountAmount() + $credit;

            if($credit > $subtotal) {
                $this->_getSession()->setCredit(0);
            }
        }
    }
}