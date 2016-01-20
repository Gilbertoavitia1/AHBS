<?php
class Mage_Firstdatae4_Model_Source_Cctype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'creditcard_visa', 'label' => 'Visa'),
            array('value' => 'creditcard_mastercard', 'label' => 'MasterCard'),
            array('value' => 'creditcard_eurocard', 'label' => 'EuroCard'),
            array('value' => 'creditcard_americanexpress', 'label' => 'American Express'),
            array('value' => 'creditcard_discover', 'label' => 'Discover'),
            array('value' => 'creditcard_dinersclub', 'label' => 'Diners Club'),
            array('value' => 'creditcard_carteblanche', 'label' => 'Carte Blanche'),
            array('value' => 'creditcard_jcb', 'label' => 'JCB'),
        );
    }
}