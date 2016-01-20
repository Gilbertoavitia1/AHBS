<?php
class MW_Affiliate_Model_Months
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('affiliate')->__('1')),
            array('value'=>2, 'label'=>Mage::helper('affiliate')->__('2')),
            array('value'=>3, 'label'=>Mage::helper('affiliate')->__('3')),
            array('value'=>4, 'label'=>Mage::helper('affiliate')->__('4')),
            array('value'=>5, 'label'=>Mage::helper('affiliate')->__('5')),
            array('value'=>6, 'label'=>Mage::helper('affiliate')->__('6')),
            array('value'=>7, 'label'=>Mage::helper('affiliate')->__('7')),
            array('value'=>8, 'label'=>Mage::helper('affiliate')->__('8')),
            array('value'=>9, 'label'=>Mage::helper('affiliate')->__('9')),
            array('value'=>10, 'label'=>Mage::helper('affiliate')->__('10')),
            array('value'=>11, 'label'=>Mage::helper('affiliate')->__('11')),
            array('value'=>12, 'label'=>Mage::helper('affiliate')->__('12')),
            array('value'=>13, 'label'=>Mage::helper('affiliate')->__('13')),
            array('value'=>14, 'label'=>Mage::helper('affiliate')->__('14')),
            array('value'=>15, 'label'=>Mage::helper('affiliate')->__('15')),
            array('value'=>16, 'label'=>Mage::helper('affiliate')->__('16')),
            array('value'=>17, 'label'=>Mage::helper('affiliate')->__('17')),
            array('value'=>18, 'label'=>Mage::helper('affiliate')->__('18')),
            array('value'=>19, 'label'=>Mage::helper('affiliate')->__('19')),
            array('value'=>20, 'label'=>Mage::helper('affiliate')->__('20')),
            array('value'=>21, 'label'=>Mage::helper('affiliate')->__('21')),
            array('value'=>22, 'label'=>Mage::helper('affiliate')->__('22')),
            array('value'=>23, 'label'=>Mage::helper('affiliate')->__('23')),
            array('value'=>24, 'label'=>Mage::helper('affiliate')->__('24')),
            array('value'=>25, 'label'=>Mage::helper('affiliate')->__('25')),
            array('value'=>26, 'label'=>Mage::helper('affiliate')->__('26')),
            array('value'=>27, 'label'=>Mage::helper('affiliate')->__('27')),
            array('value'=>28, 'label'=>Mage::helper('affiliate')->__('28')),
            array('value'=>29, 'label'=>Mage::helper('affiliate')->__('29')),
            array('value'=>30, 'label'=>Mage::helper('affiliate')->__('30')),
            array('value'=>31, 'label'=>Mage::helper('affiliate')->__('31')),                                         
        );
    }
    
    public function monthsToArray()
    {
        $array = array();
        $array[1] =  Mage::helper('affiliate')->__('January');
        $array[2] =  Mage::helper('affiliate')->__('February');
        $array[3] =  Mage::helper('affiliate')->__('March');
        $array[4] =  Mage::helper('affiliate')->__('April');
        $array[5] =  Mage::helper('affiliate')->__('May');
        $array[6] =  Mage::helper('affiliate')->__('June');
        $array[7] =  Mage::helper('affiliate')->__('July');
        $array[8] =  Mage::helper('affiliate')->__('August');
        $array[9] =  Mage::helper('affiliate')->__('September');
        $array[10] =  Mage::helper('affiliate')->__('October');
        $array[11] =  Mage::helper('affiliate')->__('November');
        $array[12] =  Mage::helper('affiliate')->__('December');    
        
        return $array;
    }

}
