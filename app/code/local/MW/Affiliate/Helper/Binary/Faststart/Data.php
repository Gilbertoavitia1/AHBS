<?php
class MW_Affiliate_Helper_Binary_Faststart_Data extends Mage_Core_Helper_Abstract
{     
    protected $payment = array();
    protected $default_payment = array();
    public function runBinaryFaststartReport(){        
        $this->_getPaymentInfo(); //
        $this->_getDefaultPayment(); //
        echo '<pre>';print_r($this->default_payment);echo '</pre>';
    }
    
    public function _getPaymentInfo(){
        $fields = Mage::getStoreConfig('binary/faststart', 0);        
        $this->payment = array();
        foreach ($fields as $key => $value) {
            if (strstr($key, "rank") && ($value != 0)){
                $rank = explode("_", $key);
                $rank = array_pop($rank);                
                $this->payment[$rank] = array();                
                $payvalues = explode("%", $value);
                for($i=0;$i<(sizeof($payvalues)-1);$i++){
                    $level = explode("=>", $payvalues[$i]);
                    $this->payment[$rank][trim($level[0])] = trim($level[1]);
                }
            }            
        }
    }
    
    public function _getDefaultPayment()
    {
        $default = Mage::getStoreConfig('binary/faststart/defaultpayment', 0);        
        $payvalues = explode("%", $default);
        for($i=0;$i<(sizeof($payvalues)-1);$i++){
            $level = explode("=>", $payvalues[$i]);
            $this->default_payment[trim($level[0])] = trim($level[1]);
        }
                
    }
    
    public function _getSponsorParent($customer_id){
        $child = Mage::getModel('affiliate/affiliatecustomers')
            ->getCollection()
            ->addFieldToFilter('anetwork',array('in' => array(2,3)))
            ->addFieldToFilter('active',2)
            ->addFieldToFilter('customer_id',$customer_id)
            ->getFirstItem()
            ->getData();
        
        $resource = Mage::getModel('core/resource');
        $customer_table = $resource->getTableName('customer/entity');
  	  
      	$parent = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('anetwork',array('in' => array(2,3)))
            ->addFieldToFilter('active',2)
            ->addFieldToFilter('referral_code',$child['referral_sponsor']);
        
      	$parent->getSelect()->join(array('customer_entity'=>$customer_table),'main_table.customer_id = customer_entity.entity_id',array('website_id', 'email'));
     	
        $parent = $parent->getFirstItem()->getData();
        return $parent;
    }
    
    
    public function calculateBonus($range, $entity_id){
        $this->_getPaymentInfo();
        $this->_getDefaultPayment();
        $sale = Mage::getModel('sales/order')->getCollection()->addAttributeToFilter('entity_id', array('in' => array($entity_id)));            
        $points = 0;
        $levels = 0;
        $customer_id = 0;
        $currency;
        $store_id = 0;
        $force = 0;
        $order_id = 0;
        $increment_id =0;
        $order_id =0;
        foreach ($sale as $order) { 
            $store_id = $order->getStoreId();
            $package = explode(",", Mage::getStoreConfig('binary/faststart/products', $store_id));
            $package_force = explode(",", Mage::getStoreConfig('binary/faststart/cumulative_products', $store_id));
            $levels = Mage::getStoreConfig('binary/faststart/levelstopay', $store_id);
            $customer_id = $order->getCustomerId();
            $items = $order->getAllVisibleItems();  
            $currency = $order->getOrderCurrencyCode();            
            $increment_id = $order->getIncrementId();          
            foreach($items as $item){
                $single = $item->getData(); 
                if ($single['product_type'] == 'bundle'){
                    if (array_search($single['sku'], $package)){
                        $points = Mage::getModel('catalog/product')->setStoreId($single['store_id'])->load($single['product_id'])->getData('puntos') + $points;
                    }
                    if (array_search($single['sku'], $package_force)){
                        $force = 1;
                    }
                }
            }
        }
        
        $returned = array(
            'customer_id'   => $customer_id,
            'currency'      => $currency,
            'increment_id'  => $increment_id,
            'order_id'      => $entity_id,
            'start_date'    => $range[2],
            'end_date'      => $range[1],
            'qualify'       => 0,
            'points'        => $points,
            'total'         => $points,
        );
        
        $resource = Mage::getModel('core/resource');
        $customer_table = $resource->getTableName('customer/entity');  	  
      	$customer_information = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('customer_id',array('in' => array($customer_id)));
        
      	$customer_information->getSelect()->join(array('customer_entity'=>$customer_table),'main_table.customer_id = customer_entity.entity_id',array('*'));     	
        $customer_information = $customer_information->getFirstItem()->getData();
        
        $created = strtotime($customer_information['created_at']. '-8 hour');
        $today  = strtotime($range[1]);
        $from = strtotime($range[0]. ' -2 Month');
                
        $toPay = array();
        if ((($created >= $from) && ($created <= $today)) || ($force)){
            $returned['qualify'] = 1;
            for($i = 1; $i<=$levels; $i++){
                $tmp = array();
                $tmp['start_date'] = $range[2];
                $tmp['end_date'] = $range[1];
                $tmp['points'] = $points;
                $tmp['level'] = $i;
                $customer_id = ($i == 1) ? $customer_id : $parent['customer_id'];            
                if (($parent['referral_sponsor']) || ($i == 1 )){
                    $parent = Mage::helper('affiliate_binary_faststart')->_getSponsorParent($customer_id);
                    $tmp['customer_id'] = $parent['customer_id'];

                    $groups = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
                        ->addFieldToFilter('customer_id',$parent['customer_id']);
                    $group_id = '';
                    foreach ($groups as $group) {
                        $group_id = $group->getGroupId();
                        break;
                    }
                    $tmp['rank'] = $group_id;
                    $tmp['rank_name'] =  Mage::getModel('affiliate/affiliategroup')->load($group_id)->getGroupName();                
                    $payment = $this->payment[$group_id];
                    $tmp['rank_qualify']= ($i <= sizeof($payment)) ? 1 : 0;
                    $tmp['percentage'] = $this->payment[$group_id][$i];
                    $tmp['total'] = ($points *  $tmp['percentage']) / 100;
                    $tmp['website'] = $parent['website_id'];
                    $tmp['website_currency'] = Mage::app()->getStore($parent['website_id'])->getCurrentCurrencyCode();

                }else{
                    $tmp['customer_id'] = 0;
                    $tmp['rank'] = 0;
                    $tmp['rank_name'] = "N/A";
                    $tmp['rank_qualify'] = 0;
                    $tmp['percentage'] = $this->default_payment[$i];
                    $tmp['total'] = ($points *  $tmp['percentage']) / 100;
                    $tmp['website'] = $store_id;
                    $tmp['website_currency'] = Mage::app()->getStore($store_id)->getCurrentCurrencyCode();
                }

                array_push($toPay, $tmp);
            }
        } 
        
        $returned['payment'] = $toPay;
        return $returned;
    }
    
}