<?php
class MW_Affiliate_Helper_Bonus_Uninivel_Faststart_Data extends Mage_Core_Helper_Abstract
{  
    protected $_simplearray = array();
    protected $payLevels = array('config'=> array(0.25, .05, .05, .05, .10));
    protected $config = array(
        'ranks' => array(
            array("name" => "INVALID RANK",       "requiredPV" => 100000, "levelToPay" => 0),
            array("name" => "CLIENTE PREFERENTE", "requiredPV" => 50,     "levelToPay" => 1),
            array("name" => "REP. MERCADEO",      "requiredPV" => 50,     "levelToPay" => 0),  
            array("name" => "DISTRIBUIDOR",       "requiredPV" => 50,     "levelToPay" => 1),
            array("name" => "BRONCE",             "requiredPV" => 100,    "levelToPay" => 2),
            array("name" => "ORO",                "requiredPV" => 100,    "levelToPay" => 3),
            array("name" => "ESMERALDA",          "requiredPV" => 100,    "levelToPay" => 4),
            array("name" => "DIAMANTE",           "requiredPV" => 100,    "levelToPay" => 5)
    ));
        
    public function fastStartBonus($f1b, $f2b){
        
        $f1b = "2015-07-14";
        $f2b = "2015-07-15";
        $this->fastStartBonusCollection($f1b, $f2b);
//        echo '<pre>';print_R($this->_simplearray);
//        return;
        echo "<table id='main'>";
        echo "<tr><th>Nombre</th><th>Pais</th><th>Codigo</th><th>Paquete</th><th>Patrocinadores</th></tr>";
        foreach ($this->_simplearray as $key => $member) {            
            if (($member['new'] == 1) && ($member['vp'] > 0) && ($member['currency'] == 'MX')){
                echo "<tr>";
                echo "<td width='20%'>{$member['name']}</td>";
                echo "<td width='5%' style='text-align: center;'>{$member['currency']}</td>";
                echo "<td width='5%' style='text-align: center;'>{$member['code']}</td>";
                echo "<td width='20%'>";
                foreach ($member['package'] as $k => $paquete) {
                    echo "<p>{$paquete[0]} | {$paquete[1]} | #{$paquete[2]} | {$paquete[3]} </p>";
                }
                echo "</td>";
                echo "<td width='50%'>";
//                echo "<pre>";print_R($member['package']);echo '</pre>';    
                    echo "<table width='100%'>";
                        $this->getSponsorFast($member['sponsor'], 5, 0, $member['package'], $member['currency']);
                    echo '</table>';
                echo "</td>";
                echo "</tr>";
            }    
            
        }
        echo '</table>';
        
        echo "<style>
            table { font-family: sans serif;}
            #main {
                border-collapse: collapse;
                border-spacing: 0;
                border: 1px solid #ddd;
            }

            #main tr:nth-child(even) { background: #CCC }
            #main table tr { background: transparent !important; }
        </style>";
    }
    
    public function getSponsorFast($code, $payLevels, $level, $package, $currency){  
        if ($payLevels > 0){
            $levels = $payLevels - 1;
            $percentage = $this->payLevels['config'][$level];
            $price = 0;
            
            for($i=0; $i<sizeof($package); $i++) {
                if ($currency == "MX"){
                    $price = (($package[$i][3] * 10) * $percentage) + $price;  
                }
                else{
                    $price = ($package[$i][1] * $percentage) + $price;  
                }
            }
            
            echo "<tr><td>{$levels}</td>";
            
            if ($code){
                $sponsor = $this->getSponsorCodeFast($code);
                
                if (!$sponsor){
                    echo "<td>N/A</td>"; 
                    echo "<td></td>"; 
                }else{
                    $rank = $this->getRankData($this->_simplearray[$sponsor]['rank']);
                    echo "<td>{$this->_simplearray[$sponsor]['code']}</td>";
                    echo "<td>{$this->_simplearray[$sponsor]['name']}</td>";
                }
                
//                echo "{$rank['levelToPay']} | {$this->_simplearray[$sponsor]['rank']} | ";
                
                //echo "<td>{$this->payLevels['config'][$level]}</td>";
                echo "<td>{$price}</td>";   
                
                $this->_simplearray[$sponsor]['fastStartBonus'] = array('won' => 0, 'lost' => 0); 
                if (($this->_simplearray[$sponsor]['vg'] > 0) && ($rank['levelToPay'] >= $level)){
                    $this->_simplearray[$sponsor]['fastStartBonus']['won'] = $price;
                    echo "<td><strong>Ganado</strong></td>";
                }
                else{
                    $this->_simplearray[$sponsor]['fastStartBonus']['lost'] = $price;
                    echo "<td><strong>Perdido</strong></td>";
                }
                
                echo '</tr>';
                $level = $level + 1;
                $this->getSponsorFast($this->_simplearray[$sponsor]['sponsor'],$levels, $level, $package, $currency);  
            }
            else{
                $level = $level + 1;
                echo "<td>N/A</td><td></td><td>{$price}</td><td><strong>Perdido</strong></td></<tr>";
                $this->getSponsorFast(0,$levels, $level, $package, $currency);
            }
        }
    }
    
    public function getRankData($name){
        foreach ($this->config['ranks'] as $key => $rank) {
            if ($rank['name'] == $name){
                return $rank;
            }
        }
        
        return $this->config['ranks'][0];
    }

    function getSponsorCodeFast($code){
        foreach ($this->_simplearray as $key => $value) {
            if ($code == $value['id']){
                return $key;
            }
        }
        
        return 3;
    }
    
    
    public function fastStartBonusCollection($f1b, $f2b){
        $dayFirst   = date("Y-m-d 00:00:00", strtotime("first day of last month"));
        $day15      = date("Y-m-d 23:59:59", strtotime($dayFirst. " + 14 day"));
        $dayLast    = date("Y-m-d 23:59:59", strtotime("last day of last month"));                
        
        $f1a = strtotime("{$f1b} 00:00:00");        
        $f2a = strtotime("{$f2b} 23:59:59");  
        
        $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection();
        
        $collection->getSelect()->joinLeft(
            array('c'=>'customer_entity'),
            '`main_table`.`customer_id` = `c`.`entity_id`',
            array('c.*')
        );
        
        $collection->getSelect()->joinLeft(
            array('at_firstname'=>'customer_entity_varchar'),
            '`c`.`entity_id` = `at_firstname`.`entity_id`',
            array('at_firstname.value as firstname')
        );
        
        $collection->getSelect()->joinLeft(
            array('at_lastname'=>'customer_entity_varchar'),
            '`c`.`entity_id` = `at_lastname`.`entity_id`',
            array('at_lastname.value as lastname')
        );
        
        $collection->getSelect()->joinLeft(
            array('gpm'=>'mw_affiliate_group_member'),
            '`main_table`.`customer_id` = `gpm`.`customer_id`',
            array()
        );
        
        $collection->getSelect()->joinLeft(
            array('gp'=>'mw_affiliate_group'),
            '`gpm`.`group_id` = `gp`.`group_id`',
            array('gp.group_name')
        );
        
        $collection->addFieldToFilter('at_firstname.attribute_id',5);
        $collection->addFieldToFilter('at_lastname.attribute_id',7);
        $collection->addFieldToFilter('main_table.active',2);
//        $collection->addFieldToFilter('c.email', 'riberacarlam@yahoo.com');
//        $collection->addFieldToFilter('c.email', 'zeiday2245@yahoo.com');
        
        
        $collection->getSelect()->order('main_table.customer_id', 'DESC');
////        echo '<pre>';print_R($collection->getSelect()->__ToString());echo '</pre>';
//        echo '<pre>';print_R($collection->getData());echo '</pre>';
//        return;
        $new = array();
        
        foreach ($collection->getData() as $key => $value) {
            $tmp = array();
            $parent = 0;
            
//            if ($value['customer_invited'] != 0)
//            {
                if ($value['customer_invited'] != 0)
                {
                    $parent = Mage::getModel('affiliate/affiliatecustomers')
                        ->getCollection()
                        ->addFieldToFilter('customer_id',$value['customer_invited'])
                        ->getFirstItem()
                        ->getData();
                }
                
                $tmp["id"] = $value['referral_code'];
                $tmp["website"] = $value['website_id'];
                $tmp["currency"] = $value['website_id'] == 2 ? "USD" : "MX";
                $tmp["parent"] = ($parent) ? $parent['referral_code'] : null;
                $tmp["created"] = strtotime($value['created_at']);
                $tmp["created2"] = $value['created_at'];
                $tmp["email"] = $value['email'];
                $tmp["title"] = null;
                $tmp["description"] = null;
                $tmp["image"] = null;
                $tmp["context"] = null;
                $tmp["itemTitleColor"] = "#ffa500";
                $tmp["groupTitle"] = null;
                $tmp["groupTitleColor"] = "#4169e1";
                $tmp["isVisible"] = true;
                $tmp["hasSelectorCheckbox"] = 0;
                $tmp["hasButtons"] = 0;
                $tmp["itemType"] = 0;
                $tmp["adviserPlacementType"] = 0;
                $tmp["childrenPlacementType"] = 0;
                $tmp["templateName"] = null;
                $tmp["showCallout"] = 0;
                $tmp["calloutTemplateName"] = null;
                $tmp["label"] = null;
                $tmp["showLabel"] = 0;
                $tmp["labelSize"] = null;
                $tmp["labelOrientation"] = 3;
                $tmp["labelPlacement"] = 0;
                $tmp["sponsor"] = $value['referral_sponsor'] ? $value['referral_sponsor'] : '';
                $tmp["name"] = ($value['firstname'] ? $value['firstname'].' ' : '').($value['middlename'] ? $value['middlename'].' ' : '').($value['lastname'] ? $value['lastname'] : '');
                $tmp["code"] = $value['referral_code'] ? $value['referral_code'] : '';
                $tmp["rank"] = $value['group_name'];
                $tmp["photo"] = $value['customer_photo'] ? $value['customer_photo'] : '';
                $tmp["personactive"] = true;
                $tmp["newrank"] = false;
                $tmp["vp"] = "0";
                $tmp["vg"] = "0";
                $tmp["vt"] = "0";
                $tmp["slot1"] = "images/blank.png";
                $tmp["slot2"] = "images/blank.png";
                $tmp["slot3"] = "images/blank.png";
                $tmp["edit"] = "true";
                $tmp["isFormValid"] = true;    
                
                $sale = Mage::getResourceModel('sales/order_status_history_collection')
                    ->addAttributeToSelect('created_at', 'parent_id')
                    ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
                    ->addAttributeToFilter('main_table.created_at', array('from'=>$dayFirst, 'to'=>$dayLast))
                    ->addAttributeToFilter('main_table.entity_name', array('in'=>array('invoice','shipment')));
                
                $sale->getSelect()->join(
                    array('c'=>'sales_flat_order'),
                    '`main_table`.`parent_id` = `c`.`entity_id`',
                    array('c.*','main_table.created_at as complete_date')    
                );
                
                $sale->addAttributeToFilter('c.customer_id', array('eq' => $value['customer_id']));
                
//                echo '<pre>';print_R($sale->getData());echo '</pre>';
//                echo '<pre>';print_R($sale->getSelect()->__ToString());echo '</pre>';
                
                $totalVP = 0;
                $totalVG = 0;
                $check = 0;
                $package = array();
                $packTotal = 0;
                $flag = sizeof($sale->getData()) ? 1 : 0;
                
                if ($tmp["created"] >= strtotime($dayFirst)){
                    $check = 1;
                }
                else{                            
                    $tsale = Mage::getResourceModel('sales/order_status_history_collection')
                        ->addAttributeToSelect('created_at', 'parent_id')
                        ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
                        ->addAttributeToFilter('main_table.entity_name', array('eq'=>'invoice'))
                        ->setOrder('main_table.created_at', 'DESC');

                    $tsale->getSelect()->join(
                        array('c'=>'sales_flat_order'),
                        '`main_table`.`parent_id` = `c`.`entity_id`',
                        array('c.*')    
                    );

                    $tsale->addAttributeToFilter('c.customer_id', array('eq' => $value['customer_id']));
//                            ->getFirstItem()
//                            ->getData();
                    
                    if (sizeof($tsale->getData()>0)){
                        $tsale = $tsale->getData();
                        $oneMonth = new DateTime($tsale[0]['created_at']);
                        $oneMonth->add(new \DateInterval('P1M'));
                        $twoMonth = new DateTime($tsale[0]['created_at']);
                        $twoMonth->add(new \DateInterval('P2M'));

                        if ((strtotime($oneMonth->format('Y-m-d H:i:s')) >= $f1a)){
                            $check = 1;
                        }

                        if ((strtotime($twoMonth->format('Y-m-d H:i:s')) >= $f1a) && ($check ==0)){
                            $check = 2;
                            
                            $aSale = Mage::getResourceModel('sales/order_status_history_collection')
                                ->addAttributeToSelect('created_at', 'parent_id')
                                ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
                                ->addAttributeToFilter('main_table.entity_name', array('eq'=>'invoice'));

                            $aSale->getSelect()->joinLeft(
                                array('c'=>'sales_flat_order'),
                                '`main_table`.`parent_id` = `c`.`entity_id`',
                                array('c.*', 'c.created_at as created_date')
                            );
                    
                            $aSale->addAttributeToFilter('customer_id', array('eq' => $value['customer_id']));
                            
                            $aSale->getSelect()->join(
                                array('sfoi'=>'sales_flat_order_item'),
                                '`c`.`entity_id` = `sfoi`.`order_id`',
                                array('sfoi.base_price','sfoi.qty_ordered','sfoi.parent_item_id','sfoi.qty_ordered as quantity')
                            );
                            
                            $aSale->getSelect()->join(
                                array('cpe'=>'catalog_product_entity'),
                                '`sfoi`.`product_id` = `cpe`.`entity_id`',
                                array('cpe.*')
                            );
                                        
                            foreach ($aSale->getData() as $f => $h) {
                                if (!($h['parent_item_id'])){ 
                                    if ($h['product_type'] == 'bundle'){
                                        for($i=0;$i<$h['qty_ordered'];$i++){
                                            $packTotal = $packTotal + $h['base_price'];
                                        }
                                    }
                                }                        
                            }
                        }                    
                    }                
                }
                
                $mTotal = 0;
                foreach ($sale as $k => $order) {
                    $increment = $order->getIncrementId();
                    $complete_order = $order['complete_date'];
                    $order = Mage::getModel("sales/order")->load($order->getEntityId());
//                    echo $complete_order. ' '.$increment. ' ';
                    foreach($order->getAllVisibleItems() as $i){
                        $v = $i->getData();
                        $puntos = 0;
                        $product = Mage::getModel('catalog/product')
                            ->setStoreId($v['store_id'])
                            ->load($v['product_id']);
                        
                        $puntos = $product->getData('puntos');
                        
                        if (!($v['parent_item_id'])){ 
                            if ($v['product_type'] == 'bundle'){
                                for($i=0;$i<$v['qty_ordered'];$i++){
                                    array_push($package, array($v['sku'], $v['base_price'], $increment, $puntos));
                                    $mTotal = $mTotal + $v['base_price'];
                                }
                            }
                        }    
                        
//                        echo $complete_order. ' > ' . $f1b . ' < '.$f2b;
                        if ((strtotime($complete_order) > $f1a) && (strtotime($complete_order) < $f2a) && ($v['product_type'] == 'bundle')){
                            $totalVP = $totalVP + ($puntos * $v['qty_ordered']); 
                        }
                        
                        if (($flag == 1) && (strtotime($day15) < $f1a)){
                            $flag = 0;
                        }
                        
                        $totalVG = $totalVG + ($puntos * $v['qty_ordered']);
                    }
                    
//                    echo $totalVG;
//                    
//                    echo '<br>';
                }

                $tmp['vp'] = $totalVP;
                $tmp['vg'] = $totalVG;
                $tmp['package'] = $package; 
                $tmp['active'] = $flag; 
                $tmp['new'] = ($check == 2) ?  ($tmp["currency"] == "MX" ? ($packTotal + $mTotal >= 14000 ? 1 : 0) : ($packTotal + $mTotal >= 500 ? 1 : 0)) : $check; 
          
//                echo $tmp['new'];
                array_push($new, $tmp);
//            }
        }
        
        $this->_simplearray = $new;
    }
    
}
