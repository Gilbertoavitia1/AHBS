<?php
class MW_Affiliate_Helper_Binary_Residual_Data extends Mage_Core_Helper_Abstract
{    
    protected $_treeResult = array();    
    protected $tree_network_table = array();
    protected $orders = array();
    protected $main_id = 0;
    protected $main_code = 0;
    protected $main_currency = 0;
    protected $leftleg = array();        
    protected $rightleg = array();
    
    //Binary Residual    
    public function runBinaryResidualReport($dayFirst, $filterFirst, $dayLast, $customerId){
        $collection = Mage::getModel('affiliate/affiliatecustomers')
            ->getCollection()
            ->addFieldToFilter('anetwork',array('in' => array(2,3)))
            ->addFieldToFilter('active',2)
//            ->addFieldToFilter('customer_id', array('from'=>1200, 'to'=>1900))
            ->addFieldToFilter('customer_id',$customerId)
            ->getData();
        
        foreach ($collection as $key => $value) {
            $customer_id = $value['customer_id'];
            $code = $value['referral_code'];            
            $network = $this->binaryNetwork($customer_id, null, 0, $dayFirst, $dayLast);
            
            if ($network){
                $item = array_pop($network);
                $insert = array(
                    "customer_id"   => $value['customer_id'],
                    "vp"            => $item['vp'] ? $item['vp'] : 0,
                    "vg"            => $item['vg'] ? $item['vg'] : 0,
                    "constructor"   => $item['constructor'] ? $item['constructor'] : 0,
                    "left"          => $item['vpleft'] ? $item['vpleft'] : 0,
                    "right"         => $item['vpright'] ? $item['vpright'] : 0,
                    "package"       => json_encode($item['package']),
                    "paid"          => 0,
                    "hold"          => 0,
                    "hold_side"     => "",
                    "cp"            => 0,
                    "aright"        => json_encode($item['aright']),
                    "aleft"         => json_encode($item['aleft']),
                    "start_date"    => $filterFirst,
                    "end_date"      => $dayLast,
                );                 
                
                $historial = Mage::getResourceModel('sales/order_status_history_collection')
                    ->addAttributeToSelect('parent_id')
                    ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
                    ->addAttributeToFilter('main_table.created_at', array('from'=>$dayFirst, 'to'=>$dayLast))
                    ->addAttributeToFilter('main_table.entity_name', array('in'=>array('invoice','shipment')));            
                
                $t = array();
                $cp = 0;

                $preferred_sponsor = Mage::helper('affiliate_binary')->getDirectPreferredCustomers($code);
                
                if (sizeof($preferred_sponsor) > 0){
                    $sale = Mage::getModel('sales/order')->getCollection()
                        ->addAttributeToFilter('customer_id', array('in' => array($preferred_sponsor->getData('customer_id')))) 
                        ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
                        ->addAttributeToFilter('affiliate_network', array('eq' => 2)); 
                    
                    foreach ($sale as $order) { 
                        $items = $order->getAllVisibleItems();  
                        $spoints = 0;
                        foreach($items as $item){
                            $single = $item->getData(); 
                            $puntos = Mage::getModel('catalog/product')->setStoreId($single['store_id'])->load($single['product_id'])->getData('puntos');

                            if ($single['product_type'] == 'bundle'){
                                if (array_search($single['sku'], array('PTK-USA-010','PKT-USA-009','PKT-USA-008','PKT-USA-007','PKT-REC'))){
                                    $puntos = $puntos;
                                }
                                else
                                {
                                    $puntos = $puntos / 2;
                                }
                            }

                            $spoints = $spoints + ($puntos * $single['qty_ordered']);
//                            $cp = $cp + ($puntos * $single['qty_ordered']);
                        }
                        
                        if ($this->main_currency == 2){
                            if ($order->getBaseCurrencyCode() == "MXN"){
                                $spoints = $spoints * .65;
                            }
                        }             

                        $cp = $cp + $spoints;
                        
                        array_push($t, array($order->getIncrementId(), $spoints, $order->getAffiliateSaleType(), $order->getCustomerId(),$order->getBaseCurrencyCode()));
                    }
                }
                
                $insert['cp'] = $cp;
                $insert['package_cp'] = json_encode($t);
                
                $collection = Mage::getModel('affiliate/affiliatebinaryresidual')->getCollection()
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('start_date', $filterFirst)
                    ->getFirstItem()
                    ->getData();
                
                //rewrite vp
                
                if (($customer_id == 1272) || ($customer_id == 1271) || ($customer_id == 1344)){
                    if ($insert['vp'] < 130){
                        $insert['vp'] = 130;
                    }
                }
                
                if (sizeof($collection)>0){
                    Mage::getModel('affiliate/affiliatebinaryresidual')->updateResidual($insert);
                }else{
                    Mage::getModel('affiliate/affiliatebinaryresidual')->saveResidual($insert); 
                }
            }
        }   
    }
    
    
    public function calculateBonus($filterFirst, $filterLast, $firstDay, $customerId){
        $data = Mage::getModel('affiliate/affiliatebinaryresidual')
            ->getCollection()                
            ->addFieldToFilter('start_date', array('eq'=>$filterFirst))
//            ->addFieldToFilter('customer_id', array('in'=> array(1378,1380,1371,1368,1370,1379,1382,1369,1442)))
            ->addFieldToFilter('customer_id', array('eq'=>$customerId))
            ->getData();     
        
        $percentage = array(
            1   =>  0,
            2   => .10,
            3   => .12,
            4   =>  0,
            5   => .15,
            6   => .17,
            7   =>  0,
            8   => .20,
            9   => .20,
            10  => .20,
            11  => .20,
            12  => .20,
            13  =>  0,
        );
        
        foreach ($data as $key => $value) {
            $customer_id = $value['customer_id'];
            $cp = $value['cp'] * .25;
            $left = $value['vpleft'];
            $right = $value['vpright'];
            $vp = $value['vp'];
            $vg = $value['vg'];
            
            $rank = $percentage[Mage::getModel('affiliate/affiliategroupmember')->getCollection()
                ->addFieldToFilter('customer_id',array('in' => $customer_id))
                ->getFirstItem()
                ->getData('group_id')];
            
//             if (($customer_id == 1378) ||
//                ($customer_id == 1380) ||
//                ($customer_id == 1371) ||
//                ($customer_id == 1368) ||
//                ($customer_id == 1370) ||
//                ($customer_id == 1379) ||
//                ($customer_id == 1382) ||
//                ($customer_id == 1369) ||
//                ($customer_id == 1442)){
//                $rank = .17;
//            }
            
            $code = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('customer_id',array('in' => $customer_id))
                ->getFirstItem()
                ->getData('referral_code');

            /* */
            $directos = Mage::helper('affiliate_binary')->getBinaryDirect($code, $customer_id, false); 
            
            $actives = 0;
            $actives_array = array();
            $aleft = json_decode($value['aleft']);
            $aright = json_decode($value['aright']);
            
            $tmpL = array();        
            foreach ($aleft as $a => $b) {                
                $cTemp = Mage::helper('affiliate_binary')->customerInformation($b, 0, "m", null);   
                $cTemp = $this->customerSaleInformation($cTemp, 0, $firstDay, $filterLast);
                if ($cTemp['vp'] > 0){
                    array_push($tmpL, $b);
                }
            }            
            
            $tmpR = array();    
            foreach ($aright as $a => $b) {
                $cTemp = Mage::helper('affiliate_binary')->customerInformation($b, 0, "m", null);   
                $cTemp = $this->customerSaleInformation($cTemp, 0, $firstDay, $filterLast);
                if ($cTemp['vp'] > 0){
                    array_push($tmpR, $b);
                }
            }
            
            if ((sizeof($tmpL) > 0) && (sizeof($tmpR) > 0)){
                $actives = 1;
            }
            
            $value['aleft'] = json_encode($tmpL);
            $value['aright'] = json_encode($tmpR);
            
//            $this->customerSaleInformation($value, $i, $firstDay, $lastDay);
//            if ($directos){
//                $historial = Mage::getResourceModel('sales/order_status_history_collection')
//                    ->addAttributeToSelect('parent_id')
//                    ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
//                    ->addAttributeToFilter('main_table.created_at', array('from'=>date("Y-m-d 00:00:00", strtotime("first day of last month")), 'to'=> $filterLast))
//                    ->addAttributeToFilter('main_table.entity_name', array('in'=>array('invoice','shipment'))); 
//                
//                if (sizeof($historial->getData())>0){ 
//                    $info =  Mage::getModel('sales/order')->getCollection()
//                        ->addAttributeToFilter('customer_id', array('in' => array($directos))) 
//                        ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
//                        ->addAttributeToFilter('affiliate_network', array('eq' => 2));
//                    
//                    foreach ($info as $order) {
//                        $actives = $actives + 1;
//                        array_push($actives_array, array($order->getIncrementId()));
//                    }
//                }
//            }
            /* */
            
            $tleft = 0;
            $tright = 0;
            $tvg = 0;
            $tcp = 0;
            $thold = 0;
            $past_records = Mage::getModel('affiliate/affiliatebinaryresidual')
                ->getCollection()                
                ->addFieldToFilter('customer_id', array('eq'=>$customer_id))
                ->addFieldToFilter('start_date', array('lt'=>$filterFirst))
                ->setOrder('start_date','DESC')
                ->getData();
            
            for ($i=0;$i<sizeof($past_records);$i++){
                if (($past_records[$i]['flag'] == 1) && ($i ==0)){  //Si pago la pasada y es la primera :)
                    $tcp = $tcp + $past_records[$i]['cp'];
                    if ($past_records[$i]['hold_side'] == 2){
                        $right = $right + $past_records[$i]['hold'];                       
                    }else if ($past_records[$i]['hold_side'] == 1){
                        $left = $left + $past_records[$i]['hold'];
                    }
                    $i = sizeof($past_records) + 1;
                }
                
                
                if ($past_records[$i]['flag'] == 0){
                    $tcp = $tcp + ($past_records[$i]['cp'] * .25);                    
                    if ($past_records[$i]['hold_side'] == 3){
                        $left = $left + $past_records[$i]['vpleft'];
                        $right = $right + $past_records[$i]['vpright'];
                    }else if ($past_records[$i]['hold_side'] == 2){
                        $right = $right + $past_records[$i]['vpright'] + $cp;
                    }else if ($past_records[$i]['hold_side'] == 1){
                        $left = $left + $past_records[$i]['vpleft'] + $cp;
                    }
                }
                else{                    
                    $thold = $thold + $past_records[$i]['hold'];
                    $i = sizeof($past_records) + 1;
                }
            }            
            
            $cp = $tcp + $cp;
            $weak_points =  ($left == $right) ? 3 : (($left > $right) ? 2 : 1);            
            $paid = 0;
            $hold = 0;
            $flag = 0;
            $hold_side = 3;
            
            if ($weak_points == 1){
                $left = $left + $cp;                
            }
            else if ($weak_points == 2){
                $right = $right + $cp;  
            }
            
            if ($thold > 0){
                if ($weak_points == 1){
                    $right = $right + $thold;
                }
                else if ($weak_points == 2){
                    $left = $left + $thold;
                }
            }
                
            if (($left >= 100) && ($right >= 100)){
                if (($vp >= 50) && ($actives > 0)){
                    if ($weak_points == 1){                        
                        $hold = ($right - $left) < 0 ? 0 : ($right - $left);  
                        $paid = $left * $rank;
                        $flag = 1;
                        $hold_side = 2;
                    }
                    elseif($weak_points == 2){
                        $hold = ($left - $right) < 0 ? 0 : ($left - $right); 
                        $paid = $right * $rank;
                        $flag = 1;
                        $hold_side = 1;
                    }
                    else{
                        $right = $right + $cp; 
                        $right = $right + $thold;
                        
                        $hold = 0; 
                        $paid = $right * $rank;
                        $flag = 1;
                        $hold_side = 0;
                    }
                }
            }
            
            $tleft = $left;
            $tright = $right;
            $tvg = $left + $right;            
            
            $result = array(
                'customer_id'   => $customer_id,
                'paid'          => $paid,
                'actives'       => $actives,
                'cp'            => $cp,
                'actives_orders'=> json_encode($actives_array)
            );
            
            $actives_array = json_encode($actives_array);
//            echo '<pre>';print_R($result);
            
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $sql = "UPDATE mw_affiliate_binary_residual SET active_orders = '{$actives_array}',  tleft={$tleft}, tright = {$tright}, tvg = {$tvg}, paid = {$paid}, hold = {$hold}, flag = {$flag}, hold_side = {$hold_side}, aleft = '{$value['aleft']}', aright='{$value['aright']}' WHERE customer_id = {$customer_id} and start_date = '{$filterFirst}'"; 
            
            $write->query($sql);              
        }        
    }
    
    
        
    public function binaryNetwork($customer_id, $parent_id, $i, $firstDay, $lastDay){
        $this->_treeResult =  array();    
        $this->tree_network_table = array();
        $this->orders = array();
        $this->leftleg = array();
        $this->rightleg = array();
        $this->main_id = $customer_id;
        $this->binaryNetworkTable($customer_id, $parent_id, $i, $firstDay, $lastDay, 'm');
        
        $array = $this->_treeResult;       
        
        $count = sizeof($array);
        
        $array[$count - 1]['aleft'] = $this->leftleg;
        $array[$count - 1]['aright'] = $this->rightleg;
        return $array;
    }        
    
    public function binaryNetworkTable($customer_id, $parent_id, $i, $firstDay, $lastDay, $leg){
        if(!in_array($customer_id, $this->tree_network_table)) {
            $this->tree_network_table[] = $customer_id;
            
            $vp = 0;
            $vg = 0;
            $izq = 0;
            $der = 0;
            
            $tmp = Mage::helper('affiliate_binary')->customerInformation($customer_id, $i, $leg, $parent_id);             
            $this->main_currency = $i == 0 ? Mage::getModel('customer/customer')->load($customer_id)->getData("website_id") :  $this->main_currency;
            $tmp = $this->customerSaleInformation($tmp, $i, $firstDay, $lastDay);
            
            $this->main_code = $i == 0 ? $tmp['code'] :  $this->main_code;
            
            if (sizeof($tmp)>0){
                $vp = $tmp['vp'] ? $tmp['vp'] : 0; 
                
                if (($leg == "r") && ($this->main_code == $tmp['sponsor'])) {
                    array_push($this->rightleg, $tmp['customer_id']);
                }
                
                if (($leg == "l") && ($this->main_code == $tmp['sponsor'])){
                    array_push($this->leftleg, $tmp['customer_id']);
                }
                
                $left =  Mage::helper('affiliate_binary')->getBinaryLegs($tmp['left'], $customer_id, false);
                $right = Mage::helper('affiliate_binary')->getBinaryLegs($tmp['right'], $customer_id, false);  
                
                $p = $i;
                $x = $i;
                
                if(sizeof($left) > 0) {
                    $leg = $i == 0 ? "l" : $leg;
                    $x = $x + 1;
                    foreach($left as $child) {
                        $izq = $izq + $this->binaryNetworkTable($child, $tmp['code'], $x, $firstDay, $lastDay, $leg);
                    }
                }
                
                if(sizeof($right) > 0) {
                    $leg = $i == 0 ? "r" : $leg;
                    $p = $p + 1 ;
                    foreach($right as $child) { 
                        $der = $der + $this->binaryNetworkTable($child, $tmp['code'], $p, $firstDay, $lastDay, $leg);
                    }
                }
                
                $vg = $izq + $der;
                $tmp['vg'] = "{$vg}" ? "{$vg}" : "0";       
                $tmp['vpleft'] = "{$izq}" ? "{$izq}" : "0";        
                $tmp['vpright'] = "{$der}" ? "{$der}" : "0";
                if ($i == 0) {
                    $tmp['package'] = $this->orders;
                }
                $this->_treeResult[] = $tmp;
            }            
        }
        
        return $vp + $vg;
    }
    
    public function customerSaleInformation($info, $i, $firstDay, $lastDay){        
        $array          = $info;     
        $customer_id    = $info['customer_id'];
        $rank           = $info['rank'];
        $vp             = 0;
        $vg             = 0;
        $vpleft         = 0;
        $vpright        = 0;
        $constructor    = 0;
        
        $expired_package= Mage::getModel('affiliate/affiliateexpired')->load($customer_id);
        
        if ($i==0):
            $today  = strtotime($lastDay);
            $last_pkt = strtotime($expired_package['expired_package']. ' -1 Month');
            $firstDay = date("Y-m-d 00:00:00", strtotime("first day of this month"));
            
            if($expired_package['expired_package']){
                if ($last_pkt <= $today){
                    $diff = abs($today - $last_pkt);
                    $days = floor(($diff)/(60*60*24));                    
                    if ($days <= 33){
                        $last_pkt = strtotime(date("Y-m-d 00:00:00",$last_pkt). ' -1 Day');
                        $firstDay =  date("Y-m-d 00:00:00", $last_pkt); 
                    }
                }            
            }
        endif;
        
        if (!($rank == "CLIENTE PREFERENTE")){
            $historial = Mage::getResourceModel('sales/order_status_history_collection')
                ->addAttributeToSelect('parent_id')
                ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
                ->addAttributeToFilter('main_table.created_at', array('from'=>$firstDay, 'to'=>$lastDay))
                ->addAttributeToFilter('main_table.entity_name', array('in'=>array('invoice','shipment'))); 
            
            if ($historial->getData('parent_id')):
                $sale = Mage::getModel('sales/order')->getCollection()
                    ->addAttributeToFilter('customer_id', array('eq' => $customer_id)) 
                    ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
                    ->addAttributeToFilter('affiliate_network', array('eq' => 2)); 

                foreach ($sale as $order) {      
                    $items = $order->getAllVisibleItems();
                    $cpoints = 0;
                    foreach($items as $item){
                        $single = $item->getData();
                        $puntos = Mage::getModel('catalog/product')->setStoreId($single['store_id'])->load($single['product_id'])->getData('puntos');                        
                        if ($single['product_type'] == 'bundle'){
                            if (array_search($single['sku'], array('PTK-USA-010','PKT-USA-009','PKT-USA-008','PKT-USA-007','PKT-REC'))){
                                $puntos = $puntos;
                            }
                            else
                            {
                                $puntos = $puntos / 2;
                            }
                        }                        
                        
                        if ($order->getAffiliateSaleType() == 3){
                            $constructor = $puntos * $single['qty_ordered'];
                            $puntos = $puntos * .30;
                        }
                        
                        $cpoints = $cpoints + ($puntos * $single['qty_ordered']);                        
                    }    
                    
                    
                    if (!($i == 0)){
                        if ($this->main_currency == 2){
                            if ($order->getBaseCurrencyCode() == "MXN"){
                                $cpoints = $cpoints * .65;
                            }
                        }
                    }                   
                    
                    $vp = $vp + $cpoints;
                    
                    if ($this->main_id != $customer_id)
                        array_push($this->orders, array($order->getIncrementId(), $cpoints, $order->getAffiliateSaleType(), $customer_id, $order->getBaseCurrencyCode()));
                }
                
            endif;
                  
            if (($customer_id == 1272) || ($customer_id == 1271) || ($customer_id == 1344)){
                if ($vp < 130){
                    $vp = 130;
                }
            }
             
            $array['vp'] = "{$vp}";
            $array['vg'] = "{$vg}";
            $array['vpleft'] = "{$vpleft}";
            $array['vpright'] = "{$vpright}";
            $array['constructor'] = "{$constructor}";                
        }
        
        return $array;        
    }
}