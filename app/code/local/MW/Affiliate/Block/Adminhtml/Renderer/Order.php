<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['order_id'])) return '';
    	
    	//return $row['order_id'].'hello';
    $collections = Mage::getModel('affiliate/affiliatehistory')->getCollection()
      					->addFieldToFilter('customer_invited',$this->getRequest()->getParam('id'))
      					->addFieldToFilter('order_id',$row['order_id'])
						->setOrder('transaction_time', 'DESC')
						->setOrder('history_id', 'DESC');	
	$result='<div style="font-weight:bold;padding-bottom:5px; padding-top:5px;padding-left:5px;" >'.'#'.$row['order_id'].'</div>';					
    $result.= '<table>
	    <thead>
	        <tr style="background:#E5ECF2;">
				<th style="text-align: center;">Program Name</th>
				<th style="text-align: center;">Product Name</th>
				<th style="text-align: center;">Commission</th>
				<th style="text-align: center;">Discount</th>
	        </tr>
	    </thead>
	    <tbody>';
	    foreach ($collections as $collection){ 
	    	$result.= '
	    	<tr>
	            <td >'.Mage::helper('affiliate')->getProgramName($collection->getProgramId()).'</td>
				<td >'.Mage::helper('affiliate')->getProductName($collection->getProductId()).'</td>
	            <td >'.Mage::helper('affiliate')->formatMoney($collection->getCommission()).'</td>
	            <td>'.Mage::helper('affiliate')->formatMoney($collection->getDiscount()).'</td>
	       </tr>';
	    }
	    $result.='
	    </tbody>
	</table>';
    
    return $result;
    }

}