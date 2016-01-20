<?php
class MW_Affiliate_Helper_Binary_Constructor_Data extends Mage_Core_Helper_Abstract
{
	public function runBinaryConstructorReport($initDay, $endDay, $filterFirst){
		$sales = Mage::helper('affiliate_binary_constructor')->test($initDay, $endDay);

foreach ($sales as $key => $value) {
            $customer_id = $key;
            $orders = json_encode($value['order']);
            $data = array('customer_id' => $customer_id, 'start_date' => $filterFirst, "end_date" => $endDay,'order' => $orders);
            
	    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $sql = "INSERT INTO mw_affiliate_binary_constructor VALUES (NULL, $customer_id, '{$filterFirst}', '{$endDay}', '{$orders}')"; 
            
            $write->query($sql);  

	    //$model = Mage::getModel('affiliate/affiliatebinaryconstructor')->addData($data);
            //$model->save();
        }	
}

	public function test($initDay, $endDay){
		$total_sale;
		
	$preferred_customer = $this->preferred();
        
        $historial = Mage::getResourceModel('sales/order_status_history_collection')
            ->addAttributeToSelect('parent_id')
            ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
            ->addAttributeToFilter('main_table.created_at', array('from'=>$initDay, 'to'=>$endDay))
            ->addAttributeToFilter('main_table.entity_name', array('in'=>array('invoice','shipment')));       
        
        if ($historial->getData('parent_id')){
            $sale = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToFilter('customer_id', array('nin' => $preferred_customer))
                ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
                ->addAttributeToFilter('affiliate_network', array('eq' => 2))
                ->addAttributeToFilter('affiliate_sale_type', array('eq' => 3));
            
            $vdec = 0;
            
            foreach ($sale as $order) {   
                $profit = 0;
                $items = $order->getAllVisibleItems();                
                $customer_id  = $order->getCustomerId();
                $currency  = $order->getBaseCurrencyCode();
                $vdec = 0;
                
                foreach($items as $item){
                    $single = $item->getData(); 
                    $puntos = Mage::getModel('catalog/product')->setStoreId($single['store_id'])->load($single['product_id'])->getData('puntos');
                    $profit = $profit + ($single['price']* $single['qty_ordered']);
                    $vdec = $vdec + ($puntos * $single['qty_ordered']);
                }                
                
                $percentage = Mage::getStoreConfig('binary/constructor/percentage', $order->getStoreId());
                $vdec = $vdec * $percentage;
                $profit = $profit * $percentage;
                
                if (!(isset($total_sale[$customer_id]))){
                    $total_sale[$customer_id] = array('order' => array());                    
                }
                
                array_push($total_sale[$customer_id]["order"], array("order" => $order->getIncrementId(), "profit" => $profit, "points" => $vdec, "currency" => $currency, "id" => $order->getEntityId()));                
            }
        }
        
        return $total_sale;
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
