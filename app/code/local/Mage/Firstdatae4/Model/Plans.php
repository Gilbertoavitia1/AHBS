<?php
class Mage_Firstdatae4_Model_Plans extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('firstdatae4/plans');
    }
    
    public function toOptionArray()
    {
        return array(
//            'once'           => Mage::helper('core')->__('Once'),
//            'daily'          => Mage::helper('core')->__('Daily'),
//            'weekly'         => Mage::helper('core')->__('Weekly'),
//            'biweekly'       => Mage::helper('core')->__('Bi-Weekly'),
//            'semimonthly'    => Mage::helper('core')->__('Semi-Monthly'),
            'monthly'        => Mage::helper('core')->__('Monthly'),
//            'bimonthly'      => Mage::helper('core')->__('Bi-Monthly'),
//            'quarterly'      => Mage::helper('core')->__('Quarterly'),
//            'fourmonths'     => Mage::helper('core')->__('4 Months'),
//            'semiannually'   => Mage::helper('core')->__('Semi-Annually'),
//            'annually'       => Mage::helper('core')->__('Annually')
        );
    }
        
    public function nextDate($date, $type){
        $date = new DateTime($date);
        switch ($type) {
            case 'daily':
                $date->add(new DateInterval('P1D')); 
                break;
            case 'weekly':
                $date->add(new DateInterval('P7D')); 
                break;
            case 'monthly':
                $date->add(new DateInterval('P1M')); 
                break;
            case 'quarterly':
                $date->add(new DateInterval('P3M')); 
                break;
            case 'fourmonths':
                $date->add(new DateInterval('P4M')); 
                break;
            case 'anually':
                $date->add(new DateInterval('P12M')); 
                break;
            default:
                break;
        }
        
        return $date->format('Y-m-d h:i:s');
    }
}