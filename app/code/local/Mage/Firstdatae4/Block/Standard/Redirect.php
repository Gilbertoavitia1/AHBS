<?php
class Mage_Firstdatae4_Block_Standard_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $date = new DateTime(date('Y-m-d H:i:s'));
//        $date->sub(new DateInterval('PT6H'));
        $this->datetime = $date->getTimestamp();
//        $this->datetime = time();
        $standard = Mage::getModel('firstdatae4/standard');        
        
        $form = new Varien_Data_Form();
        $form->setAction($standard->getFirstdatae4Url())
            ->setId('firstdatae4_standard_checkout')
            ->setName('firstdatae4_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);

        #Mapp these
        $fn='';$ln='';$ad1='';$ad2='';
        $inv='';$desc='';$amt=0; $ship_addr='';

        $total_items = 0;
        $checkoutfields = $standard->getStandardCheckoutFormFields();
        foreach ($checkoutfields as $field=>$value) {
            if($field == 'testmode')        $form->addField('x_test_request', 'hidden', array('name'=>'x_test_request', 'value'=>$value));
            if($field == 'payment_action')  $form->addField('x_type', 'hidden', array('name'=>'x_type', 'value'=>$value));
            if($field == 'email_customer')  $form->addField('x_email_customer', 'hidden', array('name'=>'x_email_customer', 'value'=>$value));
            if($field == 'merchant_email')  $form->addField('x_merchant_email', 'hidden', array('name'=>'x_merchant_email', 'value'=>$value));
            if($field == 'store_id') {
                $this->storeid = $value;
                $form->addField('x_login', 'hidden', array('name'=>'x_login', 'value'=>$value));
            }
            if($field == 'shared_secret') {
                $this->sharedsecret = $value;
            }
            if($field == 'sequence') {
                $this->sequence = $value;
                $form->addField('x_fp_sequence', 'hidden', array('name'=>'x_fp_sequence', 'value'=>$value));
            }
            if($field == 'invoice') $form->addField('x_invoice_num', 'hidden', array('name'=>'x_invoice_num', 'value'=>$value));
			
            if($field == 'first_name')  $form->addField('x_first_name', 'hidden', array('name'=>'x_first_name', 'value'=>$value));
            if($field == 'last_name')  $form->addField('x_last_name', 'hidden', array('name'=>'x_last_name', 'value'=>$value));
            if($field == 'address1') $form->addField('x_address', 'hidden', array('name'=>'x_address', 'value'=>$value));
            if($field == 'city')  $form->addField('x_city', 'hidden', array('name'=>'x_city', 'value'=>$value));
            if($field == 'state') $form->addField('x_state', 'hidden', array('name'=>'x_state', 'value'=>$value));
            if($field == 'zip') $form->addField('x_zip', 'hidden', array('name'=>'x_zip', 'value'=>$value));
            if($field == 'country') $form->addField('x_country', 'hidden', array('name'=>'x_country', 'value'=>$value));
			
            if($field == 'telephone') $form->addField('x_phone', 'hidden', array('name'=>'x_phone', 'value'=>$value));
            if($field == 'email') $form->addField('x_email', 'hidden', array('name'=>'x_email', 'value'=>$value));
			
            //if($field == 'subtotal_amount') $form->addField('subtotal', 'hidden', array('name'=>'subtotal', 'value'=>$value));
            if($field == 'shipping_amount') $form->addField('x_freight', 'hidden', array('name'=>'x_freight', 'value'=>$value));
            if($field == 'tax_amount') $form->addField('x_tax', 'hidden', array('name'=>'x_tax', 'value'=>$value));
            if($field == 'discount_amount') $form->addField('discount_amount', 'hidden', array('name'=>'discount_amount', 'value'=>$value));
            if($field == 'grandtotal_amount') {
                $chargetotal = $value;
                $form->addField('x_amount', 'hidden', array('name'=>'x_amount', 'value'=>$value));
            }
			
            if($field == 's_first_name') 	$form->addField('x_ship_to_first_name', 'hidden', array('name'=>'x_ship_to_first_name', 'value'=>$value));
            if($field == 's_last_name') 	$form->addField('x_ship_to_last_name', 'hidden', array('name'=>'x_ship_to_last_name', 'value'=>$value));
            if($field == 's_address1') 		$form->addField('x_ship_to_address', 'hidden', array('name'=>'x_ship_to_address', 'value'=>$value));
            //if($field == 's_address2') $form->addField('saddr2', 'hidden', array('name'=>'saddr2', 'value'=>$value));
            if($field == 's_city')  		$form->addField('x_ship_to_city', 'hidden', array('name'=>'x_ship_to_city', 'value'=>$value));
            if($field == 's_state') 		$form->addField('x_ship_to_state', 'hidden', array('name'=>'x_ship_to_state', 'value'=>$value));
            if($field == 's_zip') 			$form->addField('x_ship_to_zip', 'hidden', array('name'=>'x_ship_to_zip', 'value'=>$value));
            if($field == 's_country') 		$form->addField('x_ship_to_country', 'hidden', array('name'=>'x_ship_to_country', 'value'=>$value));

            if (strpos($field, 'item_name_') !== FALSE) {
                $temp = (int)substr($field, 10) + 0;
                if ($temp > $total_items) $total_items = $temp;
            }
        }

        $i = 1;
        while ($i < $total_items) { // usually it is $i <= $total_items but, the last item is always shipping and we do not want it in the line item
            if (isset($checkoutfields['item_name_' . $i]) && !empty($checkoutfields['item_name_' . $i])) {
                $isDiscount = 'N';
                if ($checkoutfields['individual_discount_' . $i] > 0) {
                    $isDiscount = 'Y';
                }
                //$line_item = $checkoutfields['item_number_' . $i] . '<|>' . $checkoutfields['item_name_' . $i] . '<|>' . $checkoutfields['item_name_' . $i] . '<|>' . $checkoutfields['quantity_' . $i] . '<|>' . $checkoutfields['base_price_' . $i] . '<|>' . 'YES' . '<|>' . $checkoutfields['item_number_' . $i] . '<|><|><|><|><|>' . $checkoutfields['tax_' . $i] . '<|><|>0<|>' . ($checkoutfields['base_price_' . $i] * $checkoutfields['quantity_' . $i]);
                //Item ID<|>Item Title<|>Item Description<|>Quantity<|>Unit Price<|>Taxable (Y or N)<|>Product Code<|>Commodity Code<|>Unit of Measure<|>Tax Rate<|>Tax Type<|>Tax Amount<|>Discount Indicator<|>Discount Amount<|>Line Item Total
                //$line_item = $checkoutfields['item_number_' . $i] . '<|>' . $checkoutfields['item_name_' . $i] . '<|>' . $checkoutfields['item_name_' . $i] . '<|>' . $checkoutfields['quantity_' . $i] . '<|>' . $checkoutfields['base_price_' . $i] . '<|>' . 'Y' . '<|>' . $checkoutfields['item_number_' . $i] . '<|>' . $checkoutfields['item_number_' . $i] . '<|>EA<|><|><|>' . $checkoutfields['tax_' . $i] . '<|>' . $isDiscount . '<|>' . $checkoutfields['individual_discount_' . $i] . '<|>' . sprintf('%.2f', $checkoutfields['individual_total_' . $i]);
                $line_item = $checkoutfields['item_number_' . $i] . '<|>' . $checkoutfields['item_name_' . $i] . '<|>' . $checkoutfields['item_name_' . $i] . '<|>' . $checkoutfields['quantity_' . $i] . '<|>' . $checkoutfields['base_price_' . $i] . '<|>' . 'Y' . '<|>' . $checkoutfields['item_number_' . $i] . '<|>' . $checkoutfields['item_number_' . $i] . '<|>EA<|><|><|>' . $checkoutfields['tax_' . $i] . '<|>' . $isDiscount . '<|>' . $checkoutfields['individual_discount_' . $i] . '<|>' . sprintf('%.2f', ($checkoutfields['base_price_' . $i] * $checkoutfields['quantity_' . $i]));
                $form->addField('x_line_item' . $i, 'hidden', array('name'=>'x_line_item', 'value'=> $line_item));
            }
            $i++;
        }

        $form->addField('x_receipt_link_method', 'hidden', array('name'=>'x_receipt_link_method', 'value'=> 'AUTO-POST'));
        $form->addField('x_receipt_link_text', 'hidden', array('name'=>'x_receipt_link_text', 'value'=> 'Return to store'));
        $form->addField('x_receipt_link_url', 'hidden', array('name'=>'x_receipt_link_url', 'value'=> $standard->getSuccessUrl()));

        $form->addField('x_fp_timestamp', 'hidden', array('name'=>'x_fp_timestamp', 'value'=> $this->datetime));
        $form->addField('x_customer_ip', 'hidden', array('name'=>'x_customer_ip', 'value'=> $_SERVER['REMOTE_ADDR']));
        $form->addField('x_show_form', 'hidden', array('name'=>'x_show_form', 'value'=> "PAYMENT_FORM"));
        $form->addField('x_fp_hash', 'hidden', array('name'=>'x_fp_hash', 'value'=>$this->createHash($chargetotal)));
        
        $session = Mage::getSingleton("core/session");
        $fde4recurring = $session->getData("fde4-recurring");
        if ($fde4recurring != '0'){
            $form->addField('x_recurring_billing_amount', 'hidden', array('name'=>'x_recurring_billing_amount', 'value'=> $chargetotal));
            $form->addField('x_recurring_billing_id', 'hidden', array('name'=>'x_recurring_billing_id', 'value'=> $fde4recurring));
            $form->addField('x_recurring_billing', 'hidden', array('name'=>'x_recurring_billing', 'value'=> "TRUE"));
            $form->addField('x_relay_response', 'hidden', array('name'=>'x_relay_response', 'value'=> ""));
            $form->addField('donation_prompt', 'hidden', array('name'=>'donation_prompt', 'value'=> ""));
        }

		
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to FirstdataE4 in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("firstdatae4_standard_checkout").submit();</script>';
        $html.= '</body></html>';

        
        return $html;
    }
	function createHash($chargetotal) {
		$loginID 		= $this->storeid;
		$sequence 		= $this->sequence;
		$timeStamp 		= $this->datetime;
		$amount 		= $chargetotal;
		$transactionKey = $this->sharedsecret;
		if (phpversion() >= '5.1.2') { 
			$fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey); 
		} else { 
			$fingerprint = bin2hex(mhash(MHASH_MD5, $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey)); 
		}
		
		return $fingerprint;
	}
}