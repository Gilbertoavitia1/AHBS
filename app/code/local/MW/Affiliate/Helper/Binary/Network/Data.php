<?php
class MW_Affiliate_Helper_Binary_Network_Data extends Mage_Core_Helper_Abstract
{    
    protected $_treeResult = array();    
    protected $tree_network_table = array();
    protected $orders = array();
    protected $leftleg = 0;
    protected $rightleg = 0;
        
    public function binaryNetwork($customer_id, $referral, $parent_id, $i, $firstDay, $lastDay){
        $this->_treeResult =  array();    
        $this->tree_network_table = array();
        $this->orders = array();
        $this->binaryNetworkTable($customer_id, $referral, $parent_id, $i, $firstDay, $lastDay, 'm');
        
        $array = $this->_treeResult;
        foreach ($array as $key => $item) { 
            $total = $item['vg'];
            $id = $item['id'];
            foreach ($array as $k => $child) {
                if ($id == $child['parent']){
                    $array[$k]['percentage'] = number_format(((100*($child['vg']+$child['vp']))/$total),2) . '%';
                }                
            }
        }
        
        return $array;
    }
    
    public function binaryBlank($parent_id, $i, $side){        
        $tmp = $this->networkBlank($i, $parent_id, $side);
        $this->_treeResult[] = $tmp;
        return 0;
    }
    
    public function binaryNetworkTable($customer_id, $referral, $parent_id, $i, $firstDay, $lastDay, $leg){        
        if(!in_array($customer_id, $this->tree_network_table)) {
            $this->tree_network_table[] = $customer_id;
            
            $vp = 0;
            $vg = 0;
            $izq = 0;
            $der = 0;

            $tmp = $this->networkInfo($customer_id, $parent_id, $i, 2, $firstDay, $lastDay, $leg);  
            if (sizeof($tmp)>0){
                $vp = $tmp['vp'] ? $tmp['vp'] : 0;                
                $left = $this->getBinaryLegs($tmp['left'], $customer_id, false);
                $right = $this->getBinaryLegs($tmp['right'], $customer_id, false);                    
                
                $p = $i;
                $x = $i;
                
                if(sizeof($left) > 0) {
                    $x = $x + 1;
                    foreach($left as $child) {
                        $izq = $izq + $this->binaryNetworkTable($child, $tmp['name'], $tmp['code'], $x, $firstDay, $lastDay, 'l');
                    }
                }
                else{
                    if (sizeof($right) > 0){
                        $x = $x + 1;
                        $izq = $izq + $this->binaryBlank($tmp['code'], $x, 'l');                    
                    }
                }
                
                if(sizeof($right) > 0) {
                    $p = $p + 1 ;
                    foreach($right as $child) {
                        $der = $der + $this->binaryNetworkTable($child, $tmp['name'], $tmp['code'], $p, $firstDay, $lastDay, 'r');
                    }
                }else{
                    if (sizeof($left) > 0){
                        $p = $p + 1 ;
                        $der = $der + $this->binaryBlank($tmp['code'], $p, 'r');
                    }
                }
                
                $vg = $izq + $der;
                $tmp['vg'] = "{$vg}" ? "{$vg}" : "0";       
                $tmp['vleft'] = "{$izq}" ? "{$izq}" : "0";        
                $tmp['vpright'] = "{$der}" ? "{$der}" : "0";
                if ($i == 0) {
                    $tmp['package'] = $this->orders;
                }
                $this->_treeResult[] = $tmp;
            }            
        }
        
        return $vp + $vg;
    }
    
    public function getBinaryLegs($code, $customer_id, $direct)
    {   
        $result = array();
        if($code) {
            $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('referral_code',$code)
                ->addFieldToFilter('status ',MW_Affiliate_Model_Statusreferral::ENABLED)
                ->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);
            
            $result = array_diff($collection->getAllIds(),array($customer_id));
        }
        
        return $result;
    }
        
    public function networkBlank($i, $parent_id, $side){
        $extra = $side == "l" ? "a" : "b";
        $array = array(
                'level'             => $i, 
                'id'                => "{$parent_id}-{$extra}", 
                'code'              => "{$parent_id}-{$extra}", 
                'image'             => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/hs/default/images/avatar.png', 
                'title'             => "PATROCINAR",
                'name'              => "PATROCINAR",
                'parent'            => $parent_id,
                'sponsor'           => 0,
                'right'             => null,
                'left'              => null,
                'rank'              => "",
                'status'            => false,
                'personaactive'     => 'false',
                'itemTitleColor'    => '#000',
                'templateName'      => 'contactTemplate2',
                'vp'                => "0",
                'vg'                => "0",
                'vleft'             => "0",
                'vpright'           => "0",
                'codeurl'           => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).Mage::app()->getStore()->getCode()."/affiliate/binary/registry/s/{$side}/n/{$parent_id}",
            );
                
        return $array;
    }
    
    public function networkInfo($customer_id, $parent_id, $i, $type, $firstDay, $lastDay, $leg){
        $customer       = Mage::getModel('customer/customer')->load($customer_id);
        $expired_package= Mage::getModel('affiliate/affiliateexpired')->load($customer_id);
        $affiliate      = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
        $code           = $affiliate->getReferralCode();
        $name           = $customer->getName();
        $sponsor        = $affiliate->getReferralSponsor(); 
        $invited        = $affiliate->getCustomerInvited(); 
        $sponsor_id     = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->addFieldToFilter('referral_code',$code)->getFirstItem()->getData("customer_invited");
        $sponsor_name   = Mage::getModel('customer/customer')->load($sponsor_id)->getName();
        $referral_name  = Mage::getModel('customer/customer')->load($invited)->getName();
        $customerStatus = $affiliate->getActive(); 
        $image          = $affiliate->getCustomerPhoto() ? $affiliate->getCustomerPhoto() : Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/hs/default/images/avatar.png';
        $group_member   = Mage::getModel('affiliate/affiliategroupmember')->getCollection()->addFieldToFilter('customer_id', $customer_id)->getFirstItem();
        $group          = Mage::getModel('affiliate/affiliategroup')->getCollection()->addFieldToFilter('group_id',$group_member['group_id'])->getFirstItem();
        $group_name     = $group->getData('group_name');  
        $vp             = 0;
        $vg             = 0;
        $vpleft         = 0;
        $vpright        = 0;
        $constructor    = 0;
        $color          = '#166700';
        $statusOptions  = MW_Affiliate_Model_Statusactive::getOptionArray();
        
        $array          = array();     
                
        $today  = strtotime($lastDay);
        $last_pkt = strtotime($expired_package['expired_package']. ' -1 Month');
        $firstDay = date("Y-m-d 00:00:00", strtotime("first day of this month"));


        if($expired_package['expired_package']){
            if ($last_pkt <= $today){ 
                $diff = abs($today - $last_pkt);
                $days = floor(($diff)/(60*60*24)); 

                if ($days <= 30){
                    $firstDay =  date("Y-m-d 00:00:00", $last_pkt); 
                }
            }            
        }
        
        if (!($group_name == "CLIENTE PREFERENTE")){
            $historial = Mage::getResourceModel('sales/order_status_history_collection')
                ->addAttributeToSelect('parent_id')
                ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
                ->addAttributeToFilter('main_table.created_at', array('from'=>$firstDay, 'to'=>$lastDay))
                ->addAttributeToFilter('main_table.entity_name', array('in'=>array('invoice','shipment')));    
            
            if ($historial->getData('parent_id')):
                $sale = Mage::getModel('sales/order')->getCollection()
                    ->addAttributeToFilter('customer_id', array('eq' => $customer_id)) 
                    ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
                    ->addAttributeToFilter('affiliate_network', array('eq' => $type)); 
            
            
                foreach ($sale as $order) {     
                    
                    $items = $order->getAllVisibleItems();                    
                    $po = 0;
                    foreach($items as $item){
                        $single = $item->getData();
                        $puntos = Mage::getModel('catalog/product')->setStoreId($single['store_id'])->load($single['product_id'])->getData('puntos');
                        
                        if ($order->getAffiliateSaleType() == 3){ //constructor
                            $constructor = $puntos * $single['qty_ordered'];
                            $puntos = $puntos * .25;
                        }
                        
                        $po = $po + ($puntos * $single['qty_ordered']);
                        
                        $vp = $vp + $po;
                    }
                    
                    array_push($this->orders, array($order->getIncrementId(), $po));
                }
            endif;
            
            if ($vp > 0){
                if ($approve  == 1){
                    $approve = 3;
                    Mage::getSingleton('affiliate/affiliatecustomers')->load($customer_id)->setAutoapprove(3)->save();
                }
            }else{
                $color = '#ff0000';
            }

            if ($approve  == 1){
                $color = '#4F2F4F';
            }
            
            $array = array(
                'level'             => $i, 
                'leg'               => $leg, 
                'id'                => $code, 
                'code'              => $code, 
                'image'             => $image, 
                'title'             => $name,
                'name'              => $name,
                'parent'            => $parent_id,
                'sponsor'           => $sponsor,
                'right'             => $affiliate->getRightLeg() ? $affiliate->getRightLeg() : null,
                'left'              => $affiliate->getLeftLeg() ? $affiliate->getLeftLeg() : null,
                'rank'              => $group_name,
                'status'            => $statusOptions[$customerStatus],
                'personaactive'     => 'true',
                'itemTitleColor'    => $color,
                'templateName'      => 'contactTemplate',
                'vp'                => "{$vp}",
                'vg'                => "{$vg}",
                'vleft'             => "{$vpleft}",
                'vpright'           => "{$vpright}",
                'constructor'       => "{$constructor}",
            );
        }
        
        return $array;        
    }    
}