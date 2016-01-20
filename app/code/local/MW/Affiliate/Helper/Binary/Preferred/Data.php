<?php
class MW_Affiliate_Helper_Binary_Preferred_Data extends Mage_Core_Helper_Abstract
{     
    public function runBinaryPreferredReport(){
//        $dayFirst   = date("Y-09-01 00:00:00");
//        $filterDay   = date("Y-09-01 08:00:00");
//        $dayLast    = date("Y-09-30 23:59:59");
        $dayFirst   = date("Y-m-d 00:00:00", strtotime("first day of last month"));
        $filterDay   = date("Y-m-d 08:00:00", strtotime("first day of last month"));
        $dayLast    = date("Y-m-d 23:59:59", strtotime("last day of last month"));
        
//        $preferred = array(array('customer_id'=> 1317));//$this->preferred();
        $preferred = $this->preferred();
        
        $historial = Mage::getResourceModel('sales/order_status_history_collection')
            ->addAttributeToSelect('parent_id')
            ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
            ->addAttributeToFilter('main_table.created_at', array('from'=>$dayFirst, 'to'=>$dayLast))
            ->addAttributeToFilter('main_table.entity_name', array('in'=>array('invoice','shipment')));
        
        $affiliate_parent = array();
        foreach ($preferred as $key => $value) {
            $customer_info = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->addFieldToFilter('customer_id', $value['customer_id'])->getFirstItem()->getData();
            $parent_info = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->addFieldToFilter('referral_code', $customer_info['referral_sponsor'])->getFirstItem()->getData();
            
            $sale = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToFilter('customer_id', array('in' => array($value['customer_id']))) 
                ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
                ->addAttributeToFilter('affiliate_network', array('eq' => 2))
                ->addAttributeToFilter('affiliate_sale_type', array('nin' => 0))
                    ; 
                        
            $sale_parent = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToFilter('customer_id', array('in' => array($parent_info['customer_id']))) 
                ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
                ->addAttributeToFilter('affiliate_network', array('eq' => 2))
                ->addAttributeToFilter('affiliate_sale_type', array('nin' => 0))
                ->getData(); 
                  
            
            if ($sale->getData() && sizeof($sale_parent)>0){
                $store_id = $value['store_id'];
                $package = array();
                $percentage = 0;
                $currency = 0;
                
                foreach ($sale as $k => $order) { 
                    $total = 0;
                    $totala = 0;
                    $store_id = $order->getStoreId();
                    $available = explode(",", Mage::getStoreConfig('binary/preferred/products', $store_id));
                    $payment_percentage = Mage::getStoreConfig('binary/preferred/percentage', $store_id);
                    $increment = $order->getIncrementId();
                    $items = $order->getAllVisibleItems();                    
                    
                    foreach($items as $item){
                        $product = $item->getData();
                        $sku = $product['sku'];
                        if (in_array($sku,$available)){
                            $total = $total + (($product['qty_shipped'] *  $product['price']) * $payment_percentage); 
                        }   
                    }
                    
                    $percentage = $percentage + $total;
                    array_push($package, array(
                        $increment,$order->getAffiliateSaleType(),$order->getBaseCurrencyCode(), $order->getEntityId(), $total
                    ));                    
                }    
                
                $insert = array(
                    "preferred_id"   => $value['customer_id'],
                    "profit"         => $percentage,
                    "currency"       => $order->getBaseCurrencyCode(),
                    "package"        => json_encode($package),
                    "customer_id"    => $parent_info['customer_id'],
                    "start_date"     => $filterDay,
                    "end_date"       => $dayLast,
                );

                $model = Mage::getModel('affiliate/affiliatebinarypreferred')->addData($insert);
                $model->save();

                echo "<pre>";print_R($insert);echo '</pre>';                    
            }
        }
        
    }
    
    public function preferred(){
        $array = Mage::getModel('affiliate/affiliategroupmember')
            ->getCollection()
            ->addFieldToSelect('customer_id')
            ->addFieldToFilter('group_id', array('eq'=>13))
            ->getData();
        
        return $array;
    }
}