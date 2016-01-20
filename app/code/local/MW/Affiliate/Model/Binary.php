<?php
class MW_Affiliate_Model_Binary
{    
    public function runBinaryResidual(){
        $month   = date("Y-m-d 00:00:00", strtotime("first day of this month"));       
        $dayFirst   = date("Y-m-d 00:00:00", strtotime("last friday"));        
        $filterFirst  = date("Y-m-d 08:00:00", strtotime("last friday"));
        $dayLast    = date("Y-m-d 23:59:59", strtotime("last thursday"));
        
        Mage::helper('affiliate_binary')->getResidualReport($month, $dayFirst, $filterFirst, $dayLast);       
        Mage::helper('affiliate_binary')->getResidualBonus($filterFirst);  
        Mage::helper('affiliate_binary')->getBinaryCheck($filterFirst);  
    }
    
    
    public function binaryDateReport(){
        $month   = date("Y-m-d 00:00:00", strtotime("first day of last month"));
        $dayFirst   = date("2015-08-01 00:00:00");
        $filterFirst  = date("2015-08-01 08:00:00");
        $dayLast    = date("2015-08-06 23:59:59");
        
        $dayFirst   = date("2015-08-07 00:00:00");
        $filterFirst  = date("2015-08-07 08:00:00");
        $dayLast    = date("2015-08-13 23:59:59");
        
        $dayFirst   = date("2015-08-14 00:00:00");
        $filterFirst  = date("2015-08-14 08:00:00");
        $dayLast    = date("2015-08-20 23:59:59");
        
        $dayFirst   = date("2015-08-21 00:00:00");       
        $filterFirst  = date("2015-08-21 08:00:00");
        $dayLast    = date("2015-08-27 23:59:59");
        
        $month   = date("Y-m-d 00:00:00", strtotime("first day of this month"));       
        $dayFirst   = date("2015-08-28 00:00:00");    
        $filterFirst  = date("2015-08-28 08:00:00");
        $dayLast    = date("2015-09-03 23:59:59");

        $dayFirst   = date("2015-09-04 00:00:00");        
        $filterFirst  = date("2015-09-04 08:00:00");
        $dayLast    = date("2015-09-10 23:59:59");
        
        $dayFirst   = date("2015-09-11 00:00:00");        
        $filterFirst  = date("2015-09-11 08:00:00");
        $dayLast    = date("2015-09-17 23:59:59");
    }
}