<?php
class MW_Affiliate_Helper_Binary_Check_Data extends Mage_Core_Helper_Abstract
{
    public function runBinaryCheckReport($filterFirst, $customer_id){
        $data = Mage::getModel('affiliate/affiliatebinaryresidual')
            ->getCollection()                
            ->addFieldToFilter('start_date', array('eq'=>$filterFirst))
            ->addFieldToFilter('id', array('eq'=>$customer_id))
            ->getData();         
        
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "DELETE FROM mw_affiliate_binary_check WHERE start_date = '{$filterFirst}' and chain = {$customer_id}";
        $write->query($sql); 
                    
        foreach ($data as $key => $value) {
            if ($value['paid'] > 0){
                $points = $value['paid'];
                $customer_id = $value['customer_id'];
                
                $sponsor = $this->getSponsorResidual($customer_id, $filterFirst);
                if ($sponsor['success'] == 1){
                    $profit = $points * $sponsor['rank'];
                    $sql = "INSERT INTO mw_affiliate_binary_check (start_date, end_date, customer_id, profit, level, chain) "
                        . "VALUES ('{$filterFirst}', '{$value['end_date']}', {$sponsor['customer_id']}, {$profit}, 1, {$value['id']})";
                    $write->query($sql); 
                }else{
                    $next_sponsor = $this->getSponsorResidual($sponsor['customer_id'], $filterFirst);
                    if ($next_sponsor['success'] == 1){
                        $profit = $points * $next_sponsor['rank'];
                        $sql = "INSERT INTO mw_affiliate_binary_check (start_date, end_date, customer_id, profit, level, chain) "
                            . "VALUES ('{$filterFirst}', '{$value['end_date']}', {$next_sponsor['customer_id']}, {$profit}, 2, {$value['id']})";
                        $write->query($sql);
                    }
                }
            }
        }
    }
    
    
    public function getSponsorResidual($customer_id, $filterFirst){
        $percentage = array(
            1   =>  0,
            2   =>  0,
            3   => .20,
            4   =>  0,
            5   => .30,
            6   => .40,
            7   =>  0,
            8   => .50,
            9   => .50,
            10  => .50,
            11  => .50,
            12  => .50,
            13  =>  0,
        );
        
        $code = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('customer_id',array('in' => $customer_id))
            ->getFirstItem()
            ->getData('referral_sponsor');
        
        $sponsor = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('referral_code',array('in' => $code))
            ->getFirstItem()
            ->getData();
        
        $sponsor_sale = Mage::getModel('affiliate/affiliatebinaryresidual')->getCollection()
            ->addFieldToFilter('customer_id', array('in' => array($sponsor['customer_id'])))
            ->addFieldToFilter('start_date', array('eq' => $filterFirst))
            ->getFirstItem()
            ->getData();        
        
        if ((sizeof($sponsor_sale) > 0) || ($code == 10000)){
            $rank = $percentage[Mage::getModel('affiliate/affiliategroupmember')->getCollection()
                ->addFieldToFilter('customer_id',array('in' => $sponsor['customer_id']))
                ->getFirstItem()
                ->getData('group_id')];
            
//            if (($sponsor['customer_id'] == 1378) ||
//                ($sponsor['customer_id'] == 1380) ||
//                ($sponsor['customer_id'] == 1371) ||
//                ($sponsor['customer_id'] == 1368) ||
//                ($sponsor['customer_id'] == 1370) ||
//                ($sponsor['customer_id'] == 1379) ||
//                ($sponsor['customer_id'] == 1382) ||
//                ($sponsor['customer_id'] == 1369) ||
//                ($sponsor['customer_id'] == 1442)){
//                $rank = .40;
//            }
            
            $directos = Mage::helper('affiliate_binary')->getBinaryDirect($sponsor['referral_code'], $sponsor['customer_id'], false);            
            $actives = 0;
            $aleft = json_decode($sponsor_sale['aleft']);
            $aright = json_decode($sponsor_sale['aright']);
            
            if ((sizeof($aleft) > 0) && (sizeof($aright) > 0)){
                $actives = 1;
            }
            
            if ((($actives > 0) && ($sponsor_sale['vp'] > 50)) || ($code == 10000)){
                return array('success' => 1, 'rank' => $rank, 'customer_id' => $sponsor['customer_id']);
            }
            
        }
        
        return array('success' => 0, 'customer_id' => $sponsor['customer_id']);                
    }
}